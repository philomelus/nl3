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

    define('ROOT', '../');

	//set_include_path('..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';
	
	define('PAGE', SCW_ADJUSTMENT);
	
	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/popups/customers/adjustments.inc.php';
	require_once 'inc/sql.inc.php';
	
	if (!preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['cid']))
	{
		echo invalid_parameters('Add Adjustment', 'Customers/AdjustmentPopup.php');
		return;
	}

    global $smarty;
    
	define('CREDIT', 0);
	define('CHARGE', 1);
	
    $smarty->assign('CREDIT', CREDIT);
    $smarty->assign('CHARGE', CHARGE);

	$customer = lup_customer(intval($_REQUEST['cid']));
    $smarty->assign('customer', $customer);
	
	populate_routes();
	populate_types();
	
	$message = '';
	
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Submit')
	{
		$fields = array();
		do
		{
			// Validate dollar amount
			if (!preg_match('/^([0-9]+|[0-9]{1,3}(,[0-9]{3})*)(\.[0-9]{1,2})?$/', $_POST['amount']))
			{
				$err = ERR_INVALID;
				$errCode = ERR_INVALID;
				$errContext = 'Adding adjustment';
				$errQuery = '';
				$errText = 'Invalid amount (numbers and period only)';
				break;
			}
			if ($_POST['type'] == CREDIT)
				$fields['amount'] = floatval(-str_replace(',', '', $_POST['amount']));
			else
				$fields['amount'] = floatval(str_replace(',', '', $_POST['amount']));
			$fields['period_id'] = 'NULL';
            
			// Make sure they give a reason
			if (strlen($_REQUEST['desc']) == 0)
			{
				$err = ERR_INVALID;
				$errCode = 0;
				$errContext = 'Adding adjustment';
				$errQuery = "";
				$errText = 'Missing reason (required)';
				break;
			}
			$fields['desc'] = "'" . db_escape(stripslashes($_POST['desc'])) . "'";
			$fields['created'] = 'NOW()';
			$fields['customer_id'] = $customer->id;
			$fields['note'] = "'" . db_escape(stripslashes($_POST['notes'])) . "'";
			$result = db_insert('customers_adjustments', $fields);
		} while (false);
		if ($err >= ERR_SUCCESS)
		{
			$message = '<span>Created adjustment '
					. sprintf('A%08d', $result) . '</span>';
			
			audit('Added adjustment (id = ' . sprintf('%08d', $result) . ') for customer '
					. sprintf('C%06d', $customer->id)
					. audit_add($fields));
		}
		else
		{
			$message = '<span>Adjustment not created!</span>';
			$errContext = 'Adding adjustment';
		}
	}
	
	$script = '<script src="../js/popups/customers/adjustments.js.php"></script>';
    $smarty->assign('script', $script);

	$style = '';
    $smarty->assign('style', $style);

    $smarty->assign('message', $message);

	// Generate customer information
    $smarty->assign('name', valid_name($customer->firstName, $customer->lastName));
    
    // Get list of adjustments
    $query = '
SELECT
    *
FROM
    `customers_adjustments`
WHERE
    `customer_id` = ' . $customer->id . '
ORDER BY
    `period_id` DESC, `created` DESC
LIMIT 4
';
    $adjustments = db_query($query);
    if ($adjustments)
        $count = $adjustments->num_rows;
    else
        $count = 0;
    $smarty->assign('count', $count);

		// Add the adjustments
    $data = array();
    if ($count > 0)
    {
        while ($adjustment = $adjustments->fetch_object())
            $data[] = $adjustment;
    }
    $smarty->assign('adjustments', $data);

    $smarty->assign('action', $_SERVER['PHP_SELF']);

	if (isset($_POST['type']))
	{
		if ($_POST['type'] == CREDIT)
		{
			$val = array
				(
					0 => ' checked="checked"',
					1 => ''
				);
		}
		else
		{
			$val = array
				(
					0 => '',
					1 => ' checked="checked"'
				);
		}
	}
	else
	{
        // Set default adjustment type
		$val = array
			(
				0 => ' checked="checked"',
				1 => ''
			);
    }
    $smarty->assign('type', $val);

	if (!isset($_REQUEST['amount']))
		$_REQUEST['amount'] = '';
    $smarty->assign('amount', htmlspecialchars($_REQUEST['amount']));

    if (!isset($_REQUEST['desc']))
        $_REQUEST['desc'] = '';
    $smarty->assign('desc', htmlspecialchars(stripslashes($_REQUEST['desc'])));

    if (!isset($_REQUEST['desc']))
        $_REQUEST['notes'] = '';
    $smarty->assign('notes', htmlspecialchars(stripslashes($_REQUEST['notes'])));

    $smarty->assign('cid', $_REQUEST['cid']);

    $smarty->display('customers/adjustmentpopup.tpl');
