<?php 
ob_start();
include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php require '../../vendor/autoload.php'; ?>

<?php
if(isset($_GET['sem_id'])){
  $sem_id=$_GET['sem_id'];

}
$students=[];
$count=0;
  $passcount=0;
  $failcount=0;
  $highestpercetage=0;
$student=mysqli_query($con,"SELECT user_id FROM usermeta WHERE meta_key='semester' AND meta_value='$sem_id'");
while($row_select=mysqli_fetch_assoc($student)){

$user_id=$row_select['user_id'];
$td=mysqli_query($con,"SELECT id,Name FROM `accounts` WHERE type='teacher'");
$tdrow=mysqli_fetch_assoc($td);
$tname=$tdrow['Name'];

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
$get_teacher=mysqli_query($con,"SELECT user_id FROM usermeta WHERE meta_key='teacher_sign' LIMIT 1");
$row_teacher=mysqli_fetch_assoc($get_teacher);
$teacher_id=$row_teacher['user_id'];

$get_teacher_sign=mysqli_query($con,"SELECT meta_value FROM usermeta WHERE user_id='$teacher_id' AND meta_key='teacher_sign'");
$row_sign=mysqli_fetch_assoc($get_teacher_sign);
$t_sign=$row_sign['meta_value'];

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

$pdf=new TCPDF();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetAlpha(0.1);
$pdf->Image('uploads/akglogo.png',50,49,122);
$pdf->SetAlpha(1);
$html="";
$logo="uploads/akglogo.png";
$pdf->image($logo, 10,10,25,25);
$pdf->SetXY(90, 10); // X=40 means right of logo (logo width = 25 + 10 margin = 35, thoda aur space)
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 8, 'Ajay Kumar Garg Engineering', 0, 1, 'L');

// Shift Y a bit down if you want it center aligned with logo
$pdf->SetXY(90, 17); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(0, 6, 'Krishan Ganj, Pilkhuwa, Hapur', 0, 1, 'L');
$pdf->SetXY(90, 24); 
$pdf->Cell(0, 6, 'Phone: 2387688463, 765984642', 0, 1, 'L');

$pdf->Ln(10); 


$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(63, 7, "Semester: $sem_id", 0, 0, 'L'); // 1 = new line
$pdf->Cell(68, 7, "Teacher name: $tname", 0, 0, 'L'); // 1 = new line
$pdf->Ln(15);
$html = '
<h3 align="center">Semester Report</h3>

<table cellpadding="4">
<tr>
<td><b>Total Student:</b> '.$count.'</td>
<td><b>Total Pass Student:</b> '.$passcount.'</td>
</tr>

<tr>
<td><b>Total Fail Student:</b> '.$failcount.'</td>
<td><b>Highest Percentage:</b> '.$highestpercetage.'%</td>
</tr>

<tr>
<td><b>Topper Roll No:</b> '.$topper_id.'</td>
<td><b>Topper Name:</b> '.ucfirst($topper_name).'</td>
</tr>
</table>

<br><br>
';
$html .= '
<table border="1" cellpadding="4" width="100%" margin-top="12px" align="center">
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
<tbody>';
$i=1;
foreach($students as $std){

$html .= '<tr>
<td>'.$i++.'</td>
<td>'.$std['id'].'</td>
<td>'.$std['name'].'</td>
<td>'.$std['term1'].'</td>
<td>'.$std['term2'].'</td>
<td>'.$std['score'].'</td>
<td>'.$std['percentage'].'%</td>
<td>'.$std['result'].'</td>
<td>'.$std['grade'].'</td>
</tr>';

}
$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$html_sign='
<table width="100%" style="margin-top:50px;">
<tr>

<td width="50%" align="left">
<img src="uploads/'.$t_sign.'" height="60" ><br>
<b>Class Teacher</b>
</td>
</tr>
</table>';
$pdf->SetY(-87);
$pdf->writeHTML($html_sign);
$pdf->Output("semester_report.pdf","I");