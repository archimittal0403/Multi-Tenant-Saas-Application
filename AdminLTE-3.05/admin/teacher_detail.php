<?php
session_start();
include('includes/config.php');
include('includes/functions.php');


include('header.php');
 include('sidebar.php');
if(!isset($_GET['teacher_id'])){
   // echo "teacher id is missing";
}
else{
    $teacher_id=$_GET['teacher_id'];
}
?>
<?php
if(isset($_GET['teacher_id'])){
    $teacher_id=$_GET['teacher_id'];
    $college_id=$_SESSION['college_id'];
    $query=$con->prepare("SELECT * FROM `accounts` WHERE id=? AND college_id=?");
    $query->bind_param("ii",$teacher_id,$college_id);
    $query->execute();
    $query_result=$query->get_result();
    $row_fetch=$query_result->fetch_assoc();
    $teacher_name=$row_fetch['Name'];
    $teacher_email=$row_fetch['email'];
    $phone=get_usermeta($teacher_id,'phone');
    $address=get_usermeta($teacher_id,'address');
    $dob=get_usermeta($teacher_id,'dob');
    $gender=get_usermeta($teacher_id,'gender');
    $qualification=get_usermeta($teacher_id,'qualification');
    $experience=get_usermeta($teacher_id,'experience');
    $class_id=get_usermeta($teacher_id,'class');
    $section_id=get_usermeta($teacher_id,'section');
    $subject_id=get_usermeta($teacher_id,'subject');
$class=$con->prepare("SELECT title FROM `posts` WHERE id=?");
$class->bind_param("i",$class_id);
$class->execute();
$class_result=$class->get_result();
while($row_class=$class_result->fetch_assoc()){
    $class_title=$row_class['title'];
}
$section=$con->prepare("SELECT title FROM `section` WHERE id=?");
$section->bind_param("i",$section_id);
$section->execute();
$section_result=$section->get_result();
while($row_section=$section_result->fetch_assoc()){
    $section_title=$row_section['title'];
}
$subject=$con->prepare("SELECT name FROM `courses` WHERE id=?");
$subject->bind_param("i",$subject_id);
$subject->execute();
$subject_result=$subject->get_result();
while($row_subject=$subject_result->fetch_assoc()){
    $subject_title=$row_subject['name'];
}
$salary=get_usermeta($teacher_id,'salary');
$bank=get_usermeta($teacher_id,'bank');
$ano=get_usermeta($teacher_id,'ano');
$ifsc=get_usermeta($teacher_id,'ifsc');
}
?>
<section class="content">
    <div class="container-fluid">
      
<div class="row">
   <div class="col-md-4">
        <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                 <h3 class="mt-4 text-center"><u>Personal Details:-</u></h3>
                <div class="text-center mt-5">
                  <img class="profile-user-img img-fluid img-circle" src="uploads/akglogo.png" alt="User profile picture">
                </div>
               
                <h3 class="profile-username text-center mt-4">Teacher Name:- &nbsp;<?php  echo ucfirst($teacher_name);?></h3>
                <h3 class="profile-username text-center mt-2">Email ID:- &nbsp;<?php echo $teacher_email;?></h3>
                <h3 class="profile-username text-center mt-2">Phone NO:- &nbsp;<?php echo $phone; ?></h3>
                <h3 class="profile-username text-center mt-2">Address:-&nbsp;<?php echo ucfirst($address);?></h3>
                <h3 class="profile-username text-center mt-2">DOB:- &nbsp;<?php echo $dob;?></h3>
                <h3 class="profile-username text-center mt-2">Gender:- &nbsp;<?php echo ucfirst($gender); ?></h3>

</div>

  <!-- <input type="submit" name="update_details" id="update_details" value="update_details" class="form-group "> -->
   <?php
$teacher_id = $_GET['teacher_id'] ?? '';
?>
 <a href="update_teacher.php?update_details=<?php echo $teacher_id; ?>" 
   class="btn btn-primary form-control">
   Update Details
</a>

</div>
   </div>
   
   <div class="col-md-8">
       <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="mt-4"><u>Acedemic Background:-</u></h3>
                <h3 class="profile-username mt-4 ml-3 ">Qualification:- &nbsp; <?php echo ucfirst($qualification);?></h3>
            <h3 class="profile-username my-4 ml-3"> Past Experience:- &nbsp; <?php echo $experience;?></h3>
            <h3 class="profile-username my-4 ml-3">Present Class:- &nbsp; <?php echo $class_title ;?></h3>
            <h3 class="profile-username my-4 ml-3">Present Section:- &nbsp;<?php echo $section_title;?></h3>
            <h3 class="profile-username my-4 ml-3">Present Subject:- &nbsp;<?php echo $subject_title;?></h3> 
</div>
</div>
 <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="mt-4"><u>Bank Details:-</u></h3>
                <h3 class="profile-username mt-4 ml-3">Salary:- &nbsp;<?php echo $salary;?></h3>
                <h3 class="profile-username mt-4 ml-3">Bank Name:-&nbsp;<?php echo $bank;?></h3>
                <h3 class="profile-username mt-4 ml-3">Account NO:-&nbsp;<?php echo $ano;?></h3>
                <h3 class="profile-username mt-4 ml-3">IFSC Code:-&nbsp;<?php echo $ifsc;?></h3>
</div>
</div>
   </div>
</div>
    </div>
     <div class="container my-4">
    <?php
    if(isset($_GET['update_detail'])){
        include('update_teacher.php');
    }
?>

   
      </div><!--/. container-fluid -->
</section>