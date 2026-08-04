<?php
session_start();
include('includes/config.php');
include('includes/functions.php');

include('pdf_header.php');
require '../../vendor/autoload.php';
// GET DATA
$student_id  = $_GET['student_id'];
$exam_id     = $_GET['exam_id'];
$session_id  = $_GET['session'];
$semester_id = $_GET['semester'];
$class_id=$_GET['class_id'];
$section_id=$_GET['section_id'];
$course_id   = $_GET['course_id'];
$branch_id   = $_GET['branch_id'];

$institute_id = $_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
$year = date('Y');
$next_year = $year + 1;
$academic_year = $year . "-" . $next_year;

// STUDENT DATA
$q = mysqli_query($con,"SELECT * FROM accounts WHERE id='$student_id'");
$row = mysqli_fetch_assoc($q);

$name = $row['Name'];
$roll = $row['roll_no'];

$father = get_usermeta($student_id,'father_name');
$mother = get_usermeta($student_id,'mother_name');

$photo = get_usermeta($student_id,'student_photo');
$st_photo = dirname(__DIR__)."/admin/uploads/student_photo/".$photo;
$st_photo = str_replace('\\', '/', $st_photo);
// signature
 $sp = mysqli_query($con,"SELECT id FROM accounts WHERE type='principal' AND institute_id='$institute_id'");
 $prow = mysqli_fetch_assoc($sp);
$pid = $prow['id'];
$sign_q = mysqli_query($con,"SELECT meta_value FROM usermeta WHERE user_id='$pid' AND meta_key='pricipal_sign'");
$sign_row = mysqli_fetch_assoc($sign_q);

$p_sign = dirname(__DIR__)."/admin/uploads/sign/".$sign_row['meta_value'];
$p_sign = str_replace('\\', '/', $p_sign);

// SESSION
$sq = mysqli_query($con,"SELECT title FROM posts WHERE id='$session_id'");
$srow = mysqli_fetch_assoc($sq);
$academic_session = $srow['title'];

// EXAM
$eq = mysqli_query($con,"SELECT exam_type FROM exam_type WHERE id='$exam_id'");
$erow = mysqli_fetch_assoc($eq);
$exam_name = $erow['exam_type'];



// CREATE PDF
$pdf = new TCPDF();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$institute = getInstitute($con, $institute_id);

$logo = $institute['logo'];

$logoPath = $_SERVER['DOCUMENT_ROOT']
.'/student management/AdminLTE-3.05/admin/uploads/logo/'
.$logo;

if(!empty($logo) && file_exists($logoPath)){

    $pdf->SetAlpha(0.07);

    $pdf->Image(
        $logoPath,
        55,
        80,
        100,
        100,
        'PNG',
        '',
        '',
        false,
        300,
        '',
        false,
        false,
        0,
        false,
        false,
        true
    );

    $pdf->SetAlpha(1);
}

$pdf->SetCreator('Institute');
$pdf->SetAuthor('Institute');
$pdf->SetTitle('Admit Card');
$institute = getInstitute($con, $institute_id);

$header = generatePDFHeader($institute);
// HTML START
$html = '

<!-- HEADER -->
<div style="background-color:#0b3d91;color:white;text-align:center;padding:10px;">
    <h2>ADMIT CARD</h2>
       <span>'.$academic_year.'</span>
</div>

<br>

<!-- STUDENT INFO -->
<table cellpadding="8" width="100%" style="font-size:12px;">
<tr>
<td width="60%">
<b><span style="font-size:17px;">Name</span>:</b> &nbsp;&nbsp;&nbsp; 
<span style="font-size:17px;">'.$name.'</span><br>

<b><span style="font-size:17px;">Roll No</span>:</b> &nbsp;&nbsp;&nbsp; <span style="font-size:17px;">'.$roll.'</span><br>

<b><span style="font-size:17px;">Father</span>:</b> &nbsp;&nbsp;&nbsp; <span style="font-size:17px;">'.$father.'</span><br>

<b><span style="font-size:17px;">Mother</span>:</b> &nbsp;&nbsp;&nbsp; <span style="font-size:17px;">'.$mother.'</span><br>

<b><span style="font-size:17px;">Exam</span>:</b> &nbsp;&nbsp;&nbsp; <span style="font-size:17px;">'.$exam_name.'</span><br>

<b><span style="font-size:17px;">Academic Session</span>:</b> &nbsp;&nbsp;&nbsp; <span style="font-size:17px;">'.$academic_session.'</span><br>

