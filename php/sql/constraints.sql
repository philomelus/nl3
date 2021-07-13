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

ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_3` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`),
  ADD CONSTRAINT `customers_ibfk_4` FOREIGN KEY (`type_id`) REFERENCES `customers_types` (`id`);

ALTER TABLE `customers_addresses`
  ADD CONSTRAINT `customers_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

ALTER TABLE `customers_adjustments`
  ADD CONSTRAINT `customers_adjustments_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

ALTER TABLE `customers_bills_log`
  ADD CONSTRAINT `customers_bills_log_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `customers_bills_log_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

ALTER TABLE `customers_combined_bills`
  ADD CONSTRAINT `customers_combined_bills_ibfk_1` FOREIGN KEY (`customer_id_main`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `customers_combined_bills_ibfk_2` FOREIGN KEY (`customer_id_secondary`) REFERENCES `customers` (`id`);

ALTER TABLE `customers_complaints`
  ADD CONSTRAINT `customers_complaints_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

ALTER TABLE `customers_names`
  ADD CONSTRAINT `customers_names_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

ALTER TABLE `customers_payments`
  ADD CONSTRAINT `customers_payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `customers_payments_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

ALTER TABLE `customers_rates`
  ADD CONSTRAINT `customers_rates_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `customers_types` (`id`),
  ADD CONSTRAINT `customers_rates_ibfk_2` FOREIGN KEY (`period_id_begin`) REFERENCES `periods` (`id`);

ALTER TABLE `customers_service`
  ADD CONSTRAINT `customers_service_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

ALTER TABLE `customers_service_types`
  ADD CONSTRAINT `customers_service_types_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

ALTER TABLE `customers_telephones`
  ADD CONSTRAINT `customers_telephones_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

ALTER TABLE `routes_sequence`
  ADD CONSTRAINT `routes_sequence_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `routes_sequence_ibfk_3` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`);

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`);

ALTER TABLE `users_config`
  ADD CONSTRAINT `users_config_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `users_configuration`
  ADD CONSTRAINT `users_configuration_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
