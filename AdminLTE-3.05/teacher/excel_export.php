<?php
include("includes/config.php");
 include('includes/functions.php');
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=semester_report.xls");
// excel header row
echo "Subject Name\tT1_ResultID\tMark in Term1\tT2_ResultID\tMark in Term2\tTotal Marks\n";

if(isset($_GET['all_id'])){
    $std_id=$_GET['all_id'];
    $semester_id=$_GET['sem_id'];
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
foreach($subjects as $subject){
    $subject_id=$subject['id'];
    $subject_name=$subject['name'];
    // selext the term 1 mark
    $q1=mysqli_query($con,"SELECT r.result_id,m.marks
    FROM result r JOIN result_marks m ON r.result_id=m.result_id
    WHERE r.class_id='$class'
    AND r.section_id='$section'
    AND r.subject_id='$subject_id'
    AND r.term_id='1'
    AND m.student_id='$std_id'");
    $row1=mysqli_fetch_assoc($q1);
    $marks1=$row1['marks'] ?? 0;
$result1_id=$row1['result_id'] ?? 0;
// term 2 mark
$q2=mysqli_query($con,"SELECT r.result_id,m.marks
FROM result r JOIN result_marks m ON r.result_id=m.result_id
WHERE r.class_id='$class'
AND r.section_id='$section'
    AND r.subject_id='$subject_id'
AND r.term_id='2'
AND m.student_id='$std_id'");
$row2=mysqli_fetch_assoc($q2);
$marks2=$row2['marks'] ?? 0;
$total=$marks1+$marks2;
$result2_id=$row2['result_id'] ?? 0;
echo  $subject_name."\t".
 $result1_id."\t".
         $marks1."\t".
$result2_id."\t".
         $marks2."\t".
         $total."\n";
}
$select = mysqli_query($con,"SELECT 
COUNT(DISTINCT r.subject_id) as TotalSubject,
SUM(m.marks) as Score,
SUM(CASE WHEN r.term_id=1 THEN m.marks ELSE 0 END) as term1,
SUM(CASE WHEN r.term_id=2 THEN m.marks ELSE 0 END) as term2
FROM result r 
JOIN result_marks m ON r.result_id=m.result_id
WHERE m.student_id='$std_id'");

$row = mysqli_fetch_assoc($select);
$TotalSubjects = $row['TotalSubject'];
$score = $row['Score'];
$term1 = $row['term1'];
$term2 = $row['term2'];
$maxMarks = $TotalSubjects * 200;
$percentage = round(($score / $maxMarks) * 100, 2);


// ===== Grade & Remark =====
if($percentage <= 60){
    $grade='D';
    $remark="Capable of higher grades; improved focus will boost performance.";
}
else if($percentage <= 70){
    $grade='C';
    $remark="Capable of achieving higher results with more in-depth study.";
}
else if($percentage <= 80){
    $grade='B';
    $remark="Solid understanding of the material; great potential to reach the next level.";
}
else if($percentage <= 90){
    $grade='A';
    $remark="Outstanding performance and dedication to learning.";
}
else if($percentage == 100){
    $grade='A+';
    $remark="An impeccable result reflecting dedication and attention to detail.";
}


// ===== Summary in Excel =====
echo "\n";

echo "Term 1 Total:\t".$term1."\n";
echo "Term 2 Total:\t".$term2."\n";
echo "Grand Total:\t".$score."\n";
echo "Percentage:\t".$percentage."%\n";

?>
