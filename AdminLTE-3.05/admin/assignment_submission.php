<?php include('includes/auth.php');
checkRole('admin');?>

<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>
<?php
$institute_id = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];
?>

<?php
if(isset($_GET['action'])&& $_GET['action']=='edit_mode'){
$edit_id=$_GET['edit_id'];
$subject_id=$_GET['subject_id'];
$session_id=$_GET['session'];
// insert into 
$edit_insert=mysqli_query($con,"INSERT INTO student_assignments (user_id,subject_id,session,institute_id) VALUES('$edit_id','$subject_id','$session_id','$institute_id')");
if($edit_insert){
    echo "<script>alert('Updation has been done successfully')</script>";
    echo "<script>window.href('assignment_submission.php?','_self')</script>";
}
}
?>
<?php

$course_id=$_GET['course_id'];
$branch_id=$_GET['branch_id'];
$semester=$_GET['semester'];
$session=$_GET['session'];
$subject_id=$_GET['subject_id'];
$select_profile=mysqli_query($con,"SELECT a.id,a.Name,a.roll_no,a.institute_id FROM accounts as a 
JOIN usermeta as u ON u.user_id=a.id AND u.meta_key='course_name' AND u.meta_value='$course_id'
JOIN usermeta as u1 ON u1.user_id=a.id AND u1.meta_key='branch_name' AND u1.meta_value='$branch_id'
JOIN usermeta as u2 ON u2.user_id=a.id AND u2.meta_key='semester' AND u2.meta_value='$semester'
JOIN usermeta as u3 ON u3.user_id=a.id AND u3.meta_key='session' AND u3.meta_value='$session'"
);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission</title>
</head>
<body>
   <h2 class="mt-3 ml-3">Assignment Submission</h2> 

   <h4 class="mt-3 ml-3">Student Details</h4>
   <div class="card">
    <div class="card-body">
        <div class="table-responsible">
            <table class="table table-bordered w-100">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Roll Number</th>
                        <th>Student Name</th>
                        <th>Submission Status</th>
                        <th>Action</th>
                        <th>Submitted Date</th>
                    </tr>
                </thead>
                <?php 
$count = 1;

while($fetch_profile = mysqli_fetch_assoc($select_profile)){

    $student_id = $fetch_profile['id'];
    $name       = $fetch_profile['Name'];
    $roll_no    = $fetch_profile['roll_no'];
    $institute_id=$fetch_profile['institute_id'];
// now get the data from the student-submissions
$select_data=mysqli_query($con,"SELECT * FROM student_assignments WHERE user_id='$student_id' AND institute_id='$institute_id' AND subject_id='$subject_id' AND session='$session'");
$count=0;
if(mysqli_num_rows($select_data) > 0){
    $row = mysqli_fetch_assoc($select_data);
    $count++;
    $status = "Submitted";
    $file=$row['file'];
    $file_path = "../student/uploads/assignment/".$file;
    $submitted = $row['created_at']; // अगर column है
}else{
    $status = "Pending";
    $submitted = "-";
}
echo "<tr>";
echo "<td>".$count."</td>";
echo "<td>".$roll_no."</td>";
echo "<td>".$name."</td>";

if(isset($_GET['action']) && $_GET['action']=='edit_mode' && $edit_id == $student_id){
    echo "<td><input type='text' name='edit_status' value='".$status."'></td>";
}else{
    echo "<td>".$status."</td>";
}
    echo "<td>";
if($status == "Submitted"){
    echo "<a href='$file_path' target='_blank' class='btn btn-sm btn-primary'>View</a>";
}
echo "<a href='assignment_submission.php?action=edit_mode&course_id=$course_id&branch_id=$branch_id&semester=$semester&session=$session&subject_id=$subject_id&edit_id=$student_id' class='btn btn-sm btn-success'>Submission</a>";
echo "</td>";

echo "<td>".$submitted."</td>";
echo "</tr>";
} 

                ?>
            </table>
        </div>
    </div>
   </div>
</body>
</html>