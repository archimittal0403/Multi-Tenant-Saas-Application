<?php

include('includes/auth.php');
checkRole('admin');

include('includes/config.php');
include('includes/functions.php');

include('header.php');
include('sidebar.php');


// Get institute id
$institute_id = $_GET['institute_id'] ?? $_SESSION['institute_id'];


// Get latest payment details
$payment_detail = mysqli_query($con,"
SELECT * FROM payment_history 
WHERE institute_id='$institute_id'
ORDER BY id DESC
LIMIT 1
");


$row = mysqli_fetch_assoc($payment_detail);


if(!$row){

    echo "<script>
    alert('No Payment History Found');
    window.location='subscription.php';
    </script>";

    exit;

}


// Payment Details

$receipt_no      = $row['receipt_no'];
$institute_id    = $row['institute_id'];
$transaction_id  = $row['transaction_id'];
$amount           = $row['amount'];
$payment_banking  = $row['payment_type'];
$plan_type        = $row['plan_type'];
$payment_date     = $row['payment_date'];
$expiry_date      = $row['expiry_date'];



// Institute Details

$institute_detail = mysqli_query($con,"
SELECT * FROM institutes 
WHERE id='$institute_id'
");


$row_detail = mysqli_fetch_assoc($institute_detail);


$institute_name = $row_detail['name'];
$institute_code = $row_detail['institute_code'];


$date = date("d-m-Y");


?>


<!DOCTYPE html>
<html>

<head>

<title>Payment Receipt</title>


<style>

body{
    font-family:Arial;
    background:#f5f5f5;
}


.receipt-box{

    width:500px;
    margin:50px auto;
    background:white;
    padding:25px;
    border:1px solid #ddd;
    border-radius:10px;

}


h2{
    text-align:center;
}


.row{

    margin:10px 0;

}


.btn{

    margin-top:20px;
    padding:10px;
    width:100%;
    background:green;
    color:white;
    border:none;
    cursor:pointer;

}


@media print{

.btn{
display:none;
}

body{
background:white;
}

}

</style>


</head>


<body>


<div class="receipt-box">


<h2>Payment Receipt</h2>

<hr>


<div class="row">
<b>Receipt No:</b>
<?= $receipt_no ?>
</div>


<div class="row">
<b>Date:</b>
<?= $date ?>
</div>


<hr>


<div class="row">
<b>Institute Name:</b>
<?= $institute_name ?>
</div>


<div class="row">
<b>Institute Code:</b>
<?= $institute_code ?>
</div>


<hr>


<div class="row">
<b>Plan Type:</b>
<?= $plan_type ?>
</div>


<div class="row">
<b>Amount Paid:</b>
₹<?= $amount ?>
</div>


<div class="row">
<b>Payment Mode:</b>
<?= $payment_banking ?>
</div>


<div class="row">
<b>Transaction ID:</b>
<?= $transaction_id ?>
</div>


<div class="row">
<b>Payment Date:</b>
<?= $payment_date ?>
</div>


<div class="row">
<b>Expiry Date:</b>
<?= $expiry_date ?>
</div>


<button class="btn" onclick="window.print()">
🖨 Print Receipt
</button>



</div>


</body>

</html>