

function add_combined(id1)
{
    // Set main customer id
    document.getElementById('add-id1').value = id1;
    
    // Display window
    document.getElementById('add').style.display='block';

    // Set focus to secondary id
    document.getElementById('add-id2').focus();
}

function create_combined()
{
    // Display window
    document.getElementById('create').style.display='block';

    // Set focus to main id
    document.getElementById('create-id1').focus();
}

function delete_combined(id1, id2)
{
    $("#delete-id1").val(id1);
    $("#delete-id2").val(id2);
    $("#delete-form").submit();
}

function update_combined(id1, id2)
{
    // Set customer id's
    //document.getElementById('update-id1').value = id1;
    //document.getElementById('update-id2').value = id2;
    $('#update-id1').val(id1);
    $('#update-id2').val(id2);
    $('#update-id2-original').val(id2);
    
    // Display window
    document.getElementById('update').style.display='block';
    
    // Set focus to secondary id
    //document.getElementById('update-id2').focus();
    $('#update-id2').focus();
}
