<?php ob_start();

include('includes/config.php');
include('includes/functions.php');

        $class_id=isset($_GET['class'])?$_GET['class']:2;
$section_id=isset($_GET['section'])?$_GET['section']:3;
require '../../vendor/autoload.php';
// create pdf

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//sets docment creator(metadata for pdf)
$pdf->SetCreator(PDF_CREATOR);
// now set the title for pdf
$pdf->SetTitle('Class Timetable');
// now set the header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
//set the html table 
$html = "
<h2 style='text-align:center;'>Class Timetable</h2>

<p style='text-align:right;'>
<b>Class:</b> $class_id &nbsp;&nbsp;
<b>Section:</b> $section_id
</p>
";
$html .='<style>
td,th{
border: 1px solid black;
}
table{
border-collapse:collapse;
border:none;
}
</style>
<table cellpadding="5">
<thead>
<tr>
<th>Time</th>
<th>Monday</th>
<th>Tuesday</th>
<th>Wednesday</th>
<th>Thursday</th>
<th>Friday</th>
<th>Saturday</th>
</tr>
</thead>
<tbody>
';
// fetch the period timming
$periods=get_posts(['type'=>'period','status'=>'publish']);
$days = ['monday','tuesday','wednesday','thursday','friday','saturday'];
foreach($periods as $period){
   $from = get_meta_value($period->id,'from');
    $to   = get_meta_value($period->id,'to');
    $html .= '<tr>';
$html .= '<td>'.date('h:i A', strtotime($from)).' - '.date('h:i A', strtotime($to)).'</td>';
foreach($days as $day){
$tt=get_timetable($day,$period->id,$class_id,$section_id);

 $teacher = $tt['teacher'] ?? '';
    $subject = $tt['subject'] ?? '';
    $html .= "<td>$teacher<br>$subject</td>";
}
$html .= '</tr>';

}

$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('timetable.pdf', 'D'); // 'I' = open in browser, 'D' = download
?>