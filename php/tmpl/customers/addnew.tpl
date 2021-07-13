<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}customers/css/addnew.css">
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
            <div>Required Fields In <span class="w3-text-red">Red</span></div>
            <table class="w3-table w3-margin-top centered">
                <caption>Delivery</caption>
                <tr>
                    <td><label class="w3-text-red control" for="name_title">Name</label></td>
                    <td>{html_name name=name}</td>
                </tr>
                <tr>
                    <td><label class="control" for="alt_name_title">Alternate Name</label></td>
                    <td>{html_name name=alt_name}</td>
                </tr>
                <tr>
                    <td><label class="w3-text-red control" for="type_id">Delivery Type</label></td>
                    <td>{html_customer_types name="type_id" selected=$type_id}</td>
                </tr>
                <tr>
                    <td><label class="w3-text-red control" for="route_id">Route</label></td>
                    <td>{html_routes name="route_id" selected=$route_id}</td>
                </tr>
                <tr>
                    <td><label class="w3-text-red control" for="startedm">Start Date</label></td>
                    <td>{html_date prefix=started}</td>
                </tr>
                <tr>
                    <td class="vtop"><label class="w3-text-red control" for="delivery_address1">Address</label></td>
                    <td>
                        <input class="w3-input w3-border control" type="text" name="delivery_address1" id="delivery_address1" value="{$delivery_address1}" maxlength="30" size="30"><br/>
                        <input class="w3-input w3-border control" type="text" name="delivery_address2" id="delivery_address2" value="{$delivery_address2}" maxlength="30" size="30">
                    </td>
                </tr>
                <tr>
                    <td><label class="w3-text-red control" for="delivery_city">City/State/Zip</label></td>
                    <td>
                        <input class="w3-input w3-border control" type="text" name="delivery_city" id="delivery_city" value="{$delivery_city}" maxlength="30" size="20" />
                        {html_states name=delivery_state selected=$delivery_state}
                        <input class="w3-input w3-border control" type="text" name="delivery_zip" id="delivery_zip" value="{$delivery_zip}" maxlength="10" size="10" />
                    </td>
                </tr>
                <tr>
                    <td><label class="w3-text-red control" for="delivery_telephone_1">Telephone 1</label></td>
                    <td>{html_telephone prefix='delivery_telephone_1'}</td>
                </tr>
                <tr>
                    <td><label class="control" for="delivery_telephone_2">Telephone 2</label></td>
                    <td>{html_telephone prefix=delivery_telephone_2}</td>
                </tr>
                <tr>
                    <td><label class="control" for="delivery_telephone_3">Telephone 3</label></td>
                    <td>{html_telephone prefix=delivery_telephone_3}</td>
                </tr>
            </table>
            <div>
                <button class="w3-button w3-border w3-light-gray w3-margin-top" type="submit" name="action" value="submit">Submit</button>
            </div>
            <table class="w3-table w3-margin-top centered">
                <caption>Billing</caption>
                <tr>
                    <td><label class="control" for="bill_name_title">Name</label></td>
                    <td>{html_name name=bill_name}</td>
                </tr>
                <tr>
                    <td><label class="control" for="alt_bill_name_title">Alternate Name</label></td>
                    <td>{html_name name=alt_bill_name}</td>
                </tr>
                <tr>
                    <td class="vtop"><label class="control" for="bill_address1">Address</label></td>
                    <td>
                        <input class="w3-input w3-border control" type="text" name="bill_address1" id="bill_address1" value="{$bill_address1}" maxlength="30" size="30"><br/>
                        <input class="w3-input w3-border control" type="text" name="bill_address2" id="bill_address2" value="{$bill_address2}" maxlength="30" size="30">
                    </td>
                </tr>
                <tr>
                    <td><label class="control" for="bill_city">City/State/Zip</label></td>
                    <td>
                        <input class="w3-input w3-border control" type="text" name="bill_city" id="bill_city" value="{$bill_city}" maxlength="30" size="20">
                        {html_states name=bill_state selected=$bill_state}
                        <input class="w3-input w3-border control" type="text" name="bill_zip" id="bill_zip" value="{$bill_zip}" maxlength="10" size="10">
                    </td>
                </tr>
                <tr>
                    <td><label class="control" for="bill_telephone_1">Telephone 1</label></td>
                    <td>{html_telephone prefix=bill_telephone_1}</td>
                </tr>
                <tr>
                    <td><label class="control" for="bill_telephone_2">Telephone 2</label></td>
                    <td>{html_telephone prefix=bill_telephone_2}</td>
                </tr>
                <tr>
                    <td><label class="control" for="bill_telephone_3">Telephone 3</label></td>
                    <td>{html_telephone prefix=bill_telephone_3}</td>
                </tr>
            </table>
            <div>
                <button class="w3-button w3-border w3-light-gray w3-margin-top" type="submit" name="action" value="submit">Submit</button>
            </div>
            <table class="w3-table w3-margin-top centered">
                <caption>Notes</caption>
                <tbody>
                    <tr>
                        <td class="vtop"><label class="control" for="notes">Customer</label></td>
                        <td>
                            <textarea class="w3-input w3-border control" name="notes" id="notes" rows="4" cols="60">{$notes}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="vtop"><label class="control" for="bill_notes">Billing</label></td>
                        <td>
                            <textarea class="w3-input w3-border control" name="bill_notes" id="bill_notes" rows="4" cols="32">{$bill_notes}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="vtop"><label class="control" for="delivery_notes">Delivery</label></td>
                        <td>
                            <textarea class="w3-input w3-border control" name="delivery_notes" id="delivery_notes" rows="1" cols="60">{$delivery_notes}</textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div>
                <button class="w3-button w3-border w3-light-gray w3-margin-top" type="submit" name="action" value="submit">Submit</button>
            </div>
        </form>
        <script src="{$ROOT}js/calendar.js"></script>
        <script src="{$ROOT}js/jquery.js"></script>
        <script src="{$ROOT}js/jquery.autotab.js"></script>
        <script src="{$ROOT}js/popups/customers.js.php"></script>
        <script src="{$ROOT}customers/js/addnew.js"></script>
    </body>
</html>
