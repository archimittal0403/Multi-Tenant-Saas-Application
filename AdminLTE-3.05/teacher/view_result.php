<?php include('includes/auth.php');
checkRole('teacher'); ?>
<?php include('includes/config.php') ?>
<?php include('header.php') ?>
<?php include('sidebar.php') ?>
<?php include('includes/functions.php') ?>

<?php

$institute_id = $_SESSION['institute_id'];
$system_type=$_SESSION['system_type'];

$class   = $_GET['class_id'] ?? '';
$section = $_GET['section'] ?? '';
$session = $_GET['session'] ?? '';

$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$session  = $_GET['session'] ?? '';
$semester = $_GET['semester'] ?? '';

// ================= STUDENTS =================
if($system_type == 'college') {

$students = mysqli_query($con,"
SELECT a.id, a.Name, a.roll_no
FROM accounts a
JOIN usermeta u1 ON u1.user_id=a.id AND u1.meta_key='course_name' AND u1.meta_value='$course'
JOIN usermeta u2 ON u2.user_id=a.id AND u2.meta_key='branch_name' AND u2.meta_value='$branch'
JOIN usermeta u3 ON u3.user_id=a.id AND u3.meta_key='semester' AND u3.meta_value='$semester'
JOIN usermeta u4 ON u4.user_id=a.id AND u4.meta_key='session' AND u4.meta_value='$session'
WHERE a.type='student'
AND a.institute_id='$institute_id'
");

} else {

$students = mysqli_query($con,"
SELECT a.id, a.Name, a.roll_no
FROM accounts a
JOIN usermeta u1 ON u1.user_id=a.id AND u1.meta_key='st_class' AND u1.meta_value='$class'
JOIN usermeta u2 ON u2.user_id=a.id AND u2.meta_key='st_section' AND u2.meta_value='$section'
JOIN usermeta u3 ON u3.user_id=a.id AND u3.meta_key='session' AND u3.meta_value='$session'
WHERE a.type='student'
AND a.institute_id='$institute_id'
");
}
// ================= SUBJECTS =================
$subjects = mysqli_query($con,"
SELECT id, title 
FROM posts
WHERE type='subject'
AND parent='$branch'
AND institute_id='$institute_id'
");
if($system_type == 'school'){

$subjects = mysqli_query($con,"
SELECT id, title 
FROM posts
WHERE type='subject'
AND parent='$class'
AND institute_id='$institute_id'
");

}
// ================= EXAMS =================
$exams = mysqli_query($con,"
SELECT id, exam_type, max_marks
FROM exam_type
WHERE institute_id='$institute_id'
AND status='active'
");

// ================= MARKS =================
if($system_type == 'college'){

$marks_q = mysqli_query($con,"
SELECT rm.student_id, r.subject_id, r.exam_id, rm.marks
FROM result_marks rm
JOIN results r ON r.id = rm.result_id
WHERE r.course_id='$course'
AND r.branch_id='$branch'
AND r.semester_id='$semester'
AND r.session_id='$session'
AND r.institute_id='$institute_id'
");

} else {

$marks_q = mysqli_query($con,"
SELECT rm.student_id, r.subject_id, r.exam_id, rm.marks
FROM result_marks rm
JOIN results r ON r.id = rm.result_id
WHERE r.class_id='$class'
AND r.section_id='$section'
AND r.session_id='$session'
AND r.institute_id='$institute_id'
");

}

$marks_data = [];

while($m = mysqli_fetch_assoc($marks_q)){

    $marks_data[$m['student_id']][$m['subject_id']][$m['exam_id']] = $m['marks'];
}

// ================= SUBJECT ARRAY =================
$sub_list = [];

while($s = mysqli_fetch_assoc($subjects)){
    $sub_list[] = $s;
}

// ================= EXAM ARRAY =================
$exam_list = [];

while($e = mysqli_fetch_assoc($exams)){
    $exam_list[] = $e;
}

// ================= STUDENT ARRAY =================
$student_list = [];

while($stu = mysqli_fetch_assoc($students)){
    $student_list[] = $stu;
}

// ================= VALIDATION =================

$all_marks_exist = true;

foreach($student_list as $stu){

    foreach($sub_list as $sub){

        foreach($exam_list as $ex){

            if(
                !isset(
                    $marks_data[$stu['id']][$sub['id']][$ex['id']]
                )
            ){

                $all_marks_exist = false;

                break 3;
            }

        }

    }

}

// ================= TOPPER =================

$total_max_per_subject = 0;

foreach($exam_list as $ex){
    $total_max_per_subject += $ex['max_marks'];
}

$grand_total_max = $total_max_per_subject * count($sub_list);

$topper_name = '-';
$topper_marks = 0;

foreach($student_list as $stu){

    $total = 0;

    foreach($sub_list as $sub){

        foreach($exam_list as $ex){

            $m = $marks_data[$stu['id']][$sub['id']][$ex['id']] ?? 0;

            $total += $m;
        }
    }

    if($total > $topper_marks){

        $topper_marks = $total;
        $topper_name = $stu['Name'];
    }
}

?>

<style>
.card{
    border-radius:12px;
}

.table th{
    font-size:14px;
}

.table td{
    font-size:13px;
}

.badge{
    padding:6px 10px;
    font-size:13px;
}

.filter-box{
    background:#fff;
    border-radius:12px;
    padding:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

.filter-box select{
    height:40px;
    border-radius:8px;
    font-size:14px;
}

.filter-box label{
    font-size:12px;
    font-weight:600;
    margin-bottom:3px;
}
</style>

<div class="card shadow-lg">
<div class="card-body">

<h4 class="mb-3">📊 Result Dashboard</h4>

<!-- ACTION BUTTONS -->

<div class="d-flex gap-2 mb-3">

<?php if(!empty($course) && !empty($branch) && !empty($session) && !empty($semester)){ ?>

<a href="pdf.php?type=result_report&course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>"
class="btn btn-danger">
📄 Generate PDF
</a>

<?php } ?>

<?php if(!empty($class) && !empty($section) && !empty($session)){ ?>

<a href="pdf.php?type=result_report&class=<?= $class ?>&section=<?= $section ?>&session=<?= $session ?>"
class="btn btn-danger">
📄 Generate PDF
</a>

<?php } ?>
<button onclick="window.print()" class="btn btn-primary">
🖨 Print
</button>

<?php if($all_marks_exist){ ?>

<button class="btn btn-success" id="sendOtpBtn">
📲 Send OTP
</button>

<?php } else { ?>

<button class="btn btn-secondary" disabled>
❌ Result Incomplete
</button>

<?php } ?>

</div>


<div class="filter-box">

<form method="GET">

<div class="row">

<?php if($institute_type == 'college'){ ?>

<!-- ================= COLLEGE ================= -->

<div class="col-lg-3">

<select name="course" id="st_course" class="form-control">

<option value="">Select Course</option>

<?php

$courses = get_posts(['type'=>'course']);

foreach($courses as $c){

$sel = ($course == $c->id) ? 'selected' : '';

echo "<option value='$c->id' $sel>$c->title</option>";

}

?>

</select>

</div>

<div class="col-lg-3">

<select name="branch" id="st_branch" class="form-control">

<option value="">Select Branch</option>

</select>

</div>

<div class="col-lg-3">

<select name="session" id="st_session" class="form-control">

<option value="">Select Session</option>

<?php

$sessions = get_posts([
'type'=>'session',
'institute_id'=>$institute_id
]);

foreach($sessions as $s){

$sel = ($session == $s->id) ? 'selected' : '';

echo "<option value='$s->id' $sel>$s->title</option>";

}

?>

</select>

</div>

<div class="col-lg-3">

          <select name="semester" id="st_semester" class="form-control">
<option value="">Select Semester</option>
</select>


</div>

<?php } else { ?>

<!-- ================= SCHOOL ================= -->

<div class="col-lg-3">

<select name="class_id" id="class_id" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts([
'type'=>'class',
'institute_id'=>$institute_id
]);

foreach($classes as $c){

$sel = ($class_id == $c->id) ? 'selected' : '';

echo "<option value='$c->id' $sel>$c->title</option>";

}

?>

</select>

</div>

<div class="col-lg-3">

<select name="section" id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>

<div class="col-lg-3">

<select name="session" id="st_session" class="form-control">

<option value="">Select Session</option>

<?php

$sessions = get_posts([
'type'=>'session',
'institute_id'=>$institute_id
]);

foreach($sessions as $s){

$sel = ($session == $s->id) ? 'selected' : '';

echo "<option value='$s->id' $sel>$s->title</option>";

}

?>

</select>

</div>

<div class="col-lg-3">

<select id="st_subject" name="st_subject" class="form-control">

<option value="">Select Subject</option>

</select>

</div>

<?php } ?>

</div>

<div class="row mt-3">

<div class="col-lg-4">

<select name="exam" id="st_exam" class="form-control">

<option value="">Select Exam</option>

<?php
$q = mysqli_query($con,"SELECT * FROM exam_type WHERE institute_id='$institute_id'");

while($r=mysqli_fetch_assoc($q)){

$sel = ($exam == $r['id']) ? 'selected' : '';

echo "<option value='{$r['id']}' $sel>{$r['exam_type']}</option>";
}
?>

</select>

</div>

<?php if($institute_type == 'college'){ ?>

<div class="col-lg-4">

<select id="st_subject" name="st_subject" class="form-control">

<option value="">Select Subject</option>

</select>

</div>

<?php } ?>


<div class="col-md-4 d-flex align-items-end gap-2">

<button class="btn btn-primary w-50">
Apply
</button>

<a href="?" class="btn btn-outline-secondary w-50">
Reset
</a>

</div>

</div>

</form>

</div>

<!-- SUMMARY -->

<div class="row mb-3">

<div class="col-md-4">

<div class="card bg-success text-white text-center shadow">

<div class="card-body">

<h6>Total Students</h6>

<h3><?= count($student_list) ?></h3>

</div>
</div>
</div>

<div class="col-md-4">

<div class="card bg-info text-white text-center shadow">

<div class="card-body">

<h6>Subjects</h6>

<h3><?= count($sub_list) ?></h3>

</div>
</div>
</div>

<div class="col-md-4">

<div class="card bg-warning text-dark text-center shadow">

<div class="card-body">

<h6>🏆 Topper</h6>

<h5><?= $topper_name ?></h5>

<small><?= $topper_marks ?> Marks</small>

</div>
</div>
</div>

</div>

<!-- TABLE -->

<div class="table-responsive">

<table class="table table-bordered table-hover text-center">

<thead class="bg-dark text-white">

<tr>

<th>#</th>
<th>Roll</th>
<th>Name</th>

<?php

foreach($sub_list as $sub){

    foreach($exam_list as $ex){

?>

<th>

<?= $sub['title'] ?>

<br>

<small class="text-warning">
<?= $ex['exam_type'] ?> (<?= $ex['max_marks'] ?>)
</small>

</th>

<?php }} ?>

<th class="bg-success">Total</th>

<th class="bg-info">%</th>

</tr>

</thead>

<tbody>

<?php

$i = 1;

foreach($student_list as $stu){

$total = 0;

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $stu['roll_no'] ?></td>

<td>
<b><?= $stu['Name'] ?></b>
</td>

<?php

foreach($sub_list as $sub){

    foreach($exam_list as $ex){

        $m = $marks_data[$stu['id']][$sub['id']][$ex['id']] ?? 0;

        $total += $m;

?>

<td>

<span class="badge bg-secondary">
<?= $m ?>
</span>

</td>

<?php }} ?>

<?php

$percentage = ($grand_total_max > 0)
? round(($total / $grand_total_max) * 100)
: 0;

?>

<td>

<span class="badge bg-success">
<?= $total ?>
</span>

</td>

<td>

<span class="badge bg-info">
<?= $percentage ?>%
</span>

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
$(document).ready(function () {

let isCollege = <?= json_encode($institute_type == 'college') ?>;

/* ================= COLLEGE FLOW ================= */
function loadCollege() {

    let course_id = $('#st_course').val();

    if (!course_id) return;

    // BRANCH
    $.post('ajax.php', {
        action: 'get_branch',
        course_id: course_id
    }, function (res) {
        $('#st_branch').html(res.options);
    }, 'json');

    // SEMESTER
    $.post('ajax.php', {
        action: 'get_semester',
        course_id: course_id
    }, function (res) {
        $('#st_semester').html(res.options);
    }, 'json');

    // SUBJECT + EXAM refresh
    loadCollegeSubjectExam();
}

/* ================= COLLEGE SUBJECT + EXAM ================= */
function loadCollegeSubjectExam() {

    $.post('ajax.php', {
        action: 'get_subject',
        course_id: $('#st_course').val(),
        branch_id: $('#st_branch').val(),
        semester_id: $('#st_semester').val(),
        session: $('#st_session').val()
    }, function (res) {
        $('#st_subject').html(res.options);
    }, 'json');

}

/* ================= SCHOOL FLOW ================= */
function loadSections() {

    let class_id = $('#class_id').val();

    if (!class_id) return;

    $.post('ajax.php', {
        action: 'get_sections',
        class_id: class_id
    }, function (res) {

        $('#st_section').html(res.options);

        loadSchoolSubject();

    }, 'json');
}

/* ================= SCHOOL SUBJECT ================= */
function loadSchoolSubject() {

    $.post('ajax.php', {
        action: 'get_subject',
        class_id: $('#class_id').val(),
        section_id: $('#st_section').val()
    }, function (res) {
        $('#st_subject').html(res.options);
    }, 'json');

}

/* ================= EVENTS ================= */

if (isCollege) {

    $('#st_course').on('change', function () {
        loadCollege();
    });

    $('#st_branch, #st_semester, #st_session').on('change', function () {
        loadCollegeSubjectExam();
    });

    // AUTO LOAD
    if ($('#st_course').val()) {
        loadCollege();
    }

} else {

    $('#class_id').on('change', function () {
        loadSections();
    });

    $('#st_section').on('change', function () {
        loadSchoolSubject();
    });

    // AUTO LOAD
    if ($('#class_id').val()) {
        loadSections();
    }

}

});
</script>