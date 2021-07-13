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
function smarty_function_route_title($params, &$smarty)
{
	global $Routes;

	$id = null;
	$show = false;
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
			case 'id':
				$$_key = (string)$_val;
				break;
			
			case 'show':
				$$_key = (boolean)$_val;
				break;
			
			default:
				$smarty->trigger_error('route_title: extra attribute ' . $_key . ' unknown', E_USER_NOTICE);
				break;
		}
	}

	// If its null or 0, there is nothing to lookup
	$text = '';
	if (empty($id))
		$text = '&nbsp;';
	else
	{
		// If global not set, try setting it
		if (!isset($Routes))
			populate_routes();
		
		// Get it from global if possible
		if (isset($Routes))
		{
			if (isset($Routes[$id]))
				$text = $Routes[$id];
		}
	}

	// Get it from database if all else fails
	if (empty($text))
	{
		$text = db_query_result('SELECT `title` FROM `routes` WHERE `id` = ' . $id);
		if (!$text)
		{
			$smarty->trigger_error('route_title: unable to locate title for route ' . $id, E_USER_WARNING);
			return sprintf('%04d', $id);
		}
	}
	if ($show)
		$text .= sprintf(' (id = %04d)', $id);
	return $text;
}
?>
