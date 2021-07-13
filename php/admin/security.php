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

define('PAGE', SA_SECURITY);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/securitydata.inc.php';
require_once 'inc/popups/security.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;
global $message;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Security');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Security';

// NOTE:  REQUEST is used because Delete does a GET while all others
//        use POST (reset/clear/etc.).

if (isset($_REQUEST['action']))
{    
    $action = $_REQUEST['action'];
    if ($action == 'reset')
    {
        $_REQUEST['gid'] = 'All';
        $_REQUEST['uid'] = 'All';
        $_REQUEST['page'] = 'All';
        $_REQUEST['feature'] = 'All';
    }
    elseif ($action == 'delete')
    {
        $query = 'DELETE FROM `security` WHERE `page` = ' . $_GET['p3']
                . ' AND `feature` = \'' . $_GET['p4'] . '\'';
        if (strlen($_GET['p1']) == 0)
            $query .= ' AND ISNULL(`group_id`)';
        else
            $query .= ' AND `group_id` = ' . intval($_GET['p1']);
        if (strlen($_GET['p2']) == 0)
            $query .= ' AND ISNULL(`user_id`)';
        else
            $query .= ' AND `user_id` = ' . intval($_GET['p2']);
        $result = db_query($query);
        if ($err < ERR_SUCCESS)
            $message = 'Security entry deletion failed!';
        else
            $message = 'Security entry deleted successfully.';
        audit(sprintf("Deleted security setting: Group: %s, User: %s, Page: %s,"
                      . " Feature: %s", $_GET['p1'], $_GET['p2'], $_GET['p3'], $_GET['p4']));
    }
}

$data = SecurityData::create();
$sdesc = $data->descriptions();
$spages = $data->pages();
		
$full = isset($_REQUEST['gid']);
		
// List of groups
$query = "SELECT * FROM `groups` ORDER BY `name` ASC";
$gid = db_query($query);
if (!$gid)      // TODO: BUGBUG:
    return;
$temp = array();
while ($group = $gid->fetch_object())
    $temp[] = $group;
$smarty->assign('groups', $temp);
if (!isset($_REQUEST['gid']))
    $_REQUEST['gid'] = 'All';
$smarty->assign('gid', $_REQUEST['gid']);

// List of users
$query = "SELECT DISTINCT(`id`) FROM `users` ";
if (isset($_REQUEST['gid']) && !empty($_REQUEST['gid']) && $_REQUEST['gid'] != 'All')
    $query .= "WHERE `group_id` = " . intval($_REQUEST['gid']) . " ";
$query .= "ORDER BY `id` ASC";
$uid = db_query($query);
if (!$uid)      // TODO: BUGBUG:
    return;
$temp = array();
while ($val = $uid->fetch_object())
{
    $user = lup_user($val->id);
    if ($user)
        $temp[] = array('id' => $user->id, 'name' => $user->name);
    else
        $temp[] = array('id' => $val->id, 'name' => sprintf('%04d', $val->id));
}
$uid->close();
$smarty->assign('users', $temp);
if (!isset($_REQUEST['uid']))
    $_REQUEST['uid'] = 'All';
$smarty->assign('uid', $_REQUEST['uid']);
		
// List of human readable page names, alphabetized
$pages = array_flip($spages);
ksort($pages);
$temp = array();
foreach($pages as $page => $val)
    $temp[] = $page;
$smarty->assign('pages', $temp);
if (!isset($_REQUEST['page']))
    $_REQUEST['page'] = 'All';
$smarty->assign('page', $_REQUEST['page']);

// List of unique feature's
$query = "SELECT DISTINCT(`feature`) FROM `security` ";
$where = 'WHERE';
if (isset($_REQUEST['gid']) && !empty($_REQUEST['gid']) && $_REQUEST['gid'] != 'All')
{
    $query .= $where . ' `group_id` = ' . intval($_REQUEST['gid']);
    $where = ' AND';
}
if (isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) && $_REQUEST['uid'] != 'All')
{
    $query .= $where . ' `user_id` = ' . intval($_REQUEST['uid']);
    $where = ' AND';
}
if (isset($_REQUEST['page']) && !empty($_REQUEST['page']) && $_REQUEST['page'] != 'All')
{
    $query .= $where . ' `page` = ' . intval($_REQUEST['page']);
    $where = ' AND';
}
$query .= ' ORDER BY `feature` ASC';
$feature = db_query($query);
if (!$feature)      // TODO: BUGBUG:
    return;
$temp = array();
while ($val = $feature->fetch_object())
    $temp[] = $val;
$feature->close();
$smarty->assign('features', $temp);
if (!isset($_REQUEST['feature']))
    $_REQUEST['feature'] = 'All';
$smarty->assign('feature', $_REQUEST['feature']);

// Build query for config items if needed
if ($full)
{
    $query = "SELECT * FROM `security`";
    $where = ' WHERE ';
    if (isset($_REQUEST['gid']) && !empty($_REQUEST['gid']) && $_REQUEST['gid'] != 'All')
    {
        $query .= $where . '`group_id` = ' . intval($_REQUEST['gid']);
        $where = ' AND ';
    }
    if (isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) && $_REQUEST['uid'] != 'All')
    {
        $query .= $where . '`user_id` = ' . intval($_REQUEST['uid']);
        $where = 'AND ';
    }
    if (isset($_REQUEST['page']) && !empty($_REQUEST['page']) && $_REQUEST['page'] != 'All')
    {
        $query .= $where . '`page` = ' . intval($_REQUEST['page']);
        $where = 'AND ';
    }
    if (isset($_REQUEST['feature']) && !empty($_REQUEST['feature']) && $_REQUEST['feature'] != 'All')
    {
        $query .= $where . '`feature` = \'' . db_escape($_REQUEST['feature']) . '\' ';
        $where = 'AND ';
    }
    $query .= "ORDER BY `group_id`, `user_id`, `page`, `feature`";
    $set = db_query($query);
    if (!$set)      // TODO: BUGBUG:
        return;
    $count = $set->num_rows;
}
else
    $count = 0;
$smarty->assign('count', $count);

// TODO:  Despite name, this is really about making sure nothing is listed
//        before user refreshes the page, so that there isn't a long list
//        that takes forever to display before the user gets tp restrict
//        what's displayed (to the subset they want).

if ($full)
{
    $temp = array();
    while ($row = $set->fetch_object())
        $temp[] = $row;
    $smarty->assign('rows', $temp);
}
else
    $smarty->assign('rows', array());
	
$smarty->display("admin/security.tpl");
?>
