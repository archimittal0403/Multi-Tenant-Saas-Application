<?php include('includes/auth.php');
checkRole('student'); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>

<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$user_id = $_SESSION['user_id'];

// ================= USER DATA =================

if($institute_type=='college'){

    $course_data  = get_usermeta($user_id,'course_name');
    $branch_data  = get_usermeta($user_id,'branch_name');
    $session=get_usermeta($user_id,'session');
     $year = date('Y');
    $next_year = $year + 1;

    $session_data = $year . '-' . $next_year;


}else{

    $course_data  = get_usermeta($user_id,'st_class');
    $branch_data  = '';
    $session=get_usermeta($user_id,'session');
      $year = date('Y');
    $next_year = $year + 1;

    $session_data = $year . '-' . $next_year;
}

$course_id  = $course_data ?? '';
$branch_id  = $branch_data ?? '';
$session_id = $session_data ?? '';

?>

<?php

$subject_id = $_GET['subject_id'] ?? '';

$selected_session = $session_id;

// ================= UPLOAD ASSIGNMENT =================

if(isset($_POST['upload'])){

    $subject_id = $_POST['subject_id'] ?? '';

    if(empty($_FILES['file']['name'])){

        echo "<script>alert('Please select file')</script>";

    }else{

        $file = $_FILES['file']['name'];
        $tmp  = $_FILES['file']['tmp_name'];

        $upload_dir = __DIR__ . '/uploads/assignment/';

        if(!file_exists($upload_dir)){
            mkdir($upload_dir,0777,true);
        }

        $file_name = time().'_'.$file;

        if(move_uploaded_file($tmp,$upload_dir.$file_name)){

            mysqli_query($con,"
                INSERT INTO student_assignments
                (user_id,subject_id,session,file,institute_id)

                VALUES(
                    '$user_id',
                    '$subject_id',
                    '$session',
                    '$file_name',
                    '$institute_id'
                )
            ");

            echo "<script>alert('Uploaded Successfully')</script>";

        }else{

            echo "<script>alert('Upload Failed')</script>";
        }
    }
}

// ================= FILE DATA =================

$assignment_title = get_metadata($subject_id,'assignment_title');

$assignment_description = get_metadata($subject_id,'assignment_description');

$assignment_last_date = get_metadata($subject_id,'assignment_last_date');

$youtube_link = get_metadata($subject_id,'youtube_link');

$academic_session = get_metadata($subject_id,'session');

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Student Assignment</title>

</head>

<body>

<div class="container mt-3">

    <h2>Assignment</h2>

    <h6>Filteration</h6>

    <div class="row">

        <?php if($institute_type=='college'){ ?>

        <!-- SEMESTER -->

        <div class="col-lg-4">

            <div class="form-group">

                <label>Select Semester:</label>

                <select id="st_semester" class="form-control">

                    <option value="">Select Semester</option>

                </select>

            </div>

        </div>

        <!-- SUBJECT -->

        <div class="col-lg-4">

            <div class="form-group">

                <label>Select Subject:</label>

                <select id="st_subject" class="form-control">

                    <option value="">Select Subject</option>

                </select>

            </div>

        </div>

        <?php } else { ?>

        <!-- SCHOOL SUBJECT -->

        <div class="col-lg-4">

            <div class="form-group">

                <label>Select Subject:</label>

                <select id="st_subject" class="form-control">

                    <option value="">Select Subject</option>

                </select>

            </div>

        </div>

        <?php } ?>

        <div class="col-lg-2 d-flex align-items-end">

            <button type="button"
                    id="apply_filter"
                    class="btn btn-danger w-100">

                Apply

            </button>

        </div>

    </div>

</div>

<!-- ================= FILE TABLE ================= -->

<div class="container mt-5">

    <div class="row">

        <div class="col-lg-12">

            <div class="card shadow p-4"
                 style="border-radius:15px;">

                <h5 class="mb-3">Assignment Files</h5>

                <table class="table table-hover table-bordered">

                    <thead class="table-dark">

                    <tr>

                        <th>Sno</th>

                        <th>Title</th>

                        <th>File</th>

                        <th>Action</th>

                    </tr>

                    </thead>

                <tbody>

<?php

if(!empty($assignment_title)){

    for($i=0; $i<count($assignment_title); $i++){

        $session =
        $academic_session[$i]->meta_value ?? '';

        if($session == $selected_session){

            $title =
            $assignment_title[$i]->meta_value ?? '';

            $description =
            $assignment_description[$i]->meta_value ?? '';

            $last_date =
            $assignment_last_date[$i]->meta_value ?? '';

            $youtube =
            $youtube_link[$i]->meta_value ?? '';

?>

<tr>

<td><?php echo ($i+1); ?></td>

<td>

<b><?php echo $title; ?></b>

<br>

<?php echo $description; ?>

<br>

<b>Last Date:</b>

<?php echo $last_date; ?>

</td>

<td>

<?php if(!empty($youtube)){ ?>

<a href="<?php echo $youtube; ?>"
   target="_blank"
   class="btn btn-sm btn-primary">

    Watch Video

</a>

<?php } ?>

</td>

<td>

<?php

$q_check = mysqli_query($con,"
SELECT *
FROM student_assignments
WHERE user_id='$user_id'
AND subject_id='$subject_id'
AND session='$selected_session'
");

if(mysqli_num_rows($q_check)==0){

?>

<form method="post"
      enctype="multipart/form-data">

<input type="hidden"
       name="subject_id"
       value="<?php echo $subject_id; ?>">

<input type="file"
       name="file"
       required>

<button type="submit"
        name="upload"
        class="btn btn-success btn-sm">

Upload

</button>

</form>

<?php } else { ?>

<span class="badge bg-success">

Uploaded

</span>

<?php } ?>

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

<?php include('footer.php'); ?>

<script>

// ================= LOAD SUBJECT FOR COLLEGE =================

function loadSubject(){

    let semester = $('#st_semester').val();

    $.ajax({

        url: 'ajax.php',

        type: 'POST',

        dataType: 'json',

        data: {

            action: 'get_subject',

            course_id: "<?php echo $course_id; ?>",

            branch_id: "<?php echo $branch_id; ?>",

            session: "<?php echo $session_id; ?>",

            semester: semester
        },

        success: function(res){

            $('#st_subject').html(res.options);
        }
    });
}

$(document).ready(function(){

    <?php if($institute_type=='college'){ ?>

    // ================= LOAD SEMESTER =================

    $.post('ajax.php',{

        action:'get_semester',

        course_id:"<?php echo $course_id; ?>"

    },function(res){

        $('#st_semester').html(res.options);

    },'json');

    <?php } ?>



    <?php if($institute_type=='school'){ ?>

    // ================= LOAD SUBJECT FOR SCHOOL =================

    $.ajax({

        url:'ajax.php',

        type:'POST',

        dataType:'json',

        data:{

            action:'get_school_subject',

            class_id:"<?php echo $course_id; ?>",

            session:"<?php echo $session_id; ?>"
        },

        success:function(res){

            $('#st_subject').html(res.options);
        }
    });

    <?php } ?>

});


// ================= SEMESTER CHANGE =================

$('#st_semester').on('change', function(){

    loadSubject();
});


// ================= APPLY FILTER =================

$('#apply_filter').on('click',function(){

    <?php if($institute_type=='college'){ ?>

    let semester = $('#st_semester').val();

    let subject_id = $('#st_subject').val();

    let url = "student_assignment.php?semester="+semester+
              "&subject_id="+subject_id;

    <?php } else { ?>

    let subject_id = $('#st_subject').val();

    let url = "student_assignment.php?subject_id="+subject_id;

    <?php } ?>

    window.location.href = url;

});

</script>

</body>
</html>