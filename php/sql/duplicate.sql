/*
 * Do a search/replace on `backup_gearhart` and `gearhart` with the database names, then execute.
 */

/* Disable recursive foreign keys */
ALTER TABLE `customers`
    DROP FOREIGN KEY `customers_billPeriod`,
    DROP FOREIGN KEY `customers_lastPayment`;

/* no constraints */
INSERT INTO `backup_gearhart`.`configuration` SELECT * FROM `gearhart`.`configuration`;
INSERT INTO `backup_gearhart`.`customers_types` SELECT * FROM `gearhart`.`customers_types`;
INSERT INTO `backup_gearhart`.`errors` SELECT * FROM `gearhart`.`errors`;
INSERT INTO `backup_gearhart`.`groups` SELECT * FROM `gearhart`.`groups`;
INSERT INTO `backup_gearhart`.`periods` SELECT * FROM `gearhart`.`periods`;
INSERT INTO `backup_gearhart`.`routes` SELECT * FROM `gearhart`.`routes`;
INSERT INTO `backup_gearhart`.`users` SELECT * FROM `gearhart`.`users`;
/* users constraints */
INSERT INTO `backup_gearhart`.`audit_log` SELECT * FROM `gearhart`.`audit_log`;
INSERT INTO `backup_gearhart`.`users_configuration` SELECT * FROM `gearhart`.`users_configuration`;
/* users, groups constraints */
INSERT INTO `backup_gearhart`.`security` SELECT * FROM `gearhart`.`security`;
/* groups constraints */
INSERT INTO `backup_gearhart`.`groups_configuration` SELECT * FROM `gearhart`.`groups_configuration`;
/* customers_types, periods constraints */
INSERT INTO `backup_gearhart`.`customers_rates` SELECT * FROM `gearhart`.`customers_rates`;
/* routes constraints */
INSERT INTO `backup_gearhart`.`routes_changes_notes` SELECT * FROM `gearhart`.`routes_changes_notes`;
/* customers_types, routes constraints */
INSERT INTO `backup_gearhart`.`customers` SELECT * FROM `gearhart`.`customers`;
/* customers constraints */
INSERT INTO `backup_gearhart`.`customers_addresses` SELECT * FROM `gearhart`.`customers_addresses`;
INSERT INTO `backup_gearhart`.`customers_combined_bills` SELECT * FROM `gearhart`.`customers_combined_bills`;
INSERT INTO `backup_gearhart`.`customers_names` SELECT * FROM `gearhart`.`customers_names`;
INSERT INTO `backup_gearhart`.`customers_telephones` SELECT * FROM `gearhart`.`customers_telephones`;
/* customers, periods constraints */
INSERT INTO `backup_gearhart`.`customers_adjustments` SELECT * FROM `gearhart`.`customers_adjustments`;
INSERT INTO `backup_gearhart`.`customers_complaints` SELECT * FROM `gearhart`.`customers_complaints`;
INSERT INTO `backup_gearhart`.`customers_payments` SELECT * FROM `gearhart`.`customers_payments`;
INSERT INTO `backup_gearhart`.`customers_service` SELECT * FROM `gearhart`.`customers_service`;
INSERT INTO `backup_gearhart`.`customers_service_types` SELECT * FROM `gearhart`.`customers_service_types`;
INSERT INTO `backup_gearhart`.`customers_bills_log` SELECT * FROM `gearhart`.`customers_bills_log`;
/* periods constraints (will be customers later on as well) */
INSERT INTO `backup_gearhart`.`customers_bills` SELECT * FROM `gearhart`.`customers_bills`;
/* customers, routes constraints */
INSERT INTO `backup_gearhart`.`routes_sequence` SELECT * FROM `gearhart`.`routes_sequence`;

/* Enable recursive foreign keys */
ALTER TABLE `customers`
    ADD CONSTRAINT `customers_billPeriod`
        FOREIGN KEY ( `billPeriod` )
        REFERENCES `periods` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    ADD CONSTRAINT `customers_lastPayment`
        FOREIGN KEY ( `lastPayment` )
        REFERENCES `customers_payments` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE;
