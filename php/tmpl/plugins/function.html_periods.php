<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009 Russell E. Gibson

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
function smarty_function_html_periods($params, &$smarty)
{
	global $Periods;

	require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

	$name = null;
	$selected = null;
	$extra = '';
    $any = false;
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
			case 'name':
            case 'selected':
            case 'any':
				$$_key = (string)$_val;
				break;

			default:
				if (!is_array($_val))
					$extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
				else
					$smarty->trigger_error("html_periods: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				break;
		}
	}

    $_html_result = '';
    if ($any)
    {
        $_html_result = '<option value="0"';
        if ($selected == 0)
            $_html_result .= ' selected="selected"';
        $_html_result .= '>Any</option>';
    }
	if (empty($Periods))
		populate_periods();	// TODO:  Ignoring errors here
	reset($Periods);
	foreach ($Periods as $_key => $_val)
	{
		$_html_result .= '<option label="' . smarty_function_escape_special_chars($_val['title']) . '" value="' .
				smarty_function_escape_special_chars($_key) . '"';
		if ($_key == $selected)
			$_html_result .= ' selected="selected"';
		$_html_result .= '>' . smarty_function_escape_special_chars($_val['title']) . '</option>' . "\n";
	}
	if(!empty($name)) {
		$_html_result = '<select name="' . $name . '"' . $extra . '>' . "\n" . $_html_result . '</select>' . "\n";
	}

	return $_html_result;
}
?>
