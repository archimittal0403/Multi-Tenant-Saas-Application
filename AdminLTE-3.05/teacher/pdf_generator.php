<?php
require '../../vendor/autoload.php';
require_once './includes/config.php';
require_once 'pdf_header.php';

function generatePDF($title = "Report", $html = "")
{
  global $con;
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();
$institute_id = $_SESSION['institute_id'];

$logoQuery = mysqli_query($con,"
    SELECT logo 
    FROM institutes 
    WHERE id='$institute_id'
");

$logoData = mysqli_fetch_assoc($logoQuery);

$logo = $logoData['logo'] ?? '';

$logoPath = $_SERVER['DOCUMENT_ROOT'] . '/student management/AdminLTE-3.05/admin/uploads/logo/' . $logo;

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
    // ✅ USE HEADER FUNCTION HERE
      // ✅ USE HEADER FUNCTION HERE
  $institute = getInstitute($con, $institute_id);

$header = generatePDFHeader($institute);

    $pdf->writeHTML($header . $html, true, false, true, false, '');

    $pdf->Output($title . '.pdf', 'I');
}
?>