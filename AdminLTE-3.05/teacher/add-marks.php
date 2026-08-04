<?php include('includes/auth.php');
checkRole('teacher');?>

<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

// ================= COLLEGE =================

$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$semester = $_GET['semester'] ?? '';

// ================= COMMON =================

$session  = $_GET['session'] ?? '';
$exam     = $_GET['exam'] ?? '';
$subject  = $_GET['st_subject'] ?? '';

// ================= SCHOOL =================

$class_id = $_GET['class_id'] ?? '';
$section  = $_GET['section'] ?? '';

?>

<?php

// ================= SAVE =================

if(isset($_POST['save'])){

    // ================= CHECK RESULT EXISTS =================

    if($institute_type == 'college'){

        $check = $con->prepare("
        SELECT id FROM results
        WHERE
        course_id = ?
        AND branch_id = ?
        AND semester_id = ?
        AND session_id = ?
        AND exam_id = ?
        AND subject_id = ?
        AND institute_id = ?
        ");

        $check->bind_param(
        "iiisiii",
        $course,
        $branch,
        $semester,
        $session,
        $exam,
        $subject,
        $institute_id
        );

    } else {

        $check = $con->prepare("
        SELECT id FROM results
        WHERE
        class_id = ?
        AND section_id = ?
        AND session_id = ?
        AND exam_id = ?
        AND subject_id = ?
        AND institute_id = ?
        ");

        $check->bind_param(
        "iisiii",
        $class_id,
        $section,
        $session,
        $exam,
        $subject,
        $institute_id
        );

    }

    $check->execute();

    $res = $check->get_result();

    if($res->num_rows > 0){

        $row = $res->fetch_assoc();
        $result_id = $row['id'];

    } else {

        // ================= INSERT RESULT =================

        if($institute_type == 'college'){

            $insert_result = $con->prepare("
            INSERT INTO results(
                course_id,
                branch_id,
                semester_id,
                session_id,
                exam_id,
                subject_id,
                institute_id
            ) VALUES(?,?,?,?,?,?,?)
            ");

            $insert_result->bind_param(
            "iiisiii",
            $course,
            $branch,
            $semester,
            $session,
            $exam,
            $subject,
            $institute_id
            );

        } else {

            $insert_result = $con->prepare("
            INSERT INTO results(
                class_id,
                section_id,
                session_id,
                exam_id,
                subject_id,
                institute_id
            ) VALUES(?,?,?,?,?,?)
            ");

            $insert_result->bind_param(
            "iisiii",
            $class_id,
            $section,
            $session,
            $exam,
            $subject,
            $institute_id
            );

        }

        $insert_result->execute();

        $result_id = $con->insert_id;
    }

    // ================= INSERT / UPDATE MARKS =================

    foreach($_POST['marks'] as $student_id => $marks){

        $marks = trim($marks);

        if($marks === ''){
            continue;
        }
$select_total = mysqli_query($con,"
SELECT max_marks
FROM exam_type
WHERE institute_id='$institute_id'
AND id='$exam'
");

$row_exam = mysqli_fetch_assoc($select_total);

$max_marks = (float)$row_exam['max_marks'];
    // MAX MARKS VALIDATION
    if($marks > $max_marks){

        echo "<script>
        alert('Entered marks cannot be greater than Maximum Marks (".$max_marks.")');
        window.history.back();
        </script>";

        exit;
    }
        $check_marks = $con->prepare("
        SELECT id FROM result_marks
        WHERE result_id = ? AND student_id = ?
        ");

        $check_marks->bind_param("ii",$result_id,$student_id);

        $check_marks->execute();

        $marks_res = $check_marks->get_result();

        if($marks_res->num_rows > 0){

            $update = $con->prepare("
            UPDATE result_marks
            SET marks = ?
            WHERE result_id = ? AND student_id = ?
            ");

            $update->bind_param(
            "dii",
            $marks,
            $result_id,
            $student_id
            );

            $update->execute();

        } else {

            $insert = $con->prepare("
            INSERT INTO result_marks(
                result_id,
                student_id,
                marks
            ) VALUES(?,?,?)
            ");

            $insert->bind_param(
            "iid",
            $result_id,
            $student_id,
            $marks
            );

            $insert->execute();
        }
    }

    // ================= REDIRECT =================

    if($institute_type == 'college'){

        echo "<script>
        alert('College Marks Saved Successfully');

        window.location.href='result.php?course=$course&branch=$branch&session=$session&semester=$semester&exam=$exam&st_subject=$subject';

        </script>";

    } else {

        echo "<script>
        alert('School Marks Saved Successfully');

        window.location.href='result.php?class_id=$class_id&section=$section&session=$session&exam=$exam&st_subject=$subject';

        </script>";
    }

}

?>

<div class="card">

<div class="card-body">

<h4 class="mb-4">Upload Marks</h4>

<!-- ================= BACK ================= -->

<?php if($institute_type == 'college'){ ?>

<a href="result.php?course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>&exam=<?= $exam ?>&st_subject=<?= $subject ?>"
class="btn btn-secondary mb-3">

Back to Result

</a>

<?php } else { ?>

<a href="result.php?class_id=<?= $class_id ?>&section=<?= $section ?>&session=<?= $session ?>&exam=<?= $exam ?>&st_subject=<?= $subject ?>"
class="btn btn-secondary mb-3">

Back to Result

</a>

<?php } ?>


<form method="POST">

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead class="bg-dark text-white">

<tr>

<th>SNO</th>
<th>Enroll ID</th>
<th>Student Name</th>
<th>Marks <br> <?php 
$select_total=mysqli_query($con,"SELECT max_marks FROM `exam_type` WHERE institute_id='$institute_id' AND id='$exam'");
$row_exam=mysqli_fetch_assoc($select_total);
$max_marks=$row_exam['max_marks'];
echo "(  $max_marks  )";
?></th>

</tr>

</thead>

<tbody>

<?php

// ================= COLLEGE STUDENTS =================

if($institute_type == 'college'){

    $select_student = mysqli_query($con,"
    SELECT
        a.id,
        a.Name,
        a.roll_no

    FROM accounts a

    LEFT JOIN usermeta um_course
        ON um_course.user_id = a.id
        AND um_course.meta_key = 'course_name'

    LEFT JOIN usermeta um_sem
        ON um_sem.user_id = a.id
        AND um_sem.meta_key = 'semester'

    LEFT JOIN usermeta um_branch
        ON um_branch.user_id = a.id
        AND um_branch.meta_key = 'branch_name'

    LEFT JOIN usermeta um_session
        ON um_session.user_id = a.id
        AND um_session.meta_key = 'session'

    WHERE a.type='student'
    AND a.institute_id='$institute_id'

    AND um_course.meta_value='$course'
    AND um_branch.meta_value='$branch'
    AND um_sem.meta_value='$semester'
    AND um_session.meta_value='$session'
    ");

} else {

    // ================= SCHOOL STUDENTS =================

    $select_student = mysqli_query($con,"
    SELECT
        a.id,
        a.Name,
        a.roll_no

    FROM accounts a

    LEFT JOIN usermeta um_class
        ON um_class.user_id = a.id
        AND um_class.meta_key = 'st_class'

    LEFT JOIN usermeta um_section
        ON um_section.user_id = a.id
        AND um_section.meta_key = 'st_section'

    LEFT JOIN usermeta um_session
        ON um_session.user_id = a.id
        AND um_session.meta_key = 'session'

    WHERE a.type='student'
    AND a.institute_id='$institute_id'

    AND um_class.meta_value='$class_id'
    AND um_section.meta_value='$section'
    AND um_session.meta_value='$session'
    ");

}

$i = 1;

while($row = mysqli_fetch_assoc($select_student)){

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $row['roll_no'] ?></td>

<td><?= $row['Name'] ?></td>

<td>

<input type="number"
name="marks[<?= $row['id'] ?>]"
class="form-control"
placeholder="Enter Marks"
min="0"
max="<?= $max_marks ?>">

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<button type="submit"
name="save"
class="btn btn-success mt-3">

Save Marks

</button>

</form>

</div>

</div>

<?php include('footer.php');?> 