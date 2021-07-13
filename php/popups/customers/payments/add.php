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

	define('PAGE', SP_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/errors.inc.php';
	require_once 'inc/database.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/popups/customers.inc.php';

	if (!isset($_REQUEST['cid']) || !preg_match('/^[[:digit:]]{0,6}$/', $_REQUEST['cid']))
	{
		echo invalid_parameters('Add Payment', 'Customers/Payments/Add.php');
		return;
	}

	$customer = lup_customer(intval($_REQUEST['cid']));

	$errorList = array();

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Add')
	{
		$fields = array();
		$errorList = array();

		$fields['created'] = 'NOW()';
		$fields['customer_id'] = $customer->id;
		$fields['period_id'] = intval($_REQUEST['iid']);
		$fields['type'] = "'" . $_REQUEST['type'] . "'";
		$fields['extra1'] = "'" . db_escape(stripslashes($_REQUEST['id'])) . "'";
		if (preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_REQUEST['amount']))
			$fields['amount'] = floatval($_REQUEST['amount']);
		else
			$errorList[] = 'Amount is invalid';
		if (preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_REQUEST['tip']))
			$fields['tip'] = floatval($_REQUEST['tip']);
		else
			$errorList[] = 'Tip is invalid';
		$fields['note'] = "'" . db_escape(stripslashes($_REQUEST['note'])) . "'";

		// Add record if needed
		if (count($errorList) > 0)
			$message = '<span>Error(s) prevented adding Payment</span>';
		else
		{
			// Add change
			$pid = db_insert('customers_payments', $fields);

			// Finish up
			if ($err >= ERR_SUCCESS)
			{
				// Let user know it was successful
				$message = '<span>Added payment <span>' . sprintf('P%08d', $pid)
						. '</span>!</span>';

				audit('Added payment (id = ' . sprintf('%08d', $pid) . '). ' . audit_add($fields));
			}
			else
			{
				// Let user know it failed
				$message = '<span>Add payment failed!</span>';
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

	echo gen_htmlHeader('Add Payment', $style, $script);

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
					<td>CID</td>
					<td>
<?php
	echo CustomerViewLink($customer->id, '../../../') . sprintf('C%06d', $customer->id) . '</a>';
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
		$val = get_config('billing-period', 0);
	echo gen_periodsSelect('iid', $val, false, '', '');
?>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select name="type">
<?php
	$temp = array
			(
				'CHECK' => '',
				'MONEYORDER' => '',
				'CASH' => ''
			);
	if (isset($_REQUEST['type']))
		$val = $_REQUEST['type'];
	else
		$val = 'CHECK';
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
		$val = '';
?>
						<input type="text" name="id" value="<?php echo $val ?>" size="30" maxlength="30" />
					</td>
				</tr>
				<tr>
					<td>Amount</td>
					<td>
<?php
	if (isset($_REQUEST['amount']))
		$val = sprintf('%01.2f', floatval($_REQUEST['amount']));
	else
		$val = '0.00';
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
		$val = '0.00';
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
		$val = '';
?>
						<textarea name="note" rows="4" cols="40"><?php echo $val ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Add" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="cid" value="<?php echo $customer->id ?>" />
		</form>
<?php
    gen_htmlFooter();
?>
