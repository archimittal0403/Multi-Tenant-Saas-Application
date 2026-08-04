<?php include('includes/auth.php');
checkRole('teacher'); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/dynamic-form.php'); ?>

<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<style>

.card{
    border-radius:12px;
}

.table-responsive{
    overflow-x:auto;
}

.table td,
.table th{
    white-space: nowrap;
    vertical-align: middle;
}

.form-control{
    width:100%;
}

@media(max-width:768px){

    h3,h5{
        font-size:20px;
    }

    .table{
        font-size:13px;
    }

    .card-body{
        padding:15px;
    }

    .filter-buttons .btn{
        width:100%;
    }

    .action-buttons{
        min-width:180px;
    }
}

</style>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$class_id   = $_GET['class'] ?? '';
$section_id = $_GET['section'] ?? '';

$course_id  = $_GET['course_id'] ?? '';
$branch_id  = $_GET['branch_id'] ?? '';
$semester   = $_GET['semester'] ?? '';
$session_id = $_GET['session'] ?? $_POST['session_id'] ?? '';

$subject_id = $_GET['subject_id'] ?? '';

?>

<?php
/* ================= DELETE ================= */
if(isset($_GET['delete_id'])){

    $delete_id = intval($_GET['delete_id']);

    $file_q = mysqli_query($con,"
        SELECT meta_value
        FROM metadata
        WHERE item_id='$delete_id'
        AND meta_key='study_material_file'
    ");

    $file = mysqli_fetch_assoc($file_q);

    if($file){

        $path = __DIR__."/uploads/assignment/".$file['meta_value'];

        if(file_exists($path)){
            unlink($path);
        }
    }

    mysqli_query($con,"DELETE FROM metadata WHERE item_id='$delete_id'");
    mysqli_query($con,"DELETE FROM posts WHERE id='$delete_id'");

    echo "<script>
        alert('Deleted Successfully');
        window.location.href='study.php';
    </script>";
}
?>

<?php
/* ================= UPLOAD ================= */
if(isset($_POST['upload'])){

    if(empty($_FILES['file']['name'])){

        echo "<script>alert('Please Select File')</script>";

    }else {

        if(empty($subject_id)){

            echo "<script>alert('Please Select Subject First')</script>";

        } 
        else {

            $title = mysqli_real_escape_string($con,$_POST['title']);

            // ================= DUPLICATE CHECK =================

            $duplicate_where = "
                p.type='study_material'
                AND p.parent='$subject_id'
                AND p.title='$title'
            ";

            if($institute_type == 'college'){

                $duplicate_where .= "

                    AND EXISTS (
                        SELECT 1 FROM metadata
                        WHERE item_id=p.id
                        AND meta_key='course_id'
                        AND meta_value='$course_id'
                    )

                    AND EXISTS (
                        SELECT 1 FROM metadata
                        WHERE item_id=p.id
                        AND meta_key='branch_id'
                        AND meta_value='$branch_id'
                    )

                    AND EXISTS (
                        SELECT 1 FROM metadata
                        WHERE item_id=p.id
                        AND meta_key='semester'
                        AND meta_value='$semester'
                    )

                    AND EXISTS (
                        SELECT 1 FROM metadata
                        WHERE item_id=p.id
                        AND meta_key='session_id'
                        AND meta_value='$session_id'
                    )
                ";

            } else {

                $duplicate_where .= "

                    AND EXISTS (
                        SELECT 1 FROM metadata
                        WHERE item_id=p.id
                        AND meta_key='class_id'
                        AND meta_value='$class_id'
                    )

                    AND EXISTS (
                        SELECT 1 FROM metadata
                        WHERE item_id=p.id
                        AND meta_key='section_id'
                        AND meta_value='$section_id'
                    )
                ";
            }

            $duplicate_query = mysqli_query($con,"
                SELECT p.id
                FROM posts p
                WHERE $duplicate_where
            ");

            if(mysqli_num_rows($duplicate_query) > 0){

                echo "<script>
                    alert('Study Material Already Uploaded');
                </script>";

            }
        
    else {

        $title = mysqli_real_escape_string($con,$_POST['title']);

        $file = preg_replace('/[^A-Za-z0-9\-\._]/', '_', $_FILES['file']['name']);

        $tmp  = $_FILES['file']['tmp_name'];

        $filename = time().'_'.$file;

        $upload_dir = __DIR__.'/uploads/assignment/';

        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0777,true);
        }

        if(move_uploaded_file($tmp,$upload_dir.$filename)){

            mysqli_query($con,"
                INSERT INTO posts
                (author,title,type,parent,institute_id)
                VALUES
                (1,'$title','study_material','$subject_id','$institute_id')
            ");

            $study_material_id = mysqli_insert_id($con);

            $meta_data = [
                'study_material_file'  => $filename,
                'study_material_title' => $title,
                'subject_id'           => $subject_id,
                'session_id'           => $session_id,
            ];

            if($institute_type == 'college'){

                $meta_data['course_id'] = $course_id;
                $meta_data['branch_id'] = $branch_id;
                $meta_data['semester']  = $semester;

            } else {

                $meta_data['class_id']   = $class_id;
                $meta_data['section_id'] = $section_id;
            }

            foreach($meta_data as $key => $value){

                if($value === '' || $value === null) continue;

                mysqli_query($con,"
                    INSERT INTO metadata
                    (item_id,meta_key,meta_value)
                    VALUES
                    ('$study_material_id','$key','$value')
                ");
            }

            foreach($_POST as $key => $value){

                $skip = [
                    'upload','title','file','subject_id','session_id',
                    'course_id','branch_id','semester','class_id','section_id'
                ];

                if(in_array($key,$skip)) continue;
                if($value === '' || $value === null) continue;

                $value = mysqli_real_escape_string($con,$value);

                mysqli_query($con,"
                    INSERT INTO metadata
                    (item_id,meta_key,meta_value)
                    VALUES
                    ('$study_material_id','$key','$value')
                ");
            }

            echo "<script>alert('Uploaded Successfully')</script>";
        }
    }
}
    }
}
?>

<div class="container-fluid mt-4">

<div class="card shadow">

<div class="card-body">

<h3 class="mb-4">Study Material</h3>

<!-- <div class="d-flex flex-wrap gap-2 mb-4">

<a href="feilds.php" class="btn btn-dark">
    Manage Fields
</a>

</div> -->

<div class="row g-3 align-items-end">

<?php if($institute_type=='college'){ ?>

<!-- COURSE -->

<div class="col-lg-3">

<div class="form-group">

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
</div>

<!-- BRANCH -->

<div class="col-lg-3">

<div class="form-group">

<label>Select Branch</label>

<select id="st_branch" class="form-control">

<option value="">Select Branch</option>

</select>

</div>
</div>

<!-- SESSION -->

<div class="col-lg-3">

<div class="form-group">

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
</div>

<!-- SEMESTER -->

<div class="col-lg-3">

<div class="form-group">

<label>Select Semester</label>

<select id="st_semester" class="form-control">

<option value="">Select Semester</option>

</select>

</div>
</div>

<!-- SUBJECT -->

<div class="col-lg-3 mt-3">

<div class="form-group">

<label>Select Subject</label>

<select id="st_subject" class="form-control">

<option value="">Select Subject</option>

</select>

</div>
</div>

<?php } else { ?>

<!-- CLASS -->

<div class="col-12 col-md-6 col-lg-3">

<label>Select Class</label>

<select id="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){
?>

<option value="<?= $c->id ?>"
<?= ($class_id==$c->id)?'selected':'' ?>>

<?= $c->title ?>

</option>

<?php } ?>

</select>

</div>

<!-- SECTION -->

<div class="col-12 col-md-6 col-lg-3">

<label>Select Section</label>

<select id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>

<!-- SUBJECT -->

<div class="col-12 col-md-6 col-lg-3">

<label>Select Subject</label>

<select id="st_subject" class="form-control">

<option value="">Select Subject</option>

</select>

</div>

<?php } ?>

<div class="col-12 mt-2">

<div class="d-flex flex-wrap gap-2 filter-buttons">

<button type="button"
        id="apply_filter"
        class="btn btn-primary">

    Apply

</button>

<a href="study.php"
   class="btn btn-secondary">

    Reset

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- UPLOAD SECTION -->

<div class="container-fluid mt-4">

<div class="row">

<!-- UPLOAD FORM -->

<div class="col-12 col-lg-4 mb-4">

<div class="card shadow h-100">

<div class="card-body">

<h5 class="mb-3 text-center">
    Upload Study Material
</h5>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="subject_id" value="<?= $subject_id ?>">
<input type="hidden" name="session_id" value="<?= $session_id ?>">

<?php if($institute_type=='college'){ ?>

<input type="hidden" name="course_id" value="<?= $course_id ?>">
<input type="hidden" name="branch_id" value="<?= $branch_id ?>">
<input type="hidden" name="semester" value="<?= $semester ?>">

<?php } else { ?>

<input type="hidden" name="class_id" value="<?= $class_id ?>">
<input type="hidden" name="section_id" value="<?= $section_id ?>">

<?php } ?>

<div class="form-group mb-3">

<label>Title</label>

<input type="text"
       name="title"
       class="form-control"
       required>

</div>

<div class="form-group mb-3">

<label>Upload File</label>

<input type="file"
       name="file"
       class="form-control"
       required>

</div>

<div class="row">

<?php render_dynamic_form('study_material'); ?>

</div>

<button type="submit"
        name="upload"
        class="btn btn-primary w-100">

    Upload File

</button>

</form>

</div>

</div>

</div>

<!-- FILE LIST -->

<div class="col-12 col-lg-8">

<div class="card shadow">

<div class="card-body">

<h5 class="mb-3">
    Uploaded Files
</h5>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>S.No</th>
<th>Title</th>
<th>File</th>

<?php

$field_query = mysqli_query($con,"
    SELECT *
    FROM fields
    WHERE institute_id='$institute_id'
    AND form_type='study_material'
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

$where = " WHERE p.type='study_material' ";

if(!empty($subject_id)){
    $where .= " AND p.parent='$subject_id' ";
}

if($institute_type == 'college'){

    if(!empty($course_id)){
        $where .= " AND EXISTS (
            SELECT 1 FROM metadata
            WHERE item_id=p.id
            AND meta_key='course_id'
            AND meta_value='$course_id'
        )";
    }

    if(!empty($branch_id)){
        $where .= " AND EXISTS (
            SELECT 1 FROM metadata
            WHERE item_id=p.id
            AND meta_key='branch_id'
            AND meta_value='$branch_id'
        )";
    }

    if(!empty($semester)){
        $where .= " AND EXISTS (
            SELECT 1 FROM metadata
            WHERE item_id=p.id
            AND meta_key='semester'
            AND meta_value='$semester'
        )";
    }

} else {

    if(!empty($class_id)){
        $where .= " AND EXISTS (
            SELECT 1 FROM metadata
            WHERE item_id=p.id
            AND meta_key='class_id'
            AND meta_value='$class_id'
        )";
    }

    if(!empty($section_id)){
        $where .= " AND EXISTS (
            SELECT 1 FROM metadata
            WHERE item_id=p.id
            AND meta_key='section_id'
            AND meta_value='$section_id'
        )";
    }
}

