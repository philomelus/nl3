/*
	Copyright 2005, 2006, 2007, 2008 Russell E. Gibson

    This file is part of NewsLedger.

    NewsLedger is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    NewsLedger is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with NewsLedger; see the file LICENSE.  If not, see
	<http://www.gnu.org/licenses/>.
*/

/* ***************************************************************************/
/* addresses */
ALTER TABLE `addresses`
	CHANGE `owner_id` `customer_id` INT( 11 ) UNSIGNED NOT NULL,
	ADD `sequence` SMALLINT UNSIGNED NOT NULL AFTER `customer_id`,
	DROP INDEX `customers_id`;
UPDATE `addresses` SET `sequence` = 1 WHERE `type` = 'DELIVERY';
UPDATE `addresses` SET `sequence` = 101 WHERE `type` = 'BILLING';
RENAME TABLE `nl-demo`.`addresses`  TO `nl-demo`.`customers_addresses`;
ALTER TABLE `customers_addresses`
	DROP `id`,
	DROP `type`;
ALTER TABLE `customers_addresses`
	ADD PRIMARY KEY (`customer_id`, `sequence`);

/* ***************************************************************************/
/* adjustments */
ALTER TABLE `adjustments`
	DROP `asRate`,
	DROP `generated`,
	DROP `hid`;
ALTER TABLE `adjustments`
	CHANGE `aid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
	CHANGE `cid` `customer_id` INT(11) UNSIGNED NOT NULL,
	CHANGE `iid` `period_id` INT(11) UNSIGNED NOT NULL,
	CHANGE `desc` `desc` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	CHANGE `amount` `amount` DECIMAL( 6, 2 ) NOT NULL,
	CHANGE `note` `note` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL;
ALTER TABLE `adjustments`
	DROP INDEX `periods`,
	DROP INDEX `cid`,
	ADD INDEX (`customer_id`, `period_id`);
RENAME TABLE `nl-demo`.`adjustments`  TO `nl-demo`.`customers_adjustments`;
ALTER TABLE `customers_adjustments` COMMENT = '';

/* ***************************************************************************/
/* audit_log */
CREATE TABLE IF NOT EXISTS `audit_log`
(
  `id` int(11) unsigned NOT NULL auto_increment,
  `when` datetime NOT NULL,
  `who` int(11) unsigned NOT NULL,
  `what` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `when` (`when`,`who`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;
INSERT INTO `audit_log` SELECT 0 as `id`, `when`, `who`, `what` FROM `audit_200709`;
INSERT INTO `audit_log` SELECT 0 as `id`, `when`, `who`, `what` FROM `audit_200710`;
INSERT INTO `audit_log` SELECT 0 as `id`, `when`, `who`, `what` FROM `audit_200711`;
DROP TABLE `audit_200709`,
	`audit_200710`,
	`audit_200711`;

/* ***************************************************************************/
/* bills */
RENAME TABLE `nl-demo`.`bills`  TO `nl-demo`.`customers_bills`;
ALTER TABLE `customers_bills`  COMMENT = '';

/* ***************************************************************************/
/* bills_log */
/*ALTER TABLE `bills_log` ADD `sequence` SMALLINT( 6 ) UNSIGNED NOT NULL AFTER `when`;*/
RENAME TABLE `nl-demo`.`bills_log`  TO `nl-demo`.`customers_bills_log`;
ALTER TABLE `customers_bills_log` DROP PRIMARY KEY, ADD PRIMARY KEY (`when`, `sequence`, `cid`, `iid`, `what`);
ALTER TABLE `customers_bills_log`
	CHANGE `cid` `customer_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `iid` `period_id` INT( 11 ) UNSIGNED NOT NULL;

