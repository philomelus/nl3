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

	set_include_path('..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';
	
	Header("content-type: text/plain");
	
	require_once 'tools/bill.php';

	$period_id = get_config('billing-period', 0);
echo $period_id . "\n";
	$BILL = new Biller(array());
	$BILL->Generate(1270, $period_id); exit;
//	$BILL->Combine($period_id); exit;

	// Period 33 = December 2007
//	for ($i = 61; $i < 529; ++$i)
//	for ($i = 277; $i < 313; ++$i)
//	{
//		$BILL->Generate($i, $period_id);
//		echo "\n";
//	}

	unset($BILL);

?>
