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

	function CustomerRateAddUrl($path = '')
	{
		return 'JavaScript:CustomerRateAddPopup(\'' . $path . '\')';
	}
	
	function CustomerRateEditLink($type_id, $period_id_start, $period_id_end, $path = '', $standAlone = false)
	{
		$temp = 'Edit Customer Rate ' . sprintf('%08d', $type_id);
		return '<a href="' . CustomerRateEditUrl($type_id, $period_id_start, $period_id_end, $path)
				. '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerRateEditUrl($type_id, $period_id_start, $period_id_end, $path = '')
	{
		return 'JavaScript:CustomerRateEditPopup(\'id=' . $type_id . '&amp;s=' . $period_id_start
				. '&amp;e=' . $period_id_end . '\',\'' . $path . '\')';
	}
	
	function CustomerRateViewLink($type_id, $period_id_start, $period_id_end, $path = '', $standAlone = false)
	{
		$temp = 'View Customer Rate ' . sprintf('%08d', $type_id);
		return '<a href="' . CustomerRateViewUrl($type_id, $period_id_start, $period_id_end, $path)
				. '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerRateViewUrl($type_id, $period_id_start, $period_id_end, $path = '')
	{
		return 'JavaScript:CustomerRateViewPopup(\'id=' . $type_id . '&amp;s=' . $period_id_start
				. '&amp;e=' . $period_id_end . '\',\'' . $path . '\')';
	}
	
?>
