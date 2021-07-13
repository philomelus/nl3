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
					<td>Name</td>
					<td>
<?php
		if (isset($_REQUEST['firstName']))
			$val = htmlspecialchars(stripslashes($_POST['firstName']));
		else
			$val = htmlspecialchars(stripslashes($customer->firstName));
?>
						<input type="text" name="firstName" value="<?php echo $val ?>" size="20" maxLength="30" />
<?php
		if (isset($_REQUEST['lastName']))
			$val = htmlspecialchars(stripslashes($_REQUEST['lastName']));
		else
			$val = htmlspecialchars(stripslashes($customer->lastName));
?>
						<input type="text" name="lastName" value="<?php echo $val ?>" size="20" maxLength="30" />
					</td>
				</tr>
				<tr>
					<td>Route</td>
					<td>
<?php
		if (isset($_REQUEST['rid']))
			$val = intval($_REQUEST['rid']);
		else
			$val = $customer->route_id;
		echo gen_routesSelect('rid', $val, false, '', '');
?>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
<?php
		if (isset($_REQUEST['type_id']))
			$val = intval($_REQUEST['type_id']);
		else
			$val = $customer->type_id;
		echo gen_typeSelect('type_id', $val, false, '', array(), '');
?>
					</td>
				</tr>
				<tr>
					<td>Include in Route List</td>
					<td>
<?php
		$val = array(0=>'',1=>'');
		if (isset($_REQUEST['routeList']))
			$temp = $_REQUEST['routeList'];
		else
			$temp = $customer->routeList;
		if ($temp == 'Y')
		{
			$val[0] = ' checked="checked"';
			$val[1] = '';
		}
		else
		{
			$val[0] = '';
			$val[1] = ' checked="checked"';
		}
?>
						<input type="radio" name="routeList" value="Y"<?php echo $val[0] ?>>Yes</input>
						<input type="radio" name="routeList" value="N"<?php echo $val[1] ?>>No</input>
					</td>
				</tr>
				<tr>
					<td>Include in Billing</td>
					<td>
<?php
		if (isset($_REQUEST['active']))
			$val = $_REQUEST['active'];
		else
			$val = $customer->active;
		if ($val == 'Y')
		{
			$yes = ' checked="checked"';
			$no = '';
		}
		else
		{
			$yes = '';
			$no = ' checked="checked"';
		}
?>
						<input type="radio" name="active" value="Y"<?php echo $yes ?>>Yes</input>
						<input type="radio" name="active" value="N"<?php echo $no ?>>No</input>
					</td>
				</tr>
				<tr>
					<td>Started</td>
					<td>
<?php
		if (!isset($_REQUEST['startedm']))
		{
			if (!empty($customer->started))
			{
				$started = strtotime($customer->started);
				$_REQUEST['startedm'] = date('m', $started);
				$_REQUEST['startedd'] = date('d', $started);
				$_REQUEST['startedy'] = date('Y', $started);
			}
		}
		echo gen_dateField('started');
?>
					</td>
				</tr>
				<tr>
					<td>Last Payment</td>
					<td>
<?php
		if (is_null($customer->lastPayment))
			echo 'No Payments Received';
		else
		{
			$err = ERR_SUCCESS;
			$payment = lup_c_payment(intval($customer->lastPayment));
			if ($err >= ERR_SUCCESS)
			{
				echo '<span>' . sprintf('$%01.2f', $payment->amount)
						. '</span> on <span>'
							. strftime('%m/%d/%Y', strtotime($payment->created)) . '</span>'
						. ' ('
						. gen_c_paymentid($customer->lastPayment, SCE_SUMMARY, IDS_BOTH, '../../')
						. ')';
			}
			else
				echo '<span>Error locating payment</span>';
		}
?>
					</td>
				</tr>
				<tr>
					<td>Interim Balance</td>
					<td>$<?php printf('%01.2f', $customer->balance); ?></td>
				</tr>
				<tr>
					<td>Rate</td>
					<td>
<?php
		switch($customer->rateType)
		{
		case RATE_STANDARD:
			printf('<b>Standard</b> (<b>$%01.2f</b>)', $DeliveryTypes[$customer->type_id]['rate']);
			break;

		case RATE_REPLACE:
			printf('<b>Overridden</b> @ <b>$%01.2f</b>', $customer->rateOverride);
			break;

		case RATE_SURCHARGE:
			printf('<b>Surcharged</b> @ <b>$%01.2f</b> (+ $%01.2f = $%01.2f)',
					$customer->rateOverride, $DeliveryTypes[$customer->type_id]['rate'],
					$DeliveryTypes[$customer->type_id]['rate'] + $customer->rateOverride);
			break;
		}
		echo '</span>';
