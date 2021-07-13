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
	require_once 'inc/common.inc.php';
	require_once 'inc/database.inc.php';
	require_once 'inc/errors.inc.php';
	require_once 'inc/ids.inc.php';
	
//-----------------------------------------------------------------------------
// Loop through form field names in $fields, and check against the field name
// in the variables named by $var, and build the right side of an INSERT or
// UPDATE SQL statement.
// If $deref is true, then the keys of $fields are the form field name, and the
// values of $fields are the names of the fields within $var, as well as the
// column names.

	function form_to_query($fields, &$var, $deref = false, &$audit = NULL, $title = NULL)
	{
		reset($fields);
		$query = '';
		$comma = '';
		if ($deref)
		{
			foreach ($fields as $field => $name)
			{
				$temp = stripslashes($_REQUEST[$field]);
				if ($temp != stripslashes($var->$name))
				{
					$query .= $comma . "`" . $name . "` = '" . db_escape($temp) . "'";
					$comma = ', ';
					if (!is_null($audit))
					{
						if (!is_null($title))
							$audit[$title . ' ' . ucfirst($name)] = array(stripslashes($var->$name), $temp);
						else
							$audit[$name] = array(stripslashes($var->$name), $temp);
					}
				}
			}
			return $query;
		}
		else
		{
			foreach ($fields as $field)
			{
				$temp = stripslashes($_REQUEST[$field]);
				if ($temp != stripslashes($var->$field))
				{
					$query .= $comma . "`" . $field . "` = '" . db_escape($temp) . "'";
					$comma = ', ';
					if (!is_null($audit))
					{
						if (!is_null($title))
							$audit[$title . ' ' . ucfirst($field)] = array(stripslashes($var->$name), $temp);
						else
							$audit[$field] = array(stripslashes($var->$field), $temp);
					}
				}
			}
			return $query;
		}
	}
		
//-----------------------------------------------------------------------------
// Generate 3 selects - month, day, and year.  $name is the base of the name
// for the 3 selects, where 'm' is added to the month, 'd' to day, and 'y' to
// year.  Sets class of each select to "list", and adds the help prompts
// defined in the constants H_$nameM, H_$nameD, and H_$nameY.
// Finally, adds a button at the end (or in front) to popup the calendar
// selection popup.

	function gen_dateField($name, $extra = '', $left = false, $id = '')
	{
		$html = '';
		if ($left)
		{
            $html .= '<input type="button" value="Cal"';
            if (!empty($extra))
                $html .= ' ' . $extra;
            if (!empty($id))
                $html .= ' id="' . $id . '"';
            $html .= ' onclick="displayCalendarSelectBox(document.forms[0].'
                . $name . 'y,document.forms[0].'
                . $name . 'm,document.forms[0].' 
                . $name . 'd,false,false,this)">';
		}
		
		// Month
		$html .= '<select name="' . $name . 'm"';
		if (!empty($extra))
            $html .= ' ' . $extra;
        if (!empty($id))
            $html .= ' id="' . $id . 'm"';
		$html .= '>';
		if (isset($_REQUEST[$name . 'm']))
			$val = intval($_REQUEST[$name . 'm']);
		else
			$val = date('n');
		for ($i = 1; $i < 13; ++$i)
		{
			$html .= '<option value="' . sprintf('%02d', $i) . '"';
			if ($i == $val)
				$html .= ' selected="selected"';
			$html .= '>' . date('M', mktime(0, 0, 0, $i, 1, 2006)) . '</option>';
		}
		$html .= '</select>';
		
		// Day
		$html .= '<select name="' . $name . 'd"';
		if (!empty($extra))
			$html .= ' ' . $extra;
        if (!empty($id))
            $html .= ' id="' . $id . 'd"';
		$html .= '>';
		if (isset($_REQUEST[$name . 'd']))
			$val = intval($_REQUEST[$name . 'd']);
		else
			$val = date('j');
		for ($i = 1; $i < 32; ++$i)
		{
			$html .= '<option value="' . sprintf('%02d', $i) . '"';
			if ($i == $val)
				$html .= ' selected="selected"';
			$html .= '>' . $i . '</option>';
		}
		$html .= '</select>';
		
		// Year
		$html .= '<select name="' . $name . 'y"';
		if (!empty($extra))
			$html .= ' ' . $extra;
        if (!empty($id))
            $html .= ' id="' . $id . 'y"';
		$html .= '>';
		if (isset($_REQUEST[$name . 'y']))
			$val = intval($_REQUEST[$name . 'y']);
		else
			$val = date('Y');
		$year = date('Y') - 20;
		for ($i = 0; $i < 23; ++$i)
		{
			$html .= '<option value="' . sprintf('%04d', $year + $i) . '"';
			if ($year + $i == $val)
				$html .= ' selected="selected"';
			$html .= '>' . ($year + $i) . '</option>';
		}
		$html .= '</select>';
		if (!$left)
		{
			$html .= '<input type="button" value="Cal" onclick="displayCalendarSelectBox(document.forms[0].'
					. $name . 'y,document.forms[0].' . $name . 'm,document.forms[0].' . $name . 'd,false,false,this)">';
		}
							
		return $html;
	}

