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
function smarty_function_html_telephone($params, &$smarty)
{
	require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    require_once $smarty->_get_plugin_filepath('function', 'html_options');

	$prefix = 'tele';
	$type = null;
	$extra = '';
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
			case 'prefix':
			case 'type':
				$$_key = (string)$_val;
				break;
			
			default:
				if (!is_array($_val))
					$extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
				else
					$smarty->trigger_error("html_telephone: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				break;
		}
	}

	if (empty($type))
	{
		if (isset($_POST[$prefix . 't']))
			$type = $_POST[$prefix . 't'];
		else
			$type = '';
	}
		
	$names = $values = array
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

	if (isset($_POST[$prefix]))
		$value = $_POST[$prefix];
	else
		$value = '';
	
	return '<select class="w3-select w3-border control" name="' . $prefix . '_type" id="' . $prefix . '_type">'
			. smarty_function_html_options(array
					(
						'output'     => $names,
						'values'     => $values,
						'selected'   => $type,
						'print_result' => false
					), $smarty)
			. '</select>'
			. '<input class="w3-input w3-border control" type="text" name="' . $prefix . '" id="' . $prefix . '" value="' . $value . '" maxlength="30" size="30">';
}
?>
