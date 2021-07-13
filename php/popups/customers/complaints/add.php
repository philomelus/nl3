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

	define('PAGE', SQ_ADD);
	define('CONTEXT', 'Popup Complaint Add');

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/popups/customers.inc.php';

	//-------------------------------------------------------------------------
	// Handle commands
	$errorList = array();
	if (isset($_POST['action']))
	{
		if ($_POST['action'] == 'Add')
		{
			// Arrays of fields
			$fields = array();
			$errorList = array();

			// Created
			$fields['created'] = 'NOW()';

			// Customer ID
			if (preg_match('/^[[:digit:]]{1,8}$/', $_POST['cid']))
            {
				$CID = intval($_POST['cid']);
				$fields['customer_id'] = $CID;
            }
			else
				$errorList[] = 'Customer ID is invalid';

			// Period
			if ($_POST['period'] == '0')
				$fields['period_id'] = 'NULL';
			else
				$fields['period_id'] = intval($_POST['iid']);

			// When
			$when = valid_date('when', 'When');
			if ($err >= ERR_SUCCESS)
				$fields['when'] = "'" . strftime('%Y-%m-%d', strtotime($when)) . "'";
			else
			{
				$errorList[] = $errText;
				$err = ERR_SUCCESS;
			}

			// Type
			$fields['type'] = "'" . $_POST['what'] . "'";

			// Result / Amount
			$temp = $_POST['result'];
			$fields['result'] = "'" . $temp . "'";
			if ($temp == RESULT_CREDIT || $temp == RESULT_CHARGE)
			{
				if (preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_POST['amount']))
					$fields['amount'] = floatval($_POST['amount']);
				else
					$errorList[] = 'Invalid amount';
			}

			// Ignore On Bill?
			$fields['ignoreOnBill'] = "'" . $_POST['ignoreOnBill'] . "'";

			// Why
			$fields['why'] = "'" . db_escape(stripslashes($_POST['why'])) . "'";

			// Notes
			$fields['note'] = "'" . db_escape(stripslashes($_POST['notes'])) . "'";

			// Add record if needed
			$count = count($errorList);
			if ($count > 0)
			{
				if ($count > 1)
					$message = '<span>Errors prevented adding complaint.</span>';
				else
					$message = '<span>Error prevented adding complaint.</span>';
			}
			else
			{
				do
				{
					// Start transaction
					if (!db_query(SQL_TRANSACTION))
						break;

					// Add change
					$id = db_insert('customers_complaints', $fields);
				} while (false);

				// Finish up
				if ($err >= ERR_SUCCESS)
				{
					db_query(SQL_COMMIT);
					$message = '<span>Complaint added successfully!</span>';
					$temp = '';
					foreach($fields as $field => $val)
					{
						if (strval($val) == 'NOW()')
							$temp .= $field . ' is \'' . strftime('%m/%d/%Y %H:%M:%S', time()) . '\'. ';
						else
							$temp .= $field . ' is ' . $val . '. ';
					}
					audit('Added complaint ' . sprintf('%08d', $id) . '. ' . $temp);
				}
				else
				{
					db_query(SQL_ROLLBACK);
					$message = '<span>Adding complaint failed!</span>';
				}
			}
		}
	}

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/calendar.js"></script>
<script type="text/javascript" src="../../../js/printf.js"></script>
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
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
	function ViewCustomer()
	{
		var c = document.getElementById("cid");
		if (c)
		{
			CustomerViewPopup(\'cid=\' + c.value, \'../../../\');
		}
	}
</script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('Add Complaint', $style, $script);

?>
	<body><script language="JavaScript">pathToImages="../../img/";</script>
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
					<td>Customer ID</td>
					<td>
<?php
	if (isset($_GET['id']))
		$_POST['cid'] = $_GET['id'];
	if (isset($_POST['cid']))
		$val = intval($_POST['cid']);
	else
		$val = '';
?>
						<input type="text" name="cid" value="<?php echo $val ?>" size="6" maxLength="6" />
						<input type="submit" name="action" value="View" onclick="JavaScript:ViewCustomer(); return false;" />
					</td>
				</tr>
				<tr>
					<td>Bill</td>
					<td>
<?php
	if (isset($_POST['period']))
		$val = $_POST['period'];
	else
		$val = '0';
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
	if (isset($_POST['iid']))
		$val = intval($_POST['iid']);
	else
		$val = get_config('billing-period');
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
	if (!isset($_POST['whenm']))
	{
		$_POST['whenm'] = date('m');
		$_POST['whend'] = date('d');
		$_POST['wheny'] = date('Y');
	}
	echo gen_dateField('when', '', true);
?>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select name="what" onchange="JavaScript:ShowHideControls();">
<?php
	$temp = array
			(
				BITCH_MISSED => '',
				BITCH_WET => '',
				BITCH_DAMAGED => '',
			);
	if (isset($_POST['what']))
		$temp[$_POST['what']] = ' selected="selected"';
	else
		$temp[BITCH_MISSED] = ' selected="selected"';
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
	if (isset($_POST['result']))
		$temp[$_POST['result']] = ' selected="selected"';
	else
		$temp[RESULT_NOTHING] = ' selected="selected"';
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
	if (isset($_POST['amount']))
		$val = sprintf('%01.2f', floatval($_POST['amount']));
	else
		$val = '0.00';
?>
						$<input type="text" name="amount" value="<?php echo $val ?>" size="6" maxlength="6" />
					</td>
				</tr>
				<tr>
					<td>Include in Billing</td>
					<td>
<?php
	if (isset($_POST['ignoreOnBill']))
		$val = $_POST['ignoreOnBill'];
	else
		$val = 'N';
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
	if (isset($_POST['why']))
		$val = htmlspecialchars(stripslashes($_POST['why']));
	else
		$val = 'Customer Request';
?>
						<input name="why" size="40" value="<?php echo $val ?>" />
					</td>
				</tr>
				<tr>
					<td>Note</td>
					<td>
<?php
	if (isset($_POST['notes']))
		$val = htmlspecialchars(stripslashes($_POST['notes']));
	else
		$val = '';
?>
						<textarea name="notes" rows="4" cols="40"><?php echo $val ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Add" />
					</td>
				</tr>
			</table>
		</form>
<?php
    echo gen_htmlHeader();
?>
