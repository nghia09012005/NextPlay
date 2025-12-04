<?php
require_once 'config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get some users (customers)
    $stmt = $db->query("SELECT uid FROM User LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($users)) {
        die("No users found. Please create users first.\n");
    }

    // Get some games with their publishers
    $stmt = $db->query("SELECT Gid, publisherid FROM Game WHERE publisherid IS NOT NULL LIMIT 5");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($games)) {
        die("No games found. Please create games first.\n");
    }

    $comments = [
        "Game này chơi rất cuốn!",
        "Đồ họa đẹp, cốt truyện hay.",
        "Gameplay hơi khó làm quen nhưng sau đó thì nghiện.",
        "Không đáng tiền lắm, chờ sale hãy mua.",
        "Tuyệt phẩm! 10/10.",
        "Nhiều bug quá, cần fix gấp.",
        "Chơi co-op với bạn bè rất vui.",
        "Âm thanh đỉnh cao.",
        "Cấu hình yêu cầu hơi cao.",
        "Mong chờ phần tiếp theo."
    ];

    echo "Seeding game reviews...\n";

    $sql = "INSERT INTO Receives_feedback (feedback_time, customerid, Gid, publisherid, content, rating) VALUES (:time, :uid, :gid, :pubid, :content, :rating)";
    $stmt = $db->prepare($sql);

    $count = 0;
    foreach ($games as $game) {
        $gid = $game['Gid'];
        $pubid = $game['publisherid'];

        // Add 2-3 reviews per game
        $numReviews = rand(2, 3);
        for ($i = 0; $i < $numReviews; $i++) {
            $uid = $users[array_rand($users)];
            $content = $comments[array_rand($comments)];
            $rating = rand(3, 5);
            // Random date within last 30 days
            $time = date('Y-m-d', strtotime("-" . rand(0, 30) . " days"));

            try {
                $stmt->execute([
                    ':time' => $time,
                    ':uid' => $uid,
                    ':gid' => $gid,
                    ':pubid' => $pubid,
                    ':content' => $content,
                    ':rating' => $rating
                ]);
                $count++;
            } catch (PDOException $e) {
                // Ignore duplicates (PK is time + uid, so collision is possible but unlikely with random days)
                // echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "Successfully inserted $count game reviews.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
