<?php
// ================== SAFE INIT ==================
session_start();


$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$class_id = $_GET['class'] ?? '';
$section  = $_GET['section'] ?? '';

$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$session  = $_GET['session'] ?? '';
$semester = $_GET['semester'] ?? '';

?>

<style>

body{
    font-family: dejavusans;
    font-size:11px;
    color:#222;
}

.report-title{
    text-align:center;
    font-size:18px;
    font-weight:bold;
    color:#17479e;
    margin-bottom:2px;
}

.report-subtitle{
    text-align:center;
    font-size:10px;
    color:#666;
    margin-bottom:12px;
}

.result-table{
    width:100%;
    border-collapse:collapse;
}

.result-table th{
    background:#17479e;
    color:#fff;
    border:1px solid #000;
    padding:7px;
    font-size:10px;
    font-weight:bold;
    text-align:center;
}

.result-table td{
    border:1px solid #555;
    padding:6px;
    font-size:10px;
    text-align:center;
}

.summary-table{
    width:45%;
    border-collapse:collapse;
    margin-top:12px;
}

.summary-table th{
    background:#17479e;
    color:#fff;
    border:1px solid #000;
    padding:7px;
    text-align:left;
}

.summary-table td{
    border:1px solid #555;
    padding:7px;
}

.pass{
    color:green;
    font-weight:bold;
}

.fail{
    color:red;
    font-weight:bold;
}

</style>
<?php

// ================= STUDENTS =================

