
function adjustment(cid)
{
    //$('#update-id1').val(id1);
    document.getElementById('adjustment').style.display='block';
    //$('#update-id1').focus();    
}

function complaint(cid)
{
    document.getElementById('complaint').style.display='block';
}

function payment(cid, name, address, type)
{
    $('#payment-cid').val(cid);
    $('#payment-name').text(name);
    $('#payment-address').text(address);
    $('#payment-dtype').text(type);
    $('#payment').css('display', 'block');
}

function payment_click()
{
    // Hide form
    $('#payment').css('display', 'none');

    // Request payment be added

    // Report success/fail/error message on completion
    // $('.flashes') add '<div class="">message</div>' 
}

function service(cid)
{
    document.getElementById('service').style.display='block';
}

function type(cid)
{
    document.getElementById('type').style.display='block';
}

function update(cid)
{
    document.getElementById('update').style.display='block';
}

function view(cid)
{
    document.getElementById('view').style.display='block';
}
