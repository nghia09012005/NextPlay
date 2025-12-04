<?php
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create PageContent table
    $sql = "CREATE TABLE IF NOT EXISTS `PageContent` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `page_key` varchar(50) NOT NULL,
        `section_key` varchar(50) NOT NULL,
        `content_value` text NOT NULL,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_content` (`page_key`, `section_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "Table PageContent created successfully.\n";

    // Seed initial data
    $initialData = [
        ['contact', 'address', '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh, Việt Nam'],
        ['contact', 'email', 'support@nextplay.com'],
        ['contact', 'hotline', '1900 1234'],
        ['contact', 'facebook', '#'],
        ['contact', 'instagram', '#'],
        ['contact', 'twitter', '#'],
        ['contact', 'youtube', '#']
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO PageContent (page_key, section_key, content_value) VALUES (?, ?, ?)");
    
    foreach ($initialData as $row) {
        $stmt->execute($row);
    }
    echo "Initial data seeded.\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
