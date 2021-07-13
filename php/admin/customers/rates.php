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

define('ROOT', '../../');

require_once 'inc/security.inc.php';

define('PAGE', SAC_RATES);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/periods.inc.php';
require_once 'inc/popups/customers/rates.inc.php';
	
global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Customers / Rates');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Customers / Rates';

$message = '';
$errors = array();
    
populate_types();
if ($err < ERR_SUCCESS)
{
    echo gen_error();
    return;
}

// Customer billing type
$cbt = get_config('customer-billing-type');
if ($cbt == CFG_NONE)
    $cbt = 'auto';
$smarty->assign('cbt', $cbt);

// Flag stop type
$fs = get_config('flag-stop-type');
$smarty->assign('fs', $fs);

// End periods
$filters = db_query('SELECT DISTINCT(`period_id_end`) FROM `customers_rates` ORDER BY `period_id_end` ASC');
if (!$filters)
{
    echo gen_error();
    return;
}
$temp = array();
while ($filter = $filters->fetch_object())
{
    if (is_null($filter->period_id_end))
        $temp[0] = 'Current';
    else
        $temp[$filter->period_id_end] = iid2title($filter->period_id_end);
}
$filters->close();
$smarty->assign('filters', $temp);

// Rates
if (!isset($_POST['iid'])
        || !preg_match('/^[[:digit:]]+$/', $_POST['iid']))
{
    $_POST['iid'] = '0';
}
if (!preg_match('/^[[:digit:]]+$/', $_POST['iid']))
    $_POST['iid'] = '0';
$smarty->assign('iid', $_POST['iid']);
$rates = db_query('SELECT * FROM `customers_rates` WHERE `period_id_end` '
        . ($_POST['iid'] == '0' ? '<=> NULL' : '= ' . $_POST['iid'])
        . ' ORDER BY `period_id_begin` DESC, ' . db_customer_type_field());
if (!$rates)
{
    echo gen_error();
    return;
}
$temp = array();
while ($rate = $rates->fetch_assoc())
    $temp[] = $rate;
$rates->close();
$smarty->assign('rates', $temp);
$smarty->assign('count', count($temp));

$smarty->display('admin/customers/rates.tpl');

?>
