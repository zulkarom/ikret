-- Upgrade script: Program 7 competition category (competition_cat_program)
-- Generated for ikret

SET @db := DATABASE();

-- 1) Add competition_cat_program column to program_reg (if missing)
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'program_reg'
        AND COLUMN_NAME = 'competition_cat_program'
    ),
    'SELECT ''program_reg.competition_cat_program exists'';',
    'ALTER TABLE `program_reg` ADD COLUMN `competition_cat_program` INT(11) NULL AFTER `participant_cat_program`'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) Create competition_cat_program table if not exists
CREATE TABLE IF NOT EXISTS `competition_cat_program` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Seed/refresh Program 7 categories (delete+insert)
DELETE FROM `competition_cat_program` WHERE `program_id` = 7;

INSERT INTO `competition_cat_program` (`id`, `program_id`, `cat_name`, `sort_order`, `is_active`) VALUES
(1, 7, 'Educator & Learning Transformation Innovation', 1, 1),
(2, 7, 'Business & Digital Economy Innovation', 2, 1),
(3, 7, 'AI, Emerging Technology & Digital Media Innovation', 3, 1),
(4, 7, 'Sustainability & Environmental Innovation', 4, 1);
