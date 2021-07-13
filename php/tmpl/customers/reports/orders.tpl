<div>
{include file="menu.tpl"}
	<div>
{if (isset($message) && !empty($message))}
        {$message}
{/if}
        {html_error}
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table>
                <tr>
                    <td>Week that includes</td>
                    <td>
                        {html_date prefix=date}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="action" value="Update" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
        </form>
	</div>
</div>
