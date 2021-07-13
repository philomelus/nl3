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

	set_include_path('..' . PATH_SEPARATOR . '../inc' . PATH_SEPARATOR . get_include_path());

	require_once 'security.inc.php';
	require_once 'common.inc.php';

	$DB = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (!$DB)
		die(mysqli_connect_error() . ' (' . mysqli_connect_errno() . ')');
	
	// Get all the keys, and their breakdown
	$records = $DB->query('SELECT `key`, `value` FROM `configuration`');
	if (!$records)
		die($DB->error . ' (' . $DB->errno . ')');
	$KEYS = array();
	while ($record = $records->fetch_object())
	{
		// Don't cache billing period, since it changes every period
		if ($record->key == 'billing-period')
			continue;
		
		$KEYS[$record->key] = array
			(
				'parts' => explode('-', $record->key),
				'value' => $record->value
			);
	}

	// Generate the code
	echo "<?php\n"
			. "	\$CONFIG = array();\n";
	foreach($KEYS as $k => $a)
	{
		echo "	\$CONFIG";
		foreach($a['parts'] as $n)
			echo '[\'' . $n . '\']';
		$value = str_replace("\r\n", '\n', $a['value'], $count);
		$total = $count;
		$value = str_replace("\t", '\t', $value, $count);
		$total += $count;
		if ($total > 0)
			echo ' = "' . $value . "\";\n";
		else
			echo ' = \'' . $value . "';\n";
	}
	echo "?>\n";

?>