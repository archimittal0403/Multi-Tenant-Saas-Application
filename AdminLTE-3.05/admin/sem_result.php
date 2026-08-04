<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>


<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
if(isset($_GET['sem_id'])){
  $sem_id=$_GET['sem_id'];
}
?>
<div class="content-header">

         <div class="container-fluid">
       
        <div class="row ">

          <div class="col-sm-6">
<div class="d-flex">
            <h1 class="m-0 text-dark">Semester Report :-</h1>
          <a href="generate_semReport.php? sem_id=<?php echo $sem_id ?>" name="generate_report" class="btn btn-success btn-sm mx-4">Genrate PDF</a>
            <!-- <a href="feedback.php?&action=add-new"
   class="btn btn-primary btn-sm mx-4">Fill feedback</a> -->
</div>
</div>
             <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Attendance</a></li>
              <li class="breadcrumb-item active">semester</li>
            </ol>
          </div><!-- /.col -->


</div>
</div>
</div>
<?php
if(isset($_GET['sem_id'])){
  $sem_id=$_GET['sem_id'];
}

$count=0;
  $passcount=0;
  $failcount=0;
  $highestpercetage=0;
$student=mysqli_query($con,"SELECT user_id FROM usermeta WHERE meta_key='semester' AND meta_value='$sem_id'");
while($row_fetch=mysqli_fetch_assoc($student)){

$user_id=$row_fetch['user_id'];

$std=mysqli_query($con,"SELECT id,Name FROM accounts WHERE id='$user_id' AND type='student'");
$row=mysqli_fetch_assoc($std);
if($row){
$count++;

$select=mysqli_query($con,"SELECT COUNT(*) as TotalSubject,
 SUM(m.marks) as Score,
 SUM(CASE WHEN r.term_id=1 THEN m.marks ELSE 0 END) as term1,
 SUM(CASE WHEN r.term_id=2 THEN m.marks ELSE 0 END) as term2
 FROM result r
 JOIN result_marks m ON r.result_id=m.result_id
 Where student_id='$user_id'");
while($row_fetch=mysqli_fetch_assoc($select)){
$TotalSubject=$row_fetch['TotalSubject'];
$score=$row_fetch['Score'];
$term1=$row_fetch['term1'];
$term2=$row_fetch['term2'];
$percentage=round(($score/($TotalSubject*100))*100);

if($percentage>35){

  $result="Pass";
  $passcount++;
}
else{
  $result="Fail";
  $failcount++;
}
if($percentage<=60){
  $grade="D";
}
else if($percentage<=70){
  $grade="C";
}
else if($percentage<=80){
  $grade="B";
}
else if($percentage<=90){
  $grade="A";
}
else if($percentage==100){
  $grade="A+";
}

if($percentage>$highestpercetage){
  $highestpercetage=$percentage;
  $topper_id=$row['id'];
  $topper_name=$row['Name'];
}
$students[]=[
'id'=>$row['id'],
'name'=>$row['Name'],
'term1'=>$term1,
'term2'=>$term2,
'score'=>$score,
'percentage'=>$percentage,
'result'=>$result,
'grade'=>$grade
];
}}}
?>

<div class="card-body">
  <h5>Total Student:-<?php echo $count ;?></h5>
  <h5>Total Pass Student:-<?php echo $passcount ;?></h5>
  <h5>Total Fail Student:-<?php echo $failcount ;?></h5>
  <h5>Highest Percentage:-<?php echo $highestpercetage."%"; ?></h5>
  <h5>Topper's Roll NO:- <?php echo $topper_id ; ?></h5>
  <h5>Topper's Name:-<?php echo ucfirst($topper_name) ; ?></h5>
<table class="table table-bordered w-100 mt-3">
<thead>
<tr>
<th>Sno</th>
<th>Enroll_ID</th>
<th>Student_Name</th>
<th>Term1</th>
<th>Term2</th>
<th>Scored Marks</th>
<th>Percentage</th>
<th>Result</th>
<th>Grade</th>
</tr>
</thead>

<tbody>



<tr>
  <?php foreach($students as $std){?>
<td><?php echo $count ?></td>
<td><?php echo $std['id'] ?></td>
<td><?php echo $std['name'] ?></td>
<td><?php echo $std['term1'] ?></td>
<td><?php echo $std['term2'] ?></td>
<td><?php echo $std['score'] ?></td>
<td><?php echo $std['percentage'].'%' ?></td>
<td><?php echo $std['result'] ?></td>
<td><?php echo $std['grade'] ?></td>

</tr>

<?php  } ?>


</tbody>
</table>
</div>