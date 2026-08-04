<?php 
ob_start();
include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php require '../../vendor/autoload.php'; ?>
<?php
if(isset($_GET['exam_id'])){
    $exam_id=$_GET['exam_id'];
 
  $select_sem=mysqli_query($con,"SELECT semester_id FROM `create_exam` WHERE id='$exam_id'");
  $row=mysqli_fetch_assoc($select_sem);
  $sem=$row['semester_id'];
}
?>
<?php
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

$pdf->Ln(9); 
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(20, 7, "Exam Name: $exam_id", 0, 0, 'C'); // 1 = new line
$pdf->Cell(0, 7, "Semester: $sem", 0, 0, 'C'); // 1 = new line

$html .= '
<table cellpadding="6" cellspacing="0" width="100%">
<thead>
<tr>
<th style="border:1px solid #000;" align="center"><b>Sno</b></th>
<th style="border:1px solid #000;" align="center"><b>Enroll_ID</b></th>
<th style="border:1px solid #000;" align="center"><b>Student_Name</b></th>
<th style="border:1px solid #000;" align="center"><b>Fee Status</b></th>
</tr>
</thead>
<tbody>
';
$count=0;
$select_sem = mysqli_query($con,"SELECT semester_id 
                                 FROM create_exam 
                                 WHERE id='$exam_id'");
$row = mysqli_fetch_assoc($select_sem);
$sem_id = $row['semester_id'];

$q = mysqli_query($con,"
    SELECT a.Name,a.id
    FROM accounts a
    JOIN usermeta u ON a.id = u.user_id
    WHERE a.type='student'
    AND u.meta_key='semester'
    AND u.meta_value='$sem_id'
");

while($data = mysqli_fetch_assoc($q)){
   $student_id=$data['id'];
    $Name=$data['Name'];
    $count++;
    //$id=$data['id'];
    $father_name=get_usermeta($student_id,'father_name');
$mother_name=get_usermeta($student_id,'mother_name');
$check=mysqli_query($con,"SELECT exam_id,student_id FROM `admit_card` WHERE exam_id='$exam_id' AND student_id='$student_id'");
if(mysqli_num_rows($check)>0){

}
else{
 

$html .='<tr>
<td style="border:1px solid #000;" align="center">'.$count.'</td>
<td style="border:1px solid #000;" align="center">'.$student_id.'</td>
<td style="border:1px solid #000;">'.ucfirst($Name).'</td>
<td style="border:1px solid #000;" align="center">Pending</td>
</tr>';

}
}
$html .= '</tbody></table>';
$pdf->SetY(55);
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("semester_report.pdf","I");
