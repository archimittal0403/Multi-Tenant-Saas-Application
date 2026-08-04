<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>


<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
$edit_id=$_GET['edit_id'];

$select=mysqli_query($con,"SELECT date,file,subject_id FROM `assignment` WHERE id='$edit_id'");
$select_fetch=mysqli_fetch_assoc($select);
$subject_id=$select_fetch['subject_id'];
$date=$select_fetch['date'];
$file=$select_fetch['file'];
// get the course name
  $course=mysqli_query($con,"SELECT name FROM `courses` WHERE id='$subject_id'");
                    $course_fetch=mysqli_fetch_assoc($course);
                    $course_name=$course_fetch['name'];
?>
<div class="card">
    <div class="card-body">
        <h2 class="text-center mt-4">Update Assignment</h2>
        <form method="post" enctype="multipart/form-data">
        <h4 class="mt-4">Subject Name:-<?php echo $course_name ?>  
        </h4>
           <h4 class="mt-4">Date:-<?php echo $date?>  
        </h4>
        <div class="form-group">
            <h4 class="mt-4" class="form-control">Uploaded Assignment</h4>
<input type="file" name="update_file" id="update_file" value="uploads/assignment/<?php $file ?>">
        </div>
</form>
    </div>
</div>