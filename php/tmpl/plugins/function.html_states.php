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
function smarty_function_html_states($params, &$smarty)
{
	require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

	static $abbr = array
		(
			'AL' => 'Alabama',
			'AK' => 'Alaska',
			'AZ' => 'Arizona',
			'AR' => 'Arkansas',
			'CA' => 'California',
			'CO' => 'Colorado',
			'CT' => 'Connecticut',
			'DE' => 'Delaware',
			'FL' => 'Florida',
			'GA' => 'Georgia',
			'HI' => 'Hawaii',
			'ID' => 'Idaho',
			'IL' => 'Illinois',
			'IN' => 'Indiana',
			'IA' => 'Iowa',
			'KS' => 'Kansas',
			'KY' => 'Kentucky',
			'LA' => 'Louisiana',
			'ME' => 'Maine',
			'MD' => 'Maryland',
			'MA' => 'Massachusetts',
			'MI' => 'Michigan',
			'MN' => 'Minnesota',
			'MS' => 'Mississippi',
			'MO' => 'Missouri',
			'MT' => 'Montana',
			'NE' => 'Nebraska',
			'NV' => 'Nevada',
			'NH' => 'New Hampshire',
			'NJ' => 'New Jersey',
			'NM' => 'New Mexico',
			'NY' => 'New York',
			'NC' => 'North Carolina',
			'ND' => 'North Dakota',
			'OH' => 'Ohio',
			'OK' => 'Oklahoma',
			'OR' => 'Oregon',
			'PA' => 'Pennsylvania',
			'PR' => 'Puerto Rico ',
			'RI' => 'Rhode Island',
			'SC' => 'South Carolina',
			'SD' => 'South Dakota',
			'TN' => 'Tennessee',
			'TX' => 'Texas',
			'UT' => 'Utah',
			'VT' => 'Vermont',
			'VI' => 'Virgin Islands ',
			'VA' => 'Virginia',
			'WA' => 'Washington',
			'WV' => 'West Virginia',
			'WI' => 'Wisconsin',
			'WY' => 'Wyoming'
		);

	$name = null;
	$selected = null;
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
                $smarty->trigger_error("html_states: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
            break;
		}
	}
	
	$_html_result = '';
	reset($abbr);
	foreach ($abbr as $_key => $_val)
	{
		$_html_result .= '<option label="' . smarty_function_escape_special_chars($_val) . '" value="' .
				smarty_function_escape_special_chars($_key) . '"';
		if ($_key == $selected)
			$_html_result .= ' selected="selected"';
		$_html_result .= '>' . smarty_function_escape_special_chars($_val) . '</option>';
	}
    if(!empty($name))
    {
        $_html_result = '<select class="w3-select w3-border control" name="' . $name . '" id="' . $name . '" '
                      . $extra . '>' . $_html_result . '</select>';
    }
    else
    {
        $_html_result = '<select class="w3-select w3-border control"' . $extra . '>' . $_html_result . '</select>';
    }

	return $_html_result;
}
?>
