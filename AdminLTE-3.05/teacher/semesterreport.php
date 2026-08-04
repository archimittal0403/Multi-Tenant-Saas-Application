<?php include('includes/auth.php'); ?>
<?php checkRole('teacher'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$class   = trim($_GET['class'] ?? '');
$section = trim($_GET['section'] ?? '');

$course     = trim($_GET['course'] ?? '');
$branch     = trim($_GET['branch'] ?? '');
$session    = trim($_GET['session'] ?? '');
$semester   = trim($_GET['semester'] ?? '');
$subject_id = trim($_GET['subject_id'] ?? '');

?>

<?php

// ================= COLLEGE QUERY =================
if($institute_type == 'college'){
$totalClassQuery = mysqli_query($con,"
SELECT COUNT(*) as total_classes
FROM attendance
WHERE course_id='$course'
AND branch_id='$branch'
AND session_id='$session'
AND semester='$semester'
AND institute_id='$institute_id'
");

$totalClassRow = mysqli_fetch_assoc($totalClassQuery);
$total_classes = $totalClassRow['total_classes'];

$query = mysqli_query($con,"
SELECT
a.id,
a.Name,
a.roll_no,

SUM(
CASE
WHEN am.status='present' THEN 1
ELSE 0
END
) as present_class

FROM attendance att

JOIN attendance_meta am
ON att.id = am.attendance_id

JOIN accounts a
ON a.id = am.user_id

WHERE att.course_id='$course'
AND att.branch_id='$branch'
AND att.session_id='$session'
AND att.semester='$semester'
AND att.institute_id='$institute_id'

GROUP BY a.id
");

// ================= SCHOOL QUERY =================
}else{

$query = mysqli_query($con,"

SELECT 
a.id,
a.Name,
a.roll_no,

COUNT(am.id) as total_class,

SUM(
CASE 
WHEN am.status='present' THEN 1 
ELSE 0 
END
) as present_class

FROM attendance att

JOIN attendance_meta am 
ON att.id = am.attendance_id

JOIN accounts a
ON a.id = am.user_id

WHERE att.class_id='$class'
AND att.section_id='$section'
AND att.subject_id='$subject_id'
AND att.institute_id='$institute_id'

GROUP BY a.id

");

}

?>

<div class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <div class="d-flex">

                    <h1 class="m-0 text-dark">
                        Attendance Report :-
                    </h1>
<div class="no-print d-flex gap-2">

<?php if($institute_type == 'college'){ ?>

   <a href="pdf.php?type=semester_report&course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>"
   class="btn btn-danger">
   📄 Generate PDF
</a>

<?php } else { ?>

  
   <a href="pdf.php?type=semester_report&class=<?= $class ?>&section=<?= $section ?>&session=<?= $session ?>
   class="btn btn-danger">
   📄 Generate PDF
</a>

<?php } ?>
</div>
                    <a href="attendance.php"
                       class="btn btn-primary btn-sm mx-4">
                        Go Back
                    </a>

                </div>

            </div>

            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item">
                        <a href="#">Accounts</a>
                    </li>

                    <li class="breadcrumb-item active">
                        Attendance
                    </li>

                </ol>

            </div>

        </div>

    </div>
</div>

<div class="card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-striped w-100">

                <thead class="bg-dark text-white">

                    <tr>

                        <th>S.No</th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Total Class</th>
                        <th>Present Class</th>
                        <th>Absent Class</th>
                        <th>Attendance %</th>

                    </tr>

                </thead>

                <tbody>

<?php

$i=1;

while($row=mysqli_fetch_assoc($query)){
$total = $total_classes;
$present_class = $row['present_class'] ?? 0;
$absent_class = $total - $present_class;

$percentage = 0;

if($total > 0){

    $percentage = ($present_class / $total) * 100;
}

?>

<tr>

    <td><?= $i++ ?></td>

    <td><?= $row['roll_no'] ?></td>

    <td><?= $row['Name'] ?></td>

    <td><?= $total ?></td>

    <td><?= $present_class ?></td>

    <td><?= $absent_class ?></td>

    <td>

<?php if($percentage < 75){ ?>

    <span class="badge badge-danger p-2">
        <?= round($percentage,2) ?>%
    </span>

<?php }else{ ?>

    <span class="badge badge-success p-2">
        <?= round($percentage,2) ?>%
    </span>

<?php } ?>

    </td>

</tr>

<?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include('footer.php'); ?>

<script>

$(document).ready(function(){

    $('.table').DataTable();

});

</script>