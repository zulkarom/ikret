-- Upgrade script: Program 7 contact fields (contact person / contact number / email)
-- Generated for ikret

SET @db := DATABASE();

-- Add columns to program_reg if missing
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'program_reg' AND COLUMN_NAME = 'contact_person'
    ),
    'SELECT ''program_reg.contact_person exists'';',
    'ALTER TABLE `program_reg` ADD COLUMN `contact_person` varchar(255) DEFAULT NULL AFTER `institution`'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'program_reg' AND COLUMN_NAME = 'contact_no'
    ),
    'SELECT ''program_reg.contact_no exists'';',
    'ALTER TABLE `program_reg` ADD COLUMN `contact_no` varchar(255) DEFAULT NULL AFTER `contact_person`'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'program_reg' AND COLUMN_NAME = 'contact_email'
    ),
    'SELECT ''program_reg.contact_email exists'';',
    'ALTER TABLE `program_reg` ADD COLUMN `contact_email` varchar(255) DEFAULT NULL AFTER `contact_no`'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Enable in program_reg_field for program 7 (safe even if already exists)
INSERT INTO `program_reg_field` (`program_id`, `field_name`, `is_enabled`, `is_required`, `sort_order`)
VALUES
(7, 'contact_person', 1, 1, 55),
(7, 'contact_no', 1, 1, 56),
(7, 'contact_email', 1, 1, 57)
ON DUPLICATE KEY UPDATE
`is_enabled`=VALUES(`is_enabled`),
`is_required`=VALUES(`is_required`),
`sort_order`=VALUES(`sort_order`);
