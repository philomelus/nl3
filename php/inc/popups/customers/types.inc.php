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

	function CustomerTypeAddUrl($path = '')
	{
		return 'JavaScript:CustomerTypeAddPopup(\'' . $path . '\')';
	}
	
	function CustomerTypeEditLink($id, $path = '', $standAlone = false)
	{
		$temp = 'Edit Type ' . sprintf('%08d', $id);
		return '<a href="' . CustomerTypeEditUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerTypeEditUrl($id, $path = '')
	{
		return 'JavaScript:CustomerTypeEditPopup(\'id=' . $id . '\',\'' . $path . '\')';
	}
	
	function CustomerTypeViewLink($id, $path = '', $standAlone = false)
	{
		$temp = 'View Type ' . sprintf('%04d', $id);
		return '<a href="' . CustomerTypeViewUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerTypeViewUrl($id, $path = '')
	{
		return 'JavaScript:CustomerTypeViewPopup(\'id=' . $id . '\',\'' . $path . '\')';
	}
	
?>
