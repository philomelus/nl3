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

define('PAGE', SC_FLAGSTOPS);

require_once 'inc/login.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/sql.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/config.inc.php';
require_once 'inc/popups/customers.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Customers / Flag Stops');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Customers / Flag Stops';

$message = '';

// If flag stop type isn't set, bail
$flagStopId = get_config('flag-stop-type');
if ($flagStopId == CFG_NONE)
{
    echo '<div>'
            . 'Flag Stop Delivery Type has not been set.<br/>'
            . '(Can be set <a href="' . ConfigurationAddUrl('', 'flag-stop-type') . '">here</a>.)'
            . '</div>';
    return;
}

// Get the list of flag stop customers
$flagStops = db_query('
SELECT
	`c`.`id`,
	`c`.`route_id`,
	`a`.`address1` AS `address`,
	`t`.`number` AS `telephone`,
	`n`.`first` AS `firstName`,
	`n`.`last` AS `lastName`
FROM
	`customers` AS `c`,
	`customers_addresses` AS `a`,
	`customers_telephones` AS `t`,
	`customers_names` AS `n`
WHERE
	`c`.`id` = `a`.`customer_id`
	AND `c`.`id` = `t`.`customer_id`
	AND `c`.`id` = `n`.`customer_id`
	AND `c`.`active` = \'Y\'
	AND `c`.`type_id` = ' . $flagStopId . '
	AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
	AND `t`.`sequence` = ' . TEL_C_DELIVERY1 . '
	AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
ORDER BY
	`lastName` ASC,
	`firstName` ASC');
if (!$flagStops)
{
    echo gen_error(false, false);
    return;
}
$temp = array();
while ($flagStop = $flagStops->fetch_array(MYSQLI_ASSOC))
    $temp[] = $flagStop;
$flagStops->close();
$smarty->assign('customers', $temp);
$smarty->assign('count', count($temp));

// Display page
$smarty->display('customers/flagstops.tpl');
?>
