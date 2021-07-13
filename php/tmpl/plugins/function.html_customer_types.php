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
function smarty_function_html_customer_types($params, &$smarty)
{
	global $DeliveryTypes;

	require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

	if (empty($DeliveryTypes))
		populate_types();
	
	$name = null;
	$selected = null;
	$any = false;
	$extra = '';
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
        case 'name':
        case 'selected':
            $$_key = (string)$_val;
            break;
        
        case 'any':
            $$_key = (boolean)$_val;
            break;
        
        default:
            if (!is_array($_val))
                $extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
            else
                $smarty->trigger_error("html_customer_types: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
            break;
		}
	}
	
	$html = '';
	reset($DeliveryTypes);
	if ($any)
		$html .= '<option label="Any" value="0">Any</option>';
	foreach ($DeliveryTypes as $_key => $_val)
	{
		if ($_val['visible'])
		{
			$html .= '<option label="' . smarty_function_escape_special_chars($_val['abbr']) . '" value="' .
					smarty_function_escape_special_chars($_key) . '"';
			if ($_key == $selected)
				$html .= ' selected="selected"';
			$html .= '>' . smarty_function_escape_special_chars($_val['abbr']) . '</option>' . "\n";
		}
	}
    if(!empty($name))
    {
        $html = '<select class="w3-select w3-border control" name="' . $name . '" id="' . $name . '"'
              . $extra . '>' . "\n" . $html . '</select>' . "\n";
    }
    else
    {
        $html = '<select class="w3-select w3-border control"' . $extra . '>' . "\n" . $html . '</select>' . "\n";
    }

	return $html;
}
?>
