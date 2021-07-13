/*
    Convert the customers_rates table to new format
    that properly supports desired contstraints.
*/

CREATE TABLE `gearhart`.`customers_rates_old` (
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
