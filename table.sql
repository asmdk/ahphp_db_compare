CREATE TABLE `records` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL DEFAULT '',
	`text` TEXT NULL,
	`price` DECIMAL(10,0) NOT NULL DEFAULT '0',
	`status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;