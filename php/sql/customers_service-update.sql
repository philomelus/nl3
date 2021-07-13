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
