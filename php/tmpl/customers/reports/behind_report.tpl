<form>
    <input type="submit" name="action" value="Print Report" onclick="JavaScript: window.print(); return false;" />
</form>
<div></div>
<div>{$title}</div>
<table>
    <thead>
        <tr>
            <th>Account</th>
            <th>Name</th>
            <th>Address</th>
            <th>Telephone</th>
            <th>Start Date</th>
            <th>Type</th>
            <th>Route</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
{if (count($customers) > 0)}
    {section name=i loop=$customers}
        <tr>
            <td>{$customers[i].id|customer_id}</td>
            <td>{customer_name first=$customers[i].firstName last=$customers[i].lastName order=fl}</td>
            <td>{$customers[i].address}</td>
            <td>{$customers[i].telephone}</td>
            <td>{$customers[i].started}</td>
            <td>{customer_type_abbr id=$customers[i].type_id}</td>
            <td>{route_title id=$customers[i].route_id}</td>
            <td>${$customers[i].balance}</td>
        </tr>
    {/section}
{else}
        <tr><td colspan="8">None</td></tr>
{/if}
    </tbody>
</table>
