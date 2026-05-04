-- Add rubric description
ALTER TABLE `rubric`
  ADD COLUMN `rubric_description` LONGTEXT NULL AFTER `rubric_name`;

-- Ensure rubric table supports 4-byte UTF-8 (emoji)
ALTER TABLE `rubric` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create rubric judging sessions table
CREATE TABLE `rubric_judging_session` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rubric_id` INT(11) NOT NULL,
  `session_name` VARCHAR(255) NOT NULL,
  `datetime_start` DATETIME NULL,
  `datetime_end` DATETIME NULL,
  `location` VARCHAR(255) NULL,
  `mode` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=physical,2=online',
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  `created_at` INT(11) NULL,
  `updated_at` INT(11) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rjs_rubric_id` (`rubric_id`),
  CONSTRAINT `fk_rjs_rubric_id` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add per-item required flag
ALTER TABLE `rubric_item`
  ADD COLUMN `is_required` TINYINT(1) NOT NULL DEFAULT 0 AFTER `colum_ans`;

-- Backfill required defaults to preserve current behavior:
-- - likert/yesno were previously always required
-- - shorttext/longtext were previously optional
UPDATE `rubric_item`
  SET `is_required` = CASE WHEN `item_type` IN (1, 2) THEN 1 ELSE 0 END;
