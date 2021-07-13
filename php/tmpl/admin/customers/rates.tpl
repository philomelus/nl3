<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/customers/css/rates.css">
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
            <label class="control" for="iid">End Period</label>
            <select class="w3-select w3-border control" name="iid" id="iid" onchange="this.form.submit();">
                {html_options options=$filters selected=$iid}
            </select>
            <button class="w3-button w3-border" type="submit" name="action" value="refresh">Refresh</button>
            <button class="w3-button w3-border" type="submit" name="action" value="Add New" onclick="{customer_rate_add_url path=$ROOT}; return false;">Add New</button>
            <table class="w3-table w3-bordered w3-striped w3-margin-top centered">
                <thead>
                    <tr>
                        <th colspan="2">{$count}</th>
                        <th>Type</th>
                        <th>Start Period</th>
                        <th>End Period</th>
                        <th>Rate</th>
{if $cbt != 'auto'}
                        <th>Daily Cr</th>
                        <th>Sun Cr</th>
{/if}
                    </tr>
                </thead>
                <tbody>
{section name=i loop=$rates}
                    <tr>
                        <td class="icon-link">
                            {customer_rate_view_link type=$rates[i].type_id begin=$rates[i].period_id_begin end=$rates[i].period_id_end path=$ROOT}
                                <img class="icon-link" src="{$ROOT}img/view.png" alt="V" title="View this Customer Rate" />
                            </a>
                        </td>
                        <td class="icon-link">
                            {customer_rate_edit_link type=$rates[i].type_id begin=$rates[i].period_id_begin end=$rates[i].period_id_end path=$ROOT}
                                <img class="icon-link" src="{$ROOT}img/edit.png" alt="E" title="Edit this Customer Rate" />
                            </a>
                        </td>
                        <td>{customer_type_abbr id=$rates[i].type_id}</td>
                        <td>{period_title id=$rates[i].period_id_begin}</td>
                        <td>{period_title id=$rates[i].period_id_end}</td>
                        <td>${$rates[i].rate|string_format:"%01.2f"}</td>
	{if $cbt != 'auto'}
                        <td>${$rates[i].daily_credit|string_format:"%01.2f"}</td>
                        <td>${$rates[i].sunday_credit|string_format:"%01.2f"}</td>
	{/if}
                    </tr>
{/section}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/popups/customers/rates.js.php"></script>
        <script src="{$ROOT}js/popups/periods.js.php"></script>
        <script src="{$ROOT}js/tableruler.js"></script>
    </body>
</html>

