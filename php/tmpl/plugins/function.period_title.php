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
function smarty_function_period_title($params, &$smarty)
{
	$id = null;
	$show = false;
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
		case 'id':
			$$_key = (integer)$_val;
			break;
		
		case 'show':
			$$_key = (boolean)$_val;
			break;
		
		default:
			$smarty->trigger_error('period_title: extra attribute ' . $_key . ' unknown', E_USER_NOTICE);
			break;
		}
	}
	if ($id === 0)
		return 'Current' . ($show ? sprintf(' (id = %04d)', $iid) : '');
	return iid2title($id, $show);
}
?>
