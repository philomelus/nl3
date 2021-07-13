
CREATE TABLE `audit_log` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `when` datetime NOT NULL,
    `user_id` smallint(5) unsigned,
    `what` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `configuration` (
    `key` varchar(255) NOT NULL,
    `value` varchar(255) NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB;

CREATE TABLE `customers` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `route_id` int(11) unsigned NOT NULL,
    `type_id` int(11) unsigned NOT NULL,
    `active` enum('N','Y') NOT NULL,
    `routeList` enum('N','Y') NOT NULL,
    `started` date,
    `rateType` enum('STANDARD','REPLACE','SURCHARGE') NOT NULL,
    `rateOverride` decimal(10,2) NOT NULL,
    `billType` int(11) unsigned NOT NULL,
    `billBalance` decimal(10,2) NOT NULL,
    `billStopped` enum('N','Y') NOT NULL,
    `billCount` smallint(5) unsigned NOT NULL,
    `billPeriod` int(11) unsigned,
    `billQuantity` smallint(6) NOT NULL,
    `billStart` date,
    `billEnd` date,
    `billDue` date,
    `balance` decimal(10,2) NOT NULL,
    `lastPayment` int(11) unsigned,
    `billNote` text,
    `notes` text,
    `deliveryNote` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `route_id` (`route_id`),
    KEY `type_id` (`type_id`),
    KEY `billPeriod` (`billPeriod`),
    KEY `last_payment` (`lastPayment`)
) ENGINE=InnoDB;

CREATE TABLE `customers_addresses` (
    `customer_id` int(11) unsigned NOT NULL,
    `sequence` smallint(5) unsigned NOT NULL,
    `address1` varchar(30) NOT NULL,
    `address2` varchar(30) NOT NULL,
    `city` varchar(30) NOT NULL,
    `state` varchar(2) NOT NULL,
    `zip` varchar(10) NOT NULL,
    PRIMARY KEY (`customer_id`,`sequence`)
) ENGINE=InnoDB;

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

CREATE TABLE `customers_bills` (
    `cid` varchar(6) NOT NULL,
    `iid` int(11) unsigned NOT NULL,
    `rateType` enum('STANDARD','REPLACE','SURCHARGE') NOT NULL,
    `rateOverride` decimal(10,2) NOT NULL,
    `created` datetime NOT NULL,
    `when` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `export` enum('N','Y') NOT NULL,
    `cnm` varchar(25) NOT NULL,
    `cad1` varchar(25) NOT NULL,
    `cad2` varchar(25) NOT NULL,
    `ctel` varchar(25) NOT NULL,
    `rt` varchar(10) NOT NULL,
    `dNm` varchar(20) NOT NULL,
    `dAd` varchar(20) NOT NULL,
    `dCt` varchar(11) NOT NULL,
    `dSt` varchar(2) NOT NULL,
    `dZp` varchar(5) NOT NULL,
    `bNm` varchar(22) NOT NULL,
    `bAd1` varchar(22) NOT NULL,
    `bAd2` varchar(22) NOT NULL,
    `bAd3` varchar(22) NOT NULL,
    `bAd4` varchar(22) NOT NULL,
    `rTit` varchar(30) NOT NULL,
    `rate` varchar(10) NOT NULL,
    `fwd` varchar(10) NOT NULL,
    `pmt` varchar(10) NOT NULL,
    `adj` varchar(10) NOT NULL,
    `bal` varchar(10) NOT NULL,
    `due` varchar(10) NOT NULL,
    `dts` varchar(10) NOT NULL,
    `dte` varchar(10) NOT NULL,
    `nt1` varchar(36) NOT NULL,
    `nt2` varchar(36) NOT NULL,
    `nt3` varchar(36) NOT NULL,
    `nt4` varchar(36) NOT NULL,
    PRIMARY KEY (`cid`,`iid`),
    KEY `iid` (`iid`)
) ENGINE=InnoDB;

CREATE TABLE `customers_bills_log` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `when` datetime NOT NULL,
    `sequence` smallint(6) unsigned NOT NULL,
    `customer_id` int(11) unsigned NOT NULL,
    `period_id` int(11) unsigned NOT NULL,
    `what` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `period_id` (`period_id`),
    KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB;

