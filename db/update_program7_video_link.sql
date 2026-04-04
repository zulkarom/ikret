-- Upgrade script: Program 7 video link field (video_link) replacing video_file upload
-- Generated for ikret

SET @db := DATABASE();

-- Add video_link column to program_reg if missing
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'program_reg' AND COLUMN_NAME = 'video_link'
    ),
    'SELECT ''program_reg.video_link exists'';',
    'ALTER TABLE `program_reg` ADD COLUMN `video_link` varchar(500) DEFAULT NULL'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Optional migration: if you previously used video_file, copy it into video_link when video_link is empty
SET @has_video_file := (
  SELECT COUNT(1)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'program_reg' AND COLUMN_NAME = 'video_file'
);

SET @sql := IF(
  @has_video_file > 0,
  'UPDATE `program_reg` SET `video_link` = `video_file` WHERE (`video_link` IS NULL OR `video_link` = "") AND `video_file` IS NOT NULL AND `video_file` <> "";',
  'SELECT ''program_reg.video_file not found, skip migration'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Enable video_link in program_reg_field for program 7
INSERT INTO `program_reg_field` (`program_id`, `field_name`, `is_enabled`, `is_required`, `sort_order`)
VALUES
(7, 'video_link', 1, 0, 80)
ON DUPLICATE KEY UPDATE
`is_enabled`=VALUES(`is_enabled`),
`is_required`=VALUES(`is_required`),
`sort_order`=VALUES(`sort_order`);

-- Disable old video_file field if it exists in config
UPDATE `program_reg_field`
SET `is_enabled` = 0, `is_required` = 0
WHERE `program_id` = 7 AND `field_name` = 'video_file';
