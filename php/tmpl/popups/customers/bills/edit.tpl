<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<title>Edit Bill</title>
	<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/printf.js"></script>
	<script type="text/javascript" src="../../../js/periods.js.php"></script>
	<script type="text/javascript" src="js/edit.js.php"></script>
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
					<div>Subscription Period: <span>{$dts}</span>-<span>{$dte}</span></div>
					<table>
						<tr>
							<th>Previous Balance</th>
							<td>$<input name="previous" type="text" value="{$previous}" size="6" /></td>
						</tr>
						<tr>
							<th>Payment(s) Received</th>
							<td>$<input name="payments" type="text" value="{$payments}" size="6" /></td>
						</tr>
						<tr>
							<th><input type="text" name="title" value="{$title}" size="25" /></th>
							<td>$<input type="text" name="rate" value="{$rate}" size="6" /></td>
						<tr>
						</tr>
							<th>Adjustments</th>
							<td>$<input  name="adjustments" type="text" value="{$adjustments}" size="6" /></td>
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
					<div><input type="text" name="billName" value="{$billName}" size="22" /></div>
					<div><input type="text" name="billAddress1" value="{$billAddress1}" size="22" /></div>
					<div><input type="text" name="billAddress2" value="{$billAddress2}" size="22" /></div>
					<div><input type="text" name="billAddress3" value="{$billAddress3}" size="22" /></div>
					<div><input type="text" name="billAddress4" value="{$billAddress4}" size="22" /></div>
				</div>
				<div>{$clientName}</div>
				<div><span>{$dts}</span>-<span>{$dte}</span></div>
				<div>
					<div>
						<div>Account #</div>
						<div>{customer_view_link id=$CID path=$ROOT}{$id}</a></div>
					</div>
					<div>
						<div>Amount Due</div>
						<div>{$total}</div>
					</div>
				</div>
				<div>
					<div>Driver Tip</div>
					<div>$_________</div>
				</div>
			</div>
		</div>
		<div>
			<div>
				<button name="action" value="update">Update</button>
				<button name="action" value="cancel">Cancel</button>
			</div>
			<div>
				<div>Created</div>
				<div>{$billCreated|date_format:'%m-%d-%Y %H:%M:%S'}</div>
			</div>
			<div>
				<div>Updated</div>
				<div>{$billUpdated|date_format:'%m-%d-%Y %H:%M:%S'}</div>
			</div>
			<div>
				<div>Period</div>
				<div>{html_periods name='period' selected=$IID}</div>
			</div>
			<div>
				<div>Print?</div>
				<div>{html_radios name='export' options=$exportOptions selected=$export}</div>
			</div>
			<div>
				<div>Rate</div>
				<div>
					{html_options name='rateType' options=$rateTypeOptions selected=$rateType id="rateType"}
					$<input type="text" name="rateOverride" value="{$rateOverride}" size="5" />
				</div>
			</div>
		</div>
		<input type="hidden" name="cid" value="{$CID}" />
		<input type="hidden" name="iid" value="{$IID}" />
		<input type="hidden" name="dueDate" value="{$dueDate}" />
		<input type="hidden" name="periodStart" value="{$periodStart}" />
		<input type="hidden" name="periodEnd" value="{$periodEnd}" />
	</form>
<?php
    gen_htmlFooter();
?>
