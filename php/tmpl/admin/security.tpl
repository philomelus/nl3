<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/security.css">
    </head>
    <body class="w3-container">
        <div class="w3-row w3-display-container" id="header">
            <div class="w3-display-bottomleft">{$path}</div>
            <div class="w3-center w3-xlarge">{$title}</div>
            <div class="w3-display-bottomright">{$username}</div>
        </div>
{include file="menu.tpl"}
        {html_message}
        {html_error}
        <form class="w3-container w3-margin-top w3-center" method="post" action="{$action}">
            <div class="w3-row">
                <label class="control" for="gid">Group</label>
                <select class="w3-select w3-border control" name="gid" id="gid">
{if ($gid == 'All')}
                    <option selected>All</option>
{else}
                    <option>All</option>
{/if}
{foreach from=$groups item=group}
    {if ($gid == $group->id)}
                    <option value="{$group->id}" selected>{$group->name}</option>
    {else}
                    <option value="{$group->id}">{$group->name}</option>
    {/if}
{/foreach}
                </select>
                <div class="gutter">&nbsp;</div>
                <label class="control" for="uid">User</label>
                <select class="w3-select w3-border control" name="uid" id="uid">
{if ($uid == 'All')}
                    <option selected>All</option>
{else}
                    <option>All</option>
{/if}
{foreach from=$users item=user}
    {if ($uid == $user.id)}
                    <option value="{$user.id}" selected>{$user.name}</option>
    {else}
                    <option value="{$user.id}">{$user.name}</option>
    {/if}
{/foreach}
                </select>
            </div>
            <div class="w3-row">
                <label class="control" for="page">Page</label>
                <select class="w3-select w3-border control" name="page" id="page">
{if ($page == 'All')}
                    <option selected>All</option>
{else}
                    <option>All</option>
{/if}
{foreach from=$pages item=data}
    {if ($page == $data)}
                    <option selected>{$data}</option>
    {else}
                    <option>{$data}</option>
    {/if}
{/foreach}
                </select>
                <div class="gutter">&nbsp;</div>
                <label class="control" for="feature">Feature</label>
                <select class="w3-select w3-border control" name="feature" id="feature">
{if ($feature == 'All')}
                    <option selected>All</option>
{else}
                    <option>All</option>
{/if}
{foreach from=$features item=data}
    {if ($feature == $data.feature)}
                    <option selected>{$data.feature}</option>
    {else}
                    <option>{$data.feature}</option>
    {/if}
{/foreach}
                </select>
            </div>
            <div class="w3-row w3-margin-top">
                <button type="submit" name="action" value="update">Update</button>
                <button type="submit" name="action" value="reset">Reset</button>
                <button type="submit" name="action" value="create" onclick="{security_add_url path=$ROOT}; return false;">
                    Add New
                </button>
            </div>
            <table class="w3-table w3-bordered w3-striped w3-margin-top">
                <thead>
                    <tr>
                        <th colspan="2">{$count}</th>
                        <th>Group</th>
                        <th>User</th>
                        <th>Page</th>
                        <th>Feature</th>
                        <th>Description</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
{if ($count > 0)}
    {foreach from=$rows item=row}
                    <tr>
                        <td>
                            {security_edit_link group=$row.group_id user=$row.user_id page=$row.page feature=$row.feature path=$ROOT}
                                <img src="{$ROOT}img/edit.png" alt="Edit" title="Edit Security Setting">
                            </a>
                        </td>
                        <td>
                            <a href="{$action}?action=delete&p1={$row.group_id}&p2={$row.user_id}&p3={$row.page}&p4={$row.feature}&gid={$gid}&uid={$uid}&page={$page}&feature={$feature}" alt="Delete" title="Delete Security Setting">
                                <img src="{$ROOT}img/delete.png" alt="Delete" title="Delete Security Setting" />
                            </a>
                        </td>
                        <td>
                        <td>{group_title id=$row.group_id all=true}</td>
                        <td>{user_name id=$row.user_id all=true}</td>
                        <td>{security_page_title page=$row.page}</td>
                        <td>{$row.feature}</td>
                        <td>{security_desc page=$row.page feature=$row.feature}</td>
                        </td>
        {if ($row.allowed == 'Y')}
                        <td>Enabled</td>
        {else}
                        <td>Disabled</td>
        {/if}
                    </tr>
    {/foreach}
{else}
                    <tr><td class="w3-center" colspan="9">None</td></tr>
{/if}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/tableruler.js"></script>
        <script src="{$ROOT}js/popups/security.js.php"></script>
    </body>
</html>
