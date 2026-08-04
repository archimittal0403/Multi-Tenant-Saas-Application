<?php include('includes/auth.php');
checkRole('teacher');?>

<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

// ================= FILTER VALUES =================

$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$session  = $_GET['session'] ?? '';
$semester = $_GET['semester'] ?? '';

$class_id = $_GET['class_id'] ?? '';
$section  = $_GET['section'] ?? '';

$exam     = $_GET['exam'] ?? '';
$subject  = $_GET['st_subject'] ?? '';
$edit_id  = $_GET['edit_id'] ?? '';

?>

<?php

// ================= UPDATE MARKS =================

if(isset($_POST['update'])){

    $student_id = $_POST['student_id'];
    $marks      = $_POST['marks'];

    $update = $con->prepare("
        UPDATE result_marks rm
        INNER JOIN results r ON rm.result_id = r.id
        SET rm.marks = ?
        WHERE rm.student_id = ?
        AND r.exam_id = ?
        AND r.subject_id = ?
    ");

    $update->bind_param("diii",$marks,$student_id,$exam,$subject);

    $update->execute();

    echo "<script>
        alert('Marks Updated Successfully');
        window.location.href='result.php';
    </script>";
}

// ================= DELETE =================

if(isset($_GET['delete_id'])){

    $delete_id = $_GET['delete_id'];

    mysqli_query($con,"
        DELETE FROM result_marks
        WHERE id='$delete_id'
    ");

    echo "<script>
        alert('Deleted Successfully');
        window.location.href='result.php';
    </script>";
}

?>

<style>

.card{
    border:none;
    border-radius:18px;
    box-shadow:0 4px 18px rgba(0,0,0,0.08);
}

.card-body{
    padding:25px;
}

.page-title{
    font-size:28px;
    font-weight:700;
    color:#1e293b;
}

.form-control{
    height:46px;
    border-radius:12px;
    border:1px solid #dbeafe;
    transition:.3s;
}

.form-control:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,.15);
}

.btn{
    border-radius:10px;
    font-weight:600;
}

.table thead{
    background:#2563eb;
    color:#fff;
}

.table thead th{
    padding:14px;
}

.table tbody td{
    padding:14px;
    vertical-align:middle;
}

.action-btn{
    width:34px;
    height:34px;
    border-radius:8px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}

.filter-box{
    background:#f8fafc;
    padding:20px;
    border-radius:16px;
    margin-bottom:25px;
}

</style>

<div class="container-fluid">

<div class="card">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

<h3 class="page-title">Student Result</h3>

<?php if($exam){ ?>

<?php if($institute_type == 'college'){ ?>

    <?php if($course && $branch && $session && $semester && $exam && $subject){ ?>

    <a href="add-marks.php?course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>&exam=<?= $exam ?>&st_subject=<?= $subject ?>"
    class="btn btn-success">

    <i class="fa fa-plus"></i>
    Add / Edit College Marks

    </a>

    <?php } ?>

<?php } else { ?>

    <?php if($class_id && $section && $session && $exam && $subject){ ?>

    <a href="add-marks.php?class_id=<?= $class_id ?>&section=<?= $section ?>&session=<?= $session ?>&exam=<?= $exam ?>&st_subject=<?= $subject ?>"
    class="btn btn-primary">

    <i class="fa fa-plus"></i>
    Add / Edit School Marks

    </a>

    <?php } ?>

<?php } ?>

<?php } ?>

</div>

<!-- ================= FILTER ================= -->

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

<div class="col-lg-4">

<button type="submit" class="btn btn-primary">

Apply Filter

</button>

<a href="result.php" class="btn btn-secondary">

Reset

</a>

</div>

</div>

</form>

</div>

<!-- ================= TABLE ================= -->

<div class="table-responsive">

<table class="table table-bordered">

<thead>

<tr>

<th>SNO</th>
<th>Enroll ID</th>
<th>Student Name</th>
<th>Marks<?php
$max_marks = '';

