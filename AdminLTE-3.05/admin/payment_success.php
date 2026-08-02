<?php
include('includes/config.php');

$payment_id = $_POST['payment_id'];
$amount = $_POST['amount'];
$receipt_no = "IRIS-" . rand(100,999);
file_put_contents("log.txt", "SUCCESS HIT\n", FILE_APPEND);
// यहाँ DB में save करो
mysqli_query($con, "INSERT INTO payments(payment_id, amount, status)
VALUES('$payment_id','$amount','success')");
?>