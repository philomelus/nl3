<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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

define('ROOT', '../../');

require_once 'inc/security.inc.php';

define('PAGE', SCP_ADD);

require_once 'inc/login.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/sql.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/customers.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;
global $errors;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Customers / Flag Stops');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Customers / Payments / Add';

$message = '';
$errors = array();
$custInfo = array();

populate_routes();
populate_types();

if (isset($_POST['action']))
{
    do
    {
        // Validate customer id (use REQUEST as it can be both GET/POST come here)
        if (strlen($_POST['cid']) == 0)
        {
            $errors[] = 'Missing <b>Customer ID</b>.';
            break;
        }
        elseif (!preg_match('/^[ ]*[[:digit:]]+[ ]*$/', $_POST['cid']))
        {
            $errors[] = "<b>Customer ID</b> isn't valid.";
            break;
        }
        else
        {
            // We have a cid that appears valid ... get the customer info
            $custInfo = lup_customer(intval($_POST['cid']));
            if ($err < ERR_SUCCESS  || $_POST['action'] == "Show")
                break;
        }

        // Validate payment type
        if (!isset($_POST['type']) || strlen($_POST['type']) == 0)
        {
            $errors[] = '<b>Type</b> missing or invalid.';
        }
        elseif (!preg_match('/^[ ]*[[:digit:]][ ]*$/', $_POST['type']))
        {
            $errors[] = 'Unrecoginized payment <b>Type</b>.';
        }
        else
        {
            // Validate ID if needed
            if (intval($_POST['type']) != PAYMENT_CASH)
            {
                if (strlen($_POST['id']) == 0)
                {
                    $errors[] = 'Missing <b>ID</b>';
                    break;
                }
            }
        }
        
        // Make sure amount appears valid
        if (!isset($_POST['amount']) || strlen($_POST['amount']) == 0)
        {
            $errors[] = '<b>Amount</b> missing.</span>';
        }
        elseif (!preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_POST['amount']))
        {
            $errors[] = '<b>Amount</b> invalid.';
        }
        elseif (floatval($_POST['amount']) == 0)
        {
            $errors[] = '<b>Amount</b> is invalid (payment of $0.00?).';
        }
            
        // If tip provided, make sure it's valid
        if (strlen($_POST['tip']) > 0)
        {
            if (!preg_match('/^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$/', $_POST['tip']))
            {
                $errors[] = '<b>Tip</b> is invalid.';
            }
        }
    
        // Pass errors along without changing anything
        if (count($errors) > 0)
            break;
        
        // Start the transaction to prevent payment without updating customer record
        $result = $DB->query(SQL_TRANSACTION);
        if (!$result)
            break;
        
        // Build the payment query
        $query = "INSERT INTO `customers_payments` SET `created` = NOW()";
        $query .= ", `customer_id` = " . intval($_REQUEST['cid']) . ", ";
        switch (intval($_POST['type']))
        {
        case PAYMENT_CHECK:
            $query .= "`type` = 'CHECK', `extra1` = '" . db_escape(stripslashes($_POST['id'])) . "'";
            break;
            
        case PAYMENT_MONEYORDER:
            $query .= "`type` = 'MONEYORDER', `extra1` = '" . db_escape(stripslashes($_POST['id'])) . "'";
            break;
            
        case PAYMENT_CASH:
            $query .= "`type` = 'CASH'";
            break;
        }
        $query .= ", `amount` = " . floatval($_POST['amount'])
                                  . ", `period_id` = " . $Period['period'];
        if (strlen($_POST['tip']) > 0)
            $query .= ", `tip` = " . $_POST['tip'];
        if (strlen($_POST['notes']) > 0)
            $query .= ", `note` = '" . db_escape(stripslashes($_POST['notes'])) . "'";

        // Insert payment
        $result = $DB->query($query);
        if (!$result)
        {
            $err = ERR_DB_QUERY;
            $errContext = $CONTEXT;
            $errCode = $DB->errno;
            $errText = $DB->error;
            $errQuery = $query;
            $DB->rollback();
        }

        // Build query to update customer record
        $pid = $DB->insert_id;
        $query = "UPDATE `customers` SET `lastPayment` = " . $pid
               . ", `balance` = `balance` - " . (floatval($_POST['amount']) - floatval($_POST['tip']))
               . " WHERE `id` = " . $_REQUEST['cid'] . " LIMIT 1";
    
        // Update customer record
        $result = $DB->query($query);
        if (!$result)
        {
            $err = ERR_DB_QUERY;
            $errContext = $CONTEXT;
            $errCode = $DB->errno;
            $errText = $DB->error;
            $errQuery = $query;
            $DB->rollback();
        }
    
        // Database work done, so commit the changes
        $result = $DB->commit();
        if (!$result)
        {
            $err = ERR_DB_QUERY;
            $errContext = $CONTEXT;
            $errCode = $DB->errno;
            $errText = $DB->error;
            $errQuery = SQL_COMMIT;
        }
    
        // Record what was just done
        $temp = '';
        switch (intval($_POST['type']))
        {
        case PAYMENT_CHECK:
            $temp = "type = CHECK. id = '" . stripslashes($_POST['id']) . '\'. ';
            break;
                
        case PAYMENT_MONEYORDER:
            $temp = 'type = MONEY ORDER. id = \'' . stripslashes($_POST['id']) . '\'. ';
            break;
                
        case PAYMENT_CASH:
            $temp = 'type = CASH. ';
            break;
        }
        audit('Added payment to customer ' . $_REQUEST['cid'] . ". "
              . $temp
              . 'amount = $' . sprintf('%01.2f', $_POST['amount']) . '. '
              . 'period_id = ' . $Period[P_TITLE] . ' (id = ' . $Period[P_PERIOD] . '). '
              . 'tip = $' . sprintf('%01.2f', $_POST['tip']) . '. '
              . 'note = \'' . stripslashes($_POST['notes']) . '\'.');
    
        // Create return message (before clearing variables...)
        $message .= '<div>Added payment of ' . sprintf('$%01.2f', floatVal($_POST['amount']))
                                             . ' to customer ' . gen_customerid($_REQUEST['cid']) . '.</div><br />';
            
        // It was successful, so clear out variables for next one
        $_REQUEST['cid'] = "";
        $_POST['type'] = PAYMENT_CHECK;
        $_POST['id'] = "";
        $_POST['amount'] = "";
        $_POST['tip'] = "";
        $_POST['notes'] = "";
    } while (false);
}
    
