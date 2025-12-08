<?php
require_once 'config/database.php';

header('Content-Type: text/plain');

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "Starting System Settings Migration...\n";

    // 1. Create system_settings table
    $sql = "CREATE TABLE IF NOT EXISTS `system_settings` (
        `setting_key` VARCHAR(50) NOT NULL PRIMARY KEY,
        `setting_value` LONGTEXT CHECK (json_valid(`setting_value`)),
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "[OK] Table 'system_settings' created/verified.\n";

    // 2. Migrate 'General' data (from pagecontent)
    // We fetch existing data to build the JSON object
    $generalData = [
        'company_name' => 'NextPlay',
        'slogan' => 'Nền tảng phân phối game bản quyền.',
        'logo' => '/assets/images/logo.png',
        'contact' => [
            'address' => '',
            'email' => '',
            'hotline' => '',
            'facebook' => '',
            'twitter' => '',
            'instagram' => ''
        ]
    ];

    // Fetch from pagecontent if exists
    // Note: This assumes pagecontent table exists. If not, we use defaults.
    try {
        $stmt = $db->query("SELECT * FROM pagecontent");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            if ($row['page_key'] == 'system' && $row['section_key'] == 'info') {
                if ($row['content_key'] == 'company_name') $generalData['company_name'] = $row['content_value'];
                if ($row['content_key'] == 'slogan') $generalData['slogan'] = $row['content_value'];
                if ($row['content_key'] == 'address') $generalData['contact']['address'] = $row['content_value'];
                if ($row['content_key'] == 'email') $generalData['contact']['email'] = $row['content_value'];
                if ($row['content_key'] == 'hotline') $generalData['contact']['hotline'] = $row['content_value'];
                // Socials
                if ($row['content_key'] == 'facebook_url') $generalData['contact']['facebook'] = $row['content_value'];
            }
            if ($row['page_key'] == 'system' && $row['section_key'] == 'assets') {
                if ($row['content_key'] == 'logo') $generalData['logo'] = $row['content_value'];
                if ($row['content_key'] == 'favicon') $generalData['favicon'] = $row['content_value'];
            }
        }
    } catch (Exception $e) {
        echo "[WARN] Could not fetch from pagecontent: " . $e->getMessage() . "\n";
    }

    // Insert 'general'
    $stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('general', :val) ON DUPLICATE KEY UPDATE setting_value = :val");
    $json = json_encode($generalData, JSON_UNESCAPED_UNICODE);
    $stmt->bindParam(':val', $json);
    $stmt->execute();
    echo "[OK] 'general' settings migrated.\n";


    // 3. Migrate 'About Us' data (from pages table)
    $aboutData = [
        'hero' => [
            'title' => 'Về Chúng Tôi',
            'subtitle' => 'Chào mừng đến với NextPlay'
        ],
        'intro' => [
            'title' => 'Câu chuyện của chúng tôi',
            'content' => '...',
            'image' => ''
        ],
        'stats' => [],
        'features' => []
    ];

    try {
        $stmt = $db->query("SELECT content FROM pages WHERE slug = 'about' LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['content'])) {
            $existingJson = json_decode($row['content'], true);
            
            // Map existing structure to new structure
            if (isset($existingJson['hero_title'])) $aboutData['hero']['title'] = $existingJson['hero_title'];
            if (isset($existingJson['hero_subtitle'])) $aboutData['hero']['subtitle'] = $existingJson['hero_subtitle'];
            
            if (isset($existingJson['intro_title'])) $aboutData['intro']['title'] = $existingJson['intro_title'];
            if (isset($existingJson['intro_text'])) $aboutData['intro']['content'] = $existingJson['intro_text'];
            if (isset($existingJson['intro_image'])) $aboutData['intro']['image'] = $existingJson['intro_image'];

            if (isset($existingJson['stats'])) $aboutData['stats'] = $existingJson['stats'];
            if (isset($existingJson['features'])) $aboutData['features'] = $existingJson['features'];
        }
    } catch (Exception $e) {
        echo "[WARN] Could not fetch from pages table: " . $e->getMessage() . "\n";
    }

    // Insert 'about_us'
    $stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('about_us', :val) ON DUPLICATE KEY UPDATE setting_value = :val");
    $json = json_encode($aboutData, JSON_UNESCAPED_UNICODE);
    $stmt->bindParam(':val', $json);
    $stmt->execute();
    echo "[OK] 'about_us' settings migrated.\n";
    
    // 4. Create 'home_config' (Hero section)
    // Fetch from pagecontent 'home' 'hero'
     $homeData = [
        'hero' => [
            'title' => 'Game',
            'description' => '',
            'badge' => '',
            'btn_text' => 'Khám phá',
            'btn_url' => '#'
        ],
        'stats' => [],
        'services' => ['title' => 'Dịch vụ']
    ];
    
    try {
        $stmt = $db->query("SELECT * FROM pagecontent WHERE page_key='home'");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
             // Hero
             if ($row['section_key'] == 'hero') {
                 if ($row['content_key'] == 'title') $homeData['hero']['title'] = $row['content_value'];
                 if ($row['content_key'] == 'description') $homeData['hero']['description'] = $row['content_value'];
                 if ($row['content_key'] == 'badge_text') $homeData['hero']['badge'] = $row['content_value'];
                 if ($row['content_key'] == 'primary_btn_text') $homeData['hero']['btn_text'] = $row['content_value'];
                 if ($row['content_key'] == 'primary_btn_url') $homeData['hero']['btn_url'] = $row['content_value'];
             }
             // Stats
             if ($row['section_key'] == 'stats') {
                 // Map stat_1_value -> stats[stat_1_value]
                 // Or keep structure simple: stats { stat_1_value: ..., stat_1_label: ... }
                 $homeData['stats'][$row['content_key']] = $row['content_value'];
             }
             // Services
             if ($row['section_key'] == 'services') {
                 if ($row['content_key'] == 'title') $homeData['services']['title'] = $row['content_value'];
             }
        }
    } catch(Exception $e) {}


    $stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('home_config', :val) ON DUPLICATE KEY UPDATE setting_value = :val");
    $json = json_encode($homeData, JSON_UNESCAPED_UNICODE);
    $stmt->bindParam(':val', $json);
    $stmt->execute();
    echo "[OK] 'home_config' settings migrated.\n";


    echo "Migration Completed Successfully.\n";

} catch (Exception $e) {
    echo "[ERROR] Migration Failed: " . $e->getMessage() . "\n";
    http_response_code(500);
}
?>
