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

/* customers_complaints */
CREATE TABLE IF NOT EXISTS `customers_complaints`
(
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `period_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL,
  `type` enum('MISSED','WET','DAMAGED','LATE','OTHER') NOT NULL,
  `when` date NOT NULL default '0000-00-00',
  `why` tinytext NOT NULL,
  `result` enum('NONE','CREDITDAILY','CREDITSUNDAY','REDELIVERED','CREDIT','CHARGE') NOT NULL,
  `amount` decimal(8,3) NOT NULL,
  `ignoreOnBill` enum('N','Y') NOT NULL,
  `note` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `customers_complaints`
SELECT
  `id`,
  `customer_id`,
  `period_id`,
  `created`,
  `updated`,
  `what` AS `type`,
  `when`,
  `why`,
  `result`,
  `amount`,
  `ignoreOnBill`,
  `note`
FROM
  `customers_changes`
WHERE
  `what` IN ('MISSED', 'WET', 'DAMAGED');

/* customers_service */
CREATE TABLE IF NOT EXISTS `customers_service` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `period_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `type` enum('STOP','START') collate utf8_bin NOT NULL,
  `when` date NOT NULL default '0000-00-00',
  `why` tinytext collate utf8_bin NOT NULL,
  `ignoreOnBill` enum('N','Y') collate utf8_bin NOT NULL,
  `note` text collate utf8_bin,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `customers_service`
SELECT
  `id`,
  `customer_id`,
  `period_id`,
  `created`,
  `updated`,
  `what` AS `type`,
  `when`,
  IFNULL(`why`, ''),
  `ignoreOnBill`,
  `note`
FROM
  `customers_changes`
WHERE
  `what` IN ('STOP', 'START');

/* customers_service_types */
CREATE TABLE IF NOT EXISTS `customers_service_types` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `period_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `when` date NOT NULL default '0000-00-00',
  `why` tinytext collate utf8_bin NOT NULL,
  `type_id_from` int(11) unsigned NOT NULL,
  `type_id_to` int(11) unsigned NOT NULL,
  `ignoreOnBill` enum('N','Y') collate utf8_bin NOT NULL,
  `note` text collate utf8_bin,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `customers_service_types`
SELECT
  `id`,
  `customer_id`,
  `period_id`,
  `created`,
  `updated`,
  `when`,
  IFNULL(`why`, ''),
  `extra1` AS `type_id_from`,
  `extra2` AS `type_id_to`,
  `ignoreOnBill`,
  `note`
FROM
  `customers_changes`
WHERE
  `what` = 'TYPE';
