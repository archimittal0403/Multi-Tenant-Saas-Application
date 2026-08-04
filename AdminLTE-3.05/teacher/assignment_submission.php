<?php
include('includes/auth.php');
checkRole('teacher');

include('includes/config.php');
include('header.php');
include('sidebar.php');

$institute_id = $_SESSION['institute_id'];

// ================= GET URL DATA =================

$subject_id       = $_GET['subject_id'] ?? '';
$class_id         = $_GET['class_id'] ?? '';
$section_id       = $_GET['section_id'] ?? '';
$academic_session = $_GET['session'] ?? '';
$course_id=$_GET['course_id'];
$branch_id=$_GET['branch_id'];
?>


<div class="container-fluid">

<div class="card shadow">

<div class="card-header bg-primary">

<h4 class="mb-0">
Student Assignment Submissions
</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>
<th>S.No</th>
<th>Student Name</th>

<th>File</th>
<th>Date</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php

$query = mysqli_query($con,"
SELECT sa.*, u.Name as student_name
FROM student_assignments sa

LEFT JOIN accounts u
ON sa.user_id = u.id

WHERE sa.subject_id='$subject_id'
AND sa.session='$academic_session'
AND sa.institute_id='$institute_id'

ORDER BY sa.id DESC
");

if(mysqli_num_rows($query) > 0){

$i = 1;

while($row = mysqli_fetch_assoc($query)){

$file = $row['file'];

$file_path = '../student/uploads/assignment/'.$file;

?>

<tr>

<td><?php echo $i++; ?></td>

<td>
<?php echo $row['student_name']; ?>
</td>



<td>

<?php if(!empty($file)){ ?>

<a href="<?php echo $file_path; ?>" target="_blank">

<?php echo $file; ?>

</a>

<?php } ?>

</td>

<td>
<?php echo $row['created_at']; ?>
</td>

<td>

<a href="<?php echo $file_path; ?>"
   target="_blank"
   class="btn btn-sm btn-primary">

View

</a>

<a href="<?php echo $file_path; ?>"
   download
   class="btn btn-sm btn-success">

Download

</a>

</td>

</tr>

<?php
}

}else{
?>

<tr>
<td colspan="7" class="text-center">
No Assignment Submitted
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>



<?php include('footer.php'); ?>