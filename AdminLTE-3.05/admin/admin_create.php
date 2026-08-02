<?php include('includes/auth.php'); 
checkRole('admin'); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php
$institute_id = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

/* ================= INSERT EXAM ================= */
if(isset($_POST['create_exam'])){

    $course_id     = $_POST['st_course'] ?? 0;
    $branch_id     = $_POST['branch_id'] ?? 0;
    $session_id    = $_POST['session_id'] ?? '';

    $exam_type_id  = $_POST['exam_type_id'];
    $start_date    = $_POST['start_date'];
    $end_date      = $_POST['end_date'];
    $status        = $_POST['status'];

    if($institute_type == 'college'){
        $class_id = 0;
        $section_id = 0;
        $semester_id = $_POST['st_semester'] ?? 0;
    } else {
        $class_id = $_POST['st_class'] ?? 0;
        $section_id = $_POST['st_section'] ?? 0;
        $semester_id = 0;
    }

    $check = mysqli_query($con,"SELECT * FROM create_exam 
        WHERE course_id='$course_id'
        AND branch_id='$branch_id'
        AND semester_id='$semester_id'
        AND session_id='$session_id'
        AND exam_type_id='$exam_type_id'
        AND class_id='$class_id'
        AND section_id='$section_id'
    ");

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Already exists!')</script>";
    } else {

        mysqli_query($con,"INSERT INTO create_exam 
        (institute_id, course_id, branch_id, semester_id, session_id, exam_type_id, start_date, end_date, status, class_id, section_id)
        VALUES 
        ('$institute_id','$course_id','$branch_id','$semester_id','$session_id','$exam_type_id','$start_date','$end_date','$status','$class_id','$section_id')
        ");

        echo "<script>alert('Exam Created Successfully'); window.location='admin_create.php';</script>";
    }
}
?>

<?php
if(isset($_GET['delete_id'])){

    $delete_id = $_GET['delete_id'];

    mysqli_query($con,"DELETE FROM create_exam WHERE id='$delete_id' AND institute_id='$institute_id'");

    echo "<script>
        alert('Deleted Successfully');
        window.location='admin_create.php';
    </script>";
}
?>
<style>
.table-responsive table{
    min-width: 900px;
}
</style>

<div class="container-fluid mt-3">

<div class="row">

    <!-- ================= LEFT FORM ================= -->
    <div class="col-lg-4 col-md-12 mb-3">

        <div class="card shadow-sm">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Create Exam</h5>
                <a href="add_exam.php" class="btn btn-warning btn-sm">Add Exam</a>
            </div>

            <div class="card-body">
                <form method="post">

                <?php if($institute_type=='college'){ ?>

                    <div class="form-group">
                        <label>Course</label>
                        <select name="st_course" id="st_course" class="form-control">
                            <option value="">Select</option>
                            <?php
                            $courses = get_posts(['type'=>'course','institute_id'=>$institute_id]);
                            foreach($courses as $c){
                                echo "<option value='$c->id'>$c->title</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Branch</label>
                        <select name="branch_id" id="st_branch" class="form-control"></select>
                    </div>

                    <div class="form-group">
                        <label>Semester</label>
                        <select name="st_semester" id="st_semester" class="form-control"></select>
                    </div>

                    <div class="form-group">
                        <label>Session</label>
                        <select name="session_id" id="st_session" class="form-control">
                            <?php
                            $sessions = get_posts(['type'=>'session','institute_id'=>$institute_id]);
                            foreach($sessions as $s){
                                echo "<option value='$s->id'>$s->title</option>";
                            }
                            ?>
                        </select>
                    </div>

                <?php } else { ?>

                    <div class="form-group">
                        <label>Class</label>
                        <select name="st_class" id="st_class" class="form-control">
                            <option value="">Select</option>
                            <?php
                            $classes = get_posts(['type'=>'class']);
                            foreach($classes as $c){
                                echo "<option value='$c->id'>$c->title</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Section</label>
                        <select name="st_section" id="st_section" class="form-control"></select>
                    </div>

                    <div class="form-group">
                        <label>Session</label>
                        <input type="text" name="session_id" class="form-control">
                    </div>

                <?php } ?>

                <div class="form-group">
                    <label>Exam Type</label>
                    <select name="exam_type_id" class="form-control">
                        <option value="">Select Exam</option>
                        <?php
                        $q = mysqli_query($con,"SELECT * FROM exam_type WHERE institute_id='$institute_id'");
                        while($r=mysqli_fetch_assoc($q)){
                            echo "<option value='{$r['id']}'>{$r['exam_type']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>

                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" name="create_exam" class="btn btn-success btn-block">
                    Create Exam
                </button>

                </form>
            </div>

        </div>
    </div>

    <!-- ================= RIGHT TABLE ================= -->
    <div class="col-lg-8 col-md-12">

        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Exam List</h5>
            </div>

            <div class="card-body p-0">
            <div class="table-responsive">

            <table class="table table-bordered table-hover text-center mb-0">

                <thead>
                <tr>
                    <th>#</th>
                    <?php if($institute_type=='college'){ ?>
                        <th>Course</th>
                        <th>Branch</th>
                    <?php } else { ?>
                        <th>Class</th>
                        <th>Section</th>
                    <?php } ?>
                           <?php if($institute_type=='college'){ ?>
                    <th>Semester</th>
                    <?php } ?>
                    <th>Session</th>
                    <th>Exam</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

                <?php
                $i=1;
                $query=mysqli_query($con,"SELECT * FROM create_exam WHERE institute_id='$institute_id' ORDER BY id DESC");$query = mysqli_query($con,"
SELECT ce.*,

-- Course Name
c.title AS course_name,

-- Branch Name
b.title AS branch_name,

-- Semester Name
s.title AS semester_name,

-- Class Name
cl.title AS class_name,

-- Section Name
se.title AS section_name,

-- Session Name
ss.title AS session_name,

-- Exam Type Name
et.exam_type AS exam_type_name

FROM create_exam ce

LEFT JOIN posts c ON ce.course_id = c.id AND c.type='course'
LEFT JOIN posts b ON ce.branch_id = b.id AND b.type='branch'
LEFT JOIN posts s ON ce.semester_id = s.id AND s.type='semester'
LEFT JOIN posts cl ON ce.class_id = cl.id AND cl.type='class'
LEFT JOIN posts se ON ce.section_id = se.id AND se.type='section'
LEFT JOIN posts ss ON ce.session_id = ss.id AND ss.type='session'

LEFT JOIN exam_type et ON ce.exam_type_id = et.id

WHERE ce.institute_id='$institute_id'
ORDER BY ce.id DESC
");
                while($row=mysqli_fetch_assoc($query)){
                ?>

                <tr>
                    <td><?= $i++ ?></td>

                    <?php if($institute_type=='college'){ ?>
                      <td><?= $row['course_name'] ?></td>
                        <td><?= $row['branch_name'] ?></td>
                    <?php } else { ?>
                        <td><?= $row['class_name'] ?></td>
                        <td><?= $row['section_name'] ?></td>
                    <?php } ?>
  <?php if($institute_type=='college'){ ?>
                    <td><?= $row['semester_id'] ?></td>
                    <?php } ?>
                  <?php if($institute_type=='college'){ ?>
                    <td><?= $row['session_name'] ?></td>
                    <?php }  else{?>
                    <td><?= $row['session_id'] ?></td>
                    <?php } ?>
                    <td><?= $row['exam_type_name'] ?></td>

                    <td><?= $row['start_date'] ?> - <?= $row['end_date'] ?></td>

                    <td>
                        <?= $row['status']=='active'
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>' ?>
                    </td>

                    <td>
                        <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
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

// ================= LOAD SUBJECT =================
function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();

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
}


// ================= COURSE CHANGE =================
$('#st_course').on('change', function(){

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

    // 🔥 IMPORTANT: reset subject properly
    $('#st_subject').html('<option>Select Subject</option>');
});
$('#st_class').on('change',function(){

    let class_id = $(this).val();

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


// ================= AUTO SUBJECT LOAD =================
$('#st_branch, #st_semester, #st_session').on('change', function(){
    loadSubject();
});

}); // document ready end

</script>

