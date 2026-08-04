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

$logo_path = $_SERVER['DOCUMENT_ROOT'] . '/student management/AdminLTE-3.05/admin/uploads/logo/' . $logo;

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
<td width="15%" align="center" style="border:none;">

<img src="'.$logo_path.'" height="60">

</td>

<!-- TEXT -->
<td width="85%" align="center" style="border:none;">

<div style="
font-size:22px;
font-weight:bold;
color:#17479e;
line-height:24px;
">
'.$name.'
</div>

<div style="
font-size:10px;
color:#555;
line-height:14px;
">
'.$address.'
</div>

<div style="
font-size:10px;
color:green;
font-weight:bold;
line-height:14px;
">
Phone : '.$phone.'
</div>

</td>

</tr>

</table>

// <hr style="
// border:0;
// border-top:2px solid #17479e;
// margin-top:2px;
// margin-bottom:8px;
// ">

';
}
?>