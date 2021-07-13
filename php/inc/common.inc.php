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

	require_once 'inc/constants.inc.php';
	require_once 'inc/database.inc.php';
	require_once 'inc/errors.inc.php';
	require_once 'inc/lookup.inc.php';
	require_once 'inc/periods.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/regex.inc.php';
	require_once 'inc/security.inc.php';
    require_once 'smarty/smarty.inc.php';

//-----------------------------------------------------------------------------
// Given an array of names, will generate constants of the names where the
// values are consecutive starting at 0

	function enum()
	{
		$args = func_get_args();
		if (is_array($args))
		{
			$i = 0;
			foreach ($args as $const)
				define($const, ++$i);
		}
	}

//-----------------------------------------------------------------------------
// Return the passed in number formatted in dollars, and red if negative.

	function currency($val, $color = true)
	{
		if ($color)
		{
			if ($val < 0)
				return '$<span class="w3-red">' . sprintf('%01.2f', $val) . '</span>';
			else
				return sprintf('$%01.2f', $val);
		}
		else
				return sprintf('$%01.2f', $val);
	}

//-----------------------------------------------------------------------------
// Checks the string, assumed to already be in dollar format, to see if it
// needs to be red.

	function currency_text($val, $neg = false)
	{
		if ($neg || preg_match('/^[ ]*\$[ ]*-[0-9]+\.[0-9]{2}[ ]*$/', $val))
			return '<span class="w3-red">' . $val . '</span>';
		else
			return $val;
	}

//-----------------------------------------------------------------------------
// Count the days between $start and $end

	function days_between_dates($start, $end)
	{
		// Swap the two values if end is greater than start (to avoid the loop
		// of death).
		if ($start > $end)
		{
			$t = $start;
			$start = $end;
			$end = $t;
		}

		// Increment the start time by one day until it is equal or greater
		// than the end time
		$days = 0;
		while ($start <= $end)
		{
			$start = strtotime('+1 days', $start);
			++$days;
		}

		return $days;
	}

//-----------------------------------------------------------------------------
// Generates message for any kind of fatal error.

	function fatal_error($title, $msg)
	{
		return gen_htmlHeader($title) . gen_header($title) . '<hr />'
				. '<div>'
				. htmlspecialchars($msg) . '</div>' . '<hr />' . gen_htmlFooter();
	}

//-----------------------------------------------------------------------------

	function gen_header($loc = 'Home')
	{
        global $smarty, $username;

        $title = get_config('default-title', 'NewsLedger');

        $smarty->assign('path', $loc);
        $smarty->assign('title', $title);
        $smarty->assign('username', $username);

        return $smarty->fetch('header.tpl');
	}

//-----------------------------------------------------------------------------
// Generate the final part of the html file

	function gen_htmlFooter($script='',  $scripts='')
	{
        global $smarty;

        $smarty->assign('script', $script);     // href style scripts
        $smarty->assign('scripts', $scripts);   // inline scripts

        return $smarty->fetch('html_footer.tpl');
	}

//-----------------------------------------------------------------------------
// Generate the initial part of the html file

	function gen_htmlHeader($subTitle, $styles = '', $scripts = '', $style = Null, $script = Null)
	{
        global $smarty;

		$title = get_config('default-title', 'NewsLedger');

        $smarty->assign('title', "$title - $subTitle");
        $smarty->assign('script', $script);         // <script href=""> type scripts
        $smarty->assign('scripts', $scripts);       // <script>...</script> type scripts
        $smarty->assign('style', $style);           // link type styles
		$smarty->assign('styles', $styles);         // <style></style> type styles

		return $smarty->fetch('html_header.tpl');
	}

