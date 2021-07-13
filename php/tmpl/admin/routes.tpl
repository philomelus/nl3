<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/routes.css">
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
            <div>
                <button class="w3-button w3-border" type="submit" name="action" value="refresh">Refresh</button>
                <button class="w3-button w3-border" type="submit" name="action" value="create" onclick="{route_add_url path=$ROOT}; return false;">Add New</button>
            </div>
            <table class="w3-table w3-bordered w3-striped w3-margin-top centered">
                <thead>
                    <th>{$count}</th>
                    <th>Title</th>
                    <th>Active</th>
                </thead>
                <tbody>
{foreach from=$routes item=route}
                    <tr>
                        <td>
                            {route_edit_link id=$route->id path=$ROOT}
                                <img src="{$ROOT}img/edit.png" alt="Edit" title="Edit Route" />
                            </a>
                        </td>
                        <td>{$route->title}</td>
    {if $route->active == 'Y'}
                        <td>Yes</td>
    {else}
                        <td>No</td>
    {/if}
                    </tr>
{/foreach}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/tableruler.js"></script>
        <script src="{$ROOT}js/popups/routes.js.php"></script>
    </body>
</html>
