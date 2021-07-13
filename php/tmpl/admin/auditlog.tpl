<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/auditlog.css">
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
                <label class="control" for="user">User</label>
                <select class="w3-select w3-border control" name="user" id="user">
{if ($user == 'All')}
                    <option value="0" selected>Any</option>
{else}
                    <option value="0">Any</option>
{/if}
{foreach from=$users item=rec}
    {if ($user == $rec.id)}
                    <option value="{$rec.id}" selected>{$rec.name}</option>
    {else}
                    <option value="{$rec.id}">{$rec.name}</option>
    {/if}
{/foreach}
                </select>
            </div>
            <div class="w3-row">
                <label class="control" for="keywords">Search</label>
                <input class="w3-input w3-border control" type="text" name="keywords" id="keywords" size="40" width="40" value="{$keywords}" />
            </div>
            <div class="w3-row w3-margin-top">
                {html_db_fields max=$count clear=true prefix=''}
            </div>
            <input type="hidden" name="count" value="{$count}">
{if ($addResults)}
            <table class="w3-table w3-bordered w3-striped w3-margin-top">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>When</th>
                        <th>What</th>
                    </tr>
                </thead>
                <tbody>
    {if ($count > 0)}
        {foreach from=$result item=rec}
                    <tr>
                        <td>{user_name id=$rec.user_id}</td>
                        <td>{$rec.when|date_format:'%m/%d/%Y %H:%M:%S'}</td>
                        <td>{$rec.what}</td>
                    </tr>
        {/foreach}
    {else}
                    <tr><td class="w3-center" colspan="3">None</td></tr>
    {/if}
                </tbody>
            </table>
{/if}
        </form>
    </body>
</html>