//-----------------------------------------------------------------------------

	//function gen_dbFields($defOff, $defLim, $path = '../', $left = '&laquo;', $right = '&raquo;')
	function gen_dbFields($defOff, $defLim, $path = '../', $left = '&lt;', $right = '&gt;')
	{
		if (isset($_REQUEST['offset']))
			$offset = intval($_REQUEST['offset']);
		else
			$offset = intval($defOff);
		if (isset($_REQUEST['limit']))
			$limit = intval($_REQUEST['limit']);
		else
			$limit = intval($defLim);
		if ($offset == 0)
			$disabled = ' disabled="disabled"';
		else
			$disabled = '';
		echo 'Show&nbsp;<input type="text" name="limit" value="' . $limit . '" size="2" maxlength="8" />'
				. '&nbsp;from&nbsp;<input type="text" name="offset" value="' . $offset . '" size="2" maxlength="8" />'
				. '&nbsp;<input type="submit" name="action" value="' . $left. '"' . $disabled . ' />'
				. '<input type="submit" name="action" value="' . $right . '" />'
				. '<button type="submit" name="action" value="Refresh"><img src="' . $path . 'img/refresh.png" alt="Refresh" /></button>';
	}
	
//-----------------------------------------------------------------------------

	function gen_periodsSelect($name = 'period', $value = -1, $disabled = false,
			$prompt = '', $events = '', $extra = array())
	{
		global $Periods;

		// Make sure delivery types are available
		populate_periods();

		// Go to it
		$html = '<select name="' . $name . '"';
		if ($disabled)
			$html .= ' disabled="disabled"';
		$html .= $events . '>';
		if (count($extra) > 0)
		{
			foreach($extra as $val => $name)
			{
				$html .= '<option value="' . $val . '"';
				if ($value == $val)
					$html .= ' selected="selected"';
				$html .= '>' . htmlspecialchars($name) . '</option>';
			}
		}
		reset($Periods);
		foreach($Periods as $iid => $period)
		{
			$html .= '<option value="' . $iid . '"';
			if ($iid == $value)
				$html .= ' selected="selected"';
			$html .= '>' . htmlspecialchars($period['title']) . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

//-----------------------------------------------------------------------------

	function gen_routesSelect($name = 'rid', $selected = -1, $all = false, $prompt = '',
			$events = '')
	{
		global $Routes, $err;
		
		populate_routes();
		if ($err < ERR_SUCCESS)
			return '';
		
		// Generate select
		$html = '<select name="' . $name . '"';
		$html .= $events . '>';
		if ($all)
		{
			$html .= '<option value="0" ';
			if ($selected == 0)
				$html .= ' selected="selected"';
			$html .= '>All</option>';
		}
		foreach($Routes as $rid => $route)
		{
			$html .= '<option value="' . $rid . '"';
			if ($rid == $selected)
				$html .= ' selected="selected"';
			//$html .= '>' . $route['title'] . '</option>';
			$html .= '>' . $route . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
//-----------------------------------------------------------------------------

// DEPRECATED
	function gen_select($name, $values, $select = "", $disabled = false, $prompt = '', $events = '')
	{
		$html = '<select name="' . $name . '"';
		if ($disabled)
			$html .= ' disabled="disabled"';
		if (!empty($events))
			$html .= ' ' . $events;
		$html .= '>';
		while (list($key, $val) = each($values))
		{
			$html .= "<option value=\"" . $key . "\"";
			if ($key == $select)
				$html .= " selected=\"selected\"";
			$html .= ">" . htmlspecialchars($val) . "</option>";
		}
		$html .= "</select>";
		return $html;
	}

//-----------------------------------------------------------------------------

	function gen_telephone($name)
	{
		$T_AC = $name . 'AreaCode';
		$T_P = $name . 'Prefix';
		$T_N = $name . 'Number';
		$T_E = $name . 'Ext';
		$VAC = isset($_REQUEST[$T_AC]) ? $_REQUEST[$T_AC] : '';
		$VP = isset($_REQUEST[$T_P]) ? $_REQUEST[$T_P] : '';
		$VN = isset($_REQUEST[$T_N]) ? $_REQUEST[$T_N] : '';
		$VE = isset($_REQUEST[$T_E]) ? $_REQUEST[$T_E] : '';
		return '(&nbsp;<input type="text" name="' . $T_AC . '" value="' . $VAC . '" maxLength="3" size="3" />&nbsp;)'
				. '&nbsp;<input type="text" name="' . $T_P . '" value="' . $VP . '" maxLength="3" size="3" />'
				. '&nbsp;-&nbsp;<input type="text" name="' . $T_N . '" value="' . $VN . '" maxLength="4" size="4" />'
				. '&nbsp;ext.&nbsp;<input type="text" name="' . $T_E . '" value="' . $VE . '" maxLength="5" size="5" />';
	}
	
	function gen_telephoneTypeSelect($name, $selected, $extra = '')
	{
		static $TELETYPES = array
		(
			'Main',
			'Alternate',
			'Mobile',
			'Evening',
			'Day',
			'Office',
			'Message',
			'Pager',
			'Business',
			'Mobile (Office)',
			'Mobile (Business)',
			'Mobile (Day)',
			'Mobile (Evening)'
		);
		$html = '<select name="' . $name . '"' . $extra . '>';
		foreach($TELETYPES as $val)
		{
			$html .= '<option';
			if ($selected == $val)
				$html .= ' selected="selected"';
			$html .= '>' . htmlspecialchars($val) . '</option>';
		}
		return $html . '</select>';
	}

//-----------------------------------------------------------------------------

	function gen_typeSelect($name, $value = -1, $disabled = false, $prompt = '',
			$extra = array(), $events = '', $fs = true)
	{
		global $DeliveryTypes;

		// Make sure delivery types are available
		populate_types();

		// Get flag stop type if excluding it
		if (!$fs)
		{
			$fst = get_config('flag-stop-type');
			if ($fst == CFG_NONE)
				$fst = -1;
		}
		else
			$fst = -1;
		
		// Go to it
		$html = '<select name="' . $name . '"';
		if ($disabled)
			$html .= ' disabled="disabled"';
		if (!empty($events))
			$html .= ' '. $events;
		$html .= '>';
		if (count($extra) > 0)
		{
			foreach($extra as $val => $name)
			{
				$html .= '<option value="' . $val . '"';
				if ($value == $val)
					$html .= ' selected="selected"';
				$html .= '>' . htmlspecialchars($name) . '</option>';
			}
		}
		reset($DeliveryTypes);
		foreach($DeliveryTypes as $tid => $type)
		{
			if ($fst == $tid)
				continue;
			if ($type['visible'])
			{
				$html .= '<option value="' . $tid . '"';
				if ($tid == $value)
					$html .= ' selected="selected"';
				$html .= '>' . htmlspecialchars($type['abbr']) . '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}

?>
