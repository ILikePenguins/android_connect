-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2015 at 09:36 PM
-- Server version: 5.6.22-enterprise-commercial-advanced-log
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tabulate`
--

-- --------------------------------------------------------

--
-- Table structure for table `beers`
--

CREATE TABLE IF NOT EXISTS `beers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `beers`
--

INSERT INTO `beers` (`id`, `name`) VALUES
(39, 'bottle'),
(43, 'bottt'),
(26, 'caff'),
(31, 'caff10'),
(33, 'caff11'),
(35, 'caff12'),
(28, 'caff2'),
(30, 'caff5'),
(5, 'caffiend'),
(12, 'caffiend123'),
(13, 'caffiend124'),
(15, 'caffiend126'),
(17, 'caffiend129'),
(21, 'caffiend180'),
(22, 'caffiend181'),
(24, 'caffiend182'),
(19, 'caffiend190'),
(7, 'caffiend2'),
(9, 'caffiend6'),
(11, 'caffiend9'),
(2, 'Chocolate Stout'),
(41, 'eee'),
(42, 'gggg'),
(40, 'kkjlk'),
(37, 'piiiiint'),
(36, 'pkp'),
(38, 'ppppppppl');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`) VALUES
(3, 'asdas'),
(2, 'Chocolate Stout'),
(5, 'ffff'),
(6, 'ggggg'),
(7, 'jjjjjj'),
(11, 'lllll'),
(8, 'qqqqq'),
(1, 'rosie'),
(10, 'rosiejjjj'),
(4, 'seefas');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `date`) VALUES
(1, 'feer', '2015-01-20'),
(2, 'feb', '2014-12-03'),
(3, 'rosie', '2014-12-03'),
(5, 'Febrewary', '2015-01-28');

-- --------------------------------------------------------

--
-- Table structure for table `product_info`
--

CREATE TABLE IF NOT EXISTS `product_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beer_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `quantity` float(4,1) DEFAULT '0.0',
  `cost_each` float(4,1) DEFAULT '0.0',
  `event_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `beer_id` (`beer_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `product_info`
--

INSERT INTO `product_info` (`id`, `beer_id`, `type`, `quantity`, `cost_each`, `event_id`) VALUES
(2, 24, 2, 0.0, 4.0, 1),
(3, 28, 1, -7.0, 3.0, 1),
(4, 35, 2, 51.0, 3.0, 1),
(5, 36, 2, 96.0, 9.0, 1),
(6, 38, 1, -4.0, 3.0, 1),
(7, 39, 2, 95.0, 6.0, 1),
(8, 40, 1, 0.0, 3.0, 1),
(9, 40, 2, 2.0, 3.0, 1),
(10, 41, 2, 3.0, 4.0, 1),
(11, 42, 1, 0.0, 6.0, 1),
(12, 43, 2, 33.0, 98.0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `events_customers`
--

CREATE TABLE IF NOT EXISTS `events_customers` (
  `event_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`, `customer_id`),
  KEY `event_id` (`event_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beer_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `quantity` float(4,1) DEFAULT '0.0',
  `cost_each` float(4,1) DEFAULT '0.0',
  `cost_total` float(6,1) DEFAULT '0.0',
  PRIMARY KEY (`id`),
  KEY `beer_id` (`beer_id`),
  KEY `event_id` (`event_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `beer_id`, `event_id`, `customer_id`, `type`, `quantity`, `cost_each`, `cost_total`) VALUES
(1, 5, 1, 1, 2, 3.0, 9.0, 27.0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_info`
--
ALTER TABLE `product_info`
  ADD CONSTRAINT `product_info_ibfk_1` FOREIGN KEY (`beer_id`) REFERENCES `beers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_info_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_customers`
--
ALTER TABLE `events_customers`
  ADD CONSTRAINT `events_customers_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `events_customers_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`beer_id`) REFERENCES `beers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
