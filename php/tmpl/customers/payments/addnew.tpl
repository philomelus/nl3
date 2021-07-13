<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}css/customertypes.css.php">
        <link rel="stylesheet" href="{$ROOT}customers/payments/css/addnew.css">
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
                <label class="control" for="cid">Customer ID</label>
                <div class="control">
                    <input class="w3-input w3-border control" type="text" name="cid" id="cid" maxlength="8" size="6" value="{$cid}">
                    <button name="action" value="Show"><img src="{$ROOT}img/refresh.png" alt="R"/></button>
                </div>
            </div>
{if count($custInfo) > 0}
	    <div class="w3-row">
		<table class="w3-table w3-border info">
		    <tbody>
			<tr>
			    <td class="{$custInfo.color}">{$custInfo.name}</td>
			    <td class="{$custInfo.color}">{$custInfo.address}</td>
			    <td class="{$custInfo.color}">{$custInfo.telephone}</td>
			    <td class="{$custInfo.color}">{$custInfo.route}</td>
			    <td class="{$custInfo.color}">{$custInfo.type}</td>
			</tr>
		</table>
	    </div>
{/if}	    
            <div class="w3-row">
                <label class="control" for="type">Type</label>
                <div class="control">
                    {html_select name="type" values=$paymentOptions select=$type)} 
                </div>
            </div>
            <div class="w3-row">
                <label class="control" for="id">ID</label>
                <div class="control">
                    <input class="w3-input w3-border control" type="text" name="id" maxlength="30" value="{$id}">
                    (Check Number)
                </div>
            </div>
            <div class="w3-row">
                <label class="control" for="amount">Amount</label>
                <div class="control">
                    <input class="w3-input w3-border control" type="text" name="amount" size="8" value="{$amount}">
                </div>
            </div>
            <div class="w3-row">
                <label class="control" for="tip">Tip</label>
                <div class="control">
                    <input class="w3-input w3-border control" type="text" name="tip" size="8" value="{$tip}">
                </div>
            </div>
            <div class="w3-row">
                <label class="control vtop" for="notes">Notes</label>
                <div class="control">
                    <textarea class="w3-input w3-border control" name="notes" rows="4" cols="40">{$notes}</textarea>
                </div>
            </div>
            <div class="w3-row">
                <button type="submit" name="action" value="submit">Submit</button>
            </div>
        </form>
        <script src="{$ROOT}js/popups/customers.js.php"></script>
        <script src="{$ROOT}customers/payments/js/addnew.js"></script>
    </body>
</html>
