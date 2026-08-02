<?php

function getInstitute($con,$institute_id){

    $query = mysqli_query($con,"SELECT * FROM institutes WHERE id='$institute_id'");
    return mysqli_fetch_assoc($query);
}

function generatePDFHeader($institute){

    $name    = $institute['name'];
    $address = $institute['address'];
    $phone   = $institute['phone'];
    $logo    = $institute['logo'];

    $logo_path = "./uploads/logo/".$logo;

    return '

<table 
    width="100%" 
    cellpadding="0" 
    cellspacing="0" 
    border="0"
    style="border:none;"
>

    <tr style="border:none;">

   <!-- LOGO -->
<td 
    width="15%" 
    align="center"
    style="border:none;"
>

    <img src="'.$logo_path.'" width="65">

</td>

<!-- TEXT -->
<td 
    width="85%" 
    align="center"
    style="border:none;"
>

    <div style="
        font-size:24px;
        font-weight:bold;
        color:#17479e;
        line-height:30px;
    ">
        '.$name.'
    </div>

    <div style="
        font-size:11px;
        color:#444;
        margin-top:4px;
    ">
        '.$address.'
    </div>

    <div style="
        font-size:11px;
        color:green;
        font-weight:bold;
        margin-top:4px;
    ">
        Phone : '.$phone.'
    </div>

</td>
    </tr>

</table>

    <hr style="
        border:0;
        border-top:2px solid #1e73ff;
        margin-top:10px;
    ">

    ';
}
?>