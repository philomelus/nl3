/*
	Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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

/*
    Convert the audit_log table to new format that properly supports
    desired contstraints.
*/

CREATE TABLE `audit_log_old` (
    `id` int(11) unsigned NOT NULL,
    `when` datetime NOT NULL,
    `who` smallint(5) unsigned NOT NULL,
    `what` text NOT NULL
) ENGINE=InnoDB;

INSERT INTO `audit_log_old` SELECT * FROM `audit_log`;

DROP TABLE `audit_log`;

CREATE TABLE `audit_log` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `when` datetime NOT NULL,
    `user_id` smallint(5) unsigned,
    `what` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `audit_log`
    ADD CONSTRAINT `audit_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `audit_log`
SELECT
    `id`,
    `when`,
    IF (`who` = 0, NULL, `who`) as `user_id`,
    `what`
FROM `audit_log_old`;

DROP TABLE `audit_log_old`;

/*
    Convert the customers_adjustments table to new format
    that properly supports desired contstraints.
*/

CREATE TABLE `customers_adjustments_old` (
    `id` int(11) unsigned NOT NULL,
    `customer_id` int(11) unsigned NOT NULL,
    `period_id` int(11) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `desc` tinytext COLLATE utf8_bin NOT NULL,
    `amount` decimal(6,2) NOT NULL,
    `note` text
) ENGINE=InnoDB;

INSERT INTO `customers_adjustments_old` SELECT * FROM `customers_adjustments`;

DROP TABLE `customers_adjustments`;

CREATE TABLE `customers_adjustments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL,
  `period_id` int(11) unsigned,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `desc` tinytext NOT NULL,
  `amount` decimal(6,2) NOT NULL,
  `note` text,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `period_id` (`period_id`)
) ENGINE=InnoDB;


ALTER TABLE `customers_adjustments`
    ADD CONSTRAINT `customers_adjustments_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_adjustments_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON UPDATE CASCADE;

INSERT INTO `customers_adjustments`
SELECT
  `id`,
  `customer_id`,
    IF (`period_id` = 0, NULL, `period_id`) as `period_id`,
  `created`,
  `updated`,
  `desc`,
  `amount`,
  `note`
FROM `customers_adjustments_old`;

DROP TABLE `customers_adjustments_old`;

/*
    Convert the customers_complaints table to new format
    that properly supports desired contstraints.
*/

CREATE TABLE `customers_complaints_old` (
    `id` int(11) unsigned NOT NULL,
    `customer_id` int(11) unsigned NOT NULL,
    `period_id` int(11) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `type` enum('MISSED','WET','DAMAGED','LATE','OTHER') NOT NULL,
    `when` date NOT NULL,
    `why` varchar(255) NOT NULL,
    `result` enum('NONE','CREDITDAILY','CREDITSUNDAY','REDELIVERED','CREDIT','CHARGE') NOT NULL,
    `amount` decimal(8,3) NOT NULL,
    `ignoreOnBill` enum('N','Y') NOT NULL,
    `note` text
) ENGINE=InnoDB;

INSERT INTO `customers_complaints_old` SELECT * FROM `customers_complaints`;

DROP TABLE `customers_complaints`;

CREATE TABLE `customers_complaints` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) unsigned NOT NULL,
    `period_id` int(11) unsigned,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `type` enum('MISSED','WET','DAMAGED','LATE','OTHER') NOT NULL,
    `when` date NOT NULL,
    `why` varchar(255) NOT NULL,
    `result` enum('NONE','CREDITDAILY','CREDITSUNDAY','REDELIVERED','CREDIT','CHARGE') NOT NULL,
    `amount` decimal(8,3) NOT NULL,
    `ignoreOnBill` enum('N','Y') NOT NULL,
    `note` text,
    PRIMARY KEY (`id`),
    KEY `customer_id` (`customer_id`),
    KEY `period_id` (`period_id`)
) ENGINE=InnoDB;

ALTER TABLE `customers_complaints`
    ADD CONSTRAINT `customers_complaints_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_complaints_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON UPDATE CASCADE;

INSERT INTO `customers_complaints`
SELECT
    `id`,
    `customer_id`,
    IF (`period_id` = 0, NULL, `period_id`) as `period_id`,
    `created`,
    `updated`,
    `type`,
    `when`,
    `why`,
    `result`,
    `amount`,
    `ignoreOnBill`,
    `note`
FROM `customers_complaints_old`;

DROP TABLE `customers_complaints_old`;

/*
    Convert the customers_rates table to new format
    that properly supports desired contstraints.
*/

CREATE TABLE `customers_rates_old` (
    `type_id` int( 11 ) unsigned NOT NULL,
    `period_id_begin` int( 11 ) unsigned NOT NULL,
    `period_id_end` int( 11 ) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `rate` decimal( 5, 2 ) NOT NULL,
    `daily_credit` decimal( 6, 2 ) NOT NULL,
    `sunday_credit` decimal( 6, 2 ) NOT NULL
) ENGINE = InnoDB;

INSERT INTO `customers_rates_old` SELECT * FROM `customers_rates`;

DROP TABLE `customers_rates`;

CREATE TABLE `customers_rates` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `type_id` int(11) unsigned NOT NULL,
    `period_id_begin` int(11) unsigned NOT NULL,
    `period_id_end` int(11) unsigned,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `rate` decimal(6,2) NOT NULL,
    `daily_credit` decimal(6,2) NOT NULL,
    `sunday_credit` decimal(6,2) NOT NULL,
    PRIMARY KEY ( `id` ),
    KEY `customers_rates_type_id` (`type_id`),
    KEY `customers_rates_period_id_begin` (`period_id_begin`),
    KEY `customers_rates_period_id_end` (`period_id_end`)
) ENGINE=InnoDB;

