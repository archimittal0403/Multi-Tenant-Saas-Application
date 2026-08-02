<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$class     = $_GET['class'] ?? '';
$section   = $_GET['section'] ?? '';
$course    = $_GET['course'] ?? '';
$branch    = $_GET['branch'] ?? '';
$semester  = $_GET['semester'] ?? '';
$session_id   = $_GET['session'] ?? '';
$session_name=mysqli_query($con,"SELECT title FROM `posts` WHERE id='$session_id' AND institute_id='$institute_id'");
$row_session=mysqli_fetch_assoc($session_name);
$session=$row_session['title'];

?>

<style>
body{
    font-family:Arial;
    font-size:12px;
}

h2{
    text-align:center;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
}

table,th,td{
    border:1px solid #000;
}

th{
    background:#f2f2f2;
    padding:8px;
    text-align:center;
    font-weight:bold;
}
td{
    padding:6px;
    text-align:center;
}
</style>

<?php

// ====================== QUERY =====================

if($institute_type=='college'){

$query=mysqli_query($con,"
SELECT
a.id,
a.roll_no,
a.Name,
a.email,

MAX(CASE WHEN um.meta_key='mobile' THEN um.meta_value END) mobile,
MAX(CASE WHEN um.meta_key='dob' THEN um.meta_value END) dob,
MAX(CASE WHEN um.meta_key='course_name' THEN um.meta_value END) course,
MAX(CASE WHEN um.meta_key='branch_name' THEN um.meta_value END) branch,
MAX(CASE WHEN um.meta_key='semester' THEN um.meta_value END) semester,
MAX(CASE WHEN um.meta_key='session' THEN um.meta_value END) session

FROM accounts a

LEFT JOIN usermeta um
ON a.id=um.user_id

WHERE
a.type='student'
AND a.institute_id='$institute_id'

GROUP BY a.id
HAVING
course='$course'
AND branch='$branch'
AND session='$session'

ORDER BY a.roll_no ASC
");

}else{
$query=mysqli_query($con,"
SELECT
a.id,
a.roll_no,
a.Name,
a.email,

MAX(CASE WHEN um.meta_key='mobile' THEN um.meta_value END) mobile,
MAX(CASE WHEN um.meta_key='dob' THEN um.meta_value END) dob,
MAX(CASE WHEN um.meta_key='st_class' THEN um.meta_value END) class_id,
MAX(CASE WHEN um.meta_key='st_section' THEN um.meta_value END) section_id,
MAX(CASE WHEN um.meta_key='session' THEN um.meta_value END) session

FROM accounts a

LEFT JOIN usermeta um
ON a.id=um.user_id

WHERE
a.type='student'
AND a.institute_id='$institute_id'

GROUP BY a.id

HAVING
class_id='$class'
AND section_id='$section'
AND session='$session'

ORDER BY a.roll_no ASC
");

}

?>

<h2>Student Report</h2>

<table>

<thead>

<tr>

<th><strong>S.No</strong></th>
<th><strong>Roll No</strong></th>
<th><strong>Name</strong></th>
<th><strong>Email</strong></th>
<th><strong>Mobile</strong></th>
<th><strong>DOB</strong></th>

<?php if($institute_type=='college'){ ?>


<th><strong>Session</strong></th>
<th><strong>Semester</strong></th>

<?php }else{ ?>


<th><strong>Session</strong></th>

<?php } ?>

</tr>

</thead>

<tbody>

<?php

$i=1;

while($row=mysqli_fetch_assoc($query)){

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $row['roll_no'] ?></td>

<td><?= $row['Name'] ?></td>

<td><?= $row['email'] ?></td>

<td><?= $row['mobile'] ?></td>

<td><?= $row['dob'] ?></td>

<?php if($institute_type=='college'){ ?>



<td><?= $row['session'] ?></td>
<td><?= $row['semester'] ?></td>

<?php }else{ ?>



<td><?= $row['session'] ?></td>

<?php } ?>

</tr>

<?php } ?>

</tbody>

</table>