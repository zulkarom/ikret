ALTER TABLE `program`
ADD COLUMN `public_reg_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `program_abbr`;

UPDATE `program`
SET `public_reg_enabled` = 1
WHERE `public_reg_enabled` IS NULL;
