<?php
include('includes/auth.php');
checkRole('admin');

include('includes/config.php');
include('includes/functions.php');
include('header.php');
include('sidebar.php');

$institute_id = $_SESSION['institute_id'];

/* =========================
   GET DATA (IMPORTANT FIX)
========================= */
$exam_id     = (int)($_GET['exam_id'] ?? 0);
$course_id   = $_GET['course_id'] ?? '';
$branch_id   = $_GET['branch_id'] ?? '';
$semester_id = $_GET['semester_id'] ?? '';
$session_id  = $_GET['session_id'] ?? '';
$class_id    = $_GET['class_id'] ?? '';
$section_id  = $_GET['section_id'] ?? '';

if($exam_id == 0){
    echo "<div class='container mt-5'>
            <h4 class='text-center text-danger'>Invalid Exam ID</h4>
          </div>";
    include('footer.php');
    exit;
}

/* =========================
   EXAM INFO
========================= */
$exam_info = mysqli_fetch_assoc(mysqli_query($con,"
    SELECT * FROM create_exam 
    WHERE id='$exam_id' 
    AND institute_id='$institute_id'
"));
?>

<div class="content-header">
<div class="container-fluid">

    <h2 class="text-center mb-4">📅 Exam Datesheet</h2>

    <?php if($exam_info){ ?>
        <div class="alert alert-info text-center">
            <b>Exam ID:</b> <?= $exam_id ?> |
            <b>Start:</b> <?= $exam_info['start_date'] ?? 'N/A' ?> |
            <b>End:</b> <?= $exam_info['end_date'] ?? 'N/A' ?>
        </div>
    <?php } ?>

    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Subject</th>
                        <th>Exam Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $count = 1;

                $query = mysqli_query($con,"
                    SELECT ed.*, p.title AS subject_name
                    FROM exam_datesheet ed
                    LEFT JOIN posts p ON ed.subject_id = p.id
                    WHERE ed.exam_id='$exam_id'
                    AND ed.institute_id='$institute_id'
                    ORDER BY ed.exam_date ASC
                ");

                if(mysqli_num_rows($query) > 0){

                    while($row = mysqli_fetch_assoc($query)){
                ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $row['subject_name'] ?></td>
                        <td><?= $row['exam_date'] ?></td>
                        <td><?= $row['start_time'] ?></td>
                        <td><?= $row['end_time'] ?></td>
                        <td><?= $row['duration'] ?></td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr>
                            <td colspan='6' class='text-center text-danger'>
                                No Datesheet Found
                            </td>
                          </tr>";
                }
                ?>
                </tbody>

            </table>

            <div class="text-right mt-3">
                <a href="exam_datesheet.php?exam_id=<?= $exam_id ?>" 
                   class="btn btn-primary">
                    ← Back
                </a>
            </div>

        </div>
    </div>

</div>
</div>

<?php include('footer.php'); ?>