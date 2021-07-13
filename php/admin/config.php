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

define('PAGE', SA_CONFIG);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/profiledata.inc.php';
require_once 'inc/securitydata.inc.php';
require_once 'inc/popups/config.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;
global $message;
global $Routes;
global $DeliveryTypes;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Config');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);
$smarty->assign('addResults', isset($_POST['action']));

$errContext = 'Admin / Config';

// Handle form submission
if (isset($_POST['action']))
{
    if ($_POST['action'] == 'clear')
    {
        $_POST['key1'] = 'All';
        $_POST['key2'] = 'All';
        $_POST['key3'] = 'All';
    }
    else if ($_POST['action'] == 'delete')
    {
        // Create title
        $key = $_GET['key'];

        // Delete the specified entry
        $query = 'DELETE FROM `configuration` WHERE `key` = \'' . $key . '\' LIMIT 1';
        $result = db_query($query);
        if (!$result)
            $message = 'Failed to delete ' . $key . '.';
        else
            $message = $key . ' deleted successfully.';
    }
}

populate_routes();
populate_types();

$full = isset($_POST['key1']);

$data = ProfileData::create();

$security = SecurityData::create();
$pages = $security->pages();

// Build query for config items if needed
if ($full)
{
    $query = "SELECT * FROM `configuration`";
    $temp = '';
    if ($_POST['key1'] != 'All')
    {
        $temp .= $_POST['key1'];
        if (isset($_POST['key2']) && $_POST['key2'] != 'All')
        {
            $temp .= '-' . $_POST['key2'];
            if (isset($_POST['key3']) && $_POST['key3'] != 'All')
                $temp .= '-' . $_POST['key3'];
        }
    }
    if (!empty($temp))
        $query .= ' WHERE `key` LIKE \'' . db_escape($temp) . '%\'';
    $query .= ' ORDER BY `key`';
    $set = db_query($query);
    if (!$set)
        $count = 0;
    else
        $count = $set->num_rows;
}
else
    $count = 0;
$smarty->assign('count', $count);

// Make sure all three keys are available to script
if ($full)
{
    if ($_POST['key1'] == "All")
    {
        $_POST['key2'] = 'All';
        $_POST['key3'] = 'All';
    }
    else
    {
        if (!isset($_POST['key2']))
        {
            $_POST['key2'] = 'All';
            $_POST['key3'] = 'All';
        }
        else
        {
            if (!isset($_POST['key3']))
                $_POST['key3'] = 'All';
        }
    }
}

if (!$full)
{
    $_POST['key1'] = 'All';
    $_POST['key2'] = 'All';
    $_POST['key3'] = 'All';
}

$smarty->assign('key1', $_POST['key1']);
$smarty->assign('key2', $_POST['key2']);
$smarty->assign('key3', $_POST['key3']);

$temp = array();
foreach($data->keys1(true) as $val)
    $temp[] = $val;
$smarty->assign('keys1', $temp);

$temp = array();
foreach($data->keys2(true) as $val)
    $temp[] = $val;
function cmp($a, $b)
{
    return strcasecmp($a, $b);
}
uksort($temp, 'cmp');
$smarty->assign('keys2', $temp);

$temp = array();
foreach($data->keys3(true) as $val)
    $temp[] = $val;
$smarty->assign('keys3', $temp);

if ($full)
{
    if ($count > 0)
    {
        $temp = array();
        while ($row = $set->fetch_object())
        {
            $profile = $data->lookup($row->key);

            $rec = array();
            $rec['key'] = $row->key;

            if (isset($profile[ProfileData::IS_READONLY]))
                $rec['readonly'] = $profile[ProfileData::IS_READONLY];
            else
                $rec['readonly'] = false;

            if (isset($profile[ProfileData::IS_REQUIRED]))
                $rec['required'] = $profile[ProfileData::IS_REQUIRED];
            else
                $rec['required'] = false;

            if (isset($profile[ProfileData::DESC]))
                $rec['desc'] = $profile[ProfileData::DESC];
            else
                $rec['desc'] = '&nbsp;';

            if (isset($profile[ProfileData::TYPE]))
            {
                switch ($profile[ProfileData::TYPE])
                {
                case CFG_BOOLEAN:
                    switch ($row->value)
                    {
                    case 'true':
                        $rec['value'] = 'True';
                        break;

                    case 'false':
                        $rec['value'] = 'False';
                        break;

                    default:
                        $rec['value'] = 'INVALID BOOLEAN';
                        break;
                    }
                    break;

                case CFG_STRING:
                    if (empty($row->value))
                    {
                        if (isset($profile[ProfileData::IS_REQUIRED]) && $profile[ProfileData::IS_REQUIRED])
                            $rec['value'] = '!!! MISSING !!!';
                        else
                            $rec['value'] = '&nbsp;';
                    }
                    else
                        $rec['value'] = wordwrap(valid_text($row->value), 27, '<br />', true);
                    break;

                case CFG_PERIOD:
                    $rec['value'] = iid2title(intval($row->value));
                    break;

                case CFG_ROUTE:
                    $rec['value'] = $Routes[intval($row->value)];
                    break;

                case CFG_TYPE:
                    $rec['value'] = $DeliveryTypes[intval($row->value)]['abbr'];
                    break;

                case CFG_COLOR:
                    $rec['value'] = intval($row->val) . ' (#' . sprintf('%06X', intval($row->value)) . ')';
                    break;

                case CFG_INTEGER:
                    $rec['value'] = intval($row->value);
                    break;

                case CFG_FLOAT:
                    $rec['value'] = floatval($row->value);
                    break;
                    
                case CFG_MONEY:
                    $rec['value'] = sprintf('$%01.4f', $row->value);
                    break;
                
                case CFG_ENUM:
                    $rec['value'] = $profile[ProfileData::ENUM][$row->value];
                    break;
                    
                case CFG_TELEPHONE:
                    $rec['value'] = $row->value;
                    break;
                
                default:
                    $rec['value'] = 'UNKNOWN TYPE';
                    break;
                }
            }
            else
                $rec['value'] = 'UNKNOWN TYPE';

            $temp[] = $rec;
        }
    }
}
$smarty->assign('records', $temp);

$smarty->display('admin/config.tpl');
?>
