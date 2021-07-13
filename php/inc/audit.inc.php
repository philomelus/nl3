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
	function audit($what)
	{
		global $DB;
		
		$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 'NULL';
		$r = $DB->query('INSERT INTO `audit_log` SET `when` = NOW(), `user_id` = ' . $uid
				. ', `what` = \'' . db_escape($what) . '\'');
		if (!$r)
			error_log($DB->error . ' (' . $DB->errno . ')');
	}

	// array(field => value)
	function audit_add(&$array)
	{
		$text = '';
		foreach ($array as $f => $v)
			$text .= ' ' . $f . ' = ' . audit_value($f, $v) . '.';
		return $text;
	}
	
	// array(field=>array(old, new), [...])
	function audit_update(&$array)
	{
		$text = '';
		foreach ($array as $f => $v)
		{
			$old = audit_value($f, $v[0]);
			$new = audit_value($f, $v[1]);
			if (substr($new, 0, 1) == '\'' && substr($old, 0, 1) != '\'')
				$old = '\'' . $old . '\'';
			else if (substr($old, 0, 1) == '\'' && substr($new, 0, 1) != '\'')
				$new = '\'' . $new . '\'';
			$text .= ' ' . $f . ' was ' . $old . ', now is ' . $new . '.';
		}
		return $text;
	}
	
	// array(field=>array(new), [...])
	function audit_update_o(&$array, &$original)
	{
		$text = '';
		foreach ($array as $f => $v)
		{
			$old = audit_value($f, $original->$f);
			$new = audit_value($f, $v);
			if (substr($new, 0, 1) == '\'' && substr($old, 0, 1) != '\'')
				$old = '\'' . $old . '\'';
			else if (substr($old, 0, 1) == '\'' && substr($new, 0, 1) != '\'')
				$new = '\'' . $new . '\'';
			$text .= ' ' . $f . ' was ' . $old . ', now is ' . $new . '.';
		}
		return $text;
	}
	
	function audit_value($f, $v)
	{
		if ($v === 'NOW()')
			return strftime('%Y-%m-%d %H:%M:%S', time());
		if ($v === 'N' || $v === '\'N\'')
			return 'FALSE';
		if ($v === 'Y' || $v === '\'Y\'')
			return 'TRUE';
		if ($f === 'period_id')
		{
			if (intval($v) == 0)
				return 'Pending';
			else
				return iid2title($v, true);
		}
		return $v;
	}
	
?>
