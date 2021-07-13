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
                            <p>Step 5: Combine</p>
                            <p>Now that all the customers have been billed successfully, the customers
                                who's accounts should be billed on a single bill must be combined.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" name="action" value="Update Combined Accounts"{$generated} />
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
