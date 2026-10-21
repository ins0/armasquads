-- Indexes for the lookups of the application. Applied on production on 2026-10-21.
-- Rollback: docker/mariadb/migrations/2026-10-21-add-indexes.rollback.sql
ALTER TABLE sqa_squads_6d4c2s ADD UNIQUE INDEX uniq_private_id (SQA_PrivateID), ADD INDEX idx_user (BEN_ID), ALGORITHM=INPLACE, LOCK=NONE;
ALTER TABLE ben_benutzer_91c48c ADD INDEX idx_email (BEN_Email), ADD INDEX idx_username (BEN_Username), ALGORITHM=INPLACE, LOCK=NONE;
ALTER TABLE api_keys_s83dks ADD UNIQUE INDEX uniq_key (KEY_Value), ADD INDEX idx_user (BEN_ID), ALGORITHM=INPLACE, LOCK=NONE;
ALTER TABLE auth_user_role ADD PRIMARY KEY (user_id, role_id);