?>
					</td>
				</tr>
				<tr>
					<td>Current Bill Date Display</td>
					<td>
						<table>
							<tbody>
								<tr>
									<td>Start:</td>
									<td><?php echo strftime('%m-%d-%Y', strtotime($customer->billStart)); ?></td>
								</tr>
								<tr>
									<td>End:</td>
									<td><?php echo strftime('%m-%d-%Y', strtotime($customer->billEnd)); ?></td>
								</tr>
								<tr>
									<td>Due:</td>
									<td><?php echo strftime('%m-%d-%Y', strtotime($customer->billDue)); ?></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>Next Billing</td>
					<td><?php echo iid2titleEx($customer->billPeriod); ?></td>
				</tr>
				<tr>
					<td>Bill Every</td>
					<td>
<?php
		echo $customer->billCount . ' period';
		if ($customer->billCount > 1)
			echo 's';
?>
					</td>
				</tr>
				<tr>
					<td>Quantity</td>
					<td>
<?php
		echo $customer->billQuantity . ' paper';
		if ($customer->billQuantity > 1)
			echo 's';
?>
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
		global $err, $errCode, $errContext, $errQuery, $errText, $Period;

		// Get out if not saving changes
		if ($_REQUEST['action'] != 'Save Changes')
			return;

		// List of fields to update
		$fields = array();
		$queries = array();
		$audit = array();

		// Update delivery name if needed
		$temp = array
			(
				0 => stripslashes($_REQUEST['firstName']),
				1 => stripslashes($_REQUEST['lastName'])
			);
		if ($temp[0] != $customer->firstName
				|| $temp[1] != $customer->lastName)
		{
			$queries[] = "UPDATE `customers_names` SET `first` = '"
					. db_escape($temp[0]) . "', `last` = '"
					. db_escape($temp[1]) . "'"
					. " WHERE `customer_id` = " . $customer->id
					. " AND `sequence` = " . NAME_C_DELIVERY1
					. " LIMIT 1";
			$audit['firstName'] = array($customer->firstName, $temp[0]);
			$audit['lastName'] = array($customer->lastName, $temp[1]);
		}

		// Route
		$newRoute = false;
		$temp = intval($_REQUEST['rid']);
		if ($temp != intval($customer->route_id))
		{
			$fields['route_id'] = $temp;
			$newRoute = true;
			$audit['route_id'] = array($Routes[$customer->route_id] . ' (id = ' . sprintf('%04d', $customer->route_id) . ')',
					$Routes[$temp] . ' (id = ' . sprintf('%04d', $temp) . ')');
		}

		// Type
		$temp = intval($_REQUEST['type_id']);
		if ($temp != $customer->type_id)
		{
			$fields['type_id'] = $temp;
			$audit['type_id'] = array($DeliveryTypes[$customer->type_id]['abbr'] . ' (id = ' . sprintf('%04d', $customer->route_id) . ')',
					$DeliveryTypes[$temp]['abbr'] . ' (id = ' . sprintf('%04d', $temp) . ')');
		}

		// Active
		$temp = stripslashes($_REQUEST['active']);
		if ($temp != stripslashes($customer->active))
		{
			$fields['active'] = "'" . db_escape($temp) . "'";
			$audit['active'] = array(stripslashes($customer->active), $temp);
			// Reset next bill period, or billing logic will skip this customer
			// BUGBUG:  Isn't it safe for the billing code to always handle past billing periods?
			//          It's valid to have a period in the future, but not in the past...
			if ($temp == 'Y' && $customer->billPeriod != $Period[PN_PERIOD])
			{
				$fields['billPeriod'] = $Period[PN_PERIOD];
				$audit['billPeriod'] = array(iid2title($customer->billPeriod), iid2title($Period[PN_PERIOD]));
			}
		}

		// Route List
		$temp = $_REQUEST['routeList'];
		if ($temp != $customer->routeList)
		{
			$fields['routeList'] = "'" . $temp . "'";
			$audit['routeList'] = array(($customer->routeList == 'Y' ? 'TRUE' : 'FALSE'), ($temp == 'Y' ? 'TRUE' : 'FALSE'));
		}

		// Started date
		if (!empty($_REQUEST['startedm']) || !empty($_REQUEST['startedd'])
				|| !empty($_REQUEST['startedy']))
		{
			$temp = valid_date('started', 'Started');
			if ($err >= ERR_SUCCESS)
			{
				$val = strtotime($temp);
				if ($val != strtotime($customer->started))
				{
					$fields['started'] = "'" . strftime('%Y-%m-%d', $val) . "'";
					$audit['started'] = array(strftime('%Y-%m-%d', strtotime($customer->started)), strftime('%Y-%m-%d', $val));
				}
			}
			else
			{
				$errorList[] = $errText;
				$err = ERR_SUCCESS;
			}
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
						{
							$errContext = 'Updating customer record';
							break;
						}
					}

					// Update route if needed
					if ($newRoute)
					{
						// BUGBUG:  This doesn't fix the ordering of the old route sequence, so it will
						// now contain at least 1 gap
						$result = db_update('routes_sequence', array('tag_id' => $customer->id),
								array('route_id' => $fields['route_id'], 'order' => CUSTOMER_ADDSEQUENCE));
						if (!$result)
						{
							$errContext = 'Customer Edit Summary';
							break;
						}
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
					if ($newRoute)
						audit('Customer ' . sprintf('%06d', $customer->id) . ' sequence reset');
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
