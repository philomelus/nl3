<?php
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

	set_include_path('..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/common.inc.php';
	require_once 'inc/billing.inc.php';
    require_once 'inc/sql.inc.php';
    
	class log
	{
		static $SEQUENCE = 0;

		private $customer_id;
		private $period_id;
		private $context;

		function __construct()
		{
		}
		public function __call($m, $a)
		{
			if (count($a) > 0)
				$this->$m = $a[0];
			else
				return $this->$m;
		}
		public function message($m)
		{
			// Could use delayed here, but that'd force MyISAM on us as,
			// would really only be an issue for a client with a LOT of
			// customers (like 3k or 4k or more)
			global $DB;
			++log::$SEQUENCE;
			db_query(
'
INSERT INTO
	`customers_bills_log`
SET
	`when` = NOW(),
	`customer_id` = ' . $this->customer_id . ',
	`period_id` = ' . $this->period_id . ',
	`sequence` = ' . log::$SEQUENCE . ',
	`what` = \'' . db_escape($m) . '\'
');
//printf("%06d: %s\n", $this->customer_id, $m);
		}
		public function msg($id, $m)
		{
			// Could use delayed here, but that'd force MyISAM on us as,
			// would really only be an issue for a client with a LOT of
			// customers (like 3k or 4k or more)
			global $DB;
			++log::$SEQUENCE;
			db_query(
'
INSERT INTO
	`customers_bills_log`
SET
	`when` = NOW(),
	`customer_id` = ' . $id . ',
	`period_id` = ' . $this->period_id . ',
	`sequence` = ' . log::$SEQUENCE . ',
	`what` = \'' . db_escape($m) . '\'
');
//printf("%06d: %s\n", $id, $m);
		}
		public function reset()
		{
			log::$SEQUENCE = 0;
		}
	}

	class Biller
	{
		private $log;

		private $customer_id = 0;
		private $period_id = 0;
		private $global_note = array();

		private $PERIOD;					// Cached period
		private $CUSTOMER;					// Cached customer record
		private $TYPES;						// Cached customer types

		const DATE = 0;						// Actual date of day
		const TYPE = 1;						// Active Type ID on this day
		const STATUS = 2;					// true = type says deliver, false = not
		const ACTUAL = 3;					// true = actually delivered, false not
		const DAYOFWEEK = 4;				// Day of week, starting at 0 = Sunday
		private $days = array();

		const DAILY = 0;					// Index of daily rate
		const SUNDAY = 1;					// Index of sunday rate
		private $rates = array();			// Array of rates by type (or only flag stop)

		private $inserts = array();			// Array of arrays of arguments for db_insert

		private $updates = array();			// Array of arrays of arguments for db_update

	//----------------------------------------------------------------------------------------------

		function __construct($note = NULL)
		{
			if (!is_null($note))
				$this->global_note = $note;
			else
				$this->global_note = get_config('billing-note', '');
			$this->log = new log;
		}

	//----------------------------------------------------------------------------------------------

		public function Combine($period_id)
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			$this->customer_id = 0;
			$this->log->customer_id(0);
			$this->period_id = $period_id;
			$this->log->period_id($period_id);

			// Cache the delivery types
			$this->TYPES = gen_typesArray($period_id);
			if ($err < ERR_SUCCESS)
			{
				log_error();
				return;
			}

			// Cache period info
			$this->PERIOD = gen_periodArray($period_id);
			if ($err < ERR_SUCCESS)
			{
				log_error();
				return;
			}

			// Get a list of combined main id's
			$query = 'SELECT DISTINCT(`customer_id_main`) AS `id` FROM `customers_combined_bills`';
			$specials = db_query($query);
			if (!$specials)
			{
				log_error();
				return;
			}

			// Cache minimum balance for export
			$billMinimum = get_config('billing-minimum', 0.01);

			// Iterate through and combine them
			while ($special = $specials->fetch_object())
			{
				$this->log->msg($special->id, 'Start combining bills.');

				try
				{
					// Get primary customer
					$primary = lup_customer($special->id);
					if ($err < ERR_SUCCESS)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}

					// Iterate through the secondary customers
					$query = "SELECT `customer_id_secondary` AS `id` FROM `customers_combined_bills`"
							. " WHERE `customer_id_main` = " . $primary->id;
					$secondaries = db_query($query);
					if (!$secondaries)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}

					// Get the primary bill
					$primaryBill = lup_c_bill($primary->id, $this->PERIOD[P_PERIOD]);
					if ($err < ERR_SUCCESS)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}

					// Track totals
					$rate = floatval(substr($primaryBill->rate, 1));
					$forward = floatval(substr($primaryBill->fwd, 1));
					$payments = floatval(substr($primaryBill->pmt, 1));
					$adjustments = floatval(substr($primaryBill->adj, 1));
					$balance = floatval(substr($primaryBill->bal, 1));
					$this->log->msg($primary->id, sprintf('Primary (id = %d) values '
							. '(rate $%01.2f, forward $%01.2f, payments $%01.2f, '
							. 'adjustments $%01.2f, balance $%01.2f).',
							$primary->id, $rate, $forward, $payments, $adjustments, $balance));
					while ($secondary = $secondaries->fetch_object())
					{
						$this->log->msg($primary->id,
								sprintf('Combining with secondary (id = %d).',$secondary->id));
						$this->log->msg($secondary->id,
								sprintf('Combining with main (id = %d).',$primary->id));

						// Get secondary customer
						$customer = lup_customer($secondary->id);
						if ($err < ERR_SUCCESS)
						{
							log_error();
							throw new Exception($errText, $errCode);
						}

						// If customer is disabled, no need to continue
						if ($customer->active == 'N')
						{
							$this->log->msg($customer->id, 'Customer disabled, skipping.');
							$this->log->msg($primary->id,
									sprintf('Secondary (id = %d) disabled, skipping.', $customer->id));
							continue;
						}
						if ($primary->active == 'N')
						{
							$this->log->msg($primary->id, 'Main disabled, secondary (id = '
									. sprintf('%06d', $customer->id) . ') enabled.');
							$this->log->msg($customer->id, 'Main (id = '
									. sprintf('%06d', $primary->id) . ') disabled.');
							continue;
						}

						// Get the bill
						$bill = lup_c_bill($customer->id, $this->PERIOD[P_PERIOD]);
						if ($err < ERR_SUCCESS)
						{
							log_error();
							throw new Exception($errText, $errCode);
						}

						// Add update to mark the combined bill as non-export
						$this->updates[] = array
							(
								'table' => 'customers_bills',
								'id' => null,
								'fields' => array
									(
										'cid' => "'" . $bill->cid . "'",
										'iid' => $bill->iid
									),
								'fields2' => array('export' => "'N'")
							);
						$this->log->msg($primary->id, 'Updated export of secondary (id = '
								. sprintf('%06d', $customer->id) . ') bill to N.');

						// Add seconday bill to values
						$rate += floatval(substr($bill->rate, 1));
						$forward += floatval(substr($bill->fwd, 1));
						$payments += floatval(substr($bill->pmt, 1));
						$adjustments += floatval(substr($bill->adj, 1));
						$balance += floatval(substr($bill->bal, 1));
						$this->log->msg($primary->id, sprintf('Added secondary (id = %d) to values '
								. '(rate $%01.2f, forward $%01.2f, payments $%01.2f, '
								. 'adjustments $%01.2f, balance $%01.2f).',
								$customer->id, floatval(substr($bill->rate, 1)), floatval(substr($bill->fwd, 1)),
								floatval(substr($bill->pmt, 1)), floatval(substr($bill->adj, 1)),
								floatval(substr($bill->bal, 1))));

						// Add update for secondary customer record
						$this->updates[] = array
							(
								'table' => 'customers',
								'id' => 'id',
								'fields' => array
									(
										'id' => $customer->id
									),
								'fields2' => array
									(
										'balance' => 0,
										'billBalance' => 0
									)
							);
						$this->log->msg($primary->id, 'Updated balance, billBalance of secondary (id = '
								. sprintf('%06d', $customer->id) . ') to $0.00.');
						$this->log->msg($customer->id, 'Updated balance, billBalance to $0.00.');
						$this->log->msg($customer->id,
								sprintf('Combined with main (id = %d) complete.',$primary->id));
					}

					// Add update for primary bill
					$this->updates[] = array
						(
							'table' => 'customers_bills',
							'id' => NULL,
							'fields' => array
								(
									'cid' => "'" . $primaryBill->cid . "'",
									'iid' => $primaryBill->iid
								),
							'fields2' => array
								(
									'rTit' => "'Combined Rate'",
									'rate' => "'" . db_escape(sprintf('$%01.2f', $rate)) . "'",
									'fwd' => "'" . db_escape(sprintf('$%01.2f', $forward)) . "'",
									'pmt' => "'" . db_escape(sprintf('$%01.2f', $payments)) . "'",
									'adj' => "'" . db_escape(sprintf('$%01.2f', $adjustments)) . "'",
									'bal' => "'" . db_escape(sprintf('$%01.2f', $balance)) . "'",
									'export' => "'" . ($balance >= $billMinimum ? 'Y' : 'N') . "'"
								)
						);
					$this->log->msg($primary->id, sprintf("Updated bill values\n"
							. "Forward = $%01.2f\nRate = $%01.2f\nPayments = $%01.2f\n"
							. "Adjustments = $%01.2f\nBalance = $%01.2f",
							$rate, $forward, $payments, $adjustments, $balance));

					// Add update for primary customer record
					$this->updates[] = array
						(
							'table' => 'customers',
							'id' => 'id',
							'fields' => array('id' => $primary->id),
							'fields2' => array
								(
									'balance' => $balance,
									'billBalance' => $balance
								)
						);
					$this->log->msg($primary->id, sprintf('Updated balance, billBalance to $%01.2f.', $balance));
					$this->log->msg($primary->id, 'Combining bills complete.');
				}
				catch (Exception $e)
				{
					if (isset($special) && isset($special->id))
					{
						$this->log->msg($special->id, $e->getMessage() . ' (Code ' . $e->getCode() . ')');
						$this->log->msg($special->id, 'Combining bills complete.');
					}
					else
					{
						error_log($e->getMessage() . ' (Code ' . $e->getCode() . ')');
						$this->log->msg(0, $e->getMessage() . ' (Code ' . $e->getCode() . ')');
						$this->log->msg(0, 'Combining bills complete.');
					}
				}
			}

			try
			{
				if (count($this->updates) > 0)
					$this->commit(false);
			}
			catch (Exception $e)
			{
				log_error();
			}

			return true;
		}

	//----------------------------------------------------------------------------------------------

		public function Generate($customer_id, $period_id)
		{
			global $err, $errCode, $errText;

			$this->customer_id = $customer_id;
			$this->period_id = $period_id;
			$this->log->customer_id($customer_id);
			$this->log->period_id($period_id);
			$this->log->Context(__CLASS__ . '::' . __FUNCTION__);
			$this->log->message('Start Bill Generation.');

			try
			{
				do
				{
					// Find out about this customer
					$this->CUSTOMER = lup_customer($customer_id);
					if ($err < ERR_SUCCESS)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}

					// If customer is inactive, skip them
					if ($this->CUSTOMER->active == 'N')
					{
						$this->log->message('Customer Inactive.');
						break;
					}

					// Cache the delivery types
					$this->TYPES = gen_typesArray($period_id);
					if ($err < ERR_SUCCESS)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}

					// Cache period info
					$this->PERIOD = gen_periodArray($period_id);
					if ($err < ERR_SUCCESS)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}

					// Deal with customer appropriately
					$fs = get_config('flag-stop-type');
					if ($fs != CFG_NONE && $this->CUSTOMER->type_id == $fs)
					{
						$this->fs_generate();
						break;
					}
					else
					{
						// Build array of days
						$this->initialize_days();

						// Add in type changes
						$this->add_service_type();

						// Add in starts and stops
						$this->add_service();

						// Determine the correct credits per paper
						$this->calculate_rates();

						// Generate type change adjustments
						$this->generate_service_type();

						// Generate start and stop adjustments
						$this->generate_service();

						// Generate complaint adjustments
						$this->generate_complaints();

						// Generate bill
						$this->generate_bill();
					}
				} while (false);

				// Update the database
				$this->commit();
			}
			catch (Exception $e)
			{
				$this->log->message($e->getMessage() . ' (Code ' . $e->getCode() . ')');
				$this->log->message('BILL GENERATION FAILED!');
			}

			$this->log->message('Bill Generation Complete.');

			// Reset object for reuse
			$this->reset();
		}

	//----------------------------------------------------------------------------------------------

		private function add_service()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Locate all the starts and stops this period
			$query =
