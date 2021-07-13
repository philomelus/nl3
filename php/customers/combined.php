<?php
/*
	Copyright 2005-2021 Russell E. Gibson

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

define('PAGE', SC_COMBINED);

require_once 'inc/login.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/sql.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/customers.inc.php';
require_once 'inc/popups/customers/combineds.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Customers / Combined');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);
$smarty->assign('doResults', isset($_POST['action']));

$errContext = 'Customers / Combined';

$message = '';
	
function submit()
{
    global $err, $errCode, $errContext, $errQuery, $errText;
    global $message;
    
    if (!isset($_POST['action']))
        return;
    $ACTION = htmlentities($_POST['action']);
    
    if ($ACTION == 'x')
    {
        if (!isset($_REQUEST['id']))
            return;
        $id = intval($_REQUEST['id']);
        if ($id == 0)
            return;
        
        if (isset($_REQUEST['id2']) && intval($_REQUEST['id2']) > 0)
        {
            $id2 = intval($_REQUEST['id2']);
            if ($id2 == 0)
                return;
            
            $query = 'DELETE FROM `customers_combined_bills` WHERE `customer_id_main` = '
                    . $id . ' AND `customer_id_secondary` = ' . $id2 . ' LIMIT 1';
            $result = db_query($query);
            if ($result)
            {
                audit('Deleted combined bill. Main customer was ' . sprintf('%d', $id)
                        . '. Secondary customer was ' . sprintf('%d', $id2) . '.');
                $message = '<span>Deleted Combined Customer ' . sprintf('%d', $id2)
                        . ' with ' . sprintf('%d', $id) . '</span>';
            }
            else
                $message = '<span>Failed to delete combined customers!</span>';
        }
        else
        {
            $query = 'DELETE FROM `customers_combined_bills` WHERE `customer_id_main` = ' . $id;
            $result = db_query($query);
            if ($result)
            {
                audit('Deleted all combined bills with customer ' . sprintf('%d', $id) . '.');
                $message = 'Deleted All Customers Combined With ' . sprintf('%d', $id);
            }
            else
                $message = '<span>Failed to delete combined customers!</span>';
        }
    }
/*
    else if (preg_match('/&laquo;/', $ACTION))
    {
        $offset = intval($_POST['dbf_offset']);
        $limit = intval($_POST['dbf_limit']);
        if ($offset > 0)
            $offset -= $limit;
        if ($offset < 0)
            $offset = 0;
        $_POST['dbf_offset'] = $offset;
    }
    else if (preg_match('/&raquo;/', $ACTION))
    {
        $offset = intval($_POST['dbf_offset']);
        $limit = intval($_POST['dbf_limit']);
        $_POST['dbf_offset'] = $offset + $limit;
    }
*/
}


if (!isset($_POST['dbf_offset']))
    $_POST['dbf_offset'] = 0;
$offset = intval($_POST['dbf_offset']);

if (!isset($_POST['dbf_limit']))
    $_POST['dbf_limit'] = 10;
$limit = intval($_POST['dbf_limit']);

$mains = db_query('
SELECT
	`cb`.`customer_id_main` AS `custid1`,
	`a`.`address1` AS `address1`,
	`n`.`first` AS `first1`,
	`n`.`last` AS `last1`
FROM
	`customers_combined_bills` AS `cb`
	INNER JOIN `customers_addresses` AS `a` ON `cb`.`customer_id_main` = `a`.`customer_id`
	INNER JOIN `customers_names` AS `n` ON `cb`.`customer_id_main` = `n`.`customer_id`
WHERE
	`n`.`sequence` = ' . NAME_C_DELIVERY1 . '
	AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
GROUP BY
	`cb`.`customer_id_main` ASC
ORDER BY
	`cb`.`customer_id_main` ASC
LIMIT
	' . $offset . ',' . $limit);
if (!$mains)
{
    echo gen_error();
    return;
}
$temp = array();
$totalRows = 0;
while ($main = $mains->fetch_object())
{
    $twos = db_query('
SELECT
	`cb`.`customer_id_secondary` AS `custid2`,
	`a`.`address1` AS `address2`,
	`n`.`first` AS `first2`,
	`n`.`last` AS `last2`
FROM
	`customers_combined_bills` AS `cb`,
	`customers_addresses` AS `a`,
	`customers_names` AS `n`
WHERE
	`cb`.`customer_id_main` = ' . $main->custid1 . '
	AND `cb`.`customer_id_secondary` = `a`.`customer_id`
	AND `cb`.`customer_id_secondary` = `n`.`customer_id`
	AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
	AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
ORDER BY
	`customer_id_secondary` ASC');
    if (!$twos)
    {
        echo gen_error();
        return;
    }
    $temp2 = array();
    while ($two = $twos->fetch_object())
    {
        $temp2[] = array
            (
                'id' => $two->custid2,
                'name' => valid_name($two->first2, $two->last2),
                'address' => $two->address2
            );
    }
    $twos->close();
    $temp[] = array
        (
            'id' => $main->custid1,
            'name' => valid_name($main->first1, $main->last1),
            'address' => $main->address1,
            'others' => $temp2,
            'count' => count($temp2)
        );
    $totalRows += count($temp2);
}
$mains->close();
$smarty->assign('combined', $temp);
$smarty->assign('count', count($temp));
$smarty->assign('total', $totalRows);

$smarty->assign('offset', $_POST['dbf_offset']);
$smarty->assign('limit', $_POST['dbf_limit']);

$smarty->display('customers/combined.tpl');
	
?>
