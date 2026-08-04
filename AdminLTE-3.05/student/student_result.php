<?php include('includes/auth.php');
checkRole('student');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>

<?php
$std_id=$_SESSION['user_id'];
// get roll_no
$get_roll=mysqli_query($con,"SELECT roll_no FROM `accounts` WHERE id='$std_id'");
$row=mysqli_fetch_assoc($get_roll);
$roll_no=$row['roll_no'];

$institute_id = $_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
// ================= STUDENT INFO =================
$student = mysqli_fetch_assoc(mysqli_query($con,"SELECT Name,id FROM accounts WHERE id='$std_id'"));
$std_name = $student['Name'] ?? '';

// usermeta
$class_id=get_usermeta($std_id,'st_class');
$class_post = get_post([
    'id'   => $class_id,
    'type' => 'class'
]);

$class_name = $class_post->title ?? '';
$section=get_usermeta($std_id,'st_section');
$section_post = get_post([
    'id'   => $section,
    'type' => 'section'
]);

$section_name = $section_post->title ?? '';
$academic_session=get_usermeta($std_id,'session');
// get the sesssion name from post
$get_session=mysqli_query($con,"SELECT title FROM `posts` WHERE id='$academic_session'");
$row_session=mysqli_fetch_assoc($get_session);
$session_name=$row_session['title'];


$course  = get_usermeta($std_id,'course_name');
$branch  = get_usermeta($std_id,'branch_name');
$session_id = get_usermeta($std_id,'session');
$semester=get_usermeta($std_id,'semester');

// get the principal id 
$selct_prin=mysqli_query($con,"SELECT id FROM `accounts` WHERE type='principal' AND institute_id='$institute_id'");
$row_prin=mysqli_fetch_assoc($selct_prin);
$principal_id=$row_prin['id'];
$principal_sign = get_usermeta($principal_id,'pricipal_sign');


// teacher fetching 
$year = date('Y');        // current year (e.g. 2026)
$next_year = $year + 1;   // next year (2027)

$academic_year = $year . "-" . $next_year;
// echo "<pre>";

// echo "class = ".$class_id."<br>";
// echo "section = ".$section."<br>";
// echo "session = ".$academic_session."<br>";
// echo "institute = ".$institute_id."<br>";

// echo "</pre>";

// ================= TEACHER FETCH =================

