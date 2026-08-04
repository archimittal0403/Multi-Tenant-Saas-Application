<?php
session_start();

$course   = $_GET['st_course'] ?? '';
$branch   = $_GET['st_branch'] ?? '';
$semester = $_GET['st_semester'] ?? '';
$session  = $_GET['st_session'] ?? '';
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

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#17479e;
    color:#fff;
    border:1px solid #000;
    padding:8px;
    text-align:center;
}

td{
    border:1px solid #555;
    padding:7px;
    text-align:center;
}

.summary-table{
    width:45%;
    margin-top:20px;
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
    a.id,
    a.Name,
    a.roll_no

FROM accounts a

JOIN usermeta uc
    ON a.id = uc.user_id
    AND uc.meta_key='course_name'
    AND uc.meta_value='$course'

JOIN usermeta ub
    ON a.id = ub.user_id
    AND ub.meta_key='branch_name'
    AND ub.meta_value='$branch'

JOIN usermeta us
    ON a.id = us.user_id
    AND us.meta_key='session'
    AND us.meta_value='$session'

JOIN usermeta um
    ON a.id = um.user_id
    AND um.meta_key='semester'
    AND um.meta_value='$semester'

LEFT JOIN admit_cards ac
    ON ac.student_id=a.id
    AND ac.exam_id='$exam_id'
    AND ac.course_id='$course'
    AND ac.branch_id='$branch'
    AND ac.semester='$semester'
    AND ac.academic_session='$session'
    AND ac.institute_id='$institute_id'

WHERE a.type='student'
AND a.institute_id='$institute_id'
AND ac.student_id IS NULL

ORDER BY a.roll_no ASC
");

$total_pending = mysqli_num_rows($q);
?>

<div class="report-title">
Pending Admit Card Report
</div>

<div class="report-subtitle">
Students Whose Admit Cards Are Not Generated
</div>




<table border="1" cellpadding="6">

<tr style="background-color:#17479e;color:#ffffff;font-weight:bold;">
    <th width="10%">S.No</th>
    <th width="20%">Roll No</th>
    <th width="40%">Student Name</th>
    <th width="30%">Generated Date</th>
</tr>



<tbody>
<?php

$i = 1;

if(mysqli_num_rows($q) > 0){

    while($row = mysqli_fetch_assoc($q)){
?>

<tr>
    <td><?= $i++ ?></td>
    <td><?= $row['roll_no'] ?></td>
    <td><?= $row['Name'] ?></td>
    <td>Pending</td>
</tr>

<?php
    }

}else{
?>

<tr>
    <td colspan="4" style="color:red;">
        No Pending Students Found
    </td>
</tr>

<?php } ?>
</tbody>
</table>

<br><br>

<table class="summary-table">

<tr>
    <td>
      Pending Admit Cards
    </td>

    <td>
        <?= $total_pending ?>
    </td>
</tr>

</table>