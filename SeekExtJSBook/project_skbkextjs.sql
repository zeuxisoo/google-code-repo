-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 建立日期: Aug 21, 2008, 08:56 AM
-- 伺服器版本: 5.0.51
-- PHP 版本: 5.2.6

--
-- 資料庫: `project_skbkextjs`
--

-- --------------------------------------------------------

--
-- 資料表格式： `skbkej_comment`
--

DROP TABLE IF EXISTS `skbkej_comment`;
CREATE TABLE IF NOT EXISTS `skbkej_comment` (
  `cid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL,
  `email` varchar(80) NOT NULL,
  `comment` text NOT NULL,
  `adddate` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM  AUTO_INCREMENT=2 ;

--
-- 列出以下資料庫的數據： `skbkej_comment`
--

INSERT INTO `skbkej_comment` (`cid`, `username`, `title`, `sex`, `email`, `comment`, `adddate`) VALUES
(1, '呆心', '系統測試', 2, 'seekstudio@gmail.com', '這是留言內容\r\n這是留言內容\r\n這是留言內容\r\n這是留言內容', 1216718711);

-- --------------------------------------------------------

--
-- 資料表格式： `skbkej_reply`
--

DROP TABLE IF EXISTS `skbkej_reply`;
CREATE TABLE IF NOT EXISTS `skbkej_reply` (
  `rid` mediumint(8) unsigned NOT NULL auto_increment,
  `cid` mediumint(8) unsigned NOT NULL,
  `username` varchar(20) NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL,
  `email` varchar(80) NOT NULL,
  `comment` text NOT NULL,
  `adddate` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`rid`),
  KEY `cid` (`cid`)
) TYPE=MyISAM  AUTO_INCREMENT=2 ;

--
-- 列出以下資料庫的數據： `skbkej_reply`
--

INSERT INTO `skbkej_reply` (`rid`, `cid`, `username`, `sex`, `email`, `comment`, `adddate`) VALUES
(1, 1, '呆心', 1, 'seekstudio@gmail.com', '這是一個回覆測試', 1216719098);
