ALTER TABLE `cert_tmpl`
  ADD COLUMN `show_name_border` tinyint(1) NOT NULL DEFAULT 0 AFTER `name_limit_y`;
