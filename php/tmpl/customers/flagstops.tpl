<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}customers/css/flagstops.css">
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
        <table class="w3-table w3-bordered w3-striped centered">
            <thead>
                <tr>
                    <th colspan="3">{$count}</th>
                    <th>CustID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Telephone</th>
                    <th>Rt</th>
                </tr>
            </thead>
            <tbody>
{section name=i loop=$customers}
                <tr>
                    <td class="icon-link">
                        <a href="JavaScript:PopupStartStop('{$ROOT}customers/startstoppopup.php?cid={$customers[i].id}','PPFSSS{$customers[i].id}')">
                            <img src="{$ROOT}img/stopstarts.png" alt="S" title="Add Start and Stop for Customer {$customers[i].id}" />
                        </a>
                    </td>
                    <td class="icon-link">
                        {customer_view_link id=$customers[i].id path=$ROOT}
                            <img src="{$ROOT}img/view.png" alt="V" title="View Customer {$customers[i].id}" />
                        </a>
                    </td>
                    <td class="icon-link">
                        {customer_edit_link id=$customers[i].id path=$ROOT}
                            <img src="{$ROOT}img/edit.png" alt="E" title="Edit Customer {$customers[i].id}" />
                        </a>
                    </td>
                    <td>{$customers[i].id|customer_id}</td>
                    <td>{customer_name first=$customers[i].firstName last=$customers[i].lastName order=lf}</td>
                    <td>{$customers[i].address}</td>
                    <td>{$customers[i].telephone}</td>
                    <td>{route_title id=$customers[i].route_id}</td>
                </tr>
{/section}
            </tbody>
        </table>
        <script src="{$ROOT}js/tablesort.js"></script>
        <script src="{$ROOT}js/popups/config.js.php"></script>
        <script src="{$ROOT}js/popups/customers.js.php"></script>
        <script src="{$ROOT}customers/js/popups.js.php"></script>
    </body>
</html>
