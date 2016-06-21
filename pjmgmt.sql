-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 19, 2016 at 12:46 PM
-- Server version: 5.6.26
-- PHP Version: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pjmgmt`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE IF NOT EXISTS `address` (
  `aid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `latitude` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `borrow`
--

CREATE TABLE IF NOT EXISTS `borrow` (
  `bid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `message` text NOT NULL,
  `isAccepted` tinyint(1) NOT NULL DEFAULT '0',
  `isReturned` tinyint(1) NOT NULL DEFAULT '1',
  `borrowedCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Triggers `borrow`
--
DELIMITER $$
CREATE TRIGGER `borrow_check` AFTER INSERT ON `borrow`
 FOR EACH ROW BEGIN
	IF NEW.isAccepted = 0 AND NEW.isReturned = 1 THEN
    	BEGIN
        	UPDATE item SET status=1 WHERE iid=NEW.iid;
        END;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `return_check` AFTER UPDATE ON `borrow`
 FOR EACH ROW BEGIN
	IF NEW.isAccepted = 1 AND OLD.isAccepted = 0 THEN
    	BEGIN
        	UPDATE item SET status=2 WHERE iid=NEW.iid;
        END;
    ELSEIF NEW.isReturned = 1 AND OLD.isReturned = 0 THEN
    	BEGIN
        	UPDATE item SET status=0 WHERE iid=NEW.iid;
        END;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `cid` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `commentCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE IF NOT EXISTS `item` (
  `iid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `itemName` varchar(50) NOT NULL,
  `itemImageSrc` varchar(100) NOT NULL DEFAULT '0.jpg',
  `money` double NOT NULL,
  `period` varchar(20) NOT NULL,
  `expressType` varchar(50) NOT NULL,
  `aid` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `itemIntroduction` text NOT NULL,
  `itemCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `mid` bigint(20) NOT NULL,
  `messageCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suid` int(11) NOT NULL,
  `ruid` int(11) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `rid` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `rating` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `replycomment`
--

CREATE TABLE IF NOT EXISTS `replycomment` (
  `rcid` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `replyCommentCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reply` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `totalrating`
--
CREATE TABLE IF NOT EXISTS `totalrating` (
`iid` int(11)
,`rating` double
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL,
  `uname` varchar(15) NOT NULL,
  `passhash` varchar(256) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `gender` int(11) NOT NULL,
  `pic` varchar(100) NOT NULL,
  `email` varchar(25) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '0',
  `userCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `totalrating`
--
DROP TABLE IF EXISTS `totalrating`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `totalrating` AS select `rating`.`iid` AS `iid`,avg(`rating`.`rating`) AS `rating` from `rating` group by `rating`.`iid`;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`aid`),
  ADD KEY `addressuserkey` (`uid`);

--
-- Indexes for table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `borrowitemkey` (`iid`),
  ADD KEY `borrwouserkey` (`uid`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `commentitemkey` (`iid`),
  ADD KEY `commentuserkey` (`uid`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`iid`),
  ADD KEY `userkey` (`uid`),
  ADD KEY `addresskey` (`aid`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`mid`),
  ADD KEY `messageuser1key` (`suid`),
  ADD KEY `messageuser2key` (`ruid`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rid`),
  ADD KEY `ratinguserkey` (`uid`),
  ADD KEY `ratingitemkey` (`iid`);

--
-- Indexes for table `replycomment`
--
ALTER TABLE `replycomment`
  ADD PRIMARY KEY (`rcid`),
  ADD KEY `replyitemkey` (`iid`),
  ADD KEY `replyuserkey` (`uid`),
  ADD KEY `replycommentkey` (`cid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `borrow`
--
ALTER TABLE `borrow`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `iid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `mid` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `replycomment`
--
ALTER TABLE `replycomment`
  MODIFY `rcid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `borrow`
--
ALTER TABLE `borrow`
  ADD CONSTRAINT `borrow_ibfk_2` FOREIGN KEY (`iid`) REFERENCES `item` (`iid`),
  ADD CONSTRAINT `borrow_ibfk_3` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`iid`) REFERENCES `item` (`iid`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `address` (`aid`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`suid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`ruid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`iid`) REFERENCES `item` (`iid`);

--
-- Constraints for table `replycomment`
--
ALTER TABLE `replycomment`
  ADD CONSTRAINT `replycomment_ibfk_1` FOREIGN KEY (`iid`) REFERENCES `item` (`iid`),
  ADD CONSTRAINT `replycomment_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `replycomment_ibfk_3` FOREIGN KEY (`cid`) REFERENCES `comment` (`cid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
