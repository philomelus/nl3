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
		global $err, $errCode, $errContext, $errQuery, $errText, $smarty;

		$smarty->display("menu.tpl");

?>
		<table>
			<tr>
				<td>Date</td>
				<td>
<?php
		if (!isset($_REQUEST['whenm']))
		{
			$_REQUEST['whenm'] = date('n');
			$_REQUEST['whend'] = date('j');
			$_REQUEST['wheny'] = date('Y');
		}
		echo gen_dateField('when');
?>
				</td>
			</tr>
            <tr>
                <td>By</td>
                <td>
<?php
        if (!isset($_REQUEST['by']))
            $_POST['by'] = 'R';
        $temp = $_POST['by'];
        switch ($temp)
        {
        case 'P':
            $by = array(0 => '',
                        1 => ' checked="checked"');
            break;

        case 'R':
        default:
            $by = array(0 => ' checked="checked"',
                        1 => '');
            break;
        }

?>
                    <input type="radio" name="by" value="R"<?php echo $by[0]; ?>>Route</input>
                    <input type="radio" name="by" value="P"<?php echo $by[1]; ?>>Postal Code</input>
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
        $by = $_POST['by'];
        switch ($by)
        {
        case 'R':
            return byRoute();

        case 'P':
            return byPostal();

        default:
            return gen_error(false, false);
            
        }
    }

    //-------------------------------------------------------------------------

	function byRoute()
	{
		global $Routes;
		global $DeliveryTypes;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $Period;

		populate_routes();
		populate_types();

		// If showing stops, validate date
		$when = valid_date('when', 'Date');
		if ($err < ERR_SUCCESS)
			return '';
		$date = strtotime($when);

		// For each route
		$html = '';
		$draw = array();
		$day = date('D', $date);
		reset($Routes);
		$TotalTypes = array();
		foreach($Routes as $rid => $route)
		{
			// No draw yet
			$max = 0;
			$count = 0;
			$types = array();

			// Get the customers
			$query = '
SELECT
	`c`.`id`,
	`c`.`type_id`,
	`c`.`billStopped`
FROM
	`customers` AS `c`,
	`routes_sequence` AS `s`
WHERE
	`c`.`id` = `s`.`tag_id`
	AND `c`.`route_id` = `s`.`route_id`
	AND `c`.`routeList` = \'Y\'
	AND `c`.`route_id` = ' . $rid . '
ORDER BY
	`order`
';
			$customers = db_query($query);
			if ($customers)
			{
				while ($customer = $customers->fetch_object())
				{
					// Customer get delivered on specified day
					if ($DeliveryTypes[$customer->type_id][$day]['paper'])
					{
						// Add to or initialize types
						if (isset($types[$customer->type_id]))
							++$types[$customer->type_id]['max'];
						else
						{
							$types[$customer->type_id] = array
								(
									'max' => 1,
									'count' => 0
								);
						}
						if (isset($TotalTypes[$customer->type_id]))
							++$TotalTypes[$customer->type_id]['max'];
						else
						{
							$TotalTypes[$customer->type_id] = array
								(
									'max' => 1,
									'count' => 0
								);
						}

						// Locate changes for customer since last bill
						$query = "SELECT * FROM `customers_service` WHERE `customer_id` = " . $customer->id
								. " AND `when` <= '" . strftime('%Y-%m-%d', $date) . "'"
								. " AND `period_id` <=> NULL"
								. " AND `ignoreOnBill` = 'N' ORDER BY `when` ASC, `created` ASC, `updated` ASC";
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

						++$max;
						if ($what == SERVICE_START)
						{
							++$count;
							++$types[$customer->type_id]['count'];
							++$TotalTypes[$customer->type_id]['count'];
						}
					}
				}
				$err = ERR_SUCCESS;
			}

			$html .= '<div>'
					. '<table>'
					. '<caption>Route ' . $route . '</caption>'
					. '<tr>'
					. '<td>Total: ' . $max . '</td>'
					. '<td>Stopped: ' . ($max - $count). '</td>'
					. '<td>Draw: ' . $count . '</td>'
					. '</tr>';

			foreach($types as $tid => $values)
			{
				$html .= '<tr>'
						. '<td>' . $DeliveryTypes[$tid]['abbr'] . '</td>'
						. '<td>' . ($values['max'] - $values['count']) . '</td>'
						. '<td>' . $values['count'] . '</td>'
						. '</tr>';
			}

			$html .= '</table>'
					. '</div>';
		}

		// Calculate and format totals
		$total = $stopped = $draw = 0;
		$totals = '';
		foreach($TotalTypes as $tid => $values)
		{
			$totals .= '<tr>'
					. '<td>' . $DeliveryTypes[$tid]['abbr'] . '</td>'
					. '<td>' . ($values['max'] - $values['count']) . '</td>'
					. '<td>' . $values['count'] . '</td>'
					. '</tr>';
			$total += $values['max'];
			$stopped += ($values['max'] - $values['count']);
			$draw += $values['count'];
		}
		$totals .= '</table>'
				. '</div>';

		return '<form><input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" /></form>'
				. '<div></div>'
				. '<div>Draw for  ' . strftime('%m/%d/%Y', $date) . '</div>'
				. '<div>'
				. '<table>'
					. '<caption>Totals</caption>'
					. '<tr>'
						. '<td>Total: ' . $total . '</td>'
						. '<td>Stopped: ' . $stopped . '</td>'
						. '<td>Draw: ' . $draw . '</td>'
					. '</tr>'
				. $totals
				. $html;
	}

    //-------------------------------------------------------------------------

    function byPostal()
    {
        global $Routes;
        global $DeliveryTypes;
        global $err, $errCode, $errContext, $errQuery, $errText;
        global $Period;

        populate_routes();
        populate_types();

        // If showing stops, validate date
        $when = valid_date('when', 'Date');
        if ($err < ERR_SUCCESS)
            return '';
        $date = strtotime($when);

        // Set up $postalCodes with an array of all used zip
        // codes and:
        //     max papers for zip
        //     current papers for zip
        //     number of papers changing state
        // Basically, how many papers per zip code that need to be
        // added or removed from the count of papers for th zip
        // code.
        $query = '
SELECT
DISTINCT LEFT(`customers_addresses`.`zip`, 5) as `zip`
FROM
`customers_addresses`
INNER JOIN `customers` ON `customers_addresses`.`customer_id` = `customers`.`id`
WHERE
`customers`.`active` = \'Y\'
AND `customers`.`routeList` = \'Y\'
AND `customers_addresses`.`sequence` = ' . ADDR_C_DELIVERY . '
';
        $postalCodes = array();
        $result = db_query($query);
        if (!$result)
            return '';
        while ($zip = $result->fetch_object())
        {
            $postalCodes[$zip->zip] = array(
                'max' => 0,         // Total for zip
                'count' => 0,       // Total for date for zip
                'today' => 0,       // Total change on date for zip
                'stopped' => 0);    // Total stopped for zip (should be max - count)
        }

        // For each route
        $html = '';
        $draw = array();
        $day = date('D', $date);
        reset($Routes);
        $TotalTypes = array();
        foreach($Routes as $rid => $route)
        {
            // No draw yet
            $max = 0;
            $count = 0;
            $types = array();

            // Get the customers
            $query = '
SELECT
    `c`.`id`,
    `c`.`type_id`,
    `c`.`billStopped`,
    LEFT(`a`.`zip`, 5) AS `zip`
FROM
    `customers` AS `c`
    LEFT JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id`
WHERE
    `c`.`routeList` = \'Y\'
    AND `c`.`active` = \'Y\'
    AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
';
            $customers = db_query($query);
            if ($customers)
            {
                while ($customer = $customers->fetch_object())
                {
                    // If customer gets delivered on specified day
                    if ($DeliveryTypes[$customer->type_id][$day]['paper'])
                    {
                        // Locate changes for customer since last bill
                        $query = "SELECT * FROM `customers_service` WHERE `customer_id` = " . $customer->id
                            . " AND `when` <= '" . strftime('%Y-%m-%d', $date) . "'"
                            . " AND `period_id` <=> NULL"
                            . " AND `ignoreOnBill` = 'N' ORDER BY `when` ASC";
                        $changes = db_query($query);
                        if (!$changes)
                            return false;

                        // Determine starting delivery status
                        if ($customer->billStopped == 'Y')
                            $what = SERVICE_STOP;
                        else
                            $what = SERVICE_START;

                        // If there are actual changes
                        $changesToday = false;
                        if ($changes->num_rows > 0)
                        {
                            // Determine status is for specified date
                            while ($change = $changes->fetch_object())
                            {
                                // Is there a change of status?
                                // TODO: This logic isn't entirely correct,
                                //       as if there are multiple stops or starts
                                //       entered accidently, this will determine
                                //       the wrong result.  Not sure how to handle...
                                if ($change->type != $what)
                                {
                                    // Yeah, so start looking for other kind
                                    if ($what == SERVICE_STOP)
                                        $what = SERVICE_START;
                                    else
                                        $what = SERVICE_STOP;
                                }
                                if ($change->when == $date)
                                    $changesToday = true;
                            }
                        }

                        // They are counted as part of total for zip
                        ++$postalCodes[$customer->zip]['max'];

                        // If they are receiving paper, add to current
                        // count of papers for zip
                        if ($what == SERVICE_START)
                            ++$postalCodes[$customer->zip]['count'];
                        else // if $what == SERVICE_STOP
                            ++$postalCodes[$customer->zip]['stopped'];
                        
                        // If their status changed on the date, add to
                        // draw count change as well
                        if ($changesToday)
                            ++$postalCodes[$customer->zip]['today'];
                    }
                }
                $err = ERR_SUCCESS;
            }

        }

        $html .= '<div>'
            . '<table>'
            . '<tr>'
            . '<th>Zip</th>'
            . '<th>Today</th>'
            . '<th>Stopped</th>'
            . '<th>Current</th>'
            . '<th>Total</th>'
            . '</tr>';
        foreach ($postalCodes as $zip => $values)
        {
            $html .= '<tr>'
                . '<td>' . $zip . '</td>'
                . '<td>' . $values['today'] . '</td>'
                . '<td>' . $values['stopped'] . '</td>'
                . '<td>' . $values['count'] . '</td>'
                . '<td>' . $values['max'] . '</td>'
                . '</tr>';
        }
        $html .= '</table>'
            . '</div>';

        return '<form>'
                . '<input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" />'
                . '</form>'
                . '<div></div>'
                . '<div>Draw for  ' . strftime('%m/%d/%Y', $date) . '</div>'
                . $html;
    }
?>