//-----------------------------------------------------------------------------
// Makes sure that the $Periods global has been filled with contents of the
// periods table, possibly limited to a specified range.
//
// $first = If provided, the first period to fill the global with
// $last  = If provided, the last period to fill the global with
//
// Either parameter can be 0, effectively disabling it.  For example, if
// $first is 0 and $last is 6, then all periods through 6 will be put in
// the global.

	function populate_periods()
	{
		global $Periods;

		if (empty($Periods))
			$Periods = gen_periodsArray();
	}

	function populate_periods_valid($page)
	{
		global $err, $errText;
		populate_periods();
		if ($err < ERR_SUCCESS)
		{
			echo fatal_error($page, $errText);
			exit();
		}
	}

	function gen_periodsArray($first = 0, $last = 0)
	{
		global $err, $errCode, $errContext, $errQuery, $errText;

		$errObj = new error_stack();

		$periods = array();
		$query = "SELECT * FROM `periods` ORDER BY `changes_start`";
		$records = db_query($query);
		if ($records)
		{
			while ($record = $records->fetch_object())
			{
				$periods[$record->id] = array
						(
							P_PERIOD => $record->id,
							P_TITLE => $record->title,
							P_START => strtotime($record->changes_start),
							P_END => strtotime($record->changes_end),
							P_DSTART => strtotime($record->display_start),
							P_DEND => strtotime($record->display_end),
							P_BILL => strtotime($record->bill),
							P_DUE => strtotime($record->due)
						);
			}
			$err = ERR_SUCCESS;
		}
		else
		{
			$err = ERR_DB_QUERY;
			$errContext = "generating periods array";
			$errQuery = $query;
		}

		return $periods;
	}

//-----------------------------------------------------------------------------
// Return array of route ids associated to route titles.

	function populate_routes($all = false)
	{
		global $Routes;

		if (empty($Routes))
			$Routes = gen_routesArray();
	}

	function populate_routes_valid($page)
	{
		global $err, $errText;
		populate_routes();
		if ($err < ERR_SUCCESS)
		{
			echo fatal_error($page, $errText);
			exit();
		}
	}

	function gen_routesArray($all = false)
	{
		global $err, $errCode, $errContext, $errQuery, $errText;

		$errObj = new error_stack();

		// Get routes from database
		$query = "SELECT `id`, `title`, `active` FROM `routes` ORDER BY `title` ASC";
		$result = db_query($query);

		// Build array
		$routes = array();
		if ($result)
		{
			while ($route = $result->fetch_object())
			{
				if ($all || $route->active == 'Y')
					$routes[$route->id] = $route->title;
			}
			$err = ERR_SUCCESS;
		}
		return $routes;
	}

//-----------------------------------------------------------------------------
// Return array of all routes associated with array of all route info.

	function gen_routesArrayFull($all = false)
	{
		global $err, $errCode, $errContext, $errQuery, $errText;

		$errObj = new error_stack();

		// Get routes from database
		$query = "SELECT `id`, `title`, `active` FROM `routes` ORDER BY `title` ASC";
		$result = db_query($query);
		$routes = array();
		if ($result)
		{
			// Build array
			while ($route = $result->fetch_object())
			{
				if ($all || $route->active == 'Y')
					$routes[$route->id] = $route;
			}
			$err = ERR_SUCCESS;
		}
		return $routes;
	}

//-----------------------------------------------------------------------------

	function gen_menu_secure(&$menus, $active, $level = 1)
	{
		$html = '<div>';
		$html .= '<div><ul>';
		foreach($menus as $id => $menu)
		{
			if (!isset($menu[MI_PAGE]))
				$allowed = true;
			else
				$allowed = allowed('page', $menu[MI_PAGE]);
			if ($allowed)
			{
				if ($id == $active)
					$html .= '<li><span>' . $menu[MI_NAME] . '</span></li>';
				else
					$html .= '<li><a href="' . htmlentities($menu[MI_URL]) . '"><span>' . $menu[MI_NAME] . '</span></a></li>';
			}
		}
		$html .= '</ul></div></div>';
		echo $html;
	}

