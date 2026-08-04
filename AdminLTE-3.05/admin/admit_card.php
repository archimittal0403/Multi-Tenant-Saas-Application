<?php include('includes/auth.php');
checkRole('admin'); ?>
<?php include('includes/config.php') ?>
<?php include('includes/functions.php') ?>
<?php include('header.php') ?>
<?php include('sidebar.php') ?>

<?php
$institute_id = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];
?>
<?php
$course   = $_GET['st_course'] ?? '';
$branch   = $_GET['st_branch'] ?? '';
$session  = $_GET['st_session'] ?? '';
$semester = $_GET['st_semester'] ?? '';
$class=$_GET['st_class'] ?? '' ;
$section=$_GET['st_section'] ?? '' ;
$academic_session=$_GET['academic_session'] ?? '';

?>
<style>
body{
    overflow-x:hidden;
}

.card{
    border-radius:10px;
}

.info-box{
    min-height:80px;
}

.table-responsive{
    overflow-x:auto;
}

@media (max-width:768px){

    h1{font-size:20px;}

    .card-body{
        padding:12px !important;
    }

    table td, table th{
        font-size:13px;
        white-space:nowrap;
    }

    .info-box{
        margin-bottom:10px;
    }

    .btn{
        width:100%;
    }
}
</style>
<div class="container-fluid">

      <div class="col-12">
        <h1 style="color:#0b3d91;" class=" mb-4 text-center"><u>Generate Admit Card</u></h1>
      </div>
</div>
<h4>Filteration</h4>
<div class="card shadow-sm mb-3">
<div class="card-body">
            <form  method="post">

   <!-- Course -->
  <div class="row g-2">
         <?php
        if($institute_type=='college'){?>
<div class="col-12 col-md-6 col-lg-4">
   
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


    <!-- Branch -->
<div class="col-12 col-md-6 col-lg-4">

        <label>Select Branch:</label>
        <select id="st_branch" name="st_branch" class="form-control">
          <option value="">Select Branch</option>
        </select>
      </div>
   
    <!-- Semester -->
<div class="col-12 col-md-6 col-lg-4">
  
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
<div class="row g-2">
<div class="col-12 col-md-6 col-lg-4">

        <label>Select Semester:</label>
        <select id="st_semester" name="st_semester" class="form-control">
          <option value="">Select Semester</option>
        </select>
      </div>
  
        <?php } else {?>
<div class="col-12 col-md-6 col-lg-4">


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
<div class="col-12 col-md-6 col-lg-4">
  

<label>Select Section</label>

<select id="st_section" name="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>

<div class="col-12 col-md-6 col-lg-4">

    <label for="">Academic Session</label>
 <input type="text"
       id="academic_session"
       name="session_id"

       placeholder="Enter Academic Session"
       class="form-control">
 </div>
</div>

 <?php } ?>

     <div class="col-12 col-md-6 col-lg-4">
  
        <label>Select Exam Type:</label>
        <select id="st_exam" name="st_exam" class="form-control">
          <option value="">Select Exam</option>
        </select>
   
    </div>
     
    <div class="col-12 col-md-4 d-flex align-items-end">
                <!-- <button type="submit" class="btn btn-sm btn-success mt-4" name="generate_admit" id="generate_admit">Generate Admit Card</button> -->
                                <button type="submit" class="btn btn-sm btn-success mt-4" name="apply" id="apply">Apply</button>
            </div>
        
    </div>
</div>
</form>
<div class="card">
    <div class="card-body">
        <h2>Status</h2>

<?php

// ================= COMMON =================

$exam_id = $_GET['st_exam'] ?? '';

$total_count = 0;
$count = 0;


// ================= COLLEGE =================

