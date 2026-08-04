<?php include('includes/auth.php');
checkRole('teacher');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>]
<?php  $institute_id=$_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
$user_id=$_SESSION['user_id'];
$roll=mysqli_query($con,"SELECT * FROM `accounts` WHERE id='$user_id' AND institute_id='$institute_id'");
$row=mysqli_fetch_assoc($roll);
$get_roll=$row['roll_no'];
?>
<?php

    $teacher_roll=$get_roll;
    $teacher=mysqli_query($con,"SELECT id,name FROM `accounts` WHERE roll_no='$teacher_roll'");
    $teacher_fetch=mysqli_fetch_assoc($teacher);
    $teacher_id=$teacher_fetch['id'];
    $teacher_name=$teacher_fetch['name'];
    //$semester_id=$_GET['sem_id'];
$check=mysqli_query($con,"SELECT teacher_id FROM `teacher_feedback` WHERE teacher_id='$teacher_id'");
if(mysqli_num_rows($check)>0){
    echo "<script>alert('Feedback form is already Submitted Successfully');</script>";
            echo "<script>window.open('dashboard.php','_self')</script>";
}

$search=mysqli_query($con,"SELECT roll_no,Name,email FROM `accounts` WHERE roll_no='$teacher_id'");
if(mysqli_num_rows($search)>0){
while($row_fetch=mysqli_fetch_assoc($search)){
    $teacher_name=$row_fetch['Name'];
$teacher_email=$row_fetch['email'];
}
}

?>
<?php 
if(isset($_POST['submit_feedback'])){
  
    //$semester_id=intval($_GET['sem_id']);
    $year=date("Y");
    $next_year=$year+1;
    $current_session=$year ."-". $next_year;
    // send the data into teacher_feedback table
    $insert_data=mysqli_query($con,"INSERT INTO `teacher_feedback` (teacher_id,session,institute_id) VALUES('$teacher_id','$current_session',$institute_id)");
    $feedback_id=mysqli_insert_id($con);
    if($_POST['rating']){
       
    foreach($_POST['rating'] as $question_no=>$subjects){
        foreach($subjects as $subject_id=>$rating){
$rating=intval($rating);
$question_no=intval($question_no);
$subject_id=intval($subject_id);
if($rating>0){
    $insert_feedback=mysqli_query($con,"INSERT INTO `meta_teacher` (feedback_id,subject_id,question_id,rating,institute_id) VALUES('$feedback_id','$subject_id','$question_no','$rating',$institute_id)");
   
if(!$insert_feedback){
    die("Meta Insert Error: ".mysqli_error($con));
}
}
        }
    }
    }
     echo "<script>alert('Feedback Submitted Successfully');</script>";
   echo "<script>window.open('dashboard.php','_self')</script>";
}
?>
<?php
$questions = [];

$select=mysqli_query($con,"
    SELECT question 
    FROM feedback_questions 
    WHERE audience='teachers' 
    AND institute_id='$institute_id'
");

while($fetch_select=mysqli_fetch_assoc($select)){
    $questions[] = $fetch_select['question'];
}
?>
<?php
$subjects=[];
$year=date("Y");
$next_year=$year+1;
$session=$year . '-' . $next_year;
$subject_query=mysqli_query($con,"SELECT p.id,p.title FROM teacher_subjects ts JOIN posts p ON ts.subject_id=p.id WHERE ts.teacher_id='$teacher_id' AND ts.session_id='$session'");
while($subject_fetch=mysqli_fetch_assoc($subject_query)){
    $subjects[]=$subject_fetch;
}
//}
?>


<div class="container mt-4">
    <div class="card shadow-lg border-0">
        
        <!-- Header -->
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Teacher Feedback Form</h4>
            <small><?php echo $teacher_name; ?></small>
            <br>
                <small><?php echo $teacher_roll; ?></small>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        
                        <!-- Table Head -->
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width:300px; text-align:left;">Questions</th>
                                <?php foreach($subjects as $subject){ ?>
                                    <th><?php echo $subject['title']; ?></th>
                                <?php } ?>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody>
                        <?php foreach($questions as $qindex => $question){ ?>
                            <tr>
                                
                                <!-- Question -->
                                <td style="text-align:left; font-weight:500;">
                                    <?php echo ($qindex+1).". ".$question; ?>
                                </td>

                                <!-- Ratings -->
                                <?php foreach($subjects as $subject){ ?>
                                <td>
                                    <select class="form-select shadow-sm"
                                        name="rating[<?php echo $qindex;?>][<?php echo $subject['id']?>]">

                                        <option value="">⭐ Rate</option>

                                        <?php for($i=1;$i<=5;$i++){ ?>
                                            <option value="<?php echo $i?>">
                                                <?php echo $i ?> ⭐
                                            </option>
                                        <?php } ?>

                                    </select>
                                </td>
                                <?php } ?>

                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>

                <!-- Submit Button -->
                <div class="text-end mt-4">
                    <button type="submit" name="submit_feedback"
                        class="btn btn-success px-5 py-2 shadow">
                        Submit Feedback ✅
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php include('footer.php');?>