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

define('PAGE', SA_USERS);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/users.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Users');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Users';

$message = '';

if (isset($_REQUEST['action']))
{
    if ($_REQUEST['action'] == 'Delete')
    {
        if ($_REQUEST['p1'] = 'uid')
        {
            // Delete the specified user
            $query = 'DELETE FROM `users` WHERE `id` = ' . $_REQUEST['p2'] . ' LIMIT 1';
            $result = db_query($query);
            if (!$result)
                $message = 'Failed to delete user!';
            else
                $message = 'User deleted successfully!';
        }
    }
}
    
$users = db_query('SELECT * FROM `users`');
if (!$users)    // TODO: BUGBUG:
    return;
$temp = array();
while ($user = $users->fetch_object())
{
    $temp[] = array('id' => $user->id,
                    'group_id' => $user->group_id,
                    'name' => $user->name,
                    'login' => $user->login);
}
$smarty->assign('users', $temp);

$smarty->display('admin/users.tpl');
?>
