<?php
require_once 'config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Attempting to add 'category' column...\n";
    try {
        $sql = "ALTER TABLE News ADD COLUMN category VARCHAR(100)";
        $db->exec($sql);
        echo "Success: 'category' column added.\n";
    } catch (PDOException $e) {
        // Check if error is because column already exists (Error 1060)
        if (strpos($e->getMessage(), "Duplicate column name") !== false) {
            echo "Info: 'category' column already exists.\n";
        } else {
            echo "Error adding 'category': " . $e->getMessage() . "\n";
        }
    }

    echo "Attempting to add 'source' column...\n";
    try {
        $sql = "ALTER TABLE News ADD COLUMN source VARCHAR(100)";
        $db->exec($sql);
        echo "Success: 'source' column added.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), "Duplicate column name") !== false) {
            echo "Info: 'source' column already exists.\n";
        } else {
            echo "Error adding 'source': " . $e->getMessage() . "\n";
        }
    }
    
    // Re-insert sample data to ensure fields are populated
    echo "Re-inserting sample data...\n";
    
    // First, clear existing news to avoid duplicates or partial data
    // $db->exec("DELETE FROM News WHERE id <= 20"); 
    // Actually, let's just update the existing rows if they exist, or insert new ones.
    // For simplicity in this fix, let's truncate and re-seed or just insert.
    // Given the previous insert might have succeeded but with null category/source, 
    // let's try to update them or just delete and re-insert.
    // Deleting is safer for "sample data" integrity.
    
    $db->exec("DELETE FROM News");
    echo "Cleared News table.\n";

    $sql_insert = "INSERT INTO News (title, content, thumbnail, author_id, views, category, source, created_at) VALUES
    ('Giải đấu NextPlay Championship 2025 chính thức khởi tranh', 'Sự kiện eSports lớn nhất năm đã trở lại với tổng giải thưởng lên đến 1 tỷ đồng. Các đội tuyển hàng đầu sẽ tranh tài...', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop', 25, 1200, 'Esports', 'NextPlay Esports', '2025-12-01 08:00:00'),
    ('Top 10 game nhập vai đáng chơi nhất tháng 12', 'Tổng hợp những tựa game RPG đình đám vừa ra mắt. Danh sách bao gồm những cái tên được mong chờ nhất...', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2071&auto=format&fit=crop', 25, 850, 'Review', 'GameK', '2025-11-28 09:30:00'),
    ('Bản cập nhật mới của Cyber Future có gì hot?', 'Nhà phát triển vừa tung ra bản vá lỗi lớn cùng DLC mở rộng bản đồ. Người chơi sẽ được trải nghiệm khu vực mới...', 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2070&auto=format&fit=crop', 25, 2100, 'Tin Game', 'GenK', '2025-11-25 14:15:00'),
    ('Hướng dẫn build PC chơi game giá rẻ năm 2025', 'Tối ưu hiệu năng trên giá thành với cấu hình PC gaming tầm trung. Chỉ với 15 triệu đồng, bạn có thể chiến tốt...', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?q=80&w=2070&auto=format&fit=crop', 25, 3500, 'Công nghệ', 'TinhTe', '2025-11-20 10:00:00'),
    ('Review: Lost Kingdom - Siêu phẩm hay bom xịt?', 'Đánh giá chi tiết tựa game được mong chờ nhất năm. Cốt truyện sâu sắc nhưng gameplay còn nhiều sạn...', 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2165&auto=format&fit=crop', 25, 1500, 'Review', 'Game4V', '2025-11-15 16:45:00'),
    ('Cộng đồng game thủ Việt nói gì về sự kiện sắp tới?', 'Những ý kiến trái chiều xoay quanh việc thay đổi thể thức thi đấu. Nhiều người ủng hộ nhưng cũng không ít...', 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?q=80&w=2070&auto=format&fit=crop', 25, 500, 'Cộng đồng', 'ThanhNien', '2025-11-10 11:20:00'),
    ('Sony công bố PlayStation 6: Cấu hình khủng khiếp', 'Những thông tin rò rỉ đầu tiên về thế hệ console tiếp theo. Sức mạnh xử lý đồ họa được nâng cấp gấp đôi...', 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop', 25, 5000, 'Công nghệ', 'Sony', '2025-11-05 08:30:00'),
    ('GTA VI lộ diện trailer mới: Đồ họa siêu thực', 'Rockstar Games tiếp tục khiến cộng đồng đứng ngồi không yên. Trailer mới hé lộ bối cảnh Vice City hiện đại...', 'https://images.unsplash.com/photo-1628260412297-a3377e45006f?q=80&w=2000&auto=format&fit=crop', 25, 8000, 'Tin Game', 'Rockstar', '2025-11-01 20:00:00')";
    
    $db->exec($sql_insert);
    echo "Sample news re-inserted.\n";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
?>
