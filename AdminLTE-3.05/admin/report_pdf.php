<?php ob_start();

session_start();
include('includes/config.php');
include('includes/functions.php');
require '../../vendor/autoload.php';
include('pdf_header.php');

$institute_id = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$institute = getinstitute($con, $institute_id);

/* ✅ session fix */
$session = $_POST['session'] ?? null;

if(!$session){
    die("Session not provided");
}

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = generatePDFHeader($institute);

$html .= "<h2>Teacher Feedback Report</h2>";

$q = mysqli_query($con,"
SELECT 
    ts.subject_id,
    s.title AS subject_name,
    a.Name AS teacher_name,
    a.roll_no,
    AVG(m.rating) AS avg_rating
FROM teacher_subjects ts
JOIN accounts a ON ts.teacher_id = a.id
JOIN posts s ON ts.subject_id = s.id
LEFT JOIN teacher_feedback f 
    ON f.teacher_id = ts.teacher_id 
    AND f.session = ts.session_id
LEFT JOIN meta_teacher m 
    ON m.feedback_id = f.id 
    AND m.subject_id = ts.subject_id
WHERE ts.session_id = '$session'
GROUP BY ts.subject_id, ts.teacher_id
");

if(mysqli_num_rows($q) == 0){
    $html .= "<h3>No Data Found</h3>";
}

while($row = mysqli_fetch_assoc($q)){
    $html .= "
    <p>
    {$row['subject_name']} - {$row['teacher_name']} - {$row['avg_rating']}
    </p>";
}

$pdf->writeHTML($html);
$pdf->Output("report.pdf", "I");
exit;
?>