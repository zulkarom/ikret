-- Production update script for app_setting table

CREATE TABLE IF NOT EXISTS `app_setting` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL,
  `value` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_app_setting_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default enable Call for Juries
INSERT IGNORE INTO `app_setting` (`key`, `value`) VALUES ('call_for_juries_enabled', '1');
