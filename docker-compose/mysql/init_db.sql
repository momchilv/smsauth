DROP TABLE IF EXISTS `smsauth`.`users`;
CREATE TABLE `smsauth`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT , 
  `phone` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
  `email` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
  `password` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
  `status` ENUM('PENDING','ACTIVE','SUSPENDED') NOT NULL , 
  `access_token` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  PRIMARY KEY (`id`), UNIQUE `phone_unique` (`phone`)
  ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `smsauth`.`verifications`;
CREATE TABLE `smsauth`.`verifications` (
  `user_id` INT NOT NULL , 
  `code` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
  `date_last_sent` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  PRIMARY KEY (`user_id`)
  ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `smsauth`.`verification_logs`;
CREATE TABLE `smsauth`.`verification_logs` (
  `id` INT NOT NULL AUTO_INCREMENT, 
  `user_id` INT NOT NULL , 
  `code` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
  `valid` TINYINT(1) NOT NULL , 
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  PRIMARY KEY (`id`)
  ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;