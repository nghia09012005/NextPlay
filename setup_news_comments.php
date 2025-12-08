<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create news_comments table
    $query = "CREATE TABLE IF NOT EXISTS `news_comments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `news_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `content` text NOT NULL,
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $stmt = $db->prepare($query);
    if($stmt->execute()) {
        echo "Table 'news_comments' created successfully or already exists.<br>";
    } else {
        echo "Error creating table.<br>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
