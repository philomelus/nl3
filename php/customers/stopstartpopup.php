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

    define('ROOT', '../');
	set_include_path('..' . PATH_SEPARATOR . get_include_path());
	
	require_once 'inc/security.inc.php';
	
	define('PAGE', SCW_STOPSTART);
	
	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/popups/customers/service.inc.php';
	require_once 'inc/sql.inc.php';

	//-------------------------------------------------------------------------
	
	$customer = lup_customer(intval($_REQUEST['cid']));
	
	populate_routes();
	populate_types();
	
	$resultHtml = '';

    $context = "Add Service Change";
    
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Submit')
	{
        $stop = false;
        if (isset($_REQUEST['addStop']) && $_REQUEST['addStop'] == 'AddStop')
        {
            $stop = true;
            $context = "Add Stop";
        }
        $start = false;
        if (isset($_REQUEST['addStart']) && $_REQUEST['addStart'] == 'AddStart')
        {
            $start = true;
            if ($stop)
                $context = "Add Stop/Start";
            else
                $context = "Add Start";
        }

		do
		{
			// Validate stop date
			if ($stop)
			{
				$stopText = valid_date("dateStop", "stop date");
				if ($err < ERR_SUCCESS)
				{
					$errContext = $context;
					break;
				}
                $stopDate = strtotime($stopText);

                // Make sure stop date is sensible
				if ($stopDate <= $Period[P_END])
				{
					$err = ERR_FAILURE;
					$errCode = ERR_FAILURE;
					$errContext = $context;
					$errQuery = '';
					$errText = 'Stop date affects prior bill period';
					break;
				}
			}

			// Validate start date
			if ($start)
			{
				$startText = valid_date("dateStart", "start date");
				if ($err < ERR_SUCCESS)
				{
					$errContext = $context;
					break;
				}
                $startDate = strtotime($startText);

                // Make sure start date is sensible
				if ($startDate <= $Period[P_END])
				{
					$err = ERR_FAILURE;
					$errCode = ERR_FAILURE;
                    $errContext = $context;
					$errQuery = '';
					$errText = 'Start date affects previous period';
					break;
				}
			}
			
			// Make sure the date(s) are logical
			if ($start && $stop)
			{
				if ($startDate < $stopDate)
				{
					$err = ERR_FAILURE;
					$errCode = ERR_FAILURE;
                    $errContext = $context;
					$errQuery = '';
					$errText = 'Start occurrs before stop';
					break;
				}
				if ($startDate == $stopDate)
				{
					$err = ERR_FAILURE;
					$errCode = ERR_FAILURE;
                    $errContext = $context;
					$errQuery = '';
					$errText = 'Stop and Start are the same date';
					break;
				}
			}
			
			// Start transaction
			if (!db_query(SQL_TRANSACTION))
				break;
		
            // Add service stop record if needed (and no errors have occurred;
            // should never be the case, alas ... be prepared ...)
			if ($stop && $err >= ERR_SUCCESS)
            {
				// Add new service record
				$hid = db_insert('customers_service', array
					(
                        'period_id' => 'NULL',
						'created' => 'NOW()',
						'customer_id' => $_REQUEST['cid'],
						'type' => '\'' . SERVICE_STOP . '\'',
						'when' => '\'' . strftime('%Y-%m-%d', $stopDate) . '\'',
						'ignoreOnBill' => '\'N\'',
						'why' => '\'' . db_escape(stripslashes($_REQUEST['why'])) . '\'',
						'note' => '\'' . db_escape(stripslashes($_REQUEST['notes'])) . '\''
                    ));
                // On failure, $hid will be eval to false and $err* vars will be set
				if ($hid)
				{
					$resultHtml .= '<div>Service stop for '
							. strftime('%m-%d-%Y', $stopDate) . ' added.</div>';
					audit('customers_service: New stop (' . sprintf('%d', $hid)
							. ') for customer ' . sprintf('%d', $customer->id) . '. '
							. 'when = ' . strftime('%Y-%m-%d', $stopDate) . '. '
							. 'why = \'' . stripslashes($_REQUEST['why']) . '\'. '
							. 'note = \'' . stripslashes($_REQUEST['notes']) . '\'.');
				}
			}
		
			// Add service start record if needed and no errors have occurred
			if ($start && $err >= ERR_SUCCESS)
            {
                // Add new service record
				$hid = db_insert('customers_service', array
					(
                        'period_id' => 'NULL',
						'created' => 'NOW()',
						'customer_id' => $_REQUEST['cid'],
						'type' => '\'' . SERVICE_START . '\'',
						'when' => '\'' . strftime('%Y-%m-%d', $startDate) . '\'',
						'ignoreOnBill' => '\'N\'',
						'why' => '\'' . db_escape(stripslashes($_REQUEST['why'])) . '\'',
						'note' => '\'' . db_escape(stripslashes($_REQUEST['notes'])) . '\''
					));
				if ($hid)
				{
					$resultHtml .= '<div>Service start for '
							. strftime('%m-%d-%Y', $startDate) . ' added.</div>';
					audit('customers_service: New start (' . sprintf('%d', $hid)
							. ') for customer ' . sprintf('%d', $customer->id) . '. '
							. 'when = ' . strftime('%Y-%m-%d', $startDate) . '. '
							. 'why = \'' . stripslashes($_REQUEST['why']) . '\'. '
							. 'note = \'' . stripslashes($_REQUEST['notes']) . '\'.');
				}
			}
			
			// Commit or dispose of the transaction depending on whether we succeeded
			if ($err >= ERR_SUCCESS)
			{
				// Save the change(s)
				db_query(SQL_COMMIT);
				
				// Reset the controls
				unset($_REQUEST['dateStopm']);
				unset($_REQUEST['dateStopd']);
				unset($_REQUEST['dateStartm']);
				unset($_REQUEST['dateStartd']);
				unset($_REQUEST['why']);
				unset($_REQUEST['notes']);
                unset($_REQUEST['addStop']);
                unset($_REQUEST['addStart']);
			}
			else
			{
				db_query(SQL_ROLLBACK);
				$resultHtml = '';
			}
		} while (false);
	}
	
	//-------------------------------------------------------------------------
	$script = '<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/popups/customers/service.js.php"></script>
