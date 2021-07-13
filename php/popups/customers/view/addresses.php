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
	// Handle view customer addresses page display
	function display()
	{
		global $customer, $deliveryAddr, $billingAddr;

?>
			<table>
				<caption>Delivery Address</caption>
				<tr>
					<td>Name</td>
					<td>
<?php
		echo valid_name($customer->firstName, $customer->lastName);
?>
					</td>
				</tr>
				<tr>
					<td>Alternate Name</td>
					<td>
<?php
		$name = lup_c_name($customer->id, NAME_C_DELIVERY2);
		if ($name)
			echo valid_name($name->first, $name->last);
		else
			echo '&nbsp;';
?>
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td>
						<?php echo stripslashes($deliveryAddr->address1); ?>
						<br />
						<?php echo stripslashes($deliveryAddr->city); ?>&nbsp;<?php echo stripslashes($deliveryAddr->state); ?>,&nbsp;<?php echo stripslashes($deliveryAddr->zip); ?>
					</td>
				</tr>
<?php
		$telephone = lup_c_telephone($customer->id, TEL_C_DELIVERY1);
		if ($telephone)
		{
			$temp = array
				(
					0 => stripslashes($telephone->type),
					1 => valid_text($telephone->number)
				);
		}
		else
		{
			$temp = array
				(
					0 => 'Main',
					1 => ''
				);
		}
?>
				<tr>
					<td>Telephone 1</td>
					<td>
						<span><?php echo $temp[0] ?></span>
						<span><?php echo $temp[1] ?></span>
					</td>
				</tr>
<?php
		$telephone = lup_c_telephone($customer->id, TEL_C_DELIVERY2);
		if ($telephone)
		{
			$temp = array
				(
					0 => stripslashes($telephone->type),
					1 => valid_text($telephone->number)
				);
		}
		else
		{
			$temp = array
				(
					0 => 'Alternate',
					1 => ''
				);
		}
?>
				<tr>
					<td>Telephone 2</td>
					<td>
						<span><?php echo $temp[0] ?></span>
						<span><?php echo $temp[1] ?></span>
					</td>
				</tr>
				<tr>
<?php
		$telephone = lup_c_telephone($customer->id, TEL_C_DELIVERY3);
		if ($telephone)
		{
			$temp = array
				(
					0 => stripslashes($telephone->type),
					1 => valid_text($telephone->number)
				);
		}
		else
		{
			$temp = array
				(
					0 => 'Mobile',
					1 => ''
				);
		}
?>
					<td>Telephone 3</td>
					<td>
						<span><?php echo $temp[0] ?></span>
						<span><?php echo $temp[1] ?></span>
					</td>
				</tr>
			</table>
			<br />
			<table>
				<caption>Billing Address</caption>
				<tr>
					<td>Name</td>
					<td>
<?php
		$name = lup_c_name($customer->id, NAME_C_BILLING1);
		if ($name)
			echo valid_name($name->first, $name->last);
		else
			echo '&nbsp;';
?>
					</td>
				</tr>
				<tr>
					<td>Alternate Name</td>
					<td>
<?php
		$name = lup_c_name($customer->id, NAME_C_BILLING2);
		if ($name)
			echo valid_name($name->first, $name->last);
		else
			echo '&nbsp;';
?>
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td>
<?php
		if (!empty($billingAddr->address1))
		{
			echo stripslashes($billingAddr->address1) . '<br />'
					. stripslashes($billingAddr->city) . '&nbsp;'
					. stripslashes($billingAddr->state) . ',&nbsp;'
					. stripslashes($billingAddr->zip);
		}
		else
			echo '&nbsp;<br />&nbsp;';
?>
					</td>
				</tr>
				<tr>
<?php
		$telephone = lup_c_telephone($customer->id, TEL_C_BILLING1);
		if ($telephone)
		{
			$temp = array
				(
					0 => stripslashes($telephone->type),
					1 => valid_text($telephone->number)
				);
		}
		else
		{
			$temp = array
				(
					0 => 'Main',
					1 => ''
				);
		}
?>
					<td>Telephone 1</td>
					<td>
						<span><?php echo $temp[0] ?></span>
						<span><?php echo $temp[1] ?></span>
					</td>
				</tr>
				<tr>
<?php
		$telephone = lup_c_telephone($customer->id, TEL_C_BILLING2);
		if ($telephone)
		{
			$temp = array
				(
					0 => stripslashes($telephone->type),
					1 => valid_text($telephone->number)
				);
		}
		else
		{
			$temp = array
				(
					0 => 'Alternate',
					1 => ''
				);
		}
?>
					<td>Telephone 2</td>
					<td>
						<span><?php echo $temp[0] ?></span>
						<span><?php echo $temp[1] ?></span>
					</td>
				</tr>
				<tr>
<?php
		$telephone = lup_c_telephone($customer->id, TEL_C_BILLING3);
		if ($telephone)
		{
			$temp = array
				(
					0 => stripslashes($telephone->type),
					1 => valid_text($telephone->number)
				);
		}
		else
		{
			$temp = array
				(
					0 => 'Mobile',
					1 => ''
				);
		}
?>
					<td>Telephone 3</td>
					<td>
						<span><?php echo $temp[0] ?></span>
						<span><?php echo $temp[1] ?></span>
					</td>
				</tr>
			</table>
<?php
	}

	//-------------------------------------------------------------------------
	// Return view customer addresses page specific scripts
	function scripts()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Return view customer addresses page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle view customer addresses page submits
	function submit()
	{
	}

?>
