<?php
session_start();

include('includes/config.php');
include('includes/functions.php');

require '../../vendor/autoload.php';
include('pdf_header.php');

/* =========================
   BASIC DATA
========================= */

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$year = date('Y');
$next_year = $year + 1;
$academic_year = $year . "-" . $next_year;

/* =========================
   GET DATA
========================= */

$exam_id     = $_GET['exam_id'] ?? '';
$student_id  = $_GET['student_id'] ?? '';

/* =========================
   COLLEGE DATA
========================= */

$session_id  = $_GET['session'] ?? '';
$semester_id = $_GET['semester'] ?? '';
$course_id   = $_GET['course_id'] ?? '';
$branch_id   = $_GET['branch_id'] ?? '';

/* =========================
   SCHOOL DATA
========================= */

$class_id         = $_GET['class_id'] ?? '';
$section_id       = $_GET['section_id'] ?? '';
$academic_session = $_GET['academic_session'] ?? '';

/* =========================
   COLLEGE SESSION
========================= */

if($institute_type == 'college'){

    $session_q = mysqli_query($con,"
    SELECT title 
    FROM posts 
    WHERE id='$session_id'
    ");

    $row_session = mysqli_fetch_assoc($session_q);

    $academic_session = $row_session['title'] ?? '';
}

/* =========================
   STUDENT DATA
========================= */

$student_q = mysqli_query($con,"
SELECT * 
FROM accounts 
WHERE id='$student_id'
AND institute_id='$institute_id'
");

$student = mysqli_fetch_assoc($student_q);

$name    = $student['Name'] ?? '';
$roll_no = $student['roll_no'] ?? '';

$father_name = get_usermeta($student_id,'father_name');
$mother_name = get_usermeta($student_id,'mother_name');

$photo = get_usermeta($student_id,'student_photo');

$st_photo = __DIR__."/uploads/student_photo/".$photo;

/* =========================
   EXAM NAME
========================= */

$get_exam = mysqli_query($con,"
SELECT et.exam_type
FROM create_exam ce

LEFT JOIN exam_type et
ON ce.exam_type_id = et.id

WHERE ce.id='$exam_id'
");

$row_exam = mysqli_fetch_assoc($get_exam);

$exam_name = $row_exam['exam_type'] ?? '';

/* =========================
   PRINCIPAL SIGN
========================= */

$p_sign = '';

$select_priid = mysqli_query($con,"
SELECT id 
FROM accounts 
WHERE type='principal'
AND institute_id='$institute_id'
");

if(mysqli_num_rows($select_priid) > 0){

    $row_prin = mysqli_fetch_assoc($select_priid);

    $pid = $row_prin['id'];

    $q = mysqli_query($con,"
    SELECT meta_value 
    FROM usermeta 
    WHERE user_id='$pid' 
    AND meta_key='principle_sign'
    ");

    if(mysqli_num_rows($q) > 0){

        $row_sign = mysqli_fetch_assoc($q);

        if(!empty($row_sign['meta_value'])){

            $p_sign = __DIR__."/uploads/sign/".$row_sign['meta_value'];
        }
    }
}

/* =========================
   CREATE PDF
========================= */

$pdf = new TCPDF();

$pdf->SetCreator('DPS ERP');
$pdf->SetAuthor('DPS ERP');
$pdf->SetTitle('Admit Card');

$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(TRUE, 10);

$pdf->AddPage();

/* =========================
   HTML START
========================= */

$html = '

<style>

body{
    font-family:helvetica;
}

.main-card{
    border:1px solid #dcdcdc;
    padding:20px;
}

.header-title{
    text-align:center;
    color:#0b3d91;
}

.student-table{
    width:100%;
}

.student-info{
    font-size:15px;
    line-height:28px;
}

.photo-box img{
    width:140px;
    height:170px;
    border-radius:8px;
    object-fit:cover;
}

.exam-heading{
    text-align:center;
    color:#0b3d91;
    margin-top:20px;
}

.exam-table{
    width:100%;
    border-collapse:collapse;
}

.exam-table th{
    background-color:#0b3d91;
    color:white;
    padding:10px;
    font-size:14px;
}

.exam-table td{
    padding:10px;
    font-size:13px;
}

.instruction-heading{
    color:red;
    margin-top:20px;
}

.signature-box{
    text-align:right;
    margin-top:50px;
}

.signature-box img{
    height:80px;
}

</style>

<div class="main-card">

<h1 class="header-title"><u>Admit Card</u></h1>

<h2 class="header-title"><u>'.$academic_year.'</u></h2>

<br>

<table class="student-table">

<tr>

<td width="70%" class="student-info">

<b>Student Name:</b> '.ucfirst($name).'<br>
';

/* =========================
   COLLEGE / SCHOOL
========================= */

if($institute_type == 'college'){

$html .= '
<b>Roll Number:</b> '.$roll_no.'<br>
';

}else{

$html .= '
<b>Student ID:</b> '.$roll_no.'<br>
';
}

$html .= '

<b>Session:</b> '.$academic_session.'<br>

<b>Father Name:</b> '.ucfirst($father_name).'<br>

<b>Mother Name:</b> '.ucfirst($mother_name).'<br>

<b>Exam:</b> '.$exam_name.'<br>
';

if($institute_type == 'college'){

$html .= '
<b>Semester:</b> '.$semester_id.'<br>
';
}

$html .= '

</td>

<td width="30%" align="right" class="photo-box">
';

/* =========================
   PHOTO
========================= */

if(!empty($photo) && file_exists($st_photo)){

$html .= '
<img src="'.$st_photo.'">
';
}

$html .= '

</td>

</tr>

</table>

<br><br>

<h2 class="exam-heading"><u>Exam Date Sheet</u></h2>

<br>

<table border="1" class="exam-table">

<tr>
    <th width="28%"><b>Subject</b></th>
    <th width="20%"><b>Date</b></th>
    <th width="17%"><b>Start</b></th>
    <th width="17%"><b>End</b></th>
    <th width="18%"><b>Duration</b></th>
</tr>
';

/* =========================
   DATE SHEET QUERY
========================= */

if($institute_type == 'college'){

$datesheet = mysqli_query($con,"
SELECT ed.*, p.title
FROM exam_datesheet ed

JOIN posts p
ON ed.subject_id = p.id

WHERE ed.exam_id='$exam_id'
AND ed.course_id='$course_id'
AND ed.branch_id='$branch_id'
AND ed.semester_id='$semester_id'
AND ed.session_id='$session_id'
AND ed.institute_id='$institute_id'
");

}else{

$datesheet = mysqli_query($con,"
SELECT ed.*, p.title
FROM exam_datesheet ed

JOIN posts p
ON ed.subject_id = p.id

WHERE ed.exam_id='$exam_id'
AND ed.class_id='$class_id'
AND ed.section_id='$section_id'
AND ed.institute_id='$institute_id'
");
}

/* =========================
   DATE SHEET DATA
========================= */

if(mysqli_num_rows($datesheet) > 0){

while($d = mysqli_fetch_assoc($datesheet)){

$html .= '

<tr>
    <td>'.$d['title'].'</td>
    <td>'.$d['exam_date'].'</td>
    <td>'.$d['start_time'].'</td>
    <td>'.$d['end_time'].'</td>
    <td>'.$d['duration'].'</td>
</tr>
';
}

}else{

$html .= '

<tr>
    <td colspan="5" align="center">
        No Exam Datesheet Found
    </td>
</tr>
';
}

$html .= '
</table>

<br><br>

<h3 class="instruction-heading">Exam Instructions:</h3>

<ol>
';

/* =========================
   INSTRUCTIONS
========================= */

$instruction_q = mysqli_query($con,"
SELECT question 
FROM feedback_questions 

WHERE institute_id='$institute_id'
AND type='exam'
");

while($ins = mysqli_fetch_assoc($instruction_q)){

$html .= '
<li>'.$ins['question'].'</li>
';
}

$html .= '

</ol>

<div class="signature-box">
';

/* =========================
   SIGNATURE
========================= */

if(!empty($p_sign) && file_exists($p_sign)){

$html .= '
<img src="'.$p_sign.'"><br>
';
}

$html .= '

<div style="border-top:1px solid #000;width:200px;float:right;"></div>

<br><br>

<b>Controller of Examination</b><br>

<small>Authorized Signature</small>

</div>

</div>
';

/* =========================
   WRITE HTML
========================= */

$pdf->writeHTML($html, true, false, true, false, '');

/* =========================
   CREATE FOLDER
========================= */

$folder = __DIR__."/uploads/admit/";

if (!file_exists($folder)) {

    mkdir($folder, 0777, true);
}

/* =========================
   FILE NAME
========================= */

$file_name = "admit_".$student_id."_".time().".pdf";

$file_path = $folder.$file_name;

/* =========================
   SAVE PDF
========================= */

$pdf->Output($file_path, 'F');

/* =========================
   SAVE DATABASE
========================= */

$db_path = "uploads/admit/".$file_name;

$check = mysqli_query($con,"
SELECT * 
FROM admit_cards 

WHERE student_id='$student_id'
AND exam_id='$exam_id'
AND session='$academic_year'
");

if(mysqli_num_rows($check) == 0){

mysqli_query($con,"
INSERT INTO admit_cards
(
student_id,
institute_id,
exam_id,
session,
pdf_path
)

VALUES
(
'$student_id',
'$institute_id',
'$exam_id',
'$academic_year',
'$db_path'
)
");
}

/* =========================
   FORCE DOWNLOAD
========================= */

ob_end_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'.$file_name.'"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output($file_name, 'D');

exit;

?>