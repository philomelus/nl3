<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/periods.css">
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
                <select class="w3-select w3-border control" onchange="this.form.submit();" name="filter">
{foreach from=$years item=year}
    {if ($date == $year)}
                    <option selected>{$year}</option>
    {else}
                    <option>{$year}</option>
    {/if}
{/foreach}
                </select>
                <button class="w3-button w3-border" type="submit" name="action" value="refresh">
                    Refresh <img src="{$ROOT}img/refresh.png">
                </button>
                <button class="w3-button w3-border" type="submit" name="action" value="create" onclick="{period_add_url path=$ROOT}; return false;">
                    Add New
                </button>
            </div>
            <table class="w3-table w3-bordered w3-striped">
                <thead>
                    <tr>
                        <th>{$count}</th>
                        <th>Title</th>
                        <th>Changes</th>
                        <th>Bill</th>
                        <th>Display</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
{foreach from=$periods item=period}
                    <tr>
                        <td>
                            {period_edit_link id=$period.$P_PERIOD path=$ROOT}
                                <img src="{$ROOT}img/edit.png" alt="Edit" title="Edit Period" />
                            </a>
                        </td>
                        <td>{$period.$P_TITLE}</td>
                        <td>{$period.$P_START|date_format:'%m/%d/%Y'} - {$period.$P_END|date_format:'%m/%d/%Y'}</td>
                        <td>{$period.$P_BILL|date_format:'%m/%d/%Y'}</td>
                        <td>{$period.$P_DSTART|date_format:'%m/%d/%Y'} - {$period.$P_DEND|date_format:'%m/%d/%Y'}</td>
                        <td>{$period.$P_DUE|date_format:'%m/%d/%Y'}</td>
                    </tr>
{/foreach}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/tableruler.js"></script>
        <script src="{$ROOT}js/popups/periods.js.php"></script>
    </body>
</html>
