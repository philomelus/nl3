<?
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

	$complaint = array();
	$complaint[] = array();
	$complaint[] = array();
	$complaints[] = $complaint;


*/

	set_include_path('..' . PATH_SEPARATOR . get_include_path());

	Header("content-type: text/plain");
	require_once 'inc/security.inc.php';
	require_once 'inc/common.inc.php';

	$records = db_query("SELECT * FROM `customers_complaints` ORDER BY `customer_id`");
	if (!$records)
	{
		echo $DB->error . ' (' . $DB->errno . ")\n";
		die();
	}
	$id = 0;
	echo "\$complaints = array();\n";
	echo "\$complaints[] = array();\n";
	while ($record = $records->fetch_object())
	{
		if ($id != $record->customer_id)
		{
			if ($id != 0)
				echo "\$complaints[] = \$complaint;\n";
			$id = $record->customer_id;
			echo "\$complaint = array();\n";
		}
		echo '$complaint[] = array(\'' . $record->type . '\',\'' . strftime('%Y-%m-%d', strtotime($record->when))
				. '\',\'' . $record->result . '\',' . $record->amount . ");\n";
	}

	echo "\n";

	$records = db_query("SELECT * FROM `customers_service` ORDER BY `customer_id`");
	if (!$records)
	{
		echo $DB->error . ' (' . $DB->errno . ")\n";
		die();
	}
	$id = 0;
	echo "\$services = array();\n";
	while ($record = $records->fetch_object())
	{
		if ($id != $record->customer_id)
		{
			if ($id != 0)
				echo "\$services[] = \$service;\n";
			$id = $record->customer_id;
			echo "\$service = array();\n";
		}
		echo '$service[] = array(\'' . $record->type . '\',\'' . strftime('%Y-%m-%d', strtotime($record->when))
				. '\'' . ");\n";
	}

	echo "\n";

	$records = db_query("SELECT * FROM `customers_service_types` ORDER BY `customer_id`");
	if (!$records)
	{
		echo $DB->error . ' (' . $DB->errno . ")\n";
		die();
	}
	$id = 0;
	echo "\$types = array();\n";
	while ($record = $records->fetch_object())
	{
		if ($id != $record->customer_id)
		{
			if ($id != 0)
				echo "\$types[] = \$type;\n";
			$id = $record->customer_id;
			echo "\$type = array();\n";
		}
		echo '$type[] = array(\'' . strftime('%Y-%m-%d', strtotime($record->when))
				. '\',' . $record->type_id_from . ',' . $record->type_id_to . ");\n";
	}

?>
