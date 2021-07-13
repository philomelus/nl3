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

	function display()
	{
		global $customer;
		global $deliveryAddr;
		global $billingAddr;

?>
			<table>
				<caption>Delivery Address</caption>
				<tr>
					<td>Name</td>
					<td>
<?php
		if (isset($_REQUEST['firstName']))
			$val = stripslashes($_REQUEST['firstName']);
		else
			$val = stripslashes($customer->firstName);
?>
						<input type="text" name="firstName" value="<?php echo $val ?>" size="20" maxlength="30" />
<?php
		if (isset($_REQUEST['lastName']))
			$val = stripslashes($_REQUEST['lastName']);
		else
			$val = stripslashes($customer->lastName);
?>
						<input type="text" name="lastName" value="<?php echo $val ?>" size="20" maxlength="30" />
					</td>
				</tr>
				<tr>
					<td>Alternate Name</td>
					<td>
<?php
		$val = array(0 => '', 1 => '');
		if (isset($_REQUEST['altFirstName']) || isset($_REQUEST['altLastName']))
		{
			$val[0] = stripslashes($_REQUEST['altFirstName']);
			$val[1] = stripslashes($_REQUEST['altLastName']);
		}
		else
		{
			$temp = lup_c_name($customer->id, NAME_C_DELIVERY2);
			if ($temp)
			{
				$val[0] = stripslashes($temp->first);
				$val[1] = stripslashes($temp->last);
			}
			else
				$err = ERR_SUCCESS;
		}
?>
						<input type="text" name="altFirstName" value="<?php echo $val[0] ?>" size="20" maxlength="30" />
						<input type="text" name="altLastName" value="<?php echo $val[1] ?>" size="20" maxlength="30" />
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td>
<?php
		if (isset($_REQUEST['dAddress']))
		{
			$val = array
				(
					0 => stripslashes($_REQUEST['dAddress']),
					1 => stripslashes($_REQUEST['dCity']),
					2 => stripslashes($_REQUEST['dState']),
					3 => stripslashes($_REQUEST['dZip'])
				);
		}
		else
		{
			$val = array
				(
					0 => stripslashes($customer->address),
					1 => stripslashes($customer->city),
					2 => stripslashes($customer->state),
					3 => stripslashes($customer->zip)
				);
		}
?>
						<input type="text" name="dAddress" value="<?php echo $val[0] ?>" size="30" />
						<br />
						<input type="text" name="dCity" value="<?php echo $val[1] ?>" size="30" />
						<input type="text" name="dState" value="<?php echo $val[2] ?>" size="2" />
						<input type="text" name="dZip" value="<?php echo $val[3] ?>" size="10" />
					</td>
				</tr>
				<tr>
					<td>Telephone 1</td>
					<td>
<?php
		// Delivery Telephone 1
		unset($temp);
		unset($telephone);
		if (isset($_REQUEST['dTele1AreaCode']))
		{
			$temp = array
				(
					1 => $_REQUEST['dTele1AreaCode'],
					2 => $_REQUEST['dTele1Prefix'],
					3 => $_REQUEST['dTele1Number'],
					5 => $_REQUEST['dTele1Ext']
				);
		}
		else
		{
			$telephone = lup_c_telephone($customer->id, TEL_C_DELIVERY1);
			if ($telephone)
            {
				preg_match('/^\(([[:digit:]]{3})\) ([[:digit:]]{3})-([[:digit:]]{4})( [Ee][Xx][Tt] ([[:alnum:]]{0,10}))?$/',
                    $telephone->number, $temp);
                if (!isset($temp[5]))
                    $temp[5] = '';
            }
			else
			{
				unset($telephone);
				$temp = array
					(
						1 => '',
						2 => '',
						3 => '',
						5 => ''
					);
			}
		}

		// Delivery Telephone 1 Type
		if (isset($_REQUEST['dTele1Type']))
			$val = stripslashes($_REQUEST['dTele1Type']);
		else if (isset($telephone))
			$val = stripslashes($telephone->type);
		else
			$val = 'Main';
		echo gen_telephoneTypeSelect('dTele1Type', $val, '');
?>
						(<input type="text" name="dTele1AreaCode"  size="3" maxlength="3" value="<?php echo $temp[1] ?>" />)<input type="text" name="dTele1Prefix" value="<?php echo $temp[2] ?>" size="3" maxlength="3" />-<input type="text" name="dTele1Number" value="<?php echo $temp[3] ?>" size="4" maxlength="4" />x<input type="text" name="dTele1Ext" value="<?php echo $temp[5] ?>" size="5" maxlength="5" />
					</td>
				</tr>
				<tr>
					<td>Telephone 2</td>
					<td>
<?php
		// Delivery Telephone 2
		unset($temp);
		unset($telephone);
		if (isset($_REQUEST['dTele2AreaCode']))
		{
			$temp = array
				(
					1 => $_REQUEST['dTele2AreaCode'],
					2 => $_REQUEST['dTele2Prefix'],
					3 => $_REQUEST['dTele2Number'],
					5 => $_REQUEST['dTele2Ext']
				);
		}
		else
		{
			$telephone = lup_c_telephone($customer->id, TEL_C_DELIVERY2);
			if ($telephone)
            {
				preg_match('/^\(([[:digit:]]{3})\) ([[:digit:]]{3})-([[:digit:]]{4})( [Ee][Xx][Tt] ([[:alnum:]]{0,10}))?$/',
                    $telephone->number, $temp);
                if (!isset($temp[5]))
                    $temp[5] = '';
            }
			else
			{
				unset($telephone);
				$temp = array
					(
						1 => '',
						2 => '',
						3 => '',
						5 => ''
					);
			}
		}

		// Delivery Telephone 2 Type
		if (isset($_REQUEST['dTele2Type']))
			$val = stripslashes($_REQUEST['dTele2Type']);
		else if (isset($telephone))
			$val = stripslashes($telephone->type);
		else
			$val = 'Alternate';
		echo gen_telephoneTypeSelect('dTele2Type', $val, '');
?>
						(<input type="text" name="dTele2AreaCode"  size="3" maxlength="3" value="<?php echo $temp[1] ?>" />)<input type="text" name="dTele2Prefix" value="<?php echo $temp[2] ?>" size="3" maxlength="3" />-<input type="text" name="dTele2Number" value="<?php echo $temp[3] ?>" size="4" maxlength="4" />x<input type="text" name="dTele2Ext" value="<?php echo $temp[5] ?>" size="5" maxlength="5" />
					</td>
				</tr>
				<tr>
					<td>Telephone 3</td>
					<td>
<?php
		// Delivery Telephone 3
		unset($temp);
		unset($telephone);
		if (isset($_REQUEST['dTele3AreaCode']))
		{
			$temp = array
				(
					1 => $_REQUEST['dTele3AreaCode'],
					2 => $_REQUEST['dTele3Prefix'],
					3 => $_REQUEST['dTele3Number'],
					5 => $_REQUEST['dTele3Ext']
				);
		}
		else
		{
			$telephone = lup_c_telephone($customer->id, TEL_C_DELIVERY3);
			if ($telephone)
            {
				preg_match('/^\(([[:digit:]]{3})\) ([[:digit:]]{3})-([[:digit:]]{4})( [Ee][Xx][Tt] ([[:alnum:]]{0,10}))?$/',
                    $telephone->number, $temp);
                if (!isset($temp[5]))
                    $temp[5] = '';
            }
			else
			{
				unset($telephone);
				$temp = array
					(
						1 => '',
						2 => '',
						3 => '',
						5 => ''
					);
			}
		}

		// Delivery Telephone 3 Type
		if (isset($_REQUEST['dTele3Type']))
			$val = stripslashes($_REQUEST['dTele3Type']);
		else if (isset($telephone))
			$val = stripslashes($telephone->type);
		else
			$val = 'Mobile';
		echo gen_telephoneTypeSelect('dTele3Type', $val, '');
?>
						(<input type="text" name="dTele3AreaCode"  size="3" maxlength="3" value="<?php echo $temp[1] ?>" />)<input type="text" name="dTele3Prefix" value="<?php echo $temp[2] ?>" size="3" maxlength="3" />-<input type="text" name="dTele3Number" value="<?php echo $temp[3] ?>" size="4" maxlength="4" />x<input type="text" name="dTele3Ext" value="<?php echo $temp[5] ?>" size="5" maxlength="5" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Save Changes" />
					</td>
				</tr>
			</table>
			<div><br /></div>
			<table>
				<caption>Billing Address</caption>
				<tr>
					<td>Name</td>
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
						<input type="text" name="bFirstName" value="<?php echo $val[0] ?>" size="20" maxlength="30" />
						<input type="text" name="bLastName" value="<?php echo $val[1] ?>" size="20" maxlength="30" />
					</td>
				</tr>
				<tr>
					<td>Alternate Name</td>
					<td>
<?php
		$val = array(0 => '', 1 => '');
		if (isset($_REQUEST['bAltFirstName']) || isset($_REQUEST['bAltLastName']))
		{
			$val[0] = stripslashes($_REQUEST['bAltFirstName']);
			$val[1] = stripslashes($_REQUEST['bAltLastName']);
		}
		else
		{
			$temp = lup_c_name($customer->id, NAME_C_BILLING2);
			if ($temp)
			{
				$val[0] = stripslashes($temp->first);
				$val[1] = stripslashes($temp->last);
			}
			else
				$err = ERR_SUCCESS;
		}
?>
						<input type="text" name="bAltFirstName" value="<?php echo $val[0] ?>" size="20" maxlength="30" />
						<input type="text" name="bAltLastName" value="<?php echo $val[1] ?>" size="20" maxlength="30" />
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td>
<?php
		if (isset($_REQUEST['bAddress']))
		{
			$val = array
				(
					0 => stripslashes($_REQUEST['bAddress']),
					1 => stripslashes($_REQUEST['bCity']),
					2 => stripslashes($_REQUEST['bState']),
					3 => stripslashes($_REQUEST['bZip'])
				);
		}
		else
		{
			$val = array
				(
					0 => stripslashes($billingAddr->address1),
					1 => stripslashes($billingAddr->city),
					2 => stripslashes($billingAddr->state),
					3 => stripslashes($billingAddr->zip)
				);
		}
?>
						<input type="text" name="bAddress" value="<?php echo $val[0] ?>" size="30" />
						<br />
						<input type="text" name="bCity" value="<?php echo $val[1] ?>" size="30" />
						<input type="text" name="bState" value="<?php echo $val[2] ?>" size="2" />
						<input type="text" name="bZip" value="<?php echo $val[3] ?>" size="10" />
					</td>
				</tr>
				<tr>
					<td>Telephone 1</td>
					<td>
<?php
		// Billing Telephone 1
		unset($temp);
		unset($telephone);
		if (isset($_REQUEST['bTele1AreaCode']))
		{
			$temp = array
				(
					1 => $_REQUEST['bTele1AreaCode'],
					2 => $_REQUEST['bTele1Prefix'],
					3 => $_REQUEST['bTele1Number'],
					5 => $_REQUEST['bTele1Ext']
				);
		}
		else
		{
			$telephone = lup_c_telephone($customer->id, TEL_C_BILLING1);
            if ($telephone)
            {
				preg_match('/^\(([[:digit:]]{3})\) ([[:digit:]]{3})-([[:digit:]]{4})( [Ee][Xx][Tt] ([[:alnum:]]{0,10}))?$/',
                    $telephone->number, $temp);
                if (!isset($temp[5]))
                    $temp[5] = '';
            }
			else
			{
				unset($telephone);
				$temp = array
					(
						1 => '',
						2 => '',
						3 => '',
						5 => ''
					);
			}
		}

		// Billing Telephone 1 Type
		if (isset($_REQUEST['bTele1Type']))
			$val = stripslashes($_REQUEST['bTele1Type']);
		else if (isset($telephone))
			$val = stripslashes($telephone->type);
		else
			$val = 'Main';
		echo gen_telephoneTypeSelect('bTele1Type', $val, '');
?>
						(<input type="text" name="bTele1AreaCode"  size="3" maxlength="3" value="<?php echo $temp[1] ?>" />)<input type="text" name="bTele1Prefix" value="<?php echo $temp[2] ?>" size="3" maxlength="3" />-<input type="text" name="bTele1Number" value="<?php echo $temp[3] ?>" size="4" maxlength="4" />x<input type="text" name="bTele1Ext" value="<?php echo $temp[5] ?>" size="5" maxlength="5" />
					</td>
				</tr>
				<tr>
					<td>Telephone 2</td>
					<td>
<?php
		// Billing Telephone 2
		unset($temp);
		unset($telephone);
		if (isset($_REQUEST['bTele2AreaCode']))
		{
			$temp = array
				(
					1 => $_REQUEST['bTele2AreaCode'],
					2 => $_REQUEST['bTele2Prefix'],
					3 => $_REQUEST['bTele2Number'],
					5 => $_REQUEST['bTele2Ext']
				);
		}
		else
		{
			$telephone = lup_c_telephone($customer->id, TEL_C_BILLING2);
			if ($telephone)
            {
				preg_match('/^\(([[:digit:]]{3})\) ([[:digit:]]{3})-([[:digit:]]{4})( [Ee][Xx][Tt] ([[:alnum:]]{0,10}))?/$',
                    $telephone->number, $temp);
                if (!isset($temp[5]))
                    $temp[5] = '';
            }
			else
			{
				unset($telephone);
				$temp = array
					(
						1 => '',
						2 => '',
						3 => '',
						5 => ''
					);
			}
		}

		// Billing Telephone 2 Type
		if (isset($_REQUEST['bTele2Type']))
			$val = stripslashes($_REQUEST['bTele2Type']);
		else if (isset($telephone))
			$val = stripslashes($telephone->type);
		else
			$val = 'Alternate';
		echo gen_telephoneTypeSelect('bTele2Type', $val, '');
?>
						(<input type="text" name="bTele2AreaCode"  size="3" maxlength="3" value="<?php echo $temp[1] ?>" />)<input type="text" name="bTele2Prefix" value="<?php echo $temp[2] ?>" size="3" maxlength="3" />-<input type="text" name="bTele2Number" value="<?php echo $temp[3] ?>" size="4" maxlength="4" />x<input type="text" name="bTele2Ext" value="<?php echo $temp[5] ?>" size="5" maxlength="5" />
					</td>
				</tr>
				<tr>
					<td>Telephone 3</td>
					<td>
<?php
		// Billing Telephone 3
		unset($temp);
		unset($telephone);
		if (isset($_REQUEST['bTele3AreaCode']))
		{
			$temp = array
				(
					1 => $_REQUEST['bTele3AreaCode'],
					2 => $_REQUEST['bTele3Prefix'],
					3 => $_REQUEST['bTele3Number'],
					5 => $_REQUEST['bTele3Ext']
				);
		}
		else
		{
			$telephone = lup_c_telephone($customer->id, TEL_C_BILLING3);
            if ($telephone)
            {
				preg_match('/^\(([[:digit:]]{3})\) ([[:digit:]]{3})-([[:digit:]]{4})( [Ee][Xx][Tt] ([[:alnum:]]{0,10}))?$/',
                    $telephone->number, $temp);
                if (!isset($temp[5]))
                    $temp[5] = '';
            }
			else
			{
				unset($telephone);
				$temp = array
					(
						1 => '',
						2 => '',
						3 => '',
						5 => ''
					);
			}
		}

		// Billing Telephone 3 Type
		if (isset($_REQUEST['bTele3Type']))
			$val = stripslashes($_REQUEST['bTele3Type']);
		else if (isset($telephone))
			$val = stripslashes($telephone->type);
		else
			$val = 'Mobile';
		echo gen_telephoneTypeSelect('bTele3Type', $val, '');
?>
						(<input type="text" name="bTele3AreaCode"  size="3" maxlength="3" value="<?php echo $temp[1] ?>" />)<input type="text" name="bTele3Prefix" value="<?php echo $temp[2] ?>" size="3" maxlength="3" />-<input type="text" name="bTele3Number" value="<?php echo $temp[3] ?>" size="4" maxlength="4" />x<input type="text" name="bTele3Ext" value="<?php echo $temp[5] ?>" size="5" maxlength="5" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Save Changes" />
					</td>
				</tr>
			</table>
<?php
	}

	//-------------------------------------------------------------------------

	function scripts()
	{
		return
'
<script type="text/javascript">
$(document).ready(
	function()
	{
		$(\'#dTele1AreaCode\').autotab({ target: \'dTele1Prefix\', format: \'numeric\' });
		$(\'#dTele1Prefix\').autotab({ target: \'dTele1Number\', format: \'numeric\', previous: \'dTele1AreaCode\' });
		$(\'#dTele1Number\').autotab({ target: \'dTele1Ext\', format: \'numeric\', previous: \'dTele1Prefix\' });

		$(\'#dTele2AreaCode\').autotab({ target: \'dTele2Prefix\', format: \'numeric\' });
		$(\'#dTele2Prefix\').autotab({ target: \'dTele2Number\', format: \'numeric\', previous: \'dTele2AreaCode\' });
		$(\'#dTele2Number\').autotab({ target: \'dTele2Ext\', format: \'numeric\', previous: \'dTele2Prefix\' });

		$(\'#dTele3AreaCode\').autotab({ target: \'dTele3Prefix\', format: \'numeric\' });
		$(\'#dTele3Prefix\').autotab({ target: \'dTele3Number\', format: \'numeric\', previous: \'dTele3AreaCode\' });
		$(\'#dTele3Number\').autotab({ target: \'dTele3Ext\', format: \'numeric\', previous: \'dTele3Prefix\' });

		$(\'#bTele1AreaCode\').autotab({ target: \'bTele1Prefix\', format: \'numeric\' });
		$(\'#bTele1Prefix\').autotab({ target: \'bTele1Number\', format: \'numeric\', previous: \'bTele1AreaCode\' });
		$(\'#bTele1Number\').autotab({ target: \'bTele1Ext\', format: \'numeric\', previous: \'bTele1Prefix\' });

		$(\'#bTele2AreaCode\').autotab({ target: \'bTele2Prefix\', format: \'numeric\' });
		$(\'#bTele2Prefix\').autotab({ target: \'bTele2Number\', format: \'numeric\', previous: \'bTele2AreaCode\' });
		$(\'#bTele2Number\').autotab({ target: \'bTele2Ext\', format: \'numeric\', previous: \'bTele2Prefix\' });

		$(\'#bTele3AreaCode\').autotab({ target: \'bTele3Prefix\', format: \'numeric\' });
		$(\'#bTele3Prefix\').autotab({ target: \'bTele3Number\', format: \'numeric\', previous: \'bTele3AreaCode\' });
		$(\'#bTele3Number\').autotab({ target: \'bTele3Ext\', format: \'numeric\', previous: \'bTele3Prefix\' });
	}
);
</script>
';
	}

	//-------------------------------------------------------------------------

	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------

	function submit()
	{
		global $customer, $deliveryAddr, $billingAddr, $DB;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $errorList, $message, $resultHtml;

		if ($_REQUEST['action'] != 'Save Changes')
			return;

		// Nothing to update by default
		$queries = array();
		$audit = array();

		// Delivery name
		$temp[0] = stripslashes($_REQUEST['firstName']);
		$temp[1] = stripslashes($_REQUEST['lastName']);
		if ($temp[0] != $customer->firstName
				|| $temp[1] != $customer->lastName)
		{
			$queries[] = "UPDATE `customers_names` SET `first` = '"
					. db_escape($temp[0]) . "', `last` = '"
					. db_escape($temp[1]) . "'"
					. " WHERE `customer_id` = " . $customer->id
					. " AND `sequence` = " . NAME_C_DELIVERY1
					. " LIMIT 1";
			if ($temp[0] != $customer->firstName)
				$audit['firstName'] = array($customer->firstName, $temp[0]);
			if ($temp[1] != $customer->lastName)
				$audit['lastName'] = array($customer->lastName, $temp[1]);
		}

		// Alternate delivery name
		$temp = update_name_query($customer->id, NAME_C_DELIVERY2,
				stripslashes($_REQUEST['altFirstName']),
				stripslashes($_REQUEST['altLastName']), $audit);
		if (!empty($temp))
			$queries[] = $temp;

		// Billing name
		$temp = update_name_query($customer->id, NAME_C_BILLING1,
				stripslashes($_REQUEST['bFirstName']),
				stripslashes($_REQUEST['bLastName']), $audit);
		if (!empty($temp))
			$queries[] = $temp;

		// Altername billing name
		$temp = update_name_query($customer->id, NAME_C_BILLING2,
				stripslashes($_REQUEST['bAltFirstName']),
				stripslashes($_REQUEST['bAltLastName']), $audit);
		if (!empty($temp))
			$queries[] = $temp;

		// Telephone numbers queries
		foreach(array
			(
				'dTele1' => TEL_C_DELIVERY1,
				'dTele2' => TEL_C_DELIVERY2,
				'dTele3' => TEL_C_DELIVERY3,
				'bTele1' => TEL_C_BILLING1,
				'bTele2' => TEL_C_BILLING2,
				'bTele3' => TEL_C_BILLING3
			) as $field => $type)
		{
			$fieldType = $field . 'Type';
			if (substr($field, 0, 1) == 'd')
				$name = 'Delivery';
			else
				$name = 'Billing';

			$temp = valid_telephone($field, $name . ' Telephone ' . substr($field, -1, 1),
					$type == TEL_C_DELIVERY1 ? true : false);
			if ($err < ERR_SUCCESS)
			{
				$errorList[] = $errText;
				$err = ERR_SUCCESS;
				continue;
			}

			$telephone = lup_c_telephone($customer->id, $type);
			if ($err < ERR_SUCCESS)
			{
				if ($err != ERR_NOTFOUND)
				{
					$errorList[] = $errText;
					$err = ERR_SUCCESS;
					continue;
				}
			}
			if ($err == ERR_NOTFOUND)
			{
				$err = ERR_SUCCESS;
				if (!empty($temp))
				{
					$audit[telseq_name($type) . ' Type'] = array('', $_REQUEST[$fieldType]);
					$audit[telseq_name($type)] = array('', $temp);
					$queries[] = "INSERT INTO `customers_telephones` SET `customer_id` = " . $customer->id
							. ", `sequence` = " . $type . ", `created` = NOW(), `updated` = NOW()"
							. ", `type` = '" . db_escape($_REQUEST[$fieldType]) . "'"
							. ", `number` = '" . db_escape($temp) . "'";
				}
			}
			else
			{
				if (stripslashes($_REQUEST[$fieldType]) != $telephone->type
						|| $temp != stripslashes($telephone->number))
				{
					if (!empty($temp))
					{
						$audit[telseq_name($type) . ' Type'] = array($telephone->type, $_REQUEST[$fieldType]);
						$audit[telseq_name($type)] = array($telephone->number, $temp);
						$queries[] = "UPDATE `customers_telephones` SET `number` = '"
								. db_escape($temp) . "', `type` = '"
								. db_escape($_REQUEST[$fieldType]) . "' WHERE `customer_id` = "
								. $customer->id . " AND `sequence` = " . $type . " LIMIT 1";
					}
					else
					{
						$audit[telseq_name($type) . ' Type'] = array($telephone->type, '');
						$audit[telseq_name($type)] = array($telephone->number, '');
						$queries[] = "DELETE FROM `customers_telephones` WHERE `customer_id` = "
								. $customer->id . " AND `sequence` = " . $type . " LIMIT 1";
					}
				}
			}
		}

		// Build delivery address record query
		$query = "UPDATE `customers_addresses` SET";
		$comma = '';
		$where = " WHERE `customer_id` = " . $customer->id
				. " AND `sequence` = " . ADDR_C_DELIVERY . " LIMIT 1";
		$temp = form_to_query(array
			(
				'dAddress' => 'address1',
				'dCity' => 'city',
				'dState' => 'state',
				'dZip' => 'zip'
			), $deliveryAddr, true, $audit, 'Delivery Address');
		if (!empty($temp))
			$queries[] = $query . $temp . $where;

		// Build billing address record query
		// TODO:  If the address is cleared, should the record be removed?
		if ($billingAddr->customer_id == 0)
		{
			$query = "INSERT INTO `customers_addresses` SET `customer_id` = " . $customer->id
					. ", `sequence` = " . ADDR_C_BILLING;
			$where = '';
			$comma = ',';
		}
		else
		{
			$query = "UPDATE `customers_addresses` SET";
			$where = "WHERE `customer_id` = " . $customer->id
					. " AND `sequence` = " . ADDR_C_BILLING . " LIMIT 1";
			$comma = '';
		}
		$temp = form_to_query(array
			(
				'bAddress' => 'address1',
				'bCity' => 'city',
				'bState' => 'state',
				'bZip' => 'zip'
			), $billingAddr, true, $audit, 'Billing Address');
		if (!empty($temp))
			$queries[] = $query . ' ' . $comma . $temp . $where;

		// If anything needs updating, update now
		if (count($errorList) > 0)
			$message = '<span>Changes not saved due to error(s)</span>';
		else if (count($queries))
		{
			// Wrap changes with transaction
			$result = db_query(SQL_TRANSACTION);
			if (!$result)
				return;

			// Perform updates
			foreach($queries as $query)
			{
				// Apply update
				$result = db_query($query);
				if (!$result)
					break;
			}

			// Complete or abort transaction as needed
			if ($err >= ERR_SUCCESS)
			{
				db_query(SQL_COMMIT);
				$message = '<span>Changes saved</span>';
				audit('Updated customer ' . sprintf('%06d', $customer->id) . '. ' . audit_update($audit));
			}
			else
			{
				db_query(SQL_ROLLBACK);
				$message = '<span>Update failed</span>';
			}
		}
		else
			$message = '<span>No changes required saving</span>';
	}

?>
