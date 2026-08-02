<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>

<?php
$institute_id=$_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
?>

<?php
if(isset($_POST['insert'])){

    $exam_id     = $_POST['st_exam'] ?? 0;
    $course_id   = $_POST['st_course'] ?? 0;
    $branch_id   = $_POST['st_branch'] ?? 0;
    $semester_id = $_POST['st_semester'] ?? 0;
    $session_id  = $_POST['st_session'] ?? 0;

    $class_id    = $_POST['st_class'] ?? 0;
    $section_id  = $_POST['st_section'] ?? 0;

    $subject_id  = $_POST['st_subject'];
    $exam_date   = $_POST['exam_date'];
    $duration    = $_POST['duration'];
    $start_time  = $_POST['start_time'];
    $end_time    = $_POST['end_time'];

    $check = mysqli_query($con,"
        SELECT id FROM exam_datesheet 
        WHERE exam_id='$exam_id'
        AND subject_id='$subject_id'
        AND exam_date='$exam_date'
        AND institute_id='$institute_id'
    ");

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Already Added')</script>";
    } else {

        mysqli_query($con,"
            INSERT INTO exam_datesheet 
            (exam_id, course_id, branch_id, semester_id, session_id, class_id, section_id, subject_id, exam_date, start_time, end_time, duration, institute_id)
            VALUES
            ('$exam_id','$course_id','$branch_id','$semester_id','$session_id','$class_id','$section_id','$subject_id','$exam_date','$start_time','$end_time','$duration','$institute_id')
        ");

        echo "<script>
        window.location.href='exam_datesheet.php?exam_id=$exam_id';
        </script>";
    }
}
?>
<?php
if(isset($_POST['update_row'])){
    $update_id=$_POST['update_id'];
    $update_exam=$_POST['exam_date'];
    $update_start=$_POST['start_time'];
    $update_end=$_POST['end_time'];
    $update_duration=$_POST['duration'];
    // update the dtaa 
    $update_query=mysqli_query($con,"UPDATE `exam_datesheet` SET exam_date='$update_exam',duration='$update_duration', start_time='$update_start', end_time='$update_end' WHERE id='$update_id'");
    if($update_query){
        echo "<script>alert('Updation has been done Succesfully')</script>";
        echo "<script>window.open('dashboard.php','_self')</script>";
    }
}
?>
<?php
if(isset($_GET['delete_id'])){
    $delete_id=$_GET['delete_id'];
    $delete_query=mysqli_query($con,"DELETE  FROM `exam_datesheet` WHERE id='$delete_id'");
    if($delete_query){
         echo "<script>alert('Deletion has been done Succesfully')</script>";
        echo "<script>window.open('dashboard.php','_self')</script>";
    }
}
?>
<style>
.card{
    overflow:hidden;
}

.table-responsive{
    overflow-x:auto;
}

.form-control{
    width:100%;
}

@media(max-width:768px){

    h1{
        font-size:24px;
    }

    .btn{
        width:100%;
    }

    .table th,
    .table td{
        white-space:nowrap;
    }
}
</style>
<div class="content-header">
  
  <div class="container-fluid">
    <div class="row">

      <div class="col-12">
        <h1 style="color:#0b3d91;" class=" mb-4 text-center"><u>Create Exam Datesheet:</u></h1>
      </div>
</div>
 <div class="card shadow mb-1">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">🔍 Add Exam</h5>
        </div>
        <div class="card-body">
            <form  method="post">

   <!-- Course -->
    <div class="row">
        <?php
        if($institute_type=='college'){?>
<div class="col-lg-4 col-md-6 col-12">
      <div class="form-group">
        <label>Select Course:</label>
        <select id="st_course" name="st_course" class="form-control">
          <option value="">Select Course</option>

          <?php
          $args = array('type'=>'course');
          $s_course_id = get_posts($args);

          foreach($s_course_id as $s_course){
              echo '<option value="'.$s_course->id.'">'.$s_course->title.'</option>';
          }
          ?>

        </select>
      </div>
    </div>

    <!-- Branch -->
  <div class="col-lg-4 col-md-6 col-12">

      <div class="form-group">
        <label>Select Branch:</label>
        <select id="st_branch" name="st_branch" class="form-control">
          <option value="">Select Branch</option>
        </select>
      </div>
    </div>

    <!-- Semester -->
<div class="col-lg-4 col-md-6 col-12">
      <div class="form-group">
        <label>Select Session:</label>
        <select id="st_session" name="st_session" class="form-control">
          <option value="">Select Session</option>
          <?php
  
    $sessions = get_posts([
        'type'=>'session',
        'institute_id'=>$institute_id
    ]);

    $options = '<option value="">Select Academic Session</option>';

    if(!empty($sessions)){
        foreach($sessions as $session){
           echo '<option value="'.$session->id.'">'.$session->title.'</option>';
        }
    }

          ?> 
        </select>
      </div>
    </div>
    <?php } else {?>
       <div class="col-lg-4 col-md-6 col-12">
<div class="form-group">

<label>Select Class</label>

<select id="st_class" name="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){

?>

<option value="<?= $c->id ?>">

<?= $c->title ?>

</option>

<?php } ?>

</select>

</div>
</div>
<div class="col-lg-4 col-md-6 col-12">
      <div class="form-group">

<label>Select Section</label>

<select id="st_section" name="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>
</div>
<div class="col-lg-4 col-md-6 col-12">
   <div class="form-group">
    <label for="">Academic Session</label>
 <input type="text"
       id="academic_session"
       name="session_id"

       placeholder="Enter Academic Session"
       class="form-control">
 </div>
</div>
 <?php } ?>
