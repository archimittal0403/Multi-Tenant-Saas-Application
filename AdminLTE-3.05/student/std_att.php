<?php include('includes/auth.php');
checkRole('student');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>

<?php

$std_id = $_SESSION['user_id'];

$institute_id = $_SESSION['institute_id'];

// STUDENT DETAILS
$course_id  = get_usermeta($std_id,'course_name');
$branch_id  = get_usermeta($std_id,'branch_name');
$session_id = get_usermeta($std_id,'session');
$semester   = get_usermeta($std_id,'semester');


$subjects = [];
$attendance = [];
$dates = [];

// QUERY
$query = mysqli_query($con,"
SELECT
att.attendance_date,
am.status,
p.title as subject_name,
COUNT(am.id) as total_class,
SUM(
CASE WHEN am.status='present' THEN 1
ELSE 0
END) as present_class

FROM attendance att

JOIN attendance_meta am
ON att.id = am.attendance_id

JOIN posts p
ON p.id = att.subject_id

WHERE am.user_id='$std_id'

AND att.course_id='$course_id'
AND att.branch_id='$branch_id'
AND att.session_id='$session_id'
AND att.semester='$semester'

GROUP BY p.title
ORDER BY att.attendance_date ASC

");

$total_class = 0;
$present_class = 0;
while($row = mysqli_fetch_assoc($query)){

    $date = date('d/m/y', strtotime($row['attendance_date']));

    $subject = $row['subject_name'];

    $status = strtoupper(substr($row['status'],0,1));

    // COUNT TOTAL
    $total_class++;

    // COUNT PRESENT
    if($row['status']=='present'){
        $present_class++;
    }

    // SUBJECTS
    $subjects[$subject] = $subject;

    // DATES
    $dates[$date] = $date;

    // ATTENDANCE MATRIX
    $attendance[$date][$subject] = $status;
}
$absent_class = $total_class - $present_class;

$percentage = 0;

if($total_class > 0){

    $percentage = ($present_class / $total_class) * 100;
}
?>


<style>

.page-title{
    font-size:30px;
    font-weight:700;
    color:#2c3e50;
}

.attendance-card{
    border:none;
    border-radius:24px;
    overflow:hidden;
    background:#fff;
    box-shadow:0 10px 35px rgba(0,0,0,0.08);
}

.stats-card{
    border-radius:20px;
    padding:25px;
    color:#fff;
    position:relative;
    overflow:hidden;
    transition:0.3s;
    box-shadow:0 6px 18px rgba(0,0,0,0.1);
}

.stats-card:hover{
    transform:translateY(-5px);
}

.stats-card h2{
    font-size:34px;
    font-weight:700;
    margin-bottom:5px;
}

.stats-card p{
    margin:0;
    font-size:15px;
    opacity:0.9;
}

.bg-blue{
    background:linear-gradient(135deg,#4facfe,#00f2fe);
}

.bg-green{
    background:linear-gradient(135deg,#43e97b,#38f9d7);
}

.bg-red{
    background:linear-gradient(135deg,#fa709a,#fee140);
}

.bg-purple{
    background:linear-gradient(135deg,#667eea,#764ba2);
}

.attendance-table{
    border-radius:20px;
    overflow:hidden;
}

.attendance-table thead th{
    background:#111827;
    color:#fff;
    border:none;
    padding:16px;
    font-size:15px;
    white-space:nowrap;
}

.attendance-table tbody td{
    padding:14px;
    vertical-align:middle;
    white-space:nowrap;
    font-size:14px;
}

.attendance-table tbody tr{
    transition:0.2s;
}

.attendance-table tbody tr:hover{
    background:#f8fafc;
    transform:scale(1.002);
}

.subject-header{
    min-width:120px;
}

.date-column{
    font-weight:600;
    color:#374151;
}

.badge-present{
    background:#dcfce7;
    color:#15803d;
    padding:8px 14px;
    border-radius:30px;
    font-weight:700;
    font-size:13px;
    display:inline-block;
    min-width:42px;
}

.badge-absent{
    background:#fee2e2;
    color:#dc2626;
    padding:8px 14px;
    border-radius:30px;
    font-weight:700;
    font-size:13px;
    display:inline-block;
    min-width:42px;
}

.progress{
    height:18px;
    border-radius:30px;
    background:#e5e7eb;
}

.progress-bar{
    border-radius:30px;
    font-weight:600;
}

.summary-section{
    padding:25px;
}

.table-responsive{
    padding:20px;
}

.glass-header{
    background:linear-gradient(135deg,#667eea,#764ba2);
    padding:25px;
    border-radius:20px;
    color:#fff;
    margin-bottom:25px;
}

.glass-header h4{
    margin:0;
    font-size:26px;
    font-weight:700;
}

.glass-header p{
    margin:0;
    opacity:0.9;
}

</style>



<div class="content-header">

<div class="container-fluid">

<div class="glass-header d-flex justify-content-between align-items-center">

<div>
<h4>
<i class="fas fa-calendar-check"></i>
Student Attendance Dashboard
</h4>

<p>
Track your daily attendance records
</p>
</div>

<div>
<i class="fas fa-user-graduate fa-3x"></i>
</div>

</div>

</div>

</div>



<div class="container-fluid">

<div class="row mb-4">

<div class="col-md-3 col-6 mb-3">

<div class="stats-card bg-blue">

<h2><?= $total_class ?></h2>

<p>Total Classes</p>

</div>

</div>


<div class="col-md-3 col-6 mb-3">

<div class="stats-card bg-green">

<h2><?= $present_class ?></h2>

<p>Present Classes</p>

</div>

</div>


<div class="col-md-3 col-6 mb-3">

<div class="stats-card bg-red">

<h2><?= $absent_class ?></h2>

<p>Absent Classes</p>

</div>

</div>


<div class="col-md-3 col-6 mb-3">

<div class="stats-card bg-purple">

<h2><?= round($percentage,2) ?>%</h2>

<p>Attendance Percentage</p>

</div>

</div>

</div>



<div class="card attendance-card">

<div class="summary-section">

<h5 class="mb-3">
Overall Attendance Progress
</h5>

<div class="progress">

<div class="progress-bar 
<?php
if($percentage >= 75){
    echo 'bg-success';
}elseif($percentage >= 50){
    echo 'bg-warning';
}else{
    echo 'bg-danger';
}
?>"

style="width:<?= $percentage ?>%">

<?= round($percentage,2) ?>%

</div>

</div>

</div>



<div class="table-responsive">

<table class="table attendance-table">

<thead>

<tr>

<th>Date</th>

<?php foreach($subjects as $subject){ ?>

<th class="subject-header">
<?= $subject ?>
</th>

<?php } ?>

</tr>

</thead>

<tbody>

<?php foreach($dates as $date){ ?>

<tr>

<td class="date-column">

<?= $date ?>

</td>

<?php foreach($subjects as $subject){ ?>

<td>

<?php

if(isset($attendance[$date][$subject])){

    $status = $attendance[$date][$subject];

    if($status == 'P'){

        echo '<span class="badge-present">
        <i class="fas fa-check-circle"></i> P
        </span>';

    }else{

        echo '<span class="badge-absent">
        <i class="fas fa-times-circle"></i> A
        </span>';
    }

}else{

    echo '<span class="text-muted">-</span>';
}

?>

</td>

<?php } ?>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>
<?php include('footer.php');?>