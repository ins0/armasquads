/*
MySQL Backup
Source Server Version: 5.5.46
Source Database: armasquads
Date: 26.12.2015 14:35:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `api_keys_s83dks`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `api_keys_s83dks` (
  `KEY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `KEY_Value` varchar(54) CHARACTER SET utf8 NOT NULL,
  `KEY_Status` tinyint(1) NOT NULL DEFAULT '1',
  `KEY_Limit` int(20) NOT NULL DEFAULT '150',
  `KEY_LastRequest` datetime DEFAULT NULL,
  `KEY_Requests` int(20) NOT NULL DEFAULT '0',
  `BEN_ID` int(11) NOT NULL,
  PRIMARY KEY (`KEY_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1469 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `auth_permission`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `auth_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `auth_role`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `auth_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `role_source` int(11) DEFAULT NULL,
  `role_target` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `auth_role_permission`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `auth_role_permission` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `auth_role_roles`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `auth_role_roles` (
  `role_source` int(255) NOT NULL,
  `role_target` int(255) NOT NULL,
  PRIMARY KEY (`role_source`,`role_target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `auth_user_role`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `auth_user_role` (
  `user_id` int(255) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ben_benutzer_91c48c`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `ben_benutzer_91c48c` (
  `BEN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BEN_Username` varchar(100) NOT NULL,
  `BEN_Password` varchar(255) NOT NULL,
  `BEN_Email` varchar(255) CHARACTER SET latin1 NOT NULL,
  `BEN_Disabled` tinyint(1) NOT NULL DEFAULT '0',
  `BEN_LastLogin` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `BEN_Register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BEN_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=23537 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `sqa_squad_member_4de785`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sqa_squad_member_4de785` (
  `SQA_ID` int(11) NOT NULL,
  `MEM_UID` varchar(255) NOT NULL,
  `MEM_Username` varchar(255) DEFAULT NULL,
  `MEM_Name` varchar(255) DEFAULT NULL,
  `MEM_Email` varchar(255) DEFAULT NULL,
  `MEM_ICQ` varchar(255) DEFAULT NULL,
  `MEM_Remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SQA_ID`,`MEM_UID`),
  UNIQUE KEY `SQA_ID` (`SQA_ID`,`MEM_UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `sqa_squads_6d4c2s`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sqa_squads_6d4c2s` (
  `SQA_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BEN_ID` int(11) NOT NULL,
  `SQA_PrivateID` varchar(32) NOT NULL,
  `SQA_Logo` varchar(255) DEFAULT NULL,
  `SQA_Tag` varchar(255) NOT NULL,
  `SQA_Name` varchar(255) NOT NULL,
  `SQA_Title` varchar(255) DEFAULT NULL,
  `SQA_Homepage` varchar(255) DEFAULT NULL,
  `SQA_Email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SQA_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=33211 DEFAULT CHARSET=utf8;

INSERT INTO `auth_role` VALUES ('1','Guest',NULL,NULL), ('2','User','1',NULL), ('4','Admin',NULL,NULL);
INSERT INTO `auth_role_roles` VALUES ('2','1'), ('4','2');
