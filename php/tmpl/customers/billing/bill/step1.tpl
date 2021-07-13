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
                            <p>Step 1: Close Period</p>
                            <p>Closing the period increments the active period and resets the billing status.  The signifcance
                                of the period change is that from the moment that happens, all new payments will be marked as
                                part of that new period.
                            </p>
                            <p>Additionally, it is used for some sanity checks through out, to make
                                sure some old bugs don't somehow creep back in.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" name="action" value="Close Period"{$complete} />
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
        </form>
	</div>
</div>
