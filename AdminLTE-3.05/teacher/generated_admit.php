<?php include('includes/auth.php');
checkRole('teacher'); ?>
<?php include('includes/config.php') ?>
<?php include('header.php') ?>
<?php include('sidebar.php') ?>
<?php include('includes/functions.php') ?>

<?php
$institute_id = $_SESSION['institute_id'];

$course   = $_GET['st_course'] ?? '';
$branch   = $_GET['st_branch'] ?? '';
$semester = $_GET['st_semester'] ?? '';
$session  = $_GET['academic_session'] ?? '';
$exam_id  = $_GET['exam_id'] ?? '';
?>

<div class="container mt-3">

<h2 class="text-center mb-4">Generated Admit Cards</h2>
<a href="pdf.php?type=admit_report&st_course=<?= $course ?>&st_branch=<?= $branch ?>&academic_session=<?= $session ?>&st_semester=<?= $semester ?>&exam_id=<?= $exam_id?>"
class="btn btn-danger">
📄 Generate PDF
</a>
<table class="table table-bordered">
<thead class="bg-dark text-white">
<tr>
    <th>S.No</th>
    <th>Roll No</th>
    <th>Name</th>
    <th>Admit Card</th>
</tr>
</thead>

<tbody>

<?php
$i = 1;

$q = mysqli_query($con, "
SELECT 
    a.id,
    a.name,
    a.roll_no,
    ac.pdf_path
FROM admit_cards ac
JOIN accounts a ON a.id = ac.student_id

WHERE ac.exam_id = '$exam_id'
AND ac.course_id = '$course'
AND ac.branch_id = '$branch'
AND ac.semester = '$semester'
AND ac.academic_session = '$session'
AND ac.institute_id = '$institute_id'
");

if(mysqli_num_rows($q) > 0){

    while($row = mysqli_fetch_assoc($q)){
?>

<tr>
    <td><?= $i++ ?></td>
    <td><?= $row['roll_no'] ?></td>
    <td><?= $row['name'] ?></td>
    <td>
        <a href="<?= $row['pdf_path'] ?>" target="_blank" class="btn btn-success btn-sm">
            View Admit Card
        </a>
    </td>
</tr>

<?php
    }

}else{
    echo "
    <tr>
        <td colspan='4' class='text-center text-danger'>
            No Generated Admit Cards Found
        </td>
    </tr>";
}
?>

</tbody>
</table>

</div>

<?php include('footer.php'); ?>