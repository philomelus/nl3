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

	define('PAGE', SL_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/popups/customers.inc.php';
	require_once 'inc/sql.inc.php';
	
	if (!isset($_REQUEST['cid']) || !isset($_REQUEST['iid'])
			|| !preg_match('/^\d+$/', $_REQUEST['cid'])
			|| !preg_match('/^\d+$/', $_REQUEST['iid']))
	{
		echo invalid_parameters('Edit Bill', 'popups/customers/bills/edit.php');
		return;
	}

	$CID = intval($_REQUEST['cid']);
	$IID = intval($_REQUEST['iid']);

	$err = ERR_SUCCESS;

	$bill = lup_c_bill($CID, $IID);
	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Edit Bill', $errText);
		return;
	}
	$customer = lup_customer($CID);
	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Edit Bill', $errText);
		return;
	}
	populate_routes();
	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Edit Bill', $errText);
		return;
	}
	populate_periods();
	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Edit Bill', $errText);
		return;
	}

	$message = '';
	$errorList = array();
	
	if (isset($_POST['action']) && $_POST['action'] == 'update')
	{
		
		$fields = array();
		$audit = array();		

		// Text fields
		foreach(array
			(
				'export' => 'export',
				'dNm' => 'deliveryName',
				'dAd' => 'deliveryAddress',
				'dCt' => 'deliveryCity',
				'dSt' => 'deliveryState',
				'dZp' => 'deliveryZip',
				'bNm' => 'billName',
				'bAd1' => 'billAddress1',
				'bAd2' => 'billAddress2',
				'bAd3' => 'billAddress3',
				'bAd4' => 'billAddress4',
				'rTit' => 'title',
				'nt1' => 'note1',
				'nt2' => 'note2',
				'nt3' => 'note3',
				'nt4' => 'note4'
			) as $field => $form)
		{
			$val = stripslashes($_POST[$form]);
			if ($val != $bill->$field)
			{
				$fields[$field] = '\'' . db_escape($val) . '\'';
				$audit[$field] = array($bill->$field, $val);
			}
		}

		// Numerical fields
		foreach(array
			(
				'iid' => 'period'
			) as $field => $form)
		{
			if (!preg_match('/^\d+$/', $_POST[$form]))
				$errorList[] = 'Invalid ' . $form;
			else
			{
				$val = intval($_POST[$form]);
				if ($val != $bill->$field)
				{
					$fields[$field] = $val;
					$audit[$field] = array($bill->$field, $val);
				}
			}
		}

		// Route is a special numerical case
		if (!preg_match('/^\d+$/', $_POST['route']))
			$errorList[] = 'Invalid route';
		else
		{
			$val = rid2title(intval($_POST['route']));
			if ($val != $bill->rt)
			{
				$fields['rt'] = '\'' . $val . '\'';
				$audit['rt'] = array($bill->rt, $val);
			}
		}

		// Currency fields
		foreach (array
			(
				'fwd' => 'previous',
				'rate' => 'rate',
				'pmt' => 'payments',
				'adj' => 'adjustments',
				'bal' => 'total'
			) as $field => $form)
		{
			if (!preg_match('/^[-+]?[0-9]*\.?[0-9]+$/', $_POST[$form]))
				$errorList[] = 'Invalid dollar amount in ' . $form;
			else
			{
				$val = sprintf('$%01.2f', floatval($_POST[$form]));
				if ($val != $bill->$field)
				{
					$fields[$field] = '\'' . $val . '\'';
					$audit[$field] = array($bill->$field, $val);
				}
			}
		}

		// Dates
		foreach (array
			(
				'due' => 'dueDate',
				'dts' => 'periodStart',
				'dte' => 'periodEnd',
			) as $field => $form)
		{
			$val = strtotime($_POST[$form]);
			$tmp = strtotime($bill->$field);
			if ($val != $tmp)
			{
				$val = strftime('%m/%d/%Y', $val);
				$fields[$field] = '\'' . $val . '\'';
				$audit[$field] = array(strftime('%Y-%m-%d', $tmp), $val);
			}
		}

		// Update record if needed
		if (count($errorList) > 0)
			$message = '<span>Error(s) prevented updating Bill</span>';
		else
		{
			if (count($fields) > 0)
			{
				do
				{
					// Start transaction
					$result = db_query(SQL_TRANSACTION);
					if (!$result)
						break;

					// Update
					$result = db_update('customers_bills', array('iid' => $IID,
							'cid' => '\'' . sprintf('%06d', $CID) . '\''), $fields);
					if (!$result)
						break;
					
					// Update the database
					$result = db_query(SQL_COMMIT);
					if (!$result)
						break;
					$message = '<span>Updated bill successfully!</span>';
					audit('Updated bill from ' . iid2title($bill->iid) . ' (id = ' . sprintf('%04d', $bill->iid)
							. ') for customer ' . sprintf('%06d', $bill->cid) . '. ' . audit_update($audit));
					$err = ERR_SUCCESS;
					
					// Retreive updated bill
					if (isset($fields['iid']))
						$IID = $fields['iid'];
					$bill = lup_c_bill($CID, $IID);
					if ($err < ERR_SUCCESS)
					{
						echo fatal_error('Edit Bill', $errText);
						return;
					}
				} while (false);

				// Finish up
				if ($err < ERR_SUCCESS)
				{
					// Undo everything
					db_query(SQL_ROLLBACK);

					// Let user know it failed
					$message = '<span>Bill update failed!</span>';
				}
			}
			else
				$message = '<span>No Changes To Save</span>';
		}
	}

	$smarty->assign('action', $_SERVER['PHP_SELF']);
	$smarty->assign('CID', $CID);
	$smarty->assign('IID', $IID);
	$smarty->assign('ROOT', ROOT);
	$smarty->assign('customer', $customer);
	$smarty->assign('clientName', get_config('client-name', 'ERROR'));
	$smarty->assign('clientAddress1', get_config('client-address-1', 'ERROR'));
	$smarty->assign('clientAddress2', get_config('client-address-2', 'ERROR'));
	$smarty->assign('clientTelephone', get_config('client-telephone', 'ERROR'));
	foreach(array
		(
			'id' => $bill->cid,
			'period' => intval($bill->iid),
			'deliveryName' => $bill->dNm,
			'deliveryAddress' => $bill->dAd,
			'deliveryCity' => $bill->dCt,
			'deliveryState' => $bill->dSt,
			'deliveryZip' => $bill->dZp,
			'previous' => substr($bill->fwd, 1),
			'payments' => substr($bill->pmt, 1),
			'title' => $bill->rTit,
			'rate' => substr($bill->rate, 1),
			'adjustments' => substr($bill->adj, 1),
			'total' => substr($bill->bal, 1),
			'billName' => $bill->bNm,
			'billAddress1' => $bill->bAd1,
			'billAddress2' => $bill->bAd2,
			'billAddress3' => $bill->bAd3,
			'billAddress4' => $bill->bAd4,
			'dts' => $bill->dts,
			'dte' => $bill->dte,
			'note1' => $bill->nt1,
			'note2' => $bill->nt2,
			'note3' => $bill->nt3,
			'note4' => $bill->nt4,
			'export' => $bill->export,
			'rateType' => $bill->rateType,
			'rateOverride' => $bill->rateOverride,
			'periodStart' => $bill->dts,
			'periodEnd' => $bill->dte,
			'dueDate' => $bill->due
		) as $field => $default)
	{
		if (isset($_POST[$field]))
			$smarty->assign($field, $_POST[$field]);
		else
			$smarty->assign($field, $default);
	}
	$smarty->assign('route', title2rid($bill->rt));
	$smarty->assign('rateTypeOptions', array(RATE_STANDARD => 'Standard', RATE_REPLACE => 'Override', RATE_SURCHARGE => 'Surcharge'));
	$smarty->assign('exportOptions', array('Y' => 'Yes', 'N' => 'No'));
	$smarty->assign('billCreated', $bill->created == 0 ? 'Unknown' : strtotime($bill->created));
	$smarty->assign('billUpdated', $bill->when == 0 ? 'unknown' : strtotime($bill->when));
	$smarty->assign('message', $message);
	$smarty->assign('errorList', $errorList);
	$smarty->display('popups/customers/bills/edit.tpl');
?>
