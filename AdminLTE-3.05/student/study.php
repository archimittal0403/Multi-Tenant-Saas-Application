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

if($institute_type=='college'){

    $course_data  = get_usermeta($user_id,'course_name');
    $branch_data  = get_usermeta($user_id,'branch_name');
     $year = date('Y');
    $next_year = $year + 1;

    $session_data = $year . '-' . $next_year;

}else{

    $class_data  = get_usermeta($user_id,'st_class');
    $session_data = get_usermeta($user_id,'session');
    $get_session=mysqli_query($con,"SELECT title FROM `posts` WHERE id='$session_data'");
    $row_session=mysqli_fetch_assoc($get_session);
    $session=$row_session['title'];
}
$course_id  = $course_data ?? '';
$branch_id  = $branch_data ?? '';
$session_id = $session_data ?? '';

$subject_id = $_GET['subject_id'] ?? '';

$selected_session = $session_id;

// ================= FILE DATA =================

$get_file_session = get_metadata($subject_id,'study_material_session');

$get_title = get_metadata($subject_id,'study_material_title');
$get_file  = get_metadata($subject_id,'study_material_file');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Study Material</title>
</head>

<body>

<div class="container mt-3">

    <h2>Study Material</h2>
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

            <div class="card shadow p-4" style="border-radius:15px;">

                <h5 class="mb-3">Uploaded Files</h5>

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

                    if(!empty($get_file)){

                        for($i=0; $i<count($get_file); $i++){

                            $file_session =
                            $get_file_session[$i]->meta_value ?? '';

                            if($file_session == $selected_session){

                                $file_name =
                                $get_file[$i]->meta_value ?? '';

                                $file_title =
                                $get_title[$i]->meta_value ?? '';

                                $file_path =
                                "../admin/uploads/assignment/".$file_name;

                                echo "

                                <tr>

                                    <td>".($i+1)."</td>

                                    <td>$file_title</td>

                                    <td>$file_name</td>

                                    <td>

                                        <a href='$file_path'
                                           target='_blank'
                                           class='btn btn-sm btn-primary'>

                                           View

                                        </a>

                                        <a href='download.php?file=".urlencode($file_name)."'
                                           class='btn btn-sm btn-danger'>

                                           Download

                                        </a>

                                    </td>

                                </tr>

                                ";
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

        course_id: "<?php echo $course_id; ?>"

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

            class_id:"<?php echo $class_data; ?>",
             session: "<?php echo $session; ?>"
        },

       success:function(res){

    console.log(res);

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

    let semester  = $('#st_semester').val();

    let subject_id = $('#st_subject').val();

    let url = "study.php?semester="+semester+
              "&subject_id="+subject_id;

    <?php } else { ?>

    let subject_id = $('#st_subject').val();

    let url = "study.php?subject_id="+subject_id;

    <?php } ?>

    window.location.href = url;

});

</script>

