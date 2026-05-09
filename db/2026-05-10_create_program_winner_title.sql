CREATE TABLE `program_winner_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `achievement_id` int(11) NOT NULL,
  `winner_order` int(11) NOT NULL,
  `title_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `achievement_winner_order` (`achievement_id`,`winner_order`),
  KEY `achievement_id` (`achievement_id`),
  CONSTRAINT `program_winner_title_ibfk_1` FOREIGN KEY (`achievement_id`) REFERENCES `program_achievement` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
