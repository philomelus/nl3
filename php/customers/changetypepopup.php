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

	set_include_path('..' . PATH_SEPARATOR . get_include_path());
	
	require_once 'inc/security.inc.php';
	
	define('PAGE', SCW_CHANGETYPE);
	
	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/popups/customers/servicetypes.inc.php';
	require_once 'inc/sql.inc.php';
	
	//-------------------------------------------------------------------------
	
    global $smarty;

	$customer = lup_customer(intval($_REQUEST['cid']));
	$fsid = get_config('flag-stop-type');
	if ($fsid == CFG_NONE)
		$fsid = -1;
	
	populate_routes();
	populate_types();
	$resultHtml = '';
	
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Submit')
	{
		do
		{
			// Validate date
			$dateText = valid_date('when', 'date');
			if ($err < ERR_SUCCESS)
			{
				$errContext = 'Adding customer delivery type change';
				break;
			}
			$date = strtotime($dateText);
			if ($date < $customer->started)
			{
				$err = ERR_FAILURE;
				$errCode = ERR_FAILURE;
				$errContext = 'Adding customer delivery type change';
				$errText = 'Date is before customer starts';
				$errQuery = '';
				break;
			}
			
			// Make sure transformation appears to be valid
			if ($customer->type_id == intval($_REQUEST['type']))
			{
				$err = ERR_FAILURE;
				$errCode = ERR_FAILURE;
				$errContext = 'Service Type Popup';
				$errText = 'Cannot convert to the same type the customer already is';
				$errQuery = '';
				break;
			}
			
			// Determine whether to ignore this change for billing
			$include = false;
			if (isset($_REQUEST['include']) && $_REQUEST['include'] == 'Y')
				$include = true;
			
			// Start transaction
			$result = db_query(SQL_TRANSACTION);
			if (!$result)
				break;

			do
			{
				// Add the change delivery type change
				$hid = db_insert('customers_service_types', array
                    (
                        'period_id' => 'NULL',
                        'created' => 'NOW()',
                        'customer_id' => $_REQUEST['cid'],
                        'when' => '\'' . strftime('%Y-%m-%d', $date) . '\'',
                        'type_id_from' => $customer->type_id,
                        'type_id_to' => intval($_REQUEST['type']),
                        'why' => '\'Customer request\'',
                        'ignoreOnBill' => '\'' . ($include ? 'N' : 'Y') . '\'',
                        'note' => '\'' . db_escape(stripslashes($_REQUEST['notes'])) . '\''
					));
				if (!$hid)
					break;
				
				// For now, go ahead and commit the change to the customer record
				// BUGBUG:  This shouldn't take effect until the scheduled date, but sometimes a route list
				//          with the change already applied needs to be printed.  What needs to be done is the
				//          reports need to be able to handle generating a report for a _specific_ date, which
				//          would provide the context for the change to be shown correctly.  However, there
				//          currently isn't a way to apply the change later...
				$query = "UPDATE `customers` SET `type_id` = " . intval($_REQUEST['type']) . " WHERE `id` = " . $customer->id;
				db_query($query);
			} while (false);
			
			if ($err >= ERR_SUCCESS)
			{
				// Apply the changes
				db_query(SQL_COMMIT);
				
				// Set result message
				$resultHtml = '<div>Change from ' . tid2abbr($customer->type_id) . ' to '
						. tid2abbr($_REQUEST['type']) . ' on ' . strftime('%m/%d/%Y', $date) . '</div>';

				audit('Added type change (id = ' . sprintf('%08d', $hid)
						. ') for customer ' . sprintf('%06d', $customer->id) . '. '
						. 'created = ' . strftime('%Y-%m-%d %H:%M:%S', time()) . '. '
						. 'when = ' . strftime('%Y-%m-%d', $date) . '. '
						. 'type_id_from = ' . tid2abbr($customer->type_id) . ' (id = ' . sprintf('%04d', $customer->type_id) . '). '
						. 'type_id_to = ' . tid2abbr(intval($_REQUEST['type'])) . ' (id = ' . sprintf('%04d', intval($_REQUEST['type'])) . '). '
						. 'why = \'Customer request\'. '
						. 'ignoreOnBill = ' . ($include ? 'FALSE' : 'TRUE') . '. '
						. 'note = \'' . stripslashes($_REQUEST['notes']) . '\'.');

				// Clear out controls
				unset($_REQUEST['whenm']);
				unset($_REQUEST['whend']);
				unset($_REQUEST['notes']);
				unset($_REQUEST['type']);

				// Update customer info
				$customer = lup_customer($customer->id);
			}
			else
			{
				db_query(SQL_ROLLBACK);
			}
		} while (false);
	}
	
    $script = '
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/popups/customers/servicetypes.js.php"></script>
';
    $smarty->assign('script', $script);

    $style = '';
    $smarty->assign('style', $style);

    $smarty->assign('subtitle', 'Change Delivery Type');

    $smarty->assign('action', $_SERVER['PHP_SELF']);

    $smarty->assign('result', $resultHtml);
		
    if ($customer->type_id == $fsid)
        $temp = '<div>REMINDER: Change MUST occur at the beginning of the period for Flag Stop customers.</div>';
    else
        $temp = '';
    $smarty->assign('flagStopWarning', $temp);

    $smarty->assign('name', valid_name($customer->firstName, $customer->lastName));
    $smarty->assign('address', $customer->address);
    $smarty->assign('route_id', $customer->route_id);
    $smarty->assign('type_id', $customer->type_id);

    // Get list of type changes
    $query = "SELECT * FROM `customers_service_types` WHERE `customer_id` = " . $customer->id
            . " ORDER BY `when` DESC, `created` DESC, `updated` DESC LIMIT 4";
    $changes = db_query($query);
    if ($changes)
        $count = $changes->num_rows;
    else
        $count = 0;
    $smarty->assign('count', $count);
    while ($change = $changes->fetch_object())
        $data[] = $change;
    $smarty->assign('changes', $data);
		

	if (isset($_REQUEST['type']))
		$val = intval(stripslashes($_REQUEST['type']));
	else
	    $val = -1;
    $smarty->assign('type', $val);

	if (isset($_REQUEST['include']))
	{
		if ($_REQUEST['include'] == 'Y')
			$temp = true;
		else
			$temp = false;
	}
	else
        $temp = true;   // Default value
    $smarty->assign('include', $temp);

    if (isset($_REQUEST['notes']))
        $val = htmlspecialchars(stripslashes($_REQUEST['notes']));
    else
        $val = '';
    $smarty->assign('notes', $val);

    $smarty->assign('cid', $customer->id);

    $smarty->display('customers/changetypepopup.tpl');
?>
