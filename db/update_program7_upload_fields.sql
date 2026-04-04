-- Upgrade script: Program 7 upload fields (abstract_file) and enable configurable fields
-- Generated for ikret

SET @db := DATABASE();

-- Add columns to program_reg if missing
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'program_reg' AND COLUMN_NAME = 'abstract_file'
    ),
    'SELECT ''program_reg.abstract_file exists'';',
    'ALTER TABLE `program_reg` ADD COLUMN `abstract_file` text AFTER `poster_file`'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Enable in program_reg_field for program 7 (safe even if already exists)
INSERT INTO `program_reg_field` (`program_id`, `field_name`, `is_enabled`, `is_required`, `sort_order`)
VALUES
(7, 'project_name', 1, 1, 5),
(7, 'abstract_file', 1, 1, 60),
(7, 'poster_file', 1, 0, 70),
(7, 'video_link', 1, 0, 80),
(7, 'payment_file', 1, 1, 90)
ON DUPLICATE KEY UPDATE
`is_enabled`=VALUES(`is_enabled`),
`is_required`=VALUES(`is_required`),
`sort_order`=VALUES(`sort_order`);