"
SELECT
 *
FROM
 `customers_service`
WHERE
 `period_id` <=> NULL
 AND `customer_id` = " . $this->CUSTOMER->id . "
 AND `when` BETWEEN '" . strftime('%Y-%m-%d', $this->PERIOD[P_START])
		. "' AND '" . strftime('%Y-%m-%d', $this->PERIOD[P_END]) . "'
 AND `ignoreOnBill` = 'N'
ORDER BY
 `when` ASC, `created` ASC, `updated` ASC
";
			$records = db_query($query);
			if (!$records)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			// Update day array
			$type = ($this->CUSTOMER->billStopped == 'Y' ? SERVICE_START : SERVICE_STOP);
			while ($record = $records->fetch_object())
			{
				$this->log->message('Located ' . $record->type . ' for '
						. strftime('%Y/%m/%d', strtotime($record->when)) . ' (id = '
						. sprintf('%08d', $record->id) . ').');

				// Bail if this is not the expected type
				if ($type != $record->type)
				{
					throw new Exception('Encountered a ' . $record->type .
							' when expecting ' . $type . '.', ERR_FAILURE);
				}

				// Locate effective day
				$when = strtotime($record->when);
				reset($this->days);
				$found = false;
				foreach($this->days as $day => $state)
				{
					if ($when < $state[Biller::DATE])
					{
						$found = true;
						break;
					}
				}
				if ($found)
					--$day;

				// Mark from effective day until the end of the period
				// as not delivered
				$count = count($this->days);
				for ($i = $day; $i < $count; ++$i)
				{
					if ($type == SERVICE_START)
						$this->days[$i][Biller::ACTUAL] = true;
					else
						$this->days[$i][Biller::ACTUAL] = false;
				}

				// Add this record to update list
				$this->updates[] = array
					(
						'table' => 'customers_service',
						'id' => 'id',
						'fields' => array('id' => $record->id),
						'fields2' => array('period_id' => $this->PERIOD[P_PERIOD])
					);
				$this->log->message('Updated period_id of ' . $record->type . ' for '
						. strftime('%Y/%m/%d', strtotime($record->when)) . ' (id = '
						. sprintf('%08d', $record->id) . ') to ' . $this->PERIOD[P_TITLE]
						. ' (id = ' . sprintf('%04d', $this->PERIOD[P_PERIOD]) . ').');

				// Siwtch what should come next
				if ($type == SERVICE_START)
					$type = SERVICE_STOP;
				else
					$type = SERVICE_START;
			}
		}

	//----------------------------------------------------------------------------------------------

		private function add_service_type()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Locate all type changes this period
			$query =
"
SELECT
 *
FROM
 `customers_service_types`
WHERE
 `period_id` <=> NULL
 AND `customer_id` = " . $this->CUSTOMER->id . "
 AND `when` BETWEEN '" . strftime('%Y-%m-%d', $this->PERIOD[P_START])
		. "' AND '" . strftime('%Y-%m-%d', $this->PERIOD[P_END]) . "'
 AND `ignoreOnBill` = 'N'
ORDER BY
 `when` ASC, `created` ASC, `updated` ASC