//-----------------------------------------------------------------------------

	function populate_types()
	{
		global $DeliveryTypes;

		if (empty($DeliveryTypes))
			$DeliveryTypes = gen_typesArray();
	}

	function populate_types_valid($page)
	{
		global $err, $errText;
		populate_types();
		if ($err < ERR_SUCCESS)
		{
			echo fatal_error($page, $errText);
			exit();
		}
	}

	function gen_typesArray($period = 0)
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $Period;

		$errObj = new error_stack();

		// Make sure we know the period to get data for
		if ($period == 0 && isset($Period))
			$period = $Period[P_PERIOD];
		if (empty($period))
		{
			$period = get_config('billing-period', 0);
		}

		// Get list of delivery types
		$query = "SELECT * FROM `customers_types`";
		$records = db_query($query);
		if (!$records)
			return array();

		// Build array of delivery types
		$types = array();
		while ($type = $records->fetch_object())
		{
			// Lookup the rates for this type
			$query = "SELECT * FROM `customers_rates` WHERE `type_id` = " . $type->id
					. " AND `period_id_begin` <= " . $period
					. " AND (`period_id_end` >= " . $period . " OR `period_id_end` <=> NULL)";
			$result = db_query($query);
			if (!$result)
				return array();

			// Add the delivery type into the array
			$types[$type->id] = array
					(
						'abbr' => $type->abbr,
						'name' => $type->name,
						'color' => intval($type->color),
						'visible' => (($type->visible == 'Y') ? true : false),
						'newChange' => (($type->newChange == 'Y') ? true : false),
						'watchStart' => (($type->watchStart == 'Y') ? true : false),
						'Sun' => array('paper' => ($type->su == 'Y' ? true : false)),
						'Mon' => array('paper' => ($type->mo == 'Y' ? true : false)),
						'Tue' => array('paper' => ($type->tu == 'Y' ? true : false)),
						'Wed' => array('paper' => ($type->we == 'Y' ? true : false)),
						'Thu' => array('paper' => ($type->th == 'Y' ? true : false)),
						'Fri' => array('paper' => ($type->fr == 'Y' ? true : false)),
						'Sat' => array('paper' => ($type->sa == 'Y' ? true : false))
					);

			// Add in the rate to the array
			if ($result->num_rows == 0)
			{
				// No rate, so make it 0
				$types[$type->id]['rate'] = 0;
				$types[$type->id]['daily_credit'] = 0;
				$types[$type->id]['sunday_credit'] = 0;
			}
			else
			{
				// Add the current rate
				$rates = $result->fetch_object();
				$types[$type->id]['rate'] = doubleval($rates->rate);
				$types[$type->id]['daily_credit'] = doubleval($rates->daily_credit);
				$types[$type->id]['sunday_credit'] = doubleval($rates->sunday_credit);
			}

		}
		$err = ERR_SUCCESS;
		return $types;
	}

//-----------------------------------------------------------------------------
// Given a period id (iid), return the title for it

	function iid2title($iid, $id = false)
	{
		global $Period, $Periods;
		global $err, $errCode, $errContext, $errQuery, $errText;

		$text = '';

		// If its null or 0, there is nothing to lookup
		if (empty($iid))
			return '&nbsp;';

		do
		{
			// Get it from global if possible
			if (isset($Periods))
			{
				if (isset($Periods[$iid]))
				{
					$text = $Periods[$iid][P_TITLE];
					break;
				}
			}

			// Otherwise check current period global
			if (isset($Period))
			{
				if ($Period[P_PERIOD] == $iid)
				{
					$text = $Period[P_TITLE];
					break;
				}
				if ($Period[PP_PERIOD] == $iid)
				{
					$text = $Period[PP_TITLE];
					break;
				}
				if ($Period[PN_PERIOD] == $iid)
				{
					$text = $Period[PN_TITLE];
					break;
				}
			}

			// Get it from database if all else fails
			$errObj = new error_stack;
			$query = 'SELECT `title` FROM `periods` WHERE `id` = ' . $iid;
			$text = db_query_result($query);
			if (!$text)
				return false;
		} while (false);
		if ($id)
			$text .= sprintf(' (id = %04d)', $iid);
		return $text;
	}

//-----------------------------------------------------------------------------
// Given a period id (iid), return the title for it.  Difference from previous
// is that this works for 0 or NULL periods as well.

	function iid2titleEx($iid, $id = false)
    {
        if (is_null($iid) || $iid === 0 || $iid === '0')
            return '<span>pending</span>';
        return iid2title($iid, $id);
    }
