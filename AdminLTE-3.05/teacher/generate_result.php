<?php
include('includes/config.php');
include('includes/functions.php');
require '../../vendor/autoload.php';


if(isset($_GET['all_id'])){
    $std_id=$_GET['all_id'];
    $semester_id=$_GET['sem_id'];
}

// now created the verify token
// $verify_token=md5($std_id,$semester_id,time());
// $insert_token=mysqli_query($con,"INSERT INTO `result_verification` (std_id,sem_id,verify_token) VALUE('$std_id','$semester_id','$verify_token')");
// now create the verification token
// $verification_url="https://"

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

$get_principal_id=mysqli_query($con,"SELECT id FROM `accounts` WHERE type='principal'");
$row_fetch=mysqli_fetch_assoc($get_principal_id);
$pid=$row_fetch['id'];
$get_sign=mysqli_query($con,"SELECT meta_value FROM `usermeta` WHERE user_id='$pid' AND meta_key='Principal_sign'");
$row=mysqli_fetch_assoc($get_sign);
$p_sign=$row['meta_value'];

$get_teacher=mysqli_query($con,"SELECT user_id FROM usermeta WHERE meta_key='teacher_sign' LIMIT 1");
$row_teacher=mysqli_fetch_assoc($get_teacher);
$teacher_id=$row_teacher['user_id'];

$get_teacher_sign=mysqli_query($con,"SELECT meta_value FROM usermeta WHERE user_id='$teacher_id' AND meta_key='teacher_sign'");
$row_sign=mysqli_fetch_assoc($get_teacher_sign);
$t_sign=$row_sign['meta_value'];

$pdf=new TCPDF();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetAlpha(0,1);
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
$pdf->Cell(63, 7, "Student Name: $std_name", 0, 0, 'L'); // width = 63mm
$pdf->Cell(63, 7, "Roll Number: $std_id", 0, 0, 'L');
$pdf->Cell(63, 7, "Semester: $semester_id", 0, 1, 'L'); // 1 = new line

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(63, 7, "Class Name: $class_name", 0, 0, 'L'); // width = 63mm
$pdf->Cell(63, 7, "Section Name: $section_name", 0, 0, 'L');

$pdf->Ln(15);
$html = '<table border="1" cellpadding="4" width="100%" align="center">
<thead>
<tr>
<th>Subject Name</th>
<th>Mark in Term 1<br>(100)</th>
<th>Mark in Term 2<br>(100)</th>
<th>Total Marks<br>(200)</th>
</tr>
</thead>
<tbody>';
        
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
$html .= '<tr>
    <td>'.$subject['name'].'</td>
    <td>'.$marks.'</td>
    <td>'.$marks2.'</td>
   <td>'.$marks+$marks2.'</td>
    </tr>';
}

$html .='<tr>
<td><h5>Total</h5></td>
<td><h3>'.$term1.'</h3></td>
<td><h3>'.$term2.'</h3></td>
<td><h3>'.$score.'</h3></td>
</tr>';
$html .= '</tbody></table>';
$total_marks=$count*200;
$html .="<p>Total Marks:-$total_marks</p>";
$html .="<p>Score:-$score</p>";
$html .="<p>Percentage:-$percentage%</p>";
$html .="<p>Grade:-$grade</p>";


$subjects = $subject_names;
$marks = $subject_total;

$width = 450;
$height = 300;

$image = imagecreate($width,$height);

$white = imagecolorallocate($image,255,255,255);
$blue = imagecolorallocate($image,0,102,204);
$black = imagecolorallocate($image,0,0,0);

$x = 60;
$maxMarks = 200;
foreach($marks as $i=>$m){


$bar = ($m / $maxMarks) * ($height - 40);

imagefilledrectangle($image,$x,$height-$bar,$x+40,$height-20,$blue);

imagestring($image,3,$x,$height-$bar-35,$m,$black);
imagestring($image,3,$x,$height-15,$subjects[$i],$black);

$x += 80;
}

imagepng($image,"graph.png");
imagedestroy($image);
$html .='<br><span style="font-weight:bold;">Score Mark Graph:-</span>';

$pdf->writeHTML($html);



$y = $pdf->GetY();   // space reduce
$pdf->Image("graph.png",10,$y,100);
$pdf->Ln(80);

$pdf->writeHTML("<p><b>Remark:</b>$remark</p>");
$html_sign='
<table width="100%" style="margin-top:50px;">
<tr>

<td width="50%" align="left">
<img src="uploads/'.$t_sign.'" height="60" ><br>
<b>Class Teacher</b>
</td>

<td width="50%" align="center">
<img src="uploads/'.$p_sign.'" height="60"><br>
<b>Principal</b>
</td>

</tr>
</table>';
$pdf->SetY(-48);
$pdf->writeHTML($html_sign);

$pdf->Output('result_report.pdf','I');
?>