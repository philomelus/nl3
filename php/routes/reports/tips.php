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

	function by_period()
	{
		global $DeliveryTypes, $Routes, $errContext;

		// Build query
		$query =
"
SELECT
 `c`.`route_id`,
 `p`.`period_id`,
 `p`.`tip`,
 `p`.`customer_id`
FROM
 `customers` AS `c`,
 `customers_payments` AS `p`
WHERE
 `c`.`id` = `p`.`customer_id`
 AND `p`.`period_id` = " . $_REQUEST['iid'] . "
 AND `p`.`tip` > 0
";
		if (isset($_REQUEST['rid1']) && intval($_REQUEST['rid1']) > 0)
			$query .= " AND `c`.`route_id` = " . $_REQUEST['rid1'];
		$query .= " ORDER BY " . db_route_field('`c`.`route_id`') . ", `c`.`id`";
		$payments = db_query($query);
		if (!$payments)
		{
			$errContext = 'Routes/Reports/Tips';
			return gen_error();
		}

		$html = '';
		$first = true;
		$rid = 0;
		$gen = false;
		while ($payment = $payments->fetch_object())
		{
			if ($rid != $payment->route_id)
			{
				if (!$first)
				{
					$html .= '<tr><td colspan="6">Total</td><td>' . currency($total) . '</td></tr>'
							. '</tbody>'
							. '</table>';
					$pageClass = '';
				}
				else
				{
					$first = false;
					$pageClass = '';
				}
				$total = 0;
				$rid = $payment->route_id;

				$html .= '<div' . $pageClass . '>';
				if (isset($_REQUEST['rid1']) && intval($_REQUEST['rid1']) > 0)
					$html .= 'Route ' . $Routes[intval($_REQUEST['rid1'])] . ' ';
				$html .= 'Tips ' . iid2title($_REQUEST['iid']) . '</div>';

				if (isset($_REQUEST['rid1']) && intval($_REQUEST['rid1']) == 0)
					$html .= '<div>Route ' . $Routes[$rid] . '</div>';
				$html .= '<table>'
						. '<thead>'
						. '<tr>'
						. '<th>Period</th>'
						. '<th>CID</th>'
						. '<th>Name</th>'
						. '<th>Address</th>'
						. '<th>Telephone</th>'
						. '<th>Type</th>'
						. '<th>Tip</th>'
						. '</tr>'
						. '</thead>'
						. '<tbody>';
				$gen = true;
			}
			$customer = lup_customer($payment->customer_id);
			$color = 'background-color: #' . sprintf("%06X", $DeliveryTypes[$customer->type_id]['color']) . ';';
			$html .= '<tr>'
					. '<td>' . iid2title($payment->period_id) . '</td>'
					. '<td>' . sprintf('%06d', $payment->customer_id) . '</td>'
					. '<td>' . valid_name($customer->firstName, $customer->lastName) . '</td>'
					. '<td>' . valid_text($customer->address) . '</td>'
					. '<td>' . valid_text($customer->telephone) . '</td>'
					. '<td>' . $DeliveryTypes[$customer->type_id]['abbr'] . '</td>'
					. '<td>' . currency($payment->tip) . '</td>'
					. '</tr>';
			$total += $payment->tip;
		}
		$payments->close();
		if ($gen)
		{
			$html .= '<tr><td colspan="6">Total</td><td>' . currency($total) . '</td></tr>'
					. '</tbody>'
					. '</table>';
		}
		else
			$html .= '<div>None</div>';
		return '<form><input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" /></form>'
				. '<div></div>'
				. $html;
	}

	//-------------------------------------------------------------------------

	function by_periods()
	{
		global $DeliveryTypes;
		global $Routes;

		// TODO:  Lots of queries here when there only needs to be one
		
		// Build query
		$query = "SELECT `c`.`route_id`, `p`.`period_id`, `p`.`tip`, `p`.`customer_id` FROM `customers` as `c`, `customers_payments` as `p`"
				. " WHERE `c`.`id` = `p`.`customer_id`"
				. " AND `p`.`period_id` BETWEEN " . $_REQUEST['iidl'] . " AND " . $_REQUEST['iidr']
				. " AND `p`.`tip` > 0";
		if (isset($_REQUEST['rid2']) && intval($_REQUEST['rid2']) > 0)
			$query .= " AND `c`.`route_id` = " . $_REQUEST['rid2'];
		$query .= " ORDER BY " . db_route_field('`c`.`route_id`') . ", `c`.`id`";
		$payments = db_query($query);
		if (!$payments)
			return '';

		// Generate title
		$html = '<div>';
		if (isset($_REQUEST['rid2']) && intval($_REQUEST['rid2']) > 0)
			$html .= 'Route ' . $Routes[intval($_REQUEST['rid2'])] . ' ';
		$html .= 'Tips ' . iid2title($_REQUEST['iidl']);
		if (intval($_REQUEST['iidl']) != intval($_REQUEST['iidr']))
		 	$html .= ' - ' . iid2title($_REQUEST['iidr']);
		$html .= '</div>';
		$first = true;
		$rid = 0;
		$gen = false;
		while ($payment = $payments->fetch_object())
		{
			if ($rid != $payment->route_id)
			{
				if (!$first)
				{
					$html .= '<tr><td colspan="6">Total</td><td>' . currency($total) . '</td></tr>'
							. '</tbody>'
							. '</table>';
				}
				else
					$first = false;
				$total = 0;
				$rid = $payment->route_id;
				if (isset($_REQUEST['rid2']) && intval($_REQUEST['rid2']) == 0)
					$html .= '<div>Route ' . $Routes[$rid] . '</div>';
				$html .= '<table>'
						. '<thead>'
						. '<tr>'
						. '<th>Period</th>'
						. '<th>CID</th>'
						. '<th>Name</th>'
						. '<th>Address</th>'
						. '<th>Telephone</th>'
						. '<th>Type</th>'
						. '<th>Tip</th>'
						. '</tr>'
						. '</thead>'
						. '<tbody>';
				$gen = true;
			}
			$customer = lup_customer($payment->customer_id);
			$html .= '<tr>'
					. '<td>' . iid2title($payment->period_id) . '</td>'
					. '<td>' . sprintf('%06d', $customer->id) . '</td>'
					. '<td>' . valid_name($customer->firstName, $customer->lastName) . '</td>'
					. '<td>' . valid_text($customer->address) . '</td>'
					. '<td>' . valid_text($customer->telephone) . '</td>'
					. '<td>' . $DeliveryTypes[$customer->type_id]['abbr'] . '</td>'
					. '<td>' . currency($payment->tip) . '</td>'
					. '</tr>';
			$total += $payment->tip;
		}
		if ($gen)
		{
			$html .= '<tr><td colspan="6">Total</td><td>' . currency($total) . '</td></tr>'
					. '</tbody>'
					. '</table>';
		}
		else
			$html .= '<div>None</div>';
		return '<form><input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" /></form>'
				. '<div></div>'
				. $html;
	}

	//-------------------------------------------------------------------------

	function subdisplay()
	{
		global $smarty;

		if (!isset($_REQUEST['what']) || empty($_REQUEST['what']))
			$_REQUEST['what'] = 'iid';

		$smarty->display("menu.tpl");

?>
		<table>
			<tr>
				<td>
					<input type="radio" name="what" value="iid" <?php if ($_REQUEST['what'] == 'iid') echo 'checked="checked"'; ?>>Period</input>
				</td>
				<td>
<?php
		if (isset($_REQUEST['iid']) && intval($_REQUEST['iid']) > 0)
			$iid = intval($_REQUEST['iid']);
		else
			$iid = get_config('billing-period', 0);
		echo gen_periodsSelect('iid', $iid, false, '', '');
?>
				</td>
			</tr>
			<tr>
				<td />
				<td>
					Route(s)
<?php
		if (isset($_REQUEST['rid1']) && !empty($_REQUEST['rid1']))
			$rid = intval($_REQUEST['rid1']);
		else
			$rid = 0;
		echo gen_routesSelect('rid1', $rid, true, '', '');
?>
				</td>
			</tr>

			<tr><td colspan="2"><div></div></td></tr>
			<tr>
				<td>
					<input type="radio" name="what" value="iids" <?php if ($_REQUEST['what'] == 'iids') echo 'checked="checked"'; ?>>Periods</input>
				</td>
				<td>
					From
<?php
		if (isset($_REQUEST['iidl']) && intval($_REQUEST['iidl']) > 0)
			$iid = intval($_REQUEST['iidl']);
		else
			$iid = get_config('billing-period', 0);
		echo gen_periodsSelect('iidl', $iid, false, '', '');
?>
					To
<?php
		if (isset($_REQUEST['iidr']) && intval($_REQUEST['iidr']) > 0)
			$iid = intval($_REQUEST['iidr']);
		else
			$iid = get_config('billing-period', 0);
		echo gen_periodsSelect('iidr', $iid, false, '', '');
?>
				</td>
			</tr>
			<tr>
				<td />
				<td>
					Route(s)
<?php
		if (isset($_REQUEST['rid2']) && !empty($_REQUEST['rid2']))
			$rid = intval($_REQUEST['rid2']);
		else
			$rid = 0;
		echo gen_routesSelect('rid2', $rid, true, '', '');
?>
				</td>
			</tr>
		</table>
		<div>
			<input type="submit" name="action" value="Update" />
		</div>
<?php
	}

	//-------------------------------------------------------------------------

	function subsubmit()
	{
		global $err;

		populate_types();
		if ($err < ERR_SUCCESS)
			return '';
		populate_routes();
		if ($err < ERR_SUCCESS)
			return '';

		if ($_REQUEST['what'] == 'iid')
			return by_period();
		else //if ($_REQUEST['what'] == 'iids')
			return by_periods();
	}

?>
