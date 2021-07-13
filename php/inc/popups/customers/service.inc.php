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

	function ServiceAddUrl($id = 0, $path = '')
	{
		if ($id > 0)
			return "JavaScript:ServiceAddPopup('?id=" . $id . "', '" . $path . "')";
		else
			return "JavaScript:ServiceAddPopup('', '" . $path . "')";
	}
	
	function ServiceEditLink($id, $path = '', $standAlone = false)
	{
		$temp = 'Edit Start/Stop ' . sprintf('%08d', $id);
		return '<a href="' . ServiceEditUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function ServiceEditUrl($id, $path = '')
	{
		return "JavaScript:ServiceEditPopup('id=" . $id . "','" . $path . "')";
	}
	
	function ServiceViewLink($id, $path = '', $standAlone = false)
	{
		$temp = 'View Start/Stop' . sprintf('%08d', $id);
		return '<a href="' . ServiceViewUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function ServiceViewUrl($id, $path = '')
	{
		return "JavaScript:ServiceViewPopup('id=" . $id . "','" . $path . "')";
	}

?>
