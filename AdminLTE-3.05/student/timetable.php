<?php include('includes/auth.php'); ?>
<?php checkRole('student'); ?>
<?php include('includes/config.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('includes/functions.php'); ?>

<?php

$institute_id = $_SESSION['institute_id'];
$user_id      = $_SESSION['user_id'];
$institute_type=$_SESSION['system_type'];
$class_id=get_usermeta($user_id,'st_class');
$section_id=get_usermeta($user_id,'st_section');

$year = date('Y');
$next_year = $year + 1;

$academic_session = $year.'-'.$next_year;
$course_id   = get_usermeta($user_id,'course_name');
$branch_id   = get_usermeta($user_id,'branch_name');
$semester_id = get_usermeta($user_id,'semester');
$session_id  = get_usermeta($user_id,'session');

// GET PERIODS

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

.container-fluid{
    width:100%;
    max-width:100%;
    padding:0 15px;
}

/* HEADER */

.content-header{
    padding:15px 0 5px;
}

.content-header h1{
  
    font-size:34px;
    font-weight:700;
    color:#222;
    margin:0;
}

/* CARD */

.timetable-wrapper{
    padding:10px 0 25px;
}

.timetable-card{
    width:100%;
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 3px 15px rgba(0,0,0,0.06);
}

/* BLUE HEADER */

.timetable-header{
    padding:16px 22px;
    background:linear-gradient(135deg,#4e73df,#224abe);
    color:#fff;
}

.timetable-header h3{
    margin:0;
    font-size:22px;
    font-weight:700;
}

.timetable-header p{
    margin:4px 0 0;
    font-size:13px;
    opacity:0.9;
}

/* TABLE */

.table-responsive{
    padding:0;
}

.table-timetable{
    margin:0;
    width:100%;
}

.table-timetable thead th{
    background:#f7f8fc;
    border:none;
    padding:14px 10px;
    
    font-size:13px;
    font-weight:700;
    color:#444;
    text-transform:uppercase;
}

.table-timetable tbody td{
    padding:10px;
    border-top:1px solid #f1f1f1;
    vertical-align:top;
}

/* PERIOD COLUMN */

.period-box{
    min-width:160px;
}

.period-title{
    font-size:14px;
    font-weight:700;
    color:#222;
    margin-bottom:4px;
}

.period-time{
    font-size:12px;
    color:#777;
}

/* SUBJECT BOX */

.day-box{
    min-height:95px;
    background:#f8f9fd;
    border-radius:12px;
    padding:10px;
    transition:0.3s;
}

.day-box:hover{
    background:#eef2ff;
    transform:translateY(-2px);
}

.subject-name{
    font-size:14px;
    font-weight:700;
    color:#4e73df;
    margin-bottom:6px;
}

.teacher-name{
    font-size:13px;
    color:#555;
}

.empty-box{
    font-size:12px;
    color:#b2b2b2;
    
    padding-top:25px;
}

/* NO TIMETABLE */

.no-timetable{
    background:#fff;
    padding:40px;
    border-radius:14px;
   
    box-shadow:0 3px 15px rgba(0,0,0,0.06);
}

/* MOBILE */

@media(max-width:768px){

    .content-header h1{
        font-size:24px;
    }

    .table-timetable thead th{
        font-size:11px;
        padding:10px 5px;
    }

    .period-title{
        font-size:12px;
    }

    .period-time{
        font-size:10px;
    }

    .subject-name{
        font-size:12px;
    }

    .teacher-name{
        font-size:11px;
    }

    .day-box{
        min-height:75px;
        padding:8px;
    }

}

</style>



<div class="content-header">
<div class="container-fluid">

<h1 class="mb-4">

<i class="fas fa-calendar-alt text-primary"></i>

Student Time Table

</h1>

</div>
</div>


<div class="container-fluid">

<?php

if($institute_type == 'college'){

    $get_timetable = $con->prepare("

        SELECT id FROM timetables

        WHERE institute_id=?
        AND course_id=?
        AND branch_id=?
        AND semester_id=?
        AND session_id=?

    ");

    $get_timetable->bind_param(
        "iiiii",
        $institute_id,
        $course_id,
        $branch_id,
        $semester_id,
        $session_id
    );

}
else{

    $get_timetable = $con->prepare("

        SELECT id FROM timetables

        WHERE institute_id=?
        AND class_id=?
        AND section_id=?
        AND academic_session=?

    ");

    $get_timetable->bind_param(
        "iiis",
        $institute_id,
        $class_id,
        $section_id,
        $academic_session
    );

}

$get_timetable->execute();

$timetable_result = $get_timetable->get_result();

if($timetable_result->num_rows > 0){

    $timetable_row = $timetable_result->fetch_assoc();

    $timetable_id = $timetable_row['id'];
        $timetable_id = $timetable_row['id'];

?>

<div class="timetable-wrapper">

<div class="timetable-card">

<div class="timetable-header">

<h3>
Weekly Class Schedule
</h3>

<p>
View your complete class timetable
</p>

</div>

<div class="table-responsive">

<table class="table table-timetable">

<thead>

<tr>

<th width="220">Period</th>

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

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

foreach($days as $day){

?>

<td>

<div class="day-box">

<?php

$get_lecture = $con->prepare("

    SELECT * FROM timetable_lectures

    WHERE timetable_id=?
    AND period_id=?
    AND day=?

");

$get_lecture->bind_param(
    "iis",
    $timetable_id,
    $period->id,
    $day
);

$get_lecture->execute();

$lecture_result = $get_lecture->get_result();

if($lecture_result->num_rows > 0){

    $lecture = $lecture_result->fetch_assoc();

?>

<div class="subject-name">
<?=$lecture['subject_name']?>
</div>

<div class="teacher-name">
<?=$lecture['teacher_name']?>
</div>

<?php } else { ?>

<div class="empty-box">

No Lecture

</div>

<?php } ?>

</div>

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

<?php } else { ?>

<div class="no-timetable">

<h4>No Timetable Found</h4>

<p class="text-muted">
Your timetable has not been created yet.
</p>

</div>

<?php } ?>




</div>


<?php include('footer.php'); ?>