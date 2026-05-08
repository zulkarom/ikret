ALTER TABLE `session`
ADD COLUMN `allow_scan_outside_duration` TINYINT(1) NOT NULL DEFAULT 0 AFTER `datetime_end`,
ADD COLUMN `allow_scan_1_hour_after_event` TINYINT(1) NOT NULL DEFAULT 0 AFTER `allow_scan_outside_duration`;