/* ***************************************************************************/
/* changes */
RENAME TABLE `nl-demo`.`changes`  TO `nl-demo`.`customers_changes`;
ALTER TABLE `customers_changes` COMMENT = '';
ALTER TABLE `customers_changes`
	CHANGE `hid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
	CHANGE `cid` `customer_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `iid` `period_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `mid` `parameter_id_old` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
	CHANGE `stamp` `created` DATETIME NOT NULL,
	CHANGE `result` `result_old` SMALLINT( 6 ) NULL DEFAULT NULL,
	CHANGE `amount` `amount` DECIMAL( 8, 3 ) NOT NULL,
	CHANGE `notes` `note` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL;
ALTER TABLE `customers_changes`
	ADD `result` ENUM( 'NONE', 'CREDITDAILY', 'CREDITSUNDAY', 'REDELIVERED', 'CREDIT', 'CHARGE' ) NOT NULL AFTER `result_old`,
	ADD `extra1` INT( 11 ) UNSIGNED NOT NULL AFTER `result` ,
	ADD `extra2` INT( 11 ) UNSIGNED NOT NULL AFTER `extra1`;
UPDATE `customers_changes` SET `result` = 'NONE' WHERE `result_old` = 0;
UPDATE `customers_changes` SET `result` = 'CREDITDAILY' WHERE `result_old` = 1;
UPDATE `customers_changes` SET `result` = 'CREDITSUNDAY' WHERE `result_old` = 2;
UPDATE `customers_changes` SET `result` = 'REDELIVERED' WHERE `result_old` = 3;
UPDATE `customers_changes` SET `result` = 'CREDIT' WHERE `result_old` = 4;
UPDATE `customers_changes` SET `result` = 'CHARGE' WHERE `result_old` = 5;
UPDATE `customers_changes`, `parameters`
	SET `extra1` = `parameters`.`p1i`, `extra2` = `parameters`.`p2i`
	WHERE `customers_changes`.`parameter_id_old` = `parameters`.`mid`;
ALTER TABLE `customers_changes` DROP `result_old`, DROP `parameter_id_old`;
DROP TABLE `parameters`;

/* ***************************************************************************/
/* changes_notes */
ALTER TABLE `changes_notes` ADD `created` datetime NOT NULL AFTER `rid`;
ALTER TABLE `changes_notes` CHANGE `rid` `route_id` INT( 11 ) NOT NULL;
RENAME TABLE `nl-demo`.`changes_notes`  TO `nl-demo`.`routes_changes_notes`;

/* ***************************************************************************/
/* customers */
CREATE TABLE IF NOT EXISTS `customers_names`
(
  `customer_id` int(11) unsigned NOT NULL,
  `sequence` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `title` varchar(10) collate utf8_bin NOT NULL,
  `first` varchar(30) collate utf8_bin NOT NULL,
  `last` varchar(30) collate utf8_bin NOT NULL,
  `suffix` varchar(10) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`customer_id`,`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `customers_names`
SELECT
  `cid` AS `customer_id`,
  1 AS `sequence`,
  NOW() AS `created`,
  NOW() AS `updated`,
  '' AS `title`,
  IFNULL(`firstName`, '') AS `first`,
  IFNULL(`lastName`, '') AS `last`,
  '' AS `suffix`
FROM
  `customers`;
INSERT INTO `customers_names`
SELECT
  `cid` AS `customer_id`,
  2 AS `sequence`,
  NOW() AS `created`,
  NOW() AS `updated`,
  '' AS `title`,
  IFNULL(`altFirstName`, '') AS `first`,
  IFNULL(`altLastName`, '') AS `last`,
  '' AS `suffix`
FROM
  `customers`
WHERE
  (`altFirstName` <=> NULL
  AND `altFirstName` != '')
  OR (`altLastName` <=> NULL
  AND `altLastName` != '');
INSERT INTO `customers_names`
SELECT
  `cid` AS `customer_id`,
  101 AS `sequence`,
  NOW() AS `created`,
  NOW() AS `updated`,
  '' AS `title`,
  IFNULL(`bFirstName`, '') AS `first`,
  IFNULL(`bLastName`, '') AS `last`,
  '' AS `suffix`
FROM
  `customers`
WHERE
  `bFirstName` != ''
  OR `bLastName` != '';
INSERT INTO `customers_names`
SELECT
  `cid` AS `customer_id`,
  102 AS `sequence`,
  NOW() AS `created`,
  NOW() AS `updated`,
  '' AS `title`,
  IFNULL(`bAltFirstName`, '') AS `first`,
  IFNULL(`bAltLastName`, '') AS `last`,
  '' AS `suffix`
FROM
  `customers`
WHERE
  `bAltFirstName` != ''
  OR `bAltLastName` != '';
ALTER TABLE `customers`
  DROP `firstName`,
  DROP `lastName`,
  DROP `altFirstName`,
  DROP `altLastName`,
  DROP `bFirstName`,
  DROP `bLastName`,
  DROP `bAltFirstName`,
  DROP `bAltLastName`;