if($institute_type=='college'){

    $course   = $_GET['st_course'] ?? '';
    $branch   = $_GET['st_branch'] ?? '';
    $session  = $_GET['st_session'] ?? '';
    $semester = $_GET['st_semester'] ?? '';

    // TOTAL STUDENTS
    $q = mysqli_query($con,"
        SELECT COUNT(DISTINCT a.id) as total
        FROM accounts a

        JOIN usermeta uc
        ON a.id=uc.user_id
        AND uc.meta_key='course_name'
        AND uc.meta_value='$course'

        JOIN usermeta ub
        ON a.id=ub.user_id
        AND ub.meta_key='branch_name'
        AND ub.meta_value='$branch'

        JOIN usermeta us
        ON a.id=us.user_id
        AND us.meta_key='session'
        AND us.meta_value='$session'

        JOIN usermeta um
        ON a.id=um.user_id
        AND um.meta_key='semester'
        AND um.meta_value='$semester'

        WHERE a.type='student'
        AND a.institute_id='$institute_id'
    ");

    $data = mysqli_fetch_assoc($q);

    $total_count = $data['total'] ?? 0;

    // GENERATED ADMIT
    $select_count = mysqli_query($con,"
        SELECT COUNT(DISTINCT a.student_id) as Totalcount
        FROM admit_cards a

        JOIN usermeta u
        ON a.student_id=u.user_id

        WHERE u.meta_key='semester'
        AND u.meta_value='$semester'

        AND a.exam_id='$exam_id'
    ");

    $count_fetch = mysqli_fetch_assoc($select_count);

    $count = $count_fetch['Totalcount'] ?? 0;
}


// ================= SCHOOL =================

else{

    $class_id = $_GET['st_class'] ?? '';
    $section_id = $_GET['st_section'] ?? '';
    $academic_session = $_GET['academic_session'] ?? '';

    $session_year = explode('-', $academic_session)[0] ?? '';

    // TOTAL STUDENTS
    $q = mysqli_query($con,"
        SELECT COUNT(DISTINCT a.id) as total
        FROM accounts a

        JOIN usermeta uc
        ON a.id=uc.user_id
        AND uc.meta_key='st_class'
        AND uc.meta_value='$class_id'

        JOIN usermeta us
        ON a.id=us.user_id
        AND us.meta_key='st_section'
        AND us.meta_value='$section_id'

        JOIN usermeta ud
        ON a.id=ud.user_id
        AND ud.meta_key='doa'

        WHERE a.type='student'
        AND a.institute_id='$institute_id'

        AND YEAR(STR_TO_DATE(ud.meta_value,'%Y-%m-%d'))='$session_year'
    ");

    $data = mysqli_fetch_assoc($q);

    $total_count = $data['total'] ?? 0;

    // GENERATED ADMIT
    $select_count = mysqli_query($con,"
        SELECT COUNT(DISTINCT a.student_id) as Totalcount
        FROM admit_cards a

        JOIN usermeta uc
        ON a.student_id=uc.user_id
        AND uc.meta_key='st_class'
        AND uc.meta_value='$class_id'

        JOIN usermeta us
        ON a.student_id=us.user_id
        AND us.meta_key='st_section'
        AND us.meta_value='$section_id'

        JOIN usermeta ud
        ON a.student_id=ud.user_id
        AND ud.meta_key='doa'

        WHERE a.exam_id='$exam_id'

        AND YEAR(STR_TO_DATE(ud.meta_value,'%Y-%m-%d'))='$session_year'
    ");

    $count_fetch = mysqli_fetch_assoc($select_count);

    $count = $count_fetch['Totalcount'] ?? 0;
}


// ================= PENDING =================

$pending = $total_count - $count;

?>

<div class="row g-2">

            <!-- TOTAL -->
<div class="col-12 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1">
                        <i class="fas fa-graduation-cap"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Students</span>

                        <span class="info-box-number">
                            <?= $total_count ?>
                        </span>
                    </div>
                </div>
            </div>


            <!-- GENERATED -->
<div class="col-12 col-md-4">
                <div class="info-box">

                    <span class="info-box-icon bg-warning elevation-1">
                        <i class="fas fa-id-card"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            Generated Admit Card
                        </span>

                        <span class="info-box-number">
                            <?= $count ?>
                        </span>

                    </div>
                </div>
            </div>


            <!-- PENDING -->
<div class="col-12 col-md-4">
                <div class="info-box">

                    <span class="info-box-icon bg-danger elevation-1">
                        <i class="fas fa-clock"></i>
                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">
                            Pending Admit Card
                        </span>

                        <span class="info-box-number">
                            <?= $pending ?>

                            <div class="text-right">

<?php
if($pending>0){

echo "<a href='pending_admit.php?exam_id=$exam_id'
class='btn btn-sm btn-danger'>
<i class='fas fa-file-pdf'></i>
</a>";

}
?>

                            </div>

                        </span>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="card shadow-sm">
<div class="card-body table-responsive">

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>S.No</th>
    <th>Roll No</th>
    <th>Name</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php

$i=1;

if($institute_type=='college'){

    $course   = $_GET['st_course'] ?? '';
    $branch   = $_GET['st_branch'] ?? '';
    $session  = $_GET['st_session'] ?? '';
    $semester = $_GET['st_semester'] ?? '';

    $q = mysqli_query($con, "
    SELECT a.id,a.name,a.roll_no
    FROM accounts a

    JOIN usermeta uc 
    ON a.id=uc.user_id 
    AND uc.meta_key='course_name'
    AND uc.meta_value='$course'

    JOIN usermeta ub 
    ON a.id=ub.user_id 
    AND ub.meta_key='branch_name'
    AND ub.meta_value='$branch'

    JOIN usermeta us 
    ON a.id=us.user_id 
    AND us.meta_key='session'
    AND us.meta_value='$session'

    JOIN usermeta um 
    ON a.id=um.user_id 
    AND um.meta_key='semester'
    AND um.meta_value='$semester'

    WHERE a.type='student'
    AND a.institute_id='$institute_id'
    ");

}else{

    $class_id   = $_GET['st_class'] ?? '';
    $section_id = $_GET['st_section'] ?? '';

        $academic_session = $_GET['academic_session'] ?? '';
        $session_year = explode('-', $academic_session)[0];
    $q = mysqli_query($con,"
    SELECT a.id,a.name,a.roll_no
    FROM accounts a

    JOIN usermeta uc
    ON a.id=uc.user_id
    AND uc.meta_key='st_class'
    AND uc.meta_value='$class_id'

    JOIN usermeta us
    ON a.id=us.user_id
    AND us.meta_key='st_section'
    AND us.meta_value='$section_id'

    JOIN usermeta um
ON a.id=um.user_id
AND um.meta_key='doa'

    WHERE a.type='student'
    AND a.institute_id='$institute_id'
    AND YEAR(STR_TO_DATE(um.meta_value, '%Y-%m-%d'))='$session_year'
    ");
}

while($row=mysqli_fetch_assoc($q)){
?>

<tr>
<td><?= $i++ ?></td>
<td><?= $row['roll_no'] ?></td>
<td><?= $row['name'] ?></td>
<td>
  <?php if($institute_type=='college'){ ?>

<a href="generate_admit.php?
student_id=<?php echo $row['id']; ?>
&exam_id=<?php echo $exam_id;?>
&session=<?php echo $session;?>
&semester=<?php echo $semester;?>
&course_id=<?php echo $course;?>
&branch_id=<?php echo $branch;?>"
class="btn btn-danger btn-sm w-100 w-md-auto">
Generate PDF
</a>

<?php } else { ?>

<a href="generate_admit.php?
student_id=<?php echo $row['id']; ?>
&exam_id=<?php echo $exam_id;?>
&class_id=<?php echo $class;?>
&section_id=<?php echo $section;?>
&academic_session=<?php echo $academic_session;?>"
class="btn btn-danger btn-sm w-100 w-md-auto">
Generate PDF
</a>

<?php } ?>
</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>
<div class="card shadow">
  <div class="card-body p-4">
<?php
// join create_exam table and admit_card table
// $select=mysqli_query($con,"SELECT e.exam_name,e.academic_year,e.semester_id
// FROM create_exam e JOIN admit_card a ON e.id=a.exam_id");
// $row_fetch=mysqli_fetch_assoc($select);
// $exam_name=$row_fetch['exam_name'];
// $academic_year=$row_fetch['academic_year'];
// $sem_id=$row_fetch['semester_id'];
?>

<?php
include('footer.php');
?>

<script>
$(document).ready(function(){

// ================= COLLEGE =================

function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();

    // SUBJECT
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

                if(res.status){
                    $('#st_subject').html(res.options);
                }else{
                    $('#st_subject').html('<option>No Subject Found</option>');
                }
            }
        });

        // EXAM
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

                }else{

                    $('#st_exam').html('<option>No Exam Found</option>');
                }
            }
        });
    }
}