</div>

<div class="row">
    <?php
    if($institute_type=='college'){?>
    
   <div class="col-lg-4 col-md-6 col-12">
      <div class="form-group">
        <label>Select Semester:</label>
        <select id="st_semester" name="st_semester" class="form-control">
          <option value="">Select Semester</option>
        </select>
      </div>
    </div>
    <?php } ?>
     <div class="col-lg-4 col-md-6 col-12">
      <!-- Exam Type -->
                <div class="form-group">
                    <label>Exam Type</label>
                    <select name="st_exam" id="st_exam" class="form-control" required>
                        <option value="">Select Exam Type</option>
                        <?php
                        $select_exam=mysqli_query($con,"SELECT id,exam_type FROM `exam_type` WHERE institute_id='$institute_id'");
                        while($row=mysqli_fetch_assoc($select_exam)){
                            $type_id=$row['id'];
                            $exam_name=$row['exam_type'];
                            echo '<option value="'.$type_id.'">'.$exam_name.'</option>';
                        }
                        ?>
                    </select>
                </div>
    </div>
  <div class="col-lg-4 col-md-6 col-12">
      <div class="form-group">
        <label>Select Subject:</label>
        <select id="st_subject" name="st_subject" class="form-control">
          <option value="">Select Subjects</option>
        </select>
      </div>
    </div>
</div>
<div class="row">