";
			$types = db_query($query);
			if (!$types)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			// Update day array
			while ($type = $types->fetch_object())
			{
				$when = strtotime($type->when);
				$this->log->message('Located change from ' . $this->TYPES[$type->type_id_from]['abbr']
						. ' to ' . $this->TYPES[$type->type_id_to]['abbr'] . ' on '
						. strftime('%Y/%m/%d', $when) . ' (id = ' . sprintf('%08d', $type->id) . ').');

				// Locate effective date
				reset($this->days);
				foreach($this->days as $day => $state)
				{
					if ($when < $state[Biller::DATE])
						break;
				}
				--$day;

				// Mark from effective date until the end of the period
				// as the new type and update delivery status
				$count = count($this->days);
				for ($i = $day; $i < $count; ++$i)
				{
					$this->days[$i][Biller::TYPE] = $type->type_id_to;
					$this->days[$i][Biller::STATUS] = $this->TYPES[$type->type_id_to][
							date('D', $this->days[$i][Biller::DATE])]['paper'];
				}

				// Add this record to update list
				$this->updates[] = array
					(
						'table' => 'customers_service_types',
						'id' => 'id',
						'fields' => array('id' => $type->id),
						'fields2' => array('period_id' => $this->PERIOD[P_PERIOD])
					);
				$this->log->message('Updated period_id of change from '
						. $this->TYPES[$type->type_id_from]['abbr'] . ' to '
						. $this->TYPES[$type->type_id_to]['abbr'] . ' on '
						. strftime('%Y/%m/%d', $when) . ' (id = ' . sprintf('%08d', $type->id)
						. ') to ' . $this->PERIOD[P_TITLE] . ' (id = '
						. sprintf('%04d', $this->PERIOD[P_PERIOD]) . ').');
			}
		}

	//----------------------------------------------------------------------------------------------

		private function calculate_rates()
		{
			global $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// What type of billing is active?
			$CBT = get_config('customer-billing-type');
			if ($CBT == CFG_NONE)
				$CBT = 'auto';

			// Old style is quick lookups, so do it and get out
			if ($CBT != 'auto')
			{
				// Determine the rates this customer uses
				$types = array();	// types in period for this customer
				reset($this->days);
				foreach($this->days as $d => $v)
				{
					if (!in_array($v[Biller::TYPE], $types))
						$types[] = $v[Biller::TYPE];
				}

				// Build the rates array
				reset($types);
				foreach($types as $t)
				{
					$this->rates[$t] = array
						(
							Biller::SUNDAY => $this->TYPES[$t]['sunday_credit'],
							Biller::DAILY => $this->TYPES[$t]['daily_credit']
						);
				}

				return;
			}

			// Determine number of types, and number of each week day,
			// in current period
			$types = array();	// types in period for this customer
			$weekdays = array	// number of each type of day in this period, generically
				(
					0 => 0,
					1 => 0,
					2 => 0,
					3 => 0,
					4 => 0,
					5 => 0,
					6 => 0
				);
			reset($this->days);
			foreach($this->days as $d => $v)
			{
				if (!in_array($v[Biller::TYPE], $types))
					$types[] = $v[Biller::TYPE];
				++$weekdays[$v[Biller::DAYOFWEEK]];
			}

			// Determine expected number of daily and sunday
			// deliveries for each type
			$counts = array();
			reset($this->days);
			foreach($types as $t)
			{
				$counts[$t] = array
					(
						Biller::SUNDAY => ($this->TYPES[$t]['Sun']['paper'] ? $weekdays[0] : 0),
						Biller::DAILY => (($this->TYPES[$t]['Mon']['paper'] ? $weekdays[1] : 0)
								+ ($this->TYPES[$t]['Tue']['paper'] ? $weekdays[2] : 0)
								+ ($this->TYPES[$t]['Wed']['paper'] ? $weekdays[3] : 0)
								+ ($this->TYPES[$t]['Thu']['paper'] ? $weekdays[4] : 0)
								+ ($this->TYPES[$t]['Fri']['paper'] ? $weekdays[5] : 0)
								+ ($this->TYPES[$t]['Sat']['paper'] ? $weekdays[6] : 0))
					);
			}

			// Get the costs
			$dailyCost = floatval(get_config('customers-daily-only-cost', 0));
			$sundayCost = floatval(get_config('customers-sunday-only-cost', 0));
			$dailyCostSingle = floatval(get_config('customers-daily-single-cost', 0));
			$sundayCostSingle = floatval(get_config('customers-sunday-single-cost', 0));

			// Calculate the rates
			foreach($counts as $t => $c)
			{
				switch ($this->CUSTOMER->rateType)
				{
				case RATE_STANDARD:
					$A = $this->TYPES[$t]['rate'];																// Rate for period for type
					break;

				case RATE_REPLACE:
					$A = $this->CUSTOMER->rateOverride;															// Rate for period for type
					break;

				case RATE_SURCHARGE:
					$A = $this->TYPES[$t]['rate']																// Rate for period for type
							+ $this->CUSTOMER->rateOverride;
					break;
				}
				$B = $dailyCost;																				// Daily Home Delivery Cost
				$C = $sundayCost;																				// Sunday Home Delivery Cost
				$D = $dailyCostSingle;																			// Daily Wholesale Cost
				$F = $weekdays[1] + $weekdays[2] + $weekdays[3] + $weekdays[4] + $weekdays[5] + $weekdays[6];	// # of daily days
				$G = $weekdays[0];																				// # of sunday days
				$H = $c[Biller::DAILY];																			// # of daily days for type
				$I = $c[Biller::SUNDAY];																		// # of sunday days for type
				$Z = round(floatval($dailyCost / $dailyCostSingle), 0);											// Max D before B cheaper
				$Y = ($Z > $H ? ($D * $H) : $B);																// Daily Cost for period
				$W = ($H == 0 ? 0 : ($I == 0 ? 1 : $Y / ($C + $Y)));											// % Daily is of A
				$V = ($I == 0 ? 0 : ($H == 0 ? 1 : 1 - $W));													// % Sunday is of A
				$U = $W * $A;																					// Daily Amount of A
				$T = $A - $U;																					// Sunday Amount of A
				$S = ($H > 0 ? $U / $H : 0);																	// per Daily of A
				$R = ($I > 0 ? $T / $I : 0);																	// per Sunday of A
/*
				printf("%40s: $%01.2f\n", 'Rate for period for type', $A);
				printf("%40s: $%01.2f\n", 'Daily Home Delivery Cost', $B);
				printf("%40s: $%01.2f\n", 'Sunday Home Delivery Cost', $C);
				printf("%40s: $%01.4f\n", 'Daily Wholesale Cost', $D);
				printf("%40s: %d\n", '# of daily days', $F);
				printf("%40s: %d\n", '# of sunday days', $G);
				printf("%40s: %d\n", '# of daily days for type', $H);
				printf("%40s: %d\n", '# of sunday days for type', $I);
				printf("%40s: %d\n", 'Max D before B cheaper', $Z);
				printf("%40s: $%01.2f\n", 'Daily Cost for period', $Y);
				printf("%40s: %01.1f%%\n", '% Daily is of A', round($W * 100, 1));
				printf("%40s: %01.1f%%\n", '% Sunday is of A', round($V * 100, 1));
				printf("%40s: $%01.2f\n", 'Daily Amount of A', round($U, 2));
				printf("%40s: $%01.2f\n", 'Sunday Amount of A', round($T, 2));
				printf("%40s: $%01.4f\n", 'per Daily of A', round($S, 4));
				printf("%40s: $%01.4f\n", 'per Sunday of A', round($R, 4));
*/
				$this->rates[$t] = array
					(
						Biller::SUNDAY => $R,
						Biller::DAILY => $S
					);
			}
		}

	//----------------------------------------------------------------------------------------------

		private function commit($log = true)
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			$result = db_query(SQL_TRANSACTION);
			if (!$result)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			try
			{
				// Perform inserts
				reset($this->inserts);
				foreach($this->inserts as $insert)
				{
					$result = db_insert($insert['table'], $insert['fields']);
					if (!$result)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}
				}

				// Perform updates
				reset($this->updates);
				foreach($this->updates as $update)
				{
					$result = db_update($update['table'], $update['fields'], $update['fields2']);
					if (!$result)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}
				}

				// Commit
				db_query('COMMIT');
				if ($log)
					$this->log->message('Database changes commited.');
			}
			catch (Exception $e)
			{
				// Undo everything
				db_query('ROLLBACK');
				if ($log)
					$this->log->message('DATABASE CHANGES REVERTED.');

				// Pass exception on
				throw $e;
			}
		}

	//----------------------------------------------------------------------------------------------

		private function fs_generate()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Validate Flag Stop variables
			$rate = get_config('flag-stop-daily-rate');
			if ($rate == CFG_NONE)
				throw new Exception('Flag Stop - Daily Rate Not Set.', ERR_INVALID);
			$this->rates[Biller::DAILY] = $rate;
			$rate = get_config('flag-stop-sunday-rate');
			if ($rate == CFG_NONE)
				throw new Exception('Flag Stop - Sunday Rate Not Set.', ERR_INVALID);
			$this->rates[Biller::SUNDAY] = $rate;

			// Log warning if either rate is 0 or negative
			if ($this->rates[Biller::DAILY] <= 0)
			{
				$this->log->message('Flag Stop - Daily rate is strange: $'
						. $sprintf('%01.2f', $this->rates[Biller::DAILY]) . '.');
			}
			if ($this->rates[Biller::SUNDAY] <= 0)
			{
				$this->log->message('Flag Stop - Daily rate is strange: $'
						. $sprintf('%01.2f', $this->rates[Biller::SUNDAY]) . '.');
			}

			// Generate adjustments
			$this->fs_validate_service_type();

			// Generate adjustments
			$this->fs_generate_service();

			// Generate bill
			$this->generate_bill();
		}

	//----------------------------------------------------------------------------------------------

		private function fs_generate_service()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Locate all the starts and stops this period
			$query =
