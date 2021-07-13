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

define('BASE', 'demo/');
define('HOME', '/home/' . BASE);
define('DB_SERVER', '127.0.0.1');
define('DB_USER', 'demo');
define('DB_PASSWORD', 'omedOMED');
define('DB_DATABASE', 'demo');

date_default_timezone_set('US/Pacific');
error_reporting(E_ALL);


	Header("content-type: text/plain");

	set_include_path('..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/common.inc.php';

	$query =
'
SELECT
	DISTINCT(`cid`)
FROM
	`customers_bills`
ORDER BY
	`cid` ASC
';
	$records = $DB->query($query);
	if ($DB->errno)
		die($DB->error . ' (' . $DB->errno . ')' . "\n" . $query . "\n");
	while ($CUSTOMER = $records->fetch_object())
	{
		$customer = lup_customer(intval($CUSTOMER->cid));
		if ($err < ERR_SUCCESS)
			die($errText);

		$query =
'
SELECT
	DISTINCT(`iid`)
FROM
	`customers_bills`
WHERE
	`cid` = \'' . $CUSTOMER->cid . '\'
ORDER BY
	`iid` ASC
';
		$periods = $DB->query($query);
		if ($DB->errno)
			die($DB->error . ' (' . $DB->errno . ')' . "\n" . $query . "\n");

		while ($PERIOD = $periods->fetch_object())
			update_bill($customer, $PERIOD->iid);
		$periods->close();
	}
	$records->close();
	exit(0);

	function update_bill($customer, $iid)
	{
		global $DB, $err, $errCode, $errText;

		// Generate name
		$dNm = valid_name($customer->firstName, $customer->lastName);

		// Generate bill name
		$bNm = $dNm;
		$temp = lup_c_name($customer->id, NAME_C_BILLING1);
		if ($err < ERR_SUCCESS)
		{
			if ($err != ERR_NOTFOUND)
				die($errText . ' (' . $errCode . ")\n");
			else
				$err = ERR_SUCCESS;
		}
		if ($temp && !empty($temp->first))
			$bNm = valid_name($temp->first, $temp->last);

		// Determine which address to use for billing
		$length = strlen($bNm);
		$temp = lup_c_address($customer->id, ADDR_C_BILLING);
		if ($err < ERR_SUCCESS)
		{
			if ($err != ERR_NOTFOUND)
				die($errText . ' (' . $errCode . ")\n");
			else
				$err = ERR_SUCCESS;
		}
		if ($temp && !empty($temp->address1))
		{
			$bAd1 = $temp->address1;
			$city = $temp->city;
			$state = $temp->state;
			$zip = $temp->zip;
		}
		else
		{
			$bAd1 = $customer->address;
			$city = $customer->city;
			$state = $customer->state;
			$zip = $customer->zip;
		}

		// Validate first and possibly second lines
		// BUGBUG:  This length is HARD CODED HERE, but if a client is doing
		// their own billing, it doesn't need to be.
		$bAd4Required = false;
		if (strlen($bAd1) > 22)
		{
			$bAd4Required = true;

			// Locate whitespace before 23'rd char, and use from there to end as new line
			$i = 21;
			while ($bAd1{$i} != ' ' && $i > 0)
				--$i;

			// Make sure the first line at least has something
			if ($i <= 1)
				$i = 21;

			// Split the line into 2
			$bAd2 = substr($bAd1, $i + 1);
			$bAd1 = substr($bAd1, 0, $i + ($bAd1{$i} == ' ' ? 0 : 1));

			// Update zip padding length if needed
			if (strlen($bAd2) > $length)
				$length = strlen($bAd2);
		}
		if (strlen($bAd1) > $length)
			$length = strlen($bAd1);

		// Generate the final 2 lines
		if ($bAd4Required)
		{
			$bAd3 = substr($city, 0, 19) . ' ' . $state;
			if (strlen($bAd3) > $length)
				$length = strlen($bAd3);
			if ($length > 22)		// BUGBUG: HARD CODED field width
				$length = 22;
			$bAd4 = str_repeat(' ', $length - 5) . substr($zip, 0, 5);
		}
		else
		{
			$bAd2 = substr($city, 0, 19) . ' ' . $state;
			if (strlen($bAd2) > $length)
				$length = strlen($bAd2);
			if ($length > 22)		// BUGBUG: HARD CODED field width
				$length = 22;
			$bAd3 = str_repeat(' ', $length - 5) . substr($zip, 0, 5);
			$bAd4 = '';
		}

		// Get the client info
		$cnm = get_config('client-name', '');
		$cad1 = get_config('client-address-1', '');
		$cad2 = get_config('client-address-2', '');
		$ctel = 'Phone: ' . get_config('client-telephone', '');

		$query =
'
UPDATE
	`customers_bills`
SET
	`cnm` = \'' . db_escape($cnm) . '\',
	`cad1` = \'' . db_escape($cad1) . '\',
	`cad2` = \'' . db_escape($cad2) . '\',
	`ctel` = \'' . db_escape($ctel) . '\',
	`dNm` = \'' . db_escape($dNm) . '\',
	`dAd` = \'' . db_escape($customer->address) . '\',
	`dCt` = \'' . db_escape($customer->city) . '\',
	`dSt` = \'' . db_escape(strtoupper($customer->state)) . '\',
	`dZp` = \'' . db_escape($customer->zip) . '\',
	`bNm` = \'' . db_escape(strtoupper($bNm)) . '\',
	`bAd1` = \'' . db_escape(strtoupper($bAd1)) . '\',
	`bAd2` = \'' . db_escape(strtoupper($bAd2)) . '\',
	`bAd3` = \'' . db_escape(strtoupper($bAd3)) . '\',
	`bAd4` = \'' . db_escape(strtoupper($bAd4)) . '\'
WHERE
	`cid` = \'' . sprintf('%06d', $customer->id) . '\'
	AND `iid` = ' . $iid . '
LIMIT
	1
';
		$DB->query($query);
		if ($DB->errno)
			die($DB->error . ' (' . $DB->errno . ')' . "\n" . $query . "\n" . $query . "\n");
//echo $query . "\n";
		echo 'Fixed ' . sprintf('%08d', $customer->id) . ' - ' . sprintf('%04d', $iid) . "\n";
	}

?>
