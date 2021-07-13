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
ALTER TABLE `gearhart`.`routes_changes_notes` ADD INDEX ( `route_id` );

ALTER TABLE `routes_changes_notes`
    ADD FOREIGN KEY ( `route_id` ) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