"
SELECT
 *
FROM
 `customers_service`
WHERE
 `period_id` <=> NULL
 AND `customer_id` = " . $this->CUSTOMER->id . "
 AND `when` BETWEEN '" . strftime('%Y-%m-%d', $this->PERIOD[P_START])
		. "' AND '" . strftime('%Y-%m-%d', $this->PERIOD[P_END]) . "'
 AND `ignoreOnBill` = 'N'
ORDER BY
 `when` ASC, `created` ASC, `updated` ASC
";
			$records = db_query($query);
			if (!$records)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			// Build array of starts and stops
			$service = array();
			$type = ($this->CUSTOMER->billStopped == 'Y' ? SERVICE_START : SERVICE_STOP);
			while ($record = $records->fetch_object())
			{
				$this->log->message('Located ' . $record->type . ' for '
						. strftime('%Y/%m/%d', strtotime($record->when)) . ' (id = '
						. sprintf('%08d', $record->id) . ').');

				// Bail if this is not the expected type
				if ($type != $record->type)
				{
					throw new Exception('Encountered a ' . $record->type .
							' when expecting ' . $type . '.', ERR_FAILURE);
				}

				// Save in array
				$when = strtotime($record->when);
				$service[] = array
					(
						'type' => $type,
						'when' => strtotime($record->when)
					);

				// Add this record to update list
				$this->updates[] = array
					(
						'table' => 'customers_service',
						'id' => 'id',
						'fields' => array('id' => $record->id),
						'fields2' => array('period_id' => $this->PERIOD[P_PERIOD])
					);
				$this->log->message('Updated period_id of ' . $record->type . ' for '
						. strftime('%Y/%m/%d', strtotime($record->when)) . ' (id = '
						. sprintf('%08d', $record->id) . ') to ' . $this->PERIOD[P_TITLE]
						. ' (id = ' . sprintf('%04d', $this->PERIOD[P_PERIOD]) . ').');

				// Siwtch what should come next
				if ($type == SERVICE_START)
					$type = SERVICE_STOP;
				else
					$type = SERVICE_START;
			}
			$records->close();

			// Generate adjustments
			$type = ($this->CUSTOMER->billStopped == 'Y' ? SERVICE_START : SERVICE_STOP);
			if ($type == SERVICE_STOP)
				$curDate = $this->PERIOD[P_START];
			foreach($service as $parms)
			{
				if ($type == SERVICE_START)
				{
					$curDate = $parms['when'];
					$type = SERVICE_STOP;
				}
				else
				{
					// Add adjustment
					$this->fs_generate_service_adjustment($curDate, strtotime('-1 day', $parms['when']));

					// Now expecting start
					$type = SERVICE_START;
				}
			}

			// Add final adjustment if needed
			if ($type == SERVICE_STOP)
				$this->fs_generate_service_adjustment($curDate, $this->PERIOD[P_END]);

			// Update billStopped if needed
			$billStopped = ($type == SERVICE_START ? 'Y' : 'N');
			if ($billStopped != $this->CUSTOMER->billStopped)
			{
				$this->updates[] = array
					(
						'table' => 'customers',
						'id' => 'id',
						'fields' => array('id' => $this->CUSTOMER->id),
						'fields2' => array('billStopped' => "'" . $billStopped . "'")
					);
				$this->log->message('Updated billStopped to '
						. ($billStopped == 'N' ? 'FALSE' : 'TRUE'));
			}
		}

	//----------------------------------------------------------------------------------------------

		private function fs_generate_service_adjustment($begin, $end)
		{
			global $DB;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Determine number of daily's and sunday's
			$daily = 0;
			$sunday = 0;
			$date = $begin;
			while ($date <= $end)
			{
				if (date('w', $date) == 0)
					++$sunday;
				else
					++$daily;
				$temp = $date;
				$date = strtotime('+1 day', $temp);
			}

			// Add adjustment
			$desc = 'Delivered ' . strftime('%m/%d/%y', $begin);
			if ($begin != $end)
				$desc .= ' - ' . strftime('%m/%d/%y', $end);
			$note = 'Charged for ' . $daily . ' daily, and ' . $sunday . ' sunday.';
			$amount = ($daily * $this->rates[Biller::DAILY])
					+ ($sunday * $this->rates[Biller::SUNDAY]);
			$this->inserts[] = array
				(
					'table' => 'customers_adjustments',
					'fields' => array
						(
							'customer_id' => $this->CUSTOMER->id,
							'period_id' => 'NULL',
							'created' => 'NOW()',
							'updated' => 'NOW()',
							'desc' => "'" . db_escape($desc) . "'",
							'amount' => $amount,
							'note' => "'" . db_escape($note). "'"
						)
				);
			$this->log->message('Added adjustment of $' . sprintf('%01.2f', $amount) . ' for ' . $desc);
		}

	//----------------------------------------------------------------------------------------------

		private function fs_validate_service_type()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Locate all type changes this period
			$query =
"
SELECT
 *
FROM
 `customers_service_types`
WHERE
 `period_id` <=> NULL
 AND `customer_id` = " . $this->CUSTOMER->id . "
 AND `when` BETWEEN '" . strftime('%Y-%m-%d', $this->PERIOD[P_START])
		. "' AND '" . strftime('%Y-%m-%d', $this->PERIOD[P_END]) . "'
 AND `ignoreOnBill` = 'N'
ORDER BY
 `when` ASC, `created` ASC, `updated` ASC
