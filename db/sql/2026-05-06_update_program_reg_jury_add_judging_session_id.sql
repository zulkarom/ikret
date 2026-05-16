-- Add judging session reference to jury assignment table
ALTER TABLE `program_reg_jury`
  ADD COLUMN `judging_session_id` INT(11) NULL AFTER `rubric_id`;

ALTER TABLE `program_reg_jury`
  ADD KEY `idx_prj_judging_session_id` (`judging_session_id`);

ALTER TABLE `program_reg_jury`
  ADD CONSTRAINT `fk_prj_judging_session_id` FOREIGN KEY (`judging_session_id`)
    REFERENCES `rubric_judging_session` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;