<b><span style="font-size:17px;">Semester</span>:</b> &nbsp;&nbsp;&nbsp; <span style="font-size:17px;">'.$semester_id.'</span>

</td>
</tr>
</table>

<br>

<!-- DATE SHEET HEADER -->
<div style="background-color:#0b3d91;color:white;padding:5px;">
<b>Exam Date Sheet</b>
</div>

<table border="1" cellpadding="8" width="100%" style="font-size:12px;">
<tr style="background-color:#d9e3f0;">
<th><b>Subject</b></th>
<th><b>Date</b></th>
<th><b>Start</b></th>
<th><b>End</b></th>
<th><b>Duration</b></th>
</tr>
';

// DATE SHEET
$ds = mysqli_query($con,"SELECT ed.*, p.title 
FROM exam_datesheet ed
JOIN posts p ON ed.subject_id=p.id
WHERE ed.exam_id='$exam_id'
AND ed.course_id='$course_id'
AND ed.branch_id='$branch_id'
AND ed.semester_id='$semester_id'
AND ed.session_id='$session_id'
");

while($d = mysqli_fetch_assoc($ds)){
    $html .= '
    <tr>
        <td>'.$d['title'].'</td>
        <td>'.$d['exam_date'].'</td>
        <td>'.$d['start_time'].'</td>
        <td>'.$d['end_time'].'</td>
            <td>'.$d['duration'].'</td>
    </tr>';
}

$html .= '</table>';


// ✅ EXAM INSTRUCTIONS
$html .= '
<br>
<div style="background-color:#0b3d91;color:white;padding:5px;">
<b>Exam Instructions</b>
</div>

<ol>
';

// FETCH INSTRUCTIONS
$ins = mysqli_query($con,"SELECT question FROM feedback_questions 
WHERE type='exam'
AND course_id='$course_id'
AND branch_id='$branch_id'
AND session='$session_id'
AND semester='$semester_id'
AND institute_id='$institute_id'");

while($i = mysqli_fetch_assoc($ins)){
    $html .= '<li>'.$i['question'].'</li>';
}

$html .= '</ol>';
// SIGNATURE
$html .= '
<br><br>
<table width="100%">
<tr>
<td></td>

</tr>
</table>
';
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTML($header.$html, true, false, true, false, '');

// Student Photo
if(file_exists($st_photo)){
$headerHeight = 20; // approx header height

$pdf->Image(
    $st_photo,
    160,
    58 + $headerHeight,
    39,
    39
);
}

// Signature
$headerHeight = 20; 
if(file_exists($p_sign)){
    $pdf->Image($p_sign, 145, 180+$headerHeight, 35, 15);

    $pdf->SetXY(135, 195+$headerHeight);
    $pdf->Cell(50,5,'____________________',0,1,'C');

    $pdf->SetXY(135, 200+$headerHeight);
    $pdf->Cell(50,5,'Controller of Examination',0,1,'C');
}


// FILE SAVE
$file_name = "admit_".$student_id."_".time().".pdf";

// create folder if not exist
$folder = __DIR__."/uploads/admit/";
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
}

// absolute path
$file_path = $folder.$file_name;

// SAVE PDF
$pdf->Output($file_path, 'F');

//SAVE DB (relative path)
$db_path = "uploads/admit/".$file_name;
$check = mysqli_query($con,"SELECT * FROM admit_cards 
WHERE student_id='$student_id'
AND exam_id='$exam_id'
AND session='$academic_year'");

if(mysqli_num_rows($check) > 0){

    $row = mysqli_fetch_assoc($check);

    // already exist → same PDF open
    header("Location: ".$row['pdf_path']);
    exit;
}

if($institute_type=='college'){
mysqli_query($con,"INSERT INTO admit_cards(student_id,institute_id,exam_id,session,pdf_path,course_id,branch_id,academic_session,semester)
VALUES('$student_id','$institute_id','$exam_id','$academic_year','$db_path','$course_id','$branch_id','$session_id','$semester_id')");
}
else{
mysqli_query($con,"INSERT INTO admit_cards(student_id,institute_id,exam_id,session,pdf_path,class,section,academic_session)
VALUES('$student_id','$institute_id','$exam_id','$academic_year','$db_path','$class_id','$section_id','$session_id')");
}
header("Location: download_admit.php?file=".$db_path);
exit;