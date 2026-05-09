ALTER TABLE `program_reg_achieve`
  ADD COLUMN `winner_title_id` int(11) DEFAULT NULL AFTER `achieve_id`,
  ADD KEY `winner_title_id` (`winner_title_id`);

ALTER TABLE `program_reg_achieve`
  ADD CONSTRAINT `program_reg_achieve_winner_title_fk`
  FOREIGN KEY (`winner_title_id`) REFERENCES `program_winner_title` (`id`) ON DELETE SET NULL;
