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
	
	//-------------------------------------------------------------------------
	// Display orders page
	
	function subdisplay()
	{
		global $smarty;
        global $resultHtml;
        global $message;

        $smarty->assign('message', $message);
        $smarty->assign('action', $_SERVER['PHP_SELF']);
        $smarty->assign('menu', $_REQUEST['menu']);
        $smarty->assign('submenu', $_REQUEST['submenu']);
        $smarty->display("customers/reports/orders.tpl");
	}
	
	//-------------------------------------------------------------------------
	// Return MySQL rows for order production customers
	
	function restarted($first, $last)
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $DeliveryTypes;
		
		// Find the customers that restarted during the week
		$query = "SELECT DISTINCT(`customer_id`) FROM `customers_service` WHERE `type` = '" . SERVICE_START . "' "
				. "AND `when` BETWEEN '" . strftime('%Y-%m-%d', $first) . "' AND '" . strftime('%Y-%m-%d', $last)
				. "' ORDER BY `customer_id`";
		$cids = db_query($query);
		if (!$cids)
			return array();
	
		// Build the result array
		$restarted = array();
		while ($cid = $cids->fetch_object())
		{
			$query = "SELECT `when` FROM `customers_service` WHERE `type` = '" . SERVICE_START . "' "
					. "AND `customer_id` = " . $cid->customer_id
					. " AND `when` BETWEEN '" . strftime('%Y-%m-%d', $first) . "' AND '" . strftime('%Y-%m-%d', $last) . "'"
					. " ORDER BY `when` DESC LIMIT 1";
			$starts = db_query_result($query);
			if (!$starts)
				return array();
			$start = strtotime($starts);
			
			// Locate the last stop for this customer
			$query = "SELECT `when` FROM `customers_service` WHERE `type` = '" . SERVICE_STOP . "' "
					. "AND `customer_id` = " . $cid->customer_id . " AND `when` < '" . strftime('%Y-%m-%d', $start)
					. "' ORDER BY `when` DESC LIMIT 1";
			$stops = db_query($query);
			if (!$stops)
				return array();
	
			// Is the start more than 30 days before the stop?
			// NOTE: It is NOT a data failure if there is no start for a stop as new customers
			// will get a start initially.
			$flagStopId = get_config('flag-stop-type');
			if ($stops->num_rows > 0)
			{
				$temp = $stops->fetch_object();
				$stop = strtotime($temp->when);
				$count = days_between_dates($stop, $start);
				if ($count > 30)
				{
					$customer = lup_customer($cid->customer_id);
					if ($customer->type_id != $flagStopId)
					{
						if ($DeliveryTypes[$customer->type_id]['watchStart'])
						{
							$restarted[$cid->customer_id] = array
									(
										'customer' => $customer,
										'start' => $start,
										'stop' => $stop,
										'count' => $count
									);
						}
					}
				}
			}
		}
		
		return $restarted;
	}
	
	//-------------------------------------------------------------------------
	// Handle orders report buttons
	
	function subsubmit()
	{
        global $smarty;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $DeliveryTypes;
		global $Routes;
        global $message;

		populate_routes();
		populate_types();
		
		// Get the date
		$date = valid_date('date', 'Date');
		if ($err < ERR_SUCCESS)
			return '';
		$date = strtotime($date);
		
		// Determine the start and end dates of the week
		$day = date('w', $date);
		if ($day)
			$firstDay = strtotime('-' . $day . ' days', $date);
		else
			$firstDay = $date;
        $lastDay = strtotime('+6 days', $firstDay);
        $smarty->assign('title', 'Order Production ' . strftime('%m/%d/%Y', $firstDay)
            . ' - ' . strftime('%m/%d/%Y', $lastDay));
		
		// Get the list of customers that started during those dates
		$query =
"
SELECT
    `c`.`id`,
    `c`.`route_id`,
    `c`.`type_id`,
    `c`.`started`,
    `a`.`address1` AS `address`,
    `t`.`number` AS `telephone`,
    `n`.`first` AS `firstName`,
    `n`.`last` AS `lastName`
FROM
    `customers` AS `c`
    INNER JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id` 
    INNER JOIN `customers_telephones` AS `t` ON `c`.`id` = `t`.`customer_id`
    INNER JOIN `customers_names` AS `n` ON `c`.`id` = `n`.`customer_id`
WHERE
    `a`.`sequence` = " . ADDR_C_DELIVERY . "
    AND `t`.`sequence` = " . TEL_C_DELIVERY1 . "
    AND `n`.`sequence` = " . NAME_C_DELIVERY1 . "
    AND `c`.`started` BETWEEN '" . strftime('%Y-%m-%d', $firstDay) . "' AND '" . strftime('%Y-%m-%d', $lastDay) . "'
ORDER BY `id`
";
		$customers = db_query($query);
		if (!$customers)
			return '';
        $smarty->assign('customers', $customers);
        $smarty->assign('customersCount', $customers->num_rows);

		// Get the list of customers that were stopped more than a month prior to their restart
		$restarted = restarted($firstDay, $lastDay);
		if ($err < ERR_SUCCESS)
			return '';
        $smarty->assign('restarted', $restarted);

        return $smarty->fetch('customers/reports/orders_report.tpl');
	}

?>
