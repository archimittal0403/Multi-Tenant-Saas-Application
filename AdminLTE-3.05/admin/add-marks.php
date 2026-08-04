<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>

<?php
$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$session  = $_GET['session'] ?? '';
$semester = $_GET['semester'] ?? '';
$exam     = $_GET['exam'] ?? '';
$subject=$_GET['st_subject'] ?? '';
?>
<?php
if(isset($_POST['save'])){

    // 🔍 CHECK duplicate result
    $check = $con->prepare("
    SELECT id FROM results 
    WHERE course_id=? AND branch_id=? AND semester_id=? AND session_id=? AND exam_id=? AND subject_id=?
    ");
    $check->bind_param("iiiiii", $course, $branch, $semester, $session, $exam, $subject);
    $check->execute();
    $res = $check->get_result();

    if($res->num_rows > 0){
        // ✅ Already exists → use same result_id
        $row = $res->fetch_assoc();
        $result_id = $row['id'];

    } else {
        // ✅ Insert new result
        $stmt = $con->prepare("
        INSERT INTO results (course_id, branch_id, semester_id, session_id, exam_id, subject_id, institute_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiiiiii", $course, $branch, $semester, $session, $exam, $subject, $institute_id);
        $stmt->execute();

        $result_id = $con->insert_id;
    }

    // 🔥 Insert / Update Marks
    foreach($_POST['marks'] as $student_id => $marks){

        // check if mark exists
        $check2 = $con->prepare("
        SELECT id FROM result_marks 
        WHERE result_id=? AND student_id=?
        ");
        $check2->bind_param("ii",$result_id,$student_id);
        $check2->execute();
        $res2 = $check2->get_result();

        if($res2->num_rows > 0){
            // UPDATE
            $update = $con->prepare("
            UPDATE result_marks SET marks=? 
            WHERE result_id=? AND student_id=?
            ");
            $update->bind_param("dii",$marks,$result_id,$student_id);
            $update->execute();

        } else {
            // INSERT
            $insert = $con->prepare("
            INSERT INTO result_marks(result_id, student_id, marks)
            VALUES(?,?,?)
            ");
            $insert->bind_param("iid",$result_id,$student_id,$marks);
            $insert->execute();
        }
    }

    echo "<script>
    alert('Marks Saved Successfully');
    window.location.href='result.php?course=$course&branch=$branch&session=$session&semester=$semester&exam=$exam';
    </script>";
}
?>
<div class="card">
<div class="card-body">

<h4 class="mb-4">📊 Upload Marks</h4>

<!-- 🔙 BACK -->
<a href="result.php?course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>&exam=<?= $exam ?>" 
class="btn btn-secondary mb-3">⬅ Back to Result</a>

<!-- ✅ FORM START -->
<form method="post">

<div class="table-responsive">
<table class="table table-bordered table-striped">

<thead class="bg-dark text-white">
<tr>
<th>SNO</th>
<th>Enroll ID</th>
<th>Student Name</th>
<th>Marks</th>
</tr>
</thead>

<tbody>

<?php
$select_student = mysqli_query($con,"
SELECT 
    a.id,
    a.Name,
    a.roll_no,

    um_course.meta_value AS course_id,
    um_sem.meta_value AS semester,
    um_branch.meta_value AS branch_id,
    um_session.meta_value AS session_id

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

WHERE a.type = 'student' 
AND a.institute_id='$institute_id'

AND um_course.meta_value = '$course'
AND um_branch.meta_value = '$branch'
AND um_sem.meta_value = '$semester'
AND um_session.meta_value = '$session'
");
 $i=1;
while($row=mysqli_fetch_assoc($select_student)){
   
?>

<tr>
<td><?= $i++ ?></td>
<td><?= $row['roll_no'] ?></td>
<td><?= $row['Name'] ?></td>
<td>
<input type="number" 
       name="marks[<?= $row['id'] ?>]" 
      value="<?= isset($row['marks']) ? $row['marks'] : '' ?>"
       class="form-control"
       placeholder="Enter Marks">
</td>
</tr>

<?php }  ?>

</tbody>
</table>
</div>

<!-- ✅ SAVE BUTTON -->
<button type="submit" name="save" class="btn btn-success mt-3">
💾 Save Marks
</button>

</form>

</div>
</div>

<?php include('footer.php');?>

<?php
// ================= SAVE LOGIC =================
if(isset($_POST['save'])){

    foreach($_POST['marks'] as $student_id => $marks){

        // check already exists
        $check = $con->prepare("
        SELECT id FROM results 
        WHERE student_id=? AND exam_id=?
        ");
        $check->bind_param("ii",$student_id,$exam);
        $check->execute();
        $res = $check->get_result();

        if($res->num_rows > 0){
            // UPDATE
            $update = $con->prepare("
            UPDATE results SET marks=? 
            WHERE student_id=? AND exam_id=?
            ");
            $update->bind_param("dii",$marks,$student_id,$exam);
            $update->execute();

        } else {
            // INSERT
            $insert = $con->prepare("
            INSERT INTO results(student_id, exam_id, marks)
            VALUES(?,?,?)
            ");
            $insert->bind_param("iid",$student_id,$exam,$marks);
            $insert->execute();
        }
    }

    echo "<script>alert('Marks Saved Successfully'); window.location.href='result.php?course=$course&branch=$branch&session=$session&semester=$semester&exam=$exam';</script>";
}
?>