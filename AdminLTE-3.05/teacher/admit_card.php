<?php include('includes/auth.php');
checkRole('teacher');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>
<?php
$institute_id=$_SESSION['institute_id'];

$system_type=$_SESSION['system_type'];
?>

<div class="content-header">
  
  <div class="container-fluid">
    <div class="row">

      <div class="col-12">
        <h1 style="color:#0b3d91;" class=" mb-4 text-center"><u>Generate Admit Card</u></h1>
      </div>
</div>
<h4>Filteration</h4>
<div class="card">
    <div class="card-body">
    <div class="card-body">
            <form  method="post">
<?php
if($system_type=='college'){?>
   <!-- Course -->
    <div class="row">
    <div class="col-lg-4">
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
    <div class="col-lg-4">
      <div class="form-group">
        <label>Select Branch:</label>
        <select id="st_branch" name="st_branch" class="form-control">
          <option value="">Select Branch</option>
        </select>
      </div>
    </div>

    <!-- Semester -->
    <div class="col-lg-4">
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
</div>
<div class="row">
      <div class="col-lg-4">
      <div class="form-group">
        <label>Select Semester:</label>
        <select id="st_semester" name="st_semester" class="form-control">
          <option value="">Select Semester</option>
        </select>
      </div>
    </div>
        <div class="col-lg-4">
      <div class="form-group">
        <label>Select Exam Type:</label>
       
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
    </div>
    <?php } else {?>
  
     <div class="row">
    <div class="col-lg-4">
      <div class="form-group">
        <label>Select Class:</label>
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
    </div>

    <!-- Branch -->
    <div class="col-lg-4">
      <div class="form-group">
        <label>Select Branch:</label>
        <select name="section" id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

      </div>
    </div>

    <!-- Semester -->
    <div class="col-lg-4">
      <div class="form-group">
        <label>Select Session:</label>
    
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
    </div>
</div>

        <div class="col-lg-4">
      <div class="form-group">
        <label>Select Exam Type:</label>
       
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
    </div>
    <?php } ?>
            <div class="col-lg-4">
                <!-- <button type="submit" class="btn btn-sm btn-success mt-4" name="generate_admit" id="generate_admit">Generate Admit Card</button> -->
                                <button type="submit" class="btn btn-sm btn-success mt-4" name="apply" id="apply">Apply</button>
            </div>
        
    </div>
</div>
</form>
<div class="card">
    <div class="card-body">
        <h2>Status</h2>
        <div class="row">
            <div class="col-4">
                <div class="info-box">
                      <span class="info-box-icon bg-info elevation-1"><i class="fas fa-graduation-cap"></i></span>
                      <div class="info-box-content">
                          <span class="info-box-text">Total Students</span>
                                 <span class="info-box-number">
                 <?php



$course   = $_GET['st_course'] ?? '';
$branch   = $_GET['st_branch'] ?? '';
$semester = $_GET['st_semester'] ?? '';

$class    = $_GET['class_id'] ?? '';
$section  = $_GET['section'] ?? '';

$session  = $_GET['st_session'] ?? '';
$exam_id  = $_GET['st_exam'] ?? '';

