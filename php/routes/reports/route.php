<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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

	function divider()
	{
		return '<tr><td colspan="5">&nbsp;</td></tr>'
				. '<tr><td colspan="5">&nbsp;</td></tr>'
				. '<tr><td colspan="5">&nbsp;</td></tr>';
	}

	//-------------------------------------------------------------------------

	function subdisplay()
	{
		global $err, $errCode, $errContext, $errQuery, $errText, $smarty;

		$smarty->display("menu.tpl");

?>
		<table>
			<tr>
				<td>Route</td>
				<td>
<?php
		if (isset($_REQUEST['rid']) && !empty($_REQUEST['rid']))
			$val = intval($_REQUEST['rid']);
		else
			$val = '';
		echo gen_routesSelect('rid', $val, false, '', '');
?>
				</td>
			</tr>
			<tr>
				<td>Show Stops</td>
				<td>
<?php
		if (isset($_REQUEST['stops']) && $_REQUEST['stops'] == 'Y')
			$val = ' checked="checked"';
		else
			$val = '';
?>
					<input type="checkbox" name="stops" value="Y"<?php echo $val ?> />
					as of
<?php
		if (!isset($_REQUEST['whenm']))
		{
			$_REQUEST['whenm'] = date('n');
			$_REQUEST['whend'] = date('j');
			$_REQUEST['wheny'] = date('Y');
		}
		echo gen_dateField('when');

		if (isset($_REQUEST['only']) && $_REQUEST['only'] == 'Y')
			$val = ' checked="checked"';
		else
			$val = '';
?>
					<input type="checkbox" name="only" value="Y"<?php echo $val ?>>Only</input>
				</td>
			</tr>
			<tr>
				<td>Show Type(s)</td>
				<td>
<?php

?>
<?php
		if (isset($_REQUEST['type1']) && !empty($_REQUEST['type1']))
			$val = intval($_REQUEST['type1']);
		else
			$val = -1;
		echo gen_typeSelect('type1', $val, false, '', array(0 => 'All'), '');
		if (isset($_REQUEST['type2']) && !empty($_REQUEST['type2']))
			$val = intval($_REQUEST['type2']);
		else
			$val = -1;
		echo gen_typeSelect('type2', $val, false, '', array(0 => 'Ignore'), '');
		if (isset($_REQUEST['type3']) && !empty($_REQUEST['type3']))
			$val = intval($_REQUEST['type3']);
		else
			$val = -1;
		echo gen_typeSelect('type3', $val, false, '', array(0 => 'Ignore'), '');
		if (isset($_REQUEST['type4']) && !empty($_REQUEST['type4']))
			$val = intval($_REQUEST['type4']);
		else
			$val = -1;
		echo gen_typeSelect('type4', $val, false, '', array(0 => 'Ignore'), '');
?>
				</td>
			</tr>
			<tr>
				<td>Show Delivery Notes</td>
				<td>
<?php
		if (isset($_REQUEST['instr']) && $_REQUEST['instr'] == 'Y')
			$val = ' checked="checked"';
		else
			$val = '';
?>
					<input type="checkbox" name="instr" value="Y"<?php echo $val ?> />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Update" />
				</td>
			</tr>
		</table>
<?php
	}

	//-------------------------------------------------------------------------

	function subsubmit()
	{
		global $Routes;
		global $DeliveryTypes;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $Period;

		populate_routes();
		populate_types();

		// Determine whether to show stops
		if (isset($_REQUEST['stops']) && $_REQUEST['stops'] == 'Y')
			$showStops = true;
		else
			$showStops = false;

		// If showing stops, validate date
		$stopsOnly = false;
		if ($showStops)
		{
			$when = valid_date('when', 'Date');
			if ($err < ERR_SUCCESS)
				return '';
			$date = strtotime($when);

			// Update show only stops if set
			if (isset($_REQUEST['only']) && $_REQUEST['only'] == 'Y')
				$stopsOnly = true;
		}

		// Get the customers from the database
		$html = '';
		$query =
"
SELECT
	`s`.`order`,
	`c`.`id`,
	`c`.`type_id`,
	`c`.`billStopped`,
	`c`.`deliveryNote`,
	`a`.`address1` AS `address`,
	`n`.`first` AS `firstName`,
	`n`.`last` AS `lastName`
FROM
	`routes_sequence` AS `s`,
	`customers` AS `c`,
	`customers_addresses` AS `a`,
	`customers_names` AS `n`
WHERE
	`c`.`id` = `s`.`tag_id`
	AND `c`.`id` = `a`.`customer_id`
	AND `c`.`id` = `n`.`customer_id`
	AND `a`.`sequence` = " . ADDR_C_DELIVERY . "
	AND `n`.`sequence` = " . NAME_C_DELIVERY1 . "
	AND `c`.`routeList` = 'Y'
	AND `c`.`route_id` = " . $_REQUEST['rid'] . "
";
		$temp = intval($_REQUEST['type1']);
		if ($temp > 0)
		{
			$query .= " AND `c`.`type_id` IN (" . $temp;
			$subtitle = ' (' . $DeliveryTypes[$temp]['abbr'];
			$temp = intval($_REQUEST['type2']);
			if ($temp > 0)
			{
				$query .= ', ' . $temp;
				$subtitle .= ', ' . $DeliveryTypes[$temp]['abbr'];
				$temp = intval($_REQUEST['type3']);
				if ($temp > 0)
				{
					$query .= ', ' . $temp;
					$subtitle .= ', ' . $DeliveryTypes[$temp]['abbr'];
					$temp = intval($_REQUEST['type4']);
					if ($temp > 0)
					{
						$query .= ', ' . $temp;
						$subtitle .= ', ' . $DeliveryTypes[$temp]['abbr'];
					}
				}
			}
			$query .= ')';
			$subtitle .= ' only)';
		}
		else
			$subtitle = '';
		$query .= " ORDER BY `s`.`order`";
		$customers = db_query($query);
		if ($customers)
		{
			if (isset($_REQUEST['instr']) && $_REQUEST['instr'] == 'Y')
				$instr = true;
			else
				$instr = false;
			$pre = false;
			$post = false;
			while ($customer = $customers->fetch_object())
			{
				$color = 'background-color: #' . sprintf("%06X", $DeliveryTypes[$customer->type_id]['color']) . ';';

				$lastPre = $pre;
				$pre = false;
				$lastPost = $post;
				$post = false;

				$stopped = false;
				if ($showStops)
				{
					// Locate changes for customer since last bill
					// BUGBUG:  Should this pay attention to ignoreOnBill=Y ones?
					$query = "SELECT * FROM `customers_service` WHERE `customer_id` = " . $customer->id
							. " AND `when` <= '" . strftime('%Y-%m-%d', $date) . "'"
							. " AND `period_id` <=> NULL"
							. " ORDER BY `when` ASC, `created` ASC, `updated` ASC";
					$changes = db_query($query);
					if (!$changes)
						return false;

					// Determine starting status
					if ($customer->billStopped == 'Y')
						$what = SERVICE_STOP;
					else
						$what = SERVICE_START;

					// Determine current status
					if ($changes->num_rows > 0)
					{
						while ($change = $changes->fetch_object())
						{
							if ($change->type != $what)
							{
								if ($what == SERVICE_STOP)
									$what = SERVICE_START;
								else
									$what = SERVICE_STOP;
							}
						}
					}

					$stopped = ($what == SERVICE_STOP);
				}

				// If not stopped and only showing stops, skip it
				if ($showStops && $stopsOnly && !$stopped)
					continue;

				$html .= '<tr>'
						. '<td>' . sprintf("%06d", $customer->id) . '</td>'
						. '<td>' . stripslashes(valid_name($customer->firstName, $customer->lastName)) . '</td>';
				if ($instr)
				{
					$html .= '<td>' . htmlspecialchars(stripslashes($customer->deliveryNote)) . '</td>';
				}
				if ($stopped)
				{
					$html .= '<td>';
					if ($what == SERVICE_STOP)
						$html .= 'STOP';
					$html .= '&nbsp;</td>';
				}
				else if (!$stopsOnly)
					$html .= '<td>&nbsp;</td>';
				$html .= '<td>' . stripslashes($customer->address) . '</td>'
						. '<td>' . stripslashes($DeliveryTypes[$customer->type_id]['abbr']) . '</td>'
						. '</tr>';
			}
			$err = ERR_SUCCESS;
		}

		if ($showStops)
			$title = '<div>' . strftime('%m/%d/%Y', $date) . '</div>';
		else
			$title = '';

		$temp = 'border-bottom: 1px solid black;';
		$head = '<tr><td colspan="5">Route List' . $subtitle . '</td></tr>'
				. '<tr>'
				. '<th>'
				. 'Account'
				. '</th>'
				. '<th>'
				. 'Name'
				. '</th>';
		if ($instr)
		{
			$head .= '<th>Notes</th>'
				. '<th>&nbsp;</th>';
		}
		else
		{
			$head .= '<th>&nbsp;</th>';
		}

		$head .= '<th>'
				. 'Delivery Address'
				. '</th>'
				. '<th>'
				. 'Type'
				. '</th>'
				. '</tr>';

		return '<form><input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" /></form>'
				. '<div></div>'
				. '<div>Route ' . $Routes[$_REQUEST['rid']] . '</div>'
				. $title
				. '<table>'
				. '<thead>'
				. $head
				. '</thead>'
				. '<tbody>'
				. $html
				. '</tbody>'
				. '</table>';
	}

?>
