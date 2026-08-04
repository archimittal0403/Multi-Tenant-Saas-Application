<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>



<?php
$institute_id = $_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];

$year = date('Y');
$next_year = $year + 1;
$academic_year = $year . "-" . $next_year;

$exam_id     = $_GET['exam_id'] ?? '';
$student_id  = $_GET['student_id'] ?? '';

// $select_exam=mysqli_query($con,"SELECT exam_type_id FROM `create_exam` WHERE id='$exam_id' AND intitute_id='$institute_id'");
// $row_exam=mysqli_fetch_assoc($select_exam);
// $get_exam=$row_exam['exam_type_id'];
// //
// $get_date=mysqli_query($con,"SELECT  * FROM `exam_datesheet` WHERE id='$get_exam'")
// while($row_date=mysqli_fetch_assoc($get_date)){
//     $get_datesheet=$roe
// }

// COLLEGE
$session_id  = $_GET['session'] ?? '';
$semester_id = $_GET['semester'] ?? '';
$course_id   = $_GET['course_id'] ?? '';
$branch_id   = $_GET['branch_id'] ?? '';

// SCHOOL
$class_id          = $_GET['class_id'] ?? '';
$section_id        = $_GET['section_id'] ?? '';
if($institute_type=='college'){

    $session_q = mysqli_query($con,"
    SELECT title FROM posts 
    WHERE id='$session_id'
    ");

    $row_session = mysqli_fetch_assoc($session_q);

    $academic_session = $row_session['title'] ?? '';

}else{

    $academic_session = $_GET['academic_session'] ?? '';
}

// STUDENT DATA
$select = mysqli_query($con,"SELECT * FROM accounts WHERE id='$student_id' AND institute_id='$institute_id'");
$row = mysqli_fetch_assoc($select);

$Name = $row['Name'];
$roll_no = $row['roll_no'];

$father_name = get_usermeta($student_id,'father_name');
$mother_name = get_usermeta($student_id,'mother_name');

$photo = get_usermeta($student_id,'student_photo');
$st_photo = "uploads/student_photo/".$photo;



// EXAM
$get_exam = mysqli_query($con,"
SELECT et.exam_type
FROM create_exam ce

LEFT JOIN exam_type et
ON ce.exam_type_id = et.id

WHERE ce.id='$exam_id'
");

$row_exam = mysqli_fetch_assoc($get_exam);

$exam_name = $row_exam['exam_type'] ?? '';
?>

<style>
.card-admit {
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    padding: 25px;
    background: #fff;
}
.header-title {
    text-align:center;
    color:#0b3d91;
}
.student-info h5 {
    margin-bottom:10px;
}
.photo-box img {
    width:140px;
    height:170px;
    object-fit:cover;
    border-radius:8px;
}
.table thead {
    background:#0b3d91;
    color:#fff;
}
.signature-box {
    margin-top:60px;
    text-align:right;
}
.signature-box img {
    height:120px;
}
@media print {
    .no-print { display:none; }
}
</style>

<div class="container mt-4">

<div class="card-admit">

    <!-- HEADER -->
    <h2 class="header-title"><u>Admit Card</u></h2>
    <h4 class="header-title mb-4"><u><?php echo $academic_year ?></u></h4>

    <div class="row">

        <!-- LEFT -->
        <div class="col-md-9 student-info">
            <h5><b>Student Name:</b> <?php echo ucfirst($Name); ?></h5>
            <?php
            if($institute_type=='college'){?>
            <h5><b>Roll Number:</b> <?php echo $roll_no; ?></h5>
            <?php } else {?>
              <h5><b>Student IDr:</b> <?php echo $roll_no; ?></h5>
      <?php  } ?>
            <h5><b>Session:</b> <?php echo $academic_session; ?></h5>
            <h5><b>Father Name:</b> <?php echo ucfirst($father_name); ?></h5>
            <h5><b>Mother Name:</b> <?php echo ucfirst($mother_name); ?></h5>
            <h5><b>Exam:</b> <?php echo $exam_name; ?></h5>
            <?php if($institute_type=='college'){ ?>

<h5><b>Semester:</b> <?php echo $semester_id; ?></h5>

<?php }?>
        </div>

        <!-- RIGHT PHOTO -->
        <div class="col-md-3 text-end photo-box">
            <img src="<?php echo $st_photo ?>">
        </div>

    </div>

    <!-- DATE SHEET -->
    <h4 class="text-center mt-4" style="color:#0b3d91;"><u>Exam Date Sheet</u></h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>

<?php

if($institute_type=='college'){

$select = mysqli_query($con,"
SELECT ed.*, p.title 
FROM exam_datesheet ed

JOIN posts p 
ON ed.subject_id = p.id 

WHERE ed.exam_id='$exam_id'
AND ed.course_id='$course_id'
AND ed.branch_id='$branch_id'
AND ed.semester_id='$semester_id'
AND ed.session_id='$session_id'
AND ed.institute_id='$institute_id'
");

}else{

$select = mysqli_query($con,"
SELECT ed.*, p.title 
FROM exam_datesheet ed

JOIN posts p 
ON ed.subject_id = p.id 

WHERE ed.exam_id='$exam_id'
AND ed.class_id='$class_id'
AND ed.section_id='$section_id'
AND ed.institute_id='$institute_id'
");

}

while($row = mysqli_fetch_assoc($select)){
?>
<tr>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['exam_date']; ?></td>
    <td><?php echo $row['start_time']; ?></td>
    <td><?php echo $row['end_time']; ?></td>
    <td><?php echo $row['duration']; ?></td>
</tr>
<?php } ?>

        </tbody>
    </table>

    <!-- INSTRUCTIONS -->
    <h5 class="mt-4 text-danger">Exam Instructions:</h5>
    <ol>
<?php
$select_question=mysqli_query($con,"SELECT question FROM `feedback_questions` WHERE institute_id='$institute_id' AND type='exam'");


while($row = mysqli_fetch_assoc($select_question)){
    echo "<li>".$row['question']."</li>";
}
?>
    </ol>

    <!-- SIGNATURE -->
<?php
$select_priid = mysqli_query($con,"SELECT id FROM accounts WHERE type='principal' AND institute_id='$institute_id'");
$row_prin = mysqli_fetch_assoc($select_priid);
$pid = $row_prin['id'];

$q = mysqli_query($con,"SELECT meta_value FROM usermeta WHERE user_id='$pid' AND meta_key='principle_sign'");
$row = mysqli_fetch_assoc($q);

$p_sign = "uploads/sign/".$row['meta_value'];
?>

<div class="signature-box">
    <img src="<?php echo $p_sign; ?>"><br>
    <div style="border-top:1px solid #000; width:200px; float:right;"></div>
    <br>
    <b>Controller of Examination</b><br>
    <small>Authorized Signature</small>
</div>

<!-- PRINT BUTTON -->
<div class="text-center mt-4 no-print">
    <?php if($institute_type=='college'){?>
<a href="admit_pdf.php?student_id=<?php echo $student_id;?>
&exam_id=<?php echo $exam_id;?>
&session=<?php echo $session_id;?>
&semester=<?php echo $semester_id;?>
&course_id=<?php echo $course_id;?>
&branch_id=<?php echo $branch_id;?>" 
class="btn btn-danger">
Generate PDF
</a>
<?php } else {?>
<a href="admit_pdf.php?student_id=<?php echo $student_id;?>
&exam_id=<?php echo $exam_id;?>
&academic_session=<?php echo $academic_session;?>
&class_id=<?php echo $class_id;?>
&section_id=<?php echo $section_id;?>" 
class="btn btn-danger">
Generate PDF
</a>
<?php } ?>
    <button onclick="window.print()" class="btn btn-primary">Print Admit Card</button>
</div>

</div>
</div>