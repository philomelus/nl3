<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/config.css">
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
        {errors}
        <form class="w3-container w3-center w3-margin-top" method="post" action="{$action}">
            <div class="w3-row">
                <label class="control" style="display: inline-block;">Filter</label>
                <select class="w3-select w3-border control" name="key1" onchange="JavaScript:update_keys();">
{if ($key1 == 'All')}
                    <option selected>All</option>
{else}
                    <option>All</option>
{/if}
{foreach from=$keys1 item=key}
    {if ($key == $key1)}
                    <option selected>{$key}</option>
    {else}
                    <option>{$key}</option>
    {/if}
{/foreach}
                </select>
{if ($key1 == 'All')}
                <select class="w3-select w3-border control" name="key2" onchange="JavaScript:update_keys();" disabled>
{else}
                <select class="w3-select w3-border control" name="key2" onchange="JavaScript:update_keys();">
{/if}
{if ($key2 == 'All')}
                    <option selected>All</option>
{else}
                    <option>All</option>
{/if}
{foreach from=$keys2 item=key}
    {if ($key2 == $key)}
                    <option selected>{$key}</option>
    {else}
                    <option>{$key}</option>
    {/if}
{/foreach}
                </select>
{if ($key1 == 'All' || $key2 == 'All')}
                <select class="w3-select w3-border control" name="key3" onchange="JavaScript:update_keys();" disabled>
{else}
                <select class="w3-select w3-border control" name="key3" onchange="JavaScript:update_keys();">
{/if}
{if ($key3 == 'All')}
                    <option selected>All</option>
{else}
                    <option>All</option>
{/if}
{foreach from=$keys3 item=key}
    {if ($key == $key3)}
                    <option selected>{$key}</option>
    {else}
                    <option>{$key}</option>
    {/if}
{/foreach}
                </select>
                <button class="w3-button w3-border" name="action" value="refresh">Refresh</button>
                <button class="w3-button w3-border" name="action" value="clear">Clear</button>
                <button class="w3-button w3-border" name="action" value="create" onclick="{config_add_url path=$ROOT}; return false;">Add New</button>
            </div>
{if ($addResults)}
            <table class="w3-table w3-bordered w3-striped">
                <thead>
                    <tr>
                        <th colspan="2">{$count}</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
    {if ($count > 0)}
        {foreach from=$records item=data}
                    <tr>
                        <td>
            {if ($data.readonly)}
                            <img src="{$ROOT}img/edit_d.png" />
            {else}
                            <a href="{config_edit_url key=$data.key path=$ROOT}">
                                <img src="{$ROOT}img/edit.png" alt="Edit" title="Edit" />
                            </a>
            {/if}
                        </td>
                        <td>
            {if ($data.readonly || $data.required)}
                            <img src="{$ROOT}img/delete_d.png" />
            {else}
                            <a href="{$action}?action=delete&amp;key={$data.key}">
                                <img src="{$ROOT}img/delete.png" alt="Delete" title="Delete Setting" />
                            </a>
            {/if}
                        </td>
                        <td>{$data.key}</td>
                        <td>{$data.desc}</td>
                        <td>{$data.value}</td>
                    </tr>
        {/foreach}
    {else}
                    <tr><td class="w3-center" colspan="6">None</td></tr>
    {/if}
                </tbody>
            </table>
{/if}
        </form>
        <script type="text/javascript" src="{$ROOT}js/tableruler.js"></script>
        <script type="text/javascript" src="{$ROOT}js/popups/config.js.php"></script>
        <script type="text/javascript" src="{$ROOT}admin/js/config.js"></script>
    </body>
</html>
