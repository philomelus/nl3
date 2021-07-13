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

define('ROOT', '../');

require_once 'inc/security.inc.php';

define('PAGE', SC_SEARCH);

require_once 'inc/login.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/sql.inc.php';
require_once 'inc/profile.inc.php';

require_once 'inc/audit.inc.php';
require_once 'inc/popups/customers.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Customers / Search');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Customers / Search';

$message = '';
$errors = array();

$searchList = array();
$searchListTotal = 0;
	
populate_routes_valid('Customers / Search');
populate_types_valid('Customers / Search');

$smarty->assign('doResults', isset($_POST['action']));

function post_int($name, $default)
{
    if (isset($_POST[$name]))
        return intval($_POST[$name]);
    else
        return $default;
}

function post_str($name, $default)
{
    // TODO:  Security risk ...
    if (isset($_POST[$name]))
        return $_POST[$name];
    else
        return $default;
}

if (isset($_POST['action']))
{
    $doQuery = true;
    $action = $_POST['action'];
    if ($action == 'clear')
    {
        unset($_POST['cid']);
        unset($_POST['name']);
        unset($_POST['telephone']);
        unset($_POST['address']);
        unset($_POST['postal']);
        unset($_POST['routeList']);
        unset($_POST['active']);
        unset($_POST['orid']);
        unset($_POST['ocid']);
        unset($_POST['limit']);
        unset($_POST['offset']);
        unset($_POST['route']);
        unset($_POST['type']);
        $smarty->assign('doResults', false);
        $doQuery = false;
    }
    else if ($action == 'prev')
    {
        $count = post_int('dbf_limit', 30);
        $offset = post_int('dbf_offset', 0);
        //$count = intval($_POST['dbf_limit']);
        //$offset = intval($_POST['dbf_offset']);
        $offset -= $count;
        if ($offset < 0)
            $offset = 0;
        $_POST['dbf_offset'] = $offset;
    }
    else if ($action == 'next')
    {
        $count = post_int('dbf_limit', 30);
        $offset = post_int('dbf_offset', 0);
        $_POST['dbf_offset'] = $offset + $count;
    }
    else if ($action == 'begin')
        $_POST['dbf_offset'] = 0;
    
    if ($doQuery)
    {
        // Log from Ron or June Gibson (since they require
        // hand holding on why their searches aren't working)
        // TODO:  Add global config option to enable this ...
        //        In fact, enable global logging of almost
        //        anything via config.  Use security and profile
        //        config for settings.
        // Changed to use a group instead, and moved Ron and June to
        // the new group.
        // 220520T0238 RUSSG
        $gid = isset($_SESSION['gid']) ? $_SESSION['gid'] : 'NULL';
        if ($gid == 3 && false)
        {
            // This should be disabled normally ... but it saves
            // me a lot of frustration ...
            // 150520T1940 RUSSG
            // Changed this to use an array.  Technically, I should
            // log only the fields used, not all fields every time ...
            // but that is more work than I want to put into it ATM.
            // 220520T0237 RUSSG
            $temp = array();
            foreach (array
                (
                    'Cust ID' => 'cid',
                    'Name' => 'name',
                    'Telephone' => 'telephone',
                    'Address' => 'address',
                    'Zip' => 'postal',
                    'Route List' => 'routeList',
                    'Active' => 'active',
                    'Route ID' => 'route',
                    'Type ID' => 'type'
                ) as $n => $f)
            {
                if (isset($_POST[$f]))
                    $temp[$n] = $_POST[$f];
            }
            audit('customer search: ' . audit_add($temp));
        }

        // Set the max lines if provided
        $max = post_int('dbf_limit', 30);
        $offset = post_int('dbf_offset', 0);

        // Assemble conditionals on WHERE and sub-query INNER JOIN's
        $whereCond = array();
        $aCond = array();
        $nCond = array();
        $tCond = array();
        
        // Route List (if prevents PHP runtime warning...)
        if (isset($_POST['routeList']))
        {
            // Does submission appear valid?
            if (preg_match('/^[012]$/', $_POST['routeList']))
            {
                // Update where clause with routeList query
                switch (intval($_POST['routeList']))
                {
                case 0:	$whereCond[] = '`routeList` = \'N\'';	break;
                case 1:	$whereCond[] = '`routeList` = \'Y\'';	break;
                case 2:	/* NOTHING TO DO */                     break;
                }
            }
            else
                $errors[] = 'Invalid route list option specified.';
        }
        
        // Active
        if (isset($_POST['active']))
        {
            if (preg_match('/^[012]$/', $_POST['active']))
            {
                switch (intval($_POST['active']))
                {
                case 0:	$whereCond[] = '`active` = \'N\'';	break;
                case 1:	$whereCond[] = '`active` = \'Y\'';	break;
                case 2:	/* NOTHING TO DO */                 break;
                }
            }
            else
                $errors[] = 'Invalid billing option specified.';
        }
        
        // Customer id(s)
        if (isset($_POST['cid']))
        {
            if (preg_match('/^\d{0,8}$/', $_POST['cid']))
            {
                $cid = intval($_POST['cid']);
                if ($cid > 0)
                    $whereCond[] = '`id` = ' . $cid;
            }
            else
                $errors[] = 'Invalid customer id specified.';
        }
        
        // Name
        if (isset($_POST['name']))
        {
            if (strlen($_POST['name']) > 0)
            {
                $names = explode(' ', stripslashes($_POST['name']));
                foreach($names as $name)
                {
                    $nCond[] = 'UPPER(`n`.`first`) LIKE UPPER(\'%' . db_escape($name)
                            . '%\') OR UPPER(`n`.`last`) LIKE UPPER(\'%' . db_escape($name)
                            . '%\')';
                }
            }
        }

        // Address
        if (isset($_POST['address']))
        {
            if (strlen($_POST['address']) > 0)
            {
                $parts = explode(' ', stripslashes($_POST['address']));
                foreach($parts as $part)
                {
                    $value = 'UPPER(\'%' . db_escape($part) . '%\')';
                    $aCond[] = 'UPPER(`a`.`address1`) LIKE ' . $value
                           . ' OR UPPER(`a`.`address2`) LIKE ' . $value;
                }
            }
        }

        // Postal code
        if (isset($_POST['postal']))
        {
            if (strlen($_POST['postal']) > 0)
            {
                $parts = explode(' ', stripslashes($_POST['postal']));
                foreach($parts as $part)
                    $aCond[] = '`a`.`zip` LIKE \'%' . $part . '%\'';
            }
        }

        // Telephone
        if (isset($_POST['telephone']))
        {
            if (strlen($_POST['telephone']) > 0)
            {
                $parts = explode(' ', stripslashes($_POST['telephone']));
                foreach($parts as $part)
                    $tCond[] = '`t`.`number` LIKE \'%' . $part . '%\'';
            }
        }

        // Route
        if (isset($_POST['route']))
        {
            if (preg_match('/^\d+$/', $_POST['route']))
            {
                $temp = intval($_POST['route']);
                // TODO:  Validate its a valid route id number, not just
                //        non-zero
                if ($temp > 0)
                    $whereCond[] = '`route_id` = ' . $temp;
            }
        }
        
        // Delivery Type
        if (isset($_POST['type']))
        {   
            if (preg_match('/^\d+$/', $_POST['type']))
            {
                $temp = intval($_POST['type']);
                // TODO:  Validate its a valid delivery type that exists
                //        and is active, not just non-zero
                if ($temp > 0)
                    $whereCond[] = '`type_id` = ' . $temp;
            }
        }

        // If errors, then don't touch database
        if (!empty($errors))
            // BUGBUG: TODO: THIS IS NO LONGER CORRECT!  NO EXIT BEFORE PAGE DISPLAY CODE!
            return '';

        // Convert where conditions into main query where clause
        $where = '';
        if (count($whereCond) > 0)
        {
            $and = 0;
            $where = ' WHERE';
            foreach($whereCond as $cond)
            {
                if ($and > 0)
                    $where .= ' AND';
                $where .= $cond;
                ++$and;
            }
        }

        // If address is needed, create sub query inner join with address
        // conditions
        $aJoin = '';
        if (count($aCond) > 0)
        {
            $aJoin = 'INNER JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id`';
            foreach($aCond as $cond)
                $aJoin .= ' AND ' . $cond;
        }

        // If name is needed, create sub query inner join with name conditions
        $nJoin = '';
        if (count($nCond) > 0)
        {
            $nJoin = 'INNER JOIN `customers_names` AS `n` ON `c`.`id` = `n`.`customer_id` AND (';
            $or = 0;
            foreach($nCond as $cond)
            {
                if ($or > 0)
                    $nJoin .= ' OR';
                $nJoin .= ' ' . $cond;
                ++$or;
            }
            $nJoin .= ')';
        }

        // If telephone is needed, create sub query inner join with telephone
        // conditions
        $tJoin = '';
        if (count($tCond) > 0)
        {
            $tJoin = 'INNER JOIN `customers_telephones` AS `t` ON `c`.`id` = `t`.`customer_id`';
            foreach($tCond as $cond)
                $tJoin .= ' AND ' . $cond;
        }


        // Get a count of matches using only sub query
        $query = '
SELECT
COUNT(*)
FROM
`customers` AS `c`
WHERE
`c`.`id` IN (SELECT
        DISTINCT `c`.`id`
    FROM
        `customers` AS `c` '
        . $aJoin
        . $nJoin 
        . $tJoin
        . $where
        . ')
';
        $searchListTotal = db_query_result($query);
        if ($searchListTotal === false && $err < ERR_SUCCESS)
        {
            $errors[] = 'Unable to execute query.';
            return '';
        }
        if ($action == 'end')
        {
            $offset = $searchListTotal - $max + 1;
            $_POST['dbf_offset'] = $offset;
        }

        // Now get the info that actually matches
        $query = '
SELECT
`c`.`id`,
`c`.`route_id`,
`c`.`type_id`,
`c`.`billBalance`,
`a`.`address1` AS `address`,
`t`.`number` AS `telephone`,
`n`.`first` AS `firstName`,
`n`.`last` AS `lastName`
FROM
`customers` AS `c`
INNER JOIN `customers_addresses` AS `a` ON `c`.`id` = `a`.`customer_id`
INNER JOIN `customers_telephones` AS `t` ON `c`.`id` = `t`.`customer_id`
INNER JOIN `customers_names` AS `n` ON `c`.`id` = `n`.`customer_id`
WHERE
`c`.`id` IN (SELECT
        DISTINCT `c`.`id`
    FROM
        `customers` AS `c` '
            . $aJoin
            . $nJoin 
            . $tJoin
            . $where
            . ')
AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
AND `t`.`sequence` = ' . TEL_C_DELIVERY1 . '
ORDER BY
`c`.`id` LIMIT ' . $offset . ', ' . $max . '
';
        $customers = db_query($query);
        if (!$customers)
            return '';
        while ($customer = $customers->fetch_array(MYSQLI_ASSOC))
            $searchList[] = $customer;
        $customers->close();
    }
}

// The template needs this to know which popup to display
// for adding service changes (stop/start for normal,
// start/stop for flag stop).
$flagStopId = get_config('flag-stop-type');
if ($flagStopId == CFG_NONE)
{
    echo '<div>'
            . 'Flag Stop Delivery Type has not been set.<br/>'
            . '(Can be set <a href="' . ConfigurationAddUrl('', 'flag-stop-type') . '">here</a>.)'
            . '</div>';
    return;
}
$smarty->assign('flagStopId', $flagStopId);

// Should be able to set default values for these per profile
// I am changing the default of billing (active) to yes for
// Ron, as it is confusing why customers that aren't being
// delivered are showing up in searches.  I agree!
// 20200522T0213PDT RUSSG
foreach(array
    (
        'offset' => 0,
        'limit' => 20,
        'route' => 0,
        'type' => 0,
        'routeList' => 2,
        'active' => 1
    ) as $f => $d)
{
    if (!isset($_POST[$f]))
        $smarty->assign($f, $d);
    else
        $smarty->assign($f, (integer)$_POST[$f]);
}

foreach(array
    (
        'cid',
        'telephone',
        'name',
        'address',
        'postal'
    ) as $f)
{
    if (!isset($_POST[$f]))
        $smarty->assign($f, '');
    else
        $smarty->assign($f, $_POST[$f]);
}
if (!isset($searchList))
    $searchList = array();
$smarty->assign('customers', $searchList);
$smarty->assign('count', $searchListTotal);
$smarty->display('customers/search.tpl');

?>
