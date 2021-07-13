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
function smarty_function_user_name($params, &$smarty)
{
	global $Routes;

    $id = null;
    $any = false;
	$show = false;
	foreach($params as $_key => $_val)
	{
		switch($_key)
		{
        case 'id':
            $$_key = (string)$_val;
            break;

        case 'any':
        case 'show':
            $$_key = (boolean)$_val;
            break;
        
        default:
            $smarty->trigger_error('user_name: extra attribute ' . $_key . ' unknown', E_USER_NOTICE);
            break;
		}
	}

	// If its null or 0, there is nothing to lookup
	$text = '';
    if (is_null($id))
    {
        if ($any)
            $text = 'Any';
        else
            $text = '&nbsp;';
    }
    elseif (empty($id))
		$text = '&nbsp;';

	// Get it from database if all else fails
	if (empty($text))
    {
        $temp = lup_user($id);
		if (!$temp)
		{
			$smarty->trigger_error('user_name: unable to locate title for user ' . $id, E_USER_WARNING);
			return sprintf('%04d', $id);
		}
        $text = $temp->name;
    }

    // Add id in decimal is requested
	if ($show)
        $text .= sprintf(' (%04d)', $id);

	return $text;
}
?>
