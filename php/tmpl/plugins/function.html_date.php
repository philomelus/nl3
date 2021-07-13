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
function smarty_function_html_date($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    require_once $smarty->_get_plugin_filepath('function', 'html_options');
	
	$prefix			= 'Date_';
	$left			= false;
    $extra_attrs	= '';

    foreach ($params as $_key=>$_value)
	{
        switch ($_key)
		{
		case 'prefix':
		case 'left':
			$$_key = (string)$_value;
			break;

		default:
			if (!is_array($_value))
				$extra_attrs .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_value) . '"';
			else
				$smarty->trigger_error('html_date: extra attribute \'' . $_key . '\' cannot be an array', E_USER_NOTICE);
			break;
        }
    }

	$html = '';

	// Month field
	$month_names = array();
	$month_values = array();
	for ($i = 1; $i <= 12; ++$i)
	{
		$month_names[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		$month_values[$i] = strftime('%m', mktime(0, 0, 0, $i, 1, 2000));
	}
	if (isset($_POST[$prefix . 'm']) && preg_match('/^[0-9]{1,2}$/', $_POST[$prefix . 'm']))
		$selected = strftime('%m', mktime(0, 0, 0, intval($_POST[$prefix . 'm']), 1, 2000));
	else
		$selected = date('m');
	$html .= '<select class="w3-select w3-border control" name="' . $prefix . 'm" id="' . $prefix . 'm"' . $extra_attrs . '>'
			. smarty_function_html_options(array
					(
						'output'     => $month_names,
						'values'     => $month_values,
						'selected'   => $selected,
						'print_result' => false
					), $smarty)
			. '</select>';

    // Day field
	$days = array();
	$day_values = array();
	for ($i = 1; $i <= 31; ++$i)
	{
		$days[] = sprintf('%02d', $i);
		$day_values[] = sprintf('%d', $i);
	}
	if (isset($_POST[$prefix . 'd']) && preg_match('/^[0-9]{1,2}$/', $_POST[$prefix . 'd']))
		$selected = intval($_POST[$prefix . 'd']);
	else
		$selected = date('d');
	$html .= '<select class="w3-select w3-border control" name="' . $prefix . 'd" id="' . $prefix . 'd"' . $extra_attrs . '>'
			. smarty_function_html_options(array
					(
						'output' => $days,
						'values'     => $day_values,
						'selected'   => $selected,
						'print_result' => false
					), $smarty)
			. '</select>';

	// Year field
	$year = date('Y');
	$years = range($year - 2, $year + 2);
	$yearvals = $years;
	if (isset($_POST[$prefix . 'y']) && preg_match('/^[0-9]{4}$/', $_POST[$prefix . 'y']))
		$selected = intval($_POST[$prefix . 'y']);
	else
		$selected = $year;
	$html .= '<select class="w3-select w3-border control" name="' . $prefix . 'y" id="' . $prefix . 'y"' . $extra_attrs . '>'	
			. smarty_function_html_options(array
					(
						'output' => $years,
						'values' => $yearvals,
						'selected'   => $selected,
						'print_result' => false
					), $smarty)
			. '</select>';

    return $html;
}

?>
