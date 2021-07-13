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

	function CustomerCombinedAddLink($id = null, $path = '', $standAlone = false)
	{
		$temp = 'Add New Combined Customer Bill';
		return '<a href="' . CustomerCombinedAddUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerCombinedAddUrl($id = null, $path = '')
	{
		if (is_null($id))
			return 'JavaScript:CombinedAddPopup(\'\',\'' . $path . '\')';
		else
			return 'JavaScript:CombinedAddPopup(\'?id=' . $id . '\',\'' . $path . '\')';
	}

	function CustomerCombinedEditLink($id, $id2 = null, $path = '', $standAlone = false)
	{
		$temp = 'Edit Combined Customer ' . sprintf('%08d', $id);
		if (!is_null($id2))
			$temp .= ' for ' . sprintf('%08d', $id2);
		return '<a href="' . CustomerCombinedEditUrl($id, $id2, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerCombinedEditUrl($id, $id2 = 0, $path = '')
	{
		if (is_null($id2))
			return 'JavaScript:CombinedEditPopup(\'id=' . $id . '\',\'' . $path . '\')';
		else
			return 'JavaScript:CombinedEditPopup(\'id=' . $id . '&amp;id2=' . $id2 . '\',\'' . $path . '\')';
	}
	
?>
