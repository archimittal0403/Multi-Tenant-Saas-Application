<?php


include('includes/config.php');
include('includes/functions.php');

$edit_id = $_GET['id'] ?? $_POST['edit_parent'] ?? '';

   
  if($edit_id!=''){
$father_name=get_parent($edit_id,'father_name');
$father_mobile=get_parent($edit_id,'father_mobile');
$mother_name=get_parent($edit_id,'mother_name');
$mother_mobile=get_parent($edit_id,'mother_mobile');
$address=get_parent($edit_id,'parent_address');
    }
$parent_id=0;
$parent_email='';
$children_id='%i:$edit_id;%';
$parent=$con->prepare("SELECT user_id 
    FROM usermeta 
    WHERE meta_key='children' 
    AND meta_value LIKE ?");
    $parent->bind_param("s",$children_id);
    $parent->execute();
    $parent_result=$parent->get_result();
    if($parent && $parent_result->num_rows > 0){
$parent_row=$parent_result->fetch_assoc($parent);
$parent_id=$parent_row['user_id'];
$parent1=$con->prepare("SELECT email FROM `accounts` WHERE id=?");
$parent1->bind_param("i",$parent_id);
$parent1->execute();
$parent1_result=$parent1->get_result();
if($parent1 && $parent1_result->num_rows()>0){
$parent_email_row=$parent_result->fetch_assoc();
$parent_email=$parent_email_row['email'] ?? '';
  
    }
    }

//     if(isset($_POST['update_parent'])){
//         $edit_id = $_GET['id'] ?? $_POST['edit_parent'] ?? '';
//         $father_name=$_POST['father_name'];
//         $father_mobile=$_POST['father_mobile'];
//         $mother_name=$_POST['mother_name'];
//         $mother_mobile=$_POST['mother_mobile'];
//         $address=$_POST['address'];
//         $parent_email=$_POST['email'];
// if($father_name=='' || $father_mobile=='' || $mother_name=='' || $mother_mobile=='' || $address=='' || $parent_email==''){
//     echo '<script>alert("please,fill all the details")</script>';
// }
// else{
//         $update_parent=mysqli_query($con,"UPDATE `usermeta` SET father_name='$father_name',father_mobile='$father_mobile',mother_name='$mother_name',mother_mobile='$mother_mobile',address='$address',email='$parent_email' WHERE user_id=$edit_id");
// if($update_parent){
//     echo '<script>alert("Details are sucessfully updared")</script>';
//     echo "<script>window.open('parent.php','_self')</script>";
// }
//         }
//     }

if(isset($_POST['update_parent'])){
       $edit_id = $_GET['id'] ?? $_POST['edit_parent'] ?? '';
       $father_name=$_POST['father_name'];
        $father_mobile=$_POST['father_mobile'];
        $mother_name=$_POST['mother_name'];
        $mother_mobile=$_POST['mother_mobile'];
        $address=$_POST['address'];
        $parent_email=$_POST['email'];
     if($father_name=='' || $father_mobile=='' || $mother_name=='' || $mother_mobile=='' || $address==''){
    echo '<script>alert("please,fill all the details")</script>';
}
else{
    update_usermeta($edit_id,'father_name',$father_name);
    update_usermeta($edit_id,'father_mobile',$father_mobile);
    update_usermeta($edit_id,'mother_name',$mother_name);
    update_usermeta($edit_id,'mother_mobile',$mother_mobile);
    update_usermeta($edit_id,'address',$address);
$children_id='%i:$edit_id;%';
    $parent=$con->prepare("SELECT user_id FROM `usermeta` WHERE meta_key='children' AND meta_value LIKE ?");
    $parent->bind_param('s',$children_id);
    $parent->execute();
    $parent_result=$parent->get_result();
$parent_fetch=$parent_result->fetch_assoc($parent);
$parent_id=$parent_fetch['user_id'];
   $query=$con->prepare("UPDATE `accounts` set email=? WHERE id=?");
   $query->bind_param("si",$parent_email,$parent_id);
   $query->execute();

   
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit_parent</title>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <!-- fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <h2 class="text-center">Edit Details</h2>
<form action="" method="post" enctype="multipart/form-data">
    
    <input type="hidden" name="edit_parent" value="<?= $edit_id ?>">

    <div class="form-outline w-50 m-auto mt-6">
        <label for="father_name" class="form-label">Father Name :-</label>
        <input type="text" id="father_name" name="father_name" class="form-control" required="required" value="<?=ucfirst( $father_name) ?>">
    </div>
    <div class="form-outline w-50 m-auto mt-6">
         <label for="father_mobile" class="form-label">Father mobile :-</label>
        <input type="text" id="father_mobile" name="father_mobile" class="form-control" required="required" value="<?=$father_mobile ?>">
    </div>

     <div class="form-outline w-50 m-auto mt-6">
         <label for="mother_name" class="form-label">Mother Name :-</label>
        <input type="text" id="mother_name" name="mother_name" class="form-control" required="required" value="<?=ucfirst($mother_name) ?>">
    </div>
     <div class="form-outline w-50 m-auto mt-6">
         <label for="mother_mobile" class="form-label">Mother Mobile :-</label>
        <input type="text" id="mother_mobile" name="mother_mobile" class="form-control" required="required" value="<?=$mother_mobile ?>">
    </div>
     <div class="form-outline w-50 m-auto mt-6">
         <label for="address" class="form-label">Address :-</label>
        <input type="text" id="address" name="address" class="form-control" required="required" value="<?=$address?>">
    </div>
     <div class="form-outline w-50 m-auto mt-6">
         <label for="email" class="form-label">Edit Email Address :-</label>
        <input type="text" id="email" name="email" class="form-control" required="required" value="<?=$parent_email ?>">
    </div>
   
         <div class="text-center mt-4 ">
            <input type="submit" id="update_parent" name="update_parent" value="Update details" class="form-submit bg-info">
        </div>
</form>
</body>
</html>