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
