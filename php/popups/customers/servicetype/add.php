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

	define('PAGE', SW_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/popups/customers.inc.php';

	if (isset($_GET['id']))
		$_POST['cid'] = $_GET['id'];

	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Add Type Change', $errText);
		return;
	}

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
				$CID = intval($_REQUEST['cid']);
			else
				$errorList[] = 'Customer ID is invalid';
			if (isset($CID))
			{
				$fields['customer_id'] = $CID;
				$customer = lup_customer($CID);
				if ($err < ERR_SUCCESS)
					$errorList[] = $errText;
			}

			// Period
			if ($_POST['period'] == '0')
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

			// Type ID From
			if (isset($customer) && !empty($customer))
				$fields['type_id_from'] = $customer->type_id;

			// Type ID To
			$fields['type_id_to'] = intval($_REQUEST['newType']);
			if ($fields['type_id_from'] == $fields['type_id_to'])
				$errorList[] = 'Old Type and New Type cannot be the same.';

			// Ignore On Bill
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
					$message = '<span>Errors prevented adding type change.</span>';
				else
					$message = '<span>Error prevented adding type change.</span>';
			}
			else
			{
				do
				{
					// Start transaction
					if (!db_query(SQL_TRANSACTION))
						break;

					// Add change
					$id = db_insert('customers_service_types', $fields);
					if (!$id)
						break;

					// BUGBUG:  This shouldn't take effect until the scheduled date, but sometimes a route list
					//          with the change already applied needs to be printed.  What needs to be done is the
					//          reports need to be able to handle generating a report for a _specific_ date, which
					//          would provide the context for the change to be shown correctly.  However, there
					//          currently isn't a way to apply the change later...
					db_update('customers', array('id' => $customer->id), array('type_id' => $fields['type_id_to']));
				} while (false);

				// Finish up
				if ($err >= ERR_SUCCESS)
				{
					db_query(SQL_COMMIT);
					$message = '<span>Type Change created successfully!</span>';
					$temp = '';
					foreach($fields as $field => $val)
					{
						if (strval($val) == 'NOW()')
							$temp .= $field . ' is \'' . strftime('%m/%d/%Y %H:%M:%S', time()) . '\'. ';
						else
							$temp .= $field . ' is ' . $val . '. ';
					}
					audit('Added Type Change ' . sprintf('%08d', $id) . '. ' . $temp);
					unset($customer);
				}
				else
				{
					db_query(SQL_ROLLBACK);
					$message = '<span>Type change creation failed!</span>';
				}
			}
		}
	}

	if (isset($_REQUEST['cid']) && intval($_REQUEST['cid']) > 0
			&& !isset($customer))
	{
		populate_types();
		$customer = lup_customer(intval($_REQUEST['cid']));
		if ($err < ERR_SUCCESS)
			unset($customer);
	}

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/calendar.js"></script>
<script type="text/javascript" src="../../../js/printf.js"></script>
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
<script type="text/javascript" src="../../../js/popups/customers/servicetypes.js.php"></script>
<script language="JavaScript">
	function ViewCustomer()
	{
		var c = document.getElementById("cid");
		if (c)
		{
			CustomerViewPopup(\'../../Customers/View.php?cid=\' + c.value,
					printf(\'VC%06d\', c.value));
		}
	}
</script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('Add Type Change', $style, $script);

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
		$_REQUEST['cid'] = $_GET['id'];;
	if (isset($_REQUEST['cid']) && intval($_REQUEST['cid']) > 0)
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
	echo gen_dateField('when');
?>
					</td>
				</tr>
				<tr>
					<td>Old Type</td>
					<td>
<?php
	if (isset($customer) && !empty($customer))
		echo $DeliveryTypes[$customer->type_id]['abbr'];
	else
		echo 'Unknown';
?>
					</td>
				</tr>
				<tr>
					<td>New Type</td>
					<td>
<?php
	if (isset($_REQUEST['newType']))
		$val = intval($_REQUEST['newType']);
	else
		$val = 0;
	echo gen_typeSelect('newType', $val, false, '', array(), '');
?>
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
						<input type="text" name="why" size="40" value="<?php echo $val ?>" />
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
	if (isset($_REQUEST['cid']))
		$val = intval($_REQUEST['cid']);
	else
		$val = '';
?>
			<input type="hidden" name="id" value="<?php echo $val ?>" />
		</form>
<?php
    gen_htmlFooter();
?>
