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
function smarty_function_html_name($params, &$smarty)
{
	require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

	$abbr = array();
	$abbr[NAME_T_NONE] = '';
	$abbr[NAME_T_MR] = 'Mr';
	$abbr[NAME_T_MRS] = 'Mrs';
	$abbr[NAME_T_MS] = 'Ms';
	$abbr[NAME_T_MISS] = 'Miss';

	$name = null;
	$extra = '';
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
        case 'name':
            $$_key = (string)$_val;
            break;
        
        default:
            if (!is_array($_val))
                $extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
            else
                $smarty->trigger_error('html_name: extra attribute \'' . $_key . '\' cannot be an array', E_USER_NOTICE);
            break;
		}
	}

    if (is_null($name))
        $smarty->trigger_error('html_name: name is required'. E_USER_WARNING);

    $TITLE = $name . '_title';
    $FIRST = $name . '_first';
    $LAST = $name . '_last';
    $SURNAME = $name . '_surname';

	$title = isset($_POST[$TITLE]) ? $_POST[$TITLE] : '';
	$first = isset($_POST[$FIRST]) ? $_POST[$FIRST] : '';
	$last = isset($_POST[$LAST]) ? $_POST[$LAST] : '';
	$surname = isset($_POST[$SURNAME]) ? $_POST[$SURNAME] : '';
	
	$html = '<select class="w3-select w3-border control" name="' . $TITLE . '" id="' . $TITLE . '" ' . $extra . '>';
	reset($abbr);
	foreach ($abbr as $_key => $_val)
	{
		$html .= '<option label="' . $_val . '" value="' . $_key . '"';
		if ($_key == $title)
			$html .= ' selected="selected"';
		$html .= '>' . $_val . '</option>';
    }

	$html .= '</select>'
			. '<input class="w3-input w3-border control" type="text" name="' . $FIRST . '" id="' . $FIRST . '" value="'
					. smarty_function_escape_special_chars($first) . '" maxlength="30" size="15" />'
			. '<input class="w3-input w3-border control" type="text" name="' . $LAST . '" id="' . $LAST . '" value="'
					. smarty_function_escape_special_chars($last) . '" maxlength="30" size="15" />'
			. '<input class="w3-input w3-border control" type="text" name="' . $SURNAME . '" id="' . $SURNAME . '" value="'
					. smarty_function_escape_special_chars($surname) . '" maxlength="10" size="5" />';
	
	return $html;
}
?>