$query = mysqli_query($con,"
    SELECT p.*
    FROM posts p
    $where
    ORDER BY p.id DESC
");

$count = 0;

while($row = mysqli_fetch_assoc($query)){

    $count++;

    $file_q = mysqli_query($con,"
        SELECT meta_value
        FROM metadata
        WHERE item_id='{$row['id']}'
        AND meta_key='study_material_file'
    ");

    $file = mysqli_fetch_assoc($file_q);

    $file_name = $file['meta_value'] ?? '';

    $file_path = "uploads/assignment/".$file_name;

?>

<tr>

<td><?= $count ?></td>

<td><?= $row['title'] ?></td>

<td><?= $file_name ?></td>

<?php

foreach($field_array as $f){

    $key = $f['field_key'];

    if($key == 'study_material_title'){

        $value = $row['title'];

    } else {

        $meta_q = mysqli_query($con,"
            SELECT meta_value
            FROM metadata
            WHERE item_id='{$row['id']}'
            AND meta_key='$key'
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

    } else {

        echo "<td>$value</td>";
    }
}
?>

<td>

<div class="d-flex flex-wrap gap-2 action-buttons">

<a href="<?= $file_path ?>"
   target="_blank"
   class="btn btn-sm btn-primary">

    View

</a>

<a href="download.php?file=<?= urlencode(trim($file_name)) ?>"
   download
   class="btn btn-sm btn-success">

    Download

</a>

<a href="study.php?delete_id=<?= $row['id'] ?>"
   class="btn btn-sm btn-danger"
   onclick="return confirm('Delete this file?')">

    Delete

</a>

</div>

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

</div>

<?php include('footer.php'); ?>

<script>

$(document).ready(function(){

    $('#apply_filter').on('click', function(){

        let url = "study.php?";

        let course_id  = $('#st_course').val() || '';
        let branch_id  = $('#st_branch').val() || '';
        let semester   = $('#st_semester').val() || '';
        let session    = $('#st_session').val() || '';
        let subject_id = $('#st_subject').val() || '';

        let class_id   = $('#st_class').val() || '';
        let section_id = $('#st_section').val() || '';

        if(course_id)  url += "course_id="+course_id+"&";
        if(branch_id)  url += "branch_id="+branch_id+"&";
        if(semester)   url += "semester="+semester+"&";
        if(session)    url += "session="+session+"&";
        if(subject_id) url += "subject_id="+subject_id+"&";

        if(class_id)   url += "class="+class_id+"&";
        if(section_id) url += "section="+section_id+"&";

        url = url.slice(0, -1);

        if(subject_id == ''){

            alert('Please Select Subject');
            return false;
        }

        window.location.href = url;
    });

});

</script>

<script>

$(document).ready(function(){

function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();

    if(course_id && branch_id && semester && session){

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

                $('#st_subject').html(res.options);

                <?php if($subject_id!=''){ ?>
                $('#st_subject').val('<?= $subject_id ?>');
                <?php } ?>
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

        <?php if($branch_id!=''){ ?>
        $('#st_branch').val('<?= $branch_id ?>');
        <?php } ?>

    },'json');

    $.post('ajax.php',{

        action:'get_semester',
        course_id:course_id

    },function(res){

        $('#st_semester').html(res.options);

        <?php if($semester!=''){ ?>
        $('#st_semester').val('<?= $semester ?>');
        <?php } ?>

    },'json');

});

$('#st_branch,#st_semester,#st_session').on('change',function(){

    loadSubject();

});

$('#st_class').on('change',function(){

    let class_id = $(this).val();

    $('#st_subject').html('<option value="">Loading...</option>');

    $.post('ajax.php',{

        action:'get_sections',
        class_id:class_id

    },function(res){

        $('#st_section').html(res.options);

    },'json');

    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',

        data:{
            action:'get_subject',
            class_id:class_id
        },

        success:function(res){

            $('#st_subject').html(res.options);
        }
    });

});

$('#st_section').on('change',function(){

    let class_id = $('#st_class').val();
    let section_id = $(this).val();

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

            $('#st_subject').html(res.options);

            <?php if($subject_id!=''){ ?>
            $('#st_subject').val('<?= $subject_id ?>');
            <?php } ?>
        }
    });

});

<?php if($class_id!=''){ ?>

$.post('ajax.php',{

    action:'get_sections',
    class_id:'<?= $class_id ?>'

},function(res){

    $('#st_section').html(res.options);

    $('#st_section').val('<?= $section_id ?>');

},'json');

<?php } ?>

<?php if($institute_type=='college' && $course_id!=''){ ?>

$('#st_course').trigger('change');

setTimeout(function(){

    loadSubject();

},800);

<?php } ?>

});

</script>