<script type="text/javascript" src="js/stopstartpopup.js"></script> 
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('Add Stop/Start', $style, $script);

?>
    <body>
        <script language="JavaScript">
            pathToImages="../img/";
        </script>
		<h1>Add Stop/Start</h1>
<?php
		// If there has been an error, display it
		if ($err < ERR_SUCCESS)
			echo gen_error();

        // Display result of action if there was one already
		echo $resultHtml;
	
		// Generate customer information
		$color = 'background-color: #' . sprintf('%06X', $DeliveryTypes[$customer->type_id]['color']) . ';';
		echo '<div>'
				. '<div>'
				. '<table>'
				. '<thead><tr><td>Customer</td></tr></thead>'
				. '<tr>'
				. '<td>' . valid_name($customer->firstName, $customer->lastName) . '</td>'
				. '</tr>'
				. '<tr>'
				. '<td>' . $customer->address . '</td>'
				. '</tr>'
				. '<tr>'
				. '<td>' . valid_text($Routes[$customer->route_id]) . '</td>'
				. '</tr>'
				. '<tr>'
				. '<td>' . $DeliveryTypes[$customer->type_id]['abbr'] . '</td>'
				. '</tr>'
				. '</table>'
				. '</div>';
		
        // Get list of changes
        // TODO:  This is supposed to be PENDING changes, not LAST 4 CHANGES.
        //        Should be as simple as adding NULL period id check (heh heh,
        //        right...).
		$query = "SELECT * FROM `customers_service` WHERE `customer_id` = " . $customer->id
				. " ORDER BY `when` DESC, `created` DESC, `updated` DESC LIMIT 4";
		$changes = db_query($query);
		if ($changes)
			$count = $changes->num_rows;
		else
			$count = 0;
		
		// Add in change list table header		
		echo '<div>'
				. '<table>'
				. '<thead>'
				. '<tr><td colspan="3">Pending Changes</td></tr>'
				. '</thead>'
				. '<tbody>';
		
		// Add the change list body
		if ($changes)
		{
			if ($count > 0)
			{
				while ($change = $changes->fetch_object())
				{
					$hid = sprintf('%08d', $change->id);
					$alt = ' change ' . $hid;
					echo '<tr>'
							. '<td>'
							. ServiceViewLink($change->id, '../')
							. '<img src="../img/view.png" alt="V" title="View' . $alt . '" />'
							. '</a>'
							. '</td>'
							. '<td>';
					if ($change->type == SERVICE_STOP)
						echo '<span>STOP</span>';
					else
						echo '<span>START</span>';
					echo '</td>'
							. '<td>' . strftime("%m/%d/%Y", strtotime($change->when)) . '</td>'
							. '</tr>';
				}
			}
			else
			{
				echo '<tr><td colspan="3">None</td></tr>';
			}
		}
		else
		{
			echo '<tr><td colspan="3">'
					. 'Error prevented locatiing changes</td></tr>';
		}
		
		// Close the change list table
		echo '</tbody>'
				. '</table>'
				. '</div>'
				. '</div>';
?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
            <tr>
                <td>
                    <label for="addStop">Stop</label>
                </td>
                <td>
<?php
        $temp = '';
        if (isset($_REQUEST['addStop']) && $_REQUEST['addStop'] == 'AddStop')
            $temp = ' checked="checked"';
?>
                    <input type="checkbox" name="addStop" value="AddStop" onchange="addStopChanged()"<?php echo $temp; ?> />
                    <?php echo gen_dateField('dateStop', '', true, "dateStop"); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="addStart">Start</label>
                </td>
                <td>
<?php
        $temp = '';
        if (isset($_REQUEST['addStart']) && $_REQUEST['addStart'] == 'AddStart')
            $temp = ' checked="checked"';
?>
    <input type="checkbox" name="addStart" value="AddStart" onchange="addStartChanged()"<?php echo $temp; ?> />
                    <?php echo gen_dateField('dateStart', '', true, "dateStart"); ?>
                </td>
            </tr>
			<tr>
				<td>Reason</td>
				<td>
<?php
		if (isset($_REQUEST['why']))
			$why = stripslashes($_REQUEST['why']);
		else
			$why = '';
?>
					<input type="text" name="why" size="40" value="<?php echo $why ?>" placeholder="Customer Request" maxLength="254" />
				</td>
			</tr>
			<tr>
				<td rowspan="4">Notes</td>
				<td colspan="3">
<?php
		if (isset($_REQUEST['notes']))
			$notes = stripslashes($_REQUEST['notes']);
		else
			$notes = '';
?>
					<textarea name="notes" rows="4" cols="40"><?php echo $notes ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Submit" disabled="disabled" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="cid" value="<?php echo $_REQUEST['cid'] ?>" />
	</form>
<?php
    echo gen_htmlHeader();
?>
