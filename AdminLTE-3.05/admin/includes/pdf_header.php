<?php

function getPDFHeader($title = "")
{
    global $con;

    $institute_id = $_SESSION['institute_id'] ?? 0;

    $query = mysqli_query($con,"
        SELECT *
        FROM institutes
        WHERE id='$institute_id'
    ");

    $institute = mysqli_fetch_assoc($query);

    $name    = $institute['name'] ?? '';
    $logo    = $institute['logo'] ?? '';
    $address = $institute['address'] ?? '';
    $phone   = $institute['phone'] ?? '';

    // LOGO PATH
    $logoPath = realpath(__DIR__ . '/../uploads/logo/' . $logo);

    return '

<style>
body{
    font-family: helvetica;
    color:#222;
}

/* HEADER */
.header-wrap{
    border-bottom:2px solid #0d6efd;
    padding-bottom:8px;
    margin-bottom:10px;
}

.logo{
    width:70px;
    height:70px;
    vertical-align:top;
    margin-top:-5px;
}

.inst-name{
    font-size:22px;
    font-weight:bold;
    color:#0d47a1;

    display:inline-block;
    vertical-align:top;

    margin-left:10px;
    margin-top:-8px;
}
.inst-address{
    font-size:11px;
    color:#555;

    margin-left:95px;
    margin-top:-35px;

    line-height:16px;
}

.inst-phone{
    font-size:11px;
    color:#198754;

    margin-left:95px;
    margin-top:2px;
}
.report-title{
    text-align:center;
    font-size:18px;
    font-weight:bold;
    margin-top:15px;
    margin-bottom:20px;
}

.low{
    color:red;
    font-weight:bold;
}

.high{
    color:green;
    font-weight:bold;
}
</style>
<!-- HEADER -->
<div class="header-wrap">

    <img src="'.$logoPath.'" class="logo">

    <span class="inst-name">
        '.$name.'
    </span>

    <div class="inst-address">
        '.$address.'
    </div>

    <div class="inst-phone">
        Phone : '.$phone.'
    </div>

</div>

';
}
?>