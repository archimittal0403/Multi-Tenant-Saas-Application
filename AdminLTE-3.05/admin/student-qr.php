<?php include('includes/auth.php'); ?>
<?php checkRole('admin'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

// ================= FILTER VALUES =================

// COLLEGE
$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$session  = $_GET['session'] ?? '';
$semester = $_GET['semester'] ?? '';

// SCHOOL
$class_id          = $_GET['class_id'] ?? '';
$section_id        = $_GET['section_id'] ?? '';
$academic_session  = $_GET['academic_session'] ?? '';

?>

<style>
.qr-card{
    border-radius:15px;
    border:none;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.qr-title{
    font-weight:700;
    color:#333;
}

.qr-btn{
    border-radius:8px;
    padding:8px 18px;
    font-weight:600;
}

.table th{
    background:#f8f9fc;
}
</style>

<div class="main-content">
<div class="container-fluid mt-4">

<div class="card qr-card">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="qr-title">Generate Student QR Code</h3>
        <p class="text-muted mb-0">
            Filter students and generate QR codes
        </p>
    </div>
</div>

<!-- FILTER FORM -->
<form method="get">

<div class="row">

<?php if($institute_type=='college'){ ?>

<!-- COURSE -->
<div class="col-lg-3 mb-3">
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

<!-- BRANCH -->
<div class="col-lg-3 mb-3">
<select name="branch" id="st_branch" class="form-control">
<option value="">Select Branch</option>
</select>
</div>

<!-- SESSION -->
<div class="col-lg-3 mb-3">
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

<!-- SEMESTER -->
<div class="col-lg-3 mb-3">
<select name="semester" id="st_semester" class="form-control">
<option value="">Select Semester</option>
</select>
</div>

<?php } else { ?>

<!-- CLASS -->
<div class="col-lg-4 mb-3">

<select name="class_id" id="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){

$sel = ($class_id == $c->id) ? 'selected' : '';

?>

<option value="<?= $c->id ?>" <?= $sel ?>>
<?= $c->title ?>
</option>

<?php } ?>

</select>

</div>

<!-- SECTION -->
<div class="col-lg-4 mb-3">

<select name="section_id" id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>

<!-- SESSION -->
<div class="col-lg-4 mb-3">

<input type="text"
       name="academic_session"
       id="academic_session"
       value="<?= $academic_session ?>"
       class="form-control"
       placeholder="Enter Academic Session">

</div>

<?php } ?>

<!-- BUTTON -->
<div class="col-lg-12">
<button type="submit" class="btn btn-primary qr-btn">
    Search Students
</button>
<a href="student-qr.php"
   class="btn btn-secondary">

    Reset

</a>
</div>

</div>

</form>

<hr>

<!-- STUDENT TABLE -->
<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead>
<tr>
<th width="80">SNO</th>
<th>Roll No</th>
<th>Student Name</th>
<th width="200">Generate QR Code</th>
</tr>
</thead>

<tbody>

<?php

// ================= COLLEGE =================

if(
$institute_type=='college'
&& $course
&& $branch
&& $session
&& $semester
){

$query = mysqli_query($con,"
SELECT 
    a.id,
    a.Name,
    a.roll_no

FROM accounts a

JOIN usermeta um_course 
ON um_course.user_id = a.id 
AND um_course.meta_key = 'course_name'
AND um_course.meta_value = '$course'

JOIN usermeta um_sem 
ON um_sem.user_id = a.id 
AND um_sem.meta_key = 'semester'
AND um_sem.meta_value = '$semester'

JOIN usermeta um_branch 
ON um_branch.user_id = a.id 
AND um_branch.meta_key = 'branch_name'
AND um_branch.meta_value = '$branch'

JOIN usermeta um_session 
ON um_session.user_id = a.id 
AND um_session.meta_key = 'session'
AND um_session.meta_value = '$session'

WHERE a.type = 'student' 
AND a.institute_id='$institute_id'
");


// ================= SCHOOL =================

}else if(
$institute_type!='college'
&& $class_id
&& $section_id
&& $academic_session
){

$session_year = explode('-', $academic_session)[0];

$query = mysqli_query($con,"
SELECT 
    a.id,
    a.Name,
    a.roll_no

FROM accounts a

JOIN usermeta uc
ON a.id=uc.user_id
AND uc.meta_key='st_class'
AND uc.meta_value='$class_id'

JOIN usermeta us
ON a.id=us.user_id
AND us.meta_key='st_section'
AND us.meta_value='$section_id'

JOIN usermeta ud
ON a.id=ud.user_id
AND ud.meta_key='doa'

WHERE a.type='student'
AND a.institute_id='$institute_id'

AND YEAR(STR_TO_DATE(ud.meta_value,'%Y-%m-%d'))='$session_year'
");

}else{

$query = false;
}

if($query){

if(mysqli_num_rows($query)>0){

$i=1;

while($row=mysqli_fetch_assoc($query)){

?>

<tr>

<td><?= $i++ ?></td>

<td>
<?= $row['roll_no'] ?>
</td>

<td>
<?= $row['Name'] ?>
</td>

<td>

<a href="qr_test.php?roll_no=<?= $row['roll_no'] ?>"
class="btn btn-success btn-sm">

Generate QR

</a>

</td>

</tr>

<?php
}

}else{
?>

<tr>
<td colspan="4" class="text-center text-danger">
No Students Found
</td>
</tr>

<?php
}

}else{
?>

<tr>
<td colspan="4" class="text-center">
Apply filters to view students
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>
</div>

</div>
</div>

<?php include('footer.php');?>

<script>
$(document).ready(function(){

// ================= COLLEGE =================

$('#st_course').on('change', function(){

    let course_id = $(this).val();

    $('#st_branch').html('<option>Loading...</option>');
    $('#st_semester').html('<option>Loading...</option>');

    $.post('ajax.php',{
        action:'get_branch',
        course_id:course_id
    },function(res){

        $('#st_branch').html(res.options);

    },'json');

    $.post('ajax.php',{
        action:'get_semester',
        course_id:course_id
    },function(res){

        $('#st_semester').html(res.options);

    },'json');

});


// ================= SCHOOL =================

$('#st_class').on('change', function(){

    let class_id = $(this).val();

    $('#st_section').html('<option>Loading...</option>');

    $.post('ajax.php',{
        action:'get_sections',
        class_id:class_id
    },function(res){

        $('#st_section').html(res.options);

    },'json');

});

});
</script>