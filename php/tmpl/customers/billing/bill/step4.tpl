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
    						<p>Step 4: Repair</p>
	    					<p>You now must repair the accounts of any customers who failed to bill
		    				   correctly (if any), and then re-bill them.
                            </p>
                            <p>You can use the <a href="customers.php?menu=4&amp;submenu=2" target="_blank">Log Page</a>
                               to determine if there were any failures.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>Customer ID
                            <input type="text" name="customer_id" size="6" value="{$customer_id}" />
                            <button type="submit" onclick="JavaScript:ViewCustomer(); return false;">
                                <img src="img/view.png" alt="V" />
                            </button>
                            <button type="submit" onclick="JavaScript:EditCustomer(); return false;">
                                <img src="img/edit.png" alt="E" />
                            </button>
                            <input type="submit" name="action" value="Re-Bill"{$generated} />
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
            <input type="hidden" name="m3" value="{$m3}" />
        </form>
        {$billResult}
	</div>
</div>