";
			$records = db_query($query);
			if (!$records)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			// Make sure any type changes occurred at the start of the period
			while ($record = $records->fetch_object())
			{
				if (strtotime($record->when) > $this->PERIOD[P_START])
					throw new Exception(sprintf('Type change (id = %08d) doesn\'t '
							. 'occur at start of perioid.', $record->id));
				++$count;
			}
		}

	//----------------------------------------------------------------------------------------------

		private function generate_bill()
		{
			global $DB, $err, $errCode, $errText, $Routes;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			populate_routes();
			if ($err < ERR_SUCCESS)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			// Cache special types
			$fs = get_config('flag-stop-type', -1);

			// Determine rate
			$rate = 0;
			$gotRate = false;
			if ($this->CUSTOMER->type_id == $fs)
			{
				$dts = $this->PERIOD[P_DSTART];
				$dte = $this->PERIOD[P_DEND];
				$due = $this->PERIOD[P_DUE];

				// Update customer record
				$this->updates[] = array
					(
						'table' => 'customers',
						'id' => 'id',
						'fields' => array('id' => $this->CUSTOMER->id),
						'fields2' => array
							(
								'billPeriod' => $this->PERIOD[P_PERIOD] + 1,
								'billStart' => '\'' . strftime('%Y-%m-%d', $dts) . '\'',
								'billEnd' => '\'' . strftime('%Y-%m-%d', $dte) . '\'',
								'billDue' => '\'' . strftime('%Y-%m-%d', $due) . '\''
							)
					);
			}
            else if (is_null($this->CUSTOMER->billPeriod)
                    || $this->PERIOD[P_PERIOD] == $this->CUSTOMER->billPeriod)
			{
				// Get base rate for customer
				$gotRate = true;
				switch ($this->CUSTOMER->rateType)
				{
				case RATE_STANDARD:
					$end = end($this->days);
					$rate = $this->TYPES[$end[Biller::TYPE]]['rate'];
					break;

				case RATE_REPLACE:
					$rate = $this->CUSTOMER->rateOverride;
					break;

				case RATE_SURCHARGE:
					$end = end($this->days);
					$rate = $this->TYPES[$end[Biller::TYPE]]['rate']
							+ $this->CUSTOMER->rateOverride;
					break;
				}

				// Adjust for multiple periods
				$rate *= $this->CUSTOMER->billCount;
				if ($this->CUSTOMER->billCount > 1)
					$this->log->message('Billing for ' . $this->CUSTOMER->billCount . ' periods.');

				// Adjust for multiple deliveries
				$rate *= $this->CUSTOMER->billQuantity;
				if ($this->CUSTOMER->billQuantity > 1)
					$this->log->message('Billing for ' . $this->CUSTOMER->billQuantity . ' deliveries.');

				// Determine start date
				$dts = $this->PERIOD[P_DSTART];

				// Determine end date
				if ($this->CUSTOMER->billCount > 1)
				{
					$temp = lup_period($this->PERIOD[P_PERIOD] + $this->CUSTOMER->billCount - 1);
					if ($err < ERR_SUCCESS)
					{
						log_error();
						throw new Exception($errText, $errCode);
					}
					$dte = strtotime($temp->display_end);
				}
				else
					$dte = $this->PERIOD[P_DEND];

				// Determine due date
				$due = $this->PERIOD[P_DUE];

				// Update customer record
				$this->updates[] = array
					(
						'table' => 'customers',
						'id' => 'id',
						'fields' => array('id' => $this->CUSTOMER->id),
						'fields2' => array
							(
								'billPeriod' => $this->PERIOD[P_PERIOD] + $this->CUSTOMER->billCount,
								'billStart' => '\'' . strftime('%Y-%m-%d', $dts) . '\'',
								'billEnd' => '\'' . strftime('%Y-%m-%d', $dte) . '\'',
								'billDue' => '\'' . strftime('%Y-%m-%d', $due) . '\''
							)
					);
			}

			// Make sure rate is valid at this point
			if (empty($rate) && !$gotRate && $this->CUSTOMER->type_id != $fs)
				throw new Exception('Unable to determine rate', ERR_FAILURE);

			// Make sure we have dates
            if (!isset($dts) || !isset($dte) || !isset($due))
                throw new Excepction('INTERNAL ERROR: Start/End/Due dates not set!');

			// Total the adjustments
			$adjustments = $this->sum_adjustments();

			// Total payments
			$payments = $this->sum_payments();

			// Figure new balance
			$balance = $this->CUSTOMER->billBalance + -$payments + $rate + $adjustments;

			// Update balance
			$this->updates[] = array
				(
					'table' => 'customers',
					'id' => 'id',
					'fields' => array('id' => $this->CUSTOMER->id),
					'fields2' => array
						(
							'balance' => $balance,
							'billBalance' => $balance
						)
				);
			$this->log->message('Updated balance, billBalance to $' . sprintf('%01.2f', $balance) . '.');

			// Determine export
			if ($fs == CFG_NONE)
				$billMinimum = get_config('billing-minimum', 0.01);
			else
				$billMinimum = get_config('flag-stop-billing-minimum', 0.01);
			if ($this->CUSTOMER->active == 'Y' && $balance >= $billMinimum)
				$export = 'Y';
			else
				$export = 'N';

			// Generate name
			$dNm = valid_name($this->CUSTOMER->firstName, $this->CUSTOMER->lastName);

			// Get bill name and addres
			$temp = bill_address($this->CUSTOMER->id);
			if (!$temp)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}
			list($bNm, $bAd1, $bAd2, $bAd3, $bAd4) = $temp;

			// Get note
			$note = bill_note($this->CUSTOMER->id, $this->CUSTOMER->billNote);
			if (!$note)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}
			if (!empty($this->CUSTOMER->billNote))
			{
				// Update billing note
				$this->updates[] = array
					(
						'table' => 'customers',
						'id' => 'id',
						'fields' => array('id' => $this->CUSTOMER->id),
						'fields2' => array('billNote' => "''")
					);
				$this->log->message('Cleared billing note.');
			}

			// Get rate title
			$rTit = bill_rate_title($this->CUSTOMER->type_id);
			if (!$rTit)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			// Get the client info
			$cnm = get_config('client-name', '');
			$cad1 = get_config('client-address-1', '');
			$cad2 = get_config('client-address-2', '');
			$ctel = 'Phone: ' . get_config('client-telephone', '');

			// Insert the bill
			// BUGBUG:  MySQL 5.1.x will FAIL if a value is longer than field length
			// 5.0.x silently disregarded it
			$this->inserts[] = array
				(
					'table' => 'customers_bills',
					'fields' => array
						(
							'cid' => "'" . db_escape(sprintf("%06d", $this->CUSTOMER->id)) . "'",
							'iid' => $this->PERIOD[P_PERIOD],
							'rateType' => '\'' . $this->CUSTOMER->rateType . '\'',
							'rateOverride' => $this->CUSTOMER->rateOverride,
							'created' => 'NOW()',
							'export' => "'" . $export . "'",
							'cnm' => "'" . db_escape($cnm) . "'",
							'cad1' => "'" . db_escape($cad1) . "'",
							'cad2' => "'" . db_escape($cad2) . "'",
							'ctel' => "'" . db_escape($ctel) . "'",
							'rt' => "'" . db_escape($Routes[$this->CUSTOMER->route_id]) . "'",
							'dNm' => "'" . db_escape(substr($dNm, 0, 20)) . "'",
							'dAd' => "'" . db_escape(substr($this->CUSTOMER->address, 0, 20)) . "'",
							'dCt' => "'" . db_escape($this->CUSTOMER->city) . "'",
							'dSt' => "'" . db_escape(strtoupper($this->CUSTOMER->state)) . "'",
							'dZp' => "'" . db_escape(substr($this->CUSTOMER->zip, 0, 5)) . "'",
							'bNm' => "'" . db_escape(strtoupper($bNm)) . "'",
							'bAd1' => "'" . db_escape(strtoupper($bAd1)) . "'",
							'bAd2' => "'" . db_escape(strtoupper($bAd2)) . "'",
							'bAd3' => "'" . db_escape(strtoupper($bAd3)) . "'",
							'bAd4' => "'" . db_escape(strtoupper($bAd4)) . "'",
							'rTit' => "'" . db_escape($rTit) . "'",
							'rate' => "'" . db_escape(sprintf("$%01.2f", $rate)) . "'",
							'fwd' => "'" . db_escape(sprintf("$%01.2f", $this->CUSTOMER->billBalance)) . "'",
							'pmt' => "'" . db_escape(sprintf("$%01.2f", $payments)) . "'",
							'adj' => "'" . db_escape(sprintf("$%01.2f", $adjustments)) . "'",
							'bal' => "'" . db_escape(sprintf("$%01.2f", $balance)) . "'",
							'due' => "'" . db_escape(strftime('%m/%d/%Y', $due)) . "'",
							'dts' => "'" . db_escape(strftime('%m/%d/%Y', $dts)) . "'",
							'dte' => "'" . db_escape(strftime('%m/%d/%Y', $dte)) . "'",
							'nt1' => "'" . db_escape($note[0]) . "'",
							'nt2' => "'" . db_escape($note[1]) . "'",
							'nt3' => "'" . db_escape($note[2]) . "'",
							'nt4' => "'" . db_escape($note[3]) . "'"
						)
				);
			$this->log->message("Created bill.\n"
					. 'Forward:     $' . sprintf('%01.2f', $this->CUSTOMER->billBalance) . "\n"
					. 'Rate:        $' . sprintf('%01.2f', $rate) . "\n"
					. 'Payments:    $' . sprintf('%01.2f', $payments) . "\n"
					. 'Adjustments: $' . sprintf('%01.2f', $adjustments) . "\n"
					. 'Balance:     $' . sprintf('%01.2f', $balance));
		}

	//----------------------------------------------------------------------------------------------

		private function generate_complaints()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Locate all the starts and stops this period
			$query =