// ================= SCHOOL =================

function loadSchoolData(){

    let class_id   = $('#st_class').val();
    let section_id = $('#st_section').val();

    if(class_id && section_id){

        // EXAM LOAD
        $.ajax({
            url:'ajax.php',
            type:'POST',
            dataType:'json',
            data:{
                action:'get_exam',
                class_id:class_id,
                section_id:section_id
            },
            success:function(res){

                console.log(res);

                if(res.status){

                    $('#st_exam').html(
                        '<option value="">Select Exam</option>' +
                        '<option value="'+res.exam_id+'">'+res.exam_type+'</option>'
                    );

                }else{

                    $('#st_exam').html('<option>No Exam Found</option>');
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

});


// ================= SCHOOL CLASS =================

$('#st_class').on('change', function(){

    let class_id = $(this).val();

    $('#st_section').html('<option>Loading...</option>');

    $.post('ajax.php',{
        action:'get_sections',
        class_id:class_id
    },function(res){

        $('#st_section').html(res.options);

    },'json');
});


// ================= EVENTS =================

$('#st_branch, #st_semester, #st_session').on('change', function(){
    loadSubject();
});

$('#st_section').on('change', function(){
    loadSchoolData();
});

});
</script>
<script>
$('#apply').click(function(e){

    e.preventDefault();

    // COLLEGE
    let course   = $('#st_course').val();
    let branch   = $('#st_branch').val();
    let session  = $('#st_session').val();
    let semester = $('#st_semester').val();

    // SCHOOL
    let class_id   = $('#st_class').val();
    let section_id = $('#st_section').val();
    let academic_session = $('#academic_session').val();

    // COMMON
    let exam = $('#st_exam').val();

    let url = '';

    // ================= COLLEGE =================
    if(course){

        url = `?st_course=${course}&st_branch=${branch}&st_session=${session}&st_semester=${semester}&st_exam=${exam}`;

    }

    // ================= SCHOOL =================
    else{

        url = `?st_class=${class_id}&st_section=${section_id}&academic_session=${academic_session}&st_exam=${exam}`;

    }

    window.location.href = url;

});
</script>