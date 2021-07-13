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
		
		if (!isset($_POST['count']))
			$_POST['count'] = 'one';
		if ($_POST['count'] == 'one')
			$count = array(0=>' checked="checked"', 1=>'');
		else
			$count = array(0=>'', 1=>' checked="checked"');
        $smarty->assign('count', $count);
        
		if (!isset($_POST['many']))
			$_POST['many'] = '2';
		$many = intval($_POST['many']);
        $smarty->assign('many', $many);

		$smarty->display("customers/reports/ahead.tpl");
	}
	
	function subsubmit()
	{
		global $err, $errContext;

		// BUGBUG:  This isn't truely accurate, as it doesn't handle a sequence
		// of periods where the rates change.
		
		populate_routes();
		
		$Types = gen_typesArray(get_config('billing-period'));

		if ($_POST['count'] == 'one')
		{
			$count = 1;
			$title = 'Customers Ahead 1 Period';
		}
		else
		{
			$count = intval($_POST['many']);
			$title = 'Customers Ahead ' . $count . ' Periods';
		}
		$FS = get_config('flag-stop-type', 0);
		$query =
'
SELECT
	`c`.`id`,
	`c`.`route_id`,
	`c`.`type_id`,
	`c`.`rateType`,
	`c`.`rateOverride`,
	`c`.`balance`,
	`c`.`billType`,
	`a`.`address1` AS `address`,
	`n`.`first`,
	`n`.`last`
FROM
	`customers` AS `c`,
	`customers_addresses` AS `a`,
	`customers_names` AS `n`,
	`routes_sequence` AS `s`
WHERE
	`c`.`id` = `a`.`customer_id`
	AND `c`.`id` = `n`.`customer_id`
	AND `c`.`id` = `s`.`tag_id`
	AND `c`.`route_id` = `s`.`route_id`
	AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
	AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
	AND `c`.`balance` < 0
	AND `c`.`active` = \'Y\'
	AND `c`.`type_id` != ' . $FS . '
ORDER BY
	' . db_route_field('`c`.`route_id`') . ' ASC,
	`s`.`order` ASC
';
		$records = db_query($query);
		if ($err < ERR_SUCCESS)
		{
			$errContext = 'Customers/Reports/Ahead';
			return gen_error();
		}
		$html = '';
		$total = 0;
		$totalall = 0;
		$totalCustomers = 0;
		while ($record = $records->fetch_object())
		{
			switch ($record->rateType)
			{
			case RATE_STANDARD:
				$rate = $Types[$record->billType]['rate'];
				break;
				
			case RATE_REPLACE:
				$rate = $record->rateOverride;
				break;
				
			case RATE_SURCHARGE:
				$rate = $Types[$record->billType]['rate']
						+ $record->rateOverride;
				break;
			}
			if ($record->balance <= -($count * $rate))
			{
				++$totalCustomers;
				$html .= '<tr>'
						. '<td>' . sprintf('%06d', $record->id) . '</td>'
						. '<td>' . valid_name($record->first, $record->last) . '</td>'
						. '<td>' . htmlspecialchars($record->address) . '</td>'
						. '<td>' . tid2abbr($record->type_id) . '</td>'
						. '<td>' . rid2title($record->route_id) . '</td>'
						. '<td>($' . sprintf('%01.2f', abs($record->balance)) . ') +' . sprintf('%02d', abs(intval($record->balance / $rate))) . '</td>'
						. '</tr>';
				$total += ($count * $rate);
				$totalall += $record->balance;
			}
		}
		$subtitle = sprintf('%d Customers', $totalCustomers);
		return '<form><input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" /></form>'
				. '<div</div>'
				. '<div>' . $title . '</div>'
				. '<div>' . $subtitle . '</div>'
				. '<table>'
				. '<thead>'
				. '<tr>'
				. '<th>CustID</th>'
				. '<th>Name</th>'
				. '<th>Address</th>'
				. '<th>Type</th>'
				. '<th>Rte</th>'
				. '<th>Balance</th>'
				. '</tr>'
				. '</thead>'
				. '<tbody>'
				. $html
				. '<tr><td colspan="5">Total (for specified period' . ($count > 1 ? 's' : '') . ')</td><td>$' . sprintf('%01.2f', $total) . '</td></tr>'
				. '<tr><td colspan="5">Total (all prepaid\'s)</td><td>$' . sprintf('%01.2f', abs($totalall)) . '</td></tr>'
				. '</tbody>'
				. '</table>';
	}

?>
