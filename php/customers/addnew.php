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

require_once 'inc/security.inc.php';

define('PAGE', SC_ADDNEW);

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

global $DB;
global $err, $errCode, $errContext, $errQuery, $errText;
global $errors, $DeliveryTypes, $Period, $message;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Customers / Add');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('addResults', isset($_POST['action']));

$errContext = 'Customers / Add';

populate_routes();
if ($err < ERR_SUCCESS)
{
    echo gen_error_page($errContext, 'Unable to get routes from database', ROOT);
    exit();
}

populate_types();
if ($err < ERR_SUCCESS)
{
    echo gen_error_page($errContext, 'Unable to get delivery types from database', ROOT);
    exit();
}

// Handle submission
if (isset($_POST['action']))
{
    // Initialize message for failure
    $message = '<span>Adding new customer failed.</span>';
    
    $errors = array();

    // At least the delivery name is required
    if (!isset($_POST['name_first']) || strlen($_POST['name_first']) == 0)
        $errors[] = 'Delivery name is required.';

    // Validate regex capable required fields
    foreach(array
        (
            'type_id' => array('Delivery Type', '/^[[:digit:]]{1,8}$/'),
            'route_id' => array('Route', '/^[[:digit:]]{1,8}$/'),
            'delivery_address1' => array('Delivery Address', '/^.+$/'),
            'delivery_city' => array('Delivery City', '/^.+$/'),
            'delivery_state' => array('Delivery State', '/^[A-Z]{2}$/'),
            'delivery_zip' => array('Delivery Zip', '/^[0-9]{5}(-[0-9]{4})?$/'),
        ) as $field => $info)
    {
        $$field = $_POST[$field];
        if (!preg_match($info[1], $$field))
            $errors[] = $info[0] . ' is invalid';
    }
    $delivery_address2 = $_POST['delivery_address2'];
    
    // Started date validity
    $started = valid_dateNew('started');
    if (!$started)
        $error[] = 'Start date isn\'t valid';
    if ($started < $Period[P_START])
        $errors[] = 'Started Date is in previous period';

    // Delivery Telephone 1 provided
    if (!isset($_POST['delivery_telephone_1'])
            || strlen($_POST['delivery_telephone_1']) == 0)
    {
        $errors[] = 'Delivery Telephone 1 is required';
    }
    
    // Telephone numbers validity
    // TODO: BUGBUG:  No check for duplicate type (e.g. two Main types).
    //       Not sure if it even matters...
    foreach(array
        (
            'delivery' => 'Delivery',
            'bill' => 'Billing'
        ) as $type => $type_display)
    {
        for($n = 1; $n < 4; ++$n)
        {
            $v = $type . '_telephone_' . $n;
            if (!empty($_POST[$v]))
            {
                if (!preg_match('/^\([[:digit:]]{3}\) [[:digit:]]{3}-[[:digit:]]{4}(?: x [[:digit:]]+)?$/', $_POST[$v]))
                    $errors[] = $type_display . ' Telephone ' . $n . ' is invalid';
            }
        }
    }

    // Billing Address, City, State, Zip validity
    if (!empty($_POST['bill_address1'])
        || !empty($_POST['bill_address2'])
        || !empty($_POST['bill_city'])
        || !empty($_POST['bill_zip']))
    {
        foreach(array
            (
                'bill_address1' => array('Delivery Address', '/^.+$/'),
                'bill_city' => array('Delivery City', '/^.+$/'),
                'bill_state' => array('Delivery State', '/^[A-Z]{2}$/'),
                'bill_zip' => array('Delivery Zip', '/^[0-9]{5}(-[0-9]{4})?$/'),
            ) as $field => $info)
        {
            $$field = $_POST[$field];
            if (!preg_match($info[1], $$field))
                $errors[] = $info[0] . ' is invalid';
        }
    }
    else
        $bill_address1 = $bill_address2 = $bill_city = $bill_state = $bill_zip = false;
    
    // Only update if there are no errors
    if (empty($errors))
    {
        // Update database:  Wrap in transaction so any failures undo everything
        $err = ERR_SUCCESS;
        if (!db_tr_start())
        {
            echo gen_error_page($errContext, 'Unable to update database', ROOT);
            exit();
        }

        do
        {
            // Add customer record
            $fields = array
                (
                    'route_id' => intval($route_id),
                    'type_id' => intval($type_id),
                    'active' => '\'Y\'',
                    'routeList' => '\'Y\'',
                    'started' => '\'' . strftime('%Y-%m-%d', $started) . '\'',
                    'rateType' => '\'STANDARD\'',
                    'rateOverride' => 0,
                    'billType' => intval($type_id),
                    'billBalance' => 0,
                    'billStopped' => '\'Y\'',
                    'billCount' => 1,
                    'billPeriod' => 'NULL',
                    'billQuantity' => 1,
                    'billStart' => 'NULL',
                    'billEnd' => 'NULL',
                    'billDue' => 'NULL',
                    'balance' => 0,
                    'lastPayment' => 'NULL',
                    'billNote' => '\'' . db_escape($_POST['bill_notes']) . '\'',
                    'notes' => '\'' . db_escape($_POST['notes']) . '\'',
                    'deliveryNote' => '\'' . db_escape($_POST['delivery_notes']) . '\''
                );
            $customer_id = db_insert('customers', $fields, 'id');
            if (!$customer_id)
                break;
            audit(sprintf('Added new customer %08d. ', $customer_id) . audit_add($fields));

            // Add names (if provided)
            foreach(array
                (
                    'name' => array('', true, NAME_C_DELIVERY1),
                    'alt_name' => array('Alternate ', false, NAME_C_DELIVERY2),
                    'bill_name' => array('Billing ', false, NAME_C_BILLING1),
                    'alt_bill_name' => array('Alternate Billing ', false, NAME_C_BILLING2)
                ) as $name => $info)
            {
                if ($info[1] || !empty($_POST[$name . '_first'])
                        || !empty($_POST[$name . '_last'])
                        || !empty($_POST[$name . '_surname']))
                {
                    $fields = array
                        (
                            'customer_id' => $customer_id,
                            'sequence' => $info[2],
                            'title' => '\'' . db_escape($_POST[$name . '_title']) . '\'',
                            'first' => '\'' . db_escape($_POST[$name . '_first']) . '\'',
                            'last' => '\'' . db_escape($_POST[$name . '_last']) . '\'',
                            'surname' => '\'' . db_escape($_POST[$name . '_surname']) . '\'',
                        );
                    if (!db_insert('customers_names', $fields))
                        break;
                    audit('New customer ' . strtolower($info[0]) . 'name. ' . audit_add($fields));
                }
            }
            
            // Add delivery address
            $fields = array
                (
                    'customer_id' => $customer_id,
                    'sequence' => ADDR_C_DELIVERY,
                    'address1' => '\'' . db_escape($delivery_address1) . '\'',
                    'address2' => '\'' . db_escape($delivery_address2) . '\'',
                    'city' => '\'' . db_escape($delivery_city) . '\'',
                    'state' => '\'' . db_escape($delivery_state) . '\'',
                    'zip' => '\'' . db_escape($delivery_zip) . '\''
                );
            if (!db_insert('customers_addresses', $fields))
                break;
            audit('New customer delivery address. ' . audit_add($fields));
            
            // Add billing address (if provided)
            if (!empty($bill_address1) || !empty($bill_address2)
                    || !empty($bill_city) || !empty($bill_zip))
            {
                $fields = array
                    (
                        'customer_id' => $customer_id,
                        'sequence' => ADDR_C_BILLING,
                        'address1' => '\'' . db_escape($bill_address1) . '\'',
                        'address2' => '\'' . db_escape($bill_address2) . '\'',
                        'city' => '\'' . db_escape($bill_city) . '\'',
                        'state' => '\'' . db_escape($bill_state) . '\'',
                        'zip' => '\'' . db_escape($bill_zip) . '\''
                    );
                if (!db_insert('customers_addresses', $fields))
                    break;
                audit('New customer billing address. ' . audit_add($fields));
            }
            
            // Add telephone numbers
            foreach(array
                (
                    'delivery_telephone_1' => array('delivery telephone 1', TEL_C_DELIVERY1),
                    'delivery_telephone_2' => array('delivery telephone 2', TEL_C_DELIVERY2),
                    'delivery_telephone_3' => array('delivery telephone 3', TEL_C_DELIVERY3),
                    'bill_telephone_1' => array('billing telephone 1', TEL_C_BILLING1),
                    'bill_telephone_2' => array('billing telephone 2', TEL_C_BILLING2),
                    'bill_telephone_3' => array('billing telephone 3', TEL_C_BILLING3)
                ) as $field => $info)
            {
                if (!empty($$field))
                {
                    $t = $field . '_type';
                    $fields = array
                        (
                            'customer_id' => $customer_id,
                            'sequence' => $info[1],
                            'created' => 'NOW()',
                            'type' => '\'' . db_escape($$t) . '\'',
                            'number' => '\'' . $$field . '\''
                        );
                    if (!db_insert('customers_telephones', $fields))
                        exit();
                    audit('New customer ' . $info[0] . '. ' . audit_add($fields));
                }
            }

            // Add the customer to the sequence table.
            // Make sure that there is at least 1 other customer that has been sequenced.
            $query = 'SELECT COUNT(*) FROM `routes_sequence` WHERE `route_id` = ' . $route_id
                    . ' AND `order` < ' . CUSTOMER_ADDSEQUENCE;
            $sequencedCount = db_query_result($query);
            if ($err < ERR_SUCCESS)
                break;
            if ($errCode == ERR_NOTFOUND)
                $sequencedCount = 0;
            $fields = array
                (
                    'tag_id' => $customer_id,
                    'route_id' => $route_id,
                    'order' => CUSTOMER_ADDSEQUENCE
                );
            if ($sequencedCount == 0)
                $fields['order'] = 1;
            if (!db_insert('routes_sequence', $fields))
                break;
            audit('New customer route sequence. ' . audit_add($fields));
            
            // Add new customer start if their delivery type says to
            if ($DeliveryTypes[$type_id]['newChange'])
            {
                $fields = array
                    (
                        'customer_id' => $customer_id,
                        'period_id' => 'NULL',
                        'created' => 'NOW()',
                        'type' => '\'' . SERVICE_START . '\'',
                        'when' => '\'' . strftime('%Y-%m-%d', $started) . '\'',
                        'why' => '\'New Customer\'',
                        'ignoreOnBill' => '\'N\'',
                        'note' => '\'\''
                    );
                if (!db_insert('customers_service', $fields))
                    break;
                audit(sprintf('New customer new start (%08d). ', $DB->insert_id) . audit_add($fields));
            }
        } while (false);
    }
    
    // If no errors up to here, then add the customer!
    if (count($errors) == 0 && $err >= ERR_SUCCESS)
    {
        db_tr_commit();
        $message = '<span>New customer id is '
                . CustomerViewLink($customer_id) . sprintf('%08d', $customer_id) . '</a></span>';
        unset($_POST['name_title']);
        unset($_POST['name_first']);
        unset($_POST['name_last']);
        unset($_POST['name_surname']);
        unset($_POST['alt_name_title']);
        unset($_POST['alt_name_first']);
        unset($_POST['alt_name_last']);
        unset($_POST['alt_name_surname']);
        unset($_POST['type_id']);
        unset($_POST['route_id']);
        unset($_POST['startedd']);
        unset($_POST['startedm']);
        unset($_POST['startedy']);
        unset($_POST['delivery_address1']);
        unset($_POST['delivery_address2']);
        unset($_POST['delivery_city']);
        unset($_POST['delivery_state']);
        unset($_POST['delivery_zip']);
        unset($_POST['delivery_telephone_1_type']);
        unset($_POST['delivery_telephone_1']);
        unset($_POST['delivery_telephone_2_type']);
        unset($_POST['delivery_telephone_2']);
        unset($_POST['delivery_telephone_3_type']);
        unset($_POST['delivery_telephone_3']);
        unset($_POST['bill_name_title']);
        unset($_POST['bill_name_first']);
        unset($_POST['bill_name_last']);
        unset($_POST['bill_name_surname']);
        unset($_POST['alt_bill_name_title']);
        unset($_POST['alt_bill_name_first']);
        unset($_POST['alt_bill_name_last']);
        unset($_POST['alt_bill_name_surname']);
        unset($_POST['bill_address1']);
        unset($_POST['bill_address2']);
        unset($_POST['bill_city']);
        unset($_POST['bill_state']);
        unset($_POST['bill_zip']);
        unset($_POST['bill_telephone_1_type']);
        unset($_POST['bill_telephone_1']);
        unset($_POST['bill_telephone_2_type']);
        unset($_POST['bill_telephone_2']);
        unset($_POST['bill_telephone_3_type']);
        unset($_POST['bill_telephone_3']);
        unset($_POST['notes']);
        unset($_POST['bill_notes']);
        unset($_POST['delivery_notes']);
    }
    else
    {
        db_tr_rollback();
        $message = '<span>Adding new customer failed.</span>';
    }
}