CREATE TABLE `customers_combined_bills` (
    `customer_id_main` int(11) unsigned NOT NULL,
    `customer_id_secondary` int(11) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`customer_id_main`,`customer_id_secondary`),
    KEY `customer_id_secondary` (`customer_id_secondary`)
) ENGINE=InnoDB;

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

CREATE TABLE `customers_names` (
    `customer_id` int(11) unsigned NOT NULL,
    `sequence` int(10) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `title` varchar(10) NOT NULL,
    `first` varchar(30) NOT NULL,
    `last` varchar(30) NOT NULL,
    `surname` varchar(10) NOT NULL,
    PRIMARY KEY (`customer_id`,`sequence`)
) ENGINE=InnoDB;

CREATE TABLE `customers_payments` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) unsigned NOT NULL,
    `period_id` int(11) unsigned NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `type` enum('CHECK','MONEYORDER','CASH','CREDIT') NOT NULL,
    `date` date NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `extra1` tinytext NOT NULL,
    `extra2` tinytext NOT NULL,
    `tip` decimal(10,2) NOT NULL,
    `note` text,
    PRIMARY KEY (`id`),
    KEY `customer_id` (`customer_id`),
    KEY `period_id` (`period_id`)
) ENGINE=InnoDB;

CREATE TABLE `customers_rates` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) unsigned NOT NULL,
    `period_id_begin` int(11) unsigned NOT NULL,
    `period_id_end` int(11) unsigned,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `rate` decimal(6,2) NOT NULL,
    `daily_credit` decimal(6,2) NOT NULL,
    `sunday_credit` decimal(6,2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `customers_rates_type_id` (`type_id`),
    KEY `customers_rates_period_id_begin` (`period_id_begin`),
    KEY `customers_rates_period_id_end` (`period_id_end`)
) ENGINE=InnoDB;

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

CREATE TABLE `customers_telephones` (
    `customer_id` int(11) unsigned NOT NULL,
    `sequence` smallint(6) NOT NULL,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `type` varchar(20) NOT NULL,
    `number` varchar(30) NOT NULL,
    PRIMARY KEY (`customer_id`,`sequence`)
) ENGINE=InnoDB;

CREATE TABLE `customers_types` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `abbr` varchar(10) NOT NULL,
    `name` varchar(30) NOT NULL,
    `color` int(11) unsigned NOT NULL,
    `visible` enum('N','Y') NOT NULL,
    `newChange` enum('N','Y') NOT NULL,
    `watchStart` enum('N','Y') NOT NULL,
    `su` enum('N','Y') NOT NULL,
    `mo` enum('N','Y') NOT NULL,
    `tu` enum('N','Y') NOT NULL,
    `we` enum('N','Y') NOT NULL,
    `th` enum('N','Y') NOT NULL,
    `fr` enum('N','Y') NOT NULL,
    `sa` enum('N','Y') NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `errors` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `when` datetime NOT NULL,
    `icode` int(11) NOT NULL,
    `ecode` int(11) NOT NULL,
    `context` varchar(255) NOT NULL,
    `query` varchar(1024) NOT NULL,
    `what` varchar(1024) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `groups` (
    `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `name` tinytext NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `groups_configuration` (
    `key` varchar(255) NOT NULL,
    `group_id` smallint(5) unsigned NOT NULL,
    `value` varchar(255) NOT NULL,
    PRIMARY KEY (`key`),
    KEY `user_id` (`group_id`)
) ENGINE=InnoDB;

CREATE TABLE `periods` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `changes_start` date NOT NULL,
    `changes_end` date NOT NULL,
    `bill` date NOT NULL,
    `display_start` date NOT NULL,
    `display_end` date NOT NULL,
    `due` date NOT NULL,
    `title` varchar(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `routes` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `title` varchar(20) NOT NULL,
    `active` enum('N','Y') NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `routes_changes_notes` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL,
    `route_id` int(11) unsigned,
    `created` datetime NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `note` mediumtext NOT NULL,
    PRIMARY KEY (`id`),
    KEY `date` (`date`),
    KEY `route_id` (`route_id`)
) ENGINE=InnoDB;

CREATE TABLE `routes_sequence` (
    `tag_id` int(11) unsigned NOT NULL,
    `route_id` int(11) unsigned NOT NULL,
    `order` int(11) unsigned NOT NULL,
    PRIMARY KEY (`tag_id`,`route_id`),
    KEY `route_id` (`route_id`)
) ENGINE=InnoDB;

CREATE TABLE `security` (
    `group_id` smallint(5) unsigned,
    `user_id` smallint(5) unsigned,
    `page` varchar(20) NOT NULL,
    `feature` varchar(20) NOT NULL,
    `allowed` enum('N','Y') NOT NULL,
    UNIQUE KEY `group_id` (`group_id`,`user_id`,`page`,`feature`),
    KEY `groups` (`group_id`),
    KEY `users` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `users` (
    `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `login` varchar(20) NOT NULL,
    `password` varchar(255) NOT NULL,
    `name` varchar(30) NOT NULL,
    `group_id` smallint(5) unsigned NOT NULL,
    `home` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `group_id` (`group_id`)
) ENGINE=InnoDB;

