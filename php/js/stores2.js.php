<?php
/*
	Copyright 2020 Russell E. Gibson

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
//	set_include_path('../..' . PATH_SEPARATOR . get_include_path());

//	require_once 'inc/security.inc.php';

//    define('PAGE', S_STORES);

//	require_once 'inc/login.inc.php';
//	require_once 'inc/common.inc.php';

	Header("content-type: application/x-javascript");

    $js = 'var returnWeeks  = [' . "\n";
    $cur = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $year = date('Y', $cur);
    $week = date('W', $cur);
    for ($w = $week - 5; $w < $week + 5; ++$w)
    {
        $js .= '    [';
        for ($dayIdx = 0; $dayIdx < 7; ++$dayIdx)
        {
            $day = date_isodate_set(new DateTime(), $year, $w, $dayIdx);
            $real = new DateTime($day->format('Y/m/d') . ' 00:00:00');
            if ($dayIdx == 0)
                $js .= $real->getTimestamp() .',';
            $js .= '"' . $real->format('m/d') . '",';
        }
        $js .= '],' . "\n";
    }
    echo $js . '];' . "\n";
?>
