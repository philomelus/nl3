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
                        <td>Active</td>
                        <td>
                            <input type="radio" name="active" value="Y"{$active[1]}>Yes</input>
                            <input type="radio" name="active" value="N"{$active[2]}>No</input>
                            <input type="radio" name="active" value="I"{$active[0]}>Ignore</input>
                        </td>
                    </tr>
                    <tr>
                        <td>Route List</td>
                        <td>
                            <input type="radio" name="routeList" value="Y"{$routeList[1]}>Yes</input>
                            <input type="radio" name="routeList" value="N"{$routeList[2]}>No</input>
                            <input type="radio" name="routeList" value="I"{$routeList[0]}>Ignore</input>
                        </td>
                    </tr>
                    <tr>
                        <td>Last Stopped</td>
                        <td>
                            <input type="string" name="stop" value="{$stop}" size="2" />
                            Periods Ago
                        </td>
                    </tr>
                    <tr>
                        <td>Only Pending Stops?</td>
                        <td>
                            <input type="radio" name="pending" value="Y"{$pending[1]}>Yes</input>
                            <input type="radio" name="pending" value="N"{$pending[2]}>No</input>
                            <input type="radio" name="pending" value="I"{$pending[0]}>Ignore</input>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="action" value="Update" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
        </form>
	</div>
</div>
