<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009 Russell E. Gibson

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

	set_include_path('..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', S_HOME);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	Header("content-type: application/x-javascript");
?>
(function()
{
	this.periods =
	{
<?php
	$result = db_query('SELECT * FROM `periods` ORDER BY `id`');
	if ($result)
	{
		$BILL = '';
		$DAYS = '';
		$DEND = '';
		$DSTART = '';
		$DUE = '';
		$END = '';
		$START = '';
		$TITLE = '';
		while ($record = $result->fetch_object())
		{
			$s = strtotime($record->changes_start);
			$e = strtotime($record->changes_end . ' 23:59:59');
			$TITLE .= ',"' . $record->title . '"';
			$START .= ',"' . strftime('%m/%d/%y', $s) . '"';
			$END .= ',"' . strftime('%m/%d/%y', $e) . '"';
			$DSTART .= ',"' . strftime('%m/%d/%y', strtotime($record->display_start)) . '"';
			$DEND .= ',"' . strftime('%m/%d/%y', strtotime($record->display_end . ' 23:59:59')) . '"';
			$DUE .= ',"' . strftime('%m/%d/%y', strtotime($record->due)) . '"';
			$BILL .= ',"' . strftime('%m/%d/%y', strtotime($record->bill)) . '"';
			$DAYS .= ',' . days_between_dates($s, $e);
		}
		$result->close();
	}
	echo 'BILL: [""' . $BILL . '],' . "\r\n";
	echo 'DAYS: [""' . $DAYS . '],' . "\r\n";
	echo 'DEND: [""' . $DEND . '],' . "\r\n";
	echo 'DSTART: [""' . $DSTART . '],' . "\r\n";
	echo 'DUE: [""' . $DUE . '],' . "\r\n";
	echo 'END: [""' . $END . '],' . "\r\n";
	echo 'START: [""' . $START . '],' . "\r\n";
	echo 'TITLE: [""' . $TITLE . ']' . "\r\n";
?>
	}
})();
