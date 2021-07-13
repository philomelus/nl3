<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<title>View Bill</title>
	<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
</head>
<body>
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
					<div>{$bill->rt}</div>
					<div>ACCT#</div>
					<div>{customer_view_link id=$CID path=$ROOT}{$bill->cid}</a></div>
				</div>
				<div>
					<div>DELIVERY NAME</div>
					<div>{$bill->dNm}</div>
					<div>{$bill->dAd}</div>
					<div>{$bill->dCt}&nbsp;{$bill->dSt}&nbsp;{$bill->dZp}</div>
				</div>
			</div>
			<div>
				<div>Subscription Period: {$bill->dts}-{$bill->dte}</div>
				<table>
					<tr>
						<th>Previous Balance</th>
						<td>{$bill->fwd}</td>
					</tr>
					<tr>
						<th>Payment(s) Received</th>
						<td>{$bill->pmt}</td>
					<tr>
					</tr>
						<th>{$bill->rTit}</th>
						<td>{$bill->rate}</td>
					<tr>
					</tr>
						<th>Adjustments</th>
						<td>{$bill->adj}</td>
					</tr>
					<tr>
						<th>TOTAL DUE</th>
						<td>{$bill->bal}</td>
					</tr>
				</table>
			</div>
			<div>{$bill->nt1} {$bill->nt2} {$bill->nt3} {$bill->nt4}</div>
		</div>
		<div>
			<div>
				PLEASE <b>RETURN</b> THIS PORTION
			</div>
			<div>
				<div>{$bill->bNm}</div>
				<div>{$bill->bAd1}</div>
				<div>{$bill->bAd2}</div>
{if empty($bill->bAd4)}
				<div>{$bill->bAd3}</div>
{else}
				<div>{$bill->bAd3}</div>
				<div>{$bill->bAd4}</div>
{/if}
			</div>
			<div>GEARHART OREGONIAN</div>
			<div>{$bill->dts}-{$bill->dte}</div>
			<div>
				<div>
					<div>Account #</div>
					<div>{customer_view_link id=$CID path=$ROOT}{$bill->cid}</a></div>
				</div>
				<div>
					<div>Amount Due</div>
					<div>{$bill->bal}</div>
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
					<td>Created</td>
					<td>{$billCreated|date_format:'%m-%d-%Y %H:%M:%S'}</td>
					<td>&nbsp;</td>
					<td>Updated</td>
					<td>{$billUpdated|date_format:'%m-%d-%Y %H:%M:%S'}</td>
				</tr>
				<tr>
					<td>Period</td>
					<td>{period_title id=$bill->iid}</td>
					<td>&nbsp;</td>
					<td>Printed</td>
{if $bill->export == 'Y'}
					<td>Yes</td>
{else}
					<td>No</td>
{/if}

				</tr>
			</tbody>
		</table>
	</div>
<?php
    gen_htmlFooter();
?>
