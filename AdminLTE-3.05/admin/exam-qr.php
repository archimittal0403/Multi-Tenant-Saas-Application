<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>

<?php

$institute_id = $_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
$current_year = date('Y');
$session = $current_year . '-' . ($current_year + 1);
// FILTER VALUES
$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$class_id          = $_GET['class_id'] ?? '';

$academic_session  = $_GET['academic_session'] ?? '';
$semester = $_GET['semester'] ?? '';

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

<th>Subject Name</th>
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
    && $semester
){

$query = mysqli_query($con,"
SELECT DISTINCT p.id,p.title
FROM posts p

INNER JOIN metadata m1
ON p.id=m1.item_id
AND m1.meta_key='semester'
AND m1.meta_value='$semester'

INNER JOIN metadata m2
ON p.id=m2.item_id
AND m2.meta_key='session'
AND m2.meta_value='$session'

WHERE p.type='subject'
AND p.parent='$branch'
AND p.institute_id='$institute_id'

ORDER BY p.title ASC
");


// ================= SCHOOL =================
}else if(
    $institute_type!='college'
    && $class_id
){

$query = mysqli_query($con,"
SELECT DISTINCT p.id,p.title
FROM posts p

INNER JOIN metadata m
ON p.id=m.item_id
AND m.meta_key='class'
AND m.meta_value='$class_id'

WHERE p.type='subject'
AND p.institute_id='$institute_id'

ORDER BY p.title ASC
");

}else{

$query=false;

}

if($query){
if(mysqli_num_rows($query)>0){

$i=1;

while($row=mysqli_fetch_assoc($query)){

?>

<tr>

<td><?= $i++ ?></td>
<td>
    <?= $row['title'] ?>
</td>

<td>
<a href="subject-qr.php?subject_id=<?= $row['id'] ?>"
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
    No Subjects Found
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

// ================= LOAD SUBJECT =================
function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();
    let subject =$('#st_subject').val();
    let exam=$('#st_exam').val();

    if(course_id && branch_id && semester && session){

        $('#st_subject').html('<option>Loading...</option>');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_subject',
                course_id: course_id,
                branch_id: branch_id,
                semester: semester,
                session: session
            },
            success: function(res){
                if(res.status){
                    $('#st_subject').html(res.options);
                } else {
                    $('#st_subject').html('<option>No Subject Found</option>');
                }
            }
        });
    }
        if(course_id && branch_id && semester && session){

        $('#st_exam').html('<option>Loading...</option>');

    $.ajax({
    url: 'ajax.php',
    type: 'POST',
    dataType: 'json',
    data: {
        action: 'get_exam',
        course_id: course_id,
        branch_id: branch_id,
        semester_id: semester,
        session_id: session
    },
  success: function(res){

    if(res.status){

        // exam dropdown
        $('#st_exam').html(
            '<option value="">Select Exam</option>' +
            '<option value="'+res.exam_id+'">'+res.exam_type+'</option>'
        );

        // 🔥 DATE RANGE GENERATE
        let start = new Date(res.start_date);
        let end   = new Date(res.end_date);

        let options = '<option value="">Select Exam Date</option>';

        while(start <= end){

            let year  = start.getFullYear();
            let month = String(start.getMonth() + 1).padStart(2, '0');
            let day   = String(start.getDate()).padStart(2, '0');

            let formatted = `${year}-${month}-${day}`;

            options += `<option value="${formatted}">${formatted}</option>`;

            start.setDate(start.getDate() + 1);
        }

        // 🔥 IMPORTANT: dropdown me set karo
        $('#exam_date').html(options);

    } else {

        $('#st_exam').html('<option>No Exam Found</option>');
        $('#exam_date').html('<option>No Dates</option>');
    }
}
});
    }
}


// ================= COURSE CHANGE =================
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

    // 🔥 IMPORTANT: reset subject properly

}); // ✅ YE MISSING THA


// ================= AUTO SUBJECT LOAD =================
$('#st_branch, #st_semester, #st_session').on('change', function(){
    loadSubject();
});

}); // document ready end

</script>