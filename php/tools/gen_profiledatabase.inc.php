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

	require_once 'newsledgerdb.inc.php';
	require_once 'inc/constants.inc.php';

	// If ProfileData.php changes these, they gotta be chaned here too
	define('DESC',			'@desc');
	define('ENUM',			'@enum');
	define('TYPE',			'@type');
	define('IS_GLOBAL',		'@global');
	define('IS_READONLY',	'@readOnly');
	define('IS_REQUIRED',	'@required');

	$DB = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (!$DB)
		die(mysqli_connect_error() . ' (' . mysqli_connect_errno() . ')');
	
	// Get all the keys, and their breakdown
	$records = $DB->query('SELECT `key` FROM `configuration`');
	if (!$records)
		die($DB->error . ' (' . $DB->errno . ')');
	$KEYS = array();
	while ($record = $records->fetch_object())
		$KEYS[$record->key] = explode('-', $record->key);

	// Create the array
	$CONFIGURATION = array();
	foreach($KEYS as $k => $a)
	{
		for ($i = 0; $i < count($a); ++$i)
		{
			$index = '$CONFIGURATION';
			for ($n = 0; $n < $i; ++$n)
				$index .= '[\'' . $a[$n] . '\']';
			eval('if (!isset(' . $index . '[\'' . $a[$i] . '\'])) '
					. $index . '[\'' . $a[$i] . '\'] = array();');
		}
	}

	// Populate the array keys
	$RECORDS = array();
	$records = $DB->query('SELECT * FROM `configuration`');
	if (!$records)
		die($DB->error . ' (' . $DB->errno . ')');
	while ($record = $records->fetch_object())
	{
		$fields = array
			(
				DESC => stripslashes($record->desc),
				TYPE => $record->type,
				IS_GLOBAL => $record->global == 'Y' ? true : false,
				IS_REQUIRED => $record->required == 'Y' ? true : false,
				IS_READONLY =>  $record->readOnly == 'Y' ? true : false
			);
		if ($record->type == CFG_ENUM)
			$fields[ENUM] = eval($record->enum);
		$RECORDS[$record->key] = $fields;
		$cmd = '$CONFIGURATION';
		foreach(explode('-', $record->key) as $s)
			$cmd .= '[\'' . $s .'\']';
		eval($cmd . ' = $fields;');
	}

	// Generate the code
	echo "<?php\n"
			. "	class ProfileDataBase\n"
			. "	{\n"
			. "		protected function initialize(&\$v)\n"
			. "		{\n"
			. "			\$v = array\n"
			. "				(\n";
	foreach($CONFIGURATION as $k => $v)
		code_recurse($k, $v, "					");



	echo "				);\n"
			. "		}\n"
			. "	\n";

// keys()
	echo "		protected function _keys(&\$v, \$global)\n"
			. "		{\n"
			. "			if (\$global)\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	reset($KEYS);
	foreach($KEYS as $key => $subs)
	{
		if ($RECORDS[$key][IS_GLOBAL])
			echo "						'" . $key . "',\n";
	}
	echo "					);\n"
			. "			}\n"
			. "			else\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	reset($KEYS);
	foreach($KEYS as $key => $subs)
	{
		if (!$RECORDS[$key][IS_GLOBAL])
			echo "						'" . $key . "',\n";
	}
	echo "					);\n"
			. "			}\n"
			. "		}\n"
			. "	\n";

// keys1()
	echo "		protected function _keys1(&\$v, \$global)\n"
			. "		{\n"
			. "			if (\$global)\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keys(true, 0, "						");
	echo "					);\n"
			. "			}\n"
			. "			else\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keys(false, 0, "						");
	echo "					);\n"
			. "			}\n"
			. "		}\n"
			. "	\n";

// keys2()
	echo "		protected function _keys2(&\$v, \$global)\n"
			. "		{\n"
			. "			if (\$global)\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keys(true, 1, "						");
	echo "					);\n"
			. "			}\n"
			. "			else\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keys(false, 1, "						");
	echo "					);\n"
			. "			}\n"
			. "		}\n"
			. "	\n";

// keys3()
	echo "		protected function _keys3(&\$v, \$global)\n"
			. "		{\n"
			. "			if (\$global)\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keys(true, 2, "						");
	echo "					);\n"
			. "			}\n"
			. "			else\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keys(false, 2, "						");
	echo "					);\n"
			. "			}\n"
			. "		}\n"
			. "	\n";

// keys2FromKeys1
	echo "		protected function _keys2FromKeys1(&\$v, \$global)\n"
			. "		{\n"
			. "			if (\$global)\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keysFromSubkeys(true, 0, 1, "						");
	echo "					);\n"
			. "			}\n"
			. "			else\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keysFromSubkeys(false, 0, 1, "						");
	echo "					);\n"
			. "			}\n"
			. "		}\n"
			. "	\n";

// keys3FromKeys2
/*
	echo "		protected function _keys3FromKeys2(&\$v, \$global)\n"
			. "		{\n"
			. "			if (\$global)\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keysFromSubkeys2(true, 1, 2, "						");
	echo "					);\n"
			. "			}\n"
			. "			else\n"
			. "			{\n"
			. "				\$v = array\n"
			. "					(\n";
	echo_keysFromSubkeys2(false, 1, 2, "						");
	echo "					);\n"
			. "			}\n"
			. "		}\n"
			. "	\n";
*/

	echo "	}\n"
			. "?>\n";
