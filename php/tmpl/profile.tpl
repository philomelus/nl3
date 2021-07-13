<div>
{include file="menu.tpl"}
	<div>
{if (isset($message) && !empty($message))}
        {$message}
{/if}
        <form method="post" action="{$action}">
            <table>
                <tbody>
                    <tr>
                        <td>
                            Filter
                            <select name="key1" onchange="JavaScript:update_keys(); this.form.submit();">
{foreach from=$keys1 item=val}
    {if ($key1 == $val)}
                                <option selected="selected">{$val}</option>
    {else}
                                <option>{$val}</option>
    {/if}
{/foreach}
                            </select>
                            <select name="key2" onchange="JavaScript:update_keys(); this.form.submit();"{$keys[2]}>
{foreach from=$keys2 item=val}
    {if ($key2 == $val)}
                                <option selected="selected">{$val}</option>
    {else}
                                <option>{$val}</option>
    {/if}
{/foreach}
                            </select>
                            <select name="key3" onchange="JavaScript:update_keys(); this.form.submit();"{$keys[3]}>
{foreach from=$keys3 item=val}
    {if ($key3 == $val)}
                                <option selected="selected">{$val}</option>
    {else}
                                <option selected="selected">{$val}</option>
    {/if}
{/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {html_db_fields limit=$limit offset=$offset max=$count path=''}
                            <input type="submit" name="action" value="Reset" />
                            <input type="submit" name="action" value="Add New" onclick="{$profileAddURL}; return false;" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">{$count}</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
{if $count > 0}
    {foreach from=$data item=datum}
                    <tr>
                        <td>
        {if $datum.edit}
                            <a href="{$datum.editLink}" alt="Edit" title="Edit">
                                <img src="img/edit.png" alt="Edit" title="Edit" />
                            </a>
        {else}
                            <img src="img/edit_d.png" alt="D" title="Delete" />
        {/if}
                        </td>
                        <td>
        {if ($datum.delete)}
                            <a href="{$datum.deleteLink}" alt="Delete" title="Delete">
                                <img src="img/delete.png" alt="Delete" title="Delete" />
                            </a>
        {else}
                            <img src="img/delete_d.png" alt="D" title="Delete" />
        {/if}
                        </td>
                        <td>{$datum.key}</td>
                        <td>{$datum.desc}</td>
                        <td>{$datum.value}</td>
                    </tr>
    {/foreach}
{else}
                    <tr><td colspan="5">None</td></tr>
{/if}
                </tbody>
            </table>
        </form>
	</div>
</div>
