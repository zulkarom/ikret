ALTER TABLE `program_winner_title`
  ADD COLUMN `no_title_text` tinyint(1) NOT NULL DEFAULT 0 AFTER `title_name`;