//--------------------------------------------------------------------------------------------------
// Support functions below here
//--------------------------------------------------------------------------------------------------

	function code_recurse($k, $v, $indent)
	{
		if (is_array($v) && $k != ENUM)
		{
			echo $indent . "'$k' => array\n"
					. $indent . "	(\n";
			foreach($v as $key => $value)
			{
				code_recurse($key, $value, $indent . "		");
			}
			echo $indent . "	),\n";
		}
		else
		{
			switch ($k)
			{
			case DESC:
				echo $indent . "ProfileData::DESC => '$v',\n";
				break;
			case IS_GLOBAL:
				if ($v)
					echo $indent . "ProfileData::IS_GLOBAL => true,\n";
				break;
			case TYPE:
				echo $indent . "ProfileData::TYPE => ";
				switch ($v)
				{
				case CFG_BOOLEAN:	echo 'CFG_BOOLEAN';		break;
				case CFG_COLOR:		echo 'CFG_COLOR';		break;
				case CFG_ENUM:		echo 'CFG_ENUM';		break;
				case CFG_FLOAT:		echo 'CFG_FLOAT';		break;
				case CFG_INTEGER:	echo 'CFG_INTEGER';		break;
				case CFG_MONEY:		echo 'CFG_MONEY';		break;
				case CFG_PERIOD:	echo 'CFG_PERIOD';		break;
				case CFG_ROUTE:		echo 'CFG_ROUTE';		break;
				case CFG_TELEPHONE:	echo 'CFG_TELEPHONE';	break;
				case CFG_TYPE:		echo 'CFG_TYPE';		break;
				case CFG_STRING:	echo 'CFG_STRING';		break;
				default:			echo 'CRAPOLA';			break;
				}
				echo ",\n";
				break;
			case IS_REQUIRED:
				if ($v)
					echo $indent . "ProfileData::IS_REQUIRED => true,\n";
				break;
			case IS_READONLY:
				if ($v)
					echo $indent . "ProfileData::IS_READONLY => true,\n";
				break;
			case ENUM:
				echo $indent . "ProfileData::ENUM => array\n"
						. $indent . "	(\n";
				foreach($v as $index => $value)
					echo $indent . "		$index => '$value',\n";
				echo $indent . "	),\n";
				break;
			default:
				echo $indent . "'$k' = '$v',\n";
				break;
			}
		}
	}

	function echo_keys($global, $index, $space)
	{
		global $RECORDS, $KEYS;
		$temp = array();
		reset($KEYS);
		foreach($KEYS as $key => $subs)
		{
			if (isset($subs[$index]))
			{
				if ($RECORDS[$key][IS_GLOBAL] === $global)
				{
					if (!in_array($subs[$index], $temp))
						$temp[] = $subs[$index];
				}
			}
		}
		sort($temp, SORT_STRING);
		foreach($temp as $subkey)
			echo $space . '\'' . $subkey . "',\n";
		unset($temp);
	}

	function echo_keysFromSubkeys($global, $index1, $index2, $space)
	{
		global $RECORDS, $KEYS;
		$keys1 = array();
		reset($KEYS);
		foreach($KEYS as $key => $subs)
		{
			if ($RECORDS[$key][IS_GLOBAL] === $global)
			{
				if (!in_array($subs[$index1], $keys1))
					$keys1[] = $subs[$index1];
			}
		}
		reset($keys1);
		foreach($keys1 as $key1)
		{
			echo $space . "'" . $key1 . "' => array\n"
				. $space . "	(\n";
			$temp = array();
			reset($KEYS);
			foreach($KEYS as $key => $subs)
			{
				if (strncmp($key1 . '-', $key, strlen($key1) + 1) === 0)
				{
					if ($RECORDS[$key][IS_GLOBAL] === $global)
					{
						if (!in_array($subs[$index2], $temp))
							$temp[] = $subs[$index2];
					}
				}
			}
			sort($temp, SORT_STRING);
			foreach($temp as $key)
				echo $space . "		'" . $key . "',\n";
			unset($temp);
			echo $space . "	),\n";
		}
	}

/*
	function echo_keysFromSubkeys2($global, $index1, $index2, $space)
	{
		global $RECORDS, $KEYS;
		$keys1 = array();
		reset($KEYS);
		foreach($KEYS as $key => $subs)
		{
			if ($RECORDS[$key][IS_GLOBAL] === $global)
			{
				if (!in_array($subs[$index1], $keys1))
					$keys1[] = $subs[$index1];
			}
		}
		reset($keys1);
		foreach($keys1 as $key1)
		{
			echo $space . "'" . $key1 . "' => array\n"
				. $space . "	(\n";
			$temp = array();
			reset($KEYS);
			foreach($KEYS as $key => $subs)
			{
				if (strncmp($key1 . '-', $key, strlen($key1) + 1) === 0)
				{
					if ($RECORDS[$key][IS_GLOBAL] === $global)
					{
						if (!in_array($subs[$index2], $temp))
							$temp[] = $subs[$index2];
					}
				}
			}
			sort($temp, SORT_STRING);
			foreach($temp as $key)
				echo $space . "		'" . $key . "',\n";
			unset($temp);
			echo $space . "	),\n";
		}
	}
*/

?>
