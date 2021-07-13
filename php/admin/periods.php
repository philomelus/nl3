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

define('PAGE', SA_PERIODS);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/periods.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Periods');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Periods';
	
$smarty->assign('P_PERIOD', P_PERIOD);
$smarty->assign('P_TITLE', P_TITLE);
$smarty->assign('P_START', P_START);
$smarty->assign('P_END', P_END);
$smarty->assign('P_BILL', P_BILL);
$smarty->assign('P_DSTART', P_DSTART);
$smarty->assign('P_DEND', P_DEND);
$smarty->assign('P_DUE', P_DUE);

$periods = gen_periodsArray();
if (isset($_POST['filter']))
    $filter = intval($_POST['filter']);
else
    $filter = date('Y');

// Years to include in filter
$years = array();
reset($periods);
foreach($periods as $iid => $period)
{
    $date = date('Y', $period[P_START]);
    if (!in_array($date, $years))
        $years[] = $date;
    $date = date('Y', $period[P_END]);
    if (!in_array($date, $years))
        $years[] = $date;
    $date = date('Y', $period[P_BILL]);
    if (!in_array($date, $years))
        $years[] = $date;
    $date = date('Y', $period[P_DSTART]);
    if (!in_array($date, $years))
        $years[] = $date;
    $date = date('Y', $period[P_DEND]);
    if (!in_array($date, $years))
        $years[] = $date;
    $date = date('Y', $period[P_DUE]);
    if (!in_array($date, $years))
        $years[] = $date;
}
$smarty->assign('years', $years);

if (isset($_POST['filter']))
    $date = intval($_POST['filter']);
else
    $date = date('Y');
$smarty->assign('date', $date);

$count = count($periods);
$smarty->assign('count', $count);

define('EMPTY_DATE', strtotime('0000-00-00'));
reset($periods);
$temp = array();
foreach($periods as $iid => $period)
{
    if ($filter > 0)
    {
        if (date('Y', $period[P_START]) != $filter
                && date('Y', $period[P_END]) != $filter
                && date('Y', $period[P_BILL]) != $filter
                && date('Y', $period[P_DSTART]) != $filter
                && date('Y', $period[P_DEND]) != $filter
                && date('Y', $period[P_DUE]) != $filter)
        {
            continue;
        }
    }
    $temp[$iid] = $period;
}
$smarty->assign('periods', $temp);

$smarty->display("admin/periods.tpl");

?>
