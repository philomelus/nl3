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

	set_include_path('../../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('CONTEXT', 'Popup Complaint Edit');
	define('PAGE', SQ_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/popups/customers.inc.php';
	require_once 'inc/popups/customers/complaints.inc.php';

	if (!isset($_REQUEST['id']) || !preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['id']))
	{
		echo invalid_parameters('Edit Complaint', 'popups/complaints/edit.php');
		return;
	}

	$ID = $_REQUEST['id'];
	$complaint = lup_c_complaint($ID);

	// Determine which optional fields need to be available
	$showAmount = false;
	switch ($complaint->result)
	{
	case RESULT_NOTHING:
	case RESULT_CREDIT1DAILY:
	case RESULT_CREDIT1SUNDAY:
	case RESULT_REDELIVERED:
		// Default setting is correct
		break;

	case RESULT_CREDIT:
	case RESULT_CHARGE:
		$showAmount = true;
		break;
	}

	//-------------------------------------------------------------------------
	// Handle commands
	$errorList = array();
	$custInfo = '';
	if (isset($_REQUEST['action']))
	{
		if ($_REQUEST['action'] == 'Save Changes')
		{
			// Arrays of fields
			$fields = array();
			$fields2 = array();
			$errorList = array();

			// Complaint ID
			$fields['id'] = $ID;

			// Period
			if ($_REQUEST['period'] == '0')
			{
				if (!is_null($complaint->period_id))
					$fields2['period_id'] = 'NULL';
			}
			else
			{
				$temp = intval($_REQUEST['iid']);
				if ($temp != $complaint->period_id)
					$fields2['period_id'] = $temp;
			}

			// When
			$temp = valid_date('when', 'When');
			if ($err >= ERR_SUCCESS)
			{
				$temp = strtotime($temp);
				if ($temp != strtotime($complaint->when))
					$fields2['when'] = "'" . strftime('%Y-%m-%d', $temp) . "'";
			}
			else
			{
				$errorList[] = $errText;
				$err = ERR_SUCCESS;
			}

			// Type
			$temp = $_REQUEST['what'];
			if ($temp != $complaint->type)
				$fields2['type'] = "'" . $temp . "'";

			// Result
			$temp = $_REQUEST['result'];
			if ($temp != $complaint->result)
				$fields2['result'] = "'" . $temp . "'";

			// Amount
			$temp = floatval($_REQUEST['amount']);
			if ($temp != $complaint->amount)
			{
				switch ($_REQUEST['result'])
				{
				case RESULT_NOTHING:
				case RESULT_CREDIT1DAILY:
				case RESULT_CREDIT1SUNDAY:
				case RESULT_REDELIVERED:
					if ($complaint->amount != 0)
						$fields2['amount'] = 0;
					break;

				case RESULT_CREDIT:
				case RESULT_CHARGE:
					if (preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_REQUEST['amount']))
						$fields2['amount'] = $temp;
					else
						$errorList[] = 'Invalid amount';
					break;
				}
			}

			// Ignore On Bill
			$temp = $_REQUEST['ignoreOnBill'];
			if ($temp != $complaint->ignoreOnBill)
				$fields2['ignoreOnBill'] = "'" . $temp . "'";

			// Why
			$temp = stripslashes($_REQUEST['why']);
			if ($temp != $complaint->why)
				$fields2['why'] = "'" . db_escape($temp) . "'";

			// Notes
			$temp = stripslashes($_REQUEST['notes']);
			if ($temp != $complaint->note)
				$fields2['note'] = "'" . db_escape($temp) . "'";

			// Add record if needed
			if (count($errorList) > 0)
			{
				if ($count > 1)
					$message = '<span>Errors prevented updating Complaint</span>';
				else
					$message = '<span>Error prevented updating Complaint</span>';
			}
			else if (count($fields2) > 0)
			{
				do
				{
					// Start transaction
					if (!db_query(SQL_TRANSACTION))
						break;

					// Update complaint
					db_update('customers_complaints', $fields, $fields2);
				} while (false);

				// Finish up
				if ($err >= ERR_SUCCESS)
				{
					db_query(SQL_COMMIT);
					$idstr = sprintf('%08d', $ID);
					$message = '<span>Complaint updated successfully.</span>';
					audit('Updated complaint ' . $idstr . '. ' . audit_update_o($fields2, $complaint));
					$complaint = lup_c_complaint($ID);
				}
				else
				{
					db_query(SQL_ROLLBACK);
					$message = '<span>Complaint update failed!</span>';
				}
			}
			else
				$message = '<span>No Changes required saving.</span>';
		}
	}

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/calendar.js"></script>
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
<script type="text/javascript" src="../../../js/popups/complaints.js.php"></script>
<script language="JavaScript">
	function ShowHideControls()
	{
		var result = document.getElementById(\'result\');
		c = document.getElementById(\'amountBlock\');
		if (result && c)
		{
			var rv = result.options[result.selectedIndex].value;
			if (rv == \'' . RESULT_CREDIT . '\' || rv == \'' . RESULT_CHARGE . '\')
				c.style.display = \'\';
			else
				c.style.display = \'none\';
		}
	}
</script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('Edit Complaint', $style, $script);

?>
	<body onload="ShowHideControls()">
		<script language="JavaScript">pathToImages="../../img/";</script>
<?php
	// Display message if needed
	if (isset($message) && !empty($message))
	{
		echo '<div>'
			. $message
			. '<br />'
			. '</div>';
	}

	// Display error(s) if needed
	if (count($errorList) > 0)
	{
		$html = gen_errorHeader();
		foreach ($errorList as $error)
			$html .= $error . '<br />';
		$html .= gen_errorFooter()
				. '<hr >';
		echo $html;
	}
	else
	{
		if ($err < ERR_SUCCESS)
			echo gen_error(true, true);
	}

?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
			<table>
				<tr>
					<td>Complaint ID</td>
					<td>
<?php
	echo ComplaintViewLink($ID, '../../../') . sprintf('%08d', $ID) . '</a>';
?>
					</td>
				</tr>
				<tr>
					<td>Customer ID</td>
					<td>
<?php
	echo CustomerViewLink($complaint->customer_id, '../../../') . sprintf('%06d', $complaint->customer_id) . '</a>';
?>
					</td>
				</tr>
				<tr>
					<td>Bill</td>
					<td>
<?php
	if (isset($_REQUEST['period']))
		$val = $_REQUEST['period'];
	else
	{
		if (is_null($complaint->period_id))
			$val = '0';
		else
			$val = '1';
	}
	if ($val == 0)
	{
		$temp = array
			(
				0 => ' checked="checked"',
				1 => ''
			);
	}
	else
	{
		$temp = array
			(
				0 => '',
				1 => ' checked="checked"'
			);
	}
	if (isset($_REQUEST['iid']))
		$val = intval($_REQUEST['iid']);
	else
    {
        if (is_null($complaint->period_id))
            $val = 0;
        else
            $val = intval($complaint->period_id);
    }
?>
						<span>
							<input type="radio" name="period" value="0"<?php echo $temp[0] ?>>Pending</input>
							<input type="radio" name="period" value="1"<?php echo $temp[1] ?>>Billed</input>
						</span>
						in
<?php
	echo gen_periodsSelect('iid', $val, false, '', '');
?>
					</td>
				</tr>
				<tr>
					<td>When</td>
					<td>
<?php
	if (!isset($_REQUEST['whenm']))
	{
		$when = strtotime($complaint->when);
		$_REQUEST['whenm'] = date('m', $when);
		$_REQUEST['whend'] = date('d', $when);
		$_REQUEST['wheny'] = date('Y', $when);
	}
	echo gen_dateField('when', '', true);
?>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select name="what" onchange="ShowHideControls()">
<?php
	$temp = array
			(
				BITCH_MISSED => '',
				BITCH_WET => '',
				BITCH_DAMAGED => ''
			);
	if (isset($_REQUEST['what']))
		$temp[$_REQUEST['what']] = ' selected="selected"';
	else
		$temp[$complaint->type] = ' selected="selected"';
?>
							<option value="<?php echo BITCH_MISSED ?>"<?php echo $temp[BITCH_MISSED] ?>>Missed Paper</option>
							<option value="<?php echo BITCH_WET ?>"<?php echo $temp[BITCH_WET] ?>>Wet Paper</option>
							<option value="<?php echo BITCH_DAMAGED ?>"<?php echo $temp[BITCH_DAMAGED] ?>>Damaged Paper</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Result</td>
					<td>
						<select name="result" onchange="ShowHideControls()">
<?php
	$temp = array
			(
				RESULT_NOTHING => '',
				RESULT_CREDIT1DAILY => '',
				RESULT_CREDIT1SUNDAY => '',
				RESULT_REDELIVERED => '',
				RESULT_CREDIT => '',
				RESULT_CHARGE => ''
			);
	if (isset($_REQUEST['result']))
		$temp[$_REQUEST['result']] = ' selected="selected"';
	else
		$temp[$complaint->result] = ' selected="selected"';
?>
							<option value="<?php echo RESULT_NOTHING ?>"<?php echo $temp[RESULT_NOTHING] ?>>None</option>
							<option value="<?php echo RESULT_CREDIT1DAILY ?>"<?php echo $temp[RESULT_CREDIT1DAILY] ?>>Credit 1 Daily</option>
							<option value="<?php echo RESULT_CREDIT1SUNDAY ?>"<?php echo $temp[RESULT_CREDIT1SUNDAY] ?>>Credit 1 Sunday</option>
							<option value="<?php echo RESULT_REDELIVERED ?>"<?php echo $temp[RESULT_REDELIVERED] ?>>Redelivered</option>
							<option value="<?php echo RESULT_CREDIT ?>"<?php echo $temp[RESULT_CREDIT] ?>>Credit</option>
							<option value="<?php echo RESULT_CHARGE ?>"<?php echo $temp[RESULT_CHARGE] ?>>Charge</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Amount</td>
					<td>
<?php
	if (isset($_REQUEST['amount']))
		$val = sprintf('%01.2f', floatval($_REQUEST['amount']));
	else
		$val = sprintf('%01.2f', floatval($complaint->amount));
?>
						$<input type="text" name="amount" value="<?php echo $val ?>" size="6" maxlength="6" />
					</td>
				</tr>
				<tr>
					<td>Include in Billing</td>
					<td>
<?php
	if (isset($_REQUEST['ignoreOnBill']))
		$val = $_REQUEST['ignoreOnBill'];
	else
		$val = $complaint->ignoreOnBill;
	if ($val == 'Y')
	{
		$temp = array
				(
					0 => '',
					1 => ' checked="checked"'
				);
	}
	else
	{
		$temp = array
				(
					0 => ' checked="checked"',
					1 => ''
				);
	}
?>
						<span>
							<input type="radio" name="ignoreOnBill" value="N"<?php echo $temp[0] ?>>Yes</input>
							<input type="radio" name="ignoreOnBill" value="Y"<?php echo $temp[1] ?>>No</input>
						</span>
					</td>
				</tr>
				<tr>
					<td>Cause</td>
					<td>
<?php
	if (isset($_REQUEST['why']))
		$val = htmlspecialchars(stripslashes($_REQUEST['why']));
	else
		$val = htmlspecialchars(stripslashes($complaint->why));
?>
						<input type="text" name="why" size="40" value="<?php echo $val ?>" ?>
					</td>
				</tr>
				<tr>
					<td>Note</td>
					<td>
<?php
	if (isset($_REQUEST['notes']))
		$val = htmlspecialchars(stripslashes($_REQUEST['notes']));
	else
		$val = htmlspecialchars(stripslashes($complaint->note));
?>
						<textarea name="notes" rows="4" cols="40"><?php echo $val ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Save Changes" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $ID ?>" />
		</form>
<?php
    echo gen_htmlHeader();
?>
