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

	function subdisplay()
	{
		global $err, $smarty;

        $smarty->assign('action', $_SERVER['PHP_SELF']);
        
		if (!isset($_POST['active']))
			$_POST['active'] = 'N';
		$temp = $_POST['active'];
		switch($temp)
		{
		case 'Y':
			$active = array(0=>'',1=>' checked="checked"',2=>'');
			break;
		case 'N':
			$active = array(0=>'',1=>'',2=>' checked="checked"');
			break;
		case 'I':
		default:
			$active = array(0=>' checked="checked"', 1=>'', 2=>'');
			break;
		}
        $smarty->assign('active', $active);

		if (!isset($_POST['routeList']))
			$_POST['routeList'] = 'Y';
		$temp = $_POST['routeList'];
		switch($temp)
		{
		case 'Y':
			$routeList = array(0=>'',1=>' checked="checked"',2=>'');
			break;
		case 'N':
			$routeList = array(0=>'',1=>'',2=>' checked="checked"');
			break;
		case 'I':
		default:
			$routeList = array(0=>' checked="checked"', 1=>'', 2=>'');
			break;
		}
        $smarty->assign('routeList', $routeList);

		if (!isset($_POST['pending']))
			$_POST['pending'] = 'I';
		$temp = $_POST['pending'];
		switch($temp)
		{
		case 'Y':
			$pending = array(0=>'',1=>' checked="checked"',2=>'');
			break;
		case 'N':
			$pending = array(0=>'',1=>'',2=>' checked="checked"');
			break;
		case 'I':
		default:
			$pending = array(0=>' checked="checked"', 1=>'', 2=>'');
			break;
		}
        $smarty->assign('pending', $pending);

		if (!isset($_POST['stop']))
			$_POST['stop'] = '0';
		$stop = intval($_POST['stop']);
        $smarty->assign('stop', $stop);
        $smarty->assign('menu', $_REQUEST['menu']);
        $smarty->assign('submenu', $_REQUEST['submenu']);

		$smarty->display('customers/reports/stopped.tpl');
	}

	function subsubmit()
	{
        global $smarty;
		global $DeliveryTypes, $Period, $err;
		
		populate_routes();
		populate_types();

        $active = $_POST['active'];
		switch($active)
		{
		case 'I':	$ACTIVE = '';							break;
		case 'Y':	$ACTIVE = 'AND `c`.`active` = \'Y\'';	break;
		case 'N':	$ACTIVE = 'AND `c`.`active` = \'N\'';	break;
		}

		$routeList = $_POST['routeList'];
		switch($routeList)
		{
		case 'I':	$ROUTELIST = '';							break;
		case 'Y':	$ROUTELIST = 'AND `c`.`routeList` = \'Y\'';	break;
		case 'N':	$ROUTELIST = 'AND `c`.`routeList` = \'N\'';	break;
		}

		$stop = intval($_POST['stop']);
        if ($stop > 0)
            $stopHeader = '<th>When</th>';
        else
            $stopHeader = '';
        $smarty->assign('stopHeader', $stopHeader);

		$pending = $_POST['pending'];

		$query =
'
SELECT
	`c`.`id`,
	`c`.`route_id`,
	`c`.`type_id`,
	`a`.`address1` AS `address`,
	`n`.`first`,
	`n`.`last`
FROM
	`customers` AS `c`
	INNER JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id`
	INNER JOIN `customers_names` AS `n` ON `c`.`id` = `n`.`customer_id`
	INNER JOIN `routes_sequence` AS `s` ON `c`.`id` = `s`.`tag_id`
WHERE
	`c`.`route_id` = `s`.`route_id`
	AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
	AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
	' . $ACTIVE . '
	' . $ROUTELIST . '
ORDER BY
	' . db_route_field('`c`.`route_id`') . ' ASC,
	`s`.`order` ASC
';
		$records = db_query($query);
		if (!$records)
			return '';

        $customers = array();
		$count = 0;
		while ($record = $records->fetch_object())
		{
			if ($stop > 0)
			{
				$period = lup_period($Period[P_PERIOD] - $stop);
				switch($pending)
				{
				case 'I':	$PENDING = '';														break;
				case 'Y':	$PENDING = 'AND `s`.`period_id` <=> NULL';							break;
				case 'N':	$PENDING = 'AND `s`.`period_id` = ' . ($Period[P_PERIOD] - $stop);	break;
				}
				$query = '
SELECT
	`s`.`when`
FROM
	`customers_service` AS `s`
WHERE
	`s`.`customer_id` = ' . $record->id . '
	AND `type` = \'' . SERVICE_STOP . '\'
	AND `when` BETWEEN \'' . date('Y-m-d', strtotime($period->changes_start)) . '\' AND \'' . date('Y-m-d', strtotime($period->changes_end)) . '\'
	' . $PENDING . '
ORDER BY
	`when` DESC
LIMIT
	1
';
				$when = db_query_result($query);
				if (!$when)
				{
					if ($err >= ERR_SUCCESS)
						continue;
					return '';
				}
			}
            $customer = array('cid' => $record->id,
                              'name' => valid_name($record->first, $record->last),
                              'address' => $record->address,
                              'type_id' => $record->type_id,
                              'route_id' => $record->route_id);
            if ($stop > 0)
                $customer['when'] = date('m-d-Y', strtotime($when));
            $customers[] = $customer;
            ++$count;
		}
        $smarty->assign('customers', $customers);
        $smarty->assign('count', $count);
        
		$subtitle2 = sprintf('%d Customer', $count);
		if ($count > 1)
            $subtitle2 .= 's';
        $smarty->assign('subtitle2', $subtitle2);

		$subtitle1 = '';
		$sep = '';
		if ($active == 'Y')
		{
			$subtitle1 .= $sep . 'Active';
			$sep = ' / ';
		}
		else if ($active == 'N')
		{
			$subtitle1 .= $sep . 'Inactive';
			$sep = ' / ';
		}
		if ($routeList == 'Y')
		{
			$subtitle1 .= $sep . 'On Route List';
			$sep = ' / ';
		}
		else if ($routeList == 'N')
		{
			$subtitle1 .= $sep . 'Off Route List';
			$sep = ' / ';
		}
		if ($stop > 0)
		{
			$subtitle1 .= $sep . 'Stopped ' . $stop . ' Period';
			if ($stop > 1)
				$subtitle1 .= 's';
			$subtitle1 .= ' Ago';
			$sep = ' / ';
        }
        $smarty->assign('subtitle1', $subtitle1);

        return $smarty->fetch('customers/reports/stopped_report.tpl');
	}
?>
