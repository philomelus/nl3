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

define('ROOT', '');

require_once 'inc/security.inc.php';

define('PAGE', S_HOME);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/calendar.inc.php';
require_once 'inc/menu.inc.php';

global $DB, $smarty, $Period, $DeliveryTypes, $Routes;
global $err, $errCode, $errContext, $errQuery, $errText;
global $username;

populate_routes();
if ($err < ERR_SUCCESS)
{
    echo gen_error(false, false);
    return;
}

populate_types();
if ($err < ERR_SUCCESS)
{
    echo gen_error(false, false);
    return;
}

$title = get_config('default-title', 'NewsLedger');
if ($err < ERR_SUCCESS)
{
    echo gen_error(false, false);
    return;
}

$smarty->assign('path', 'Home');
$smarty->assign('title', $title);
$smarty->assign('username', $username);
$smarty->assign('ROOT', ROOT);

// Build a single query so everything can be retreived at once
$query = 'SELECT 
(SELECT COUNT(*) FROM `customers` WHERE `active` = \'Y\' OR `routeList` = \'Y\') AS `totalCount`,
(SELECT COUNT(*) FROM `customers` WHERE `active` = \'Y\' AND `balance` > 0) AS `activeAndOwe`,
(SELECT COUNT(*) FROM `customers` WHERE (`active` = \'Y\' OR `routeList` = \'Y\') AND `balance` <= 0) AS `activeAndAhead`,
(SELECT COUNT(*) FROM `customers_bills` WHERE `export` = \'Y\' AND `iid` = ' . $Period[P_PERIOD] . ') AS `lastBills`';
$result = db_query($query);
if (!$result)
{
    $err = ERR_DB_QUERY;
    $errCode = $DB->errno;
    $errContext = 'Home / Home';
    $errQuery = $query;
    $errText = $DB->error;
    echo gen_error(false, false);
    return;
}
$data = $result->fetch_object();
$result->close();
$smarty->assign('totalCount', $data->totalCount);
$smarty->assign('activeAndOwe', $data->activeAndOwe);
$smarty->assign('activeAndAhead', $data->activeAndAhead);
$smarty->assign('lastBills', $data->lastBills);

// Generate calendar
$time = time();
if (isset($_GET['m']) && !empty($_GET['m']) && isset($_GET['y']) && !empty($_GET['y']))
{
    $time = mktime(0, 0, 0, $_GET['m'], 1, $_GET['y']);
}
$year = date('Y', $time);
$month = date('n', $time);
$pYear = date('Y', strtotime('-1 month', $time));
$pMonth = date('n', strtotime('-1 month', $time));
$nYear = date('Y', strtotime('+1 month', $time));
$nMonth = date('n', strtotime('+1 month', $time));
$days = array();
if (date('n') == $month && date('Y') == $year)
{
    $today = date('j');
    $days = array
    (
        $today => array
        (
            NULL,
            NULL,
            '<span>' . $today . '</span>'
        )
    );
}
$links = array
(
    '&laquo;' => 'index.php?m=' . $pMonth . '&y=' . $pYear,
    '&raquo;' => 'index.php?m=' . $nMonth . '&y=' . $nYear
);
$yearurl = 'JavaScript:PopupWindow("yearly.php?y=' . $year . '&m=' . $month . '")';
$calendar = generate_calendar($year, $month, $days, 2, $yearurl, 0, $links);
$smarty->assign('calendar', $calendar);

// Build data for customer types - headers
$routes = array();
foreach($Routes as $key => $val)
    $routes[] = $val;
$smarty->assign('routes', $routes);

// Build data for customer types - rows
// BUGBUG:  This fails if there are no routes defined
reset($DeliveryTypes);
$cpdt = array();
foreach($DeliveryTypes as $tid => $tdata)
{
    if (!$tdata['visible'])
        continue;
    $row = array();
    $row[] = sprintf('%04d', $tid);
    $row[] = $tdata['abbr'];
    
    $query = 'SELECT COUNT(*) FROM `customers` WHERE `active` = \'Y\' AND `type_id` = '
            . $tid . ' AND `route_id` = ';
    reset($Routes);
    foreach($Routes as $rid => $rname)
    {
        $result = db_query($query . $rid);
        if ($result)
            $temp = $result->fetch_array();
        else
            $temp = array(0 => 0);
        $row[] = $temp[0];
    }
    $result = db_query("SELECT COUNT(*) FROM `customers` WHERE `active` = 'Y' AND `type_id` = " . $tid);
    if ($result)
    {
        $temp = $result->fetch_array();
        $result->close();
    }
    else
        $temp = array(0 => 0);
    $row[] = $temp[0];
    $cpdt[] = $row;
}
reset($Routes);
$row = array();
$row[] = '0000';
$row[] = '#';
$query = '';
$comma = '';
foreach($Routes as $id => $name)
{
    $query .= $comma . '(SELECT COUNT(*) FROM `customers` WHERE `active` = \'Y\' AND `route_id` = '
            . $id . ') AS dt' . sprintf('%04d', $id);
    $comma = ',';
}
$result = db_query('SELECT ' . $query);
if (!$result)
{
    $errContext = 'Home';
    echo gen_error(false, false);
    return;
}
$data = $result->fetch_array();
$result->close();
reset($Routes);
$total = 0;
foreach($Routes as $id => $name)
{
    $i = $data['dt' . sprintf('%04d', $id)];
    $row[] = $i;
    $total += $i;
}
$row[] = $total;
$cpdt[] = $row;
$smarty->assign('cpdt', $cpdt);
$smarty->assign('period_id', $Period[P_PERIOD]);

$smarty->display('home.tpl');
?>
