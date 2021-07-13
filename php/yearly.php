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

	require_once 'inc/calendar.inc.php';

    $curMonth = intval($_GET['m']);
	$m = intval($_GET['m']) - 6;
	$y = intval($_GET['y']);

	$title = date('F', mktime(0, 0, 0, $m + 1, 1, $y)) . ', '
			. date('Y', mktime(0, 0, 0, $m + 1, 1, $y))
			. ' - '
			. date('F', mktime(0, 0, 0, $m + 12, 1, $y)) . ', '
			. date('Y', mktime(0, 0, 0, $m + 12, 1, $y));
?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
		<title><?php echo $title ?></title>
	</head>
	<body>
		<table>
			<tr>
<?php
	for ($month = 1; $month <= 12; $month++)
	{
		if ($month % 3 == 0)
?>
                <td>
<?php
		echo generate_calendar($y, $m + $month, array(), 2);
?>
				</td>
<?php
		if ($month % 3 == 0 and $month < 12)
		{
?>
			</tr>
			<tr>
<?php
		}
	}
?>
			</tr>
		</table>
<?php
    gen_htmlFooter();
?>
