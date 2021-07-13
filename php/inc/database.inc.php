<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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
/**
 *	Database wrapper functions
 *	@package database
 */

    require_once 'inc/db.inc.php';
	require_once 'inc/errors.inc.php';
    require_once 'inc/sql.inc.php';

//-----------------------------------------------------------------------------
// Database specific errors
	/**
	 * Error connecting to database.  Normally prevents everything else.
	 * @name ERR_DB_CONNECT
	 */
	define('ERR_DB_CONNECT',		-1000);
	/**
	 * Generic query failure.  Normally a SQL syntax error.
	 * @name ERR_DB_QUERY
	 */
	define('ERR_DB_QUERY',			-1003);

//-----------------------------------------------------------------------------
// Connect to and open the database just by including this file

	// Create connection to database server
	$DB = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (mysqli_connect_errno())
	{
		$err = ERR_DB_CONNECT;
		$errCode = mysqli_connect_errno();
		$errText = mysqli_connect_error();
		return;
	}

	// Success!
	$err = ERR_SUCCESS;

//-----------------------------------------------------------------------------
/**
 *	Returns a MySQL FIELD for ordering the custoemr delivery types by abbreviation
 *	@param string $field Name of the field to sort (normally 'type_id')
 *	@return string MySQL formatted field
 */

	function db_customer_type_field($field = 'type_id')
	{
		global $DeliveryTypes;
		populate_types();
		$temp = array();
		foreach($DeliveryTypes as $id => $type)
			$temp[$id] = $type['abbr'];
		asort($temp);
		$str = '';
		foreach($temp as $i => $t)
			$str .= ',' . $i;
		return 'FIELD(`' . $field . '`' . $str . ')';
	}

//-----------------------------------------------------------------------------
/**
 *	Escape string for query
 *	@param string $string String to escape
 *	@return string Escaped string ready for database
 */

	function db_escape($string)
	{
		global $DB;
		return $DB->escape_string($string);
	}

//-----------------------------------------------------------------------------
/**
 *	Determine whether a record exists.
 *	@param string $table Name of table containing record
 *	@param array $search Fields used to locate record
 *	@return boolean true if record exists, false otherwise
 */

	function db_exists($table, $search)
	{
		$errObj = new error_stack();
		if (count($search) == 0)
			return false;
		$q = 'SELECT COUNT(*) FROM `' . $table . '` WHERE ';
		$_a = '';
		foreach($search as $_f => $_v)
		{
			$q .= $_a . ' `' . $_f . '` = ' . $_v;
			$_a = ' AND';
		}
		return db_query_result($q);
	}

//-----------------------------------------------------------------------------
/**
 *	Insert new record.  Record MUST NOT already exist.
 *	@param string $table Name of table containing record
 *	@param array $values Data to create record, which must be quoted and escaped already
 *	@return boolean|integer false on failure; true or insert_id on success
 */

	function db_insert($table, $values)
	{
		global $DB;

		$q = 'INSERT INTO `' . $table . '` (';
		$qv = 'VALUES (';
		$_c = '';
		foreach($values as $_f => $_v)
		{
			$q .= $_c . '`' . $_f . '`';
			$qv .= $_c . $_v;
			$_c = ', ';
		}
        // TODO:  Automatically check for created date field, and fill it in if not passed
		if (!db_query($q . ') ' . $qv . ')'))
			return false;
		if (is_int($DB->insert_id) && $DB->insert_id != 0)
			return $DB->insert_id;
		return true;
	}

