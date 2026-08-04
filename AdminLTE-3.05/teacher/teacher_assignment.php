<?php include('includes/auth.php');
checkRole('teacher'); ?>

<?php include('includes/config.php') ?>
<?php include('includes/functions.php') ?>
<?php include('includes/dynamic-form.php'); ?>
<?php include('header.php') ?>
<?php include('sidebar.php') ?>

<style>

.container-fluid{
    padding-left:15px;
    padding-right:15px;
}

.card{
    border-radius:16px;
    border:none;
}

.card-body{
    padding:20px;
}

.table-responsive{
    overflow-x:auto;
}

.table{
    min-width:900px;
}

.table td,
.table th{
    vertical-align:middle;
    white-space:nowrap;
}

.form-control{
    width:100%;
    border-radius:10px;
}

.btn{
    border-radius:10px;
}

.action-btns{
    display:flex;
    flex-wrap:wrap;
    gap:6px;
}

.upload-card{
    position:sticky;
    top:20px;
}

.dynamic-field .form-group{
    margin-bottom:15px;
}

@media(max-width:991px){

    .upload-card{
        position:relative;
        top:0;
    }

    .table{
        font-size:13px;
    }

    h3{
        font-size:24px;
    }

    h5{
        font-size:18px;
    }
}

@media(max-width:768px){

    .card-body{
        padding:15px;
    }

    .filter-btn{
        width:100%;
    }

    .top-header{
        flex-direction:column;
        align-items:flex-start !important;
        gap:10px;
    }

    .top-header a{
        width:100%;
    }

    .top-header .btn{
        width:100%;
    }

    .table{
        font-size:12px;
    }

    .action-btns{
        min-width:180px;
    }

    .action-btns .btn{
        width:100%;
    }

    .mobile-gap{
        margin-top:15px;
    }
}