//-----------------------------------------------------------------------------
// Generates message for invalid parameters.

	function invalid_parameters($title, $path)
	{
		return gen_htmlHeader($title)
				. gen_header($title)
				. '<hr />'
				. '<div>'
				. $path
				. ': Called with invalid, incorrect, or missing parameters!</div>'
				. '<hr />'
				. gen_htmlFooter();
	}

//-----------------------------------------------------------------------------

	function nameseq_name($seq)
	{
		switch ($seq)
		{
		case NAME_C_DELIVERY1:	return 'Delivery';
		case NAME_C_DELIVERY2:	return 'Alternate Delivery';
		case NAME_C_BILLING1:		return 'Billing';
		case NAME_C_BILLING2:		return 'Alternate Billing';
		default:
			error_log('Unknown name sequence in nameseq_name: ' . $seq);
			return 'UNKNOWN NAME';
		}
	}

//-----------------------------------------------------------------------------

	function num_digits($val)
	{
		$count = 0;
		while ($val > 0)
		{
			++$count;
			$val = intval($val /= 10);
		}
		return $count;
	}

//-----------------------------------------------------------------------------
// Returns the "features" parameter of the JavaScript window.open call,
// minus the width, height portions.

	function popup_features($scrollbars = NULL, $resizable = NULL)
	{
		$features = 'location=no,directories=no,menubar=';

		$temp = get_config('debug-popup-menubar', 'false');
		if ($temp == 'true')
			$features .= 'yes';
		else
			$features .= 'no';

		$features .= ',resizable=';
		if (!empty($resizable))
		{
			if ($resizable)
				$features .= 'yes';
			else
				$features .= 'no';
		}
		else
		{
			$temp = get_config('debug-popup-resizable', 'false');
			if ($temp == 'true')
				$features .= 'yes';
			else
				$features .= 'no';
		}

		$features .= ',scrollbars=';
		if (!empty($scrollbars))
		{
			if ($scrollbars)
				$features .= 'yes';
			else
				$features .= 'no';
		}
		else
		{
			$temp = get_config('debug-popup-scrollbars', 'false');
			if ($temp == 'true')
				$features .= 'yes';
			else
				$features .= 'no';
		}

		$features .= ',status=';
		$temp = get_config('debug-popup-status', 'false');
		if ($temp == 'true')
			$features .= 'yes';
		else
			$features .= 'no';

		$temp = get_config('debug-popup-toolbar', 'false');
		$features .= ',toolbar=';
		if ($temp == 'true')
			$features .= 'yes';
		else
			$features .= 'no';
		return $features;
	}

//-----------------------------------------------------------------------------
// Given a route id (rid), return the title for it

	function rid2title($rid, $id = false)
	{
		global $Routes;
		global $err, $errCode, $errContext, $errQuery, $errText;

		// If its null or 0, there is nothing to lookup
		if (empty($rid))
			return '&nbsp;';

		// If global not set, try setting it
		if (!isset($Routes))
			populate_routes();

		// Get it from global if possible
		$text = '';
		if (isset($Routes))
		{
			if (isset($Routes[$rid]))
				$text = $Routes[$rid];
		}

		// Get it from database if all else fails
		if (empty($text))
		{
			$errObj = new error_stack;
			$text = db_query_result('SELECT `title` FROM `routes` WHERE `id` = ' . $rid);
			if (!$text)
				return false;
		}
		if ($id)
			$text .= sprintf(' (id = %04d)', $rid);
		return $text;
	}

//-----------------------------------------------------------------------------
// Given a date, returns the number of seconds in that day.  All silly time
// and date stuff is handled correctly (i.e. if it happens to be the day of
// daylight savings change, then it will add or subtract the correct number
// of seconds)

	function seconds_in_day($date)
	{
		$actual = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		return strtotime('+1 days', $actual) - $actual;
	}

//-----------------------------------------------------------------------------
// Given a customer status numerical value, returns the text title for it.

	function status2title($status)
	{
		switch (intval($status))
		{
		case CUSTOMER_CURRENT:		return 'CURRENT';
		case CUSTOMER_LATE:			return 'LATE';
		case CUSTOMER_VERYLATE:		return 'VERY LATE';
		case CUSTOMER_STOPPED:		return 'STOPPED';
		case CUSTOMER_PREPAID:		return 'PREPAID';
		case CUSTOMER_ONVACATION:	return 'VACATION';
		case CUSTOMER_DELETED:		return 'DELETED';
		default:					return '??? ERROR ???';
		}
	}

