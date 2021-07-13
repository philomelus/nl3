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
function smarty_function_html_select($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $name = null;
    $values = array();
	$selected = null;
    $disabled = false;
    $prompt = '';
    $events = '';
	$extra = '';
	foreach($params as $_key => $_val)
	{
		switch($_key)
        {
        case 'events':
        case 'name':
        case 'selected':
        case 'prompt':
            $$_key = (string)$_val;
            break;

        case 'disabled':
            $$_key = (bool)$_val;
            break;

        case 'values':
            if (!is_array($_val))
                $smarty->trigger_error('html_select: values must be an array', E_USER_ERROR);
            $$_key = $_val;
            break;

        default:
            if (!is_array($_val))
                $extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
            else
                $smarty->trigger_error("html_select: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
            break;
		}
	}
	
    $html = '<select class="w3-select w3-border control" name="' . $name . '" id="' . $name . '"';
    if ($disabled)
        $html .= ' disabled';
    if (!empty($events))
        $html .= ' ' . $events;
    $html .= '>';
    foreach($values as $key => $val)
    {
        $html .= '<option value="' . smarty_function_escape_special_chars($key) . '"';
        if ($key == $selected)
            $html .= ' selected="selected"';
        $html .= '>' . smarty_function_escape_special_chars($val) . '</option>';
    }
    $html .= '</select>';
    return $html;
}
?>
