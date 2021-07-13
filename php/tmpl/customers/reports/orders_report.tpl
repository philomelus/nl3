<form>
    <input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" />
</form>
<div>{$title}</div>
<div>New Customers</div>
<table>
    <thead>
        <tr>
            <th>Started</th>
            <th>CID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Telephone</th>
            <th>Route</th>
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
{if $customersCount > 0}
    {foreach from=$customers item=customer}
        <tr>
            <td>{$customer->started}</td>
            <td>{$customer->id|customer_id}</td>
            <td>{customer_name first=$customer->firstName last=$customer->lastName order=fl}</td>
            <td>{$customer->address}</td>
            <td>{$customer->telephone}</td>
            <td>{route_title id=$customer->route_id}</td>
            <td>{customer_type_abbr id=$customer->type_id}</td>
        </tr>
    {/foreach}
{else}
        <tr><td colspan="7">None</td></tr>
{/if}
    </tbody>
</table>
<br />
<div>Restarted Customers</div>
<table>
    <thead>
        <tr>
            <th>Stop</th>
            <th>Start</th>
            <th>Days</th>
            <th>CID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Telephone</th>
            <th>Route</th>
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
{if (count($restarted) > 0)}
    {foreach from=$restarted item=data}
        {assign var=customer value=$data.customer}
        <tr>
            <td>{$data.stop}</td>
            <td>{$data.start}</td>
            <td>{$data.count}</td>
            <td>{$customer->id|customer_id}</td>
            <td>{customer_name first=$customer->firstName last=$customer->lastName order=fl}</td>
            <td>{$customer->address}</td>
            <td>{$customer->telephone}</td>
            <td>{route_title id=$customer->route_id}</td>
            <td>{customer_type_abbr id=$customer->type_id}</td>
        </tr>
    {/foreach}
{else}
        <tr><td colspan="9">None</td></tr>
{/if}
    </tbody>
</table>
