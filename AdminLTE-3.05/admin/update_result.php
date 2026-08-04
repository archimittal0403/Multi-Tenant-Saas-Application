<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>


<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
if(isset($_GET['all_id'])){
    $std_id=$_GET['all_id'];
    $semester_id=$_GET['sem_id'];
}
// now update the marks 
if(isset($_POST['update'])){
    $term1=$_POST['term1'];
    $term2=$_POST['term2'];
    $total=$_POST['total'];
    if($term1!==''||$term2!==''||$total!==''){
        echo "<script>alert('fill the data')</script>";
    }
    else{

    }
}

$select=mysqli_query($con,"SELECT Name,id FROM `accounts` WHERE id='$std_id'");
$row=mysqli_fetch_assoc($select);
$std_name=$row['Name'];
$class=get_usermeta($std_id,'st_class');
$class_id=mysqli_query($con,"SELECT title FROM `posts` WHERE id='$class'");
$row_class=mysqli_fetch_assoc($class_id);
$class_name=$row_class['title'];
$section=get_usermeta($std_id,'st_section');
$select_section=mysqli_query($con,"SELECT title FROM `section` WHERE id='$section'");
$row_section=mysqli_fetch_assoc($select_section);
$section_name=$row_section['title'];

$subjects=[];
$select_sub=mysqli_query($con,"SELECT name,id FROM `courses` WHERE semester='$semester_id'");
while($row_fetch=mysqli_fetch_assoc($select_sub)){
    $subjects[]=$row_fetch;
}
?>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <!-- Row 1 -->
        <div class="row mt-1">
          <div class="col-md-6">
            <h5>. Student Name :- <?php echo ucfirst($std_name);?></h5>
          </div>

          <div class="col-md-6">
            <h5>. Roll No :- <?php echo $std_id; ?></h5>
          </div>
        </div>

        <!-- Row 2 -->
        <div class="row mt-2">
          <div class="col-md-6">
            <h5>. Class :- <?php echo $class_name; ?></h5>
          </div>

          <div class="col-md-6">
            <h5>. Section :- <?php echo $section_name; ?></h5>
          </div>

         
        </div>

      </div>
    </div>
  </div>
</section>
<form method="POST">
  <table class="table table-bordered w-100">
        <thead>
            <tr>
                <th>Subject Name</th>
                <th>Mark in Term 1
                    <br>
                    (100)
                </th>
                  <th>Mark in Term 2
                    <br>
                    (100)
                </th>
                  <th>Total Marks
                    <br>
                    (200)
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
        $count=0;
                   $subject_names=[];
    $subject_total=[];
                foreach($subjects as $subject){
                    $subject_id=$subject['id'];
   
                  $count++;
                    $subject_id=$subject['id'];
                    // result for term 1
                    $mark1=mysqli_query($con,"SELECT result_id FROM `result` WHERE class_id='$class' AND section_id='$section' AND subject_id='$subject_id' AND term_id='1'");
$row_id=mysqli_fetch_assoc($mark1);
$result_id=$row_id['result_id'];

$mark=mysqli_query($con,"SELECT marks FROM `result_marks` WHERE result_id='$result_id' AND student_id='$std_id'");
$row_mark=mysqli_fetch_assoc($mark);
$marks=$row_mark['marks'];

// result for term 2
  $mark2=mysqli_query($con,"SELECT result_id FROM `result` WHERE class_id='$class' AND section_id='$section' AND subject_id='$subject_id' AND term_id='2'");
$row_id=mysqli_fetch_assoc($mark2);
$result_id=$row_id['result_id'];

$mark=mysqli_query($con,"SELECT marks FROM `result_marks` WHERE result_id='$result_id' AND student_id='$std_id'");
$row_mark=mysqli_fetch_assoc($mark);
$marks2=$row_mark['marks'];
                ?>

            <tr>    
                <td>

  <?php  echo $subject['name']; ?>

                </td>
           <td><input type="text" id="term1" name="term1_marks[]" value="<?php echo $marks ;?>"> </td>
                <td><input type="text" id="term2" name="term2_marks[]" value="<?php echo $marks2;?>"></td>
                <td><input type="text" id="total" name="total_marks[]" value="<?php echo $marks+$marks2 ?>"></td>
                
            </tr>
          
            <?php } ?>

      

</tbody>
</table>
<button type="submit" id="update" name="update" class="ml-4 btn  btn-success">Update</button>
</form>