CREATE TABLE `users_configuration` (
    `key` varchar(255) NOT NULL,
    `user_id` smallint(5) unsigned NOT NULL,
    `value` varchar(255) NOT NULL,
    PRIMARY KEY (`key`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB;

ALTER TABLE `audit_log`
    ADD CONSTRAINT `audit_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers`
    ADD CONSTRAINT `customers_route_id` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_type_id` FOREIGN KEY (`type_id`) REFERENCES `customers_types` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_addresses`
    ADD CONSTRAINT `customers_addresses_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `customers_adjustments`
    ADD CONSTRAINT `customers_adjustments_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_adjustments_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_bills`
    ADD CONSTRAINT `customers_bills_period_id` FOREIGN KEY (`iid`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_bills_log`
    ADD CONSTRAINT `customers_bills_log_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_bills_log_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_combined_bills`
    ADD CONSTRAINT `customers_combined_bills_customer_id_main` FOREIGN KEY (`customer_id_main`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_combined_bills_customer_id_secondary` FOREIGN KEY (`customer_id_secondary`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_complaints`
    ADD CONSTRAINT `customers_complaints_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_complaints_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_names`
    ADD CONSTRAINT `customers_names_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `customers_payments`
    ADD CONSTRAINT `customers_payments_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_payments_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_rates`
    ADD CONSTRAINT `customers_rates_type_id` FOREIGN KEY (`type_id`) REFERENCES `customers_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_rates_period_id_begin` FOREIGN KEY (`period_id_begin`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_rates_period_id_end` FOREIGN KEY (`period_id_end`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_service`
    ADD CONSTRAINT `customers_service_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_service_types`
    ADD CONSTRAINT `customers_service_types_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_types_period_id` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_types_type_id_from` FOREIGN KEY (`type_id_from`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_service_types_type_id_to` FOREIGN KEY (`type_id_to`) REFERENCES `periods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customers_telephones`
    ADD CONSTRAINT `customers_telephones_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `groups_configuration`
    ADD CONSTRAINT `groups_configuration_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `routes_changes_notes`
    ADD CONSTRAINT `routes_changes_notes_route_id` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `routes_sequence`
    ADD CONSTRAINT `routes_sequence_route_id` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `routes_sequence_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `security`
    ADD CONSTRAINT `security_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `security_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users`
    ADD CONSTRAINT `users_group_id`
        FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;

ALTER TABLE `users_configuration`
    ADD CONSTRAINT `users_configuration_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;

/* ONLY recursive constraints in database */
ALTER TABLE `customers`
    ADD CONSTRAINT `customers_billPeriod`
        FOREIGN KEY ( `billPeriod` ) REFERENCES `periods` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_lastPayment`
        FOREIGN KEY ( `lastPayment` ) REFERENCES `customers_payments` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE;
