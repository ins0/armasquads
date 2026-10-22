-- Accounts that may edit a squad besides its owner (ins0/armasquads#155).
-- Rollback: docker/mariadb/migrations/2026-10-22-add-squad-editors.rollback.sql
CREATE TABLE `sqa_squad_editor` (
  `SQA_ID` int(11) NOT NULL,
  `BEN_ID` int(11) NOT NULL,
  PRIMARY KEY (`SQA_ID`,`BEN_ID`),
  KEY `idx_user` (`BEN_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
