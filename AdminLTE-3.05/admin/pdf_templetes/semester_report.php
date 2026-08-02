<?php
// ================== SAFE INIT ==================
session_start();
$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$class   = $_GET['class'] ?? '';
$section = $_GET['section'] ?? '';
$course  = $_GET['course'] ?? '';
$branch  = $_GET['branch'] ?? '';
$session = $_GET['session'] ?? '';
$semester = $_GET['semester'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';


?>

<style>
body{
    font-family: Arial;
    font-size: 12px;
}

h2{
    text-align:center;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse: collapse;
}

table, th, td{
    border:1px solid #000;
}

th{
    background:#f2f2f2;
    padding:8px;
}

td{
    padding:6px;
    text-align:center;
}

.badge-low{
    color:red;
    font-weight:bold;
}

.badge-high{
    color:green;
    font-weight:bold;
}

table{
    border:none !important;
}

td{
    border:none !important;
}

</style>

<?php
// ================== QUERY ==================
if($institute_type == 'college'){

$query = mysqli_query($con,"
SELECT 
a.id,
a.Name,
a.roll_no,
COUNT(am.id) as total_class,
SUM(CASE WHEN am.status='present' THEN 1 ELSE 0 END) as present_class
FROM attendance att
JOIN attendance_meta am ON att.id = am.attendance_id
JOIN accounts a ON a.id = am.user_id
WHERE att.course_id='$course'
AND att.branch_id='$branch'
AND att.session_id='$session'
AND att.semester='$semester'
AND att.subject_id='$subject_id'
AND att.institute_id='$institute_id'
GROUP BY a.id
");

}else{

$query = mysqli_query($con,"
SELECT 
a.id,
a.Name,
a.roll_no,
COUNT(am.id) as total_class,
SUM(CASE WHEN am.status='present' THEN 1 ELSE 0 END) as present_class
FROM attendance att
JOIN attendance_meta am ON att.id = am.attendance_id
JOIN accounts a ON a.id = am.user_id
WHERE att.class_id='$class'
AND att.section_id='$section'
AND att.subject_id='$subject_id'
AND att.institute_id='$institute_id'
GROUP BY a.id
");

}
?>

<h2>Semester Attendance Report</h2>

<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Roll No</th>
            <th>Name</th>
            <th>Total</th>
            <th>Present</th>
            <th>Absent</th>
            <th>%</th>
        </tr>
    </thead>

    <tbody>
    <?php
    $i = 1;
    while($row = mysqli_fetch_assoc($query)){

        $total = $row['total_class'];
        $present = $row['present_class'];
        $absent = $total - $present;

        $percent = ($total > 0) ? ($present / $total) * 100 : 0;
    ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= $row['roll_no'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td><?= $total ?></td>
            <td><?= $present ?></td>
            <td><?= $absent ?></td>
            <td>
                <?php if($percent < 75){ ?>
                    <span class="badge-low"><?= round($percent,2) ?>%</span>
                <?php } else { ?>
                    <span class="badge-high"><?= round($percent,2) ?>%</span>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>