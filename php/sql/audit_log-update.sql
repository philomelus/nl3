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
