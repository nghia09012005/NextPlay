<?php
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create ContactMessages table
    $sql = "CREATE TABLE IF NOT EXISTS `ContactMessages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `subject` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `status` enum('new', 'read', 'replied') DEFAULT 'new',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "Table ContactMessages created successfully.\n";

} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
