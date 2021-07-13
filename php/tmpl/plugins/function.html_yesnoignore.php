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
function smarty_function_html_yesnoignore($params, &$smarty)
{
	require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

	$name = NULL;
	$selected = NULL;
	$extra = '';
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
			case 'name':
			case 'selected':
				$$_key = (string)$_val;
				break;
			
			default:
				if (!is_array($_val))
					$extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
				else
					$smarty->trigger_error("html_routes: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				break;
		}
	}

	$html = '<option label="Ignore" value="2"';
	if ($selected == 2)
		$html .= ' selected="selected"';
	$html .= '>Ignore</option>';
	$html .= '<option label="Yes" value="1"';
	if ($selected == 1)
		$html .= ' selected="selected"';
	$html .= '>Yes</option>';
	$html .= '<option label="No" value="0"';
	if ($selected == 0)
		$html .= ' selected="selected"';
	$html .= '>No</option>';
	if(!empty($name))
    {
        $html = '<select class="w3-select w3-border control" name="' . $name . '" id="' .$name . '"'
              . $extra . '>' . "\n" . $html . '</select>' . "\n";
    }

	return $html;
}
?>