//-----------------------------------------------------------------------------

	function telseq_name($seq)
	{
		switch ($seq)
		{
		case TEL_C_DELIVERY1:	return 'Delivery Telephone 1';
		case TEL_C_DELIVERY2:	return 'Delivery Telephone 2';
		case TEL_C_DELIVERY3:	return 'Delivery Telephone 3';
		case TEL_C_BILLING1:	return 'Billing Telephone 1';
		case TEL_C_BILLING2:	return 'Billing Telephone 2';
		case TEL_C_BILLING3:	return 'Billing Telephone 3';
		default:
			error_log('Unknown telephone sequence in telseq_name: ' . $seq);
			return 'UNKNOWN';
		}
	}

//-----------------------------------------------------------------------------
// Given a delivery type id (tid), return the abbreviation for it

	function tid2abbr($type_id, $id = false)
	{
		global $DeliveryTypes;
		global $err, $errCode, $errContext, $errQuery, $errText;

		// If its null or 0, there is nothing to lookup
		if (empty($type_id))
			return '&nbsp;';

		// If global not set, try setting it
		if (!isset($DeliveryTypes))
			populate_types();

		// Get it from global if possible
		$text = '';
		if (isset($DeliveryTypes))
		{
			if (isset($DeliveryTypes[$type_id]))
				$text = $DeliveryTypes[$type_id]['abbr'];
		}

		// Get it from database if all else fails
		if (empty($text))
		{
			$errObj = new error_stack;
			$query = 'SELECT `abbr` FROM `customers_types` WHERE `id` = ' . $type_id
					. " LIMIT 1";
			$text = db_query_result($query);
			if (!$result)
				return false;
		}
		if ($id)
			$text .= sprintf(' (id = %04d)', $type_id);
		return $text;
	}

//-----------------------------------------------------------------------------
// Given a route title, return the route id for it

	function title2rid($title)
	{
		global $Routes;
		global $err, $errCode, $errContext, $errQuery, $errText;

		// If its null or 0, there is nothing to lookup
		if (empty($title))
			return false;

		// If global not set, try setting it
		if (!isset($Routes))
			populate_routes();

		// Get it from global if possible
		$text = '';
		if (isset($Routes))
		{
			foreach($Routes as $id => $data)
				if ($data == $title)
					return $id;
		}

		// Get it from database if all else fails
		$errObj = new error_stack;
		$text = db_query_result('SELECT `id` FROM `routes` WHERE `title` = \'' . $title . '\'');
		if (!$text)
			return false;
		return $text;
	}

