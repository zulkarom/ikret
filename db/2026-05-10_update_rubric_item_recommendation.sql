SET @column_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'rubric_item'
    AND COLUMN_NAME = 'is_recommend'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `rubric_item` ADD COLUMN `is_recommend` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_required`',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `rubric_item` i
INNER JOIN `rubric_category` c ON c.`id` = i.`category_id`
SET i.`is_recommend` = 1
WHERE c.`is_recommend` = 1;
