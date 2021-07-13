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

	function CustomerPaymentAddUrl($id = null, $path = '')
	{
		if (is_null($id))
			return 'JavaScript:CustomerPaymentAddPopup(\'\',' . $path . '\')';
		else
			return 'JavaScript:CustomerPaymentAddPopup(\'?cid=' . $id . '\',\'' . $path . '\')';
	}
	
	function CustomerPaymentEditLink($id, $path = '', $standAlone = false)
	{
		$temp = 'Edit Payment ' . sprintf('%08d', $id);
		return '<a href="' . CustomerPaymentEditUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerPaymentEditUrl($id, $path = '')
	{
		return 'JavaScript:CustomerPaymentEditPopup(\'pid=' . $id . '\',\'' . $path . '\')';
	}
	
	function CustomerPaymentViewLink($id, $path = '', $standAlone = false)
	{
		$temp = 'View Payment ' . sprintf('%08d', $id);
		return '<a href="' . CustomerPaymentViewUrl($id, $path) . '" title="' . $temp . '"' . ($standAlone ? ' />' : '>');
	}
	
	function CustomerPaymentViewUrl($id, $path = '')
	{
		return 'JavaScript:CustomerPaymentViewPopup(\'pid=' . $id . '\',\'' . $path . '\')';
	}
	
?>
