<?php include('includes/auth.php'); ?>
<?php checkRole('teacher'); ?>

<?php include('includes/config.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('includes/functions.php'); ?>

<?php

$institute_id = $_SESSION['institute_id'];
$user_id      = $_SESSION['user_id'];
$institute_type=$_SESSION['system_type'];
/* ===========================
   GET TEACHER NAME
=========================== */

$get_teacher = $con->prepare("
    SELECT name
    FROM accounts
    WHERE id=?
");

$get_teacher->bind_param("i",$user_id);

$get_teacher->execute();

$teacher_result = $get_teacher->get_result();

$teacher = $teacher_result->fetch_assoc();

$teacher_name = $teacher['name'];

/* ===========================
   GET ALL PERIODS
=========================== */

$args = array(
    'type'         => 'period',
    'institute_id' => $institute_id
);

$periods = get_posts($args);

?>

<style>

.content-wrapper{
    background:#f4f6f9;
    min-height:100vh;
}

.content-header{
    padding:15px 15px 5px;
}

.content-header h1{
    font-size:30px;
    font-weight:700;
    color:#222;
}

.timetable-card{
    background:#fff;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 3px 15px rgba(0,0,0,0.06);
}

.timetable-header{
    background:linear-gradient(135deg,#4e73df,#224abe);
    color:#fff;
    padding:18px 22px;
}

.timetable-header h3{
    margin:0;
    font-size:22px;
    font-weight:700;
}

.table-timetable{
    width:100%;
    margin:0;
}

.table-timetable thead th{
    background:#f8f9fd;
    padding:14px 10px;
    border:none;
    font-size:13px;
    font-weight:700;
    text-transform:uppercase;
}

.table-timetable tbody td{
    padding:10px;
    border-top:1px solid #f1f1f1;
    vertical-align:top;
}

.period-box{
    min-width:180px;
}

.period-title{
    font-size:14px;
    font-weight:700;
    color:#222;
}

.period-time{
    font-size:12px;
    color:#777;
    margin-top:4px;
}

/* LECTURE CARD */

.lecture-card{
    background:#eef2ff;
    border-left:4px solid #4e73df;
    border-radius:10px;
    padding:10px;
    min-height:90px;
}

.subject-name{
    font-size:14px;
    font-weight:700;
    color:#224abe;
}

.class-name{
    font-size:12px;
    color:#444;
    margin-top:6px;
}

.semester-name{
    font-size:11px;
    color:#777;
    margin-top:4px;
}

.empty-box{
    color:#bbb;
    font-size:12px;
    padding-top:25px;
    text-align:center;
}

@media(max-width:768px){

    .table-timetable thead th{
        font-size:11px;
        padding:10px 5px;
    }

    .lecture-card{
        padding:7px;
    }

    .subject-name{
        font-size:12px;
    }

    .class-name{
        font-size:11px;
    }

}

</style>


<div class="content-header">

<h1>

<i class="fas fa-chalkboard-teacher text-primary"></i>

Teacher Time Table

</h1>

</div>



<div class="container-fluid">

<div class="timetable-card">

<div class="timetable-header">

<h3>
Weekly Teaching Schedule
</h3>

<p>
<?=$teacher_name?>
</p>

</div>

<div class="table-responsive">

<table class="table table-timetable">

<thead>

<tr>

<th width="220">
Period
</th>

<th>Monday</th>
<th>Tuesday</th>
<th>Wednesday</th>
<th>Thursday</th>
<th>Friday</th>
<th>Saturday</th>

</tr>

</thead>

<tbody>

<?php

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

if($periods){

foreach($periods as $period){

$from = get_metadata($period->id,'from')[0]->meta_value;
$to   = get_metadata($period->id,'to')[0]->meta_value;

?>

<tr>

<td>

<div class="period-box">

<div class="period-title">
<?=$period->title?>
</div>

<div class="period-time">

<?=date('h:i A',strtotime($from))?>

-

<?=date('h:i A',strtotime($to))?>

</div>

</div>

</td>

<?php

foreach($days as $day){

?>

<td>

<?php

$get_lecture = $con->prepare("

SELECT
tl.*,
t.course_id,
t.branch_id,
t.semester_id,
t.class_id,
t.section_id,
t.session_id,
t.academic_session

FROM timetable_lectures tl

LEFT JOIN timetables t
ON tl.timetable_id = t.id

WHERE LOWER(TRIM(tl.teacher_name)) = LOWER(TRIM(?))
AND tl.period_id=?
AND tl.day=?


");

$get_lecture->bind_param(
    "sis",
    $teacher_name,
    $period->id,
    $day
);

$get_lecture->execute();

$lecture_result = $get_lecture->get_result();

if($lecture_result->num_rows > 0){

$lecture = $lecture_result->fetch_assoc();

/* ===========================
   GET COURSE
=========================== */

$course = get_posts([
    'id' => $lecture['course_id']
]);

$branch = get_posts([
    'id' => $lecture['branch_id']
]);
$class = !empty($lecture['class_id'])
    ? get_posts(['id'=>$lecture['class_id']])
    : [];

$section = !empty($lecture['section_id'])
    ? get_posts(['id'=>$lecture['section_id']])
    : [];
if(!empty($lecture['course_id']) && $lecture['course_id'] != 0){
    $session_id = $lecture['semester_id'];
    $session=!empty($lecture['session_id'])
    ? get_posts(['id'=>$lecture['session_id']])
    : [];
} else {
    $semester = $lecture['academic_session'];
}

?>

<div class="lecture-card">

<div class="subject-name">

<?=$lecture['subject_name']?>

</div>

<div class="class-name">
    <?php 
  if(!empty($lecture['course_id']) && $lecture['course_id'] != 0){?>
<?=isset($course[0]) ? $course[0]->title : ''?>
<?=isset($branch[0]) ? $branch[0]->title : ''?>
<?php } else {?>

<?= isset($class[0]) ? $class[0]->title : '' ?>

<?= isset($section[0]) ? $section[0]->title : '' ?>

<?php } ?>
</div>
<div class="semester-name">

Session :
<?=isset($session[0]) ? $session[0]->title : ''?>

</div>

</div>

</div>

<?php } else { ?>

<div class="empty-box">

No Lecture

</div>

<?php } ?>

</td>

<?php } ?>

</tr>

<?php
}
}
?>

</tbody>

</table>

</div>

</div>

</div>



<?php include('footer.php'); ?>