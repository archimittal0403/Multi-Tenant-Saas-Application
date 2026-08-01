<?php
session_start();

if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include('includes/config.php');
include('header.php');
?>

<?php
if(isset($_SESSION['error_msg'])){
    echo "
    <div class='position-fixed top-0 end-0 p-3' style='z-index:9999'>
        <div class='alert alert-danger shadow'>
            ".$_SESSION['error_msg']."
        </div>
    </div>";
    
    unset($_SESSION['error_msg']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login - IRISERP</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
@media(max-width:576px){
    .captcha-box{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }

    .captcha-box img{
        max-width:100%;
        height:auto;
    }
}
body{
    margin:0;
    padding:0;
    min-height:100vh;
    background:
    linear-gradient(135deg,#0f172a,#1e293b,#2563eb);
       overflow-x:hidden;
    overflow-y:auto;
    font-family: 'Poppins', sans-serif;
}

/* animated circles */

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

/* glass card */

.login-card{
    width:100%;
    max-width:430px;
    border-radius:28px;
    backdrop-filter: blur(18px);
    background:rgba(255,255,255,0.10);
    border:1px solid rgba(255,255,255,0.15);
    box-shadow:0 8px 40px rgba(0,0,0,0.25);
    overflow:hidden;
}




.brand-title{
    font-size:32px;
    font-weight:700;
    color:#fff;
    letter-spacing:1px;
}

.brand-subtitle{
    color:#dbeafe;
    font-size:14px;
}

/* input */

.form-control{
    height:52px;
    border-radius:14px;
    border:none;
    background:rgba(255,255,255,0.12);
    color:#fff;
    padding-left:16px;
}

.form-control:focus{
    background:rgba(255,255,255,0.18);
    box-shadow:none;
    border:1px solid #60a5fa;
    color:#fff;
}

.form-control::placeholder{
    color:#cbd5e1;
}

.form-label{
    color:#fff;
    font-weight:500;
    margin-bottom:8px;
}

/* button */

.login-btn{
    height:52px;
    border:none;
    border-radius:14px;
    background:linear-gradient(to right,#2563eb,#3b82f6);
    color:#fff;
    font-weight:600;
    transition:.3s;
}

.login-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(59,130,246,.4);
}

/* links */

.auth-link{
    color:#bfdbfe;
    text-decoration:none;
    transition:.3s;
}

.auth-link:hover{
    color:#fff;
}

.captcha-box{
    background:rgba(255,255,255,0.08);
    padding:12px;
    border-radius:14px;
}

.footer-text{
    color:#cbd5e1;
    font-size:13px;
}

@media(max-width:576px){

    .login-card{
        padding:20px !important;
        border-radius:18px;
    }

    .brand-title{
        font-size:26px;
    }

    .blur-circle{
        display:none; /* mobile pe background clean */
    }
}
.form-control{
    font-size:16px; /* zoom avoid in iPhone */
}
</style>
</head>

<body>

<div class="blur-circle circle1"></div>
<div class="blur-circle circle2"></div>

<div class="container h-100">
<div class="row justify-content-center align-items-center min-vh-100 py-5">

<div class="col-12 col-sm-10 col-md-8 col-lg-5">

            <div class="login-card p-4 p-md-5">

                <!-- Logo -->
                <div class="text-center mb-4">

                    

                    <h1 class="brand-title mb-1">
                        IRISERP
                    </h1>

                    <p class="brand-subtitle">
                        Smart Institution Management Platform
                    </p>

                </div>

                <!-- Form -->
                <form action="actions/login.php" method="POST">

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">
                            Email Address
                        </label>

                        <input 
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <input 
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required>

                    </div>

                    <!-- Captcha -->
                    <div class="mb-3">

                        <label class="form-label">
                            Security Verification
                        </label>

                        <input 
                        type="text"
                        class="form-control mb-3"
                        placeholder="Enter captcha code"
                        autocomplete="off"
                        required
                        name="captcha">

                        <div class="captcha-box d-flex justify-content-between align-items-center">

                            <img src="captcha.php" alt="captcha" class="rounded">

                            <a href="login.php" class="auth-link">
                                Refresh
                            </a>

                        </div>

                    </div>

                    <!-- CSRF -->
                    <input 
                    type="hidden" 
                    name="csrf_token"
                    value="<?php echo $_SESSION['csrf_token']; ?>">

                    <!-- Button -->
                    <div class="d-grid mt-4">
                        <button class="login-btn" name="login">
                            Login to IRISERP
                        </button>
                    </div>

                    <!-- Bottom -->
                    <div class="d-flex justify-content-between mt-4">

                        <a href="AdminLTE-3.05/admin/reset.php" class="auth-link">
                            Forgot Password?
                        </a>

                        <a href="index.php" class="auth-link">
                            Back to Home
                        </a>

                    </div>

                </form>

                <!-- Footer -->
                <div class="text-center mt-4 footer-text">
                    © 2026 IRISERP — All Rights Reserved
                </div>

            </div>

        </div>

    </div>
</div>

<?php include('footer.php'); ?>

</body>
</html>