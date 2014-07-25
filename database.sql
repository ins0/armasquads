-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Jul 2014 um 02:37
-- Server Version: 5.6.16
-- PHP-Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `clancms`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `api_keys_s83dks`
--

CREATE TABLE IF NOT EXISTS `api_keys_s83dks` (
  `KEY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `KEY` varchar(32) NOT NULL,
  `KEY_Status` tinyint(1) NOT NULL DEFAULT '1',
  `KEY_LIMIT` int(20) NOT NULL DEFAULT '150',
  `KEY_LastRequest` datetime DEFAULT NULL,
  `KEY_RequestsPerDay` int(20) NOT NULL,
  `BEN_ID` int(11) NOT NULL,
  PRIMARY KEY (`KEY_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_gruppen_a7d451`
--

CREATE TABLE IF NOT EXISTS `auth_gruppen_a7d451` (
  `GRU_ID` int(11) NOT NULL,
  `GRU_Name` varchar(50) NOT NULL,
  `GRU_Parent` varchar(50) DEFAULT NULL,
  `GRU_Supervisor` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`GRU_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_resourcen_d74fw1`
--

CREATE TABLE IF NOT EXISTS `auth_resourcen_d74fw1` (
  `RES_ID` int(11) NOT NULL AUTO_INCREMENT,
  `RES_Modul` varchar(50) NOT NULL,
  `RES_Action` varchar(50) NOT NULL,
  `RES_SubAction` varchar(70) NOT NULL,
  `RES_Description` varchar(255) NOT NULL,
  PRIMARY KEY (`RES_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_zugriff_oj19de`
--

CREATE TABLE IF NOT EXISTS `auth_zugriff_oj19de` (
  `ZUG_ID` int(11) NOT NULL AUTO_INCREMENT,
  `GRU_ID` int(11) NOT NULL,
  `RES_ID` int(11) NOT NULL,
  PRIMARY KEY (`ZUG_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ben_benutzer_91c48c`
--

CREATE TABLE IF NOT EXISTS `ben_benutzer_91c48c` (
  `BEN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `GRU_ID` int(3) NOT NULL,
  `BEN_Username` varchar(100) NOT NULL,
  `BEN_Password` varchar(255) NOT NULL,
  `BEN_Email` varchar(255) NOT NULL,
  `BEN_Disabled` tinyint(1) NOT NULL DEFAULT '0',
  `BEN_LastLogin` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `BEN_Register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BEN_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sqa_squads_6d4c2s`
--

CREATE TABLE IF NOT EXISTS `sqa_squads_6d4c2s` (
  `SQA_ID` int(11) NOT NULL AUTO_INCREMENT,
  `SQA_PrivateID` varchar(32) NOT NULL,
  `BEN_ID` int(11) NOT NULL,
  `SQA_Tag` varchar(255) NOT NULL,
  `SQA_Name` varchar(255) NOT NULL,
  `SQA_Email` varchar(255) DEFAULT NULL,
  `SQA_Logo` varchar(255) DEFAULT NULL,
  `SQA_Homepage` varchar(255) DEFAULT NULL,
  `SQA_Title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SQA_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=280 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sqa_squad_logos_wi10dc`
--

CREATE TABLE IF NOT EXISTS `sqa_squad_logos_wi10dc` (
  `LOGO_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BEN_ID` int(11) NOT NULL DEFAULT '0',
  `LOGO_Name` varchar(255) NOT NULL DEFAULT '0',
  `LOGO_File` varchar(255) NOT NULL DEFAULT '0',
  `LOGO_FilePaa` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LOGO_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sqa_squad_member_4de785`
--

CREATE TABLE IF NOT EXISTS `sqa_squad_member_4de785` (
  `SQA_ID` int(11) NOT NULL,
  `MEM_UID` varchar(255) NOT NULL,
  `MEM_Username` varchar(255) NOT NULL,
  `MEM_Name` varchar(255) NOT NULL,
  `MEM_Email` varchar(255) NOT NULL,
  `MEM_ICQ` varchar(255) NOT NULL,
  `MEM_Remark` varchar(255) NOT NULL,
  PRIMARY KEY (`SQA_ID`,`MEM_UID`),
  UNIQUE KEY `SQA_ID` (`SQA_ID`,`MEM_UID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
