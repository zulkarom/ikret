SET @column_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'program_achievement'
    AND COLUMN_NAME = 'rubric_item_id'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `program_achievement` ADD COLUMN `rubric_item_id` INT(11) NULL AFTER `winner_count`',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
