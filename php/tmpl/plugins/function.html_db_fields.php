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
function smarty_function_html_db_fields($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    require_once $smarty->_get_plugin_filepath('function', 'html_options');
	
	$prefix			= 'dbf_';
	$offset			= 0;
	$limit			= 10;
	$max			= 0;
	$path			= '../';
	$left			= false;
    $extra_attrs	= '';
    $refresh        = true;
    $clear          = false;
    foreach ($params as $_key=>$_value)
	{
        switch ($_key)
		{
		case 'prefix':
		case 'path':
			$$_key = (string)$_value;
			break;

		case 'offset':
		case 'limit':
		case 'max':
			$$_key = (integer)$_value;
			break;

        case 'left':
        case 'refresh':
        case 'clear':
			$$_key = (boolean)$_value;
			break;

		default:
			if (!is_array($_value))
				$extra_attrs .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_value) . '"';
			else
				$smarty->trigger_error('html_db_fields: extra attribute \'' . $_key . '\' cannot be an array', E_USER_NOTICE);
			break;
        }
    }

	$html = '';

	if (isset($_POST[$prefix . 'offset']))
		$offset = intval($_POST[$prefix . 'offset']);
	if (isset($_POST[$prefix . 'limit']))
		$limit = intval($_POST[$prefix . 'limit']);
    if ($refresh)
        $refreshHtml = '<button class="w3-bar-item w3-button w3-border" type="submit" name="action" value="refresh">Refresh</button>';
    else
        $refreshHtml = '';
    if ($clear)
        $clearHtml = '<button class="w3-bar-item w3-button w3-border" type="submit" name="action" value="clear">Clear</button>';
    else
        $clearHtml = '';
	if ($left)
	{
        $left = $refreshHtml . $clearHtml;
		$right = '';
	}
	else
	{
		$left = '';
        $right = $refreshHtml . $clearHtml;
	}
	if ($offset == 0)
		$lessstate = ' disabled="disabled"';
	else
		$lessstate = '';
	if ($max > 0 && $offset + $limit >= $max)
		$morestate = ' disabled="disabled"';
	else
        $morestate = '';
    return '<div class="w3-bar w3-black">'
            . $left
            . '<div class="w3-bar-item">Show</div>'
			. '<input class="w3-bar-item w3-input w3-border" type="text" name="' . $prefix . 'limit" value="' . $limit . '" size="3"/>'
			. '<div class="w3-bar-item">from</div>'
			. '<input class="w3-bar-item w3-input w3-border" type="text" name="' . $prefix . 'offset" value="' . $offset . '" size="3"/>'
			. '&nbsp;&nbsp;'
			. '<button class="w3-bar-item w3-button w3-border" type="submit" name="action" value="begin"' . $lessstate . '>&lt;&lt;</button>'
			. '<button class="w3-bar-item w3-button w3-border" type="submit" name="action" value="prev"' . $lessstate . '>&lt;</button>'
			. '<button class="w3-bar-item w3-button w3-border" type="submit" name="action" value="next"' . $morestate . '>&gt;</button>'
            . '<button class="w3-bar-item w3-button w3-border" type="submit" name="action" value="end"' . $morestate . '>&gt;&gt;</button>'
            . '&nbsp;&nbsp;'
            . $right
            . '</div>';
}
?>
