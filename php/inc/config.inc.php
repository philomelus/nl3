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

	define('CFG_NONE', '!@#bad#@!');
	
//-----------------------------------------------------------------------------

	function get_config($key, $def = CFG_NONE)
	{
		return get_configEx($key, 'value', $def);
	}

//-----------------------------------------------------------------------------

	function get_configEx($key, $field, $def = CFG_NONE)
	{
		global $DB, $err, $errCode, $errText, $CONFIG, $USER_CONFIG;
		
		$errObj = new error_stack();

		// Check user configuration if present
		if (isset($USER_CONFIG) && isset($USER_CONFIG[$key]) && isset($CONFIG[$key][$field]))
			return $USER_CONFIG[$key][$field];
		
		// Check global configuration if present
		if (isset($CONFIG) && isset($CONFIG[$key]) && isset($CONFIG[$key][$field]))
			return $CONFIG[$key][$field];
		
		// Build where clause
		$where = ' WHERE `key` = \'' . db_escape($key) . '\'';

		// Grab it from users table if user info available
		$count = 0;
		if (isset($_SESSION) && isset($_SESSION['uid']))
		{
			$query = 'SELECT `' . $field . '` FROM `users_configuration`' . $where . ' AND `user_id` = '
					. $_SESSION['uid'] . ' LIMIT 1';
			$set = db_query($query);
			if (!$set)
				return $def;
			$count = $set->num_rows;
		}
		
		// If it doesn't exist, query global config
		if ($count == 0)
		{
			$query = 'SELECT `' . $field . '` FROM `configuration`' . $where . ' LIMIT 1';
			$set = db_query($query);
			if (!$set)
				return $def;
			
			// If it still didn't exist, return default
			if ($set->num_rows == 0)
			{
				$err = ERR_SUCCESS;
				return $def;
			}
		}
		
		// Return the value
		$err = ERR_SUCCESS;
		$data = $set->fetch_array(MYSQLI_NUM);
		$set->close();
		return $data[0];
	}

//-----------------------------------------------------------------------------

	function get_globalConfig($key, $def = CFG_NONE)
	{
		return get_globalConfigEx($key, 'value', $def);
	}
	
//-----------------------------------------------------------------------------

	function get_globalConfigEx($key, $field, $def = CFG_NONE)
	{
		global $DB, $err, $CONFIG;
		
		$errObj = new error_stack();
		
		// Check global configuration if present
		if (isset($CONFIG) && isset($CONFIG[$key]) && isset($CONFIG[$key][$field]))
			return $CONFIG[$key][$field];
		
		// Build common where clause
		$query = 'SELECT `' . $field . '` FROM `configuration` WHERE `key` = \''
				. db_escape($key) . '\' LIMIT 1';
		
		// Grab it from table
		$set = db_query($query);
		if (!$set)
			return $def;
		
		// Success if we get here
		$err = ERR_SUCCESS;
		
		// If it doesn't exist, query global config
		if ($set->num_rows == 0)
			return $def;
		
		// Return the value
		$data = $set->fetch_array(MYSQLI_NUM);
		$set->close();
		return $data[0];
	}

//-----------------------------------------------------------------------------

	function get_userConfig($key, $def = CFG_NONE)
	{
		return get_userConfigEx($key, 'value', $def);
	}
	
//-----------------------------------------------------------------------------

	function get_userConfigEx($key, $field, $def = CFG_NONE)
	{
		global $DB, $err, $USERCONFIG;
		
		$errObj = new error_stack();
		
		// Check global configuration if present
		if (isset($USERCONFIG) && isset($USERCONFIG[$_SESSION['uid']])
				&& isset($USERCONFIG[$_SESSION['uid']][$key])
				&& isset($USERCONFIG[$_SESSION['uid']][$key][$field]))
		{
			return $USERCONFIG[$_SESSION['uid']][$key][$field];
		}
		
		// Grab it from database
		$query = 'SELECT `' . $field . '` FROM `users_configuration` WHERE `key` = \''
				. db_escape($key) . '\' AND `user_id` = ' . $_SESSION['uid']
				. ' LIMIT 1';
		$set = db_query($query);
		if (!$set)
			return $def;
		
		// Success if we get here
		$err = ERR_SUCCESS;
		
		// If it doesn't exist, return default
		if ($set->num_rows == 0)
			return $def;
		
		// Return the value
		$data = $set->fetch_array(MYSQLI_NUM);
		$set->close();
		return $data[0];
	}

