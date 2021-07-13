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

	function gen_status($route_id, $showStops, $date)
	{
		global $Routes;
		global $DeliveryTypes;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $Period;

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
	`c`.`billBalance`,
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
	AND `c`.`route_id` = " . $route_id . '
ORDER BY
	`s`.`order`
';
		$customers = db_query($query);
		if ($customers)
		{
			while ($customer = $customers->fetch_object())
			{
				if ($showStops)
					$stopped = get_stopped($customer, $date);

				$html .= '<tr>'
						. '<td>' . sprintf("%08d", $customer->id) . '</td>'
						. '<td>' . stripslashes(valid_name($customer->firstName, $customer->lastName)) . '</td>'
						. '<td>' . stripslashes($customer->address) . '</td>'
						. '<td>' . stripslashes($DeliveryTypes[$customer->type_id]['abbr']) . '</td>';
				if ($showStops)
				{
					if ($stopped)
						$html .= '<td>STOP</td>';
					else
						$html .= '<td>&nbsp;</td>';
				}

				$html .= '<td>$' . str_replace(' ', '&nbsp;', sprintf('% 9.2f', $customer->billBalance)) . '</td>'
						. '</tr>';
			}
			$err = ERR_SUCCESS;
		}

		if ($showStops)
			$title = '<div>' . strftime('%m/%d/%Y', $date) . '</div>';
		else
			$title = '';

		$head = '<tr>'
				. '<th">Acct</th>'
				. '<th>Name</th>'
				. '<th>Delivery Address</th>'
				. '<th>Type</th>'
				. ($showStops ? '<th>&nbsp;</th>' : '')
				. '<th>Balance</th>'
				. '</tr>';

		return '<div></div>'
				. '<div>Route ' . $Routes[$route_id] . '</div>'
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

	//-------------------------------------------------------------------------

	function get_stopped($customer, $date)
	{
		global $err;

		$query = '
SELECT
	`type`
FROM
	`customers_service`
WHERE
	`customer_id` = ' . $customer->id . '
	AND `when` <= \'' . strftime('%Y-%m-%d', $date) . '\'
	AND `period_id` <=> NULL
ORDER BY
	`when` ASC,
	`created` ASC,
	`updated` ASC
';
		$changes = db_query($query);
		if ($err < ERR_SUCCESS)
			return false;

		$what = ($customer->billStopped == 'Y' ? SERVICE_STOP : SERVICE_START);
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

		return ($what == SERVICE_STOP);
	}

	//-------------------------------------------------------------------------

	function subdisplay()
	{
		global $err, $errCode, $errContext, $errQuery, $errText, $smarty;

		$smarty->display("menu.tpl");

?>
<table>
	<tbody>
		<tr>
			<td>Route</td>
			<td>
<?php
		if (isset($_POST['rid']) && !empty($_POST['rid']))
			$val = intval($_POST['rid']);
		else
			$val = '';
		echo gen_routesSelect('rid', $val, true, '', '');
?>
			</td>
		</tr>
		<tr>
			<td>Show Stops</td>
			<td>
<?php
		if (isset($_POST['stops']) && $_POST['stops'] == 'Y')
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

		if (isset($_POST['only']) && $_POST['only'] == 'Y')
			$val = ' checked="checked"';
		else
			$val = '';
?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="action" value="Update" />
			</td>
		</tr>
	</tbody>
</table>
<?php
	}

	//-------------------------------------------------------------------------

	function subsubmit()
	{
		global $Routes;
		global $err;

		populate_routes();
		if ($err < ERR_SUCCESS)
			return '';

		populate_types();
		if ($err < ERR_SUCCESS)
			return '';

		// Determine whether to show stops
		if (isset($_POST['stops']) && $_POST['stops'] == 'Y')
			$showStops = true;
		else
			$showStops = false;

		// If showing stops, validate date
		$date = 0;
		if ($showStops)
		{
			$when = valid_date('when', 'Date');
			if ($err < ERR_SUCCESS)
				return '';
			$date = strtotime($when);
		}

		$divider = '<form>'
				. '<input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" />'
				. '</form>';

		$RID = intval($_POST['rid']);
		if ($RID == 0)
		{
			reset($Routes);
			$first = true;
			foreach($Routes as $id => $route)
			{
				if ($first)
				{
					$html = gen_status($id, $showStops, $date);
					$first = false;
				}
				else
					$html .= '<div>' . gen_status($id, $showStops, $date) . '</div>';
			}
			return $divider . $html;
		}
		else
			return $divider . gen_status($RID, $showStops, $date);
	}

?>
