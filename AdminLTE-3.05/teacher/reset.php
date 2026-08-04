<?php
session_start();
include('includes/config.php');

if(isset($_POST['reset'])){

    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $admin_email = $_POST['email'];

    if($new == $confirm){

        $hashed = password_hash($new, PASSWORD_DEFAULT);

        $select = $con->prepare("SELECT email FROM accounts WHERE email=?");
        $select->bind_param("s", $admin_email);
        $select->execute();
        $result = $select->get_result();

        if($result && $result->num_rows > 0){

            $update = $con->prepare("UPDATE accounts SET password=? WHERE email=?");
            $update->bind_param("ss", $hashed, $admin_email);

            if($update->execute()){

                echo "<script>
                alert('Password reset successful');
                window.location.href='../../login.php';
                </script>";

            } else {
                echo "<script>alert('Update failed');</script>";
            }

        } else {
            echo "<script>alert('Email not found');</script>";
        }

    } else {
        echo "<script>alert('Password not matching');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reset Password - IRISERP</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    padding:0;
    min-height:100vh;
    background:
    linear-gradient(135deg,#0f172a,#1e293b,#2563eb);
    overflow:hidden;
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

.footer-text{
    color:#cbd5e1;
    font-size:13px;
}

@media(max-width:768px){
    .login-card{
        margin:20px;
    }
}

</style>
</head>

<body>

<div class="blur-circle circle1"></div>
<div class="blur-circle circle2"></div>

<div class="container h-100">
    <div class="row justify-content-center align-items-center vh-100">

        <div class="col-lg-5 col-md-8">

            <div class="login-card p-4 p-md-5">

                <!-- Header -->
                <div class="text-center mb-4">

                    <h1 class="brand-title mb-1">
                        IRISERP
                    </h1>

                    <p class="brand-subtitle">
                        Reset Your Password
                    </p>

                </div>

                <!-- Form -->
                <form action="" method="POST">

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Registered Email</label>
                        <input 
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Enter your registered email"
                            required>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input 
                            type="password"
                            name="new_password"
                            class="form-control"
                            placeholder="Enter new password"
                            required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input 
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            placeholder="Confirm password"
                            required>
                    </div>

                    <!-- CSRF -->
                    <input 
                        type="hidden" 
                        name="csrf_token"
                        value="<?php echo $_SESSION['csrf_token']; ?>">

                    <!-- Button -->
                    <div class="d-grid mt-4">
                        <button class="login-btn" name="reset">
                            Reset Password
                        </button>
                    </div>

                    <!-- Links -->
                    <div class="d-flex justify-content-between mt-4">

                        <a href="login.php" class="auth-link">
                            Back to Login
                        </a>

                        <a href="index.php" class="auth-link">
                            Home
                        </a>

                    </div>

                </form>

                <!-- Footer -->
                <div class="text-center mt-4 footer-text">
                    © 2026 IRISERP — Secure Password Reset
                </div>

            </div>

        </div>

    </div>
</div>

<?php include('footer.php'); ?>

</body>
</html>