ALTER TABLE `session`
ADD COLUMN `has_session_certificate` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_scan_1_hour_after_event`;
