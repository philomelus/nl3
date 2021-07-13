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
	// Handle view customer summary page display
	function display()
	{
		global $customer;
		global $DeliveryTypes;
		global $err;

?>
		<table>
			<tbody>
				<tr>
					<td>Billing Name</td>
					<td>
<?php
		$name = lup_c_name($customer->id, NAME_C_BILLING1);
		if ($name)
			echo valid_name($name->first, $name->last);
		else
			echo '<span>none</span>';
?>
					</td>
				</tr>
				<tr>
					<td>Billing Type</td>
					<td>
<?php
		echo tid2abbr($customer->billType);
?>
					</td>
				</tr>
				<tr>
					<td>Status At Last Billing</td>
					<td>
<?php
		if ($customer->billStopped == 'Y')
			echo 'Stopped';
		else
			echo 'Delivered';
?>
					</td>
				</tr>
				<tr>
					<td>Balance on last Bill</td>
<?php
		$billBalance = sprintf('$%01.2f', $customer->billBalance);
?>
					<td><?php echo $billBalance ?></td>
				</tr>
				<tr>
					<td>Rate</td>
					<td>
<?php
		switch($customer->rateType)
		{
		case RATE_STANDARD:
			printf('<b>Standard</b> (<b>$%01.2f</b>)', $DeliveryTypes[$customer->type_id]['rate']);
			break;

		case RATE_REPLACE:
			printf('<b>Overridden</b> @ <b>$%01.2f</b>', $customer->rateOverride);
			break;

		case RATE_SURCHARGE:
			printf('<b>Surcharged</b> @ <b>$%01.2f</b> (+ $%01.2f = $%01.2f)',
					$customer->rateOverride, $DeliveryTypes[$customer->type_id]['rate'],
					$DeliveryTypes[$customer->type_id]['rate'] + $customer->rateOverride);
			break;
		}
		echo '</span>';
?>
					</td>
				</tr>
				<tr>
					<td>Current Bill Date Display</td>
					<td>
						<table>
							<tbody>
								<tr>
									<td>Start:</td>
									<td><?php echo strftime('%m-%d-%Y', strtotime($customer->billStart)); ?></td>
								</tr>
								<tr>
									<td>End:</td>
									<td><?php echo strftime('%m-%d-%Y', strtotime($customer->billEnd)); ?></td>
								</tr>
								<tr>
									<td>Due:</td>
									<td><?php echo strftime('%m-%d-%Y', strtotime($customer->billDue)); ?></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>Next Billing</td>
					<td><?php echo iid2titleEx($customer->billPeriod); ?></td>
				</tr>
				<tr>
					<td>Bill Every</td>
					<td>
<?php
		echo $customer->billCount . ' period';
		if ($customer->billCount > 1)
			echo 's';
?>
					</td>
				</tr>
				<tr>
					<td>Quantity</td>
					<td>
<?php
		echo $customer->billQuantity . ' paper';
		if ($customer->billQuantity > 1)
			echo 's';
?>
					</td>
				</tr>
			</tbody>
		</table>
<?php
	}

	//-------------------------------------------------------------------------
	// Return view customer summary page specific scripts
	function scripts()
	{
		return
'
<script language="JavaScript">
	function PopupWindow(url,w,h,target)
	{
		var theWin = window.open(url, target,
				"' . popup_features() . ',width=" + w + ",height=" + h);
		theWin.focus();
	}
</script>
';
	}

	//-------------------------------------------------------------------------
	// Return view customer summary page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle view customer summary page submits
	function submit()
	{
	}

?>
