CREATE TABLE `api_keys_s83dks` (
  `KEY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `KEY_Value` varchar(54) CHARACTER SET utf8 NOT NULL,
  `KEY_Status` tinyint(1) NOT NULL DEFAULT 1,
  `KEY_Limit` int(20) NOT NULL DEFAULT 150,
  `KEY_LastRequest` datetime DEFAULT NULL,
  `KEY_Requests` int(20) NOT NULL DEFAULT 0,
  `BEN_ID` int(11) NOT NULL,
  PRIMARY KEY (`KEY_ID`),
  UNIQUE KEY `uniq_key` (`KEY_Value`),
  KEY `idx_user` (`BEN_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `auth_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `auth_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `role_source` int(11) DEFAULT NULL,
  `role_target` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `auth_role_permission` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `auth_role_roles` (
  `role_source` int(255) NOT NULL,
  `role_target` int(255) NOT NULL,
  PRIMARY KEY (`role_source`,`role_target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `auth_user_role` (
  `user_id` int(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ben_benutzer_91c48c` (
  `BEN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `GRU_ID` int(1) NOT NULL DEFAULT 1,
  `BEN_Username` varchar(100) NOT NULL,
  `BEN_Password` varchar(255) NOT NULL,
  `BEN_Email` varchar(255) CHARACTER SET latin1 NOT NULL,
  `BEN_Disabled` tinyint(1) NOT NULL DEFAULT 0,
  `BEN_LastLogin` timestamp NULL DEFAULT NULL,
  `BEN_Register` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`BEN_ID`),
  KEY `idx_email` (`BEN_Email`),
  KEY `idx_username` (`BEN_Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `sqa_squad_member_4de785` (
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
CREATE TABLE `sqa_squads_6d4c2s` (
  `SQA_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BEN_ID` int(11) NOT NULL,
  `SQA_PrivateID` varchar(32) NOT NULL,
  `SQA_Logo` varchar(255) DEFAULT NULL,
  `SQA_Tag` varchar(255) NOT NULL,
  `SQA_Name` varchar(255) NOT NULL,
  `SQA_Title` varchar(255) DEFAULT NULL,
  `SQA_Homepage` varchar(255) DEFAULT NULL,
  `SQA_Email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SQA_ID`),
  UNIQUE KEY `uniq_private_id` (`SQA_PrivateID`),
  KEY `idx_user` (`BEN_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `auth_role` VALUES (1,'Guest',NULL,NULL),(2,'User',1,NULL),(4,'Admin',NULL,NULL);
INSERT INTO `auth_role_roles` VALUES (2,1),(4,2);