if($institute_type=='college'){

    $teacher_q = mysqli_query($con,"
    SELECT u1.user_id
    FROM usermeta u1
    JOIN usermeta u2 ON u1.user_id = u2.user_id
    JOIN usermeta u3 ON u1.user_id = u3.user_id

    WHERE u1.meta_key='course'
    AND u1.meta_value='$course'

    AND u2.meta_key='branch'
    AND u2.meta_value='$branch'

    AND u3.meta_key='session'
    AND u3.meta_value='$academic_session'

    LIMIT 1
    ");

}
else{

    $teacher_q = mysqli_query($con,"
    SELECT u1.user_id
    FROM usermeta u1
    JOIN usermeta u2 ON u1.user_id = u2.user_id
    JOIN usermeta u3 ON u1.user_id = u3.user_id

    WHERE u1.meta_key='class'
    AND u1.meta_value='$class_id'

    AND u2.meta_key='section'
    AND u2.meta_value='$section'

    AND u3.meta_key='session'
    AND u3.meta_value='$academic_session'

    LIMIT 1
    ");

}

$teacher = mysqli_fetch_assoc($teacher_q);

$teacher_id = $teacher['user_id'] ?? 0;

$teacher_sign = get_usermeta($teacher_id,'th_sign');
$teacher_img = "../admin/uploads/sign/".$teacher_sign;
$principal_img = "../admin/uploads/sign/".$principal_sign;
$student_id  = $std_id;
$course_id   = $course;
$branch_id   = $branch;
$semester_id = $semester;


$course_name = mysqli_fetch_assoc(mysqli_query($con,"SELECT title FROM posts WHERE id='$course'"))['title'] ?? '';
$branch_name = mysqli_fetch_assoc(mysqli_query($con,"SELECT title FROM posts WHERE id='$branch'"))['title'] ?? '';
$session = mysqli_fetch_assoc(mysqli_query($con,"SELECT title FROM posts WHERE id='$session_id'"))['title'] ?? '';



// class & section
// $class = get_usermeta($std_id,'st_class');
// $class_name = mysqli_fetch_assoc(mysqli_query($con,"SELECT title FROM posts WHERE id='$class'"))['title'] ?? '';

// $section = get_usermeta($std_id,'st_section');
// $section_name = mysqli_fetch_assoc(mysqli_query($con,"SELECT title FROM section WHERE id='$section'"))['title'] ?? '';

// ================= EXAMS =================
$exam_list = [];
$exams = mysqli_query($con,"
SELECT id, exam_type, max_marks 
FROM exam_type 
WHERE institute_id='$institute_id' AND status='active'
");

while($e = mysqli_fetch_assoc($exams)){
    $exam_list[] = $e;
}

// ================= MARKS =================

if($institute_type == 'college'){

$q = mysqli_query($con,"
SELECT 
    r.id as result_id,
    p.title AS subject,
    r.exam_id,
    m.marks
FROM results r
JOIN posts p ON p.id = r.subject_id
LEFT JOIN result_marks m 
    ON m.result_id = r.id 
    AND m.student_id='$student_id'

WHERE r.course_id='$course_id'
AND r.branch_id='$branch_id'
AND r.semester_id='$semester_id'
AND r.session_id='$session_id'
AND r.institute_id='$institute_id'
");

}
else{
$q = mysqli_query($con,"
SELECT 
    r.id as result_id,
    p.title AS subject,
    r.exam_id,
    m.marks

FROM results r

LEFT JOIN posts p 
ON p.id = r.subject_id

LEFT JOIN result_marks m 
ON m.result_id = r.id 
AND m.student_id='$student_id'

WHERE r.class_id='$class_id'
AND r.section_id='$section'
AND r.session_id='$academic_session'
AND r.institute_id='$institute_id'
");

}

$marks_data = [];

while($row = mysqli_fetch_assoc($q)){
    $subject = $row['subject'];
    $exam_id = $row['exam_id'];
    $marks   = $row['marks'] ?? 0;

    if(!isset($marks_data[$subject])){
        $marks_data[$subject] = [];
    }

    $marks_data[$subject][$exam_id] = $marks;
  
}
// ================= CALCULATION =================

$total_all = 0;
$total_max = 0;

foreach($marks_data as $subject => $exam_marks){

    foreach($exam_list as $ex){

        $exam_id = $ex['id'];

        // student marks
        $marks = $exam_marks[$exam_id] ?? null;

        // only count if marks exist
        if($marks !== null && $marks !== ''){

            $total_all += floatval($marks);

            $total_max += floatval($ex['max_marks']);
        }
    }
}

$percentage = ($total_max > 0)
    ? round(($total_all / $total_max) * 100,2)
    : 0;

// ================= GRADE =================
if($percentage >= 90) $grade="A+";
elseif($percentage >= 80) $grade="A";
elseif($percentage >= 70) $grade="B";
elseif($percentage >= 60) $grade="C";
else $grade="D";

if($percentage >= 90) $remark = "Very Good";
elseif($percentage >= 80) $remark = "Good";
elseif($percentage >= 70) $remark = "Satisfactory";
elseif($percentage >= 60) $remark = "Average";
else $remark = "Needs Improvement";
?>

<style>
.card { border-radius: 12px; }
.table th, .table td { vertical-align: middle; }
.badge { font-size: 14px; padding:6px 10px; }

.sign-img{
    width: 90px;
    height: 40px;
}

.student-img{
    width:70px;        /* size chhota */
    height:90px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #ccc;
}
.sign-img{
    max-width:100px;
    width:100%;
    height:auto;
}
/* Mobile Responsive */

@media (max-width:768px){

.container{
    padding-left:10px;
    padding-right:10px;
}
.sign-img{
    max-width:70px;
    width:100%;
    height:auto;
}

/* Student Card */

.card-body.d-flex{
    flex-direction:column !important;
    text-align:center;
}

.student-img{
    width:90px;
    height:110px;
    margin-bottom:15px;
}

/* Student Details */

.card-body h4{
    font-size:20px;
}

.card-body p{
    font-size:14px;
    margin-bottom:6px;
    word-break:break-word;
}

/* Percentage */

.text-right{
    text-align:center !important;
    margin-top:15px;
}

.text-right h3{
    font-size:24px;
}

/* Table */

.table{
    min-width:700px;
}

.table th,
.table td{
    font-size:13px;
    white-space:nowrap;
}

/* Chart */

canvas{
    width:100% !important;
    height:250px !important;
}

/* Signatures */

.sign-img{
    width:80px;
    height:auto;
}

.row.mt-5 .col-md-6{
    margin-bottom:20px;
}

}
</style>


<div class="container">

<!-- 🎓 STUDENT CARD -->
<div class="card shadow-lg mb-3">

    <?php
$photo = get_usermeta($student_id,'student_photo');
$st_photo = "../admin/uploads/student_photo/".$photo;
?>
<div class="card-body">
<div class="row align-items-center">

  <!-- 🖼 Photo -->
        <div class="col-md-2 text-center">
            <img src="<?= $st_photo ?? 'uploads/default.png' ?>" 
                 class="student-img">
        </div>
<div>
<h4><b><?= $std_name ?></b></h4>
<?php if($institute_type=='college'){?>
<p>Roll No: <?= $roll_no ?></p>
<?php } else{?>
<p>Student ID: <?= $roll_no ?></p>
<?php } ?>

<?php 
if($institute_type=='college'){?>
<p>Course: <?= $course_name ?> | Branch: <?= $branch_name ?> | session: <?= $session ?> | Semester: <?= $semester ?></p>
<?php } else{?>
 <p>Class: <?= $class_name ?> | Section: <?= $section_name ?> | session: <?= $session_name ?></p>   
<?php } ?>
</div>

<div class="text-right">
<h3 class="text-success"><?= $percentage ?>%</h3>
<span class="badge badge-primary"><?= $grade ?></span>
</div>

</div>
</div>

</div>
<!-- 📊 RESULT TABLE -->
<div class="card shadow">
<div class="card-header bg-dark text-white">
<h5 class="mb-0">Student Result</h5>
</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover text-center">

<thead class="bg-primary text-white">
<tr>
<th>Subject</th>

<?php foreach($exam_list as $ex){ ?>
<th><?= $ex['exam_type'] ?><br>(<?= $ex['max_marks'] ?>)</th>
<?php } ?>

<th>Total</th>
</tr>
</thead>

<tbody>

<?php foreach($marks_data as $subject => $exam_marks){ 
    $row_total = 0;
?>
<tr>

<td><b><?= $subject ?></b></td>

<?php foreach($exam_list as $ex){ 
$m = $exam_marks[$ex['id']] ?? '-';
$row_total += is_numeric($m) ? $m : 0;
?>
<td><span class="badge badge-info"><?= $m ?></span></td>
<?php } ?>

<td><span class="badge badge-success"><?= $row_total ?></span></td>

</tr>

<?php } ?>

<tr class="bg-light font-weight-bold">
<td>Total</td>

<?php foreach($exam_list as $ex){ ?>
<td>--</td>
<?php } ?>

<td><?= $total_all ?></td>
</tr>

</tbody>
</table>

</div>
</div>

<!-- 📈 CHART -->
<div class="card mt-3">
<div class="card-body">
    <p><b>Remark:</b> <?= $remark ?></p>

      <p><b>Percentage Chart:</p>
<div style="height:300px;">
    <canvas id="resultChart"></canvas>
</div>
<div class="row mt-5 text-center">

    <div class="col-6">
        <img src="<?= $teacher_img ?>" class="sign-img img-fluid"><br>
        <b>Class Teacher</b>
    </div>

    <div class="col-6">
        <img src="<?= $principal_img ?>" class="sign-img img-fluid"><br>
        <b>Principal</b>
    </div>

</div>
</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var subjects = <?= json_encode(array_keys($marks_data)); ?>;
var totals = <?= json_encode(array_map(function($s){ return array_sum($s); }, $marks_data)); ?>;

new Chart(document.getElementById("resultChart"),{
    type:'bar',
    data:{
        labels:subjects,
        datasets:[{
            label:'Total Marks',
            data:totals
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        scales:{
            y:{
                beginAtZero:true
            }
        }
    }
});
</script>

<?php include('footer.php');?>