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
