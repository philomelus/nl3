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

    /*
     * This was originally used to import data from a client that kept
     * all their data in an excel spreadsheet.  The spreadsheet was
     * read into a database, and cleaned up as needed, then this was
     * ran to convert it to our format.  No garantee it will work for
     * any other clients.
     */
    
	// TODO:  Forgot to add new customers new routes_sequence table

	define('TRACE', FALSE);
	define('XREF', TRUE);

	Header("content-type: text/plain");

	$DB = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (mysqli_connect_errno())
	{
		$err = ERR_DB_CONNECT;
		$errCode = mysqli_connect_errno();
		$errText = mysqli_connect_error();
		return;
	}

	// Virginize the database
	$DB->query('TRUNCATE `customers`');
	if ($DB->errno)
		die($DB->error . ' (' . $DB->errno . ')' . "\n");
	$DB->query('TRUNCATE `customers_names`');
	if ($DB->errno)
		die($DB->error . ' (' . $DB->errno . ')' . "\n");
	$DB->query('TRUNCATE `customers_addresses`');
	if ($DB->errno)
		die($DB->error . ' (' . $DB->errno . ')' . "\n");
	$DB->query('TRUNCATE `customers_telephones`');
	if ($DB->errno)
		die($DB->error . ' (' . $DB->errno . ')' . "\n");

	if (XREF)
	{
		echo "INSERT INTO\n";
		echo "	`customers_xref` (`route`, `account`, `customer_id`)\n";
		echo "VALUES\n";
	}

	// Loop through all the original customer records
	$CUSTOMERS = $DB->query('SELECT * FROM `customers_original`');
	if (!$CUSTOMERS)
		die($DB->error . ' (' . $DB->errno . ')' . "\n");
	while ($CUSTOMER = $CUSTOMERS->fetch_object())
	{
		$DB->query('BEGIN');
		if ($DB->errno)
			die($DB->error . ' (' . $DB->errno . ')' . "\n");

		// Create customer record
		if (TRACE)
			echo 'Creating customer record ... ';
		switch (trim($CUSTOMER->Type))
		{
		case 'DO':	$type = 1;	break;
		case 'SO':	$type = 2;	break;
		case 'DS':	$type = 3;	break;
		default:	die('Unknown Type');
		}
		$query = '
INSERT INTO
	`customers`
	(
		`id`,`route_id`,`type_id`,`active`,
		`routeList`,`started`,`rateType`,`rateOverride`,
		`billType`,`billBalance`,`billStopped`,`balance`,
		`lastPayment`,`billNote`,`notes`,`deliveryNote`
	)
VALUES
	(
		0,
		' . intval($CUSTOMER->Route) . ',
		' . $type . ',
		\'Y\',
		\'Y\',
		' . (strlen(trim($CUSTOMER->Started)) == 0 ? '\'2008-02-29\'' : '\'' . strftime('%Y-%m-%d', strtotime($CUSTOMER->Started)) . '\'') . ',
		\'STANDARD\',
		0,
		' . $type . ',
		' . floatval($CUSTOMER->Balance) . ',
		\'N\',
		' . floatval($CUSTOMER->Balance) . ',
		NULL,
		\'\',
		\'\',
		\'\'
	)
';/**/
		$DB->query($query);
		if ($DB->errno)
			die((TRACE ? "FAILED\n" : '') . $DB->error . ' (' . $DB->errno . ')' . "\n");
		$customer_id = $DB->insert_id;
		if (TRACE)
			echo 'success -> ' . $customer_id . "\n";
		if (XREF)
			echo '(' . intval($CUSTOMER->Route) . ',' . intval($CUSTOMER->Account) . ',' . $customer_id . "),\n";

		// Create name record
		if (TRACE)
			echo "\tAdding customer name ... ";
		$first = ucfirst(strtolower(stripos($CUSTOMER->Name, ' ') ? substr($CUSTOMER->Name, 0, stripos($CUSTOMER->Name, ' ')) : $CUSTOMER->Name));
		$last = ucfirst(strtolower(stripos($CUSTOMER->Name, ' ') ? substr($CUSTOMER->Name, stripos($CUSTOMER->Name, " ") + 1) : $CUSTOMER->Name));
		$query = '
INSERT INTO
	`customers_names`
	(
		`customer_id`,`sequence`,`created`,`updated`,
		`title`,`first`,`last`,`suffix`
	)
VALUES
	(
		' . $customer_id . ',
		1,
		NOW(),
		NOW(),
		\'\',
		\'' . db_escape($first) . '\',
		\'' . db_escape($last) . '\',
		\'\'
	)
