
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

function payment(cid, name, address, type, cls)
{
    $('#payment-cid').val(cid);
    $('#payment-name').text(name);
    $('#payment-address').text(address);
    $('#payment-dtype').text(type);
    $('#payment-dtype').addClass(cls);
    $('#payment').css('display', 'block');
}

$(document).ready(function(){
    /*
    $("#payment-do").on("click", function(e) {
	e.preventDefault();
	var data = {
	    'customer': $("#payment-cid").val(),
	    'type': $("#payment-type").val(),
	    'id': $("#payment-id").val(),
	    'amount': $("#payment-amount").val(),
	    'tip': $("#payment-tip").val(),
	    'notes': $("#payment-notes").val(),
	}
	var url = $("#payment-form").attr('action')
	var url = $("#payment-form").attr('action')
	var rq = $.post("https://localhost:5000" + url, data)
	    .done(function(){
		$('#payment').css('display', 'none');
		alert('success')
	    })
	    .fail(function(){
		alert('fail')
		$('#payment').css('display', 'none');
	    });
	
    });
    */
});

function payment_click()
{
    /*
    var data = {
	'customer': $("#payment-cid").val(),
	'type': $("#payment-type").val(),
	'id': $("#payment-id").val(),
	'amount': $("#payment-amount").val(),
	'tip': $("#payment-tip").val(),
	'notes': $("#payment-notes").val(),
    }
    var url = $("#payment-form").attr('action')
    var rq = $.post(+url, data)
	.done(function(){
	    $('#payment').css('display', 'none');
	    alert('success')
	})
	.fail(function(){
	    alert('fail')
	    $('#payment').css('display', 'none');
	});
    */
    // Request payment be added
    //$("#payment-form").submit()
    
    // Report success/fail/error message on completion
    // $('.flashes') add '<div class="">message</div>' 
    //$('#payment').css('display', 'none');
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
