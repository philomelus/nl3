<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/users.css">
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
            <button type="submit" name="action" value="create" onclick="{user_add_url path=$ROOT}; return false;">Add New</button>
            <table class="w3-table w3-bordered w3-striped w3-margin-top centered">
                <thead>
                    <tr>
                        <th colspan="2"></th>
                        <th>Group</th>
                        <th>Name</th>
                        <th>Logon</th>
                    </tr>
                </thead>
                <tbody>
{foreach from=$users item=rec}
                    <tr>
                        <td class="icon-link">
                            {user_edit_link id=$rec.id path=$ROOT}
                                <img class="icon-link" src="{$ROOT}img/edit.png" alt="Edit" title="Edit User" />
                            </a>
                        </td>
                        <td class="icon-link">
                            <a href="{$action}?action=delete&p1={$rec.id}" alt="Delete" title="Delete User">
                                <img class="icon-link" src="{$ROOT}img/delete.png" alt="Delete" title="Delete User" />
                            </a>
                        </td>
                        <td>{group_title id=$rec.group_id}</td>
                        <td>{$rec.name}</td>
                        <td>{$rec.login}</td>
                    </tr>
{/foreach}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/popups/users.js.php"></script>
    </body>
</html>
