<?php
include('includes/auth.php');
checkRole('admin');

include('includes/config.php');
include('includes/functions.php');
include('includes/dynamic-form.php');

include('header.php');
include('sidebar.php');

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

/* ================= GET FILTER ================= */
$course_id  = $_GET['course_id'] ?? '';
$branch_id  = $_GET['branch_id'] ?? '';
$session_id = $_GET['session'] ?? '';

$class_id   = $_GET['class'] ?? '';
$section_id = $_GET['section'] ?? '';

/* ================= DELETE ================= */
if(isset($_GET['delete_id'])){

    $delete_id = $_GET['delete_id'];

    mysqli_query($con,"DELETE FROM notices WHERE id='$delete_id'");
    mysqli_query($con,"DELETE FROM metadata WHERE item_id='$delete_id'");

    echo "<script>
        alert('Deleted Successfully');
        window.location='admin_notice.php';
    </script>";
}

/* ================= INSERT NOTICE ================= */
if(isset($_POST['submit_notice'])){

    $title = $_POST['title'];
    $description = $_POST['description'];

    $file_name = '';

    if(!empty($_FILES['notice_file']['name'])){
        $file = $_FILES['notice_file']['name'];
        $tmp  = $_FILES['notice_file']['tmp_name'];

        $file_name = time().'_'.$file;

        $upload_dir = __DIR__.'/uploads/annoucement/';
        if(!is_dir($upload_dir)) mkdir($upload_dir,0777,true);

        move_uploaded_file($tmp,$upload_dir.$file_name);
    }

    mysqli_query($con,"
        INSERT INTO notices
        (title,description,file,course,branch,session,class,section)
        VALUES
        ('$title','$description','$file_name','$course_id','$branch_id','$session_id','$class_id','$section_id')
    ");

    $notice_id = mysqli_insert_id($con);

    /* ================= DYNAMIC FIELDS (SAME AS STUDY) ================= */
    foreach($_POST as $key=>$value){

        $skip = [
            'submit_notice','title','description','notice_file',
            'course_id','branch_id','session_id','class_id','section_id'
        ];

        if(in_array($key,$skip)) continue;
        if($value == '') continue;

        $value = mysqli_real_escape_string($con,$value);

        mysqli_query($con,"
            INSERT INTO metadata(item_id,meta_key,meta_value)
            VALUES('$notice_id','$key','$value')
        ");
    }

    echo "<script>
        alert('Notice Created Successfully');
        window.location='admin_notice.php';
    </script>";
}
?>
<style>
    <style>
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    h3 {
        font-size: 18px;
    }

    .btn {
        width: 100%;
    }
}
</style>
</style>
<!-- ================= PAGE UI ================= -->
<div class="container mt-4">

<h3>📢 Admin Notices</h3>

<!-- ================= FILTER ================= -->
<div class="card mb-3">
<div class="card-body">

<div class="row">

<?php if($institute_type == 'college'){ ?>

<div class="col-12 col-sm-6 col-md-4 col-lg-3">
<label>Course</label>
<select id="st_course" class="form-control">
<option value="">Select</option>
<?php foreach(get_posts(['type'=>'course']) as $c){ ?>
<option value="<?= $c->id ?>"
<?= ($course_id == $c->id) ? 'selected' : '' ?>><?= $c->title ?></option>
<?php } ?>
</select>
</div>

<div class="col-12 col-sm-6 col-md-4 col-lg-3">
<label>Branch</label>
<select id="st_branch" class="form-control"></select>
</div>

<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <label for="">Session</label>
<select id="st_session" class="form-control">
    <option value="">Select</option>
    <?php foreach(get_posts(['type'=>'session']) as $s){ ?>
       <option value="<?= $s->id ?>"
<?= ($session_id == $s->id) ? 'selected' : '' ?>><?= $s->title ?></option>
    <?php } ?>
</select>
</div>

<?php } else { ?>

<div class="col-12 col-sm-6 col-md-4 col-lg-3">

<label>Select Class</label>

<select id="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){

?>

<option value="<?= $c->id ?>"
<?= ($class_id == $c->id) ? 'selected' : '' ?>>

<?= $c->title ?>

</option>

<?php } ?>

</select>

</div>

<!-- SECTION -->
<div class="col-12 col-sm-6 col-md-4 col-lg-3">

<label>Select Section</label>

<select id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>

<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <label for="">Academic Session</label>
 <input type="text"
       id="academic_session"
       name="session_id"
       value="<?= $session_id ?>"
       placeholder="Enter Academic Session"
       class="form-control">
 </div>
<?php } ?>

<div class="col-lg-12 mt-3">

    <button type="button" id="apply_filter" class="btn btn-primary">
        Apply Filter
    </button>

    <a href="admin_notice.php" class="btn btn-secondary">
        Reset
    </a>

<button type="button"
        id="add_notice_btn"
        class="btn btn-success">
    + Add Notice
