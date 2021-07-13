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

	set_include_path(HOME . PATH_SEPARATOR . '..' . PATH_SEPARATOR . get_include_path());

	require_once 'client.php';
	require_once 'inc/common.inc.php';

	// Get list of customers that are in sequence table correctly
	$query =
"
SELECT
	`c`.`id`,
	`c`.`route_id`,
	`s`.`id`,
	`s`.`route_id`
FROM
	`customers` AS `c`,
	`routes_sequence` AS `s`
WHERE
	`c`.`id` = `s`.`tag_id`
	AND `c`.`route_id` = `s`.`route_id`
ORDER BY
	`c`.`route_id`,
	`c`.`id`
";
	$result = mysqli_query($DB, $query);
	if (!$result)
	{
		echo "Error: " . mysqli_error($DB) . ' (' . mysqli_errno($DB) . ")\n";
		exit;
	}

	// Put list into array
	$good = array();
	while ($record = mysqli_fetch_object($DB, $result))
		$good[] = $record->id;

	// Query active customers
	$query = "SELECT `id` FROM `customers` WHERE `active` = 'N' ORDER BY `id`";
	$result = mysqli_query($DB, $query);
	if (!$result)
	{
		echo "Error: " . mysqli_error($DB) . ' (' . mysqli_errno($DB) . ")\n";
		exit;
	}

	// Determine who's fucked up
	while ($record = mysqli_fetch_object($DB, $result))
	{
		if (!in_array($record->id, $good))
		{
			printf('Missing: %06d', $record->id);
			echo "\n";
		}
	}

?>
