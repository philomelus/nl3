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
                    <td><label>Date</label></td>
                    <td>
                        <input type="text" name="startm" value="{$startMonth}" size="2" maxLength="2" />
                        /
                        <input type="text" name="startd" value="{$startDay}" size="2" maxLength="2" />
                        /
                        <input type="text" name="starty" value="{$startYear}" size="4" maxLength="4" />
                    </td>
                    <td><label>To</label></td>
                    <td>
                        <input type="text" name="endm" value="{$endMonth}" size="2" maxLength="2" />
                        /
                        <input type="text" name="endd" value="{$endDay}" size="2" maxLength="2" />
                        /
                        <input type="text" name="endy" value="{$endYear}" size="4" maxLength="4" />
                    </td>
                </tr>
                <tr>
                    <td><label>Route</label></td>
                    <td>{html_routes name="rid" selected=$rid}</td>
                <tr>
                    <td><label>Search Text</label></td>
                    <td colspan="3">
                        <input type="text" name="search" value="{$search}" size="40" maxlength="255" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <input type="submit" name="action" value="Update" />
                    </td>
                </tr>
            </table>
            <hr />
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
        </form>
        {$result}
	</div>
</div>
