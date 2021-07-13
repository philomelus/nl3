<?php
/*
	Copyright 2021 Russell E. Gibson

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
function smarty_function_period_edit_link($params, &$smarty)
{
	$id = 0;
	$path = '';
	$standAlone = false;
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
        case 'id':
            $$_key = (integer)$_val;
            break;

        case 'path':
            $$_key = (string)$_val;
            break;

        case 'standAlone':
            $$_key = (boolean)$_val;
            break;
        
        default:
            $smarty->trigger_error('period_edit_link: extra attribute ' . $_key . ' unknown', E_USER_NOTICE);
            break;
		}
	}
	if (empty($id))
		$smarty->trigger_error('customer_rate_edit_link: period id appers invalid ('. $id . ')', E_USER_WARNING);
	return PeriodEditLink($id, $path, $standAlone);
}
?>
