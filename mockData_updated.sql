-- Bảng 1: User
INSERT INTO `User` (`uid`, `uname`, `avatar`, `email`, `password`, `DOB`, `lname`, `fname`)
VALUES
(1, 'john_doe', 'avatar1.png', 'john@example.com', 'pass123', '1990-01-01', 'Doe', 'John'),
(2, 'jane_smith', 'avatar2.png', 'jane@example.com', 'pass456', '1992-05-10', 'Smith', 'Jane'),
(3, 'admin1', 'admin.png', 'admin@example.com', 'adminpass', '1985-03-15', 'Admin', 'System'),
(4, 'pub1', 'publisher.png', 'publisher@example.com', 'pub123', '1980-07-20', 'Game', 'Publisher');

-- Bảng 2: Customer
INSERT INTO `Customer` (`uid`, `balance`)
VALUES
(1, 100.00),
(2, 50.00);

-- Bảng 3: Admin
INSERT INTO `Admin` (`uid`, `startdate`)
VALUES
(3, '2025-01-01');

-- Bảng 4: Publisher
INSERT INTO `Publisher` (`uid`, `description`, `taxcode`, `location`)
VALUES
(4, 'Leading game publisher', 'PUB12345', 'San Francisco');

-- Bảng 5: Game
INSERT INTO `Game` (`Gid`, `name`, `version`, `description`, `price`, `thumbnail`, `category`, `tags`, `developer`, `publisher`, `release_date`, `rating`, `reviews`, `adminid`, `publisherid`)
VALUES
(101, 'Adventure Quest', '1.0', 'An epic adventure game', 29.99, 'adventure.jpg', 'Adventure', 'rpg,openworld', 'GameDev Studio', 'GamePub Inc', '2025-01-15', 4.5, 120, 3, 4),
(102, 'Space Warriors', '2.1', 'Fight in space battles', 39.99, 'space.jpg', 'Action', 'shooter,multiplayer', 'SpaceDev', 'GamePub Inc', '2025-02-20', 4.2, 85, 3, 4);

-- Bảng 6: Category
INSERT INTO `Category` (`catId`, `name`, `description`)
VALUES
(1, 'Action', 'Action-packed games'),
(2, 'Adventure', 'Exploration and story games'),
(3, 'RPG', 'Role-playing games');

-- Bảng 10: News
INSERT INTO `News` (`id`, `title`, `content`, `thumbnail`, `author_id`, `created_at`, `views`)
VALUES
(1, 'New Game Release', 'Check out our latest game!', 'news1.jpg', 3, '2025-11-01 10:00:00', 1500),
(2, 'Winter Sale', 'Biggest sale of the year!', 'sale.jpg', 3, '2025-12-01 09:00:00', 2300);

-- Bảng 7: Review
INSERT INTO `Review` (`customerid`, `news_id`, `review_time`, `content`, `rating`)
VALUES
(1, 1, '2025-11-02 14:30:00', 'Great game!', 5),
(2, 2, '2025-12-02 15:45:00', 'Amazing deals!', 4);

-- Bảng 8: Library
INSERT INTO `Library` (`uid`, `libname`)
VALUES
(1, 'My Games'),
(2, 'Favorites');

-- Bảng Lib_game
INSERT INTO `Lib_game` (`Gid`, `libname`, `uid`)
VALUES
(101, 'My Games', 1),
(102, 'Favorites', 2);

-- Bảng 9: Wishlist
INSERT INTO `Wishlist` (`uid`, `wishname`)
VALUES
(1, 'Want to Play'),
(2, 'Wishlist');

-- Bảng Wish_game
INSERT INTO `Wish_game` (`Gid`, `wishname`, `uid`)
VALUES
(102, 'Want to Play', 1),
(101, 'Wishlist', 2);

-- Bảng 10: Cart
INSERT INTO `Cart` (`uid`, `status`, `created_date`)
VALUES
(1, 'active', '2025-12-02'),
(2, 'pending', '2025-12-01');

-- Bảng Cart_game
INSERT INTO `Cart_game` (`Gid`, `uid`, `cart_status`, `quantity`)
VALUES
(101, 1, 'active', 1),
(102, 2, 'pending', 2);

-- Bảng Game_cat
INSERT INTO `Game_cat` (`Gid`, `catId`)
VALUES
(101, 2), -- Adventure
(101, 3), -- RPG
(102, 1); -- Action

-- Bảng Receives_feedback
INSERT INTO `Receives_feedback` (`feedback_time`, `customerid`, `Gid`, `publisherid`)
VALUES
('2025-11-15', 1, 101, 4),
('2025-11-20', 2, 102, 4);