//-----------------------------------------------------------------------------
/**
 *	Generic query wrapper
 *	@param string $query The query to execute
 *	@return mixed Whatever the query would normally return
 */

	function db_query($query)
	{
		global $DB;
		global $err, $errCode, $errQuery, $errText;

		$errObj = new error_stack();

		$result = $DB->query($query);
		if ($DB->errno)
		{
			$err = ERR_DB_QUERY;
			$errCode = $DB->errno;
			$errQuery = $query;
			$errText = $DB->error;
			return false;
		}
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Perform query that will return a single row, and return the row as an object
 *	@param string $query Query to execute
 *	@return object Object holding fields of row
 */

	function db_query_object($query)
	{
		global $err, $errCode, $errQuery;

		$errObj = new error_stack();

		// Get record from database
		$result = db_query($query);
		if (!$result)
			return false;
		if ($result->num_rows == 0)
		{
			$err = ERR_NOTFOUND;
			$errCode = 0;
			$errQuery = $query;
			$errText = 'Not Found';
			return false;
		}

		// Success!
		$err = ERR_SUCCESS;
		return $result->fetch_object();
	}

//-----------------------------------------------------------------------------
/**
 *	Generic query wrapper for queries that return a single result field and row.
 *	@param string $query Query to execute
 *	@return string Index 0 of first row of result set of query.
 */

	function db_query_result($query)
	{
		global $DB, $err, $errCode;
		$result = db_query($query);
		if (!$result)
			return false;
		if ($result->num_rows == 0)
		{
			$err = ERR_SUCCESS;
			$errCode = ERR_NOTFOUND;
			return false;
		}
		else
			$errCode = 0;
		$data = $result->fetch_row();
		$result->close();
		return $data[0];
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a MySQL FIELD for ordering the routes by name
 *	@param string $field Name of the field to sort (normally 'route_id')
 *	@return string MySQL formatted field
 */

	function db_route_field($field = 'route_id')
	{
		global $Routes;
		populate_routes();
		$temp = $Routes;
		asort($temp);
		$str = '';
		foreach($temp as $r => $t)
			$str .= ',' . $r;
		if (strstr($field, '`'))
			return 'FIELD(' . $field . $str . ')';
		else
			return 'FIELD(`' . $field . '`' . $str . ')';
	}

//-----------------------------------------------------------------------------
/**
 *	Update already existing record
 *	@param string $table Name of table that does or will contain record
 *	@param array $search Fields used to locate existing record (already escaped and quoted)
 *	@param array $values Fields in record to update (already escaped and quoted)
 *	@return boolean true if update was completed successfully, false otherwise
 */

	function db_update($table, $search, $values)
	{
		$q = 'UPDATE `' . $table . '` SET';
		$_c = '';
		foreach($values as $_f => $_v)
		{
			$q .= $_c . ' `' . $_f . '` = ' . $_v;
			$_c = ',';
		}
		$q .= ' WHERE';
		$_a = '';
		foreach($search as $_f => $_v)
		{
			$q .= $_a . ' `' . $_f . '` = ' . $_v;
			$_a = ' AND';
		}
		return db_query($q);
	}

//-----------------------------------------------------------------------------
/**
 *	If the record exists, update it, otherwise create it
 *	@param string $table Name of table that does or will contain record
 *	@param array $search Fields used to locate existing record
 *	@param array $values Fields in record to update
 *	@return boolean|integer false on failure; true or insert_id on success
 */

	function db_update_or_insert($table, $search, $values)
	{
		global $err;

		$found = db_exists($table, $search);
		if ($err < ERR_SUCCESS)
			return false;

		// Insert or update record
		if ($found)
			return db_update($table, $search, $values);
		else
			return db_insert($table, array_merge($search, $values));
	}

//-----------------------------------------------------------------------------
// Updates $err, etc. and returns non-false on success.

    function db_tr_commit()
    {
        return db_query(SQL_COMMIT);
    }

//-----------------------------------------------------------------------------
// Updates $err, etc. and returns non-false on success.

    function db_tr_start()
    {
        return db_query(SQL_TRANSACTION);
    }

//-----------------------------------------------------------------------------
// Updates $err, etc. and returns non-false on success.

    function db_tr_rollback()
    {
        return db_query(SQL_ROLLBACK);
    }

//-----------------------------------------------------------------------------

	include_once 'inc/config.inc.php';
	include_once 'inc/audit.inc.php';

?>
