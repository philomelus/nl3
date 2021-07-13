<div>
{include file="menu.tpl"}
    <table>
        <tr>
            <td>
                <input type="radio" name="count" value="one"{$count[0]}>
                    Customers Ahead 1 Period
                </input>
            </td>
        </tr>
        <tr>
            <td>
                <input type="radio" name="count" value="many"{$count[1]}>
                    Customers Ahead
                    <input type="text" name="many" value="{$many}" size="2" />
                    Periods
                </input>
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" name="action" value="Update" />
            </td>
        </tr>
    </table>
</div>
