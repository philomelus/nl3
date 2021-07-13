<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009 Russell E. Gibson

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
	define('ROOT', '../../../');
	set_include_path(ROOT . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SL_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/billing.inc.php';
	require_once 'inc/popups/customers.inc.php';

	if (!isset($_REQUEST['cid'])
			|| !preg_match('/^\d+$/', $_REQUEST['cid']))
	{
		echo invalid_parameters('Add Bill', 'popups/customers/bills/add.php');
		return;
	}

	$CID = intval($_REQUEST['cid']);

	$title = 'Add Bill';
	$err = ERR_SUCCESS;

	$message = '';
	$errorList = array();
	
	populate_periods_valid($title);
	populate_routes_valid($title);
	populate_types_valid($title);
	$customer = lup_customer_valid($title, $CID);
	$delAddr = lup_c_address_valid($title, $customer->id, ADDR_C_DELIVERY);
	$billAddr = lup_c_address($customer->id, ADDR_C_BILLING);
	if ($err < ERR_SUCCESS)
	{
		if ($err != ERR_NOTFOUND)
		{
			echo fatal_error('Add Bill', $errText);
			return;
		}
		$err = ERR_SUCCESS;
	}
	$delName = lup_c_name_valid($title, $customer->id, NAME_C_DELIVERY1);
	$billName = lup_c_name($customer->id, NAME_C_BILLING1);
	if ($err < ERR_SUCCESS)
	{
		if ($err != ERR_NOTFOUND)
		{
			echo fatal_error('Add Bill', $errText);
			return;
		}
		else
			$err = ERR_SUCCESS;
	}

	if (isset($_POST['action']) && $_POST['action'] == 'add')
	{
		$fields = array();

		$IID = intval($_POST['period']);
		$period = lup_period($IID);
		$fields['cid'] = '\'' . sprintf('%06d', $customer->id) . '\'';
		$fields['iid'] = $IID;
		$fields['when'] = 'NOW()';
		$fields['export'] = '\'' . $_POST['export'] . '\'';
		$fields['rt'] = '\'' . rid2title(intval($_POST['route'])) . '\'';
		$fields['dNm'] = '\'' . db_escape(stripslashes($_POST['deliveryName'])) . '\'';
		$fields['dAd'] = '\'' . db_escape(stripslashes($_POST['deliveryAddress'])) . '\'';
		$fields['dCt'] = '\'' . db_escape(stripslashes($_POST['deliveryCity'])) . '\'';
		$fields['dSt'] = '\'' . db_escape(stripslashes($_POST['deliveryState'])) . '\'';
		$fields['dZp'] = '\'' . db_escape(stripslashes($_POST['deliveryZip'])) . '\'';
		$fields['bNm'] = '\'' . db_escape(stripslashes($_POST['billName'])) . '\'';
		$fields['bAd1'] = '\'' . db_escape(stripslashes($_POST['billAddress1'])) . '\'';
		$fields['bAd2'] = '\'' . db_escape(stripslashes($_POST['billAddress2'])) . '\'';
		$fields['bAd3'] = '\'' . db_escape(stripslashes($_POST['billAddress3'])) . '\'';
		$fields['bAd4'] = '\'' . db_escape(stripslashes($_POST['billAddress4'])) . '\'';
		$fields['rTit'] = '\'' . db_escape(stripslashes($_POST['title'])) . '\'';
		if (preg_match('/^[-+]?[0-9]*\.?[0-9]+$/', $_POST['previous']))
			$fields['fwd'] = '\'' . currency(floatval($_POST['previous']), false) . '\'';
		else
			$errorList[] = 'Previous balance is invalid';
		if (preg_match('/^[-+]?[0-9]*\.?[0-9]+$/', $_POST['rate']))
			$fields['rate'] = '\'' . currency(floatval($_POST['rate']), false) . '\'';
		else
			$errorList[] = 'Rate is invalid';
		if (preg_match('/^[-+]?[0-9]*\.?[0-9]+$/', $_POST['payments']))
			$fields['pmt'] = '\'' . currency(floatval($_POST['payments']), false) . '\'';
		else
			$errorList[] = 'Payments is invalid';
		if (preg_match('/^[-+]?[0-9]*\.?[0-9]+$/', $_POST['adjustments']))
			$fields['adj'] = '\'' . currency(floatval($_POST['adjustments']), false) . '\'';
		else
			$errorList[] = 'Adjustments is invalid';
		if (preg_match('/^[-+]?[0-9]*\.?[0-9]+$/', $_POST['total']))
			$fields['bal'] = '\'' . currency($_POST['total'], false) . '\'';
		else
			$errorList[] = 'Total Due is invalid';
		$fields['dts'] = '\'' . strftime('%m-%d-%Y', strtotime($period->display_start)) . '\'';
		$fields['dte'] = '\'' . strftime('%m-%d-%Y', strtotime($period->display_end)) . '\'';
		$fields['due'] = '\'' . strftime('%m-%d-%Y', strtotime($period->due)) . '\'';
		$fields['nt1'] = '\'' . db_escape(stripslashes($_POST['note1'])) . '\'';
		$fields['nt2'] = '\'' . db_escape(stripslashes($_POST['note2'])) . '\'';
		$fields['nt3'] = '\'' . db_escape(stripslashes($_POST['note3'])) . '\'';
		$fields['nt4'] = '\'' . db_escape(stripslashes($_POST['note4'])) . '\'';

		// Add record if needed
		if (count($errorList) > 0)
			$message = '<span>Please fix errors</span>';
		else
		{
			// Add bill
			$result = db_insert('customers_bills', $fields);

			// Finish up
			if ($err >= ERR_SUCCESS)
			{
				// Let user know it was successful
				$message = '<span>Bill for ' . iid2title(intval($fields['iid']))
						. ' added.</span>';

				audit('Added bill for ' . iid2title($fields['iid']) . ' (id = ' . sprintf('I%04d', $fields['iid'])
						. ') to customer ' . sprintf('%06d', $customer->id) . '. '
						. audit_add($fields));
			}
			else
			{
				// Let user know it failed
				$message = '<span>Adding bill failed!</span>';
			}
		}
	}

	$smarty->assign('action', $_SERVER['PHP_SELF']);
	$smarty->assign('CID', $CID);
	$smarty->assign('ROOT', ROOT);
	$smarty->assign('customer', $customer);
	$smarty->assign('clientName', get_config('client-name', 'ERROR'));
	$smarty->assign('clientAddress1', get_config('client-address-1', 'ERROR'));
	$smarty->assign('clientAddress2', get_config('client-address-2', 'ERROR'));
	$smarty->assign('clientTelephone', get_config('client-telephone', 'ERROR'));
	$billInfo = bill_address($CID);
	$noteInfo = bill_note($CID);
	$title = bill_rate_title($customer->type_id);
	foreach(array
		(
			'id' => sprintf('%06d', $CID),
			'period' => get_config('billing-period', 0),
			'route' => $customer->route_id,
			'deliveryName' => substr(valid_name($delName->first, $delName->last), 0, 20),
			'deliveryAddress' => substr($customer->address, 0, 20),
			'deliveryCity' => substr($customer->city, 0, 11),
			'deliveryState' => substr($customer->state, 0, 2),
			'deliveryZip' => substr($customer->zip, 0, 5),
			'previous' => $customer->billBalance,
			'payments' => 0,
			'title' => $title,
			'rate' => $DeliveryTypes[$customer->type_id]['rate'],
			'adjustments' => 0,
			'billName' => $billInfo[0],
			'billAddress1' => $billInfo[1],
			'billAddress2' => $billInfo[2],
			'billAddress3' => $billInfo[3],
			'billAddress4' => $billInfo[4],
			'notes' => $noteInfo[0] . ' ' . $noteInfo[1] . ' ' . $noteInfo[2] . ' ' . $noteInfo[3],
			'export' => 'Y',
			'stopped' => 'N'
		) as $field => $default)
	{
		if (isset($_POST[$field]))
			$smarty->assign($field, $_POST[$field]);
		else
			$smarty->assign($field, $default);
	}
	$smarty->assign('stoppedOptions', array('Y' => 'Yes', 'N' => 'No'));
	$smarty->assign('exportOptions', array('Y' => 'Yes', 'N' => 'No'));
	$smarty->assign('message', $message);
	$smarty->assign('errorList', $errorList);
	$smarty->display('popups/customers/bills/add.tpl');
?>
