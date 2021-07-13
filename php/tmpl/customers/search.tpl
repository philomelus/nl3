<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}css/customertypes.css.php">
        <link rel="stylesheet" href="{$ROOT}customers/css/search.css">
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
            <div class="w3-row">
                <div class="left">
                    <label class="control" for="cid">Customer ID</label>
                    <input class="w3-input w3-border control" type="text" name="cid" id="cid" size="6" value="{$cid}" tabindex="1">
                    <button class="w3-button w3-border" type="submit" onclick="JavaScript:ViewCustomer(); return false;" tabindex="2"><img src="{$ROOT}img/view.png" alt="V"></button><button class="w3-button w3-border" type="submit" onclick="JavaScript:EditCustomer(); return false;" tabindex="3"><img src="{$ROOT}img/edit.png" alt="E"></button>
                </div>
                <div class="gutter">&nbsp;</div>
                <div class="right">
                    <label class="control" for="route">Route</label>
                    {html_routes name='route' selected=$route any=true tabindex="8"}
                </div>
            </div>
            <div class="w3-row">
                <div class="left">
                    <label class="control" for="telephone">Telephone</label>
                    <input class="w3-input w3-border control" type="text" name="telephone" id="telephone" value="{$telephone}" size="30" tabindex="4">
                </div>
                <div class="gutter">&nbsp;</div>
                <div class="right">
                    <label class="control" for="type">Type</label>
                    {html_customer_types name="type" selected=$type any=true tabindex="9"}
                </div>
            </div>
            <div class="w3-row">
                <div class="left">
                    <label class="control" for="name">Name</label>
                    <input class="w3-input w3-border control" type="text" name="name" id="name" value="{$name}" size="30" tabindex="5" />
                </div>
                <div class="gutter">&nbsp;</div>
                <div class="right">
                    <label class="control" for="routeList">In Route List</label>
                    {html_yesnoignore name="routeList" selected=$routeList tabindex="10"}
                </div>
            </div>
            <div class="w3-row">
                <div class="left">
                    <label class="control" for="address">Address</label>
                    <input class="w3-input w3-border control" type="text" name="address" id="address" value="{$address}" size="30" tabindex="6" />
                </div>
                <div class="gutter">&nbsp;</div>
                <div class="right">
                    <label class="control" for="active">In Billing</label>
                    {html_yesnoignore name="active" selected=$active tabindex="11"}
                </div>
            </div>
            <div class="row">
                <div class="left">
                    <label class="control" for="postal">Zip</label>
                    <input class="w3-input w3-border control" type="text" name="postal" id="postal" value="{$postal}" size="10" tabindex="7" />    
                </div>
                <div class="gutter">&nbsp;</div>
                <div class="right">&nbsp;</div>
            </div>
            <div class="w3-row w3-margin-top">
                {html_db_fields limit=$limit offset=$offset max=$count clear=true path=$ROOT}
            </div>
        </form>
{if ($doResults)}
        <table class="w3-table w3-bordered w3-margin-top">
            <thead>
                <tr>
                    <th colspan="7">{$count}</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Telephone</th>
                    <th>Route</th>
                    <th>Type</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
    {if ($count > 0)}
    	{section name=i loop=$customers}
                <tr class="{$customers[i].type_id|customer_type_color}">
                    <td class="icon-link">
                        <a href="{$ROOT}customers/payments.phpi?cid={$customers[i].id}">
                            <img src="{$ROOT}img/payments.png" alt="P" title="Add payment for Customer" />
                        </a>
                    </td>
                    <td class="icon-link">
            {if $customers[i].type_id == $flagStopId}
                        <a href="JavaScript:PopupStopStart('{$ROOT}customers/startstoppopup.php?cid={$customers[i].id}','PPFSSS{$customers[i].id}')">
                            <img src="{$ROOT}img/stopstarts.png" alt="S" title="Add Start and/or Stop for Customer" />
                        </a>
            {else}
                        <a href="JavaScript:PopupStopStart('{$ROOT}customers/stopstartpopup.php?cid={$customers[i].id}','PPFSSS{$customers[i].id}')">
                            <img src="{$ROOT}img/stopstarts.png" alt="S" title="Add Stop and/or Start for Customer" />
                        </a>
            {/if}
                    </td>
                    <td class="icon-link">
                        <a href="JavaScript:PopupComplaint('{$ROOT}customers/complaintpopup.php?cid={$customers[i].id}','PPCP{$customers[i].id}')">
                            <img src="{$ROOT}img/complaints.png" alt="C" title="Add Complaint by Customer" />
                        </a>
                    </td>
                    <td class="icon-link">
                        <a href="JavaScript:PopupAdjustment('{$ROOT}customers/adjustmentpopup.php?cid={$customers[i].id}','PPAJ{$customers[i].id}')">
                            <img src="{$ROOT}img/adjustments.png" alt="A" title="Add Adjustment for Customer">
                        </a>
                    </td>
                    <td class="icon-link">
                        <a href="JavaScript:PopupChangeType('{$ROOT}customers/changetypepopup.php?cid={$customers[i].id}','PPCT{$customers[i].id}')">
                            <img src="{$ROOT}img/changes.png" alt="T" title="Change Customer Delivery Type">
                        </a>
                    </td>
                    <td class="icon-link">
                        {customer_view_link id=$customers[i].id path=$ROOT}
                            <img src="{$ROOT}img/view.png" alt="V" title="View Customer {$customers[i].id}">
                        </a>
                    </td>
                    <td class="icon-link">
                        {customer_edit_link id=$customers[i].id path=$ROOT}
                            <img src="{$ROOT}img/edit.png" alt="E" title="Edit Customer {$customers[i].id}">
                        </a>
                    </td>
                    <td>{$customers[i].id|customer_id}</td>
                    <td>{customer_name first=$customers[i].firstName last=$customers[i].lastName order=fl}</td>
                    <td>{$customers[i].address}</td>
                    <td>{$customers[i].telephone}</td>
                    <td>{route_title id=$customers[i].route_id}</td>
                    <td>{customer_type_abbr id=$customers[i].type_id}</td>
            {if ($customers[i].billBalance < 0)}
                    <td class="w3-text-red">${$customers[i].billBalance}</td>
            {else}
                    <td>${$customers[i].billBalance}</td>
            {/if}
                </tr>
        {/section}
    {else}
                <tr><td class="w3-center" colspan="14">None</td></tr>
    {/if}
            </tbody>
        </table>
{/if}
        <script src="{$ROOT}js/popups/users.js.php"></script>
        <script src="{$ROOT}js/tablesort.js"></script>
        <script src="{$ROOT}js/popups/customers.js.php"></script>
        <script src="{$ROOT}js/jquery.js"></script>
        <script src="{$ROOT}js/jquery.autotab.js"></script>
        <script src="{$ROOT}js/default.js"></script>
        <script src="{$ROOT}customers/js/popups.js.php"></script>
        <script src="{$ROOT}customers/js/lookup.js"></script>
    </body>
</html>

