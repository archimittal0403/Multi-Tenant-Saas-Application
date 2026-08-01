
<?php
 include('includes/config.php');
//include('includes/function.php');
session_start();
?> 

<?php
// Form submit check
if(isset($_POST['verify_otp'])){

    // Ensure email session exists
    if(!isset($_SESSION['email'])){
        header("Location: login.php?msg=Session expired");
        exit();
    }

    $user_otp = trim($_POST['user_otp']);
    $user_email = $_SESSION['email'];

    // DB query to check OTP
    $stmt = $con->prepare("SELECT * FROM accounts WHERE email=? AND user_otp=?");
    $stmt->bind_param("ss", $user_email, $user_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        // OTP correct
       $user = $result->fetch_assoc();  
        $_SESSION['login'] = true;  
        $_SESSION['otp_verified']=true;       // MUST
       $_SESSION['user_type'] = strtolower($user['type']);
   
      
        $_SESSION['user_id'] = $user['id']; // optional
          $_SESSION['institute_id'] = $user['institute_id']; 
          if($_SESSION['user_type'] == "super_admin"){

    // Super Admin के लिए institute select page
    header("Location: select_institute.php");
    exit();
}
        // 🔥 GET system_type from institutes table
$institute_id = $user['institute_id'];

$query = $con->prepare("SELECT system_type,institute_code FROM institutes WHERE id=?");
$query->bind_param("i", $institute_id);
$query->execute();
$res = $query->get_result();
$inst = $res->fetch_assoc();

$_SESSION['system_type'] = $inst['system_type'] ?? '';
$_SESSION['institute_code']=$inst['institute_code'] ?? '';
        // delete OTP
        $stmt2 = $con->prepare("UPDATE accounts SET user_otp=NULL WHERE email=?");
        $stmt2->bind_param("s", $user_email);
        $stmt2->execute();

        // set cokkies 
        if(isset($_POST['remember'])){
            setcookie("email","$user_email",time()+86400,"/");
        }
$institute_id=$_SESSION['institute_id'];
$query=mysqli_query($con,"SELECT is_setup_done FROM `institutes` WHERE id='$institute_id'");
$data=mysqli_fetch_assoc($query);
if($data['is_setup_done']==0){
    header("Location: setup.php");
    exit;
}
else{
 header("Location: dashboard.php");  // redirect
        exit();   
}
       // header("Location: dashboard.php");  // redirect
      //  exit();
    } else {
        $_SESSION['error_msg'] = "Invalid OTP";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP - IRISERP</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    padding:0;
    min-height:100vh;
    background: linear-gradient(135deg,#0f172a,#1e293b,#2563eb);
    font-family: 'Poppins', sans-serif;

    overflow-x:hidden;
    overflow-y:auto;
}


/* blur circles */
.blur-circle{
    position:absolute;
    border-radius:50%;
    filter:blur(80px);
    opacity:.4;
}

.circle1{
    width:300px;
    height:300px;
    background:#3b82f6;
    top:-100px;
    left:-100px;
}

.circle2{
    width:250px;
    height:250px;
    background:#9333ea;
    bottom:-80px;
    right:-80px;
}

/* card */
.otp-card{
    width:100%;
    max-width:450px;
    border-radius:24px;
    backdrop-filter: blur(18px);
    background:rgba(255,255,255,0.10);
    border:1px solid rgba(255,255,255,0.15);
    box-shadow:0 10px 40px rgba(0,0,0,0.25);
    padding:30px;
    color:#fff;
}

/* title */
.title{
    font-size:28px;
    font-weight:700;
    text-align:center;
}

.subtitle{
    text-align:center;
    font-size:14px;
    color:#cbd5e1;
    margin-bottom:20px;
}

/* input */
.form-control{
    height:52px;
    border-radius:12px;
    border:none;
    background:rgba(255,255,255,0.12);
    color:#fff;
}

.form-control::placeholder{
    color:#cbd5e1;
}

.form-control:focus{
    background:rgba(255,255,255,0.18);
    border:1px solid #60a5fa;
    box-shadow:none;
    color:#fff;
}

/* button */
.btn-verify{
    width:100%;
    height:50px;
    border:none;
    border-radius:12px;
    background:linear-gradient(to right,#2563eb,#3b82f6);
    color:#fff;
    font-weight:600;
    transition:0.3s;
}

.btn-verify:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(59,130,246,0.4);
}

/* checkbox */
.form-check-label{
    color:#cbd5e1;
}

.alert-box{
    font-size:14px;
}
.form-control{
    font-size:18px;
    letter-spacing:6px;
    text-align:center;
}
.btn-verify{
    height:52px;
    font-size:16px;
}
@media(max-width:576px){

    .otp-card{
        margin:15px;
        padding:22px;
        border-radius:18px;
    }

    .title{
        font-size:22px;
    }

    .subtitle{
        font-size:12px;
    }

    .blur-circle{
        display:none;
    }
}
</style>
</head>

<body>

<div class="blur-circle circle1"></div>
<div class="blur-circle circle2"></div>

<div class="container h-100">
<div class="row justify-content-center align-items-center min-vh-100 py-4">

    <div class="col-lg-5 col-md-8">

        <div class="otp-card">

            <div class="title">OTP Verification</div>
            <div class="subtitle">Enter the OTP sent to your registered email</div>

            <!-- ALERT -->
            <?php if(isset($_REQUEST['msg'])){ ?>
                <div class="alert alert-danger text-center alert-box">
                    <?php echo $_REQUEST['msg']; ?>
                </div>
            <?php } ?>

            <form action="verify.php" method="post">

                <div class="mb-3">
                    <label class="form-label text-white">Enter OTP</label>
                 <input type="number"
                           name="user_otp"
                           class="form-control"
                           placeholder="6 digit OTP"
                           required
                           autocomplete="off">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="remember">
                    <label class="form-check-label">Remember Me</label>
                </div>

                <button type="submit" name="verify_otp" class="btn-verify">
                    Verify OTP
                </button>

            </form>

            <div class="text-center mt-3">
                <a href="login.php" style="color:#bfdbfe;text-decoration:none;">
                    ← Back to Login
                </a>
            </div>

        </div>

    </div>

</div>
</div>

</body>
</html>