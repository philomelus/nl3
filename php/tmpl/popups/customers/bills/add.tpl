<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<title>Add Bill</title>
	<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/printf.js"></script>
	<script type="text/javascript" src="../../../js/periods.js.php"></script>
	<script type="text/javascript" src="js/add.js"></script>
</head>
<body>
	{html_error}
{if !empty($message)}
	<div>
		{$message}
	{if count($errorList) > 0}
		{section name=err loop=$errorList}
		<div>{$errorList[err]}</div>
		{/section}
	{/if}	
	</div>
{/if}
	<form method="post" action="{$action}">
		<div>
			<div>
				<div>
					Keep This Portion For Your Records
				</div>
				<div>
					<div>{$clientName}</div>
					<div>{$clientAddress1}</div>
					<div>{$clientAddress2}</div>
					<div>Phone: <span>{$clientTelephone}</span></div>
				</div>
				<div>
					<div>
						<div>RT#</div>
						<div>{html_routes name="route" selected=$route}</div>
						<div>ACCT#</div>
						<div>{customer_view_link id=$CID path=$ROOT}{$id}</a></div>
					</div>
					<div>
						<div>DELIVERY NAME</div>
						<div><input type="text" name="deliveryName" value="{$deliveryName}" size="25" /></div>
						<div><input type="text" name="deliveryAddress" value="{$deliveryAddress}" size="25" /></div>
						<div>
							<input type="text" name="deliveryCity" value="{$deliveryCity}" size="8" />
							<input type="text" name="deliveryState" value="{$deliveryState}" size="2" />
							<input type="text" name="deliveryZip" value="{$deliveryZip}" size="5" />
						</div>
					</div>
				</div>
				<div>
					<div>Subscription Period: <span>{$bill->dts}</span>-<span>{$bill->dte}</span></div>
					<table>
						<tr>
							<th>Previous Balance</th>
							<td>$<input name="previous" type="text" value="{$previous|string_format:'%01.2f'}" size="6" /></td>
						</tr>
						<tr>
							<th>Payment(s) Received</th>
							<td>$<input name="payments" type="text" value="{$payments|string_format:'%01.2f'}" size="6" /></td>
						</tr>
						<tr>
							<th><input type="text" name="title" value="{$title}" size="25" /></th>
							<td>$<input type="text" name="rate" value="{$rate|string_format:'%01.2f'}" size="6" /></td>
						<tr>
						</tr>
							<th>Adjustments</th>
							<td>$<input name="adjustments" type="text" value="{$adjustments|string_format:'%01.2f'}" size="6" /></td>
						</tr>
						<tr>
							<th>TOTAL DUE</th>
							<td>$<input type="text" name="total" value="{$total}" size="6" /></td>
						</tr>
					</table>
				</div>
				<div>
					<input type="text" name="note1" value="{$note1}" size="35" maxlength="35" />
					<input type="text" name="note2" value="{$note2}" size="35" maxlength="35" />
					<input type="text" name="note3" value="{$note3}" size="35" maxlength="35" />
					<input type="text" name="note4" value="{$note4}" size="35" maxlength="35" />
				</div>
			</div>
			<div>
				<div>
					PLEASE <b>RETURN</b> THIS PORTION
				</div>
				<div>
					<button name="action" value="add">Add</button>
					<button name="action" value="cancel">Cancel</button>
				</div>
				<div>
					<div><input type="text" name="billName" value="{$billName}" size="22" /></div>
					<div><input type="text" name="billAddress1" value="{$billAddress1}" size="22" /></div>
					<div><input type="text" name="billAddress2" value="{$billAddress2}" size="22" /></div>
					<div><input type="text" name="billAddress3" value="{$billAddress3}" size="22" /></div>
					<div><input type="text" name="billAddress4" value="{$billAddress4}" size="22" /></div>
				</div>
				<div>{$clientName}</div>
				<div><span>{$bill->dts}</span>-<span>{$bill->dte}</span></div>
				<div>
					<div>
						<div>Account #</div>
						<div>{customer_view_link id=$CID path=$ROOT}{$id}</a></div>
					</div>
					<div>
						<div>Amount Due</div>
						<div></div>
					</div>
				</div>
				<div>
					<div>Driver Tip</div>
					<div>$_________</div>
				</div>
			</div>
		</div>
		<div>
			<table>
				<tbody>
					<tr>
						<td>Period</td>
						<td>{html_periods name='period' selected=$period}</td>
						<td>&nbsp;</td>
						<td>Printed</td>
						<td>{html_radios name='export' options=$exportOptions selected=$export}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="cid" value="{$CID}" />
	</form>
<?php
    gen_htmlFooter();
?>
