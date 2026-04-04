-- Upgrade script: Add show_matric option for program_reg_field (group_member)
-- Generated for ikret

SET @db := DATABASE();

-- Add show_matric column if missing
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'program_reg_field'
        AND COLUMN_NAME = 'show_matric'
    ),
    'SELECT ''program_reg_field.show_matric exists'';',
    'ALTER TABLE `program_reg_field` ADD COLUMN `show_matric` TINYINT(1) NOT NULL DEFAULT 1 AFTER `is_required`'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure existing rows have a value
UPDATE `program_reg_field` SET `show_matric` = 1 WHERE `show_matric` IS NULL;
