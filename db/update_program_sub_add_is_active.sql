-- Add is_active flag to program_sub table
-- 1 = active, 0 = inactive

SET @db := DATABASE();

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'program_sub'
        AND COLUMN_NAME = 'is_active'
    ),
    'SELECT ''program_sub.is_active exists'';',
    'ALTER TABLE `program_sub` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1'
  )
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `program_sub`
SET `is_active` = 1
WHERE `is_active` IS NULL;
