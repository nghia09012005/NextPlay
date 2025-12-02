<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create pages table
    $sql = "CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(50) NOT NULL UNIQUE,
        title VARCHAR(255) NOT NULL,
        content JSON,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($sql);
    echo "Table 'pages' created successfully.\n";

    // Initial Data for About Page
    $slug = 'about';
    $title = 'Về Chúng Tôi';
    $content = json_encode([
        'hero_title' => 'NEXTPLAY - KỶ NGUYÊN GAME MỚI',
        'hero_subtitle' => 'Nơi đam mê hội tụ, nơi game thủ tỏa sáng. Chúng tôi không chỉ bán game, chúng tôi kiến tạo trải nghiệm.',
        'intro_title' => 'Sứ Mệnh Của Chúng Tôi',
        'intro_text' => 'NextPlay ra đời với sứ mệnh kết nối cộng đồng game thủ Việt Nam với những tựa game đỉnh cao thế giới. Chúng tôi cam kết mang đến nền tảng phân phối game bản quyền uy tín, tốc độ và giá cả hợp lý nhất. Hơn cả một cửa hàng, NextPlay là ngôi nhà chung cho những ai yêu thích thế giới ảo.',
        'intro_image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2071&auto=format&fit=crop',
        'features' => [
            [
                'title' => 'Kho Game Khổng Lồ',
                'desc' => 'Hàng ngàn tựa game AAA và Indie được cập nhật liên tục.',
                'icon' => 'bi-controller'
            ],
            [
                'title' => 'Bảo Mật Tuyệt Đối',
                'desc' => 'Hệ thống thanh toán và bảo vệ tài khoản chuẩn quốc tế.',
                'icon' => 'bi-shield-check'
            ],
            [
                'title' => 'Hỗ Trợ 24/7',
                'desc' => 'Đội ngũ hỗ trợ nhiệt tình, sẵn sàng giải đáp mọi thắc mắc.',
                'icon' => 'bi-headset'
            ]
        ],
        'stats' => [
            ['value' => '10K+', 'label' => 'Người dùng'],
            ['value' => '500+', 'label' => 'Tựa game'],
            ['value' => '99%', 'label' => 'Hài lòng']
        ]
    ], JSON_UNESCAPED_UNICODE);

    // Insert or Update
    $stmt = $db->prepare("INSERT INTO pages (slug, title, content) VALUES (:slug, :title, :content) 
                          ON DUPLICATE KEY UPDATE title = :title, content = :content");
    $stmt->bindParam(':slug', $slug);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->execute();
    echo "Initial data for 'about' page inserted/updated successfully.\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
