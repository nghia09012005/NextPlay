-- Bảng 1: User
INSERT INTO `User` (`uid`, `uname`, `avatar`, `email`, `password`, `DOB`, `lname`, `fname`)
VALUES
(1, 'john_doe', 'avatar1.png', 'john@example.com', 'pass123', '1990-01-01', 'Doe', 'John'),
(2, 'jane_smith', 'avatar2.png', 'jane@example.com', 'pass456', '1992-05-10', 'Smith', 'Jane'),
(3, 'admin1', NULL, 'admin1@example.com', 'adminpass', '1985-03-15', 'Admin', 'One'),
(4, 'publisher1', NULL, 'pub1@example.com', 'pubpass', '1980-07-20', 'Publisher', 'One');

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
(4, 'Top game publisher', 'TX12345', 'New York');

-- Bảng 5: Game
INSERT INTO `Game` (`Gid`, `name`, `version`, `description`, `cost`, `adminid`, `publisherid`)
VALUES
(101, 'Game A', '1.0', 'First game', 29.99, 3, 4),
(102, 'Game B', '1.2', 'Second game', 39.99, 3, 4);

-- Bảng 6: Category
INSERT INTO `Category` (`catId`, `name`, `description`)
VALUES
(1, 'Action', 'Action games'),
(2, 'Adventure', 'Adventure games');

-- Bảng 7: Review
INSERT INTO `Review` (`customerid`, `Gid`, `review_time`, `content`, `rating`)
VALUES
(1, 101, CURRENT_TIMESTAMP, 'Great game!', 5),
(2, 102, CURRENT_TIMESTAMP, 'Pretty good', 4);

-- Bảng 8: Library
INSERT INTO `Library` (`uid`, `libname`)
VALUES
(1, 'MyLibrary'),
(2, 'JaneLib');

INSERT INTO `Lib_game` (`Gid`, `libname`, `uid`)
VALUES
(101, 'MyLibrary', 1),
(102, 'JaneLib', 2);

-- Bảng 9: Wishlist
INSERT INTO `Wishlist` (`uid`, `wishname`)
VALUES
(1, 'WishList1'),
(2, 'WishList2');

INSERT INTO `Wish_game` (`Gid`, `wishname`, `uid`)
VALUES
(102, 'WishList1', 1),
(101, 'WishList2', 2);

-- Bảng 10: Cart (Weak Entity)
INSERT INTO `Cart` (`uid`, `status`, `created_date`)
VALUES
(1, 'active', CURRENT_DATE),
(2, 'active', CURRENT_DATE);

INSERT INTO `Cart_game` (`Gid`, `uid`, `cart_status`, `quantity`)
VALUES
(101, 1, 'active', 1),
(102, 2, 'active', 2);

-- Bảng Game_cat
INSERT INTO `Game_cat` (`Gid`, `catId`)
VALUES
(101, 1),
(102, 2);

-- Bảng Receives_feedback
INSERT INTO `Receives_feedback` (`feedback_time`, `customerid`, `Gid`, `publisherid`)
VALUES
(CURRENT_DATE, 1, 101, 4),
(CURRENT_DATE, 2, 102, 4);
