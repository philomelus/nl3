{html_header subtitle="Add Adjustment" script=$script style=$style}
<body>
    <h1>Add Adjustment</h1>
    {$message}
    {html_error}
    <div>
        <div>
            <table>
                <thead>
                    <tr>
                        <td colspan="2">Customer</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{$name}</td>
                    </tr>
                    <tr>
                        <td>{$customer->address}</td>
                    </tr>
                    <tr>
                        <td>{route_title id=$customer->route_id}</td>
                    </tr>
                    <tr>
                        <td>{customer_type_abbr id=$customer->type_id}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <table>
                <thead>
                    <tr>
                        <td colspan="3">Adjustments</td>
                    </tr>
                </thead>
                <tbody>
{if ($count > 0)}
    {foreach from=$adjustments item=record}
                    <tr>
                        <td>
                            {adjustment_view_link id=$record->id path="../"}
                                <img src="../img/view.png" alt="View Adjustment Detail" title="View Adjustment Detail" />'
                            </a>
                        </td>
                        <td>{$record->desc}</td>
                        <td>{$record->amount}</td>
                    </tr>
    {/foreach}
{else}
                    <tr>
                        <td colspan="3">None</td>
                    </tr>
{/if}
                </tbody>
            </table>
        </div>
    </div>
    <form method="post" action="{$action}">
        <table>
            <tbody>
                <tr>
                    <td>Amount</td>
                    <td>
                        <input type="radio" name="type" value="{$CREDIT}"{$type[0]}>Credit</input>
                        <input type="radio" name="type" value="{$CHARGE}"{$type[1]}>Charge</input>
                        $<input type="text" name="amount" size="8" value="{$amount}" />
                    </td>
                </tr>
                <tr>
                    <td>Reason</td>
                    <td>
                        <input type="text" name="desc" value="{$desc}" maxLength="40" size="40" />
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
            </tbody>
        </table>
        <input type="hidden" name="cid" value="{$cid}" />
    </form>
{html_footer}
