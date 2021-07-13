<form>
    <input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" />
</form>
<div>Inactivity Report</div>
<div>{$subtitle1}</div>
<div>{$subtitle2}</div>
<table>
    <thead>
        <tr>
            <th>CustID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Type</th>
            <th>Rte</th>
            {$stopHeader}
        </tr>
    </thead>
    <tbody>
{if ($count > 0)}
    {foreach from=$customers item=data}
        <tr>
            <td>{$data.id|customer_id}</td>
            <td>{$data.name}</td>
            <td>{$data.address}</td>
            <td>{customer_type_abbr id=$data.type_id}</td>
            <td>{route_title id=$data.route_id}</td>
        {if (isset($data.when))}
            <td>{$data.when}</td>
        {/if}
        </tr>
    {/foreach}
{else}
        <tr><td span="6">None</td></tr>
{/if}
    </tbody>
</table>
