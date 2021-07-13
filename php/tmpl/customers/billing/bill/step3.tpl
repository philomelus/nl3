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
                            <p>Step 3: Bill</p>
                            <p>You need to progress through pushing each of the buttons below, one at a time. Each
                                button will disable AFTER the bills for the customers it represents have been
                                completed billing.
                            </p>
                            <p>When you are completed, the current status will change, and you can move on to the
                                next step.</p>                            
                            <p>WARNING! DO NOT attempt to execute the buttons
                                simultaneously!
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tbody>
{foreach from=$ranges item=range}
                                    <tr>
                                        <td>
    {if $range[2]}
                                            <button name="action" disabled="disabled" value="{$range[0]}">
    {else}
                                            <button name="action" value="{$range[0]}">
    {/if}
                                                {$range[1]}
                                            </button>
                                        </td>
                                    </tr>
{/foreach}
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
            <input type="hidden" name="m3" value="{$m3}" />
            <input type="hidden" name="last" value="">
        </form>
	</div>
</div>
