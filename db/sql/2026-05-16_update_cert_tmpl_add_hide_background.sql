ALTER TABLE `cert_tmpl`
ADD COLUMN `hide_background` TINYINT(1) NOT NULL DEFAULT 0 AFTER `show_name_border`;
