<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>
<?php
if(!isset($_GET['all_id'])){
    echo "id is missing";
}

$id=$_GET['all_id'];
$search=mysqli_query($con,"SELECT * FROM `feedback` WHERE student_id='$id'");
if(mysqli_num_rows($search)>0){
             echo "<script>alert('Feedback form is already Submitted Successfully');</script>";
            echo "<script>window.open('dashboard.php','_self')</script>";
            exit();
}
 $search_account=mysqli_query($con,"SELECT type FROM `accounts` WHERE id='$id'");
 $row_fetch=mysqli_fetch_assoc($search_account);
                $type=$row_fetch['type'];

                if($type=='teacher'){
            }
            elseif($type=='parent'){
               echo "<script>window.open('parent_feedback.php','_self')</script>";
                 exit();
            }
            elseif($type=='admin'){
           echo "<script>window.open('admin_feedback.php?all_id=$id','_self')</script>";
            }
            else{
echo "<script>alert('Only teacher can fill the form');</script>"; 
echo "<script>window.open('dashboard.php','_self')</script>";
    exit();
}
            
    ?>

<?php
if(isset($_GET['all_id'])){
    $id=$_GET['all_id'];
$select_data=mysqli_query($con,"SELECT Name,type,email FROM `accounts` WHERE id='$id'");
$row_fetch=mysqli_fetch_assoc($select_data);
$name=$row_fetch['Name'];
$email=$row_fetch['email'];
$type=$row_fetch['type'];
$semester=get_usermeta($id,'semester');
}
?>
<?php
if(isset($_POST['submit_teacher'])){
    $id=intval($id);
    $semester=intval($semester);
    // insert into feedback
    $insert_query=mysqli_query($con,"INSERT INTO `feedback` (student_id,semester_id) VALUES($id,$semester)");
    // generate id
    $feedback_id=mysqli_insert_id($con);
    if(isset($_POST['rating'])){
    foreach($_POST['rating'] as $question_no=>$subjects){
        foreach($subjects as $subject_id=>$rating){
            $question_no=intval($question_no);
            $rating=intval($rating);
            $subject_id=intval($subject_id);
            // insert into meta_feedback
            $insert_feedback=mysqli_query($con,"INSERT INTO `meta_feedback` (feedback_id,subject_id,question_no,rating) VALUES($feedback_id,$subject_id,$question_no,$rating)");
        
if(!$insert_feedback){
    die("Meta Insert Error: ".mysqli_error($con));
}
            }
    }
}
 echo "<script>alert('Feedback Submitted Successfully');</script>";
   echo "<script>window.open('_self','index.php')</script>";
    }
?>
<?php
$questions=[];
$questions=[
"Kya book syllabus ke according hai?",
"Content subject depth ke hisaab se sufficient hai?",
"Exercises aur practice questions useful hain?",
"Kya aap is book ko recommend karenge?",
"Syllabus industry standard ke according hai?",
"Assessment pattern fair hai?",
"Attendance module sahi kaam karta hai?",
"Kya system time save karta hai?"
]
?>
<?php 
$subjects=[];
$select=mysqli_query($con,"SELECT name,id FROM `courses` WHERE semester='$semester'");
if(mysqli_num_rows($select)>0){
    while($row_fetch=mysqli_fetch_assoc($select)){
        $subjects[]=$row_fetch;
    }
}
?>

<section class="content">
      <div class="container-fluid">
<div class="card">
 
      <div class="card-body">
<h3><u>Personal Details:</u></h3>
  <h5 class="mt-4 mb-0" style="display:inline-block; margin-right:200px;">
   .Theacher Name:- <?php echo ucfirst($name);?>
  </h5>

 <h5 class="mt-4 mb-0" style="display:inline-block; margin-right:180px;">
   . Teacher Semester :- <?php echo $semester;?>
  </h5>

  <br>

  <h5 class="mt-4 mb-0" style="display:inline-block; margin-right:280px;">
   . Teacher Email:- <?php echo $email;?>
  </h5>

 

</div>
      </div>
</div>
      
</section>
<div class="card-body">
      <h4 class="mb-4">Fill the Form:</h4>
      <form action="" method="post">
        <table class="table table-bordered w-100">
            <thead>
                <tr>
                    <th style="width:50%">Question</th>
                    <?php foreach($subjects as $subject){?>
                      
                          <th><?php echo $subject['name'] ;?></th>
                  <?php  } ?>
                        
                        
              
                </tr>
            </thead>
            <tbody>
                <?php foreach($questions as $qindex=>$question){?>
                <tr>
            <td>
                <?php echo ($qindex+1).".".($question);?>
            </td>
            
                <?php foreach($subjects as $sindex=>$subject){?>
            <td>
                <select name="rating[<?php echo $qindex ?>][<?php echo $student['id']?>]" id="" >
                    <option value="">--Select--</option>
                
                <?php for($i=1;$i<=5;$i++){?>
                    <option value="<?php echo $i?>"><?php echo $i?></option>
               <?php } ?>
</td>
            </select>
          <?php  } ?>
            
</tr>

           <?php } ?>
            </tbody>
        </table>
         <button name="submit_teacher" class="bth btn-success">Submit</button>
      </form>
     
</div>

