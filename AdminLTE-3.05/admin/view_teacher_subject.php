<?php


include('includes/auth.php');
checkRole('admin');

include('includes/config.php');
require_once('includes/functions.php');

if(
    empty($_SESSION['user_id']) ||
    empty($_SESSION['user_type']) ||
    empty($_SESSION['institute_id'])
){
    header("Location: ../login.php");
    exit;
}

$teacher_id     = $_GET['teacher_id'] ?? '';
$institute_type = $_SESSION['system_type'];

if($teacher_id==''){
    die("Teacher ID Missing");
}

/* =========================
   TEACHER DETAILS
========================= */

$teacher = mysqli_fetch_assoc(
    mysqli_query($con,"
        SELECT * FROM accounts
        WHERE id='$teacher_id'
    ")
);

if(!$teacher){
    die("Teacher Not Found");
}

include('header.php');
include('sidebar.php');

?>

<style>

body{
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
}

.content-wrapper{
    background:#f1f5f9 !important;
}

.page-title{
    font-size:32px;
    font-weight:800;
    color:#0f172a;
}

.teacher-banner{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    border-radius:24px;
    padding:30px;
    color:#fff;
    margin-bottom:30px;
    box-shadow:0 10px 35px rgba(37,99,235,.25);
}

.teacher-banner h2{
    font-size:30px;
    font-weight:800;
    margin-bottom:8px;
}

.teacher-banner p{
    margin:0;
    opacity:.9;
}

.subject-card{
    background:#fff;
    border-radius:22px;
    padding:25px;
    margin-bottom:25px;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
    border:1px solid #e2e8f0;
    transition:.3s;
}

.subject-card:hover{
    transform:translateY(-4px);
}

.subject-title{
    font-size:22px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:22px;
}

.info-box{
    background:#f8fafc;
    border-radius:16px;
    padding:18px;
    border:1px solid #e2e8f0;
    height:100%;
}

.info-box label{
    font-size:13px;
    font-weight:700;
    color:#64748b;
    display:block;
    margin-bottom:6px;
    text-transform:uppercase;
}

.info-box h6{
    font-size:17px;
    font-weight:700;
    color:#0f172a;
    margin:0;
}

.btn-back{
    background:#111827;
    color:#fff;
    border:none;
    border-radius:14px;
    padding:12px 24px;
    font-weight:700;
}

.btn-back:hover{
    background:#000;
    color:#fff;
}

.empty-box{
    background:#fff;
    border-radius:24px;
    padding:60px 20px;
    text-align:center;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
}

@media(max-width:768px){

    .page-title{
        font-size:24px;
    }

    .teacher-banner h2{
        font-size:24px;
    }

}

</style>


<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">

<h2 class="page-title">
📘 Assigned Subjects
</h2>

<a href="teacher.php?user=teacher" class="btn btn-back">
<i class="fa fa-arrow-left"></i>
Back
</a>

</div>

<!-- TEACHER CARD -->

<div class="teacher-banner">

<h2>
<?php echo $teacher['Name']; ?>
</h2>

<p>
Teacher ID :
<?php echo $teacher['roll_no']; ?>
</p>

</div>

<?php

$query = mysqli_query($con,"
SELECT *
FROM teacher_subjects
WHERE teacher_id='$teacher_id'
");

if(mysqli_num_rows($query)>0){

while($row=mysqli_fetch_assoc($query)){

/* =========================
   SUBJECT
========================= */

$subject_q = mysqli_fetch_assoc(
    mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='".$row['subject_id']."'
    ")
);

$subject_title = $subject_q['title'] ?? 'N/A';

/* =========================
   COLLEGE ERP
========================= */

if($institute_type=='college'){

$course_q = mysqli_fetch_assoc(
    mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='".$row['course_id']."'
    ")
);

$branch_q = mysqli_fetch_assoc(
    mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='".$row['branch_id']."'
    ")
);

$course_title = $course_q['title'] ?? 'N/A';
$branch_title = $branch_q['title'] ?? 'N/A';

?>

<div class="subject-card">

<div class="subject-title">

<i class="fa fa-book-open text-primary"></i>

<?php echo $subject_title; ?>

</div>

<div class="row">

<div class="col-md-3 mb-3">

<div class="info-box">

<label>Course</label>

<h6>
<?php echo $course_title; ?>
</h6>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="info-box">

<label>Branch</label>

<h6>
<?php echo $branch_title; ?>
</h6>

</div>

</div>

<div class="col-md-2 mb-3">

<div class="info-box">

<label>Semester</label>

<h6>
<?php echo $row['semester']; ?>
</h6>

</div>

</div>

<div class="col-md-2 mb-3">

<div class="info-box">

<label>Session :</label>

<h6>
<?php echo $row['session_id']; ?>
</h6>

</div>

</div>

<div class="col-md-2 mb-3">

<div class="info-box">

<label>Status</label>

<h6 class="text-success">
Assigned
</h6>

</div>

</div>

</div>

</div>

<?php } else { ?>
<!-- SCHOOL ERP -->

<?php

$class_q = mysqli_fetch_assoc(
    mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='".$row['class_id']."'
    ")
);

$section_q = mysqli_fetch_assoc(
    mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='".$row['section_id']."'
    ")
);

$class_title   = $class_q['title'] ?? 'N/A';
$section_title = $section_q['title'] ?? 'N/A';

?>

<div class="subject-card">

<div class="subject-title">

<i class="fa fa-book-open text-primary"></i>

<?php echo $subject_title; ?>

</div>

<div class="row">

<div class="col-md-3 mb-3">

<div class="info-box">

<label>Class</label>

<h6>
<?php echo $class_title; ?>
</h6>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="info-box">

<label>Section</label>

<h6>
<?php echo $section_title; ?>
</h6>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="info-box">

<label>Academic Session</label>

<h6>
<?php echo $row['session_id']; ?>
</h6>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="info-box">

<label>Status</label>

<h6 class="text-success">
Assigned
</h6>

</div>

</div>

</div>

</div>

<?php
}

}

}else{
?>

<div class="empty-box">

<i class="fa fa-book fa-4x text-muted"></i>

<h3 class="mt-4">
No Subjects Assigned
</h3>

<p class="text-muted">
This teacher has no assigned subjects yet.
</p>

</div>

<?php } ?>

</div>



<?php include('footer.php'); ?>