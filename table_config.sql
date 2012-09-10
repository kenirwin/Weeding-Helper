-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 31, 2012 at 04:22 PM
-- Server version: 5.1.47
-- PHP Version: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `weeding`
--

-- --------------------------------------------------------

--
-- Table structure for table `table_config`
--

CREATE TABLE IF NOT EXISTS `table_config` (
  `table_name` varchar(25) NOT NULL,
  `option` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_config`
--

INSERT INTO `table_config` (`table_name`, `option`, `value`) VALUES
('default', 'omitField', 'publisher'),
('default', 'omitField', 'loc'),
('default', 'omitField', 'bcode'),
('default', 'omitField', 'mat_type'),
('default', 'omitField', 'bib_record'),
('default', 'omitField', 'subclass'),
('default', 'omitField', 'subj_starts'),
('default', 'omitField', 'classic'),
('default', 'omitField', 'condition'),
('default', 'disallowEdit', 'author'),
('default', 'disallowEdit', 'title'),
('default', 'disallowEdit', 'year'),
('default', 'disallowEdit', 'lcsh'),
('default', 'disallowEdit', 'catdate'),
('default', 'disallowEdit', 'call'),
('default', 'disallowEdit', 'circs'),
('default', 'disallowEdit', 'renews'),
('default', 'disallowEdit', 'int_use'),
('default', 'disallowEdit', 'last_checkin'),
('default', 'disallowEdit', 'items'),
('default', 'disallowEdit', 'circ_items'),
('default', 'disallowEdit', 'subclass'),
('default', 'disallowEdit', 'subj_starts');
