<?php
session_start();
include('includes/config.php');
include('email.php');

if(!isset($_SESSION['email'])){
    header("location:admin_login.php?msg=Please login first");
    exit;
}

$admin_email = $_SESSION['email'];

// Only admin and super_admin allowed
$select_query = "SELECT * FROM accounts WHERE email=? AND type IN ('admin','super_admin')";

$result = $con->prepare($select_query);
$result->bind_param("s", $admin_email);
$result->execute();

$select_result = $result->get_result();

if($select_result->num_rows > 0){

    $row_data = $select_result->fetch_assoc();

    $_SESSION['email']        = $admin_email;
    $_SESSION['user_type']    = $row_data['type'];
    $_SESSION['user_id']      = $row_data['id'];
    $_SESSION['institute_id'] = $row_data['institute_id'];

    $otp = rand(11111,99999);

    send_otp($admin_email, "IRIS LOGIN OTP", $otp);

    $update_otp = "UPDATE accounts SET user_otp=? WHERE email=?";
    $result_otp = $con->prepare($update_otp);
    $result_otp->bind_param("is", $otp, $admin_email);
    $result_otp->execute();

    header("location:verify.php?msg=OTP Send Success");
    exit;

}else{

    session_destroy();
    header("location:admin_login.php?msg=Access denied");
    exit;
}
?>