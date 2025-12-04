<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        die("Connection failed: Unable to connect to database.\n");
    }

    $categories = [
        ['name' => 'RPG', 'description' => 'Role-playing games where you assume the roles of characters in a fictional setting.'],
        ['name' => 'Action', 'description' => 'Games emphasizing physical challenges, including hand-eye coordination and reaction-time.'],
        ['name' => 'Adventure', 'description' => 'Games focusing on exploration and puzzle solving.'],
        ['name' => 'Indie', 'description' => 'Games created by individuals or smaller development teams.'],
        ['name' => 'Simulation', 'description' => 'Games designed to simulate real-world activities.'],
        ['name' => 'MOBA', 'description' => 'Multiplayer Online Battle Arena games.'],
        ['name' => 'Casual', 'description' => 'Games targeted at a mass audience of casual gamers.'],
        ['name' => 'FPS', 'description' => 'First-person shooter games.'],
        ['name' => 'Horror', 'description' => 'Games designed to scare the player.'],
        ['name' => 'Sports', 'description' => 'Games that simulate the practice of sports.']
    ];

    echo "Seeding Categories...\n";

    // Check if Category table exists, if not create it (just in case)
    // But NextPlayDB.sql says it should exist.

    $query = "INSERT IGNORE INTO Category (name, description) VALUES (:name, :description)";
    $stmt = $conn->prepare($query);

    foreach ($categories as $cat) {
        $stmt->bindParam(":name", $cat['name']);
        $stmt->bindParam(":description", $cat['description']);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo "Inserted: " . $cat['name'] . "\n";
            } else {
                echo "Skipped (Already exists): " . $cat['name'] . "\n";
            }
        } else {
            echo "Error inserting " . $cat['name'] . ": " . implode(" ", $stmt->errorInfo()) . "\n";
        }
    }

    echo "Category seeding completed.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