"
SELECT
 *
FROM
 `customers_complaints`
WHERE
 `period_id` <=> NULL
 AND `customer_id` = " . $this->CUSTOMER->id . "
 AND `when` BETWEEN '" . strftime('%Y-%m-%d', $this->PERIOD[P_START])
		. "' AND '" . strftime('%Y-%m-%d', $this->PERIOD[P_END]) . "'
 AND `ignoreOnBill` = 'N'
ORDER BY
 `when` ASC, `created` ASC, `updated` ASC
";
			$records = db_query($query);
			if (!$records)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			// Add adjustments for the complaints
			while ($record = $records->fetch_object())
			{
				$this->log->message('Located ' . $record->type . ' on '
						. strftime('%Y/%m/%d', strtotime($record->when)) . ' (id = '
						. sprintf('%08d', $record->id) . ').');

				// Determine what needs to be done
				$amount = 0;
				$adjustment = false;
				switch ($record->result)
				{
				case RESULT_NOTHING:
				case RESULT_REDELIVERED:
					break;

				case RESULT_CREDIT1DAILY:
					$end = end($this->days);
					$adjustment = true;
					$amount = -round($this->rates[$end[Biller::TYPE]][Biller::DAILY], 2);
					break;

				case RESULT_CREDIT1SUNDAY:
					$end = end($this->days);
					$adjustment = true;
					$amount = -round($this->rates[$end[Biller::TYPE]][Biller::SUNDAY], 2);
					break;

				case RESULT_CREDIT:
				case RESULT_CHARGE:
					$adjustment = true;
					$amount = $record->amount;
					break;

				default:
					throw new Exception('Unknown result for complaint '
							. sprintf('%08d', $record->id), ERR_INVALID);
				}

				// Add adjustment for complaint if needed
				if ($adjustment)
				{
					// Determine description
					switch ($record->type)
					{
					case BITCH_MISSED:
						$desc = 'Missed on ' . strftime('%m/%d/%y', strtotime($record->when));
						break;

					case BITCH_WET:
						$desc = 'Wet paper on ' . strftime('%m/%d/%y', strtotime($record->when));
						break;

					case BITCH_DAMAGED:
						$desc = 'Damaged paper on ' . strftime('%m/%d/%y', strtotime($record->when));
						break;

					default:
						throw new Exception('Unknown type for complaint '
								. sprintf('%08d', $record->id), ERR_INVALID);
					}

					$end = end($this->days);
					$note = 'Daily rate was ' . sprintf('$%01.4f', round($this->rates[$end[Biller::TYPE]][Biller::DAILY], 4))
							. ".\nSunday rate was " . sprintf('$%01.4f', round($this->rates[$end[Biller::TYPE]][Biller::SUNDAY], 4))
							. ".\n";
					$this->inserts[] = array
						(
							'table' => 'customers_adjustments',
							'fields' => array
								(
									'customer_id' => $this->CUSTOMER->id,
									'period_id' => 'NULL',
									'created' => 'NOW()',
									'updated' => 'NOW()',
									'desc' => "'" . db_escape($desc) . "'",
									'amount' => $amount,
									'note' => "'" . db_escape($note). "'"
								)
						);
					$this->log->message('Added adjustment of $' . $amount . ' for ' . $desc);
				}

				// Add this record to update list
				$this->updates[] = array
					(
						'table' => 'customers_complaints',
						'id' => 'id',
						'fields' => array('id' => $record->id),
						'fields2' => array('period_id' => $this->PERIOD[P_PERIOD])
					);
				$this->log->message('Updated period_id of ' . $record->type . ' for '
						. strftime('%Y/%m/%d', strtotime($record->when)) . ' (id = '
						. sprintf('%08d', $record->id) . ') to ' . $this->PERIOD[P_TITLE]
						. ' (id = ' . sprintf('%04d', $this->PERIOD[P_PERIOD]) . ').');
			}
		}

	//----------------------------------------------------------------------------------------------

		private function generate_service()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			// Walk the days and see if there are days where a transition
			// has taken place
			$curDay = 0;
			$type = ($this->CUSTOMER->billStopped == 'Y' ? SERVICE_START : SERVICE_STOP);
			reset($this->days);
			$stopped = -1;
			$started = -1;
			foreach($this->days as $day => $data)
			{

				// If expected and actual don't agree, then a transition
				// took place on this date
				if ($type == SERVICE_START && $data[Biller::ACTUAL])
				{
					// Track first started day if needed
					if ($started < 0)
						$started = $day;

					// If it occured on first day of period, then treat it like
					// it had already been started
					if ($day == 0)
					{
						$type = SERVICE_STOP;
						continue;
					}

					// Count days not delivered
					$daily = 0;
					$sunday = 0;
					for ($i = $curDay; $i < $day; ++$i)
					{
						$weekday = $this->days[$i][Biller::DAYOFWEEK];
						if ($this->days[$i][Biller::STATUS])
						{
							if ($weekday == 0)
								++$sunday;
							else
								++$daily;
						}
					}

					// Generate credit adjustment if needed
					if ($daily > 0 || $sunday > 0)
					{
						$this->generate_service_adjustment($data[Biller::TYPE], $daily, $sunday,
								$this->days[$curDay][Biller::DATE], $this->days[$started - 1][Biller::DATE]);
					}

					// Now looking for a stop transition
					$type = SERVICE_STOP;
					$curDay = $day;
					$stopped = -1;
					$started = $day;
				}
				else if ($type == SERVICE_STOP && !$data[Biller::ACTUAL])
				{
					if ($stopped < 0)
						$stopped = $day;
					$curDay = $stopped;
					$type = SERVICE_START;
					$started = -1;
				}
			}

			// Add final credit if needed
			if ($type == SERVICE_START)
			{
				// Determine when first delivery should have taken place
				for ($i = 0; $i < count($this->days); ++$i)
				{
					if ($this->days[$i][Biller::STATUS])
						break;
				}

				// If customer stopped on or before first delivery, credit entire period
				if ($curDay <= $i)
				{
					$end = end($this->days);
					$desc = 'Customer Stop ' . strftime('%m/%d/%y', $this->days[$i][Biller::DATE])
							. ' - ' . strftime('%m/%d/%y', $this->PERIOD[P_END]);
					switch ($this->CUSTOMER->rateType)
					{
					case RATE_STANDARD:
						$amount = -$this->TYPES[$end[Biller::TYPE]]['rate'];
						break;

					case RATE_REPLACE:
						$amount = $this->CUSTOMER->rateOverride;
						break;

					case RATE_SURCHARGE:
						$amount = -$this->TYPES[$end[Biller::TYPE]]['rate']
								+ $this->CUSTOMER->rateOverride;
						break;
					}
					$this->inserts[] = array
						(
							'table' => 'customers_adjustments',
							'fields' => array
								(
									'customer_id' => $this->CUSTOMER->id,
									'period_id' => 'NULL',
									'created' => 'NOW()',
									'updated' => 'NOW()',
									'desc' => "'" . db_escape($desc) . "'",
									'amount' => $amount,
									'note' => "'Credited entire period.'"
								)
						);
					$this->log->message('Added adjustment of $' . $amount . ' for ' . $desc);
				}
				else
				{
					// Otherwise credit through end of period
					$daily = 0;
					$sunday = 0;
					$count = count($this->days);
					for ($i = $curDay; $i < $count; ++$i)
					{
						$weekday = $this->days[$i][Biller::DAYOFWEEK];
						if ($this->days[Biller::STATUS])
						{
							if ($weekday == 0)
								++$sunday;
							else
								++$daily;
						}
					}

					// Generate credit adjustment
					$this->generate_service_adjustment($data[Biller::TYPE], $daily, $sunday,
							$this->days[$curDay][Biller::DATE], $this->PERIOD[P_END]);
				}
			}

			// Generate update for customer->billStopped
			$billStopped = ($type == SERVICE_START ? 'Y' : 'N');
			if ($billStopped != $this->CUSTOMER->billStopped)
			{
				$this->updates[] = array
					(
						'table' => 'customers',
						'id' => 'id',
						'fields' => array('id' => $this->CUSTOMER->id),
						'fields2' => array('billStopped' => "'" . $billStopped . "'")
					);
				$this->log->message('Updated billStopped to '
						. ($billStopped == 'N' ? 'FALSE' : 'TRUE'));
			}
		}

	//----------------------------------------------------------------------------------------------

		private function generate_service_adjustment($type, $daily, $sunday, $begin, $end)
		{
			global $DB;

			$desc = 'Customer Stop ' . strftime('%m/%d/%y', $begin);
			if ($begin != $end)
				$desc .= ' - ' . strftime('%m/%d/%y', $end);
			$note = 'Credited for ' . $daily . ' daily, and ' . $sunday . " sunday.\n"
					. ' Daily rate was ' . sprintf('$%01.4f', round($this->rates[$type][Biller::DAILY], 4))
					. ".\nSunday rate was " . sprintf('$%01.4f', round($this->rates[$type][Biller::SUNDAY], 4))
					. ".\n";
			$amount = -round(($daily * $this->rates[$type][Biller::DAILY])
					+ ($sunday * $this->rates[$type][Biller::SUNDAY]), 2);
			$this->inserts[] = array
				(
					'table' => 'customers_adjustments',
					'fields' => array
						(
							'customer_id' => $this->CUSTOMER->id,
							'period_id' => 'NULL',
							'created' => 'NOW()',
							'updated' => 'NOW()',
							'desc' => "'" . db_escape($desc) . "'",
							'amount' => $amount,
							'note' => "'" . db_escape($note). "'"
						)
				);
			$this->log->message('Added adjustment of $' . sprintf('%01.2f', $amount) . ' for ' . $desc);
		}

	//----------------------------------------------------------------------------------------------

		private function generate_service_type()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			static $DAYS = array
				(
					0 => 'Sun',
					1 => 'Mon',
					2 => 'Tue',
					3 => 'Wed',
					4 => 'Thu',
					5 => 'Fri',
					6 => 'Sat'
				);

			// If there is only 1 rate, then no changes were made
			if (count($this->rates) == 1)
			{
				// Generate update for customer->billType
				$end = end($this->days);
				if ($end[Biller::TYPE] != $this->CUSTOMER->billType)
				{
					$this->updates[] = array
						(
							'table' => 'customers',
							'id' => 'id',
							'fields' => array('id' => $this->CUSTOMER->id),
							'fields2' => array('billType' => $end[Biller::TYPE])
						);
					$this->log->message('Updated billType to '
							. $this->TYPES[$end[Biller::TYPE]]['abbr'] . ' (id = '
							. $end[Biller::TYPE] . ').');
				}
				return;
			}

			// Walk the days and generate type change adjustments as needed
			$count = count($this->days);
			$curDay = 0;
			$curType = $this->days[0][Biller::TYPE];
			reset($this->days);
			foreach($this->days as $day => $data)
			{
				// Is this a new type?
				if ($data[Biller::TYPE] != $curType)
				{
					// Determine daily and sunday's for old type
					$daily = 0;
					$sunday = 0;
					for ($i = $curDay; $i < $day; ++$i)
					{
						$weekday = $this->days[$i][Biller::DAYOFWEEK];
						if ($this->TYPES[$curType][$DAYS[$weekday]]['paper'])
						{
							if ($weekday == 0)
								++$sunday;
							else
								++$daily;
						}
					}

					// Create adjustment
					$desc = sprintf("%s @ %d Daily, %d Sunday", $this->TYPES[$curType]['abbr'],
							$daily, $sunday);
					$amount = round(($daily * $this->rates[$curType][Biller::DAILY])
							+ ($sunday * $this->rates[$curType][Biller::SUNDAY]), 2);
					$note = sprintf("Charge\nOld Type: %s\n%d Daily\n%d Sunday\nDaily rate is $%01.4f\nSunday rate is $%01.4f",
							$this->TYPES[$curType]['abbr'], $daily, $sunday, $this->rates[$curType][Biller::DAILY],
							$this->rates[$curType][Biller::SUNDAY]);
					$this->inserts[] = array
						(
							'table' => 'customers_adjustments',
							'fields' => array
								(
									'customer_id' => $this->CUSTOMER->id,
									'period_id' => 'NULL',
									'created' => 'NOW()',
									'updated' => 'NOW()',
									'desc' => "'" . db_escape($desc) . "'",
									'amount' => $amount,
									'note' => "'" . db_escape($note) . "'"
								)
						);
					$this->log->message('Added adjustment of $' . $amount . ' for ' . $desc . '.');

					$curDay = $day;
					$curType = $data[Biller::TYPE];
				}
			}

			// Determine credit for final type adjustment
			$daily = 0;
			$sunday = 0;
			for ($i = 0; $i < $curDay; ++$i)
			{
				$weekday = $this->days[$i][Biller::DAYOFWEEK];
				if ($this->TYPES[$curType][$DAYS[$weekday]]['paper'])
				{
					if ($weekday == 0)
						++$sunday;
					else
						++$daily;
				}
			}

			// Create adjustment
			$desc = sprintf("%s @ %d Daily, %d Sunday", $this->TYPES[$curType]['abbr'],
					$daily, $sunday);
			$amount = -round(($daily * $this->rates[$curType][Biller::DAILY])
					+ ($sunday * $this->rates[$curType][Biller::SUNDAY]), 2);
			$note = sprintf("Credit\nNew Type: %s\n%d Daily\n%d Sunday\nDaily rate is $%01.4f\nSunday rate is $%01.4f",
					$this->TYPES[$curType]['abbr'], $daily, $sunday, $this->rates[$curType][Biller::DAILY],
					$this->rates[$curType][Biller::SUNDAY]);
			$this->inserts[] = array
				(
					'table' => 'customers_adjustments',
					'fields' => array
						(
							'customer_id' => $this->CUSTOMER->id,
							'period_id' => 'NULL',
							'created' => 'NOW()',
							'updated' => 'NOW()',
							'desc' => "'" . db_escape($desc) . "'",
							'amount' => $amount,
							'note' => "'" . db_escape($note) . "'"
						)
				);
			$this->log->message('Added adjustment of $' . $amount . ' for ' . $desc . '.');

			// Generate update for customer->billType
			$end = end($this->days);
			if ($end[Biller::TYPE] != $this->CUSTOMER->billType)
			{
				$this->updates[] = array
					(
						'table' => 'customers',
						'id' => 'id',
						'fields' => array('id' => $this->CUSTOMER->id),
						'fields2' => array('billType' => $end[Biller::TYPE])
					);
				$this->log->message('Updated billType to '
						. $this->TYPES[$end[Biller::TYPE]]['abbr'] . ' (id = '
						. $end[Biller::TYPE] . ').');
			}
		}

	//----------------------------------------------------------------------------------------------

		private function initialize_days()
		{
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			$start = $this->PERIOD[P_START];
			$days = days_between_dates($start, $this->PERIOD[P_END]);
			$stopped = ($this->CUSTOMER->billStopped == 'Y' ? true : false);
			for ($day = 0; $day < $days; ++$day)
			{
				$date = strtotime('+' . $day . ' days', $start);
				$this->days[$day] = array
					(
						Biller::DATE => $date,
						Biller::TYPE => $this->CUSTOMER->billType,
						Biller::STATUS => $this->TYPES[$this->CUSTOMER->billType][date('D', $date)]['paper'],
						Biller::ACTUAL => ($stopped ? false : true),
						Biller::DAYOFWEEK => date('w', $date)
					);
			}
		}

	//----------------------------------------------------------------------------------------------

		private function reset($common = true)
		{
			if ($common)
			{
				unset($this->PERIODS);
				unset($this->TYPES);
			}
			unset($this->CUSTOMER);
			$this->days = array();
			$this->inserts = array();
			$this->rates = array();
			$this->updates = array();
			$this->log->reset();
		}

	//----------------------------------------------------------------------------------------------

		private function sum_adjustments()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			$total = 0;

			// First get the adjustments from the database
			$query = "SELECT `id`, `amount` FROM `customers_adjustments` WHERE `customer_id` = "
					. $this->CUSTOMER->id . " AND `period_id` <=> NULL";
			$records = db_query($query);
			if (!$records)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			while ($record = $records->fetch_object())
			{
				$total += $record->amount;

				// Add this record to update list
				$this->updates[] = array
					(
						'table' => 'customers_adjustments',
						'id' => 'id',
						'fields' => array('id' => $record->id),
						'fields2' => array('period_id' => $this->PERIOD[P_PERIOD])
					);
				$this->log->message('Updated period_id of adjustment of $'
						. sprintf('%01.2f', $record->amount) . ' (id = '
						. sprintf('%08d', $record->id) . ') to ' . $this->PERIOD[P_TITLE]
						. ' (id = ' . sprintf('%04d', $this->PERIOD[P_PERIOD]) . ').');

			}

			// Add in the adjustments we haven't created yet
			reset($this->inserts);
			foreach($this->inserts AS $index => $insert)
			{
				if ($insert['table'] == 'customers_adjustments')
				{
					$total += $insert['fields']['amount'];
					$this->inserts[$index]['fields']['period_id'] = $this->PERIOD[P_PERIOD];
				}
			}

			return $total;
		}

	//----------------------------------------------------------------------------------------------

		private function sum_payments()
		{
			global $DB, $err, $errCode, $errText;
			$this->log->context(__CLASS__ . '::' . __FUNCTION__);

			$query = "SELECT `id`, `amount`, `tip` FROM `customers_payments` WHERE `customer_id` = "
					. $this->CUSTOMER->id . " AND `period_id` = " . $this->PERIOD[PP_PERIOD];
			$records = db_query($query);
			if (!$records)
			{
				log_error();
				throw new Exception($errText, $errCode);
			}

			$total = 0;
			while ($record = $records->fetch_object())
			{
				$this->log->message('Located payment of $' . sprintf('%01.2f', ($record->amount - $record->tip))
						. ' = (Total) $' . sprintf('%01.2f', $record->amount) . ' - (Tip) $'
						. sprintf('%01.2f', $record->tip) . '.');
				$total += ($record->amount - $record->tip);
			}
			return $total;
		}

	//----------------------------------------------------------------------------------------------

		const DUMP_DAYS		= 0x00000001;
		const DUMP_RATES	= 0x00000002;
		const DUMP_INSERTS	= 0x00000004;
		const DUMP_UPDATES	= 0x00000008;
		const DUMP_ALL		= 0xFFFFFFFF;
		private function dump($what = Biller::DUMP_ALL, $eol = "\n", $space = "\t")
		{
			printf("DUMP of %06d for %s (%s - %s)" . $eol, $this->CUSTOMER->id,
					$this->PERIOD[P_TITLE], strftime('%Y/%m/%d', $this->PERIOD[P_START]),
					strftime('%Y/%m/%d', $this->PERIOD[P_END]));

			if (($what & Biller::DUMP_DAYS) > 0)
			{
				echo "    DAYS:" . $eol;
				reset($this->days);
				foreach($this->days as $day => $state)
				{
					echo $space . "Day ";
					printf('%-3d', $day);
					echo ' = { ';
					$this->dump_date($state[Biller::DATE]);
					switch ($state[Biller::DAYOFWEEK])
					{
					case 0: echo ' | Sunday   '; break;
					case 1: echo ' | Monday   '; break;
					case 2: echo ' | Tuesday  '; break;
					case 3: echo ' | Wednesday'; break;
					case 4: echo ' | Thursday '; break;
					case 5: echo ' | Friday   '; break;
					case 6: echo ' | Saturday '; break;
					}
					printf(' | %-10s | %-7s | %-9s', $this->TYPES[$state[Biller::TYPE]]['abbr'],
							($state[Biller::STATUS] ? 'Deliver' : ''),
							($state[Biller::ACTUAL] ? 'Delivered' : 'Stopped'));
					echo " }" . $eol;
				}
			}

			if (($what & Biller::DUMP_RATES) > 0)
			{
				echo "    RATES:" . $eol;
				if (count($this->rates) > 0)
				{
					if (isset($this->rates[0]) && count($this->rates) == 2)
					{
						printf($space . "FTST       = { DAILY $%01.2f | SUNDAY $%01.2f }" . $eol,
								$this->rates[Biller::DAILY], $this->rates[Biller::SUNDAY]);
					}
					else
					{
						foreach($this->rates as $t => $r)
						{
							printf($space . "%-10s = { DAILY $%01.4f | SUNDAY $%01.4f }" . $eol,
									$this->TYPES[$t]['abbr'], $r[Biller::DAILY],
									$r[Biller::SUNDAY]);
						}
					}
				}
				else
					echo $space . "NONE" . $eol;
			}

			if (($what & Biller::DUMP_INSERTS) > 0)
			{
				echo "    INSERTS:" . $eol;
				if (count($this->inserts) > 0)
				{
					foreach($this->inserts as $update)
					{
						echo $space . "INSERT INTO `" . $update['table'] . "` SET";
						$c = '';
						foreach($update['fields'] as $f => $v)
						{
							echo $c . ' `' . $f . '` = ' . $v;
							$c = ',';
						}
						echo ' LIMIT 1' . $eol;
					}
				}
				else
					echo $space . "NONE" . $eol;
			}

			if (($what & Biller::DUMP_UPDATES) > 0)
			{
				echo "    UPDATES:" . $eol;
				if (count($this->updates) > 0)
				{
					foreach($this->updates as $update)
					{
						echo $space . "UPDATE `" . $update['table'] . "` SET";
						$c = '';
						foreach($update['fields2'] as $f => $v)
						{
							echo $c . ' `' . $f . '` = ' . $v;
							$c = ',';
						}
						echo ' WHERE';
						$a = '';
						foreach($update['fields'] as $f => $v)
						{
							echo $a . ' `' . $f . '` = ' . $v;
							$a = ' AND';
						}
						echo $eol;
					}
				}
				else
					echo $space . "NONE" . $eol;
			}
		}

		private function dump_boolean($val)
		{
			if ($val)
				echo 'TRUE';
			else
				echo 'FALSE';
		}

		private function dump_date($val)
		{
			echo strftime('%Y/%m/%d %H:%M:%S', $val);
			printf (' (%10d)', $val);
		}
	}

?>