//-----------------------------------------------------------------------------

	function lup_globalConfig($key)
	{
		global $DB;
		global $err, $errCode, $errQuery, $errText;
		
		$errObj = new error_stack();
		
		// Get record from database
		$result = db_query('SELECT * FROM `configuration` WHERE `key` = \''
				. db_escape($key) . '\' LIMIT 1');
		if (!$result)
			return false;
		if ($result->num_rows == 0)
		{
			$err = ERR_NOTFOUND;
			$errCode = ERR_NOTFOUND;
			$errQuery = '';
			$errText = 'Configuration ' . $key . ' doesn\'t exist';
			return false;
		}

		// Success!
		$err = ERR_SUCCESS;
		return $result->fetch_object();
	}

//-----------------------------------------------------------------------------

	function lup_userConfig($key)
	{
		return lup_userConfigEx($_SESSION['uid'], $key);
	}
	
//-----------------------------------------------------------------------------

	function lup_userConfigEx($uid, $key)
	{
		global $DB;
		global $err, $errCode, $errQuery, $errText;
		
		$errObj = new error_stack();
		
		// Get record from database
		$result = db_query('SELECT * FROM `users_configuration` WHERE `user_id` = ' . $uid
				. ' AND `key` = \'' . db_escape($key) . '\' LIMIT 1');
		if (!$result)
			return false;
		if ($result->num_rows == 0)
		{
			$err = ERR_NOTFOUND;
			$errCode = ERR_NOTFOUND;
			$errQuery = '';
			$errText = 'Configuration ' . $key1 . ' doesn\'t exist';
			return false;
		}

		// Success!
		$err = ERR_SUCCESS;
		return $result->fetch_object();
	}
	
//-----------------------------------------------------------------------------
// Set a persistant configuration parameter in global configuration.

	function set_globalConfig($key, $value)
	{
		set_globalConfigEx($key, $value, 'value');
	}

//-----------------------------------------------------------------------------
// Set a persistant configuration parameter field in global configuration.

	function set_globalConfigEx($key, $value, $field)
	{
		global $DB;
		global $CONFIG;
		
		db_update_or_insert('configuration',
				array('key' => '\'' . db_escape($key) . '\''),
				array($field => '\'' . db_escape($value) . '\''));
		
		// Update global if present
		if (isset($CONFIG))
		{
			if (!isset($CONFIG[$key]))
				$CONFIG[$key] = array();
			$CONFIG[$key][$field] = $value;
		}
	}

//-----------------------------------------------------------------------------
// Set a persistant configuration parameter in user configuration.

	function set_userConfig($key, $value)
	{
		set_userConfigEx($key, $value, 'value');
	}

//-----------------------------------------------------------------------------
// Set a persistant configuration parameter field in user configuration.

	function set_userConfigEx($key, $value, $field)
	{
		global $USER_CONFIG;
		
		db_update_or_insert('users_configuration', NULL,
				array
					(
						'key' => "'" . db_escape($key) . "'",
						'user_id' => $_SESSION['uid']
					),
				array($field => "'" . db_escape($value) . "'"));
		
		// Check user configuration if present
		if (isset($USER_CONFIG) && isset($USER_CONFIG[$key]) && isset($CONFIG[$key][$field]))
			$USER_CONFIG[$key][$field] = $value;
	}

?>
