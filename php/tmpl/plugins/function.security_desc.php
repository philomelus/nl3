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
function smarty_function_security_desc($params, &$smarty)
{
	global $Routes;

    $page = '';
    $feature = ''; 
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
        case 'page':
        case 'feature':
            $$_key = (string)$_val;
            break;

        default:
            $smarty->trigger_error('securitu_desc: extra attribute ' . $_key . ' unknown', E_USER_NOTICE);
            break;
		}
	}

    $digits = num_digits($page);
    if (($digits % 2) == 1)
        ++$digits;
    $id = sprintf('%0' . $digits . 'd', $page);
    $data = SecurityData::create();
	$temp = $data->descriptions();
    $desc = $temp[$id][$feature];
    if (empty($desc))
        $desc = 'Description Unspecified';
    return $desc;
}
?>
