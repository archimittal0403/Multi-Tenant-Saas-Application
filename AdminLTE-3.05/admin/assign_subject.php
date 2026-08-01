<?php
session_start();

if(
    empty($_SESSION['user_id']) || 
    empty($_SESSION['user_type']) || 
    empty($_SESSION['institute_id'])
){
    header("Location: ../login.php");
    exit;
}

include('includes/config.php');
require_once('includes/functions.php');
require_once('includes/dynamic-form.php');

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];
$institute_code = $_SESSION['institute_code'];

include('header.php');
include('sidebar.php');

$teacher_id = $_GET['teacher_id'];

if(isset($_POST['align'])){

    $course_id = $_POST['st_course'] ?? '';
    $branch_id = $_POST['st_branch'] ?? '';
    $session   = $_POST['st_session'] ?? '';
    $semester  = $_POST['st_semester'] ?? '';
    $subject   = $_POST['st_subject'] ?? '';

    $insert = mysqli_query($con,"
        INSERT INTO teacher_subjects
        (teacher_id,course_id,branch_id,semester,subject_id,session_id,institute_id)

        VALUES(
        '$teacher_id',
        '$course_id',
        '$branch_id',
        '$semester',
        '$subject',
        '$session',
        '$institute_id'
        )
    ");

    if($insert){

        echo "<script>
            alert('Subject aligned successfully');
            window.open('teacher.php','_self');
        </script>";
    }
}
?>

<style>

body{
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
}

.content-wrapper{
    background:#f1f5f9 !important;
}

/* PAGE TITLE */

.page-title{
    font-size:32px;
    font-weight:800;
    color:#0f172a;
    margin-bottom:25px;
}

/* MAIN CARD */

.main-card{
    background:#fff;
    border-radius:24px;
    padding:30px;
    box-shadow:0 10px 35px rgba(15,23,42,.08);
    border:none;
}

/* HEADER */

.top-header{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    border-radius:20px;
    padding:25px;
    color:#fff;
    margin-bottom:30px;
}

.top-header h4{
    font-size:24px;
    font-weight:700;
    margin-bottom:5px;
}

.top-header p{
    margin:0;
    opacity:.9;
}

/* FORM */

.section-box{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
}

.form-group label{
    font-size:13px;
    font-weight:700;
    color:#334155;
    margin-bottom:8px;
}

.form-control{
    height:52px;
    border-radius:14px;
    border:1px solid #dbe4f0;
    font-size:14px;
    padding:10px 14px;
    transition:.3s;
}

.form-control:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.12);
}

/* BUTTON */

.btn-align{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:#fff;
    border:none;
    padding:14px 28px;
    border-radius:14px;
    font-weight:700;
    font-size:15px;
    transition:.3s;
}

.btn-align:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(37,99,235,.25);
}

/* MOBILE */

@media(max-width:768px){

    .main-card{
        padding:20px;
    }

    .page-title{
        font-size:24px;
    }

    .top-header{
        padding:18px;
    }

    .btn-align{
        width:100%;
    }

}

</style>



<div class="container-fluid">

<h2 class="page-title">
    🎓 Align Teacher Subject
</h2>

<div class="main-card">

<div class="top-header">
    <h4>Assign Subject to Teacher</h4>
    <p>Select academic details and align subjects easily</p>
</div>

<form action="" method="post">

<div class="section-box">

<div class="row">

