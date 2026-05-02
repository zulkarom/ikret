-- Upgrade script: Program 7 participant category (participant_cat_program)
-- Generated for ikret

SET @db := DATABASE();

-- 1) Add participant_cat_program column to program_reg (if missing)
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'program_reg'
        AND COLUMN_NAME = 'participant_cat_program'
    ),
    'SELECT ''program_reg.participant_cat_program exists'';',
    'ALTER TABLE `program_reg` ADD COLUMN `participant_cat_program` INT(11) NULL AFTER `participant_mode`'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) Create participant_cat_program table if not exists
CREATE TABLE IF NOT EXISTS `participant_cat_program` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL,
  `mode` tinyint(1) NOT NULL COMMENT '1=physical 2=online',
  `fee` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Add mode + fee columns if table already existed but missing them
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'participant_cat_program'
        AND COLUMN_NAME = 'mode'
    ),
    'SELECT ''participant_cat_program.mode exists'';',
    'ALTER TABLE `participant_cat_program` ADD COLUMN `mode` TINYINT(1) NOT NULL COMMENT ''1=physical 2=online'' AFTER `cat_name`'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'participant_cat_program'
        AND COLUMN_NAME = 'fee'
    ),
    'SELECT ''participant_cat_program.fee exists'';',
    'ALTER TABLE `participant_cat_program` ADD COLUMN `fee` VARCHAR(255) NULL AFTER `mode`'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4) Seed/refresh Program 7 categories (delete+insert)
DELETE FROM `participant_cat_program` WHERE `program_id` = 7;

INSERT INTO `participant_cat_program` (`id`, `program_id`, `cat_name`, `mode`, `fee`, `sort_order`, `is_active`) VALUES
(1, 7, 'Primary School', 1, 'RM50/ Group', 1, 1),
(2, 7, 'Secondary School', 1, 'RM60/ Group', 2, 1),
(3, 7, 'University (UMK)', 1, 'RM70/ Group', 3, 1),
(4, 7, 'University (External)', 2, 'RM60/ Group', 4, 1),
(5, 7, 'Professional', 2, 'RM150/ Group', 5, 1),
(6, 7, 'Industry', 2, 'RM200/ Group', 6, 1),
(7, 7, 'International', 2, 'USD50/ Group', 7, 1);