</style>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$course_id  = $_GET['course_id'] ?? '';
$branch_id  = $_GET['branch_id'] ?? '';
$semester   = $_GET['semester'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$session_id = $_GET['session'] ?? '';

$class_id   = $_GET['class_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$academic_session = $_GET['academic_session'] ?? '';

$get_file_session = get_metadata($subject_id,'assignment_session');
$get_title = get_metadata($subject_id,'assignment_title');
$get_file  = get_metadata($subject_id,'assignment_file');
$get_desc  = get_metadata($subject_id,'assignment_description');
$get_last  = get_metadata($subject_id,'assignment_last_date');

?>

<?php

// ================= DELETE =================

if(isset($_GET['delete_id'])){

    $index = $_GET['delete_id'];

    $subject_id = $_GET['subject_id'];

    $files    = get_metadata($subject_id,'assignment_file');
    $titles   = get_metadata($subject_id,'assignment_title');
    $sessions = get_metadata($subject_id,'assignment_session');
    $descs    = get_metadata($subject_id,'assignment_description');
    $dates    = get_metadata($subject_id,'assignment_last_date');

    $file_name = $files[$index]->meta_value;

    $path = __DIR__."/uploads/assignment/".$file_name;

    if(file_exists($path)){
        unlink($path);
    }

    mysqli_query($con,"DELETE FROM metadata WHERE id='".$files[$index]->id."'");
    mysqli_query($con,"DELETE FROM metadata WHERE id='".$titles[$index]->id."'");
    mysqli_query($con,"DELETE FROM metadata WHERE id='".$sessions[$index]->id."'");

    if(isset($descs[$index])){
        mysqli_query($con,"DELETE FROM metadata WHERE id='".$descs[$index]->id."'");
    }

    if(isset($dates[$index])){
        mysqli_query($con,"DELETE FROM metadata WHERE id='".$dates[$index]->id."'");
    }

    echo "<script>
    alert('Deleted Successfully');
    window.location.href='teacher_assignment.php';
    </script>";
}

?>

<?php

// ================= UPLOAD =================

if(isset($_POST['upload'])){

    if(empty($_FILES['file']['name'])){

        echo "<script>alert('Please Select File')</script>";

    }else{

        $title       = mysqli_real_escape_string($con,$_POST['title']);
        $description = mysqli_real_escape_string($con,$_POST['description']);
        $last_date   = mysqli_real_escape_string($con,$_POST['last_date']);
   $duplicate_query = mysqli_query($con,"
                SELECT *
                FROM metadata
                WHERE item_id='$subject_id'
                AND meta_key='assignment_title'
                AND meta_value='$title'
            ");

            if(mysqli_num_rows($duplicate_query) > 0){

                echo "<script>
                    alert('Assignment Already Uploaded');
                </script>";

            }
            else{
        $file = preg_replace('/[^A-Za-z0-9\-\._]/', '_', $_FILES['file']['name']);

        $tmp  = $_FILES['file']['tmp_name'];

        $upload_dir = __DIR__.'/uploads/assignment/';

        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0777,true);
        }

        $filename = time().'_'.$file;

        if(move_uploaded_file($tmp,$upload_dir.$filename)){

            mysqli_query($con,"INSERT INTO metadata
            (item_id,meta_key,meta_value) VALUES

            ('$subject_id','assignment_file','$filename'),

            ('$subject_id','assignment_title','$title'),

            ('$subject_id','assignment_description','$description'),

            ('$subject_id','assignment_last_date','$last_date')
            ");

            if($institute_type=='college'){

                mysqli_query($con,"INSERT INTO metadata
                (item_id,meta_key,meta_value) VALUES

                ('$subject_id','assignment_session','$session_id'),

                ('$subject_id','course_id','$course_id'),

                ('$subject_id','branch_id','$branch_id'),

                ('$subject_id','semester','$semester')
                ");

            }else{

                mysqli_query($con,"INSERT INTO metadata
                (item_id,meta_key,meta_value) VALUES

                ('$subject_id','academic_session','$academic_session'),

                ('$subject_id','class_id','$class_id'),

                ('$subject_id','section_id','$section_id')
                ");
            }

            // ================= DYNAMIC FIELDS =================

            foreach($_POST as $key => $value){

                $skip = [
                    'upload',
                    'title',
                    'description',
                    'last_date',
                    'file'
                ];

                if(in_array($key,$skip)) continue;

                if($value==='' || $value===null) continue;

                $key   = mysqli_real_escape_string($con,$key);
                $value = mysqli_real_escape_string($con,$value);

                mysqli_query($con,"
                    INSERT INTO metadata
                    (item_id,meta_key,meta_value)
                    VALUES
                    ('$subject_id','$key','$value')
                ");
            }

            echo "<script>alert('Uploaded Successfully')</script>";

        }else{

            echo "<script>alert('Upload Failed')</script>";
                }
            }
        }
    }


?>

<div class="container-fluid mt-4">

<div class="card shadow">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4 top-header">

<div>

<h3 class="mb-1">Home Assignment</h3>

<p class="text-muted mb-0">
Manage assignments and uploads
</p>

</div>

<!-- <a href="feilds.php" class="btn btn-dark">
    Manage Fields
</a> -->

</div>

<h5 class="mb-3">Filteration</h5>

<div class="row g-3">

<?php if($institute_type=='college'){ ?>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Course</label>

<select id="st_course" class="form-control">

<option value="">Select Course</option>

<?php

$courses = get_posts(['type'=>'course']);

foreach($courses as $course){
?>

<option value="<?= $course->id ?>"
<?= ($course_id==$course->id)?'selected':'' ?>>

<?= $course->title ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Branch</label>

<select id="st_branch" class="form-control">

<option value="">Select Branch</option>

</select>

</div>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Session</label>

<select id="st_session" class="form-control">

<option value="">Select Session</option>

<?php

$sessions = get_posts([
'type'=>'session',
'institute_id'=>$institute_id
]);

foreach($sessions as $session){
?>

<option value="<?= $session->id ?>"
<?= ($session_id==$session->id)?'selected':'' ?>>

<?= $session->title ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Semester</label>

<select id="st_semester" class="form-control">

<option value="">Select Semester</option>

</select>

</div>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Subject</label>

<select id="st_subject" class="form-control">

<option value="">Select Subject</option>

</select>

</div>

<?php } else { ?>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Class</label>

<select id="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $class){
?>

<option value="<?= $class->id ?>"
<?= ($class_id==$class->id)?'selected':'' ?>>

<?= $class->title ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Section</label>

<select id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>

<div class="col-12 col-md-6 col-lg-3">

<label>Academic Session</label>

<input type="text"
id="academic_session"
class="form-control"
value="<?= $academic_session ?>"
placeholder="2025-2026">

</div>

<div class="col-12 col-md-6 col-lg-3">

<label>Select Subject</label>

<select id="school_subject" class="form-control">

<option value="">Select Subject</option>

</select>

</div>

<?php } ?>

<div class="col-12">

<button type="button"
id="apply_filter"
class="btn btn-danger filter-btn">

Apply Filter

</button>

</div>

</div>

</div>

</div>

<div class="row mt-4">

<!-- UPLOAD -->

<div class="col-12 col-lg-4 mb-4">

<div class="card shadow upload-card">

<div class="card-body">

<h5 class="mb-3 text-center">

Upload Assignment

</h5>

<form method="POST"
enctype="multipart/form-data">

<div class="form-group mb-3">

<label>Assignment Title</label>

<input type="text"
name="title"
class="form-control"
required>

</div>

<div class="form-group mb-3">

<label>Description</label>

<textarea name="description"
class="form-control"
rows="4"></textarea>

</div>

<div class="form-group mb-3">

<label>Last Submission Date</label>

<input type="date"
name="last_date"
class="form-control">

</div>

<div class="form-group mb-3">

<label>Select File</label>

<input type="file"
name="file"
class="form-control"
required>

</div>

<div class="row">

<?php render_dynamic_form('assignment'); ?>

</div>

<button type="submit"
name="upload"
class="btn btn-primary w-100">

Upload Assignment

</button>

</form>

</div>

</div>

</div>

<!-- FILE LIST -->

<div class="col-12 col-lg-8">

<div class="card shadow">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-3 top-header">

<h5 class="mb-0">Uploaded Files</h5>
<?php
if($institute_type=='college'){?>
<a href="assignment_submission.php?
course_id=<?= $course_id ?>
&branch_id=<?= $branch_id ?>
&semester=<?= $semester ?>
&session=<?= $session_id ?>
&subject_id=<?= $subject_id ?>"
class="btn btn-success btn-sm">

View Submission

</a>
<?php } else {?>
<a href="assignment_submission.php?
&subject_id=<?= $subject_id ?>
&class_id=<?= $class_id ?>
&section_id=<?= $section_id ?>
&academic_session=<?= $academic_session ?>"
class="btn btn-success btn-sm">

View Submission

</a>
<?php } ?>
</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Sno</th>
<th>Title</th>
<th>Description</th>
<th>Last Date</th>
<th>File</th>

<?php

$field_query = mysqli_query($con,"
    SELECT * FROM fields 
    WHERE institute_id='$institute_id'
    AND form_type='assignment'
");

$field_array = [];

while($f = mysqli_fetch_assoc($field_query)){

    $field_array[] = $f;

    echo "<th>{$f['field_name']}</th>";
}

?>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(!empty($get_file)){

for($i=0;$i<count($get_file);$i++){

$file_session = $get_file_session[$i]->meta_value ?? '';

if($institute_type=='college'){

    $show_file = ($file_session == $session_id);

}else{

    $show_file = true;
}

if($show_file){

$file_name  = $get_file[$i]->meta_value ?? '';
$file_title = $get_title[$i]->meta_value ?? '';
$file_desc  = $get_desc[$i]->meta_value ?? '';
$file_last  = $get_last[$i]->meta_value ?? '';

$file_path = "uploads/assignment/".$file_name;

?>

<tr>

<td><?= $i+1 ?></td>

<td><?= $file_title ?></td>

<td><?= $file_desc ?></td>

<td><?= $file_last ?></td>

<td><?= $file_name ?></td>

<?php

foreach($field_array as $f){

    $key = $f['field_key'];

    if($key == 'assignment_title'){

        $value = $file_title;

    }elseif($key == 'assignment_description'){

        $value = $file_desc;

    }elseif($key == 'assignment_last_date'){

        $value = $file_last;

    }elseif($key == 'assignment_file'){

        $value = $file_name;

    }else{

        $meta_q = mysqli_query($con,"
            SELECT meta_value
            FROM metadata
            WHERE item_id='$subject_id'
            AND meta_key='$key'
            ORDER BY id DESC
            LIMIT 1
        ");

        $meta = mysqli_fetch_assoc($meta_q);

        $value = $meta['meta_value'] ?? '-';
    }

    if(filter_var($value, FILTER_VALIDATE_URL)){

        echo "<td>
                <a href='$value' target='_blank'>
                    Open Link
                </a>
              </td>";

    }else{

        echo "<td>$value</td>";
    }
}

?>

<td>

<div class="action-btns">

<a href="<?= $file_path ?>"
target="_blank"
class="btn btn-primary btn-sm">

View

</a>

<a href="download.php?file=<?= urlencode($file_name) ?>"
class="btn btn-success btn-sm">

Download

</a>

<a href="teacher_assignment.php?
delete_id=<?= $i ?>
&subject_id=<?= $subject_id ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this file?')">

Delete

</a>

</div>

</td>

</tr>

<?php
}
}
}
?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include('footer.php'); ?>

<script>

$(document).ready(function(){

function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();

    if(course_id && branch_id && semester && session){

        $('#st_subject').html('<option>Loading...</option>');

        $.ajax({

            url:'ajax.php',
            type:'POST',
            dataType:'json',

            data:{
                action:'get_subject',
                course_id:course_id,
                branch_id:branch_id,
                semester:semester,
                session:session
            },

            success:function(res){

                if(res.status){

                    $('#st_subject').html(res.options);

                }else{

                    $('#st_subject').html('<option>No Subject Found</option>');
                }
            }
        });
    }
}

$('#st_course').on('change',function(){

    let course_id = $(this).val();

    $('#st_branch').html('<option>Loading...</option>');
    $('#st_semester').html('<option>Loading...</option>');
    $('#st_subject').html('<option>Select Subject</option>');

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

$('#st_branch,#st_semester,#st_session').on('change',function(){

    loadSubject();

});

$('#st_class').on('change',function(){

    let class_id = $(this).val();

    $('#st_section').html('<option>Loading...</option>');

    $('#school_subject').html('<option>Select Subject</option>');

    $.post('ajax.php',{

        action:'get_sections',
        class_id:class_id

    },function(res){

        $('#st_section').html(res.options);

    },'json');

});

$('#st_section').on('change',function(){

    let class_id   = $('#st_class').val();
    let section_id = $('#st_section').val();

    $('#school_subject').html('<option>Loading...</option>');

    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',

        data:{
            action:'get_subject',
            class_id:class_id,
            section_id:section_id
        },

        success:function(res){

            if(res.status){

                $('#school_subject').html(res.options);

            }else{

                $('#school_subject').html('<option>No Subject Found</option>');
            }
        }
    });

});

$('#apply_filter').on('click',function(){

    let url = "teacher_assignment.php?";

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();
    let subject_id = $('#st_subject').val();

    let class_id = $('#st_class').val();
    let section_id = $('#st_section').val();
    let academic_session = $('#academic_session').val();
    let school_subject = $('#school_subject').val();

    if(course_id){

        if(subject_id == ''){
            alert('Please Select Subject');
            return false;
        }

        url += "course_id="+course_id+
               "&branch_id="+branch_id+
               "&semester="+semester+
               "&session="+session+
               "&subject_id="+subject_id;

    }else{

        if(school_subject == ''){
            alert('Please Select Subject');
            return false;
        }

        url += "class_id="+class_id+
               "&section_id="+section_id+
               "&academic_session="+academic_session+
               "&subject_id="+school_subject;
    }

    window.location.href = url;

});

});

</script>