ALTER TABLE `customers_rates`
    ADD CONSTRAINT `customers_rates_type_id` FOREIGN KEY (`type_id`) REFERENCES `customers_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_rates_period_id_begin` FOREIGN KEY (`period_id_begin`) REFERENCES `periods` (`id`) ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_rates_period_id_end` FOREIGN KEY (`period_id_end`) REFERENCES `periods` (`id`) ON UPDATE CASCADE;

INSERT INTO `customers_rates`
SELECT
    0,
    `type_id`,
    `period_id_begin`,
    IF (`period_id_end` = 0, NULL, `period_id_end`) as `period_id_end`,
    `created`,
    `updated`,
    `rate`,
    `daily_credit`,
    `sunday_credit`
FROM `customers_rates_old`;

DROP TABLE `customers_rates_old`;

/*
    Convert the customers_service table to new format
    that properly supports desired contstraints.
*/

CREATE TABLE `customers_service_old` (
    `id` int( 11 ) unsigned NOT NULL,
    `customer_id` int( 11 ) unsigned NOT NULL ,
    `period_id` int( 11 ) unsigned NOT NULL ,
    `created` datetime NOT NULL ,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
    `type` enum( 'STOP', 'START' ) NOT NULL ,
    `when` date NOT NULL,
    `why` varchar( 255 ) NOT NULL,
    `ignoreOnBill` enum( 'N', 'Y' ) NOT NULL ,
    `note` text
) ENGINE = InnoDB;

INSERT INTO `customers_service_old` SELECT * FROM `customers_service`;

DROP TABLE `customers_service`;

CREATE TABLE `customers_service` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) unsigned NOT NULL,
    `period_id` int(11) unsigned,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `type` enum('STOP','START') NOT NULL,
    `when` date NOT NULL,
    `why` varchar(255) NOT NULL,
    `ignoreOnBill` enum('N','Y') NOT NULL,
    `note` text,
    PRIMARY KEY (`id`),
    KEY `period_id_index` (`period_id`),
    KEY `customer_id_index` (`customer_id`)
) ENGINE=InnoDB;

ALTER TABLE `customers_service`
    ADD CONSTRAINT `customers_service_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON UPDATE CASCADE;

INSERT INTO `customers_service`
SELECT
    `id`,
    `customer_id`,
    IF (`period_id` = 0, NULL, `period_id`) as `period_id`,
    `created`,
    `updated`,
    `type`,
    `when`,
    `why`,
    `ignoreOnBill`,
    `note` text
FROM `customers_service_old`;

DROP TABLE `customers_service_old`;

/*
    Update the customers_service_types table to version that
    properly supports constraints.
*/

CREATE TABLE `customers_service_types_old` (
    `id` int( 11 ) unsigned NOT NULL,
    `customer_id` int( 11 ) unsigned NOT NULL,
    `period_id` int( 11 ) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `when` date NOT NULL,
    `why` tinytext NOT NULL,
    `type_id_from` int( 11 ) unsigned NOT NULL,
    `type_id_to` int( 11 ) unsigned NOT NULL,
    `ignoreOnBill` enum( 'N', 'Y' ) NOT NULL,
    `note` text
) ENGINE = InnoDB;

INSERT INTO `customers_service_types_old` SELECT * FROM `customers_service_types`;

DROP TABLE `customers_service_types`;

CREATE TABLE `customers_service_types` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) unsigned NOT NULL,
    `period_id` int(11) unsigned,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `when` date NOT NULL,
    `why` tinytext NOT NULL,
    `type_id_from` int(11) unsigned NOT NULL,
    `type_id_to` int(11) unsigned NOT NULL,
    `ignoreOnBill` enum('N','Y') NOT NULL,
    `note` text,
    PRIMARY KEY (`id`),
    KEY `customer_id_index` (`customer_id`),
    KEY `type_id_from_index` (`type_id_from`),
    KEY `type_id_to_index` (`type_id_to`),
    KEY `period_id_index` (`period_id`)
) ENGINE=InnoDB;

ALTER TABLE `customers_service_types`
    ADD CONSTRAINT `customers_service_types_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_types_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_types_type_id_to` FOREIGN KEY (`type_id_to`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_types_type_id_from` FOREIGN KEY (`type_id_from`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

INSERT INTO `customers_service_types`
SELECT
    `id`,
    `customer_id`,
    IF (`period_id` = 0, NULL, `period_id`) as `period_id`,
    `created`,
    `updated`,
    `when`,
    `why`,
    `type_id_from`,
    `type_id_to`,
    `ignoreOnBill`,
    `note`
FROM `customers_service_types_old`;

DROP TABLE `customers_service_types_old`;

/*
    Convert the routes_changes_notes table to new format
    that properly supports desired contstraints.
*/

CREATE TABLE `routes_changes_notes_old` (
  `date` date NOT NULL,
  `route_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` mediumtext COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB;

INSERT INTO `routes_changes_notes_old` SELECT * FROM `routes_changes_notes`;

DROP TABLE `routes_changes_notes`;

CREATE TABLE `routes_changes_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `route_id` int(11) unsigned,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `routes_changes_notes`
SELECT
  0,
  `date`,
  IF (`route_id` = 0, NULL, `route_id`) as `route_id`,
  `created`,
  `updated`,
  `note`
FROM `routes_changes_notes_old`;

DROP TABLE `routes_changes_notes_old`;

ALTER TABLE `routes_changes_notes` ADD INDEX ( `date` );
ALTER TABLE `routes_changes_notes` ADD INDEX ( `route_id` );

ALTER TABLE `routes_changes_notes`
    ADD FOREIGN KEY ( `route_id` ) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
