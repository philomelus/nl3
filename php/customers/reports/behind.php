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

	define('WHAT_NOPAYMENTS', 1);
	define('WHAT_BEHIND1', 2);
	define('WHAT_BEHINDMANY', 3);
	
	//-------------------------------------------------------------------------
	
	function behind1()
	{
        global $smarty;
		global $Routes;
		global $DeliveryTypes;
		
		populate_routes();
		populate_types();
		
		// Ask database to get the list
		$query =
'SELECT
    `c`.`id`,
    `c`.`route_id`,
    `c`.`type_id`,
    `c`.`balance`,
    `c`.`started`,
	`s`.`order`,
	`a`.`address1` AS `address`,
	`t`.`number` AS `telephone`,
	`n`.`first` AS `firstName`,
	`n`.`last` AS `lastName`
FROM
	`customers` AS `c` 
    INNER JOIN `routes_sequence` AS `s` ON `c`.`id` = `s`.`tag_id`
    INNER JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id`
        AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
	INNER JOIN `customers_telephones` AS `t` ON `c`.`id` = `t`.`customer_id`
        AND `t`.`sequence` = ' . TEL_C_DELIVERY1 . '
	INNER JOIN `customers_names` AS `n` ON `c`.`id` = `n`.`customer_id`
        AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
WHERE
	`c`.`balance` > 0
	AND `c`.`active` = \'Y\'
ORDER BY ' . db_route_field('`c`.`route_id`') . ', `order`
';
		$customers = db_query($query);
		if (!$customers)
			return '';


        // Build array of results for display
		$results = array();
		while ($customer = $customers->fetch_array(MYSQLI_ASSOC))
		{
			// See if its for 2 or more months of current rate
            $rate = $DeliveryTypes[$customer['type_id']]['rate'];
			$dif = $customer['balance'] - (2 * $rate);
			if ($dif >= 0 && $dif <= $rate)
                $results[] = $customer;
		}
		$customers->close();

        $smarty->assign('title', 'Customers Behind 1 Period');
        $smarty->assign('customers', $results);

        return $smarty->fetch('customers/reports/behind_report.tpl');
	}
	
	//-------------------------------------------------------------------------
	
	function behindMany()
	{
        global $smarty;
		global $Routes;
		global $DeliveryTypes;
		
		populate_routes();
		populate_types();
		
		// Ask database to get the list
		$query =
'SELECT
    `c`.`id`,
    `c`.`route_id`,
    `c`.`type_id`,
    `c`.`balance`,
    `c`.`started`,
    `s`.`order`,
    `a`.`address1` AS `address`,
    `t`.`number` AS `telephone`,
    `n`.`first` AS `firstName`,
    `n`.`last` AS `lastName`
FROM
    `customers` AS `c` 
    INNER JOIN `routes_sequence` AS `s` ON `c`.`id` = `s`.`tag_id`
    INNER JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id`
        AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
    INNER JOIN `customers_telephones` AS `t` ON `c`.`id` = `t`.`customer_id`
        AND `t`.`sequence` = ' . TEL_C_DELIVERY1 . '
    INNER JOIN `customers_names` AS `n` ON `c`.`id` = `n`.`customer_id`
        AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
WHERE
    `c`.`balance` > 0
    AND `c`.`active` = \'Y\'
ORDER BY ' . db_route_field('`c`.`route_id`') . ', `order`
';
		$customers = db_query($query);
		if (!$customers)
			return '';
		$results = array();
		while ($customer = $customers->fetch_array(MYSQLI_ASSOC))
		{
			// See if its for 2 or more months of current rate
			if ($customer['balance'] - (3 * $DeliveryTypes[$customer['type_id']]['rate']) >= 0)
            {
                $results[] = $customer;
			}
		}
        $customers->close();
        
        $smarty->assign('title', 'Customers Behind More Than 1 Period');
        $smarty->assign('customers', $results);

        return $smarty->fetch('customers/reports/behind_report.tpl');
	}
	
	//-------------------------------------------------------------------------
	
	function subdisplay()
	{
		global $smarty;

        // Make sure a report is selected by default
		if (!isset($_REQUEST['what']) || empty($_REQUEST['what']))
			$_REQUEST['what'] = WHAT_BEHIND1;

        // Determine which option is selected
 		if (intval($_REQUEST['what']) == WHAT_BEHIND1)
 			$one = ' checked="checked"';
 		else
 			$one = '';
		if (intval($_REQUEST['what']) == WHAT_BEHINDMANY)
			$many = ' checked="checked"';
		else
			$many = '';
		if (intval($_REQUEST['what']) == WHAT_NOPAYMENTS)
			$noPayments = ' checked="checked"';
		else
			$noPayments = '';

        // Tell template which radio to check
        // TODO:  Klugey ... not sure of a better way ...
        $smarty->assign('WHAT_BEHIND1', WHAT_BEHIND1);
        $smarty->assign('WHAT_BEHINDMANY', WHAT_BEHINDMANY);
        $smarty->assign('WHAT_NOPAYMENTS', WHAT_NOPAYMENTS);

        $smarty->assign('one', $one);
        $smarty->assign('many', $many);
        $smarty->assign('noPayments', $noPayments);

        $smarty->display("customers/reports/behind.tpl");
	}
	
	//-------------------------------------------------------------------------
	
	function noPayments()
	{
        global $smarty;
		global $Routes;
		global $DeliveryTypes;
		
		populate_routes();
		populate_types();
		
		// Ask database to get the list
		$query =
'
SELECT
    `c`.`id`,
    `c`.`route_id`,
    `c`.`type_id`,
    `c`.`balance`,
    `c`.`started`,
	`s`.`order`,
	`a`.`address1` AS `address`,
	`t`.`number` AS `telephone`,
	`n`.`first` AS `firstName`,
	`n`.`last` AS `lastName`,
	(
		SELECT
			COUNT(*)
		FROM
			`customers_payments` AS `p`
            INNER JOIN `customers` AS `c` ON `p`.`customer_id` = `c`.`id`
	) as `num_pmts`
FROM
	`customers` AS `c`
	INNER JOIN `routes_sequence` AS `s` ON `c`.`id` = `s`.`tag_id`
	INNER JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id`
        AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
	INNER JOIN `customers_telephones` AS `t` ON `c`.`id` = `t`.`customer_id`
        AND `t`.`sequence` = ' . TEL_C_DELIVERY1 . '
	INNER JOIN `customers_names` AS `n` ON `c`.`id` = `n`.`customer_id`
        AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
	INNER JOIN `customers_payments` AS `p` ON `c`.`id` = `p`.`customer_id`
WHERE
	`c`.`balance` > 0
	AND `c`.`active` = \'Y\'
GROUP BY
	`c`.`id`
HAVING
	`num_pmts` = 0
ORDER BY
	' . db_route_field('`c`.`route_id`') . ',
	`order`
';
		$customers = db_query($query);
		if (!$customers)
			return '';
		$customerHtml = '';
		$count = 0;
		while ($customer = $customers->fetch_array(MYSQLI_ASSOC))
		{
			// See if its for 2 or more months of current rate
			if ($customer->balance - (3 * $DeliveryTypes[$customer['type_id']]['rate']) >= 0)
			{
                $results[] = $customer;
			}
		}
        $customers->close();

        $smarty->assign('title', 'Customers Behind More Than 1 Period With No Payments');
        $smarty->assign('customers', $results);

        return $smarty->fetch('customers/reports/behind_report.tpl');
	}
	
	//-------------------------------------------------------------------------
	
	function subsubmit()
	{
		switch (intval($_REQUEST['what']))
		{
		case WHAT_NOPAYMENTS:
			return noPayments();
			
		case WHAT_BEHIND1:
			return behind1();
			
		case WHAT_BEHINDMANY:
			return behindMany();
		}
		
		return '<span>ERROR - INVALID REPORT TYPE!</span>';
	}

?>
