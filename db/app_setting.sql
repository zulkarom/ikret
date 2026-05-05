-- Minimal key/value table for global app toggles

CREATE TABLE IF NOT EXISTS `app_setting` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL,
  `value` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_app_setting_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
