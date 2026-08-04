<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>


<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
if(isset($_GET['all_id'])){
    $std_id=$_GET['all_id'];
    $semester_id=$_GET['sem_id'];
}
?>

<?php

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
?>
<?php
$subjects=[];
$select_sub=mysqli_query($con,"SELECT name,id FROM `courses` WHERE semester='$semester_id'");
while($row_fetch=mysqli_fetch_assoc($select_sub)){
    $subjects[]=$row_fetch;
}
?>
<?php
$select=mysqli_query($con,"SELECT COUNT(*) as Totalclass,
SUM(status='p') as PresentClass,
(SUM(status='p')/COUNT(*))*100 as percentage
FROM attendance 
GROUP BY  user_id='$std_id'");
while($row_fetch=mysqli_fetch_assoc($select)){
    $total_class=$row_fetch['Totalclass'];
    $present=$row_fetch['PresentClass'];
    $percentage_att=round($row_fetch['percentage']);
}
?>

<?php
$get_principal_id=mysqli_query($con,"SELECT id FROM `accounts` WHERE type='principal'");
$row_fetch=mysqli_fetch_assoc($get_principal_id);
$pid=$row_fetch['id'];
$get_sign=mysqli_query($con,"SELECT meta_value FROM `usermeta` WHERE user_id='$pid' AND meta_key='Principal_sign'");
$row=mysqli_fetch_assoc($get_sign);
$p_sign=$row['meta_value'];
?>
<?php
$get_teacher=mysqli_query($con,"SELECT user_id FROM usermeta WHERE meta_key='teacher_sign' LIMIT 1");
$row_teacher=mysqli_fetch_assoc($get_teacher);
$teacher_id=$row_teacher['user_id'];

$get_teacher_sign=mysqli_query($con,"SELECT meta_value FROM usermeta WHERE user_id='$teacher_id' AND meta_key='teacher_sign'");
$row_sign=mysqli_fetch_assoc($get_teacher_sign);
$t_sign=$row_sign['meta_value'];


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
<a href="generate_result.php?all_id=<?php echo $std_id ?>& sem_id=<?php echo $semester_id ?>" name="generate_report" class="btn btn-success btn-sm mx-4">Genrate PDF</a>
<a href="excel_export.php?all_id=<?php echo $std_id ?>& sem_id=<?php echo $semester_id ?>" name="excel_report" class="btn btn-success btn-sm mx-4">Excel Export</a>
<a href="update_result.php?all_id=<?php echo $std_id ?>&sem_id=<?php echo $semester_id ?> "name="update_report" class="btn btn-success btn-sm mx-4">Update Marks</a>
<div class="card-body">
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


$select=mysqli_query($con,"SELECT COUNT(*) as TotalSubject,
SUM(m.marks) as Score,
SUM(CASE WHEN r.term_id=1 THEN m.marks ELSE 0 END) as term1,
SUM(CASE WHEN r.term_id=2 THEN m.marks ELSE 0 END) as term2
FROM result r 
JOIN result_marks m ON r.result_id=m.result_id
WHERE m.student_id='$std_id'");
$row_fetch=mysqli_fetch_assoc($select);
$TotalSubjects=$row_fetch['TotalSubject'];
$score=$row_fetch['Score'];
$term1=$row_fetch['term1'];
$term2=$row_fetch['term2'];
$percentage=round(($score/($TotalSubjects*100))*100);
 
 $subject_names[]=$subject['name'];
    $subject_total[]=$marks+$marks2;
    ?>
    <?php
 if($percentage<=60){
  $remark="Capable of higher grades; improved focus will boost performance.";
  $grade='D';
 }
 else if($percentage<=70){
  $remark="Capable of achieving higher results with more in-depth study.";
  $grade='C';
 }
 else if($percentage<=80){
  $remark="Solid understanding of the material; great potential to reach the next level.";
  $grade='B';
 }
 else if($percentage<=90){
  $remark="Outstanding performance and dedication to learning.";
  $grade='A';
 }
 else if($percentage=100){
  $remark="An impeccable result reflecting dedication and attention to detail.";
  $grade='A+';
 }
 ?>




            <tr>    
                <td>

  <?php  echo $subject['name']; ?>

                </td>
           <td><?php echo $marks ?> </td>
                <td><?php echo $marks2?></td>
                <td><?php echo $marks+$marks2 ?></td>
                
            </tr>
          
            <?php } ?>
          
            <tr>
              <td><h5>TOTAL</h5></td>
              <td><h5><?php echo $term1 ?></td>
              <td><h5><?php echo $term2 ?></td>
              <td><h5><?php echo $score?></h5></td>
            </tr>
   </tbody>
    </table>
           
          <h5>Total Marks-: <?php echo $count*200 ?></h5>
          
               <h5>Scored Marks-: <?php echo $score ?></h5>
               <h5>Percentage-:<?php echo $percentage."%" ?></h5>
               <h5>Attendance:-<?php echo $percentage_att."%" ?></h5>
               <h5>Grade:-<?php echo $grade;?></h5>
</div>


  <h5 class="mt-2 ml-2">Subject wise Performance</h5>
   <div style="width:100%; height:200px;">
  <canvas id="resultChart"></canvas>
</div>

 <h5 class="mt-2 ml-2">Remark:-  <?php echo $remark?></h5>

<div style="display:flex; justify-content:space-between; margin-top:40px;">

<div style="text-align:center;">
<img src="uploads/<?php echo $t_sign ?>" width="200">
<br>
<b>Class Teacher</b>
</div>

<div style="text-align:center;">
<img src="uploads/<?php echo $p_sign ?>" width="200">
<br>
<b>Principal</b>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  var subjects = <?php echo json_encode($subject_names); ?>;
var totals = <?php echo json_encode($subject_total); ?>;
var ctx=document.getElementById("resultChart");
new Chart(ctx,{
  type:'bar',
  data:{
    labels:subjects,
    datasets:[{
      label:'Total Marks',
      data: totals,
backgroundColor:'#4e73df'
    }]
  },

options:{
scales:{
y:{
beginAtZero:true,
max:200
}
}
}
});
</script>
