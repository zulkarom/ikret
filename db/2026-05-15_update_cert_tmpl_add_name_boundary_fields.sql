ALTER TABLE `cert_tmpl`
  ADD COLUMN `name_limit_y` double DEFAULT NULL AFTER `name_mt`,
  ADD COLUMN `show_name_border` tinyint(1) NOT NULL DEFAULT 0 AFTER `name_limit_y`;
