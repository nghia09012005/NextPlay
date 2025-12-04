<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        die("Connection failed.\n");
    }

    // 1. Get all Categories
    $stmt = $conn->query("SELECT catId, name FROM Category");
    $categories = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [catId => name]
    $catNameMap = array_flip($categories); // [name => catId]

    // 2. Get all Games
    $stmt = $conn->query("SELECT Gid, name, category FROM Game");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Linking Games to Categories...\n";

    $insertStmt = $conn->prepare("INSERT IGNORE INTO Game_cat (Gid, catId) VALUES (:gid, :catId)");

    foreach ($games as $game) {
        $gid = $game['Gid'];
        $gameName = $game['name'];
        $gameCategoryString = $game['category']; // e.g., "Action" or "RPG" from old column

        $assignedCatIds = [];

        // Strategy 1: Match existing 'category' column if it matches a new Category name
        if ($gameCategoryString && isset($catNameMap[$gameCategoryString])) {
            $assignedCatIds[] = $catNameMap[$gameCategoryString];
        }

        // Strategy 2: Keyword matching in Game Name
        foreach ($catNameMap as $catName => $catId) {
            if (stripos($gameName, $catName) !== false) {
                $assignedCatIds[] = $catId;
            }
        }

        // Strategy 3: If no category assigned yet, assign a random one (for demo purposes)
        if (empty($assignedCatIds)) {
            $randomCatId = array_rand($categories);
            $assignedCatIds[] = $randomCatId;
             // Assign a second random category sometimes
            if (rand(0, 1)) {
                 $randomCatId2 = array_rand($categories);
                 if ($randomCatId2 != $randomCatId) {
                     $assignedCatIds[] = $randomCatId2;
                 }
            }
        }

        // Insert mappings
        foreach (array_unique($assignedCatIds) as $catId) {
            $insertStmt->bindParam(':gid', $gid);
            $insertStmt->bindParam(':catId', $catId);
            if ($insertStmt->execute()) {
                 if ($insertStmt->rowCount() > 0) {
                    echo "Linked Game '{$gameName}' (ID: $gid) to Category '{$categories[$catId]}' (ID: $catId)\n";
                 }
            }
        }
    }

    echo "Game-Category linking completed.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