//-----------------------------------------------------------------------------
// Validates that the form variables $base . "m", $base . "d", $base . "y"
// contain a valid date.
// $base = Base name of form variables; $base = "foo", then month = "foom",
//         day = "food", year = "fooy"
// $name = Name of the field, for generated validation error messages
// Returns the date in string format on exit.
// Sets the error variables on exit.

	function valid_dateNew($prefix)
	{
		$month = $_POST[$prefix . 'm'];
		if (!preg_match('/^[0-9]+$/', $month))
			return false;
		$month = intval($month);

		$day = $_POST[$prefix . 'd'];
		if (!preg_match('/^[0-9]+$/', $day))
			return false;
		$day = intval($day);

		$year = $_POST[$prefix . 'y'];
		if (!preg_match('/^[0-9]+$/', $year))
			return false;
		$year = intval($year);

		if (!checkdate($month, $day, $year))
			return false;

		return strtotime(sprintf('%02d/%02d/%04d', $month, $day, $year));
	}

	function valid_date($base, $name, $required = true)
	{
		global $err, $errCode, $errContext, $errText, $errQuery;

		$errObj = new error_stack();

		// Figure the names of the fields
		$index = array
		(
			'm' => $base . 'm',
			'd' => $base . 'd',
			'y' => $base . 'y'
		);

		// If not required, only validate if one of the fields have been specified
		if (!$required)
		{
			if (empty($_REQUEST[$index['m']]) && empty($_REQUEST[$index['d']])
					&& empty($_REQUEST[$index['y']]))
			{
				$err = ERR_SUCCESS;
				return '';
			}
		}

		// Validate day
		if (!preg_match('/^[ ]*([0]?[1-9]|[1-2][0-9]|3[0-1])[ ]*$/', $_REQUEST[$index['d']]))
		{
			$err = ERR_INVALID;
			$errText = 'Invalid Day from ' . $name;
			return '';
		}

		// Validate month
		if (!preg_match('/^[ ]*([0]?[1-9]|1[012])[ ]*$/', $_REQUEST[$index['m']]))
		{
			$err = ERR_INVALID;
			$errText = 'Invalid Month from ' . $name;
			return '';
		}

		// Validate year
		if (!preg_match('/^[ ]*((19|20)[[:digit:]]{2})[ ]*$/', $_REQUEST[$index['y']]))
		{
			$err = ERR_INVALID;
			$errText = 'Invalid Year from ' . $name;
			return '';
		}

		// Make sure they specified a valid date
		if (!checkdate(intval($_REQUEST[$index['m']]), intval($_REQUEST[$index['d']]),
				intval($_REQUEST[$index['y']])))
		{
			$err = ERR_INVALID;
			$errText = ucfirst($name) . ' is invalid';
			return '';
		}

		// Build the date text
		$err = ERR_SUCCESS;
		return sprintf('%02d/%02d/%04d', intval($_REQUEST[$index['m']]), intval($_REQUEST[$index['d']]),
				intval($_REQUEST[$index['y']]));
	}

//-----------------------------------------------------------------------------

	function valid_name($first, $last)
	{
		return $first . (empty($last) ? '' : ' ' . $last);
	}

//-----------------------------------------------------------------------------

	function valid_telephone($base, $name, $required = false)
	{
		global $err, $errCode, $errContext, $errText, $errQuery;

		$errObj = new error_stack();

		$T_AC = $base . 'AreaCode';
		$T_P = $base . 'Prefix';
		$T_N = $base . 'Number';
		$T_E = $base . 'Ext';

		$didExt = false;
		$valid = 0;

		// Validate area code
		if (!empty($_POST[$T_AC]))
		{
			if (!preg_match('/^[[:digit:]]{3}$/', $_REQUEST[$T_AC]))
			{
				$err = ERR_INVALID;
				$errText = '<i>Area Code</i> from <i>' . $name . '</i> is invalid';
				return '';
			}
			++$valid;
		}

		// Validate prefix if provided
		if (!empty($_POST[$T_P]))
		{
			if (!preg_match('/^[[:digit:]]{3}$/', $_REQUEST[$T_P]))
			{
				$err = ERR_INVALID;
				$errText = '<i>Prefix</i> from <i>' . $name . '</i> is invalid';
				return '';
			}
			++$valid;
		}

		// Validate number if provided
		if (!empty($_POST[$T_N]))
		{
			if (!preg_match('/^[[:digit:]]{4}$/', $_REQUEST[$T_N]))
			{
				$err = ERR_INVALID;
				$errText = "<i>Extension</i> from <i>" . $name . "</i> is invalid";
				return '';
			}
			++$valid;
		}

		// Validate extension if provided
		if (!empty($_POST[$T_E]))
		{
			if (!preg_match('/^[0-9]{1,5}$/', $_REQUEST[$T_E]))
			{
				$err = ERR_INVALID;
				$errText = '<i>Local Extension</i> from <i>' . $name . '</i> is invalid';
				return '';
			}
			$didExt = true;
		}

		// Did they specify a valid required telephone number?
		if (($required && $valid < 3)
				|| (!$required && ($valid > 0 && $valid < 3)))
		{
			$err = ERR_INVALID;
			$errText = '<i>' . ucfirst($name) . '</i> is invalid';
			return '';
		}

		// Build the valid telephone number text if it exists
		$text = '';
		if ($valid > 0)
		{
			$text = '(' . $_REQUEST[$T_AC] . ') ' . $_REQUEST[$T_P] . '-' . $_REQUEST[$T_N];
			if ($didExt)
				$text .= ' ext ' . $_REQUEST[$T_E];
		}
		$err = ERR_SUCCESS;
		return $text;
	}

	function valid_telephoneNew($base)
	{
		$area_code = $_POST[$base . 'ac'];
		if (!preg_match('/^[[:digit:]]{3}$/', $area_code))
			return false;

		$prefix = $_POST[$base . 'p'];
		if (!preg_match('/^[[:digit:]]{3}$/', $prefix))
			return false;

		$number = $_POST[$base . 'n'];
		if (!preg_match('/^[[:digit:]]{4}$/', $number))
			return false;

		$ext = $_POST[$base . 'x'];
		if (preg_match('/^[[:alnum:]]{0,10}$/', $ext) && strlen($ext) > 0)
			return '(' . $area_code . ') ' . $prefix . '-' . $number . ' Ext ' . $ext;
		else
			return '(' . $area_code . ') ' . $prefix . '-' . $number;
	}

