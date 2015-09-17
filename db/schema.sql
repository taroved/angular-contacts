CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `name` varchar(60) NOT NULL,
      `password` varchar(40) NOT NULL,
      `salt` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users`
  ADD UNIQUE KEY `name_index` (`name`);


CREATE TABLE `contacts` (
      `int` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `user_id` int(11) NOT NULL,
      `name` varchar(60) NOT NULL,
      `email` varchar(60) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
