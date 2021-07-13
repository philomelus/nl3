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

	function PeriodAddUrl($path = '')
	{
		return 'JavaScript:PeriodAddPopup(\'' . $path . '\')';
	}
	
	function PeriodViewLink($id, $path = '', $standAlone = false)
	{
		$temp = 'View Period ' . sprintf('%04d', $id);
		return '<a href="' . PeriodViewUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function PeriodViewUrl($id, $path = '')
	{
		return 'JavaScript:PeriodViewPopup(\'id=' . $id . '\',\'' . $path . '\')';
	}
	
	function PeriodEditLink($id, $path = '', $standAlone = false)
	{
		$temp = 'View Period ' . sprintf('%04d', $id);
		return '<a href="' . PeriodEditUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function PeriodEditUrl($id, $path = '')
	{
		return 'JavaScript:PeriodEditPopup(\'id=' . $id . '\',\'' . $path . '\')';
	}
	
?>
