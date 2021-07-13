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
	require_once 'inc/securitydata.inc.php';
	require_once 'inc/profiledata.inc.php';

	$DB = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (!$DB)
		die(mysqli_connect_error() . ' (' . mysqli_connect_errno() . ')');

	$PROFILE = ProfileData::create();
	$DESC = $PROFILE->descriptions();

	$KEYS = array();

	function recurse($k, $p, $parents)
	{
		global $KEYS;
		$newp = (empty($parents) ? $k : $parents . '-' . $k);
		foreach($p as $key => $profile)
		{
			switch ($key)
			{
			case ProfileData::DESC:
			case ProfileData::ENUM:
			case ProfileData::TYPE:
			case ProfileData::IS_GLOBAL:
			case ProfileData::IS_REQUIRED:
			case ProfileData::IS_READONLY:
				if (!in_array($newp, $KEYS))
				{
					$KEYS[] = $newp;
//					echo 'Valid Key: ' . $newp . "\n";
				}
				break;

			default:
				recurse($key, $profile, $newp);
			}
		}
	}

	// Build KEYS array
	foreach($DESC as $key => $profile)
	{
		recurse($key, $profile, '');
	}

	function bool2enum($v)
	{
		if ($v)
			return '\'Y\'';
		return '\'N\'';
	}

	function enum2string($v)
	{
		$str = 'array(';
		$rn = '';
		foreach($v as $e => $c)
		{
			$str .= $rn . '\'' . $e . '\' => \'' . $c . '\'';
			$rn = ",\r\n";
		}
		$str .= ');';
		return $str;
	}

	// Add the keys to the database
	foreach($KEYS as $key)
	{
		$profile = $PROFILE->lookup($key);
		$found = array
			(
				ProfileData::DESC => false,
				ProfileData::IS_GLOBAL => false,
				ProfileData::TYPE => false,
				ProfileData::IS_REQUIRED => false,
				ProfileData::IS_READONLY => false,
				ProfileData::ENUM => false
			);
		$query = 'INSERT INTO `configuration` SET `key` = \'' . $key . '\'';
		foreach($profile as $k => $v)
		{
			switch($k)
			{
			case ProfileData::DESC:
				$query .= ', `desc` = \'' . $DB->escape_string($v) . '\'';
				break;
			case ProfileData::IS_GLOBAL:
				$query .= ', `global` = ' . bool2enum($v);
				break;
			case ProfileData::TYPE:
				$query .= ', `type` = \'' . $DB->escape_string($v) . '\'';
				break;
			case ProfileData::IS_REQUIRED:
				$query .= ', `required` = ' . bool2enum($v);
				break;
			case ProfileData::IS_READONLY:
				$query .= ', `readOnly` = ' . bool2enum($v);
				break;
			case ProfileData::ENUM:
				$query .= ', `enum` = \'' . $DB->escape_string(enum2string($v)) . '\'';
				break;
			}
			$found[$k] = true;
		}
		foreach($found as $k => $v)
		{
			if (!$v)
			{
				switch($k)
				{
				case ProfileData::DESC:
					$query .= ', `desc` = \'\'';
					break;
				case ProfileData::IS_GLOBAL:
					$query .= ', `global` = \'N\'';
					break;
				case ProfileData::TYPE:
					$query .= ', `type` = \'STRING\'';
					break;
				case ProfileData::IS_REQUIRED:
					$query .= ', `required` = \'N\'';
					break;
				case ProfileData::IS_READONLY:
					$query .= ', `readOnly` = \'N\'';
					break;
				case ProfileData::ENUM:
					$query .= ', `enum` = \'\'';
					break;
				}
			}
		}
		if (!$DB->query($query))
			die($DB->error);
		echo 'Added ' . $key . "\n";
	}
?>