';/**/
		$DB->query($query);
		if ($DB->errno)
			die((TRACE ? "FAILED\n" : '') . $DB->error . ' (' . $DB->errno . ')' . "\n");
		if (TRACE)
			echo 'success -> ' . $first . (empty($last) ? '' : ' ' . $last) . "\n";

		// Create delivery address record
		if (TRACE)
			echo "\tAdding delivery address ... ";
		$da = 'Delivery Address';
		$ma = 'Mailing Address';
		$cs = 'City & State';
		$temp = trim($CUSTOMER->$ma);
		$address = ucwords(strtolower(trim($CUSTOMER->$da)));
		unset($foo);
		preg_match('/(.+) ([NSEWnsew][NSEWnsew]) (.+)/', $address, $foo);
		if (!empty($foo[2]))
			$address = $foo[1] . ' ' . strtoupper($foo[2]) . ' ' . $foo[3];
		$city = ucfirst(strtolower(stripos($CUSTOMER->$cs, ' ') ? substr($CUSTOMER->$cs, 0, stripos($CUSTOMER->$cs, ' ')) : $CUSTOMER->$cs));
		$state = (stripos($CUSTOMER->$cs, ' ') ? substr($CUSTOMER->$cs, stripos($CUSTOMER->$cs, " ") + 1) : $CUSTOMER->$cs);
		$query = '
INSERT INTO
	`customers_addresses`
	(
		`customer_id`,`sequence`,`address1`,`address2`,
		`city`,`state`,`zip`,`notes`
	)
VALUES
	(
		' . $customer_id . ',
		1,
		\'' . db_escape($address) . '\',
		\'\',
		\'' . (empty($temp) ? db_escape($city) : '') . '\',
		\'' . (empty($temp) ? db_escape($state) : '') . '\',
		\'' . (empty($temp) ? db_escape($CUSTOMER->Zip) : '') . '\',
		\'\'
	)
';/**/
		$DB->query($query);
		if ($DB->errno)
			die((TRACE ? "FAILED\n" : '') . $DB->error . ' (' . $DB->errno . ')' . "\n");
		if (TRACE)
			echo 'success -> ' . $address . "\n";

		// Create billing address record if needed
		if (!empty($temp))
		{
			if (TRACE)
				echo "\tAdding billing address ... ";
			$cs = 'City & State';
			$address = ucwords(strtolower(trim($CUSTOMER->$ma)));
			unset($foo);
			preg_match('/(.+) ([NSEWnsew][NSEWnsew]) (.+)/', $address, $foo);
			if (!empty($foo[2]))
				$address = $foo[1] . ' ' . strtoupper($foo[2]) . ' ' . $foo[3];
			$city = ucfirst(strtolower(stripos($CUSTOMER->$cs, ' ') ? substr($CUSTOMER->$cs, 0, stripos($CUSTOMER->$cs, ' ')) : $CUSTOMER->$cs));
			$state = (stripos($CUSTOMER->$cs, ' ') ? substr($CUSTOMER->$cs, stripos($CUSTOMER->$cs, " ") + 1) : $CUSTOMER->$cs);
			$query = '
INSERT INTO
	`customers_addresses`
	(
		`customer_id`,`sequence`,`address1`,`address2`,
		`city`,`state`,`zip`,`notes`
	)
VALUES
	(
		' . $customer_id . ',
		101,
		\'' . db_escape($address) . '\',
		\'\',
		\'' . db_escape($city) . '\',
		\'' . db_escape($state) . '\',
		\'' . db_escape($CUSTOMER->Zip) . '\',
		\'\'
	)
';/**/
			$DB->query($query);
			if ($DB->errno)
				die((TRACE ? "FAILED\n" : '') . $DB->error . ' (' . $DB->errno . ')' . "\n");
			if (TRACE)
				echo 'success -> ' . $address . "\n";
		}

		// Telephone number
		if (TRACE)
			echo "\tAdding telephone number ... ";
		$temp = trim($CUSTOMER->Telephone);
		$query = '
INSERT INTO
	`customers_telephones`
	(
		`customer_id`,`sequence`,`created`,`updated`,
		`type`,`number`,`note`
	)
VALUES
	(
		' . $customer_id . ',
		1,
		NOW(),
		NOW(),
		\'Main\',
		\'' . (strlen($temp) == 8 ? db_escape('(503) ' . trim($CUSTOMER->Telephone)) : '') . '\',
		\'\'
	)
';/**/
		$DB->query($query);
		if ($DB->errno)
			die((TRACE ? "FAILED\n" : '') . $DB->error . ' (' . $DB->errno . ')' . "\n");
		if (TRACE)
			echo 'success -> (503) ' . trim($CUSTOMER->Telephone) . "\n";

		$DB->query('COMMIT');
	}
	$CUSTOMERS->close();

?>
