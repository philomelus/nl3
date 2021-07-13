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

define('PAGE', SAC_BILLING);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/sql.inc.php';
require_once 'inc/profile.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Customers / Billing');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Customers / Billing';

$message = '';
$errors = array();
    
if (isset($_POST['action']))
{
    if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['fs-daily']))
        $errors[] = 'Flag Stop - Daily is invalid';
    if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['fs-sunday']))
        $errors[] = 'Flag Stop - Sunday is invalid';
    if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['billing-minimum']))
        $errors[] = 'Print Bills Owing At Least is invalid';
    if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['flag-stop-billing-minimum']))
        $errors[] = 'Print Flag Stop Bills Owing At Least is invalid';
    
    if (count($errors) == 0)
    {
        // Update as needed
        $updated = array();
        foreach(array(
                'fs-daily' => array
                    (
                        'Flag Stop - Daily',
                        'flag-stop-daily-rate',
                        '%01.2f'
                    ),
                'fs-sunday' => array
                    (
                        'Flag Stop - Sunday',
                        'flag-stop-sunday-rate',
                        '%01.2f'
                    ),
                'billing-minimum' => array
                    (
                        'Billing Minimum',
                        'billing-minimum',
                        '%01.2f'
                    ),
                'flag-stop-billing-minimum' => array
                    (
                        'Flag Stop Billing Minimum',
                        'flag-stop-billing-minimum',
                        '%01.2f'
                    )) as $i => $f)
        {
            $temp = sprintf($f[2], floatval($_POST[$i]));
            if ($temp != floatval(get_config($f[1])))
            {
                set_globalConfig($f[1], $temp);
                if ($err < ERR_SUCCESS)
                    break;
                $updated[$f[0]] = array($_POST[$i . '-org'], $temp);
            }
        }

        if ($err >= ERR_SUCCESS)
        {
            if (count($updated) > 0)
            {
                $message = 'Changes saved successfully.';
                $temp = '';
                foreach($updated as $field => $values)
                    $temp .= ' ' . $field . ' was ' . $values[0] . ' and now is ' . $values[1] . '.';
                audit('Updated billing settings.' . $temp);
            }
            else
                $message = 'No changes needed saving.';
        }
        else
            $message = 'Not all changes saved due to error!';
    }
    else
        $message = 'Changes not saved due to error' . (count($errors) > 1 ? '' : 's');
}

if (isset($_POST['fs-daily']))
    $fsDaily = $_POST['fs-daily'];
else
    $fsDaily = sprintf('%01.2f', floatval(get_config('flag-stop-daily-rate', 0)));
$smarty->assign('fsDaily', $fsDaily);

if (isset($_POST['fs-sunday']))
    $fsSunday = $_POST['fs-sunday'];
else
    $fsSunday = sprintf('%01.2f', floatval(get_config('flag-stop-sunday-rate', 0)));
$smarty->assign('fsSunday', $fsSunday);

if (isset($_POST['billing-minimum']))
    $billingMinimum = $_POST['billing-minimum'];
else
    $billingMinimum = sprintf('%01.2f', floatval(get_config('billing-minimum', 0)));
$smarty->assign('billingMinimum', $billingMinimum);

if (isset($_POST['billing-minimum']))
    $flagStopBillingMinimum = $_POST['flag-stop-billing-minimum'];
else
    $flagStopBillingMinimum = sprintf('%01.2f', floatval(get_config('flag-stop-billing-minimum', 0)));
$smarty->assign('flagStopBillingMinimum', $flagStopBillingMinimum);

$smarty->assign('errors', $errors);
$smarty->assign('message', $message);
$smarty->display('admin/customers/billing.tpl');
	
?>
