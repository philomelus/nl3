<?php
/*
    Copyright 2021 Russell E. Gibson

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

define('PAGE', SA_GROUPS);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/groups.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Groups');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Groups';

$message = '';

// Get the list of groups
$query = "SELECT * FROM `groups` ORDER BY `name`";
$groups = db_query($query);
if ($groups)
{
    // Add groups to display
    $count = $groups->num_rows;
    $smarty->assign('count', $count);

    $temp = array();
    while ($group = $groups->fetch_object())
        $temp[] = array('id' => $group->id, 'name' => $group->name);
    $smarty->assign('groups', $temp);
}
else
{
    $smarty->assign('count', 0);
    $smarty->assign('groups', array());
}

$smarty->display('admin/groups.tpl');
?>
