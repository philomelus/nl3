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

define('PAGE', SA_BILLING);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/profile.inc.php';

global $err, $errContext, $errText;
global $message;
global $errors, $smarty;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Billing');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Billing';

$message = '';
$errors = array();

if (isset($_POST['action']) && $_POST['action'] == 'Save Changes')
{
    $updated = array();
    
    // Update client name if needed
    if ($_POST['client-name-org'] != $_POST['client-name'])
    {
        set_globalConfig('client-name', $_POST['client-name']);
        if ($err >= ERR_SUCCESS)
        {
            $updated['Name'] = array($_POST['client-name-org'], $_POST['client-name']);
            $_POST['client-name-org'] = $_POST['client-name'];
        }
        else
            $errors[] = $errText;
    }
    
    // Update client address 1 if needed
    if ($_POST['client-address-1-org'] != $_POST['client-address-1'])
    {
        set_globalConfig('client-address-1', $_POST['client-address-1']);
        if ($err >= ERR_SUCCESS)
        {
            $updated['Address'] = array($_POST['client-address-1-org'], $_POST['client-address-1']);
            $_POST['client-address-1-org'] = $_POST['client-address-1'];
        }
        else
            $errors[] = $errText;
    }
    
    // Update client address 2 if needed
    if ($_POST['client-address-2-org'] != $_POST['client-address-2'])
    {
        set_globalConfig('client-address-2', $_POST['client-address-2']);
        if ($err >= ERR_SUCCESS)
        {
            $updated['City, State, Zip'] = array($_POST['client-address-2-org'], $_POST['client-address-2']);
            $_POST['client-address-2-org'] = $_POST['client-address-2'];
        }
        else
            $errors[] = $errText;
    }
    
    // Update the telephone if needed
    if ($_POST['client-telephone-org'] != $_POST['client-telephone'])
    {
        set_globalConfig('client-telephone', $_POST['client-telephone']);
        if ($err >= ERR_SUCCESS)
        {
            $updated['Telephone'] = array($_POST['client-telephone-org'], $_POST['client-telephone']);
            $_POST['client-telephone-org'] = $_POST['client-telephone'];
        }
        else
            $errors[] = $errText;
    }

    if (count($errors) > 0)
    {
        if (count($updated) == 0)
            $message = 'Error(s) prevented saving changes.';
        else
            $message = 'Error(s) prevented saving (some) settings!';
    }
    else if (count($updated) == 0)
        $message = 'No settings changed.';
    else
    {
        $message = 'Changes saved successfully.';
        $temp = '';
        foreach($updated as $field => $values)
            $temp .= ' ' . $field . ' was ' . $values[0] . ' and now is ' . $values[1] . '.';
        audit('Updated billing settings.' . $temp);
    }
}

if (!isset($_POST['client-name-org']))
    $_POST['client-name-org'] = get_config('client-name', '');
$smarty->assign('clientNameOrg', $_POST['client-name-org']);

if (!isset($_POST['client-address-1-org']))
    $_POST['client-address-1-org'] = get_config('client-address-1', '');
$smarty->assign('clientAddress1Org', $_POST['client-address-1-org']);

if (!isset($_POST['client-address-2-org']))
    $_POST['client-address-2-org'] = get_config('client-address-2', '');
$smarty->assign('clientAddress2Org', $_POST['client-address-2-org']);

if (!isset($_POST['client-telephone-org']))
    $_POST['client-telephone-org'] = get_config('client-telephone', '');
$smarty->assign('clientTelephoneOrg', $_POST['client-telephone-org']);

if (isset($_POST['client-name']))
    $temp = $_POST['client-name'];
else
    $temp = $_POST['client-name-org'];
$smarty->assign('clientName', $temp);

if (isset($_POST['client-address-1']))
    $temp = $_POST['client-address-1'];
else
    $temp = $_POST['client-address-1-org'];
$smarty->assign('clientAddress1', $temp);

if (isset($_POST['client-address-2']))
    $temp = $_POST['client-address-2'];
else
    $temp = $_POST['client-address-2-org'];
$smarty->assign('clientAddress2', $temp);

if (isset($_POST['client-telephone']))
    $temp = $_POST['client-telephone'];
else
    $temp = $_POST['client-telephone-org'];
$smarty->assign('clientTelephone', $temp);

$smarty->display("admin/billing.tpl");

?>
