-- Upgrade script: Program Registration Fields Configuration (program_reg_field)
-- Generated for ikret

SET @db := DATABASE();

-- Create config table if not exists
CREATE TABLE IF NOT EXISTS `program_reg_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `field_name` varchar(64) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_program_field` (`program_id`,`field_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: seed program 7 defaults (only if no rows exist for program 7)
SET @has7 := (
  SELECT COUNT(1)
  FROM `program_reg_field`
  WHERE `program_id` = 7
);

SET @sql := IF(
  @has7 > 0,
  'SELECT ''program_reg_field: program 7 already seeded'';',
  'INSERT INTO `program_reg_field` (`program_id`,`field_name`,`is_enabled`,`is_required`,`sort_order`) VALUES
  (7,''participant_mode'',1,1,10),
  (7,''participant_cat_program'',1,0,20),
  (7,''competition_cat_program'',1,0,30),
  (7,''group_member'',1,1,40),
  (7,''institution'',1,1,50)'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
