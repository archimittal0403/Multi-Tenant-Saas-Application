<?php
session_start();
 include('includes/config.php');
include('email.php');
 $admin_email=$_POST['email'];
 $admin_password=$_POST['password'];

 $select_query="Select * from `accounts` where email=?";
       $result=$con->prepare($select_query);
       $result->bind_param("s",$admin_email);
       $result->execute();
       $select_result=$result->get_result();
       $row_count=$select_result->num_rows;
       if($row_count>0){
              $row_data=$select_result->fetch_assoc();
      //if(password_verify($admin_password,$row_data['admin_password'])){
      
            $_SESSION['email']=$admin_email;
 $otp=rand(11111,99999);
 send_otp($admin_email,"PHP OTP LOGIN",$otp);
 //echo "Email founnd:" .$otp;
  $update_otp="update `accounts` set user_otp=? where email=?";
       $result_otp=$con->prepare($update_otp);
       $result_otp->bind_param("is",$otp,$admin_email);
       $result_otp->execute();
        //header("location:verify.php?msg=OTP Send Success");
   
       header("location:verify.php?msg=OTP Send Success");
      }
      // echo "<script>alert('invalid credantaial')</script>";
       
       
       else{
      
       header("location:admin_login.php?msg=Email id invalid");
       }
 ?>
