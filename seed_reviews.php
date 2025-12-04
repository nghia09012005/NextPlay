<?php
require_once 'config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get some users
    $stmt = $db->query("SELECT uid FROM User LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($users)) {
        die("No users found to create reviews. Please create users first.\n");
    }

    // Get some news
    $stmt = $db->query("SELECT id FROM News LIMIT 5");
    $newsList = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($newsList)) {
        die("No news found to create reviews. Please create news first.\n");
    }

    $comments = [
        "Bài viết rất hay và bổ ích!",
        "Hóng giải đấu này quá đi mất.",
        "Game này đồ họa đẹp nhưng gameplay hơi chán.",
        "Cảm ơn admin đã chia sẻ thông tin.",
        "Có ai lập team tham gia không?",
        "Thông tin rất chi tiết, 10 điểm.",
        "Mong chờ bản cập nhật tiếp theo.",
        "Sony làm tốt lắm, PS6 chắc chắn sẽ hot.",
        "GTA VI mãi đỉnh!",
        "Bài review rất có tâm."
    ];

    echo "Seeding reviews...\n";

    $sql = "INSERT INTO Review (customerid, news_id, content, rating, review_time) VALUES (:uid, :nid, :content, :rating, :time)";
    $stmt = $db->prepare($sql);

    $count = 0;
    foreach ($newsList as $newsId) {
        // Add 2-3 comments per news
        $numComments = rand(2, 3);
        for ($i = 0; $i < $numComments; $i++) {
            $uid = $users[array_rand($users)];
            $content = $comments[array_rand($comments)];
            $rating = rand(4, 5);
            $time = date('Y-m-d H:i:s', strtotime("-" . rand(1, 10) . " days"));

            try {
                $stmt->execute([
                    ':uid' => $uid,
                    ':nid' => $newsId,
                    ':content' => $content,
                    ':rating' => $rating,
                    ':time' => $time
                ]);
                $count++;
            } catch (PDOException $e) {
                // Ignore duplicates or errors for now
                // echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "Successfully inserted $count reviews.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