// If called bia GET and customer ID provided ...
if (count($custInfo) == 0 && isset($_GET['cid'])
        && preg_match('/^[ ]*[[:digit:]]+[ ]*$/', $_GET['cid']))
{
    $custInfo = lup_customer(intval($_GET['cid']));
    // Errors will propigate automatically ...
}
if (count($custInfo) > 0)
{
    $smarty->assign('custInfo', array(
        'name' => valid_name($custInfo->firstName, $custInfo->lastName),
        'address' => $custInfo->address,
        'telephone' => $custInfo->telephone,
        'route' => valid_text($Routes[$custInfo->route_id]),
        'type' => $DeliveryTypes[$custInfo->type_id]['abbr'],
        'color' => sprintf('dt%04d', $custInfo->type_id)));
}
else
    $smarty->assign('custInfo', null);

if (isset($_REQUEST['cid']))
    $cid = stripslashes($_REQUEST['cid']);
else
    $cid = '';
$smarty->assign('cid', $cid);

if (isset($_POST['id']))
    $id = stripslashes($_POST['id']);
else
    $id = '';
$smarty->assign('id', $id);

if (isset($_POST['amount']))
    $amount = stripslashes($_POST['amount']);
else
    $amount = '';
$smarty->assign('amount', $amount);

if (isset($_POST['tip']))
    $tip = stripslashes($_POST['tip']);
else
    $tip = '';
$smarty->assign('tip', $tip);

if (isset($_POST['notes']))
    $notes = stripslashes($_POST['notes']);
else
    $notes = '';
$smarty->assign('notes', $notes);

$smarty->assign('message', $message);

$temp = array
(
    PAYMENT_CHECK => "Check",
    PAYMENT_MONEYORDER => "Money Order",
    PAYMENT_CASH => "Cash",
);
$smarty->assign('paymentOptions', $temp);

if (isset($_POST['type']))
    $temp = stripslashes($_POST['type']);
else
    $temp = PAYMENT_CHECK;
$smarty->assign('type', $temp);

$smarty->display('customers/payments/addnew.tpl');
?>
