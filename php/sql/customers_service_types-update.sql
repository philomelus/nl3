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

