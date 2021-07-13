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

define('PAGE', SA_AUDIT);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/profile.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Audit Log');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('addResults', isset($_POST['action']));

$errContext = 'Admin / Audit Log';

$query = 'SELECT `id`, `name` FROM `users` ORDER BY `name` ASC';
$users = db_query($query);
if (!$users)
    return;
$USERS = array();
while ($user = $users->fetch_object())
    $USERS[$user->id] = array('id' => $user->id, 'name' => $user->name);

$user = 0;
if (isset($_POST['user']) && preg_match('/^[[:digit:]]+$/', $_POST['user']))
    $user = intval($_POST['user']);
$smarty->assign('user', $user);

$log = array();
if (isset($_POST['action']))
{
    $ACTION = $_POST['action'];
    $performQuery = false;
    if ($ACTION == 'prev')
    {
        $offset = intval($_POST['offset']);
        $limit = intval($_POST['limit']);
        if ($offset > 0)
            $offset -= $limit;
        if ($offset < 0)
            $offset = 0;
        $_POST['offset'] = $offset;
        $performQuery = true;
    }
    else if ($ACTION == 'next')
    {
        $offset = intval($_POST['offset']);
        $limit = intval($_POST['limit']);
        $_POST['offset'] = $offset + $limit;
        $performQuery = true;
    }
    else if ($ACTION == 'begin')
    {
        $_POST['offset'] = '0';
        $performQuery = true;
    }
    else if ($ACTION == 'end')
    {
        $count = intval($_POST['count']);
        $limit = intval($_POST['limit']);
        $_POST['offset'] =  $count - $limit;
        $performQuery = true;
    }
    else if ($ACTION == 'clear')
    {
        unset($_POST['keywords']);
        unset($_POST['user']);
        unset($_POST['count']);
        unset($_POST['limit']);
        unset($_POST['offset']);
        $user = 0;
    }
    else if ($ACTION == 'refresh')
        $performQuery = true;

    if ($performQuery)
    {
        // Build up query
        $query = ' FROM `audit_log`';
        $AND = ' WHERE';

        // Add user id if requested
        if ($user > 0)
        {
            $query .= $AND . ' `user_id` = ' . $user;
            $AND = ' AND';
        }

        // Add keyword(s) if requested
        if (!empty($_POST['keywords']))
        {
            // If there is more than 1 word, check all words individually
            $keywords = explode(' ', stripslashes($_POST['keywords']));
            if (count($keywords) == 1)
            {
                $query .= $AND . " UPPER(`what`) LIKE UPPER('%"
                        . db_escape(stripslashes($_POST['keywords'])) . "%')";
                $AND = ' AND';
            }
            else
            {
                $query .= $AND . ' (';
                $AND2 = '';
                foreach($keywords as $keyword)
                {
                    $query .= $AND2 . "UPPER(`what`) LIKE UPPER('%"
                            . db_escape(stripslashes($keyword)) . "%')";
                    $AND2 = ' AND ';
                }
                $query .= ')';
                $AND = ' AND';
            }
        }

        $count = db_query_result('SELECT COUNT(*) ' . $query);
        if (isset($_POST['count']))
            unset($_POST['count']);
        $query .= ' ORDER BY `when` DESC LIMIT ' . intval($_POST['offset']) . ', ' . intval($_POST['limit']);
        $records = db_query('SELECT * ' . $query);
        if (!$records)      // TODO: BUGBUG:
            return;
        while ($record = $records->fetch_object())
        {
            $log[] = array(
                'user_id' => $record->user_id,
                'when' => strtotime($record->when),
                'what' => str_replace('. ', '.<br>', $record->what));
        }
    }
}
$smarty->assign('result', $log);

if (!isset($count))
{
    if (isset($_POST['count']) && preg_match('/^[[:digit:]]+$/', $_POST['count']))
        $count = intval($_POST['count']);
    else
        $count = 0;
}
$smarty->assign('count', $count);

// Build select <option>'s for users
reset($USERS);
$smarty->assign('users', $USERS);

if (isset($_POST['keywords']))
    $temp = $_POST['keywords'];
else
    $temp = '';
$smarty->assign('keywords', $temp);

$smarty->assign('action', $_SERVER['PHP_SELF']);

$smarty->display('admin/auditlog.tpl');
?>
