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
function smarty_function_html_header($params, &$smarty)
{
    $script = '';
    $scripts = '';
    $style = '';
    $styles = '';
    $subtitle = '';

    foreach ($params as $_key=>$_value)
	{
        switch ($_key)
		{
        case 'script':
        case 'scripts':
        case 'style':
        case 'styles':
        case 'subtitle':
			$$_key = (string)$_value;
			break;

		default:
			if (!is_array($_value))
				$extra_attrs .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_value) . '"';
			else
				$smarty->trigger_error('html_header: extra attribute \'' . $_key . '\' cannot be an array', E_USER_NOTICE);
			break;
        }
    }

    if (empty($subtitle))
        $smarty->trigger_error('html_header:  subtitle required.');

    return gen_htmlHeader($subtitle, $styles, $scripts, $style, $script);
}
?>