CREATE TABLE IF NOT EXISTS `customers_telephones`
(
  `customer_id` int(11) unsigned NOT NULL,
  `sequence` smallint(6) NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `type` varchar(20) collate utf8_bin NOT NULL,
  `number` varchar(30) collate utf8_bin NOT NULL,
  `note` text collate utf8_bin,
  PRIMARY KEY  (`customer_id`,`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='One and only customer definition table';
INSERT INTO `customers_telephones`
SELECT
  `c`.`cid` as `customer_id`,
  1 as `sequence`,
  NOW() as `created`,
  NOW() as `updated`,
  IFNULL(`c`.`dTele1Type`, 'Main') as `type`,
  IFNULL(`c`.`dTele1`, '') as `number`,
  NULL as `note`
FROM `customers` AS `c`;
INSERT INTO `customers_telephones`
SELECT
  `c`.`cid` as `customer_id`,
  2 as `sequence`,
  NOW() as `created`,
  NOW() as `updated`,
  IFNULL(`c`.`dTele2Type`, 'Alternate') as `type`,
  `c`.`dTele2` as `number`,
  NULL as `note`
FROM `customers` AS `c`
WHERE NOT ISNULL(`c`.`dTele2`)
  AND `c`.`dTele2` != ''
  AND `c`.`dTele2` != `c`.`dTele1`
  AND `c`.`dTele2` != `c`.`dTele3`
  AND `c`.`dTele2` != `c`.`bTele1`
  AND `c`.`dTele2` != `c`.`bTele2`
  AND `c`.`dTele2` != `c`.`bTele3`;
INSERT INTO `customers_telephones`
SELECT
  `c`.`cid` as `customer_id`,
  3 as `sequence`,
  NOW() as `created`,
  NOW() as `updated`,
  IFNULL(`c`.`dTele3Type`, 'Mobile') as `type`,
  `c`.`dTele3` as `number`,
  NULL as `note`
FROM `customers` AS `c`
WHERE NOT ISNULL(`c`.`dTele3`)
  AND `c`.`dTele3` != ''
  AND `c`.`dTele3` != `c`.`dTele1`
  AND `c`.`dTele3` != `c`.`dTele2`
  AND `c`.`dTele3` != `c`.`bTele1`
  AND `c`.`dTele3` != `c`.`bTele2`
  AND `c`.`dTele3` != `c`.`bTele3`;
INSERT INTO `customers_telephones`
SELECT
  `c`.`cid` as `customer_id`,
  101 as `sequence`,
  NOW() as `created`,
  NOW() as `updated`,
  IFNULL(`c`.`bTele1Type`, 'Main') as `type`,
  `c`.`bTele1` as `number`,
  NULL as `note`
FROM `customers` AS `c`
WHERE NOT ISNULL(`c`.`bTele1`)
  AND `c`.`bTele1` != ''
  AND `c`.`bTele1` != `c`.`dTele1`
  AND `c`.`bTele1` != `c`.`dTele2`
  AND `c`.`bTele1` != `c`.`dTele3`
  AND `c`.`bTele1` != `c`.`bTele2`
  AND `c`.`bTele1` != `c`.`bTele3`;
INSERT INTO `customers_telephones`
SELECT
  `c`.`cid` as `customer_id`,
  102 as `sequence`,
  NOW() as `created`,
  NOW() as `updated`,
  IFNULL(`c`.`bTele2Type`, 'Alternate') as `type`,
  `c`.`bTele2` as `number`,
  NULL as `note`
FROM `customers` AS `c`
WHERE NOT ISNULL(`c`.`bTele2`)
  AND `c`.`bTele2` != ''
  AND `c`.`bTele2` != `c`.`dTele1`
  AND `c`.`bTele2` != `c`.`dTele2`
  AND `c`.`bTele2` != `c`.`dTele3`
  AND `c`.`bTele2` != `c`.`bTele1`
  AND `c`.`bTele2` != `c`.`bTele3`;
INSERT INTO `customers_telephones`
SELECT
  `c`.`cid` as `customer_id`,
  103 as `sequence`,
  NOW() as `created`,
  NOW() as `updated`,
  IFNULL(`c`.`bTele3Type`, 'Mobile') as `type`,
  `c`.`bTele3` as `number`,
  NULL as `note`
FROM `customers` AS `c`
WHERE NOT ISNULL(`c`.`bTele3`)
  AND `c`.`bTele3` != ''
  AND `c`.`bTele3` != `c`.`dTele1`
  AND `c`.`bTele3` != `c`.`dTele2`
  AND `c`.`bTele3` != `c`.`dTele3`
  AND `c`.`bTele3` != `c`.`bTele1`
  AND `c`.`bTele3` != `c`.`bTele2`;
ALTER TABLE `customers` ADD `rateType` ENUM( 'STANDARD', 'REPLACE', 'SURCHARGE' ) NOT NULL AFTER `started`;
ALTER TABLE `customers` DROP `billSpecial`;
ALTER TABLE `customers`
	CHANGE `cid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
	CHANGE `rid` `route_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `tid` `type_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `billBalance` `billBalance` DECIMAL( 10, 2 ) NOT NULL,
	CHANGE `balance` `balance` DECIMAL( 10, 2 ) NOT NULL;
ALTER TABLE `customers` DROP `lastPeriod`;
DROP TABLE `customers_original`;

/* ***************************************************************************/
/* delivery_types */
RENAME TABLE `nl-demo`.`delivery_types`  TO `nl-demo`.`customers_types`;
ALTER TABLE `customers_types`  COMMENT = '';
ALTER TABLE `customers_types`
	ADD `su` ENUM( 'N', 'Y' ) NOT NULL ,
	ADD `mo` ENUM( 'N', 'Y' ) NOT NULL,
	ADD `tu` ENUM( 'N', 'Y' ) NOT NULL,
	ADD `we` ENUM( 'N', 'Y' ) NOT NULL,
	ADD `th` ENUM( 'N', 'Y' ) NOT NULL,
	ADD `fr` ENUM( 'N', 'Y' ) NOT NULL,
	ADD `sa` ENUM( 'N', 'Y' ) NOT NULL;
UPDATE `customers_types`, `rates` SET
	`su` = `sunPaper`,
	`mo` = `monPaper` ,
	`tu` = `tuePaper` ,
	`we` = `wedPaper` ,
	`th` = `thuPaper` ,
	`fr` = `friPaper` ,
	`sa` = `satPaper`
WHERE `customers_types`.`tid` = `rates`.`tid`;
ALTER TABLE `customers_types` CHANGE `tid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;

/* ***************************************************************************/
/* errors */
ALTER TABLE `errors` CHANGE `eid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;

/* ***************************************************************************/
/* payments */
RENAME TABLE `nl-demo`.`payments`  TO `nl-demo`.`customers_payments`;
ALTER TABLE `customers_payments`
	ADD `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `when`,
	CHANGE `type` `type_old` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	ADD `type` ENUM( 'CHECK', 'MONEYORDER', 'CASH', 'CREDIT' ) NOT NULL AFTER `updated`;
UPDATE `customers_payments` SET `type` = 'CASH' WHERE `type_old` = 'CASH';
UPDATE `customers_payments` SET `type` = 'CHECK' WHERE `type_old` = 'CHECK';
UPDATE `customers_payments` SET `type` = 'MONEYORDER' WHERE `type_old` = 'MONEYORDER';
ALTER TABLE `customers_payments`
	DROP `type_old`,
	CHANGE `pid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
	CHANGE `cid` `customer_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `iid` `period_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `when` `created` DATETIME NOT NULL,
	CHANGE `userDate` `date` DATE NOT NULL,
	CHANGE `amount` `amount` DECIMAL( 10, 2 ) NOT NULL,
	CHANGE `id` `extra1` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	CHANGE `tip` `tip` DECIMAL( 10, 2 ) NOT NULL,
	ADD `extra2` TINYTEXT NOT NULL AFTER `extra1`;

/* ***************************************************************************/
/* periods */
ALTER TABLE `periods`
	CHANGE `iid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
	CHANGE `start` `start` DATE NOT NULL,
	CHANGE `end` `end` DATE NOT NULL,
	CHANGE `bill` `bill` DATE NOT NULL,
	CHANGE `changes` `changes` DATE NOT NULL,
	CHANGE `due` `due` DATE NOT NULL,
	CHANGE `title` `title` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
UPDATE `periods`
	SET `changes` = CONCAT(YEAR(`start`), '-', MONTH(`start`), '-20')
	WHERE `changes` = '0000-00-00';
UPDATE `periods`
	SET `bill` = CONCAT(YEAR(`start`), '-', MONTH(`start`), '-25')
	WHERE `bill` = '0000-00-00';

/* ***************************************************************************/
/* rates */
RENAME TABLE `nl-demo`.`rates`  TO `nl-demo`.`customers_rates`;
ALTER TABLE `customers_rates`
	DROP `sunPaper`,
	DROP `monPaper`,
	DROP `tuePaper`,
	DROP `wedPaper`,
	DROP `thuPaper`,
	DROP `friPaper`,
	DROP `satPaper`;
ALTER TABLE `customers_rates`
	CHANGE `tid` `type_id` INT(11) UNSIGNED NOT NULL,
	CHANGE `begin` `period_id_begin` INT(11) UNSIGNED NOT NULL,
	CHANGE `end` `period_id_end` INT(11) UNSIGNED NOT NULL,
	CHANGE `rate` `rate` DECIMAL(5,2) NOT NULL,
	CHANGE `sunCredit` `sunCredit` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `sunCost` `sunCost` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `monCredit` `monCredit` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `monCost` `monCost` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `tueCredit` `tueCredit` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `tueCost` `tueCost` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `wedCredit` `wedCredit` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `wedCost` `wedCost` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `thuCredit` `thuCredit` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `thuCost` `thuCost` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `friCredit` `friCredit` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `friCost` `friCost` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `satCredit` `satCredit` DECIMAL(6,3) NULL DEFAULT NULL,
	CHANGE `satCost` `satCost` DECIMAL(6,3) NULL DEFAULT NULL;

/* ***************************************************************************/
/* route */
ALTER TABLE `routes` CHANGE `rid` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;

/* ***************************************************************************/
/* security */
ALTER TABLE `security`
	CHANGE `gid` `group_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `uid` `user_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `page` `page` INT( 11 ) NOT NULL,
	CHANGE `feature` `feature` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	CHANGE `allowed` `allowed` ENUM( 'N', 'Y' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	CHANGE `desc` `desc` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	CHANGE `note` `note` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

/* ***************************************************************************/
/* sequenc */
RENAME TABLE `nl-demo`.`sequence`  TO `nl-demo`.`routes_sequence`;
ALTER TABLE `routes_sequence`
	CHANGE `cid` `customer_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `rid` `route_id` INT( 11 ) UNSIGNED NOT NULL,
	CHANGE `order` `order` INT( 11 ) UNSIGNED NOT NULL;

/* ***************************************************************************/
/* special_bills */
CREATE TABLE IF NOT EXISTS `customers_combined_bills`
(
  `customer_id_main` int(11) unsigned NOT NULL,
  `customer_id_secondary` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`customer_id_main`,`customer_id_secondary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `customers_combined_bills`
SELECT `cid`, `param1`, NOW(), NOW()
	FROM `special_bills`
	WHERE `opcode` = 0 AND `param1` != 0;
INSERT INTO `customers_combined_bills`
SELECT `cid`, `param2`, NOW(), NOW()
	FROM `special_bills`
	WHERE `opcode` = 0 AND `param2` != 0;
DROP TABLE `special_bills`;

/* ***************************************************************************/
/* users */
ALTER TABLE `users`
	CHANGE `uid` `id` SMALLINT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT,
	CHANGE `gid` `group_id` SMALLINT( 6 ) NOT NULL;

/* ***************************************************************************/
/* users_config */
ALTER TABLE `users_config` CHANGE `uid` `user_id` INT( 10 ) UNSIGNED NOT NULL;

/* ***************************************************************************/
/* xref_ocid */
DROP TABLE `xref_ocid`;

/* ***************************************************************************/
/* configuration */
CREATE TABLE IF NOT EXISTS `configuration`
(
  `key` tinytext collate utf8_bin NOT NULL,
  `type` enum('COLOR','FLOAT','IID','INTEGER','RID','TID','STRING') collate utf8_bin NOT NULL,
  `value` tinytext collate utf8_bin NOT NULL,
  `note` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`key`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ***************************************************************************/
/* users_configuration */
CREATE TABLE IF NOT EXISTS `users_configuration`
(
  `key` tinytext collate utf8_bin NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `type` enum('COLOR','FLOAT','IID','INTEGER','RID','TID','STRING') collate utf8_bin NOT NULL,
  `value` tinytext collate utf8_bin NOT NULL,
  `note` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`key`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ***************************************************************************/
/* users_configuration */
CREATE TABLE IF NOT EXISTS `groups`
(
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` tinytext collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;
INSERT INTO `groups` (`id`, `name`) VALUES
(1, 0x41646d696e6973747261746f72),
(2, 0x5265706f72746572);