if($system_type == 'college'){

$q = mysqli_query($con,"
SELECT COUNT(DISTINCT a.id) as total
FROM accounts a
JOIN usermeta uc ON a.id=uc.user_id AND uc.meta_key='course_name' AND uc.meta_value='$course'
JOIN usermeta ub ON a.id=ub.user_id AND ub.meta_key='branch_name' AND ub.meta_value='$branch'
JOIN usermeta us ON a.id=us.user_id AND us.meta_key='semester' AND us.meta_value='$semester'
JOIN usermeta ss ON a.id=ss.user_id AND ss.meta_key='session' AND ss.meta_value='$session'
WHERE a.type='student'
AND a.institute_id='$institute_id'
");

} else {

$q = mysqli_query($con,"
SELECT COUNT(DISTINCT a.id) as total
FROM accounts a
JOIN usermeta u1 ON a.id=u1.user_id AND u1.meta_key='st_class' AND u1.meta_value='$class'
JOIN usermeta u2 ON a.id=u2.user_id AND u2.meta_key='st_section' AND u2.meta_value='$section'
JOIN usermeta u3 ON a.id=u3.user_id AND u3.meta_key='session' AND u3.meta_value='$session'
WHERE a.type='student'
AND a.institute_id='$institute_id'
");
}


$data = mysqli_fetch_assoc($q);
$total_count = $data['total'] ?? 0;

echo $total_count;
?>
</span>
                      </div>
                </div>
            </div>
            <div class="col-4">
                 <div class="info-box">
                      <span class="info-box-icon bg-info bg-warning elevation-1"><i class="fas fa-id-card"></i></span>
                      <div class="info-box-content">
                          <span class="info-box-text">Generated Admit Card</span>
                                 <span class="info-box-number">
<?php
$course   = $_GET['st_course'] ?? '';
$branch   = $_GET['st_branch'] ?? '';
$semester = $_GET['st_semester'] ?? '';

$class    = $_GET['class_id'] ?? '';
$section  = $_GET['section'] ?? '';

$session  = $_GET['st_session'] ?? '';
$exam_id  = $_GET['st_exam'] ?? '';

   $select_count = mysqli_query($con,"
SELECT COUNT(DISTINCT student_id) AS total
FROM admit_cards
WHERE exam_id='$exam_id'
AND institute_id='$institute_id'
");

$count_fetch = mysqli_fetch_assoc($select_count);
$count = $count_fetch['total'] ?? 0;

echo $count;?>
                               <div class="text-right">
                                <?php if($count>0){
                                      echo "<a href='generated_admit.php?st_course=$course&st_branch=$branch&st_semester=$semester&academic_session=$session&exam_id=$exam_id' class='btb btn-sm btn-success ml-7'><i class='fas fa-file-pdf'></i></a>";
                                } ?>
                               </div>
                             
                               
</span>
                      </div>
            </div>
</div>
     <div class="col-4">
                 <div class="info-box">
                      <span class="info-box-icon bg-info bg-danger elevation-1"><i class="fas fa-clock"></i></span>
                      <div class="info-box-content">
                          <span class="info-box-text">Pending Admit Card</span>
                                 <span class="info-box-number">
                                    <?php
                                    $pending=$total_count-$count;
                                   echo $pending;?>
                                   <div class="text-right">
                                    <?php
                                     $exam_id=isset($_GET['st_exam'])?$_GET['st_exam'] :'';
                                   if($pending>0){
                                 echo "<a href='pending_admit.php?st_course=$course&st_branch=$branch&st_semester=$semester&st_session=$session&exam_id=$exam_id'
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

<table class="table table-bordered">
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
$session  = $_GET['st_session'] ?? '';
$semester = $_GET['st_semester'] ?? '';
$exam_id=$_GET['st_exam'] ?? '';
$i = 1;

if($system_type == 'college'){

$q = mysqli_query($con,"
SELECT a.id,a.Name,a.roll_no
FROM accounts a

JOIN usermeta uc ON a.id=uc.user_id
AND uc.meta_key='course_name'
AND uc.meta_value='$course'

JOIN usermeta ub ON a.id=ub.user_id
AND ub.meta_key='branch_name'
AND ub.meta_value='$branch'

JOIN usermeta us ON a.id=us.user_id
AND us.meta_key='session'
AND us.meta_value='$session'

JOIN usermeta um ON a.id=um.user_id
AND um.meta_key='semester'
AND um.meta_value='$semester'

LEFT JOIN admit_cards ac
ON ac.student_id=a.id
AND ac.exam_id='$exam_id'
AND ac.course_id='$course'
AND ac.branch_id='$branch'
AND ac.semester='$semester'
AND ac.academic_session='$session'
AND ac.institute_id='$institute_id'

WHERE a.type='student'
AND a.institute_id='$institute_id'
AND ac.student_id IS NULL
");

}else{

$q = mysqli_query($con,"
SELECT a.id,a.Name,a.roll_no
FROM accounts a

JOIN usermeta uc ON a.id=uc.user_id
AND uc.meta_key='st_class'
AND uc.meta_value='$class'

JOIN usermeta ub ON a.id=ub.user_id
AND ub.meta_key='st_section'
AND ub.meta_value='$section'

JOIN usermeta us ON a.id=us.user_id
AND us.meta_key='session'
AND us.meta_value='$session'

LEFT JOIN admit_cards ac
ON ac.student_id=a.id
AND ac.exam_id='$exam_id'
AND ac.class='$class'
AND ac.section='$section'
AND ac.academic_session='$session'
AND ac.institute_id='$institute_id'

WHERE a.type='student'
AND a.institute_id='$institute_id'
AND ac.student_id IS NULL
");

}
if(mysqli_num_rows($q) > 0){
while($row=mysqli_fetch_assoc($q)){
?>

<tr>
<td><?= $i++ ?></td>
<td><?= $row['roll_no'] ?></td>
<td><?= $row['Name'] ?></td>
<?php
if($system_type=='college'){?>
<td>
    <a href="generate_admit.php?generate=1&student_id=<?= $row['id'] ?>&exam_id=<?= $exam_id?>&semester=<?= $semester?>&session=<?= $session?>&course_id=<?= $course?>&branch_id=<?= $branch?>" 
       class="btn btn-success btn-sm">
       Generate Admit
    </a>
</td>
<?php } else {?>
<td>
    <a href="generate_admit.php?generate=1&student_id=<?= $row['id'] ?>&exam_id=<?= $exam_id?>&session=<?= $session?>&class_id=<?= $class?>&section_id=<?= $section?>" 
       class="btn btn-success btn-sm">
       Generate Admit
    </a>
</td>
<?php } ?>
</tr>

  <?php } ?>

<?php } else { ?>
  <tr>
            <td colspan='4' class='text-center text-danger'>
                No student left
            </td>
          </tr>
        <?php } ?>
</tbody>
</table>
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

let system = "<?= $system_type ?>";

$('#apply').click(function(e){
    e.preventDefault();

    let url = "?";

    if(system === 'college'){
        url += `st_course=${$('#st_course').val()}&st_branch=${$('#st_branch').val()}&st_semester=${$('#st_semester').val()}`;
    } else {
        url += `class_id=${$('#class_id').val()}&section=${$('#st_section').val()}`;
    }

    url += `&st_session=${$('#st_session').val()}&st_exam=${$('#st_exam').val()}`;

    window.location.href = url;
});


// LOAD BRANCH / SEMESTER
$('#st_course').on('change', function(){

    $.post('ajax.php',{action:'get_branch',course_id:$(this).val()},function(res){
        $('#st_branch').html(res.options);
    },'json');

    $.post('ajax.php',{action:'get_semester',course_id:$(this).val()},function(res){
        $('#st_semester').html(res.options);
    },'json');

});

// SCHOOL SECTION
$('#class_id').on('change', function(){

    $.post('ajax.php',{action:'get_sections',class_id:$(this).val()},function(res){
        $('#st_section').html(res.options);
    },'json');

});

});
</script>
