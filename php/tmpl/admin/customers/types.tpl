<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/customers/css/types.css">
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
        <form class="w3-container w3-margin-top w3-center" method="post" action="{$action}">
            <button class="w3-button w3-border" type="submit" name="action" value="refresh">Refresh</button>
            <button class="w3-button w3-border" type="submit" name="action" value="create" onclick="{customer_type_add_url path=$ROOT}; return false;">Add New</button>
            <table class="w3-table w3-bordered w3-striped w3-margin-top centered">
                <thead>
                    <tr>
                        <th colspan="2">{$count}</th>
                        <th>Abbr.</th>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Visible</th>
                        <th>Change</th>
                        <th>Watch</th>
                        <th>Su</th>
                        <th>Mo</th>
                        <th>Tu</th>
                        <th>We</th>
                        <th>Th</th>
                        <th>Fr</th>
                        <th>Sa</th>
                    </tr>
                </thead>
                <tbody>
{section name=i loop=$types}
                    <tr>
                        <td class="icon-link">
                            {customer_type_view_link id=$types[i].id path=$ROOT}
                                <img class="icon-link" src="{$ROOT}img/view.png" alt="V" title="View Type {$types[i].id}" />
                            </a>
                        </td>
                        <td class="icon-link">
                            {customer_type_edit_link id=$types[i].id path=$ROOT}
                                <img class="icon-link" src="{$ROOT}img/edit.png" alt="E" title="Edit Type {$types[i].id}" />
                            </a>
                        </td>
                        <td>{$types[i].abbr}</td>
                        <td>{$types[i].name}</td>
                        <td>{$types[i].color|color}</td>
                        <td>{$types[i].visible|boolean}</td>
                        <td>{$types[i].newChange|boolean}</td>
                        <td>{$types[i].watchStart|boolean}</td>
                        <td>{if $types[i].su == 'Y'}xx{else}&nbsp;{/if}</td>
                        <td>{if $types[i].mo == 'Y'}xx{else}&nbsp;{/if}</td>
                        <td>{if $types[i].tu == 'Y'}xx{else}&nbsp;{/if}</td>
                        <td>{if $types[i].we == 'Y'}xx{else}&nbsp;{/if}</td>
                        <td>{if $types[i].th == 'Y'}xx{else}&nbsp;{/if}</td>
                        <td>{if $types[i].fr == 'Y'}xx{else}&nbsp;{/if}</td>
                        <td>{if $types[i].sa == 'Y'}xx{else}&nbsp;{/if}</td>
                    </tr>
{/section}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/popups/customers/types.js.php"></script>
        <script src="{$ROOT}js/tableruler.js"></script>
    </body>
</html>

