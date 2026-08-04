<?php
session_start();
include('includes/config.php');
include('includes/functions.php');


include('header.php');
 include('sidebar.php');
?>
<?php
if(isset($_GET['update_details'])){
$update_id=$_GET['update_details'];
$select_query=$con->prepare("SELECT * FROM `accounts` WHERE id=?");
$select_query->bind_param("i",$update_id);
$select_query->execute();
$select_result=$select_query->get_result();
while($row_fetch=$select_result->fetch_assoc()){
    $name=$row_fetch['Name'];
    $email=$row_fetch['email'];
    }
$teacher_phone=get_usermeta($update_id,'phone');
$teacher_address=get_usermeta($update_id,'address');
$dob=get_usermeta($update_id,'dob');
$qualification=get_usermeta($update_id,'qualification');
$experience=get_usermeta($update_id,'experience');
$gender=get_usermeta($update_id,'gender');
  $class_id=get_usermeta($update_id,'class');
    $section_id=get_usermeta($update_id,'section');
    $subject_id=get_usermeta($update_id,'subject');
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
$salary=get_usermeta($update_id,'salary');
$bank=get_usermeta($update_id,'bank');
$ano=get_usermeta($update_id,'ano');
$ifsc=get_usermeta($update_id,'ifsc');
}
?>
<?php
if (isset($_POST['update']) && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];

   $name=$_POST['title'];
   $email=$_POST['email'];
   $phone=$_POST['mobile'];
   $address=$_POST['address'];
   $dob=$_POST['dob'];
   $gender=$_POST['gender'];
   $qualification=$_POST['qualification'];
   $experience=$_POST['experience'];
   $class=$_POST['class'];
   $section=$_POST['section'];
   $subject=$_POST['subject'];
   $salary=$_POST['salary'];
   $bank=$_POST['bank'];
   $ano=$_POST['ano'];
   $ifsc=$_POST['ifsc'];
if($name==''||$email==''||$phone==''||$address==''||$dob==''||$gender==''||
$qualification==''||$experience==''||$salary==''||$bank==''||$ano==''||$ifsc==''){

    echo '<script>alert("please fill all the details")</script>';
   }
else{
    update_usermeta($update_id,'phone',$phone);
    update_usermeta($update_id,'address',$address);
    update_usermeta($update_id,'dob',$dob);
    update_usermeta($update_id,'qualification',$qualification);
    update_usermeta($update_id,'experience',$experience);
    update_usermeta($update_id,'gender',$gender);
    update_usermeta($update_id,'class',$class);
    update_usermeta($update_id,'section',$section);
    update_usermeta($update_id,'subject',$subject);
    update_usermeta($update_id,'salary',$salary);
    update_usermeta($update_id,'bank',$bank);
    update_usermeta($update_id,'ano',$ano);
    update_usermeta($update_id,'ifsc',$ifsc);

    $update_query=$con->prepare("UPDATE `accounts` set Name=?,email=? WHERE id=?");
    $update_query->bind_param("ssi",$name,$email,$update_id);
    $update_query->execute();
   
}
}
?>
 <h2 class="text-center"><u>Update Form</u></h2>
<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">

     <div class="form-outline w-50 m-auto mt-6">
            <label for="title" class="form-label">Teacher Name:-</label>
            <input type="text" id="title" name="title" class="form-control" required="required" value="<?php echo $name;?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="email" class="form-label">Email ID:-</label>
            <input type="text" id="email" name="email" class="form-control" required="required" value="<?php echo $email; ?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="mobile" class="form-label">Phone NO:-</label>
            <input type="text" id="mobile" name="mobile" class="form-control" required="required" value="<?php  echo $teacher_phone; ?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="address" class="form-label">Address:-</label>
            <input type="text" id="address" name="address" class="form-control" required="required" value="<?php  echo $teacher_address;?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="dob" class="form-label">DOB:-</label>
            <input type="text" id="dob" name="dob" class="form-control" required="required" value="<?php  echo $dob;?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">

<label>Gender:</label><br>
<input type="text" id="gender" name="gender" class="form-control" value="<?php echo $gender;?>">


        </div>
 <div class="form-outline w-50 m-auto mt-6">
            <label for="qualification" class="form-label">Qualification:-</label>
            <input type="text" id="qualification" name="qualification" class="form-control" required="required" value="<?php  echo $qualification;?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="experience" class="form-label">Past Experience:-</label>
            <input type="text" id="experience" name="experience" class="form-control" required="required" value="<?php echo $experience; ?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="class" class="form-label">Present class:-</label>
           <input type="hidden" name="class" value="<?php echo $class_id; ?>">
<input type="text" class="form-control" value="<?php echo $class_title; ?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="section" class="form-label">Present Section:-</label>
           <input type="hidden" name="section" value="<?php echo $section_id; ?>">
<input type="text" class="form-control" value="<?php echo $section_title; ?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="subject" class="form-label">Present Subject:-</label>
<input type="hidden" name="subject" value="<?php echo $subject_id; ?>">
<input type="text" class="form-control" value="<?php echo $subject_title; ?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="salary" class="form-label">Salary:-</label>
            <input type="text" id="salary" name="salary" class="form-control" required="required" value="<?php echo  $salary;?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="bank" class="form-label">Bank Name:-</label>
            <input type="text" id="bank" name="bank" class="form-control" required="required" value="<?php echo $bank; ?>">
        </div>

         <div class="form-outline w-50 m-auto mt-6">
            <label for="ano" class="form-label">Account NO:-</label>
            <input type="text" id="ano" name="ano" class="form-control" required="required" value="<?php echo $ano; ?>">
        </div>
         <div class="form-outline w-50 m-auto mt-6">
            <label for="ifsc" class="form-label">IFSC code:-</label>
            <input type="text" id="ifsc" name="ifsc" class="form-control" required="required" value="<?php  echo $ifsc;?>">
        </div>
        <div class="text-center mt-4 mb-2">
            <input type="submit" id="update" name="update" value="Update" class="form-submit bg-success">
        </div>
</form>