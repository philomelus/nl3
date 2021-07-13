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

	define('PAGE', SW_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/popups/customers.inc.php';
	require_once 'inc/popups/customers/servicetypes.inc.php';

	if (!isset($_REQUEST['id']) || !preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['id']))
	{
		echo invalid_parameters('Edit Type Change', 'popups/customers/servicetype/edit.php');
		return;
	}
	$ID = intval($_REQUEST['id']);
	$serviceType = lup_c_service_type($ID);

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

			// Change ID
			$fields['id'] = $ID;

			if ($_POST['period'] == '0')
			{
				if (!is_null($serviceType->period_id))
					$fields2['period_id'] = 'NUll';
			}
			else
			{
				$temp = intval($_POST['iid']);
				if ($temp != $serviceType->period_id)
					$fields2['period_id'] = $temp;
			}

			// When
			$temp = valid_date('when', 'When');
			if ($err >= ERR_SUCCESS)
			{
				$temp = strtotime($temp);
				if ($temp != strtotime($serviceType->when))
					$fields2['when'] = "'" . strftime('%Y-%m-%d', $temp) . "'";
			}
			else
			{
				$errorList[] = $errText;
				$err = ERR_SUCCESS;
			}

			// type_id_from / type_id_to
			$from = $_REQUEST['oldType'];
			$to = $_REQUEST['newType'];
			if ($from != $to)
			{
				if ($from != $serviceType->type_id_from)
					$fields2['type_id_from'] = $_REQUEST['oldType'];
				if ($to != $serviceType->type_id_to)
					$fields2['type_id_to'] = $_REQUEST['newType'];
			}
			else
				$errorList[] = 'Old Type and New Type cannot be the same';

			// Ignore On Bill
			$temp = $_REQUEST['ignoreOnBill'];
			if ($temp != $serviceType->ignoreOnBill)
				$fields2['ignoreOnBill'] = "'" . $temp . "'";

			// Why
			$temp = stripslashes($_REQUEST['why']);
			if ($temp != $serviceType->why)
				$fields2['why'] = "'" . db_escape($temp) . "'";

			// Notes
			$temp = stripslashes($_REQUEST['notes']);
			if ($temp != $serviceType->note)
				$fields2['note'] = "'" . db_escape($temp) . "'";

			// Add record if needed
			$count = count($errorList);
			if ($count > 0)
			{
				if ($count > 1)
					$message = '<span>Errors prevented updating Type Change</span>';
				else
					$message = '<span>Error prevented updating Type Change</span>';
			}
			else if (count($fields2) > 0)
			{
				do
				{
					// Start transaction
					if (!db_query(SQL_TRANSACTION))
						break;

					// Update change
					db_update('customers_service_types', $fields, $fields2);
				} while (false);

				// Finish up
				if ($err >= ERR_SUCCESS)
				{
					db_query(SQL_COMMIT);
					$message = '<span>Type change updated successfully.</span>';
					$idstr = sprintf('%08d', $ID);
					audit('Updated type change ' . $idstr . '. ' . audit_update_o($fields2, $serviceType));
					$serviceType = lup_c_service_type($ID);
				}
				else
				{
					db_query(SQL_ROLLBACK);
					$message = '<span>Update of type change failed!</span>';
				}
			}
			else
				$message = '<span>No Changes Made</span>';
		}
	}

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/calendar.js"></script>
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
<script type="text/javascript" src="../../../js/popups/customers/servicetypes.js.php"></script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('Edit Type Change', $style, $script);

?>
	<body>
<?php
	// Display error(s) if needed
	if (count($errorList) > 0)
	{
		foreach ($errorList as $error)
			echo $error . '<br />';
		echo '<hr >';
	}
	else
	{
		if ($err < ERR_SUCCESS)
			echo gen_error(true, true);
	}

	// Display message if needed
	if (isset($message) && !empty($message))
	{
		echo '<div>'
			. $message
			. '<br />'
			. '</div>';
	}

?>
<script language="JavaScript">
	pathToImages="../../img/";
</script>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
			<table>
				<tr>
					<td>Type Change ID</td>
					<td>
<?php
	echo ServiceTypeViewLink($serviceType->id, '../../../') . sprintf('%08d', $ID) . '</a>';
?>
					</td>
				</tr>
				<tr>
					<td>Customer ID</td>
					<td>
<?php
	echo CustomerViewLink($serviceType->customer_id, '../../../') . sprintf('%06d', $serviceType->customer_id) . '</a>';
?>
					</td>
				</tr>
				<tr>
					<td>Created</td>
					<td>
<?php
	echo strftime('%m/%d/%Y %H:%M:%S', strtotime($serviceType->created));
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
		if (is_null($serviceType->period_id))
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
        if (!is_null($serviceType->period_id))
            $val = intval($serviceType->period_id);
        else
            $val = 0;
		if ($val == 0)
			$val = get_config('billing-period');
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
		$when = strtotime($serviceType->when);
		$_REQUEST['whenm'] = date('m', $when);
		$_REQUEST['whend'] = date('d', $when);
		$_REQUEST['wheny'] = date('Y', $when);
	}
	echo gen_dateField('when', '', true);
?>
					</td>
				</tr>
				<tr>
					<td>Old Type</td>
					<td>
<?php
	if (isset($_REQUEST['oldType']))
		$val = intval($_REQUEST['oldType']);
	else
		$val = $serviceType->type_id_from;
	echo gen_typeSelect('oldType', $val, false, '', array(), '');
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
		$val = $serviceType->type_id_to;
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
		$val = $serviceType->ignoreOnBill;
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
		$val = htmlspecialchars(stripslashes($serviceType->why));
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
		$val = htmlspecialchars(stripslashes($serviceType->note));
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
    gen_htmlFooter();
?>
