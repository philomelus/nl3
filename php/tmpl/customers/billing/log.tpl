<div>
{include file="menu.tpl"}
	<div>
{if (isset($message) && !empty($message))}
        {$message}
{/if}
        {html_error}
        <form method="post" action="{$action}">
            <table>
                <tr>
                    <td colspan="2">
                        Customer ID
                        <input type="text" name="cid" value="<?php echo $CID ?>" size="6" maxlength="6" />
                        <span>
                            Period
                            {html_periods name="iid" selected=$iid any=true}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Generated
                        <select name="date">
                            <option value="&gt;"{$generated[0]}>After</option>
                            <option value="&lt;"{$generated[1]}>Before<option>
                        </select>
                        <input type="text" name="datem" size="2" value="{$month}" maxlength="2" />
                        /
                        <input type="text" name="dated" size="2" value="{$day}" maxlength="2" />
                        /
                        <input type="text" name="datey" size="4" value="{$year}" maxlength="4" />
                        &nbsp;
                        <input type="text" name="timeh" value="{$hour}" maxlength="2" size="2" />
                        :
                        <input type="text" name="timem" value="{$minute}" maxlength="2" size="2" />
                        :
                        <input type="text" name="times" value="{$second}" maxlength="2" size="2" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="checkbox" name="failed" value="1"{$failures}>Only Failures</input>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><hr /></td>
                </tr>
                <tr>
                    <td>
                        Show
                        <input type="text" name="count" value="{$count}" size="4" maxlength="8" />
                        from
                        <input type="text" name="offset" value="{$offset}" size="4" maxlength="8" />
                        <input type="submit" name="action" value="&lt;" />
                        <input type="submit" name="action" value="&gt;" />
                    </td>
                    <td>
                        <input type="submit" name="action" value="Update" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
        </form>
        {$result}
	</div>
</div>