<div class="col-lg-3 col-md-6 col-12">
        <div class="form-group">

            <label>Select Exam Date:</label>

            <select id="exam_date" name="exam_date" class="form-control">

                <option value="">Select Exam Date</option>

                <?php

                $select_date = mysqli_query($con,"
                    SELECT start_date,end_date 
                    FROM create_exam 
                    WHERE institute_id='$institute_id'
                ");

                while($row = mysqli_fetch_assoc($select_date)){

                    $start_date = $row['start_date'];
                    $end_date   = $row['end_date'];

                    $start = strtotime($start_date);
                    $end   = strtotime($end_date);

                    while($start <= $end){

                        $date = date('Y-m-d',$start);

                        echo '<option value="'.$date.'">'.$date.'</option>';

                        $start = strtotime("+1 day",$start);
                    }
                }

                ?>

            </select>

        </div>
    </div>
<div class="col-lg-3 col-md-6 col-12">
      <div class="form-group">
        <label>Exam Duration:</label>
     <input type="number"
       name="duration"
       class="form-control"
       placeholder="Minutes">
       
      </div>
    </div>
<div class="col-lg-3 col-md-6 col-12">
      <div class="form-group">
        <label>Start Time:</label>
        <input type="time" name="start_time" class="form-control">
       
      </div>
    </div>
<div class="col-lg-3 col-md-6 col-12">
      <div class="form-group">
        <label>End Time:</label>
        <input type="time" name="end_time" class="form-control">
       
      </div>
    </div>
</div>

<?php
$course_id   = $_POST['st_course']   ?? '';
$branch_id   = $_POST['st_branch']   ?? '';
$semester_id = $_POST['st_semester'] ?? '';
$session_id  = $_POST['st_session']  ?? '';
$exam_id     = $_POST['st_exam']     ?? '';

$class_id    = $_POST['st_class']    ?? '';
$section_id  = $_POST['st_section']  ?? '';
?>
<div class="row mt-3">
    <div class="col-md-12">

        <button type="submit"
                name="insert"
                class="btn btn-danger mb-2">
            Insert
        </button>

        <button type="button"
                class="btn btn-warning mb-2"
                onclick="goView()">
            👁 View Datesheet
        </button>

        <a href="exam_datesheet.php"
           class="btn btn-secondary mb-2">
            Reset
        </a>

    </div>
</div>
  </div>
            </form>
            </div>
        </div>
    </div>



</div>
<div class="row">
    <div class="col-12">
<h4 class="mt-4 mb-3 text-primary">
    
    View Exam Datesheet
</h4>
<div class="card shadow mt-3">
    <div class="card-body p-0">

    <div class="table-responsive">
<table class="table table-bordered table-striped">
            <thead>
                <tr>
                   <th>S.no</th>
                    <th>subject Name</th>
                    <th>Exam Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Duration</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php
            $exam_id = isset($_GET['exam_id']) ? $_GET['exam_id'] : '';?>
       <tbody>
<?php 
if($exam_id != ''){
$count=1;
    $select = mysqli_query($con,"
        SELECT ed.*, s.title AS subject_name
        FROM exam_datesheet ed
        LEFT JOIN posts s ON ed.subject_id = s.id
        WHERE ed.exam_id='$exam_id' AND s.type='subject'
        AND ed.institute_id='$institute_id' 
    ");

    while($row_fetch = mysqli_fetch_assoc($select)){

        $subject_name = $row_fetch['subject_name'];
        $exam_dates   = $row_fetch['exam_date'];
        $date_start   = $row_fetch['start_time'];
        $date_end     = $row_fetch['end_time'];
        $duration     = $row_fetch['duration'];
        $id           = $row_fetch['id'];
?>

<tr>
    <td><?php echo $count++ ?></td>
    <td><?php echo $subject_name; ?></td>
    <td><?php echo $exam_dates; ?></td>
    <td><?php echo $date_start; ?></td>
    <td><?php echo $date_end; ?></td>
    <td><?php echo $duration; ?></td>
    <td>
      

        <a href="exam_datesheet.php?exam_id=<?php echo $exam_id; ?>&delete_id=<?php echo $id; ?>" class="btn btn-danger btn-sm">
            Delete
        </a>
    </td>
</tr>

<?php 
    } // while end
} // if end
?>
</tbody>
        </table>
</div>
    </div>
</div>
</div>
</div>
</div>
<!-- <script>
    function viewTable(){
        var id=document.getElementById('view_exam_id').value;
        if(id!=''){
   window.location = 'exam_datesheet.php?view_table&exam_id=' + id;
        }
        else{
            alert("Select Exam");
        }
    }
</script> -->
<?php
include('footer.php');
?>
<script>
$(document).ready(function () {

    let institute_type = "<?= $institute_type ?>";

    /* ===============================
        COLLEGE LOGIC
    =============================== */

    $('#st_course').on('change', function () {

        let course_id = $(this).val();

        $('#st_branch').html('<option>Loading...</option>');
        $('#st_semester').html('<option>Loading...</option>');
        $('#st_subject').html('<option>Select Subject</option>');
    

        $.post('ajax.php', {
            action: 'get_branch',
            course_id: course_id
        }, function (res) {
            $('#st_branch').html(res.options);
        }, 'json');

        $.post('ajax.php', {
            action: 'get_semester',
            course_id: course_id
        }, function (res) {
            $('#st_semester').html(res.options);
        }, 'json');
    });

    $('#st_branch, #st_semester, #st_session').on('change', function () {
        loadCollegeData();
    });

    function loadCollegeData() {

        let course_id = $('#st_course').val();
        let branch_id = $('#st_branch').val();
        let semester  = $('#st_semester').val();
        let session   = $('#st_session').val();

        if (!course_id || !branch_id || !semester || !session) return;

        // SUBJECT
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
            success: function (res) {
                $('#st_subject').html(res.options);
            }
        });

  
    }


    /* ===============================
        SCHOOL LOGIC
    =============================== */

    $('#st_class').on('change', function () {

        let class_id = $(this).val();

        $('#st_section').html('<option>Loading...</option>');
        $('#st_subject').html('<option>Select Subject</option>');
     

        $.post('ajax.php', {
            action: 'get_sections',
            class_id: class_id
        }, function (res) {
            $('#st_section').html(res.options);
        }, 'json');
    });

    $('#st_section').on('change', function () {
        loadSchoolData();
    });

    function loadSchoolData() {

        let class_id   = $('#st_class').val();
        let section_id = $('#st_section').val();

        if (!class_id || !section_id) return;

        // SUBJECT
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_subject',
                class_id: class_id,
                section_id: section_id
            },
            success: function (res) {
                $('#st_subject').html(res.options);
            }
        });

       
    }

});
</script>
<script>
function goView(){

    let exam_id     = document.getElementById('st_exam')?.value || '';
    let course_id   = document.getElementById('st_course')?.value || '';
    let branch_id   = document.getElementById('st_branch')?.value || '';
    let semester_id = document.getElementById('st_semester')?.value || '';
    let session_id  = document.getElementById('st_session')?.value || '';
    let class_id    = document.getElementById('st_class')?.value || '';
    let section_id  = document.getElementById('st_section')?.value || '';

    if(!exam_id){
        alert("Please select exam");
        return;
    }

    let url = "view_datesheet.php?exam_id="+exam_id;

    if(course_id) url += "&course_id="+course_id;
    if(branch_id) url += "&branch_id="+branch_id;
    if(semester_id) url += "&semester_id="+semester_id;
    if(session_id) url += "&session_id="+session_id;
    if(class_id) url += "&class_id="+class_id;
    if(section_id) url += "&section_id="+section_id;

    window.location.href = url;
}
</script>