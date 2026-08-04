<?php
include('includes/auth.php');
checkRole('teacher');

include('includes/config.php');
include('includes/functions.php');
include('header.php');
include('sidebar.php');

$institute_id = $_SESSION['institute_id'];

$course   = $_GET['st_course'] ?? '';
$branch   = $_GET['st_branch'] ?? '';
$semester = $_GET['st_semester'] ?? '';
$session  = $_GET['st_session'] ?? '';
$exam_id  = $_GET['exam_id'] ?? '';
?>

<div class="container-fluid mt-3">

<div class="card">
    <div class="card-header bg-danger">
        <h3 class="card-title text-white">
            Pending Admit Cards
        </h3>
        <a href="pdf.php?type=pendingadmit_report&st_course=<?= $course ?>&st_branch=<?= $branch ?>&st_session=<?= $session ?>&st_semester=<?= $semester ?>&exam_id=<?= $exam_id?>"
class="btn btn-warning ml-3 mb-2">
📄 Generate PDF
</a>
    </div>

    <div class="card-body">

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

<?php

$i = 1;

$q = mysqli_query($con,"
SELECT
    a.id,
    a.name,
    a.roll_no

FROM accounts a

JOIN usermeta uc
    ON a.id=uc.user_id
    AND uc.meta_key='course_name'
    AND uc.meta_value='$course'

JOIN usermeta ub
    ON a.id=ub.user_id
    AND ub.meta_key='branch_name'
    AND ub.meta_value='$branch'

JOIN usermeta us
    ON a.id=us.user_id
    AND us.meta_key='session'
    AND us.meta_value='$session'

JOIN usermeta um
    ON a.id=um.user_id
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

if(mysqli_num_rows($q) > 0){

    while($row = mysqli_fetch_assoc($q)){
?>

<tr>
    <td><?= $i++; ?></td>
    <td><?= $row['roll_no']; ?></td>
    <td><?= $row['name']; ?></td>
    <td>
        <span class="badge badge-danger">
            Pending
        </span>
    </td>
</tr>

<?php
    }

}else{
?>

<tr>
    <td colspan="4" class="text-center text-success">
        No Pending Students Found
    </td>
</tr>

<?php } ?>

            </tbody>
        </table>

    </div>
</div>

</div>

<?php include('footer.php'); ?>