<?php if($institute_type=='college'){ ?>

<!-- COURSE -->

<div class="col-md-4 mb-4">
<label>Course</label>

<select name="st_course" id="filter_course" class="form-control">

<option value="">Select Course</option>

<?php

$courses = get_posts(['type'=>'course']);

foreach($courses as $c){

echo "<option value='$c->id'>$c->title</option>";

}

?>

</select>
</div>

<!-- BRANCH -->

<div class="col-md-4 mb-4">

<label>Branch</label>

<select name="st_branch" id="filter_branch" class="form-control">

<option value="">Select Branch</option>

</select>

</div>

<!-- SESSION -->

<div class="col-md-4 mb-4">

<label>Academic Session</label>

<input type="text"
name="st_session"
id="st_session"
placeholder="2026-2027"
class="form-control">

</div>

<!-- SEMESTER -->

<div class="col-md-4 mb-4">

<label>Semester</label>

<select name="st_semester" id="st_semester" class="form-control">

<option value="">Select Semester</option>

</select>

</div>

<!-- SUBJECT -->

<div class="col-md-4 mb-4">

<label>Subject</label>

<select id="st_subject"
name="st_subject"
class="form-control">

<option value="">Select Subject</option>

</select>

</div>

<?php } else { ?>

<!-- CLASS -->

<div class="col-md-4 mb-4">

<label>Class</label>

<select name="st_class"
id="filter_class"
class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){

echo "<option value='$c->id'>$c->title</option>";

}

?>

</select>

</div>

<!-- SECTION -->

<div class="col-md-4 mb-4">

<label>Section</label>

<select id="filter_section"
name="st_section"
class="form-control">

<option value="">Select Section</option>

</select>

</div>

<!-- SESSION -->

<div class="col-md-4 mb-4">

<label>Academic Year</label>

<input type="text"
id="session"
name="session"
class="form-control"
placeholder="2026-2027">

</div>

<!-- SUBJECT -->

<div class="col-md-4 mb-4">

<label>Subject</label>

<select id="st_subject"
name="st_subject"
class="form-control">

<option value="">Select Subject</option>

</select>

</div>

<?php } ?>

</div>

<div class="text-right mt-3">

<button id="apply_filter"
class="btn btn-align"
name="align">

<i class="fa fa-check-circle"></i>

Align Subject

</button>

</div>

</div>

</form>

</div>

</div>



<?php include('footer.php'); ?>

<script>

$(document).ready(function(){

/* LOAD SECTION */

$('#filter_class').on('change', function () {

    let class_id = $(this).val();

    $('#filter_section').html('<option>Loading...</option>');

    $.ajax({

        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',

        data: {
            action: 'get_sections',
            class_id: class_id
        },

        success: function (res) {

            if(res.status){

                $('#filter_section').html(res.options);

            } else {

                $('#filter_section').html(
                    '<option value="">No Section Found</option>'
                );
            }
        }

    });

});

/* LOAD BRANCH */

$('#filter_course').on('change', function(){

    let course_id = $(this).val();

    $('#filter_branch').html('<option>Loading...</option>');

    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',

        data:{
            action:'get_branch',
            course_id:course_id
        },

        success:function(res){

            if(res.status){

                $('#filter_branch').html(res.options);

            } else {

                $('#filter_branch').html(
                    '<option value="">No Branch Found</option>'
                );
            }

        }

    });

});

/* LOAD SEMESTER */

$('#filter_course').on('change', function(){

    let course_id = $(this).val();

    $('#st_semester').html('<option>Loading...</option>');

    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',

        data:{
            action:'get_semester',
            course_id:course_id
        },

        success:function(res){

            if(res.status){

                $('#st_semester').html(res.options);

            } else {

                $('#st_semester').html(
                    '<option value="">No Semester Found</option>'
                );
            }

        }

    });

});

/* LOAD SUBJECT */

function loadSubject(){

    let class_id   = $('#filter_class').val();
    let section_id = $('#filter_section').val();

    let course_id  = $('#filter_course').val();
    let branch_id  = $('#filter_branch').val();

    $('#st_subject').html('<option>Loading...</option>');

    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',

        data:{
            action:'get_subject',
            class_id:class_id,
            section_id:section_id,
            course_id:course_id,
            branch_id:branch_id
        },

        success:function(res){

            if(res.status){

                $('#st_subject').html(res.options);

            } else {

                $('#st_subject').html(
                    '<option value="">No Subject Found</option>'
                );
            }
        }
    });
}

/* TRIGGERS */

$('#filter_class').on('change', loadSubject);

$('#filter_section').on('change', loadSubject);

$('#filter_course').on('change', loadSubject);

$('#filter_branch').on('change', loadSubject);

$('#st_semester').on('change', loadSubject);

$('#st_session').on('change', loadSubject);

});

</script>