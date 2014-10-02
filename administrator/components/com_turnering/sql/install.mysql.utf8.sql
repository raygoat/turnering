CREATE TABLE IF NOT EXISTS `#__dbu_kredse` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`alder` VARCHAR(255)  NOT NULL ,
`kreds` INT(11)  NOT NULL ,
`dbukredsnr` INT(11)  NOT NULL ,
`label` VARCHAR(255)  NOT NULL ,
`gennemgaaende` BOOLEAN NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

