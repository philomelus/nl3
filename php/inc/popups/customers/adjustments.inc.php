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

	function AdjustmentAddUrl($id = null, $path = '')
	{
		if (is_null($id))
			return 'JavaScript:AdjustmentAddPopup(\'\',\'' . $path . '\')';
		else
			return 'JavaScript:AdjustmentAddPopup(\'?cid=' . $id . '\',\'' . $path . '\')';
	}
	
	function AdjustmentEditLink($id, $path = '', $standAlone = false)
	{
		$temp = 'Edit Adjustment ' . sprintf('%08d', $id);
		return '<a href="' . AdjustmentEditUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function AdjustmentEditUrl($id, $path = '')
	{
		return 'JavaScript:AdjustmentEditPopup(\'aid=' . $id . '\',\'' . $path . '\')';
	}
	
	function AdjustmentViewLink($id, $path = '', $standAlone = false)
	{
		$temp = 'View Adjustment ' . sprintf('%08d', $id);
		return '<a href="' . AdjustmentViewUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function AdjustmentViewUrl($id, $path = '')
	{
		return 'JavaScript:AdjustmentViewPopup(\'aid=' . $id . '\',\'' . $path . '\')';
	}

?>
