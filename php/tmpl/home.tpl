<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="css/w3.css">
        <link rel="stylesheet" href="css/common.css">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/customertypes.css.php">
    </head>
    <body class="w3-container">
        <div class="w3-row w3-display-container" id="header">
            <div class="w3-display-bottomleft">{$path}</div>
            <div class="w3-center w3-xlarge">{$title}</div>
            <div class="w3-display-bottomright">{$username}</div>
        </div>
{include file="menu.tpl"}
        {html_error}
        <div class="w3-row">
            <div class="w3-row">
                <div class="w3-twothird w3-center">
                    <p>Active Customers:&nbsp;&nbsp;{$totalCount}</p>
                    <p>Active Customers that Still Owe:&nbsp;&nbsp;{$activeAndOwe}</p>
                    <p>Active Customers Paid-Ahead:&nbsp;&nbsp;{$activeAndAhead}</p>
                    <p>Bills Printed For {period_title id=$period_id}:&nbsp;&nbsp;{$lastBills}</p>
                </div>
                <div class="w3-third">
                    {$calendar}
                </div>
            </div>
        </div>
        <div>
            <table class="w3-table w3-bordered" id="dist">
                <caption>Customers per Delivery Type per Route</caption>
                <thead>
                    <tr>
                        <th>Type</th>
{section name=type loop=$routes}
                        <th>{$routes[type]}</th>
{/section}
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
{section name=type loop=$cpdt}
                    <tr class="dt{$cpdt[type][0]}">
	{section name=count loop=$cpdt[type]}
		{if $smarty.section.type.last}
			{if !$smarty.section.count.first}
                        <td>{$cpdt[type][count]}</td>
			{/if}
		{else}
			{if $smarty.section.count.first}
				{assign var='dt' value=$cpdt[type][count]}
			{else}
				{if $smarty.section.count.last}
                        <td>{$cpdt[type][count]}</td>
				{else}
                        <td>{$cpdt[type][count]}</td>
				{/if}
			{/if}
		{/if}
	{/section}
                    </tr>
{/section}
                </tbody>
            </table>
        </div>
        <script type="text/javascript" src="{$ROOT}js/home.js.php"></script>
    </body>
</html>
