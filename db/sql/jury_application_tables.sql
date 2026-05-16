-- Jury application tables (MySQL)
-- Includes: jury_profiles, jury_applications, jury_requirements

SET FOREIGN_KEY_CHECKS=0;

-- 1) Jury profiles (1 row per user)
CREATE TABLE IF NOT EXISTS `jury_profiles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `fullname` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(255) NULL,
  `institution` VARCHAR(255) NULL,
  `address` TEXT NULL,
  `designation` VARCHAR(255) NULL,
  `created_at` INT NULL,
  `updated_at` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_jury_profiles_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `jury_profiles`
  ADD CONSTRAINT `fk_jury_profiles_user_id`
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;


-- 2) Jury applications (can be multiple per jury)
CREATE TABLE IF NOT EXISTS `jury_applications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `jury_profile_id` INT NOT NULL,
  `program_id` INT NOT NULL,
  `program_sub_id` INT NULL,
  `judging_session_id` INT NULL,
  `declaration_accepted` TINYINT(1) NOT NULL DEFAULT 0,
  `status` INT NOT NULL DEFAULT 0,
  `created_at` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_jury_applications_unique` (`jury_profile_id`, `program_id`, `program_sub_id`, `judging_session_id`),
  KEY `idx_jury_applications_program_id` (`program_id`),
  KEY `idx_jury_applications_program_sub_id` (`program_sub_id`),
  KEY `idx_jury_applications_judging_session_id` (`judging_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `jury_applications`
  ADD CONSTRAINT `fk_jury_applications_jury_profile_id`
  FOREIGN KEY (`jury_profile_id`) REFERENCES `jury_profiles` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `jury_applications`
  ADD CONSTRAINT `fk_jury_applications_program_id`
  FOREIGN KEY (`program_id`) REFERENCES `program` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `jury_applications`
  ADD CONSTRAINT `fk_jury_applications_program_sub_id`
  FOREIGN KEY (`program_sub_id`) REFERENCES `program_sub` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `jury_applications`
  ADD CONSTRAINT `fk_jury_applications_judging_session_id`
  FOREIGN KEY (`judging_session_id`) REFERENCES `rubric_judging_session` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;


-- 3) Admin-configured jury requirement + limit per program/sub
-- Controls which program/sub is open for jury applications and how many juries are needed/allowed.
CREATE TABLE IF NOT EXISTS `jury_requirements` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `program_id` INT NOT NULL,
  `program_sub_id` INT NULL,
  `judging_session_id` INT NULL,
  `is_required` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `jury_limit` INT NULL,
  `created_at` INT NULL,
  `updated_at` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_jury_requirements_program_sub_session` (`program_id`, `program_sub_id`, `judging_session_id`),
  KEY `idx_jury_requirements_program_id` (`program_id`),
  KEY `idx_jury_requirements_program_sub_id` (`program_sub_id`),
  KEY `idx_jury_requirements_judging_session_id` (`judging_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `jury_requirements`
  ADD CONSTRAINT `fk_jury_requirements_program_id`
  FOREIGN KEY (`program_id`) REFERENCES `program` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `jury_requirements`
  ADD CONSTRAINT `fk_jury_requirements_program_sub_id`
  FOREIGN KEY (`program_sub_id`) REFERENCES `program_sub` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `jury_requirements`
  ADD CONSTRAINT `fk_jury_requirements_judging_session_id`
  FOREIGN KEY (`judging_session_id`) REFERENCES `rubric_judging_session` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS=1;


-- =============================
-- UPDATE SCRIPT (for existing DBs)
-- Use this section if you already created the old jury_requirements table in prod
-- and need to add judging_session_id + update unique key.
-- =============================

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