if($institute_type == 'college'){

$students = mysqli_query($con,"
SELECT a.id, a.Name, a.roll_no
FROM accounts a

JOIN usermeta u1 
ON u1.user_id = a.id 
AND u1.meta_key='course_name'
AND u1.meta_value='$course'

JOIN usermeta u2 
ON u2.user_id = a.id 
AND u2.meta_key='branch_name'
AND u2.meta_value='$branch'

JOIN usermeta u3 
ON u3.user_id = a.id 
AND u3.meta_key='semester'
AND u3.meta_value='$semester'

JOIN usermeta u4 
ON u4.user_id = a.id 
AND u4.meta_key='session'
AND u4.meta_value='$session'

WHERE a.type='student'
AND a.institute_id='$institute_id'
");

} else {

$students = mysqli_query($con,"
SELECT a.id, a.Name, a.roll_no
FROM accounts a

JOIN usermeta u1 
ON u1.user_id = a.id 
AND u1.meta_key='st_class'
AND u1.meta_value='$class_id'

JOIN usermeta u2 
ON u2.user_id = a.id 
AND u2.meta_key='st_section'
AND u2.meta_value='$section'

WHERE a.type='student'
AND a.institute_id='$institute_id'
");

}

// ================= SUBJECT =================

if($institute_type == 'college'){

$subjects = mysqli_query($con,"
SELECT id, title FROM posts 
WHERE type='subject' 
AND parent='$branch'  
AND institute_id='$institute_id'
");

} else {

$subjects = mysqli_query($con,"
SELECT id, title FROM posts 
WHERE type='subject' 
AND parent='$class_id'  
AND institute_id='$institute_id'
");

}

// ================= EXAMS =================

$exams = mysqli_query($con,"
SELECT id, exam_type , max_marks 
FROM exam_type 
WHERE institute_id='$institute_id' 
AND status='active'
");

// ================= MARKS =================

if($institute_type == 'college'){

$marks_q = mysqli_query($con,"
SELECT rm.student_id, r.subject_id, r.exam_id, rm.marks
FROM result_marks rm
JOIN results r ON r.id = rm.result_id
WHERE r.course_id='$course'
AND r.branch_id='$branch'
AND r.semester_id='$semester'
AND r.session_id='$session'
AND r.institute_id='$institute_id'
");

} else {

$marks_q = mysqli_query($con,"
SELECT rm.student_id, r.subject_id, r.exam_id, rm.marks
FROM result_marks rm
JOIN results r ON r.id = rm.result_id
WHERE r.class_id='$class_id'
AND r.section_id='$section'
AND r.session_id='$session'
AND r.institute_id='$institute_id'
");

}

$marks_data = [];

while($m = mysqli_fetch_assoc($marks_q)){

    $marks_data[$m['student_id']][$m['subject_id']][$m['exam_id']] = $m['marks'];

}

// ================= SUBJECT ARRAY =================

$sub_list = [];

while($s = mysqli_fetch_assoc($subjects)){

    $sub_list[] = $s;

}

// ================= EXAM ARRAY =================

$exam_list = [];

while($e = mysqli_fetch_assoc($exams)){

    $exam_list[] = $e;

}

// ================= GRAND TOTAL =================

$total_max_per_subject = 0;

foreach($exam_list as $ex){

    $total_max_per_subject += $ex['max_marks'];

}

$grand_total_max = $total_max_per_subject * count($sub_list);

// ================= TOPPER =================

$topper_name  = '-';
$topper_marks = 0;

mysqli_data_seek($students,0);

while($stu = mysqli_fetch_assoc($students)){

    $total = 0;

    foreach($sub_list as $sub){

        foreach($exam_list as $ex){

            $m = $marks_data[$stu['id']][$sub['id']][$ex['id']] ?? 0;

            $total += $m;

        }
    }

    if($total > $topper_marks){

        $topper_marks = $total;
        $topper_name  = $stu['Name'];

    }

}

mysqli_data_seek($students,0);

?>

<div class="report-title">
Student Result Report
</div>

<div class="report-subtitle">
Academic Performance Summary
</div>

<table class="result-table">

<thead>

<tr style="background-color:#17479e; color:#ffffff; font-weight:bold;">

<th><b>#</b></th>
<th><b>Roll No</b></th>
<th><b>Name</b></th>

<?php foreach($sub_list as $sub){ ?>

<th>
<b><?= $sub['title'] ?></b>
</th>

<?php } ?>

<th>Total</th>
<th>Percentage</th>
<th>Result</th>

</tr>

</thead>

<tbody>

<?php

$i = 1;

while($stu = mysqli_fetch_assoc($students)){

$total = 0;

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $stu['roll_no'] ?></td>

<td><?= $stu['Name'] ?></td>

<?php

foreach($sub_list as $sub){

    $subject_total = 0;

    foreach($exam_list as $ex){

        $m = $marks_data[$stu['id']][$sub['id']][$ex['id']] ?? 0;

        $subject_total += $m;

        $total += $m;

    }

    echo "<td>".$subject_total."</td>";

}

$percentage = ($grand_total_max > 0)
? round(($total / $grand_total_max) * 100,2)
: 0;

?>

<td>
    <b><?= $total ?></b>
</td>

<td>

<?php if($percentage < 33){ ?>

    <span class="badge-low"><?= $percentage ?>%</span>

<?php } else { ?>

    <span class="badge-high"><?= $percentage ?>%</span>

<?php } ?>

</td>

<td>

<?php if($percentage < 33){ ?>

    <span class="badge-low">FAIL</span>

<?php } else { ?>

    <span class="badge-high">PASS</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<br><br>

<table class="summary-table">

<tr>
<td style="
background-color:#17479e;
color:#ffffff;
font-weight:bold;
width:40%;
">
Total Students
</td>
    <td><?= mysqli_num_rows($students) ?></td>
</tr>

<tr>
 <td style="
background-color:#17479e;
color:#ffffff;
font-weight:bold;
width:40%;
">
Total Subjects
</td>

    <td><?= count($sub_list) ?></td>
</tr>

<tr>
<td style="
background-color:#17479e;
color:#ffffff;
font-weight:bold;
width:40%;
">
Topper Student
</td>
    <td><?= $topper_name ?> (<?= $topper_marks ?>)</td>
</tr>

</table>