-- phpMyAdmin SQL Dump
-- version 4.0.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 26, 2013 at 12:17 AM
-- Server version: 5.5.31-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `autohomepi`
--
CREATE DATABASE IF NOT EXISTS `autohomepi` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `autohomepi`;

-- --------------------------------------------------------

--
-- Table structure for table `switches`
--

CREATE TABLE IF NOT EXISTS `switches` (
  `switch_id` int(2) unsigned NOT NULL COMMENT 'Switch ID on GUI',
  `switch_state` varchar(3) NOT NULL DEFAULT 'off' COMMENT 'Current switch status',
  `switch_descr` varchar(80) DEFAULT NULL COMMENT 'Switch Description',
  `switch_gpio` int(2) NOT NULL COMMENT 'RPi.GPIO Associated with Switch',
  UNIQUE KEY `switch_id` (`switch_id`,`switch_gpio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Switch status, description and RPi.GPIO binding';

--
-- Dumping data for table `switches`
--

INSERT INTO `switches` (`switch_id`, `switch_state`, `switch_descr`, `switch_gpio`) VALUES
(0, 'off', 'Sandas ka Lightwa', 11),
(1, 'on', 'ACba', 12),
(2, 'off', 'Balabwa', 13),
(3, 'off', 'Tubewa', 15),
(4, 'on', 'Pankhwa', 16);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique User ID',
  `username` varchar(20) NOT NULL COMMENT 'Unique Username',
  `password` varchar(32) NOT NULL COMMENT 'Encrypted user pswd',
  `name` varchar(20) DEFAULT NULL COMMENT 'User''s Name',
  `user_type` varchar(6) NOT NULL COMMENT 'User''s type (admin|normal)',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`,`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='User Authentication and Identification Table' AUTO_INCREMENT=101 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `name`, `user_type`) VALUES
(1, 'norm', '589275fdd4e5908f18310b56beaf439b', 'Normal User', 'normal'),
(100, 'pranav', 'ae2b1fca515949e5d54fb22b8ed95575', 'Pranav', 'admin');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
