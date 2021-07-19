

function add_another(mainid)
{
    // Set main customer id
    document.getElementById('addanothermainid').value = mainid;
    
    // Display window
    document.getElementById('addanother').style.display='block';

    // Set focus to secondary id
    document.getElementById('addanothersecondaryid').focus();
}

function add_combined()
{
    // Display window
    document.getElementById('addcombined').style.display='block';

    // Set focus to secondary id
    document.getElementById('addcombinedmainid').focus();
}

function edit_combined(mainid, secondaryid)
{
    // Set customer id's
    document.getElementById('editcombinedmainid').value = mainid;
    document.getElementById('editcombinedsecondaryid').value = secondaryid;
    
    // Display window
    document.getElementById('editcombined').style.display='block';

    // Set focus to secondary id
    document.getElementById('editcombinedsecondaryid').focus();
}

