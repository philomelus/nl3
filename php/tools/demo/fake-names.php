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

	set_include_path(get_include_path() . PATH_SEPARATOR . ROOT);

	define('DB_SERVER', '127.0.0.1');
	define('DB_USER', 'demo');
	define('DB_PASSWORD', 'omedOMED');
	define('DB_DATABASE', 'nl-demo');

	require_once 'inc/common.inc.php';

	set_time_limit(0);

	$handle = fopen("fakenames.csv", "r");
	$id = 1;
	if ($handle)
	{
		while (!feof($handle))
		{
			// Grab a line from the text file and parse it into an associative array.
			$buffer = fgets($handle, 4096);
			$line = explode(',', $buffer);

			// Trim up the information, while making global variables
			while(list($key, $value) = each($line))
			{
				$value = str_replace('"', '', $value);
				${$key} = trim($value);
			}

			// Get the customer record
			$customer = lup_customer($id);

			if (!empty($customer->firstName))
			{
				$query = "UPDATE `customers` SET `firstName` = '" . str_replace('"', '', $line[0]) . "'";
				if (!empty($customer->lastName))
					$query .= ", `lastName` = '" . str_replace('"', '', $line[2]) . "'";
				$query .= " WHERE `id` = " . $customer->id;
				if (!mysqli_query($DB, $query))
					echo 'Query failed: ' . $query . ' (' . mysqli_errer($DB) . ")\n";
			}
			if (!empty($customer->altFirstName))
			{
				$query = "UPDATE `customers` SET `altFirstName` = '" . str_replace('"', '', $line[1]) . "'";
				if (!empty($customer->altLastName))
					$query .= ", `altLastName` = '" . str_replace('"', '', $line[2]) . "'";
				$query .= " WHERE `id` = " . $customer->id;
				if (!mysqli_query($DB, $query))
					echo 'Query failed: ' . $query . ' (' . mysqli_errer($DB) . ")\n";
			}
			if (!empty($customer->bFirstName))
			{
				$query = "UPDATE `customers` SET `bFirstName` = '" . str_replace('"', '', $line[3]) . "'";
				if (!empty($customer->bLastName))
					$query .= ", `bLastName` = '" . str_replace(array('"', "\n", "\r"), '', $line[5]) . "'";
				$query .= " WHERE `id` = " . $customer->id;
				if (!mysqli_query($DB, $query))
					echo 'Query failed: ' . $query . ' (' . mysqli_errer($DB) . ")\n";
			}
			if (!empty($customer->bAltFirstName))
			{
				$query = "UPDATE `customers` SET `bAltFirstName` = '" . str_replace('"', '', $line[4]) . "'";
				if (!empty($customer->bAltLastName))
					$query .= ", `bAltLastName` = '" . str_replace(array('"', "\n", "\r"), '', $line[5]) . "'";
				$query .= " WHERE `id` = " . $customer->id;
				if (!mysqli_query($DB, $query))
					echo 'Query failed: ' . $query . ' (' . mysqli_errer($DB) . ")\n";
			}

			++$id;
		}
		fclose($handle);
    }


?>
