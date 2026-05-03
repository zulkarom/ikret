-- Seed legacy registration field defaults into program_reg_field
-- Goal: let DB control field visibility instead of PHP hardcoded fallbacks

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

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db
        AND TABLE_NAME = 'program_reg_field'
        AND COLUMN_NAME = 'show_matric'
    ),
    'SELECT ''program_reg_field.show_matric exists'';',
    'ALTER TABLE `program_reg_field` ADD COLUMN `show_matric` TINYINT(1) NOT NULL DEFAULT 1 AFTER `layout_width`'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `program_reg_field`
SET `layout_width` = 12
WHERE `layout_width` IS NULL OR `layout_width` NOT IN (6, 12);

UPDATE `program_reg_field`
SET `show_matric` = 1
WHERE `show_matric` IS NULL;

INSERT INTO `program_reg_field` (`program_id`, `field_name`, `is_enabled`, `is_required`, `layout_width`, `show_matric`, `sort_order`) VALUES
(1, 'project_name', 1, 1, 12, 1, 10),
(1, 'project_desc', 1, 1, 12, 1, 20),
(1, 'participant_cat_local', 1, 1, 12, 1, 30),
(1, 'competition_type', 1, 1, 12, 1, 40),
(1, 'group_member', 1, 1, 12, 1, 50),
(1, 'mentor_main', 1, 0, 12, 1, 60),
(1, 'mentor_co', 1, 0, 12, 1, 70),

(2, 'project_name', 1, 1, 12, 1, 10),
(2, 'participant_cat_group', 1, 1, 12, 1, 20),
(2, 'program_sub', 1, 1, 12, 1, 30),
(2, 'group_member', 1, 1, 12, 1, 40),
(2, 'mentor_main', 1, 0, 12, 1, 50),
(2, 'mentor_co', 1, 0, 12, 1, 60),
(2, 'group_code', 1, 1, 12, 1, 70),
(2, 'group_name', 1, 0, 12, 1, 80),

(3, 'advisor_dropdown', 1, 0, 12, 1, 10),
(3, 'booth_number', 1, 1, 12, 1, 20),
(3, 'group_member', 1, 1, 12, 1, 30),
(3, 'mentor_main', 1, 0, 12, 1, 40),
(3, 'mentor_co', 1, 0, 12, 1, 50),

(4, 'participant_mode', 1, 1, 12, 1, 10),
(4, 'participant_cat_umk', 1, 1, 12, 1, 20),
(4, 'participant_program', 1, 1, 12, 1, 30),
(4, 'institution', 1, 1, 12, 1, 40),
(4, 'mentor_main', 1, 0, 12, 1, 50),
(4, 'mentor_co', 1, 0, 12, 1, 60),

(5, 'project_name', 1, 1, 12, 1, 10),
(5, 'program_sub', 1, 1, 12, 1, 20),
(5, 'participant_cat_group', 1, 1, 12, 1, 30),
(5, 'group_member', 1, 1, 12, 1, 40),
(5, 'mentor_main', 1, 0, 12, 1, 50),
(5, 'mentor_co', 1, 0, 12, 1, 60),
(5, 'group_code', 1, 1, 12, 1, 70),
(5, 'group_name', 1, 0, 12, 1, 80),

(6, 'project_name', 1, 1, 12, 1, 10),
(6, 'group_member', 1, 1, 12, 1, 20),
(6, 'mentor_main', 1, 0, 12, 1, 30),
(6, 'mentor_co', 1, 0, 12, 1, 40),
(6, 'group_code', 1, 1, 12, 1, 50),
(6, 'group_name', 1, 0, 12, 1, 60),

(7, 'participant_mode', 1, 1, 12, 1, 10),
(7, 'participant_cat_program', 1, 0, 12, 1, 20),
(7, 'competition_cat_program', 1, 0, 12, 1, 30),
(7, 'group_member', 1, 1, 12, 1, 40),
(7, 'institution', 1, 1, 12, 1, 50),
(7, 'contact_person', 1, 1, 12, 1, 60),
(7, 'contact_no', 1, 1, 12, 1, 70),
(7, 'contact_email', 1, 1, 12, 1, 80),
(7, 'project_name', 1, 1, 12, 1, 90),
(7, 'abstract_file', 1, 1, 12, 1, 100),
(7, 'poster_file', 1, 1, 12, 1, 110),
(7, 'video_link', 1, 0, 12, 1, 120),
(7, 'payment_file', 1, 1, 12, 1, 130)
ON DUPLICATE KEY UPDATE
`is_enabled` = VALUES(`is_enabled`),
`is_required` = VALUES(`is_required`),
`layout_width` = VALUES(`layout_width`),
`show_matric` = VALUES(`show_matric`),
`sort_order` = VALUES(`sort_order`);
