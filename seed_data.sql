-- Add columns if they don't exist (safe update)
SET @dbname = DATABASE();
SET @tablename = "News";
SET @columnname = "category";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE News ADD COLUMN category VARCHAR(100);"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = "source";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE News ADD COLUMN source VARCHAR(100);"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Clear existing sample data to avoid duplicates (optional, be careful in prod)
-- DELETE FROM News WHERE id <= 20; 
-- DELETE FROM Receives_feedback;

-- Insert Sample News
INSERT INTO News (title, content, thumbnail, author_id, views, category, source, created_at) VALUES
('Giải đấu NextPlay Championship 2025 chính thức khởi tranh', 'Sự kiện eSports lớn nhất năm đã trở lại với tổng giải thưởng lên đến 1 tỷ đồng. Các đội tuyển hàng đầu sẽ tranh tài...', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop', 25, 1200, 'Esports', 'NextPlay Esports', '2025-12-01 08:00:00'),
('Top 10 game nhập vai đáng chơi nhất tháng 12', 'Tổng hợp những tựa game RPG đình đám vừa ra mắt. Danh sách bao gồm những cái tên được mong chờ nhất...', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2071&auto=format&fit=crop', 25, 850, 'Review', 'GameK', '2025-11-28 09:30:00'),
('Bản cập nhật mới của Cyber Future có gì hot?', 'Nhà phát triển vừa tung ra bản vá lỗi lớn cùng DLC mở rộng bản đồ. Người chơi sẽ được trải nghiệm khu vực mới...', 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2070&auto=format&fit=crop', 25, 2100, 'Tin Game', 'GenK', '2025-11-25 14:15:00'),
('Hướng dẫn build PC chơi game giá rẻ năm 2025', 'Tối ưu hiệu năng trên giá thành với cấu hình PC gaming tầm trung. Chỉ với 15 triệu đồng, bạn có thể chiến tốt...', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?q=80&w=2070&auto=format&fit=crop', 25, 3500, 'Công nghệ', 'TinhTe', '2025-11-20 10:00:00'),
('Review: Lost Kingdom - Siêu phẩm hay bom xịt?', 'Đánh giá chi tiết tựa game được mong chờ nhất năm. Cốt truyện sâu sắc nhưng gameplay còn nhiều sạn...', 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2165&auto=format&fit=crop', 25, 1500, 'Review', 'Game4V', '2025-11-15 16:45:00'),
('Cộng đồng game thủ Việt nói gì về sự kiện sắp tới?', 'Những ý kiến trái chiều xoay quanh việc thay đổi thể thức thi đấu. Nhiều người ủng hộ nhưng cũng không ít...', 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?q=80&w=2070&auto=format&fit=crop', 25, 500, 'Cộng đồng', 'ThanhNien', '2025-11-10 11:20:00'),
('Sony công bố PlayStation 6: Cấu hình khủng khiếp', 'Những thông tin rò rỉ đầu tiên về thế hệ console tiếp theo. Sức mạnh xử lý đồ họa được nâng cấp gấp đôi...', 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop', 25, 5000, 'Công nghệ', 'Sony', '2025-11-05 08:30:00'),
('GTA VI lộ diện trailer mới: Đồ họa siêu thực', 'Rockstar Games tiếp tục khiến cộng đồng đứng ngồi không yên. Trailer mới hé lộ bối cảnh Vice City hiện đại...', 'https://images.unsplash.com/photo-1628260412297-a3377e45006f?q=80&w=2000&auto=format&fit=crop', 25, 8000, 'Tin Game', 'Rockstar', '2025-11-01 20:00:00');

-- Insert Sample Reviews (Receives_feedback)
-- Assuming customerid 25 exists (minhtien2005) and Gid 1, 2, 3 exist
-- Note: Receives_feedback PK is (feedback_time, customerid), so we need distinct times

INSERT INTO Receives_feedback (feedback_time, customerid, Gid, content, rating) VALUES
('2025-12-01', 25, 1, 'Game quá hay, đồ họa đẹp, cốt truyện cuốn hút. Rất đáng tiền!', 5),
('2025-11-25', 25, 1, 'Gameplay ổn, nhưng server đôi khi hơi lag. Hy vọng sẽ fix sớm.', 4),
('2025-11-20', 25, 2, 'Không hợp gu mình lắm, combat hơi chán.', 3),
('2025-12-02', 25, 3, 'Tuyệt vời ông mặt trời! 10/10', 5);
