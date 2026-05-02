ALTER TABLE `program_reg`
ADD COLUMN `edit_password_hash` VARCHAR(255) NULL AFTER `contact_email`;

ALTER TABLE `program_reg`
MODIFY `contact_email` VARCHAR(255) NOT NULL;

UPDATE `program_reg`
SET `contact_email` = LOWER(TRIM(`contact_email`))
WHERE `contact_email` IS NOT NULL;

ALTER TABLE `program_reg`
ADD UNIQUE KEY `uq_program_reg_program_email` (`program_id`, `contact_email`);
