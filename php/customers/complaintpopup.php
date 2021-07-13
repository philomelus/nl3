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

	set_include_path('..' . PATH_SEPARATOR . get_include_path());
	
	require_once 'inc/security.inc.php';
	
	define('PAGE', SCW_COMPLAINT);
	
	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/popups/customers/complaints.inc.php';
	
	//-------------------------------------------------------------------------
	
	$customer = lup_customer(intval($_REQUEST['cid']));
	
	populate_routes();
	
	populate_types();
	$message = '';
	
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Submit')
	{
		$fields = array();
		
		do
		{
			$whenString = valid_date('when', 'date');
			if ($err < ERR_SUCCESS)
				break;
            $fields['period_id'] = 'NULL';
			$fields['when'] = "'" . strftime('%Y-%m-%d', strtotime($whenString)) . "'";
			if (isset($_REQUEST['ignore']) && intval($_REQUEST['ignore']) == 1)
				$fields['ignoreOnBill'] = "'N'";
			else
				$fields['ignoreOnBill'] = "'Y'";
			$fields['type'] = "'" . $_POST['what'] . "'";
			$fields['customer_id'] = $customer->id;
			$fields['result'] = "'" . $_POST['result'] . "'";
			$fields['amount'] = 0;
			$fields['why'] = "'" . db_escape(stripslashes($_POST['why'])) . "'";
			$fields['note'] = "'" . db_escape(stripslashes($_POST['notes'])) . "'";
			$fields['created'] = 'NOW()';
			$result = db_insert('customers_complaints', $fields);
		} while (false);
		if ($err >= ERR_SUCCESS)
		{
			$message = '<span>Added complaint successfully.</span>';
			audit('Added Complaint (id = ' . sprintf('%08d', $result) . ') for customer '
					. sprintf('%06d', $customer->id) . '. '
					. 'when = ' . strftime('%Y-%m-%d', strtotime($whenString)) . '. '
					. 'type = ' . $_POST['what'] . '. '
					. 'ignoreOnBill = ' . (intval($_REQUEST['ignore']) == 1 ? 'FALSE' : 'TRUE') . '. '
					. 'result = ' . $_POST['result'] . '. '
					. 'amount = $0.00. '
					. 'why = \'' . stripslashes($_POST['why']) . '\'. '
					. 'note = \'' . stripslashes($_POST['notes']) . '\'.');
		}
		else
		{
			$message = '<span>Complaint creation failed!</span>';
			$errContext = 'Adding complaint';
		}
	}
	
	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/popups/customers/complaints.js.php"></script>
<script type="text/javascript">
</script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('Add Start / Stop', $style, $script);

?>
	<body><script language="JavaScript">pathToImages="../img/";</script>
		<h1>Add Complaint</h1>
<?php
		// Display message if set
		if (!empty($message))
			echo '<div>' . $message . '</div>';
			
		// If there has been an error, display it
		if ($err < ERR_SUCCESS)
			echo gen_error();
		
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
		$query = "SELECT * FROM `customers_complaints` WHERE `customer_id` = " . $customer->id
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
				. '<tr><td colspan="4">Prior Complaints</td></tr>'
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
							. ComplaintViewLink($change->id, '../')
							. '<img src="../img/view.png" alt="View' . $alt . '" title="View' . $alt . '" />'
							. '</a>'
							. '</td>'
							. '<td>';
					switch ($change->type)
					{
					case BITCH_MISSED:	echo 'Missed';	break;
					case BITCH_WET:		echo 'Wet';		break;
					case BITCH_DAMAGED:	echo 'Damaged';	break;
					default:			echo '<span???</span>';	break;
					}
					echo '</td>'
							. '<td>' . strftime("%m/%d/%Y", strtotime($change->when)) . '</td>'
							. '<td>';
					switch ($change->result)
					{
					case RESULT_NOTHING:		echo 'No Action';	break;
					case RESULT_CREDIT1DAILY:
					case RESULT_CREDIT1SUNDAY:
					case RESULT_CREDIT:		echo 'Credit';		break;
					case RESULT_REDELIVERED:	echo 'Redelivered';	break;
					case RESULT_CHARGE:		echo 'Charged';		break;
					default:				echo '<span>???</span>';	break;
					}
					echo '</td></tr>';
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
					. 'Error prevented locatiing complaints</td></tr>';
		}
		
		// Close the change list table
		echo '</tbody>'
				. '</table>'
				. '</div>'
				. '</div>';

		// Array for complaint type
		$COMPLAINTS = array
		(
			BITCH_MISSED => 'Missed Paper',
			BITCH_WET => 'Wet Paper',
			BITCH_DAMAGED => 'Damaged Paper'
		);
		
		// Array for complaint result
		$RESULT = array
		(
			RESULT_NOTHING => 'Do Nothing',
			RESULT_CREDIT1DAILY => 'Credit 1 Daily',
			RESULT_CREDIT1SUNDAY => 'Credit 1 Sunday',
//			RESULT_CREDIT => 'Credit',
			RESULT_REDELIVERED => 'Redelivered'
		);
		
?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
			<table>
				<tr>
					<td>Date</td>
					<td>
<?php
		echo gen_dateField('when');
?>
					</td>
				</tr>
				<tr>
					<td>Issue</td>
					<td>
<?php
		echo gen_select('what', $COMPLAINTS, isset($_REQUEST['what']) ? $_REQUEST['what'] : BITCH_MISSED, false, '', '');
?>
					</td>
				</tr>
				<tr>
					<td>Result</td>
					<td>
<?php
		echo gen_select('result', $RESULT, isset($_REQUEST['result']) ? $_REQUEST['result'] : RESULT_NOTHING, false, '', '');
?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
<?php
	if (isset($_REQUEST['ignore']) && intval($_REQUEST['ignore']) == 1)
		$temp = '';
	else
		$temp = ' checked="checked"';
?>
						<input type="checkbox" name="ignore" value="1"<?php echo $temp ?>>Include in Billing?</input>
					</td>
				</tr>
				<tr>
					<td>Cause</td>
					<td>
<?php
		if (isset($_REQUEST['why']))
			$val = stripslashes($_REQUEST['why']);
		else
			$val = '';
?>
						<input type="text" name="why" value="<?php echo $val ?>" maxLength="254" />
					</td>
				</tr>
				<tr>
					<td>Note</td>
					<td>
<?php
		if (isset($_REQUEST['notes']))
			$val = htmlspecialchars(stripslashes($_REQUEST['notes']));
		else
			$val = '';
?>
						<textarea name="notes" rows="4" cols="40"><?php echo $val ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Submit" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="cid" value="<?php echo $_REQUEST['cid'] ?>" />
		</form>
<?php
    echo gen_htmlHeader();
?>
