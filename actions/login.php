<?php

session_start();
include('./includes/config.php');

$site_url = 'http://localhost/student management/AdminLTE-3.05/';
// now verify the csrf secuity 
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
        die("Invalid csrf token");
    }

}
    
// If already logged in, redirect to dashboard
// if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
//     $folder = $_SESSION['user_type'] ?? 'admin';
//     $folder = ($folder == 'admin') ? 'admin' : $folder;
//     header("Location: {$site_url}{$folder}/dashboard.php");
//     exit();
// }

// if (isset($_POST['send_otp'])) {
//     // अगर user ने send OTP क्लिक किया
//     $_SESSION['email'] = $_POST['email'];  // email save कर लो session में

//     // redirect to your send-otp.php
//     header("Location: ../AdminLTE-3.05/admin/send_otp.php");
//     exit();
// }

if (isset($_POST['login'])) {
     // 1. CAPTCHA check

    // session_unset(); 
    //  session_destroy();
    // session_start();
    session_regenerate_id(true);

    if (!isset($_POST['captcha']) || $_POST['captcha'] != $_SESSION['CODE']) {
        echo "Invalid CAPTCHA code!";
        exit;
    }
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    //$user_otp=$_POST['user_otp'];

    if (empty($email) || empty($pass)) {
        echo "Email and Password are required!";
        exit;
    }

    // cookies 
    if(isset($_POST['remember'])){
        $_SESSION['remember']="true";
    }



    // Prepared statement (safe)
    $stmt = $con->prepare("SELECT * FROM `accounts` WHERE `email` = ? ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
 // 🔒 Check if account locked
    if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {
        $_SESSION['error_msg'] ="Account locked till " . $user['lock_until'];
header("Location: ../login.php");
        exit;
    }
    
// verify password nad otp
if(password_verify($pass, $user['password']) || md5($pass) === $user['password']){
$current_date = date("Y-m-d");

if ($user['type'] != "admin" &&
    !empty($user['plan_expiry']) &&
    $current_date > $user['plan_expiry']) {

    $_SESSION['error_msg'] = " Please Contact Administrator.";

    header("Location: ../login.php");
    exit();
}
    // AUTO UPGRADE TO BCRYPT (VERY IMPORTANT)
    if(md5($pass) === $user['password']){
        $newHash = password_hash($pass, PASSWORD_DEFAULT);

        $up = $con->prepare("UPDATE accounts SET password=? WHERE email=?");
        $up->bind_param("ss", $newHash, $email);
        $up->execute();
    }

    // LOGIN SUCCESS
    $_SESSION['email'] = $user['email'];
    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['id'];
$_SESSION['user_type'] = $user['type'];

if($user['type']=="super_admin"){
    $folder="admin";
}
else{
    $folder=$user['type'];
}

header("Location: ../AdminLTE-3.05/".$folder."/send_otp.php");
exit();



}
    else{
         // if password or an otp is not correct then it will cjeck how many time attemp hasbeen done 
    $attempt=$user['failed_attempt']+1;
    $payment_expiry=$user['plan_expiry'];
    $lock_until=Null;
    // now chwck how many time failed attemp has been done
    if($attempt>=6){
$lock_until=date("Y-m-d H:i:s",strtotime("+1 hours"));
    }
// now run the update query
mysqli_query($con,"UPDATE accounts SET failed_attempt='$attempt', lock_until=" . ($lock_until ? "'$lock_until'" : "NULL") . "  WHERE email='$email'");
if($attempt>=6){
$_SESSION['error_msg'] ="Too many Wrong attempts ! Account is locked for 1 hours";
header("Location: ../login.php");
}
   else {
    $_SESSION['error_msg'] = "Invalid credentials!";
header("Location: ../login.php");
exit();
    }
    exit();
    }
    }
    else{
        echo "Email Not Found";
    }

  
 
}


?>
<!-- Simple HTML login form -->
<!-- <form method="post" action="actions/login.php">

    <input type="email" name="email" placeholder="Email" required /><br><br>
    <input type="password" name="password" placeholder="Password" required /><br><br>
    <button type="submit" name="login">Login</button>
</form> -->
