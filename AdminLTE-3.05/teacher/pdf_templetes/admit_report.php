<?php
session_start();
$course   = $_GET['st_course'] ?? '';
$branch   = $_GET['st_branch'] ?? '';
$semester = $_GET['st_semester'] ?? '';
$session  = $_GET['academic_session'] ?? '';
$exam_id  = $_GET['exam_id'] ?? '';

$institute_id = $_SESSION['institute_id'];

?>

<style>

body{
    font-family: dejavusans;
    font-size:11px;
}

.report-title{
    text-align:center;
    font-size:18px;
    font-weight:bold;
    color:#17479e;
    margin-bottom:5px;
}

.report-subtitle{
    text-align:center;
    font-size:11px;
    color:#666;
    margin-bottom:15px;
}

.report-table{
    width:100%;
    border-collapse:collapse;
}

.report-table th{
    background:#17479e;
    color:#fff;
    border:1px solid #000;
    padding:8px;
    text-align:center;
}

.report-table td{
    border:1px solid #555;
    padding:7px;
    text-align:center;
}

.summary-table{
    width:45%;
    border-collapse:collapse;
    margin-top:20px;
}

.summary-table td{
    border:1px solid #555;
    padding:8px;
}

.summary-head{
    background:#17479e;
    color:#fff;
    font-weight:bold;
}

</style>

<?php

$q = mysqli_query($con,"
SELECT
    a.roll_no,
    a.name,
    ac.created_at

FROM admit_cards ac

JOIN accounts a
ON a.id = ac.student_id

WHERE ac.exam_id='$exam_id'
AND ac.course_id='$course'
AND ac.branch_id='$branch'
AND ac.semester='$semester'
AND ac.academic_session='$session'
AND ac.institute_id='$institute_id'

ORDER BY a.roll_no ASC
");

$total_generated = mysqli_num_rows($q);

?>

<div class="report-title">
Generated Admit Card Report
</div>

<div class="report-subtitle">
Generated Admit Cards Student List
</div>

<table border="1" cellpadding="6" >

<thead>

<tr style="background-color:#17479e;color:#ffffff;font-weight:bold;">
    <th>S.No</th>
    <th>Roll No</th>
    <th>Student Name</th>
    <th>Generated Date</th>
</tr>

</thead>

<tbody>

<?php

$i = 1;

if(mysqli_num_rows($q) > 0){

    while($row = mysqli_fetch_assoc($q)){

?>

<tr>

    <td><?= $i++ ?></td>

    <td><?= $row['roll_no'] ?></td>

    <td><?= $row['name'] ?></td>

    <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>

</tr>

<?php

    }

}else{

?>

<tr>

    <td colspan="4" style="color:red;">
        No Generated Admit Cards Found
    </td>

</tr>

<?php } ?>

</tbody>

</table>

<br><br>

<table class="summary-table">

<tr>

    <td>
        Generated Admit Cards
    </td>

    <td>
        <?= $total_generated ?>
    </td>

</tr>

</table>