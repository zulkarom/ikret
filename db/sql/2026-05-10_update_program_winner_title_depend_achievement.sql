


ALTER TABLE `program_winner_title`
  DROP COLUMN `program_id`,
  DROP COLUMN `program_sub`,
  ADD COLUMN `achievement_id` int(11) NOT NULL AFTER `id`,
  ADD COLUMN `winner_order` int(11) NOT NULL AFTER `achievement_id`,
  ADD UNIQUE KEY `achievement_winner_order` (`achievement_id`,`winner_order`),
  ADD KEY `achievement_id` (`achievement_id`);

ALTER TABLE `program_winner_title`
  ADD CONSTRAINT `program_winner_title_achievement_fk`
  FOREIGN KEY (`achievement_id`) REFERENCES `program_achievement` (`id`) ON DELETE CASCADE;
