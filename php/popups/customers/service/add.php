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

	define('PAGE', SV_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/regex.inc.php';
	require_once 'inc/popups/customers.inc.php';

	//-------------------------------------------------------------------------
	// Handle commands
	$errorList = array();
	$custInfo = '';
	if (isset($_REQUEST['action']))
	{
		if ($_REQUEST['action'] == 'Add')
		{
			// Arrays of fields
			$fields = array();
			$errorList = array();

			// Created
			$fields['created'] = 'NOW()';

			// Customer ID
			if (preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['cid']))
            {
				$CID = intval($_REQUEST['cid']);
				$fields['customer_id'] = $CID;
            }
			else
				$errorList[] = 'Customer ID is invalid';

			// Period
			if ($_REQUEST['period'] == '0')
				$fields['period_id'] = 'NULL';
			else
				$fields['period_id'] = intval($_REQUEST['iid']);

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
			$fields['type'] = "'" . $_REQUEST['what'] . "'";

			// Ignore On Bill?
			$fields['ignoreOnBill'] = "'" . $_REQUEST['ignoreOnBill'] . "'";

			// Why
			$fields['why'] = "'" . db_escape(stripslashes($_REQUEST['why'])) . "'";

			// Notes
			$fields['note'] = "'" . db_escape(stripslashes($_REQUEST['notes'])) . "'";

			// Add record if needed
			$count = count($errorList);
			if ($count > 0)
			{
				if ($count > 1)
					$message = '<span>Errors prevented adding stop/start</span>';
				else
					$message = '<span>Error prevented adding stop/start</span>';
			}
			else
			{
				do
				{
					// Start transaction
					if (!db_query(SQL_TRANSACTION))
						break;

					// Add change
					$id = db_insert('customers_service', $fields);
				} while (false);

				// Finish up
				if ($err >= ERR_SUCCESS)
				{
					db_query(SQL_COMMIT);
					$message = '<span>Stop/start added successfully.</span>';
					$temp = '';
					foreach($fields as $field => $val)
					{
						if (strval($val) == 'NOW()')
							$temp .= $field . ' is \'' . strftime('%m/%d/%Y %H:%M:%S', time()) . '\'. ';
						else
							$temp .= $field . ' is ' . $val . '. ';
					}
					audit('Added stop/start ' . sprintf('%08d', $id) . '. ' . $temp);
				}
				else
				{
					// Undo everything
					db_query(SQL_ROLLBACK);

					// Let user know it failed
					$message = '<span>Stop/start creation failed!</span>';
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

	echo gen_htmlHeader('Add Stop / Start', $style, $script);

?>
	<body>
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
					<td>Customer ID</td>
					<td>
<?php
	if (isset($_GET['id']))
		$_REQUEST['cid'] = $_GET['id'];
	if (isset($_REQUEST['cid']))
		$val = intval($_REQUEST['cid']);
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
	if (isset($_REQUEST['period']))
		$val = $_REQUEST['period'];
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
	if (isset($_REQUEST['iid']))
		$val = intval($_REQUEST['iid']);
	else
		$val = get_config('billing-period', 0);
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
		$_REQUEST['whenm'] = date('m');
		$_REQUEST['whend'] = date('d');
		$_REQUEST['wheny'] = date('Y');
	}
	echo gen_dateField('when', '', true);
?>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
<?php
	$temp = array
			(
				SERVICE_START => '',
				SERVICE_STOP => ''
			);
	if (isset($_REQUEST['what']))
		$temp[$_REQUEST['what']] = ' checked="checked"';
	else
		$temp[SERVICE_START] = ' checked="checked"';
?>
						<span>
							<input type="radio" name="what" value="<?php echo SERVICE_START ?>"<?php echo $temp[SERVICE_START] ?>>Start</input>
							<input type="radio" name="what" value="<?php echo SERVICE_STOP ?>"<?php echo $temp[SERVICE_STOP] ?>>Stop</input>
						</span>
					</td>
				</tr>
				<tr>
					<td>Include in Billing</td>
					<td>
<?php
	if (isset($_REQUEST['ignoreOnBill']))
		$val = $_REQUEST['ignoreOnBill'];
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
					<td>Reason</td>
					<td>
<?php
	if (isset($_REQUEST['why']))
		$val = htmlspecialchars(stripslashes($_REQUEST['why']));
	else
		$val = 'Customer Request';
?>
						<input name="why" type="text" size="40" value="<?php echo $val ?>" />
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
						<input type="submit" name="action" value="Add" />
					</td>
				</tr>
			</table>
<?php
	if (isset($_GET['cid']))
		$val = intval($_GET['cid']);
	else
		$val = 0;
?>
			<input type="hidden" name="id" value="<?php echo $val ?>" />
		</form>
<?php
    gen_htmlFooter();
?>