if(!empty($exam)){

    $select_total = mysqli_query($con,"
        SELECT max_marks
        FROM exam_type
        WHERE institute_id='$institute_id'
        AND id='$exam'
    ");

    if(mysqli_num_rows($select_total) > 0){

        $row_exam = mysqli_fetch_assoc($select_total);
        $max_marks = $row_exam['max_marks'];

    }
}

echo ($max_marks != '') ? "( $max_marks )" : '';
?></th>
</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php

// ================= COLLEGE RESULT =================

if($institute_type == 'college'){

if($course && $branch && $session && $semester && $exam){

$q = $con->prepare("
SELECT
rm.student_id,
rm.marks,
rm.id,
a.Name,
a.roll_no
FROM results r
INNER JOIN result_marks rm ON rm.result_id = r.id
INNER JOIN accounts a ON a.id = rm.student_id
WHERE
r.course_id = ?
AND r.branch_id = ?
AND r.semester_id = ?
AND r.session_id = ?
AND r.exam_id = ?
AND r.subject_id = ?
AND r.institute_id = ?
");

$q->bind_param(
"iiiiiii",
$course,
$branch,
$semester,
$session,
$exam,
$subject,
$institute_id
);

$q->execute();

$result = $q->get_result();

$i = 1;

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $row['roll_no'] ?></td>

<td><?= $row['Name'] ?></td>

<td>

<?php if($edit_id == $row['student_id']){ ?>

<form method="POST" class="d-flex">

<input type="hidden"
name="student_id"
value="<?= $row['student_id'] ?>">

<input type="number"
name="marks"
value="<?= $row['marks'] ?>"
class="form-control mr-2"
style="width:100px;"
min="0"
max="<?= $max_marks ?>">>

<button type="submit"
name="update"
class="btn btn-success btn-sm">

<i class="fa fa-check"></i>

</button>

</form>

<?php } else { ?>

<b><?= $row['marks'] ?></b>

<?php } ?>

</td>

<td>

<a href="?course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>&exam=<?= $exam ?>&st_subject=<?= $subject ?>&edit_id=<?= $row['student_id'] ?>"
class="btn btn-primary btn-sm action-btn">

<i class="fa fa-edit"></i>

</a>

<a href="?delete_id=<?= $row['id'] ?>"
class="btn btn-danger btn-sm action-btn"
onclick="return confirm('Delete this record?')">

<i class="fa fa-trash"></i>

</a>

</td>

</tr>

<?php } } } else {

// ================= SCHOOL RESULT =================

if($class_id && $section && $session && $exam){

$q = $con->prepare("
SELECT
rm.student_id,
rm.marks,
rm.id,
a.Name,
a.roll_no
FROM results r
INNER JOIN result_marks rm ON rm.result_id = r.id
INNER JOIN accounts a ON a.id = rm.student_id
WHERE
r.class_id = ?
AND r.section_id = ?
AND r.session_id = ?
AND r.exam_id = ?
AND r.subject_id = ?
AND r.institute_id = ?
");

$q->bind_param(
"iiiiii",
$class_id,
$section,
$session,
$exam,
$subject,
$institute_id
);

$q->execute();

$result = $q->get_result();

$i = 1;

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $row['roll_no'] ?></td>

<td><?= $row['Name'] ?></td>

<td>

<?php if($edit_id == $row['student_id']){ ?>

<form method="POST" class="d-flex">

<input type="hidden"
name="student_id"
value="<?= $row['student_id'] ?>">

<input type="number"
name="marks"
value="<?= $row['marks'] ?>"
class="form-control mr-2"
style="width:100px;">

<button type="submit"
name="update"
class="btn btn-success btn-sm">

<i class="fa fa-check"></i>

</button>

</form>

<?php } else { ?>

<b><?= $row['marks'] ?></b>

<?php } ?>

</td>

<td>

<a href="?class_id=<?= $class_id ?>&section=<?= $section ?>&session=<?= $session ?>&exam=<?= $exam ?>&st_subject=<?= $subject ?>&edit_id=<?= $row['student_id'] ?>"
class="btn btn-primary btn-sm action-btn">

<i class="fa fa-edit"></i>

</a>

<a href="?delete_id=<?= $row['id'] ?>"
class="btn btn-danger btn-sm action-btn"
onclick="return confirm('Delete this record?')">

<i class="fa fa-trash"></i>

</a>

</td>

</tr>

<?php } } } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<?php include('footer.php');?>
<script>

$(document).ready(function(){

<?php if($institute_type == 'college'){ ?>

// ================= COLLEGE =================

$('#st_course').on('change', function(){

    let course_id = $(this).val();

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

function loadCollegeData(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();

    // SUBJECT LOAD
    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',
        data:{
            action:'get_subject',
            course_id:course_id,
            branch_id:branch_id,
            semester_id:semester,
            session:session
        },

        success:function(res){

            $('#st_subject').html(res.options);

            <?php if($subject != ''){ ?>
            $('#st_subject').val('<?= $subject ?>');
            <?php } ?>

        }

    });

    // EXAM LOAD
    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',
        data:{
            action:'get_exam',
            course_id:course_id,
            branch_id:branch_id,
            semester_id:semester,
            session_id:session
        },

        success:function(res){

            if(res.status){

                $('#st_exam').html(
                    '<option value="">Select Exam</option>' +
                    '<option value="'+res.exam_id+'">'+res.exam_type+'</option>'
                );

                <?php if($exam != ''){ ?>
                $('#st_exam').val('<?= $exam ?>');
                <?php } ?>

            }

        }

    });

}

$('#st_branch, #st_semester, #st_session').on('change', function(){

    loadCollegeData();

});

// AUTO LOAD
<?php if($course != ''){ ?>

$.post('ajax.php',{
    action:'get_branch',
    course_id:'<?= $course ?>'
},function(res){

    $('#st_branch').html(res.options);
    $('#st_branch').val('<?= $branch ?>');

},'json');

$.post('ajax.php',{
    action:'get_semester',
    course_id:'<?= $course ?>'
},function(res){

    $('#st_semester').html(res.options);
    $('#st_semester').val('<?= $semester ?>');

    loadCollegeData();

},'json');

<?php } ?>

<?php } else { ?>

// ================= SCHOOL =================

function loadSections(class_id, selected_section=''){

    $.post('ajax.php',{

        action:'get_sections',
        class_id:class_id

    },function(res){

        $('#st_section').html(res.options);

        if(selected_section!=''){
            $('#st_section').val(selected_section);
        }

        loadSubjectAndExam();

    },'json');

}

// SUBJECT + EXAM LOAD
function loadSubjectAndExam(){

    let class_id = $('#class_id').val();
    let section  = $('#st_section').val();

    if(class_id == '' || section == ''){
        return;
    }

    // SUBJECT
    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',
        data:{
            action:'get_subject',
            class_id:class_id,
            section_id:section
        },

        success:function(res){

            $('#st_subject').html(res.options);

            <?php if($subject != ''){ ?>
            $('#st_subject').val('<?= $subject ?>');
            <?php } ?>

        }

    });

    // EXAM
    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',
        data:{
            action:'get_exam',
            class_id:class_id,
            section_id:section
        },

        success:function(res){

            $('#st_exam').html(res.options);

            <?php if($exam != ''){ ?>
            $('#st_exam').val('<?= $exam ?>');
            <?php } ?>

        }

    });

}

// CLASS CHANGE
$('#class_id').on('change', function(){

    let class_id = $(this).val();

    loadSections(class_id);

});

// SECTION CHANGE
$('#st_section').on('change', function(){

    loadSubjectAndExam();

});

// SESSION CHANGE
$('#st_session').on('change', function(){

    loadSubjectAndExam();

});

// AUTO LOAD OLD DATA
<?php if($class_id != ''){ ?>

loadSections('<?= $class_id ?>','<?= $section ?>');

<?php } ?>

<?php } ?>

}); // ✅ DOCUMENT READY CLOSE

</script>