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

	define('PAGE', SP_EDIT);

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
	if (!isset($_REQUEST['pid']))
	{
		echo invalid_parameters('Edit Payment', 'Customers/Payments/Edit.php');
		return;
	}
	$PID = intval($_REQUEST['pid']);

	//-------------------------------------------------------------------------
	// Get payment record
	$payment = lup_c_payment($PID);

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
			$audit = array();

			// Payment ID
			$fields['id'] = $PID;

			// Period
			$temp = intval($_REQUEST['iid']);
			if ($temp != intval($payment->period_id))
			{
				$fields2['period_id'] = $temp;
				$audit['period_id'] = array(iid2title($payment->period_id) . ' (id = '
						. sprintf('%08d', $payment->period_id) . ')',
						iid2title($temp) . ' (id = ' . sprintf('%08d', $temp) . ')');
			}

			// Type
			$temp = stripslashes($_REQUEST['type']);
			if ($temp != stripslashes($payment->type))
			{
				$fields2['type'] = "'" . db_escape($temp) . "'";
				$audit['type'] = array($payment->type, $temp);
			}

			// ID
			$temp = stripslashes($_REQUEST['id']);
			if ($temp != stripslashes($payment->extra1))
			{
				$fields2['extra1'] = "'" . db_escape($temp) . "'";
				$audit['extra1'] = array($payment->extra1, $temp);
			}

			// Amount
			if (preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_REQUEST['amount']))
			{
				$temp = floatval($_REQUEST['amount']);
				if ($temp != $payment->amount)
				{
					$fields2['amount'] = $temp;
					$audit['amount'] = array(sprintf('$%01.2f', $payment->amount), sprintf('$%01.2f', $temp));
				}
			}
			else
				$errorList[] = 'Amount is invalid';

			// Tip
			if (preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_REQUEST['tip']))
			{
				$temp = floatval($_REQUEST['tip']);
				if ($temp != $payment->tip)
				{
					$fields2['tip'] = $temp;
					$audit['tip'] = array(sprintf('$%01.2f', $payment->tip), sprintf('$%01.2f', $temp));
				}
			}
			else
				$errorList[] = 'Tip is invalid';

			// Note
			$temp = stripslashes($_REQUEST['note']);
			if ($temp != stripslashes($payment->note))
			{
				$fields2['note'] = "'" . db_escape($temp) . "'";
				$audit['note'] = array(stripslashes($payment->amount), $temp);
			}

			// Update record
			if (count($errorList) > 0)
				$message = '<span>Error(s) prevented updating Payment</span>';
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

						// Update change
						$pid = db_update('customers_payments', $fields, $fields2);
					} while (false);

					// Finish up
					if ($err >= ERR_SUCCESS)
					{
						// Update the database
						db_query(SQL_COMMIT);

						// Let user know it was successful
						$message = '<span>Payment updated successfully!</span>';

						audit('Updated payment ' . sprintf('%08d', $PID) . '. '
								. audit_update($audit));
					}
					else
					{
						// Undo everything
						db_query(SQL_ROLLBACK);

						// Let user know it failed
						$message = '<span>Payment update failed!</span>';
					}
				}
				else
					$message = '<span>No Changes Made</span>';
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

	$PIDSTR = sprintf('P%08d', $PID);
	echo gen_htmlHeader('Edit Payment ' . $PIDSTR, $style, $script);

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
					<td>PID</td>
					<td>
						<span><?php echo $PIDSTR ?></span>
					</td>
				</tr>
				<tr>
					<td>CID</td>
					<td>
<?php
		echo CustomerViewLink($payment->customer_id, '../../../') . sprintf('C%06d', $payment->customer_id) . '</a>';
?>
					</td>
				</tr>
				<tr>
					<td>Period</td>
					<td>
<?php
	if (isset($_REQUEST['iid']))
		$val = intval($_REQUEST['iid']);
	else
		$val = $payment->period_id;
?>
<?php
	echo gen_periodsSelect('iid', $val, false, '', '');
?>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select name="type">
<?php
	if (isset($_REQUEST['type']))
		$val = $_REQUEST['type'];
	else
		$val = $payment->type;
	$temp = array
			(
				'CHECK' => '',
				'MONEYORDER' => '',
				'CASH' => ''
			);
	$temp[$val] = ' selected="selected"';
?>
							<option value="CHECK"<?php echo $temp['CHECK'] ?>>Check</option>
							<option value="MONEYORDER"<?php echo $temp['MONEYORDER'] ?>>Money Order</option>
							<option value="CASH"<?php echo $temp['CASH'] ?>>Cash</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>ID</td>
					<td>
<?php
	if (isset($_REQUEST['id']))
		$val = $_REQUEST['id'];
	else
		$val = htmlspecialchars(stripslashes($payment->extra1));
?>
						<input type="text" name="id" value="<?php echo $val ?>" size="20" maxlength="30" />
					</td>
				</tr>
				<tr>
					<td>Amount</td>
					<td>
<?php
	if (isset($_REQUEST['amount']))
		$val = sprintf('%01.2f', floatval($_REQUEST['amount']));
	else
		$val = sprintf('%01.2f', $payment->amount);
?>
						$<input type="text" name="amount" value="<?php echo $val ?>" size="8" />
					</td>
				</tr>
				<tr>
					<td>Tip</td>
					<td>
<?php
	if (isset($_REQUEST['tip']))
		$val = sprintf('%01.2f', floatval($_REQUEST['tip']));
	else
		$val = sprintf('%01.2f', $payment->tip);
?>
						$<input type="text" name="tip" value="<?php echo $val ?>" size="8" />
					</td>
				</tr>
				<tr>
					<td>Note</td>
					<td>
<?php
	if (isset($_REQUEST['note']))
		$val = htmlspecialchars(stripslashes($_REQUEST['note']));
	else
		$val = htmlspecialchars(stripslashes($payment->note));
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
			<input type="hidden" name="pid" value="<?php echo $PID ?>" />
		</form>
<?php
    gen_htmlFooter();
?>
