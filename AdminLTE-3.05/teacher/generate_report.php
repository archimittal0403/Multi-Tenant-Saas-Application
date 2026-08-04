<?php
include('includes/config.php');
require '../../vendor/autoload.php';

$class_id=$_GET['class'] ?? '';
$section_id=$_GET['section'] ?? '';
$semester_id=$_GET['semester'] ?? '';
$class=mysqli_query($con,"SELECT title FROM `posts` WHERE id='$class_id'");
$row_class=mysqli_fetch_assoc($class);
$class_name=$row_class['title'];

// fetch section
$section=mysqli_query($con,"SELECT title FROM `section` WHERE id='$section_id'");
$row_section=mysqli_fetch_assoc($section);
$section_name=$row_section['title'];
$sql="SELECT * FROM accounts WHERE type='student'";

//semester fetching

if($class_id!=''){
$sql.=" AND id IN (SELECT user_id FROM usermeta WHERE meta_key='st_class' AND meta_value='$class_id')";
}

if($section_id!=''){
$sql.=" AND id IN (SELECT user_id FROM usermeta WHERE meta_key='st_section' AND meta_value='$section_id')";
}

if($semester_id!=''){
$sql.=" AND id IN (SELECT user_id FROM usermeta WHERE meta_key='semester' AND meta_value='$semester_id')";
}

$select=mysqli_query($con,$sql);

$pdf = new TCPDF();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('helvetica','',12);

$html="<h2 style='text-align:center;margin-bottom:20px;'>Attendance Report:-</h2>";
$html .="<p>Class:-&nbsp;$class_name &nbsp; &nbsp; &nbsp; &nbsp; Section:-&nbsp;$section_name &nbsp; &nbsp; &nbsp; &nbsp;Semester:-$semester_id</p>

";
$html .='
<style>

table{
border-collapse:collapse;
}

th{
border:1px solid black;   /* vertical + horizontal */

}

td{
border-left:1px solid black;
border-right:1px solid black;
padding:8px;
}

</style>
';
$html.="<table border='1' cellpadding='6'>
<tr>
<th width='10%'>Sno</th>
<th width='30%'>Enroll_ID</th>
<th width='30%'>Student_Name</th>
<th width='30%'>Percentage</th>
</tr>";

$count=1;

while($row_fetch=mysqli_fetch_assoc($select)){

$student_name=$row_fetch['Name'];
$enroll_id=$row_fetch['id'];

$select_status=mysqli_query($con,"
SELECT COUNT(*) as TotalClass,
SUM(status='p') as PresentClass,
(SUM(status='p')/COUNT(*))*100 as Percentage
FROM attendance
WHERE user_id='$enroll_id'
");

$row=mysqli_fetch_assoc($select_status);

$percentage=round($row['Percentage']);

$html.="<tr>
<td>".$count++."</td>
<td>$enroll_id</td>
<td>$student_name</td>
<td>$percentage%</td>
</tr>";
}

$html.="</table>";

$pdf->writeHTML($html);

$pdf->Output('attendance_report.pdf','I');
?>