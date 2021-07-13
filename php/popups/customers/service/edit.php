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

	define('PAGE', SV_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/regex.inc.php';
	require_once 'inc/popups/customers.inc.php';
	require_once 'inc/popups/customers/service.inc.php';

	if (!isset($_REQUEST['id']) || !preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['id']))
	{
		echo invalid_parameters('Edit Stop / Start', 'popups/customers/service/edit.php');
		return;
	}

	$ID = intval($_REQUEST['id']);
	$service = lup_c_service($ID);

	$errorList = array();
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

			// Period
            if (preg_match('/^[[:digit:]]$/', $_POST['period']))
            {
                if (preg_match('/^[[:digit:]]+$/', $_POST['iid']))
                {
                    $temp = intval($_POST['period']);
                    switch ($temp)
                    {
                    case 0:
                        if (!is_null($service->period_id))
                            $fields2['period_id'] = 'NULL';
                        break;

                    case 1:
                        $temp = intval($_POST['iid']);
                        if ($temp != $service->period_id)
                            $fields2['period_id'] = $temp;
                        break;

                    default:
                        $errorList[] = 'Invalid period status';
                        $err = ERR_SUCCESS;
                        break;
                    }
                }
                else
                {
                    $errorList[] = 'Invalid period';
                    $err = ERR_SUCCESS;
                }
            }
            else
            {
                $errorList[] = 'Period data corrupted';
                $err = ERR_SUCCESS;
            }

            // When
			$temp = valid_date('when', 'When');
			if ($err >= ERR_SUCCESS)
			{
				$temp = strtotime($temp);
				if ($temp != strtotime($service->when))
					$fields2['when'] = "'" . strftime('%Y-%m-%d', $temp) . "'";
			}
			else
			{
				$errorList[] = $errText;
				$err = ERR_SUCCESS;
			}

			// Type
			$temp = $_REQUEST['what'];
			if ($temp != $service->type)
				$fields2['type'] = "'" . $temp . "'";

			// Ignore On Bill
			$temp = $_REQUEST['ignoreOnBill'];
			if ($temp != $service->ignoreOnBill)
				$fields2['ignoreOnBill'] = "'" . $temp . "'";

			// Why
			$temp = stripslashes($_REQUEST['why']);
			if ($temp != $service->why)
				$fields2['why'] = "'" . db_escape($temp) . "'";

			// Notes
			$temp = stripslashes($_REQUEST['notes']);
			if ($temp != $service->note)
				$fields2['note'] = "'" . db_escape($temp) . "'";

			// Update record if needed
			$count = count($errorList);
			if ($count > 0)
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

					// Update change
					db_update('customers_service', $fields, $fields2);
				} while (false);

				// Finish up
				if ($err >= ERR_SUCCESS)
				{
					db_query(SQL_COMMIT);
					$message = '<span>Change updated successfully.</span>';
					$idstr = sprintf('%08d', $ID);
					audit('Updated start/stop (id = ' . $idstr . '). ' . audit_update_o($fields2, $service));
					$service = lup_c_service($ID);
				}
				else
				{
					db_query(SQL_ROLLBACK);
					$message = '<span>Stop/start update failed!</span>';
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
<script type="text/javascript" src="../../../js/popups/customers/service.js.php"></script>
<script language="JavaScript">
</script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('Edit Stop / Start', $style, $script);

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
					<td>Stop / Start ID</td>
					<td>
<?php
	echo ServiceViewLink($ID, '../../') . sprintf('%08d', $ID) . '</a>';
?>
					</td>
				</tr>
				<tr>
					<td>Customer ID</td>
					<td>
<?php
	echo CustomerViewLink($service->customer_id, '../../') . sprintf('%06d', $service->customer_id) . '</a>';
?>
					</td>
				</tr>
				<tr>
					<td>Created</td>
					<td>
<?php
	echo strftime('%m/%d/%Y %H:%M:%S', strtotime($service->created));
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
		if (is_null($service->period_id))
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
		if (is_null($service->period_id))
			$val = get_config('billing-period', 0);
		else
			$val = intval($service->period_id);
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
		$when = strtotime($service->when);
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
<?php
	$temp = array
			(
				SERVICE_START => '',
				SERVICE_STOP => '',
			);
	if (isset($_REQUEST['what']))
		$temp[$_REQUEST['what']] = ' checked="checked"';
	else
		$temp[$service->type] = ' checked="checked"';
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
		$val = $service->ignoreOnBill;
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
		$val = htmlspecialchars(stripslashes($service->why));
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
		$val = htmlspecialchars(stripslashes($service->note));
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
<?php
	if (isset($_GET['cid']))
		$val = intval($_GET['cid']);
	else if (isset($_REQUEST['orgcid']) && intval($_REQUEST['orgcid']) > 0)
		$val = intval($_REQUEST['orgcid']);
?>
			<input type="hidden" name="id" value="<?php echo $ID ?>" />
		</form>
<?php
    gen_htmlFooter();
?>
