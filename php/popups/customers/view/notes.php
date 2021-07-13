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

	//-------------------------------------------------------------------------
	// Handle view customer notes page display
	function display()
	{
		global $customer;

?>
			<table>
				<tr>
					<td>Notes</td>
					<td>
<?php
		$val = valid_text(htmlspecialchars(stripslashes($customer->notes)));
		$noteEmpty = ($val == '&nbsp;');
		if ($noteEmpty)
			$val = '<span>blank</span>';
?>
						<?php echo $val ?>
					</td>
				</tr>
				<tr>
					<td>Billing Note</td>
<?php
		$billNote = array('', '', '', '');
		$temp = str_split("\r\n", $customer->billNote);
		if (!empty($temp))
		{
			if (count($temp) >= 1)
				$billNote[0] = $temp[0];
			if (count($temp) >= 2)
				$billNote[1] = $temp[1];
			if (count($temp) >= 3)
				$billNote[2] = $temp[2];
			if (count($temp) >= 4)
				$billNote[3] = $temp[3];
		}
		if (empty($billNote[0]) || empty($billNote[1]) || empty($billNote[2])
				|| empty($billNote[3]) || $noteEmpty)
		{
			$style = ' width: 300px;';
			if (empty($billNote[0]))
				$billNote[0] = '<span>blank</span>';
			if (empty($billNote[1]))
				$billNote[1] = '<span>blank</span>';
			if (empty($billNote[2]))
				$billNote[2] = '<span>blank</span>';
			if (empty($billNote[3]))
				$billNote[3] = '<span>blank</span>';
		}
		else
			$style = '';
?>
					<td>">
						<div>
							<span>Line 1</span>
							<?php echo $billNote[0] ?>
						</div>
						<div>
							<span>Line 2</span>
							<?php echo $billNote[1] ?>
						</div>
						<div>
							<span>Line 3</span>
							<?php echo $billNote[2] ?>
						</div>
						<div>
							<span>Line 4</span>
							<?php echo $billNote[3] ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>Delivery Note</td>
					<td>
<?php
		$val = valid_text(htmlspecialchars(stripslashes($customer->deliveryNote)));
		$noteEmpty = ($val == '&nbsp;');
		if ($noteEmpty)
			$val = '<span>blank</span>';
?>
						<?php echo $val ?>
					</td>
				</tr>
			</table>
<?php
	}

	//-------------------------------------------------------------------------
	// Return view customer notes page specific scripts
	function scripts()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Return view customer notes page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle view customer notes page submits
	function submit()
	{
	}

?>
