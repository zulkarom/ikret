ALTER TABLE `setting`
  ADD COLUMN `banner_image` varchar(255) DEFAULT NULL,
  ADD COLUMN `show_icreate_list_event` tinyint(1) DEFAULT 1,
  ADD COLUMN `programme_book_url` varchar(255) DEFAULT NULL,
  ADD COLUMN `programme_book_qr` varchar(255) DEFAULT NULL,
  ADD COLUMN `program_description` text DEFAULT NULL;

UPDATE `setting`
SET `show_icreate_list_event` = 1
WHERE `id` = 1 AND (`show_icreate_list_event` IS NULL);
