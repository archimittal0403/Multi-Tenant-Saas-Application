<?php ob_start();

session_start();
include('includes/config.php');
include('includes/functions.php');

include('pdf_header.php');

$institute_id = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$institute = getinstitute($con, $institute_id);

/* ✅ session fix */
$session = $_GET['session'] ?? null;

if(!$session){
    die("Session not provided");
}

if(isset($_POST['generate_pdf'])){

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = generatePDFHeader($institute);

    $html .= "
    <h2 style='text-align:center;'>Teacher Feedback Report</h2>

    <table border='1' cellpadding='6'>
        <tr style='background-color:#2c3e50;color:#fff;'>
            <th>S.No</th>
            <th>Subject</th>
            <th>Teacher</th>
            <th>ID</th>
            <th>Avg Rating</th>
            <th>Level</th>
        </tr>
    ";

    $i = 1;

    $q = mysqli_query($con,"
        SELECT 
            ts.subject_id,
            s.title as subject_name,
            a.Name as teacher_name,
            a.roll_no,
            AVG(m.rating) as avg_rating
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

    while($row = mysqli_fetch_assoc($q)){

        $avg = round($row['avg_rating'] ?? 0,1);

        if($avg >= 4) $level = "Very Good";
        elseif($avg >= 3) $level = "Good";
        elseif($avg >= 2) $level = "Average";
        else $level = "Bad";

        $html .= "
        <tr>
            <td>".$i++."</td>
            <td>".$row['subject_name']."</td>
            <td>".$row['teacher_name']."</td>
            <td>".$row['roll_no']."</td>
            <td>".$avg."</td>
            <td>".$level."</td>
        </tr>
        ";
    }

    $html .= "</table>";

    $pdf->writeHTML($html);

    $pdf->Output("feedback_report.pdf", "I");
}
?>