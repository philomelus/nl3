<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/customers/css/billing.css">
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
                <label class="control" for="fs-daily">Flag Stop - Daily</label>
                $<input class="w3-input w3-border control" type="text" name="fs-daily" id="fs-daily" size="6" value="{$fsDaily}">
            </div>
            <div class="w3-row">
                <label class="control">Flag Stop - Sunday</label>
                $<input class="w3-input w3-border control" type="text" name="fs-sunday" size="6" value="{$fsSunday}">
            </div>
            <div class="w3-row">
                <label class="control" for="billing-minimum">Print Bills Owing At Least</label>
                $<input class="w3-input w3-border control" type="text" name="billing-minimum" id="billing-minimum" size="6" value="{$billingMinimum}" />
            </div>
            <div class="w3-row">
                <label class="control" for="flag-stop-billing-minimum">Print Flag Stop Bills Owing At Least</label>
                $<input class="w3-input w3-border control" type="text" name="flag-stop-billing-minimum" id="flag-stop-billing-minimum" size="6" value="{$flagStopBillingMinimum}" />
            </div>
            <div class="w3-row w3-margin-top">
                <button type="submit" name="action" value="save">Save Changes</button>
            </div>
        </form>
    </body>
</html>
