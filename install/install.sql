
CREATE TABLE IF NOT EXISTS  `PREFIX_twisty_orders` (
	`id_twisty` INT(11) NOT NULL AUTO_INCREMENT,
	`id_order_detail` INT(10) NOT NULL,
	`qte_picked` INT(11) NOT NULL,
	`id_box` varchar(20) NOT NULL ,
	`is_finished` TINYINT(1) NOT NULL DEFAULT '0',
	`date_twisty` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`is_valid` TINYINT(1) NOT NULL DEFAULT '1',
	`is_show` TINYINT(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id_twisty`)
);