</button>
</div>

</div>

</div>
</div>



<!-- ================= LIST ================= -->
<div class="card">
<div class="card-body">

<div class="table-responsive">
    <table class="table table-bordered table-striped">

<tr>
<th>#</th>
<th>Title</th>
<th>Description</th>
<th>File</th>
<?php
// DYNAMIC HEADERS
$field_query = mysqli_query($con,"
    SELECT * FROM fields 
    WHERE institute_id='$institute_id'
    AND form_type='notices'
");

$field_array = [];

while($f = mysqli_fetch_assoc($field_query)){
    $field_array[] = $f;
    echo "<th>{$f['field_name']}</th>";
}
?>
<th>Action</th>
</tr>

<?php

$where = "WHERE institute_id='$institute_id'";

if($course_id) $where .= " AND course='$course_id'";
if($branch_id) $where .= " AND branch='$branch_id'";
if($session_id) $where .= " AND session='$session_id'";
if($class_id) $where .= " AND class='$class_id'";
if($section_id) $where .= " AND section='$section_id'";

$q = mysqli_query($con,"SELECT * FROM notices $where ORDER BY id DESC");

$i=1;
?>
<?php
while($row = mysqli_fetch_assoc($q)){
?>
<tr>
    <td><?= $i ?></td>
    <td><?= $row['title'] ?></td>
    <td><?= $row['description'] ?></td>
    <td><?= $row['file'] ?></td>

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

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            echo "<td><a href='$value' target='_blank'>Open Link</a></td>";
        } else {
            echo "<td>$value</td>";
        }
    }
    ?>

    <td>
        <a href="admin_notice.php?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">
            Delete
        </a>
    </td>

</tr>
<?php
$i++;
}
?>

</table>
</div>
</div>
</div>

</div>


<?php include('footer.php'); ?>

<!-- ================= JS ================= -->

<script>
$(document).ready(function(){

    // ================= COURSE CHANGE =================
    $('#st_course').on('change',function(){

        let course_id = $(this).val();

        $.post('ajax.php',{
            action:'get_branch',
            course_id:course_id
        },function(res){

            $('#st_branch').html(res.options);

        },'json');

    });


    // ================= CLASS CHANGE =================
    $('#st_class').on('change',function(){

        let class_id = $(this).val();

        $.post('ajax.php',{
            action:'get_sections',
            class_id:class_id
        },function(res){

            if(res.options){

                $('#st_section').html(res.options);

            }else{

                $('#st_section').html('<option value="">No Section</option>');

            }

        },'json');

    });


    // ================= AUTO LOAD SECTION =================
    if($('#st_class').val() != ''){

        $.post('ajax.php',{
            action:'get_sections',
            class_id:$('#st_class').val()
        },function(res){

            $('#st_section').html(res.options);

            // selected section set
            $('#st_section').val("<?= $section_id ?>");

        },'json');

    }


    // ================= AUTO LOAD BRANCH =================
    if($('#st_course').val() != ''){

        $.post('ajax.php',{
            action:'get_branch',
            course_id:$('#st_course').val()
        },function(res){

            $('#st_branch').html(res.options);

            // selected branch set
            $('#st_branch').val("<?= $branch_id ?>");

        },'json');

    }


    // ================= APPLY FILTER =================
    $('#apply_filter').on('click',function(){

        let url = "admin_notice.php?";

        // COLLEGE
        if($('#st_course').val()){
            url += "course_id=" + $('#st_course').val() + "&";
        }

        if($('#st_branch').val()){
            url += "branch_id=" + $('#st_branch').val() + "&";
        }

        if($('#st_session').val()){
            url += "session=" + $('#st_session').val() + "&";
        }

        // SCHOOL
        if($('#st_class').val()){
            url += "class=" + $('#st_class').val() + "&";
        }

        if($('#st_section').val()){
            url += "section=" + $('#st_section').val() + "&";
        }

       if($('#academic_session').val()){
    url += "session=" + $('#academic_session').val() + "&";
}

        url = url.replace(/&$/, '');

        window.location.href = url;

    });


    // ================= ADD NOTICE =================
    $('#add_notice_btn').on('click',function(){

        let url = "add_notice.php?";

        // COLLEGE
        if($('#st_course').val()){
            url += "course_id=" + $('#st_course').val() + "&";
        }

        if($('#st_branch').val()){
            url += "branch_id=" + $('#st_branch').val() + "&";
        }

        if($('#st_session').val()){
            url += "session=" + $('#st_session').val() + "&";
        }

        // SCHOOL
        if($('#st_class').val()){
            url += "class=" + $('#st_class').val() + "&";
        }

        if($('#st_section').val()){
            url += "section=" + $('#st_section').val() + "&";
        }

     if($('#academic_session').val()){
    url += "session=" + $('#academic_session').val() + "&";
}

        url = url.replace(/&$/, '');

        console.log(url);

        window.location.href = url;

    });

});
</script>