-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 20. Mai 2014 um 12:33
-- Server Version: 5.6.16
-- PHP-Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `clancms`
--

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

--
-- Daten für Tabelle `auth_gruppen_a7d451`
--

INSERT INTO `auth_gruppen_a7d451` (`GRU_ID`, `GRU_Name`, `GRU_Parent`, `GRU_Supervisor`) VALUES
(1, 'Administrator', NULL, 1),
(2, 'Gast', NULL, NULL),
(3, 'User', '2', NULL),
(4, 'Member', '3', NULL);

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

--
-- Daten für Tabelle `auth_resourcen_d74fw1`
--

INSERT INTO `auth_resourcen_d74fw1` (`RES_ID`, `RES_Modul`, `RES_Action`, `RES_SubAction`, `RES_Description`) VALUES
(1, 'admin', 'dashboard', 'access', 'Sieht auf dem Dashboard erweiterte Informationen'),
(2, 'admin', 'benutzer', 'access', 'Darf auf das Benutzermodul zugreifen'),
(4, 'admin', 'benutzer', 'edit', 'Darf Benutzer bearbeiten'),
(5, 'admin', 'benutzer', 'create', 'Darf Benutzer erstellen'),
(6, 'admin', 'benutzer', 'delete', 'Darf Benutzer löschen'),
(7, 'admin', 'access', '', 'Zugang zum Administration Portal'),
(8, 'admin', 'benutzer/groups', 'access', 'Darf auf Benutzergruppen zugreifen'),
(9, 'admin', 'benutzer/groups', 'edit', 'Darf Benutzergruppen bearbeiten'),
(10, 'admin', 'products', 'access', 'Darf auf Produkte zugreifen'),
(11, 'admin', 'products', 'create', 'Darf auf Produkte erstellen'),
(12, 'admin', 'products', 'edit', 'Darf auf Produkte bearbeiten'),
(13, 'admin', 'products/attributes', 'access', 'Darf auf Produkt Attribute zugreifen'),
(14, 'admin', 'products/attributes', 'create', 'Darf auf Produkt Attribute erstellen'),
(15, 'frontend', 'dashboard', 'access', 'Kann auf das Userpanel zugreifen');

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

--
-- Daten für Tabelle `auth_zugriff_oj19de`
--

INSERT INTO `auth_zugriff_oj19de` (`ZUG_ID`, `GRU_ID`, `RES_ID`) VALUES
(4, 3, 15);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ben_benutzer_91c48c`
--

CREATE TABLE IF NOT EXISTS `ben_benutzer_91c48c` (
  `BEN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `GRU_ID` int(3) NOT NULL,
  `BEN_Username` varchar(100) CHARACTER SET utf8 NOT NULL,
  `BEN_Password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `BEN_Disabled` tinyint(1) NOT NULL DEFAULT '0',
  `BEN_LastLogin` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `BEN_Register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BEN_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `ben_benutzer_91c48c`
--

INSERT INTO `ben_benutzer_91c48c` (`BEN_ID`, `GRU_ID`, `BEN_Username`, `BEN_Password`, `BEN_Disabled`, `BEN_LastLogin`, `BEN_Register`) VALUES
(1, 1, 'ins0', '098f6bcd4621d373cade4e832627b4f6', 0, '2014-05-20 09:35:34', '2013-04-10 13:30:16'),
(2, 4, 'test2', '098f6bcd4621d373cade4e832627b4f6', 0, '2014-05-20 08:34:17', '2013-10-30 22:11:28');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sqa_squads_6d4c2s`
--

CREATE TABLE IF NOT EXISTS `sqa_squads_6d4c2s` (
  `SQA_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BEN_ID` int(11) NOT NULL,
  `SQA_Tag` varchar(255) NOT NULL,
  `SQA_Name` varchar(255) NOT NULL,
  `SQA_Email` varchar(255) NOT NULL,
  `SQA_Logo` varchar(255) NOT NULL,
  `SQA_Title` varchar(255) NOT NULL,
  PRIMARY KEY (`SQA_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `sqa_squads_6d4c2s`
--

INSERT INTO `sqa_squads_6d4c2s` (`SQA_ID`, `BEN_ID`, `SQA_Tag`, `SQA_Name`, `SQA_Email`, `SQA_Logo`, `SQA_Title`) VALUES
(1, 1, 'CEC', 'Chaos Elite Crew', 'test@test.de', '', 'test tile'),
(2, 1, 'Test Squad', 'Test', 'asd@asd.de', 'logo', 'Hallo!');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sqa_squad_member_4de785`
--

CREATE TABLE IF NOT EXISTS `sqa_squad_member_4de785` (
  `MEM_ID` int(11) NOT NULL AUTO_INCREMENT,
  `SQA_ID` int(11) NOT NULL,
  `MEM_UID` int(11) NOT NULL,
  `MEM_Username` varchar(255) NOT NULL,
  `MEM_Name` varchar(255) NOT NULL,
  `MEM_Email` varchar(255) NOT NULL,
  `MEM_ICQ` varchar(255) NOT NULL,
  `MEM_Remark` varchar(255) NOT NULL,
  PRIMARY KEY (`MEM_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `sqa_squad_member_4de785`
--

INSERT INTO `sqa_squad_member_4de785` (`MEM_ID`, `SQA_ID`, `MEM_UID`, `MEM_Username`, `MEM_Name`, `MEM_Email`, `MEM_ICQ`, `MEM_Remark`) VALUES
(1, 1, 123456789, 'ins0', 'Marco Rieger', 'marco@test.de', '456789', 'Test Remakr!'),
(2, 1, 96385271, 'Gammler', 'Tobias Krämer', 'tobi@test.de', '12345678', 'test tobia remakr');
