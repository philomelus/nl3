<div>
{include file="menu.tpl"}
    <table>
        <tr>
            <td>
                <input type="radio" name="what" value="{$WHAT_BEHIND1}"{$one}>Behind 1 period</input>
            </td>
        </tr>
        <tr>
            <td>
                <input type="radio" name="what" value="{$WHAT_BEHINDMANY}"{$many}>Behind more than 1 period</input>
            </td>
        </tr>
        <tr>
            <td>
                <input type="radio" name="what" value="{$WHAT_NOPAYMENTS}"{$noPayments}>Behind more than 1 period and have No Payments</input>
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" name="action" value="Update" />
            </td>
        </tr>
    </table>
</div>
