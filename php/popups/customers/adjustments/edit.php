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

	set_include_path('../../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SD_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/errors.inc.php';
	require_once 'inc/database.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/popups/customers.inc.php';

	//-------------------------------------------------------------------------
	// Make sure we were passed required parameters
	if (!isset($_REQUEST['aid']))
	{
		echo invalid_parameters('Edit Adjustment', 'popups/customers/adjustments/edit.php');
		return;
	}
	$AID = intval($_REQUEST['aid']);

	//-------------------------------------------------------------------------
	// Get adjustment record
	$adjustment = lup_c_adjustment($AID);

	//-------------------------------------------------------------------------
	// Handle commands
	$errorList = array();
	if (isset($_REQUEST['action']))
	{
		if ($_REQUEST['action'] == 'Save Changes')
		{
			// Arrays of fields
			$fields = array();
			$fields2 = array();
			$errorList = array();
			$aduit = array();

			// AID
			$fields['id'] = $adjustment->id;

			// Period
			if ($_REQUEST['period'] == '0')
			{
				if (!is_null($adjustment->period_id))
				{
					$fields2['period_id'] = 'NULL';
					$audit['period_id'] = array(iid2title($adjustment->period_id, true), 'Pending');
				}
			}
			else
			{
                $temp = intval($_REQUEST['iid']);
				if ($temp != $adjustment->period_id)
				{
					$fields2['period_id'] = $temp;
					$audit['period_id'] = array((is_null($adjustment->period_id) ? 'Pending' : iid2title($adjustment->period_id)),
                            iid2title(intval($_REQUEST['iid']), true));
				}
			}

			// Description
			$temp = stripslashes($_REQUEST['desc']);
			if ($temp != stripslashes($adjustment->desc))
			{
				$fields2['desc'] = "'" . db_escape($temp) . "'";
				$audit['desc'] = array(stripslashes($adjustment->desc), $temp);
			}

			// Amount
			$temp = $_REQUEST['amount'];
			if (!empty($temp))
			{
				if (preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $temp))
				{
					if (floatval($temp) != $adjustment->amount)
					{
						$fields2['amount'] = floatval($temp);
						$audit['amount'] = array(sprintf('$%01.2f', $adjustment->amount),
								sprintf('$%01.2f', floatval($temp)));
					}
				}
				else
					$errorList[] = 'Amount is invalid';
			}
			else
			{
				if ($adjustment->amount != 0)
				{
					$fields2['amount'] = 0;
					$audit['amount'] = array(sprintf('$%01.2f', $adjustment->amount), '$0.00');
				}
			}

			// Notes
			$temp = stripslashes($_REQUEST['note']);
			if ($temp != stripslashes($adjustment->note))
			{
				$fields2['note'] = "'" . db_escape($temp) . "'";
				$audit['note'] = array(stripslashes($adjustment->note), $temp);
			}

			// Update record if needed
			if (count($errorList) > 0)
				$message = '<span>Error(s) prevented updating Adjustment</span>';
			else
			{
				if (count($fields2) > 0)
				{
					do
					{
						// Start transaction
						$result = db_query(SQL_TRANSACTION);
						if (!$result)
							break;

						// Update adjustment
						$aid = db_update('customers_adjustments', $fields, $fields2);
					} while (false);

					// Finish up
					if ($err >= ERR_SUCCESS)
					{
						// Update the database
						db_query(SQL_COMMIT);

						// Let user know it was successful
						$message = '<span>Adjustment ' . sprintf('A%08d', $aid)
								. ' updated successfully!</span>';

						audit('Updated adjustment (id = ' . sprintf('%08d', $aid) . '). '
								. audit_update($audit));
					}
					else
					{
						// Undo everything
						db_query(SQL_ROLLBACK);

						// Let user know it failed
						$message = '<span>Adjustment update failed!</span>';
					}
				}
				else
					$message = '<span>No Changes To Save</span>';
			}
		}
	}

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
';

	//-------------------------------------------------------------------------
	$style = '';

	$AIDSTR = sprintf('%08d', $AID);
	echo gen_htmlHeader('Edit Adjustment ' . $AIDSTR, $style, $script);

?>
	<body>
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
					<td>Adjustment ID</td>
					<td><?php echo $AIDSTR ?></td>
				</tr>
				<tr>
					<td>Customer ID</td>
					<td>
						<span>
<?php
	echo CustomerViewLink($adjustment->customer_id, '../../../') . sprintf('%06d', $adjustment->customer_id) . '</a>';
?>
						</span>
					</td>
				</tr>
				<tr>
					<td>Period</td>
					<td>
<?php
	if (isset($_REQUEST['period']))
		$val = $_REQUEST['period'];
	else
	{
		if (is_null($adjustment->period_id))
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
		if (is_null($adjustment->period_id))
			$val = get_config('billing-period');
		else
			$val = $adjustment->period_id;
	}
?>
						<span>
							<input type="radio" name="period" value="0"<?php echo $temp[0] ?>>Pending</input>
							<input type="radio" name="period" value="1"<?php echo $temp[1] ?>>Billed</input>
						</span>
						in
<?php
	echo gen_periodsSelect('iid', $val, false, '', ' ');
?>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>
<?php
	if (isset($_REQUEST['desc']))
		$val = htmlspecialchars(stripslashes($_REQUEST['desc']));
	else
		$val = htmlspecialchars(stripslashes($adjustment->desc));
?>
						<input type="text" name="desc" value="<?php echo $val ?>" size="40" maxlength="40" />
					</td>
				</tr>
				<tr>
					<td>Amount</td>
					<td>
<?php
	if (isset($_REQUEST['amount']))
		$val = sprintf('%01.2f', floatval($_REQUEST['amount']));
	else
		$val = sprintf('%01.2f', floatval($adjustment->amount));
?>
						$<input type="text" name="amount" value="<?php echo $val ?>" size="8" />
					</td>
				</tr>
				<tr>
					<td>Note</td>
					<td>
<?php
	if (isset($_REQUEST['note']))
		$val = htmlspecialchars(stripslashes($_REQUEST['note']));
	else
		$val = htmlspecialchars(stripslashes($adjustment->note));
?>
						<textarea name="note" rows="4" cols="40"><?php echo $val ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Save Changes" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="aid" value="<?php echo $AID ?>" />
		</form>
<?php
    echo gen_htmlHeader();
?>