if (!isset($_POST['delivery_state']))
{
    $temp = get_config('customers-default-delivery-state');
    if ($temp != CFG_NONE)
        $_POST['delivery_state'] = $temp;
}

if (!isset($_POST['bill_state']))
{
    $temp = get_config('customers-default-billing-state');
    if ($temp != CFG_NONE)
        $_POST['bill_state'] = $temp;
}

foreach(array
    (
        'type_id',
        'route_id',
        'startedm', 'startedd', 'startedy',
        'bill_notes',
        'delivery_notes',
        'notes',
        'delivery_address1', 'delivery_address2', 'delivery_city', 'delivery_state', 'delivery_zip',
        'bill_address1', 'bill_address2', 'bill_city', 'bill_state', 'bill_zip'
    ) as $field)
{
    if (isset($_POST[$field]))
        $smarty->assign($field, $_POST[$field]);
    else
        $smarty->assign($field, null);
}

if (isset($_POST['startedm'])
        && isset($_POST['startedd'])
        && isset($_POST['startedy']))
{
    $smarty->assign('time', sprintf('%04d-%02d-%02d', intval($_POST['startedy']),
            intval($_POST['startedm']), intval($_POST['startedd'])));
}
else
    $smarty->assign('time', time());

if (!isset($_POST['delivery_telephone_1_t']))
    $_POST['delivery_telephone_1_t'] = 'Main';
    
if (!isset($_POST['delivery_telephone_2_t']))
    $_POST['delivery_telephone_2_t'] = 'Alternate';
    
if (!isset($_POST['delivery_telephone_3_t']))
    $_POST['delivery_telephone_3_t'] = 'Mobile';
    
if (!isset($_POST['bill_telephone_1_t']))
    $_POST['bill_telephone_1_t'] = 'Main';
    
if (!isset($_POST['bill_telephone_2_t']))
    $_POST['bill_telephone_2_t'] = 'Alternate';
    
if (!isset($_POST['bill_telephone_3_t']))
    $_POST['bill_telephone_3_t'] = 'Mobile';
    
$smarty->assign('action', $_SERVER['PHP_SELF']);
$smarty->display('customers/addnew.tpl');
	
?>
