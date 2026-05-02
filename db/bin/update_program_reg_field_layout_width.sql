-- Upgrade script: Add layout_width option for program_reg_field
-- Generated for ikret

SET @db := DATABASE();

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'program_reg_field'
        AND COLUMN_NAME = 'layout_width'
    ),
    'SELECT ''program_reg_field.layout_width exists'';',
    'ALTER TABLE `program_reg_field` ADD COLUMN `layout_width` TINYINT(2) NOT NULL DEFAULT 12 AFTER `is_required`'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `program_reg_field`
SET `layout_width` = 12
WHERE `layout_width` IS NULL OR `layout_width` NOT IN (6, 12);