//-----------------------------------------------------------------------------
// Validates that the form variables $base . "h", $base . "m", $base . "s"
// contain a valid 24 hour time.
// $base = Base name of form variables; $base = "foo", then hour = "fooh",
//         minutes = "foom", seconds = "foos"
// $name = Name of the field, for generated validation error messages
// Returns the time on exit as a string on exit.
// Sets the error variables on exit.

	function valid_time($base, $name)
	{
		global $err, $errCode, $errContext, $errText, $errQuery;

		$errObj = new error_stack();

		// Make sure they specified an hour
		$hour = $_REQUEST[$base . 'h'];
		if (empty($hour) && $hour != '0')
		{
			$err = ERR_INVALID;
			$errText = "Missing <i>hour</i> from <i>" . $name . "</i>";
			return '';
		}

		// Does hour appear valid?
		if (!is_numeric($hour))
		{
			$err = ERR_INVALID;
			$errText = "<i>Hour</i> from <i>" . $name . "</i> is invalid";
			return '';
		}
		if (intval($hour) < 0 || intval($hour) > 23)
		{
			$err = ERR_INVALID;
			$errText = "<i>Hour</i> from <i>" . $name . "</i> is invalid";
			return '';
		}

		// Make sure they specified minutes
		$minutes = $_REQUEST[$base . 'm'];
		if (empty($minutes) && $minutes != '0')
		{
			$err = ERR_INVALID;
			$errText = "Missing <i>minutes</i> from <i>" . $name . "</i>";
			return '';
		}

		// Does minutes appear valid?
		if (!is_numeric($minutes))
		{
			$err = ERR_INVALID;
			$errText = "<i>Minutes</i> from <i>" . $name . "</i> is invalid";
			return '';
		}
		if (intval($minutes) < 0 || intval($minutes) > 59)
		{
			$err = ERR_INVALID;
			$errText = "<i>Minutes</i> from <i>" . $name . "</i> is invalid";
			return '';
		}

		// Make sure they specified seconds
		$seconds = $_REQUEST[$base . 's'];
		if (empty($seconds) && $seconds != '0')
		{
			$err = ERR_INVALID;
			$errText = "Missing <i>seconds</i> from <i>" . $name . "</i>";
			return '';
		}

		// Does seconds appear valid?
		if (!is_numeric($seconds))
		{
			$err = ERR_INVALID;
			$errText = "<i>Seconds</i> from <i>" . $name . "</i> is invalid";
			return '';
		}
		if (intval($seconds) < 0 || intval($seconds) > 59)
		{
			$err = ERR_INVALID;
			$errText = "<i>Seconds</i> from <i>" . $name . "</i> is invalid";
			return '';
		}

		// Build the valid time text
		$err = ERR_SUCCESS;
		return sprintf('%02d:%02d:%02d', $hour, $minutes, $seconds);
	}

//-----------------------------------------------------------------------------
// Forces $value to be valid html, and if its an empty string forces it to
// be a non-breakable space so that the client has to show something (for
// example, when used for tables, the frame of the cell won't show if the
// cell contains no data:  With a nbsp; it will).

	function valid_text($value)
	{
		$temp = htmlspecialchars($value);
		if (empty($temp))
			$temp = '&nbsp;';
		return $temp;
	}
?>
