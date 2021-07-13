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

    // TODO:  There currently isn't a way for a user to reset next bill period
    // to NULL

	//-------------------------------------------------------------------------
	// Handle edit customer summary page display
	function display()
	{
		global $customer;
		global $DeliveryTypes;
		global $err;

?>
		<table>
			<tbody>
				<tr>
					<td>Billing Name</td>
					<td>
<?php
		$val = array(0 => '', 1 => '');
		if (isset($_REQUEST['bFirstName']) || isset($_REQUEST['bLastName']))
		{
			$val[0] = stripslashes($_REQUEST['bFirstName']);
			$val[1] = stripslashes($_REQUEST['bLastName']);
		}
		else
		{
			$temp = lup_c_name($customer->id, NAME_C_BILLING1);
			if ($temp)
			{
				$val[0] = stripslashes($temp->first);
				$val[1] = stripslashes($temp->last);
			}
			else
				$err = ERR_SUCCESS;
		}
?>
						<input type="text" name="bFirstName" value="<?php echo $val[0] ?>" size="20" maxLength="30" />
						<input type="text" name="bLastName" value="<?php echo $val[1] ?>" size="20" maxLength="30" />
					</td>
				</tr>
				<tr>
					<td>Billing Type</td>
<?php
	if (allowed('bill-type', SCE_SUMMARY))
	{
		echo '<td>';
		if (isset($_REQUEST['billType']))
			$val = intval($_REQUEST['billType']);
		else
			$val = $customer->billType;
		echo gen_typeSelect('billType', $val, false, '', array(), '');
		echo '</td>';
	}
	else
	{
		echo '<td>' . tid2abbr($customer->billType) . '</td>';
	}
?>
				</tr>
				<tr>
					<td>Status At Last Billing</td>
<?php
	if (allowed('bill-type', SCE_SUMMARY))
	{
		echo '<td>';
		if (isset($_REQUEST['billStopped']))
			$val = $_REQUEST['billStopped'];
		else
			$val = $customer->billStopped;
		if ($val == 'Y')
		{
			$delivered = '';
			$stopped = ' checked="checked"';
		}
		else
		{
			$delivered = ' checked="checked"';
			$stopped = '';
		}
		echo '<input type="radio" name="billStopped" value="N"' . $delivered . '>Delivered</input>'
				. '<input type="radio" name="billStopped" value="Y"' . $stopped . '>Stopped</input>'
				. '</td>';
	}
	else
	{
		echo '<td>';
		if ($customer->billStopped == 'Y')
			echo 'Stopped';
		else
			echo 'Delivered';
		echo '</td>';
	}
?>
				</tr>
				<tr>
					<td>Balance Last Billing</td>
<?php
	if (allowed('bill-type', SCE_SUMMARY))
	{
		echo '<td>';
		if (isset($_REQUEST['billBalance']))
			$val = floatval($_REQUEST['billBalance']);
		else
			$val = $customer->billBalance;
		$billBalance = sprintf('%01.2f', $val);
		echo '$<input type="text" name="billBalance" value="' . $billBalance . '" size="10" maxLength="20" />'
				. '</td>';
	}
	else
	{
		echo '<td>' .  sprintf('$%01.2f', $customer->billBalance) . '</td>';
	}
?>
				</tr>
				<tr>
					<td>Rate</td>
					<td>
<?php
		$val = array(RATE_STANDARD => '', RATE_REPLACE => '', RATE_SURCHARGE => '');
		if (isset($_REQUEST['rateType']))
			$val[$_REQUEST['rateType']] = ' selected="selected"';
		else
			$val[$customer->rateType] = ' selected="selected"';
		if (isset($_REQUEST['rateOverride']))
			$rateOverride = sprintf('%01.2f', floatval($_REQUEST['rateOverride']));
		else
			$rateOverride = sprintf('%01.2f', $customer->rateOverride);
		$rate = sprintf('$%01.2f', $DeliveryTypes[$customer->type_id]['rate']);
?>
						<select name="rateType">
							<option value="<?php echo RATE_STANDARD ?>"<?php echo $val[RATE_STANDARD] ?>>Standard</option>
							<option value="<?php echo RATE_REPLACE ?>"<?php echo $val[RATE_REPLACE] ?>>Override</option>
							<option value="<?php echo RATE_SURCHARGE ?>"<?php echo $val[RATE_SURCHARGE] ?>>Surcharge</option>
						</select>
						$<input type="text" name="rateOverride" value="<?php echo $rateOverride ?>" size="9" maxlength="9" />
						(Standard <?php echo $rate ?>)
					</td>
				</tr>
				<tr>
					<td>Current Bill Date Display</td>
					<td>
						<table>
							<tbody>
								<tr>
									<td>Start</td>
									<td>
<?php
		if (!isset($_REQUEST['billStartm']))
		{
			if (!empty($customer->billStart))
			{
				$temp = strtotime($customer->billStart);
				$_REQUEST['billStartm'] = date('m', $temp);
				$_REQUEST['billStartd'] = date('d', $temp);
				$_REQUEST['billStarty'] = date('Y', $temp);
			}
		}
		echo gen_dateField('billStart')
?>
									</td>
								</tr>
								<tr>
									<td>End</td>
									<td>
<?php
		if (!isset($_REQUEST['billEndm']))
		{
			if (!empty($customer->billEnd))
			{
				$temp = strtotime($customer->billEnd);
				$_REQUEST['billEndm'] = date('m', $temp);
				$_REQUEST['billEndd'] = date('d', $temp);
				$_REQUEST['billEndy'] = date('Y', $temp);
			}
		}
		echo gen_dateField('billEnd')
?>
									</td>
								</tr>
								<tr>
									<td>Due</td>
									<td>
<?php
		if (!isset($_REQUEST['billDuem']))
		{
			if (!empty($customer->billDue))
			{
				$temp = strtotime($customer->billDue);
				$_REQUEST['billDuem'] = date('m', $temp);
				$_REQUEST['billDued'] = date('d', $temp);
				$_REQUEST['billDuey'] = date('Y', $temp);
			}
		}
		echo gen_dateField('billDue')
?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>Next Billing</td>
					<td>
<?php
		if (isset($_POST['billPeriod']))
			$val = intval($_POST['billPeriod']);
		else
        {
            if (is_null($customer->billPeriod))
                $val = 0;
            else
                $val = $customer->billPeriod;
        }
		echo gen_periodsSelect('billPeriod', $val, false, '', '');
?>
					</td>
				</tr>
				<tr>
					<td>Bill Every</td>
					<td>
<?php
		if (isset($_REQUEST['billCount']))
			$val = intval($_REQUEST['billCount']);
		else
			$val = $customer->billCount;
?>
							<input type="text" name="billCount" value="<?php echo $val ?>" size="2" />&nbsp; period(s)
						</span>
					</td>
				</tr>
				<tr>
					<td>Quantity</td>
					<td>
<?php
		if (isset($_POST['billQuantity']))
			$val = intval($_POST['billQuantity']);
		else
			$val = $customer->billQuantity;
?>
							<input type="text" name="billQuantity" value="<?php echo $val ?>" size="2" />&nbsp; paper(s)
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Save Changes" />
					</td>
				</tr>
			</tbody>
		</table>
<?php
	}

	//-------------------------------------------------------------------------
	// Return edit customer summary page specific scripts
	function scripts()
	{
		return
'
<script language="JavaScript">
</script>
';
	}

	//-------------------------------------------------------------------------
	// Return edit customer summary page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle edit customer summary page submits
	function submit()
	{
		global $customer, $DB, $message, $resultHtml, $errorList, $Routes, $DeliveryTypes;
		global $err, $errCode, $errContext, $errQuery, $errText;

		// Get out if not saving changes
		if ($_REQUEST['action'] != 'Save Changes')
			return;

		// List of fields to update
		$fields = array();
		$queries = array();
		$audit = array();

		// Update billing name if needed
		$temp = update_name_query($customer->id, NAME_C_BILLING1,
				stripslashes($_REQUEST['bFirstName']),
				stripslashes($_REQUEST['bLastName']), $audit);
		if (!empty($temp))
			$queries[] = $temp;

		// Billing Type
		$temp = intval($_REQUEST['billType']);
		if ($temp != $customer->billType)
		{
			$fields['billType'] = $temp;
			$audit['billType'] = array($DeliveryTypes[$customer->billType]['abbr'] . ' (id = ' . sprintf('%04d', $customer->route_id) . ')',
					$DeliveryTypes[$temp]['abbr'] . ' (id = ' . sprintf('%04d', $temp) . ')');
		}

		// Billing status
		$temp = stripslashes($_REQUEST['billStopped']);
		if ($temp != stripslashes($customer->billStopped))
		{
			$fields['billStopped'] = "'" . db_escape($temp) . "'";
			$audit['billStopped'] = array(stripslashes($customer->billStopped), $temp);
		}

		// Balance on last bill
		$temp = floatval($_REQUEST['billBalance']);
		if ($temp != $customer->billBalance)
		{
			$fields['billBalance'] = $temp;
			$audit['billBalance'] = array(sprintf('$%01.2f', $customer->billBalance), sprintf('$%01.2f', $temp));
		}

		// rateType / rateOverride
		switch($_REQUEST['rateType'])
		{
		case RATE_STANDARD:
			if ($customer->rateType != RATE_STANDARD)
			{
				$fields['rateType'] = "'" . RATE_STANDARD . "'";
				$fields['rateOverride'] = 0;
				$_REQUEST['rateOverride'] = 0;
				$audit['rateType'] = array($customer->rateType, RATE_STANDARD);
				$audit['rateOverride'] = array(sprintf('$%01.2f', $customer->rateOverride), '$0.00');
			}
			break;

		case RATE_REPLACE:
			if ($customer->rateType != RATE_REPLACE
					|| floatval($_REQUEST['rateOverride']) != $customer->rateOverride)
			{
				$fields['rateType'] = "'" . RATE_REPLACE . "'";
				$fields['rateOverride'] = floatval($_REQUEST['rateOverride']);
				$audit['rateType'] = array($customer->rateType, RATE_REPLACE);
				$audit['rateOverride'] = array(sprintf('$%01.2f', $customer->rateOverride), '$0.00');
			}
			break;

		case RATE_SURCHARGE:
			if ($customer->rateType != RATE_SURCHARGE
					|| floatval($_REQUEST['rateOverride']) != $customer->rateOverride)
			{
				$fields['rateType'] = "'" . RATE_SURCHARGE . "'";
				$fields['rateOverride'] = floatval($_REQUEST['rateOverride']);
				$audit['rateType'] = array($customer->rateType, RATE_SURCHARGE);
				$audit['rateOverride'] = array(sprintf('$%01.2f', $customer->rateOverride), '$0.00');
			}
			break;
		}

		// Billing Start
		$temp = valid_date('billStart', 'Current Bill Display Date Start');
		if ($err >= ERR_SUCCESS)
		{
			$val = strtotime($temp);
			if ($val != strtotime($customer->billStart))
			{
				$fields['billStart'] = "'" . strftime('%Y-%m-%d', $val) . "'";
				$audit['billStart'] = array(strftime('%Y-%m-%d', strtotime($customer->billStart)), strftime('%Y-%m-%d', $val));
			}
		}
		else
		{
			$errorList[] = $errText;
			$err = ERR_SUCCESS;
		}

		// Billing End
		$temp = valid_date('billEnd', 'Current Bill Display Date End');
		if ($err >= ERR_SUCCESS)
		{
			$val = strtotime($temp);
			if ($val != strtotime($customer->billEnd))
			{
				$fields['billEnd'] = "'" . strftime('%Y-%m-%d', $val) . "'";
				$audit['billEnd'] = array(strftime('%Y-%m-%d', strtotime($customer->billEnd)), strftime('%Y-%m-%d', $val));
			}
		}
		else
		{
			$errorList[] = $errText;
			$err = ERR_SUCCESS;
		}

		// Billing Due
		$temp = valid_date('billDue', 'Current Bill Display Date Due');
		if ($err >= ERR_SUCCESS)
		{
			$val = strtotime($temp);
			if ($val != strtotime($customer->billDue))
			{
				$fields['billDue'] = "'" . strftime('%Y-%m-%d', $val) . "'";
				$audit['billDue'] = array(strftime('%Y-%m-%d', strtotime($customer->billDue)), strftime('%Y-%m-%d', $val));
			}
		}
		else
		{
			$errorList[] = $errText;
			$err = ERR_SUCCESS;
		}

		// Bill period
		$temp = intval($_POST['billPeriod']);
		if ($temp != $customer->billPeriod)
		{
            if ($temp == 0)
            {
                if (!is_null($customer->billPeriod))
                {
                    $fields['billPeriod'] = 'NULL';
                    $audit['billPeriod'] = array(iid2titleEx($customer->billPeriod), 'Pending');
                }
            }
            else
            {
                $fields['billPeriod'] = $temp;
                $audit['billPeriod'] = array(iid2titleEx($customer->billPeriod), iid2titleEx($temp));
            }
		}

		// Bill Count
		$temp = intval($_REQUEST['billCount']);
		if ($temp != $customer->billCount)
		{
			$fields['billCount'] = $temp;
			$audit['billCount'] = array($customer->billCount, $temp);
		}

		// Bill Quantity
		$temp = intval($_POST['billQuantity']);
		if ($temp != $customer->billQuantity)
		{
			$fields['billQuantity'] = $temp;
			$audit['billQuantity'] = array($customer->billQuantity, $temp);
		}

		// Update database if needed
		if (count($errorList) > 0)
			$message = '<span>Changes not saved due to error(s)</span>';
		else
		{
			if (count($fields) > 0 || count($queries) > 0)
			{
				do
				{
					// Wrap changes with transaction
					$result = db_query(SQL_TRANSACTION);
					if (!$result)
						break;

					// Apply update
					if (count($fields) > 0)
					{
						$result = db_update('customers', array('id' => $customer->id), $fields);
						if (!$result)
							break;
					}

					// Update names if needed
					if (count($queries) > 0)
					{
						foreach($queries as $query)
						{
							if (!db_query($query))
								break;
						}
					}
				} while (false);

				if ($err >= ERR_SUCCESS)
				{
					// Commit the transaction
					db_query(SQL_COMMIT);
					$message = '<span>Customer updated successfully!</span>';

					audit('Updated customer ' . sprintf('%06d', $customer->id) . '. '
							. audit_update($audit));
				}
				else
				{
					// Abort
					db_query(SQL_ROLLBACK);
					$message = '<span>Customer update failed!</span>';
				}
			}
			else
				$message = '<span>No changes required saving</span>';
		}
	}

?>
