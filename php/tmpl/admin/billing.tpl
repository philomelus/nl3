<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}admin/css/billing.css">
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
        <form class="w3-container w3-center" method="post" action="{$action}">
            <div class="w3-row">
                <label class="control" for="client-name">Name</label>
                <input class="w3-input w3-border control" type="text" name="client-name" id="client-name" size="25" value="{$clientName}" />
            </div>
            <div class="w3-row">
                <label class="control" for="client-address-1">Address</label>
                <input class="w3-input w3-border control" type="text" name="client-address-1" id="client-address-1" size="25" value="{$clientAddress1}" />
            </div>
            <div class="w3-row">
                <label class="control" for="client-address-2">City, State, Zip</label>
                <input class="w3-input w3-border control" type="text" name="client-address-2" id="client-address-2" size="25" value="{$clientAddress2}" />
            </div>
            <div class="w3-row">
                <label class="control" for="client-telephone">Telephone</label>
                <input class="w3-input w3-border control" type="tel" name="client-telephone" id="client-telephone" value="{$clientTelephone}" placeholder="(000) 000-0000" />
            </div>
            <input class="w3-margin-top" type="submit" name="action" value="Save Changes" />
            <input type="hidden" name="client-name-org" value="{$clientNameOrg}" />
            <input type="hidden" name="client-address-1-org" value="{$clientAddress1Org}" />
            <input type="hidden" name="client-address-2-org" value="{$clientAddress2Org}" />
            <input type="hidden" name="client-telephone-org" value="{$clientTelephoneOrg}" />
        </form>
    </body>
</html>
