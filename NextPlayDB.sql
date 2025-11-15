-- Bảng 1: User
CREATE TABLE `User` (
    `uid` INT AUTO_INCREMENT PRIMARY KEY,
    `uname` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(255),
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `DOB` DATE,
    `lname` VARCHAR(255),
    `fname` VARCHAR(255)
);

-- Bảng 2: Customer
CREATE TABLE `Customer` (
    `uid` INT AUTO_INCREMENT PRIMARY KEY,
    `balance` DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (`uid`) REFERENCES `User`(`uid`)
);

-- Bảng 3: Admin
CREATE TABLE `Admin` (
    `uid` INT AUTO_INCREMENT PRIMARY KEY,
    `startdate` DATE,
    FOREIGN KEY (`uid`) REFERENCES `User`(`uid`)
);

-- Bảng 4: Publisher
CREATE TABLE `Publisher` (
    `uid` INT AUTO_INCREMENT PRIMARY KEY,
    `description` TEXT,
    `taxcode` VARCHAR(50),
    `location` VARCHAR(255),
    FOREIGN KEY (`uid`) REFERENCES `User`(`uid`)
);

-- Bảng 5: Game
CREATE TABLE `Game` (
    `Gid` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `version` VARCHAR(50),
    `description` TEXT,
    `cost` DECIMAL(8,2) NOT NULL,
    `adminid` INT,
    `publisherid` INT,
    FOREIGN KEY (`adminid`) REFERENCES `Admin`(`uid`),
    FOREIGN KEY (`publisherid`) REFERENCES `Publisher`(`uid`)
);

-- Bảng 6: Category
CREATE TABLE `Category` (
    `catId` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) UNIQUE NOT NULL,
    `description` TEXT
);

-- Bảng 7: Review (Customer đánh giá Game)
CREATE TABLE `Review` (
    `customerid` INT,
    `Gid` INT,
    `review_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `content` TEXT,
    `rating` INT CHECK (`rating` BETWEEN 1 AND 5),
    PRIMARY KEY (`customerid`, `Gid`),
    FOREIGN KEY (`customerid`) REFERENCES `Customer`(`uid`),
    FOREIGN KEY (`Gid`) REFERENCES `Game`(`Gid`)
);

-- Bảng 8: Library
CREATE TABLE `Library` (
    `uid` INT,
    `libname` VARCHAR(50),
    PRIMARY KEY (`uid`, `libname`),
    FOREIGN KEY (`uid`) REFERENCES `Customer`(`uid`)
);

CREATE TABLE `Lib_game` (
    `Gid` INT,
    `libname` VARCHAR(50),
    `uid` INT,
    PRIMARY KEY (`Gid`, `libname`, `uid`),
    FOREIGN KEY (`Gid`) REFERENCES `Game`(`Gid`),
    FOREIGN KEY (`uid`, `libname`) REFERENCES `Library`(`uid`, `libname`)
);

-- Bảng 9: Wishlist
CREATE TABLE `Wishlist` (
    `uid` INT,
    `wishname` VARCHAR(50),
    PRIMARY KEY (`uid`, `wishname`),
    FOREIGN KEY (`uid`) REFERENCES `Customer`(`uid`)
);

CREATE TABLE `Wish_game` (
    `Gid` INT,
    `wishname` VARCHAR(50),
    `uid` INT,
    PRIMARY KEY (`Gid`, `wishname`, `uid`),
    FOREIGN KEY (`Gid`) REFERENCES `Game`(`Gid`),
    FOREIGN KEY (`uid`, `wishname`) REFERENCES `Wishlist`(`uid`, `wishname`)
);

-- Bảng 10: Cart (Weak Entity, PK = uid + status)
CREATE TABLE `Cart` (
    `uid` INT,
    `status` VARCHAR(30),
    `created_date` DATE DEFAULT CURRENT_DATE,
    PRIMARY KEY (`uid`, `status`),
    FOREIGN KEY (`uid`) REFERENCES `Customer`(`uid`)
);

CREATE TABLE `Cart_game` (
    `Gid` INT,
    `uid` INT,
    `cart_status` VARCHAR(30),
    `quantity` INT DEFAULT 1,
    PRIMARY KEY (`Gid`, `uid`, `cart_status`),
    FOREIGN KEY (`Gid`) REFERENCES `Game`(`Gid`),
    FOREIGN KEY (`uid`, `cart_status`) REFERENCES `Cart`(`uid`, `status`)
);

-- Bảng liên kết Game – Category
CREATE TABLE `Game_cat` (
    `Gid` INT,
    `catId` INT,
    PRIMARY KEY (`Gid`, `catId`),
    FOREIGN KEY (`Gid`) REFERENCES `Game`(`Gid`),
    FOREIGN KEY (`catId`) REFERENCES `Category`(`catId`)
);

-- Bảng Receives_feedback
CREATE TABLE `Receives_feedback` (
    `feedback_time` DATE,
    `customerid` INT,
    `Gid` INT,
    `publisherid` INT,
    PRIMARY KEY (`feedback_time`, `customerid`),
    FOREIGN KEY (`customerid`) REFERENCES `Customer`(`uid`),
    FOREIGN KEY (`Gid`) REFERENCES `Game`(`Gid`),
    FOREIGN KEY (`publisherid`) REFERENCES `Publisher`(`uid`)
);
