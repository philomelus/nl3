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
                            <p>Step 2: Global Message</p>
                            <p>If you want all customers without a billing message to have a common message,
                                then enter the message below, and push the save button.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table>
                                <tbody>
                                    <caption>Global Message</caption>
                                    <tr>
                                        <td>Line 1</td>
                                        <td>
                                            <input type="text" name="line1" maxlength="33" size="40" value="{$line1}"{$pending} />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Line 2</td>
                                        <td>
                                            <input type="text" name="line2" maxlength="33" size="40" value="{$line2}"{$pending} />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Line 3</td>
                                        <td>
                                            <input type="text" name="line3" maxlength="33" size="40" value="{$line3}"{$pending} />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Line 4</td>
                                        <td>
                                            <input type="text" name="line4" maxlength="33" size="40" value="{$line4}"{$pending} />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="action" value="Save"{$pending} />
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
