<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/groups.css">
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
            <button class="w3-button w3-border control" name="action" value="create" onclick="{group_add_url path=$ROOT}; return false;">Add New</button>
            <table class="w3-table w3-bordered w3-striped centered">
                <thead>
                    <tr>
                        <th colspan="2">{$count}</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
{foreach from=$groups item=group}
                    <tr>
                        <td class="icon-link">
                            {group_edit_link id=$group.id path=$ROOT}
                                <img class="icon-link" src="{$ROOT}img/edit.png" alt="Edit" title="Edit" />
                            </a>
                        </td>
                        <td class="icon-link">
                            <img class="icon-link" src="{$ROOT}img/delete_d.png" />
                        </td>
                        <td>{$group.name}</td>
                    </tr>
{/foreach}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/popups/groups.js.php"></script>
    </body>
</html>
