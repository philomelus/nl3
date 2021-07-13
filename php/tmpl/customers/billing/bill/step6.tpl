<div>
{include file="menu.tpl"}
	<div>
{if (isset($message) && !empty($message))}
        {$message}
{/if}
        {html_error}
        <form method="post" action="{$action}">
            <table>
                <tbody>
                    <tr>
                        <td>
                            Billing Status:&nbsp;<span>{$dstate}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Current Period:&nbsp;<span>{period_title id=$period}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Step 6: Download Mail Merge Accounts</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Order of customers records:<br/>
                            <input type="radio" name="sort" value="0"{$sort[0]}>Customer ID</input>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="sort" value="1"{$sort[1]}>Delivery Order</input>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="sort" value="2"{$sort[2]}>Zip Code</input>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            Customers To Download:<br/>
                            <input name="type" type="radio" value="all"{$generated}{$type[0]}>All Exported Customers</input>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="type" type="radio" value="list"{$generated}{$type[1]}>Customers Specified Below</input>
                            <div>
                                <span>Add customer ids separated by comma (1,45,669,...)<br />
                                Add ranges by separating by - (1-5, 9, 231-233, 89, 91,...)</span><br />
                                <textarea name="ids" rows="4" cols="60"{$generated}></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" name="action" value="Download"{$generated} />
                        </td>
                    </tr>
                </tbody>
            </table>		
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
            <input type="hidden" name="m3" value="{$m3}" />
        </form>
	</div>
</div>
