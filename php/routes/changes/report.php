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

	function subdisplay()
	{
		global $smarty;
		$smarty->display("menu.tpl");

?>
		<table>
			<tr>
				<td>&nbsp;</td>
				<td>
					<label>Date</label>
<?php
		if (isset($_REQUEST['changesm']) && isset($_REQUEST['changesd']) && isset($_REQUEST['changesy']))
			$date = strtotime(valid_date('changes', 'Date'));
		else
			$date = strtotime('+1 day', time());
		$_REQUEST['changesm'] = date('n', $date);
		$_REQUEST['changesd'] = date('j', $date);
		$_REQUEST['changesy'] = date('Y', $date);
		echo gen_dateField('changes');
?>
				</td>
				<td>
					<input type="submit" name="action" value="Generate" />
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

		populate_routes();
		populate_types();

		// Determine date for report(s)
		$datetext = valid_date('changes', 'Date');
		if ($err < ERR_SUCCESS)
			return '';
		$date = strtotime($datetext);

		// Build array of rid's to create report for
		reset($Routes);
		foreach($Routes as $rid => $name)
			$rids[] = $rid;

		// Global note
		$query = "SELECT `note` FROM `routes_changes_notes` WHERE `date` = '" . strftime('%Y-%m-%d', $date) . "'"
				. " AND `route_id` <=> NULL";
		$note = db_query_result($query);
		if ($note)
			$_REQUEST['notes'] = $note;

		// Route specific notes
		reset($rids);
		foreach($rids as $rid)
		{
			$query = "SELECT `note` FROM `routes_changes_notes` WHERE `date` = '" . strftime('%Y-%m-%d', $date) . "'"
					. " AND `route_id` = " . $rid;
			$note = db_query_result($query);
			if ($note)
				$_REQUEST['notes' . $rid] = $note;
		}

		// Build report for each rid
		$html = '';
		$firstPage = true;
		foreach ($rids as $rid)
		{
			// Title
			if ($firstPage)
			{
				$html .= '<div>';
				$firstPage = false;
			}
			else
				$html .= '<div>';

			$html .= '<div>'
					. 'Route ' . $Routes[$rid] . ' - ' . date('l - F, jS, Y', $date)
					. '</div>'
					. '<br />'
					. '<br />';

			// Get the stops for specified day
			$query =
"
SELECT
	`h`.*
FROM
	`customers_service` AS `h`,
	`routes_sequence` AS `s`,
	`customers` as `c`
WHERE
	`h`.`customer_id` = `s`.`tag_id`
	AND `h`.`customer_id` = `c`.`id`
	AND `h`.`when` = '" . strftime('%Y/%m/%d', $date) . "'
	AND `s`.`route_id` = " . $rid . "
	AND `h`.`type` = '" . SERVICE_STOP ."'
	AND `c`.`routeList` = 'Y'
ORDER BY
	`s`.`order`
";
			$changes = db_query($query);
			if ($changes)
			{
				$html .= '<table>'
				. '<caption>'
				. 'Stops for Today'
				. '</caption>'
				. '<tr>'
				. '<th>Name</th>'
				. '<th>Delivery Address</th>'
				. '<th>Type</th>'
				. '</tr>';
				if ($changes->num_rows != 0)
				{
					while ($change = $changes->fetch_object())
					{
						// Get the customers info
						$err = ERR_UNDEFINED;
						$customer = lup_customer($change->customer_id);
						if ($err < ERR_SUCCESS)
						{
							$html .= '<tr>'
									. '<td colspan="3">'
									. '<span>'
									. 'Unable to locate customer ' . sprintf('C%06d', $change->customer_id)
									. '</span>'
									. '</td>'
									. '</tr>';
							continue;
						}

						// Pre-generate a couple of items
						$color = 'background-color: #' . sprintf('%06X', $DeliveryTypes[$customer->type_id]['color']) . ';';

						// Generate the report stop
						$html .= '<tr>'
								. '<td>' . stripslashes(valid_name($customer->firstName, $customer->lastName)) . '</td>'
								. '<td>' . stripslashes($customer->address) . '</td>'
								. '<td>' . stripslashes($DeliveryTypes[$customer->type_id]['abbr']) . '</td>'
								. '</tr>';
					}
				}
				else
					$html .= '<td colspan="3">None</td>';
				$err = ERR_SUCCESS;
				$html .= '</table>'
				. '<!-- NEW STOPS END -->'
				. '<br />'
				. '<br />';
			}

			// Get the starts for specified day
			$query =
"
SELECT
	`h`.*
FROM
	`customers_service` AS `h`,
	`routes_sequence` AS `s`,
	`customers` as `c`
WHERE
	`h`.`customer_id` = `s`.`tag_id`
	AND `h`.`customer_id` = `c`.`id`
	AND `h`.`when` = '" . strftime('%Y/%m/%d', $date) . "'
	AND `s`.`route_id` = " . $rid . "
	AND `h`.`type` = '" . SERVICE_START . "'
	AND `c`.`routeList` = 'Y'
ORDER BY
	`s`.`order`
";
			$changes = db_query($query);
			if ($changes)
			{
				$html .= '<!-- NEW STARTS -->'
						. '<table>'
						. '<caption>Starts for Today</caption>'
						. '<tr>'
						. '<th>Name</th>'
						. '<th>Delivery Address</th>'
						. '<th>Type</th>'
						. '</tr>';
				if ($changes->num_rows != 0)
				{
					while ($change = $changes->fetch_object())
					{
						// Get the customers info
						$err = ERR_UNDEFINED;
						$customer = lup_customer($change->customer_id);
						if ($err < ERR_SUCCESS)
						{
							$html .= '<tr>'
									. '<td colspan="">'
									. '<span>'
									. 'Unable to locate customer ' . sprintf('C%06d', $change->customer_id)
									. '</span>'
									. '</td>'
									. '</tr>';
							continue;
						}

						// Pre-generate a couple of items
						$color = 'background-color: #' . sprintf('%06X', $DeliveryTypes[$customer->type_id]['color']) . ';';

						// Generate the report stop
						$html .= '<tr>'
								. '<td>' . stripslashes(valid_name($customer->firstName, $customer->lastName)) . '</td>'
								. '<td>' . stripslashes($customer->address) . '</td>'
								. '<td>' . stripslashes($DeliveryTypes[$customer->type_id]['abbr']) . '</td>'
								. '</tr>';
					}
				}
				else
					$html .= '<td colspan="3">None</td>';
				$err = ERR_SUCCESS;
				$html .= '</table>'
						. '<!-- NEW STARTS END -->'
						. '<br />'
						. '<br />';
			}

			// Get the service changes for specified day
			$query =
"
SELECT
	`h`.*
FROM
	`customers_service_types` AS `h`,
	`routes_sequence` AS `s`,
	`customers` as `c`
WHERE
	`h`.`customer_id` = `s`.`tag_id`
	AND `h`.`customer_id` = `c`.`id`
	AND `h`.`when` = '" . strftime('%Y/%m/%d', $date) . "'
	AND `s`.`route_id` = " . $rid . "
	AND `c`.`routeList` = 'Y'
ORDER BY
	`s`.`order`
";
			$changes = db_query($query);
			if ($changes)
			{
				if ($changes->num_rows != 0)
				{
					$html .= '<!-- SERVICE NOTICES BEGIN -->'
							. '<table>'
							. '<caption>Service Notice\'s for Today</caption>'
							. '<tr>'
							. '<th>Reason</th>'
							. '<th>Name</th>'
							. '<th>Delivery Address</th>'
							. '<th>Type</th>'
							. '</tr>';

					while ($change = $changes->fetch_object())
					{
						// Get the customers info
						$err = ERR_UNDEFINED;
						$customer = lup_customer($change->customer_id);
						if ($err < ERR_SUCCESS)
						{
							$html .= '<tr>'
									. '<td colspan="">'
									. '<span>'
									. 'Unable to locate customer ' . sprintf('C%06d', $change->customer_id)
									. '</span>'
									. '</td>'
									. '</tr>';
							continue;
						}

						// Pre-generate a couple of items
						$color = 'background-color: #' . sprintf('%06X', $DeliveryTypes[$customer->type_id]['color']) . ';';

						// Generate the report stop
						$html .= '<tr>'
								. '<td>Change Delivery Type</td>'
								. '<td>' . stripslashes(valid_name($customer->firstName, $customer->lastName)) . '</td>'
								. '<td>' . stripslashes($customer->address) . '</td>'
								. '<td>' . stripslashes($DeliveryTypes[$customer->type_id]['abbr']) . '</td>'
								. '</tr>';
					}

					$html .= '</table>'
							. '<!-- SERVICE NOTICES END -->'
							. '<br />'
							. '<br />';
				}
				$err = ERR_SUCCESS;
			}

			// Add in notes if provided
			if (!empty($_REQUEST['notes']) || !empty($_REQUEST['notes' . $rid]))
			{
				$html .= '<table>'
						. '<caption>Notes For Driver</caption>'
						. '<tr>';
				$count = 0;
				if (!empty($_REQUEST['notes']))
				{
					// Update report
					$html .= '<td>'
							. htmlspecialchars(stripslashes($_REQUEST['notes']))
							. '</td>';
					++$count;
				}
				if (!empty($_REQUEST['notes' . $rid]))
				{
					// Update report
					if ($count > 0)
					{
						$html .= '</tr>'
								. '<tr>';
					}
					$html .= '<td>'
							. htmlspecialchars(stripslashes($_REQUEST['notes' . $rid]))
							. '</td>';
				}
				$html .= '</tr>'
						. '</table>'
						. '<br />'
						. '<br />';
			}

			// Close up this route
			$html .= '</div>'
					. '<div><div></div></div>';
		}

		return '<form><input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" /></form>'
				. '<div></div>'
				. $html;
	}

?>
