{html_header subtitle=$subtitle script=$script style=$style}
<body>
    <script language="JavaScript">pathToImages="../img/";</script>
    <h1>Change Delivery Type</h1>
    {$result}
    {html_error}
    {$flagStopWarning}
    <div>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{$name}</td>
                    </tr>
                    <tr>
                        <td>{$address}</td>
                    </tr>
                    <tr>
                        <td>{route_title id=$route_id}</td>
                    </tr>
                    <tr>
                        <td>{customer_type_abbr id=$type_id}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <table>
                <thead>
                    <tr>
                        <td colspan="3">Type Changes</td>
                    </tr>
                </thead>
                <tbody>
{if ($count > 0)}
    {foreach from=$changes item=change}
                <tr>
                    <td>
                        {servicetype_view_link id=$change->id path='../'}
                            <img src="../img/view.png" alt="V" title="View Service Type Change" />'
                        </a>
                    </td>
                    <td>{$change->when|date}</td>
                    <td>
                        {customer_type_abbr id=$change->type_id_from}
                        to
                        {customer_type_abbr id=$change->type_id_to}
                    </td>
                </tr>
    {/foreach}
{else}
                <tr><td colspan="3">None</td></tr>
{/if}
			</tbody>
		</table>
	</div>
</div>
<form method="post" action="{$action}">
    <table>
        <tbody>
            <tr>
                <td>Date</td>
                <td>{html_date prefix="when" left=true}</td>
            </tr>
            <tr>
                <td>Current</td>
                <td>{customer_type_abbr id=$type_id}</td>
			</tr>
			<tr>
				<td>New Type</td>
				<td>{html_customer_types name="type" selected=$type}</td>
			</tr>
			<tr>
				<td></td>
				<td>
{if ($include)}
					<input type="checkbox" name="include" value="Y" checked="checked">Include in Billing?</input>
{else}
                    <input type="checkbox" name="include" value="Y">Include in Billing?</input>
{/if}
                </td>
			</tr>
			<tr>
				<td>Notes</td>
				<td>
					<textarea name="notes" rows="4" cols="40">{$notes}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Submit" />
				</td>
			</tr>
		<tbody>
	</table>
	<input type="hidden" name="cid" value="{$cid}" />
</form>
{html_footer}
