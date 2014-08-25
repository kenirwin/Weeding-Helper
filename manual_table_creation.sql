-- If you have to create your db tables manually, use this file! 
--
-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 18, 2012 at 07:55 PM
-- Server version: 5.1.47
-- PHP Version: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
--

-- --------------------------------------------------------

--
-- Table structure for table `controller`
--

CREATE TABLE IF NOT EXISTS `controller` (
  `filename` varchar(255) NOT NULL,
  `file_title` varchar(255) NOT NULL,
  `table_name` varchar(25) NOT NULL,
  `user` varchar(50) NOT NULL,
  `records` int(11) DEFAULT NULL,
  `upload_date` datetime NOT NULL,
  `load_date` datetime DEFAULT NULL,
  `innreach_finished` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='track weeding progress';

--
-- Dumping data for table `controller`
--


-- --------------------------------------------------------

--
-- Table structure for table `table_config`
--

CREATE TABLE IF NOT EXISTS `table_config` (
  `table_name` varchar(25) NOT NULL,
  `action` varchar(255) NOT NULL,
  `field` varchar(255) DEFAULT NULL,
  `printable` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_config`
--

INSERT INTO `table_config` (`table_name`, `action`, `field`, `printable`) VALUES
('default', 'disallowEdit', 'call_order', 'Y'),
('default', 'disallowEdit', 'author', 'Y'),
('default', 'disallowEdit', 'title', 'Y'),
('default', 'omitField', 'publisher', 'Y'),
('default', 'disallowEdit', 'year', 'Y'),
('default', 'disallowEdit', 'lcsh', 'N'),
('default', 'disallowEdit', 'catdate', 'N'),
('default', 'omitField', 'loc', 'N'),
('default', 'disallowEdit', 'call_bib', 'Y'),
('default', 'disallowEdit', 'call_item', 'Y'),
('default', 'disallowEdit', 'volume', 'Y'),
('default', 'disallowEdit', 'copy', 'Y'),
('default', 'omitField', 'bcode', 'N'),
('default', 'omitField', 'mat_type', 'N'),
('default', 'omitField', 'bib_record', 'N'),
('default', 'omitField', 'item_record', 'N'),
('default', 'omitField', 'oclc', 'N'),
('default', 'disallowEdit', 'circs', 'N'),
('default', 'disallowEdit', 'renews', 'N'),
('default', 'disallowEdit', 'int_use', 'N'),
('default', 'disallowEdit', 'last_checkin', 'N'),
('default', 'disallowEdit', 'barcode', 'N'),
('default', 'disallowEdit', 'subclass', 'N'),
('default', 'disallowEdit', 'subj_starts', 'N'),
('default', 'omitField', 'classic', 'N'),
('default', 'omitField', 'best_book', 'N'),
('default', 'omitField', 'condition', 'N'),
('default', 'allowEdit', 'notes', 'N'),
('default', 'allowEdit', 'fate', 'N'),
('default', 'disallowEdit', 'innreach_circ_copies', 'N'),
('default', 'disallowEdit', 'innreach_total_copies', 'N');





