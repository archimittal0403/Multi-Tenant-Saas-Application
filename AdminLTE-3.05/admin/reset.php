<?php
session_start();
 include('includes/config.php');
?>
<?php
if(isset($_POST['change_password'])){

    $new=$_POST['new_password'];
 
    $confirm=$_POST['con_password'];
  
     $admin_email=$_SESSION['email'];
if($new==$confirm){
    $hased=password_hash($new,PASSWORD_DEFAULT);

 $select=$con->prepare("SELECT email FROM `accounts` WHERE email=?");
 $select->bind_param("s",$admin_email);
 $select->execute();
 $select_result=$select->get_result();
 $row_count=$select_result->num_rows;
if($row_count>0){
    $row_fetch=$select_result->fetch_assoc();
    $update=$con->prepare("UPDATE `accounts` SET password=? WHERE email=?");
    $update->bind_param("ss",$hased,$admin_email);
    $update->execute();
    if($update){
       echo "<script>alert('Reset the password successfully');</script>";

    }
}
}
}
else{
  echo "<script>alert('Please match the password');</script>";

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password </title>
    <!-- bootstrap css link  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
  
    <!-- fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../style.css">
</head>
<body>
    
<div class="container-fluid m-3">
    <h2 class="text-center mb-4 mt-5">Reset Password</h2>
    <div class=" row d-flex justify-content-center align-item-center">
        <div class="col-lg-6">
            <img src="../assest/images/akg-logo.png" alt="adin registration" class="img-fluid">
</div>

<div class="col-lg-6">
  <div class="alert alert-primary w-50 " role="alert">
 
    <?php
    if(isset($_REQUEST['msg'])){
      echo $_REQUEST['msg'];
     }
?>

    <form action="reset.php" method="POST">
      
        <div class="form-outline mb-4 mt-5">
<label for="new_password" class="form-label">New Password :-</label>
<input type="new_password" id="new_password" name="new_password" placeholder="Enter New password" required="required"   autocomplete="off" class="form-control w-50 ">
</div>

   <div class="form-outline mb-4 mt-5">
<label for="con_password" class="form-label">Confirm Password :-</label>
<input type="con_password" id="con_password" name="con_password" placeholder="Repeat password" required="required"   autocomplete="off" class="form-control w-50 ">
</div>
<div class="form-outline mb-4">
<input type="submit" class="bg-success  py-2 px-3 border-0 text-light" name="change_password" value="Change Password">

</form>

</div>
</div>
</div>
</body>
</html>