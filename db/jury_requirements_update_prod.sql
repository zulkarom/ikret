-- Production update script for jury_requirements
-- Adds judging_session_id and updates constraints/indexes.
-- Safe to run multiple times (idempotent using information_schema checks).

SET FOREIGN_KEY_CHECKS=0;

-- 1) Add column judging_session_id if missing
SET @col_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'jury_requirements'
    AND COLUMN_NAME = 'judging_session_id'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `jury_requirements` ADD COLUMN `judging_session_id` INT NULL AFTER `program_sub_id`',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) Drop old unique key (program_id, program_sub_id) if it exists
SET @ux_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'jury_requirements'
    AND INDEX_NAME = 'ux_jury_requirements_program_sub'
);
SET @sql := IF(@ux_exists > 0,
  'ALTER TABLE `jury_requirements` DROP INDEX `ux_jury_requirements_program_sub`',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) Add new unique key (program_id, program_sub_id, judging_session_id) if missing
SET @ux3_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'jury_requirements'
    AND INDEX_NAME = 'ux_jury_requirements_program_sub_session'
);
SET @sql := IF(@ux3_exists = 0,
  'ALTER TABLE `jury_requirements` ADD UNIQUE KEY `ux_jury_requirements_program_sub_session` (`program_id`, `program_sub_id`, `judging_session_id`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4) Add index on judging_session_id if missing
SET @idx_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'jury_requirements'
    AND INDEX_NAME = 'idx_jury_requirements_judging_session_id'
);
SET @sql := IF(@idx_exists = 0,
  'ALTER TABLE `jury_requirements` ADD KEY `idx_jury_requirements_judging_session_id` (`judging_session_id`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5) Add FK on judging_session_id if missing
SET @fk_exists := (
  SELECT COUNT(*)
  FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'jury_requirements'
    AND CONSTRAINT_NAME = 'fk_jury_requirements_judging_session_id'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE `jury_requirements` ADD CONSTRAINT `fk_jury_requirements_judging_session_id` FOREIGN KEY (`judging_session_id`) REFERENCES `rubric_judging_session` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS=1;
