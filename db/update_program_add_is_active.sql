-- Add is_active flag to program table
-- 1 = active, 0 = inactive

ALTER TABLE `program`
  ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1;

-- Backfill existing rows (safety)
UPDATE `program`
SET `is_active` = 1
WHERE `is_active` IS NULL;
