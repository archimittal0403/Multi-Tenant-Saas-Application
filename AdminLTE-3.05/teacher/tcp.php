<?php ob_start();

include('includes/config.php');
include('includes/functions.php');
require '../../vendor/autoload.php';
$pdf=new TCPDF('L',PDF_UNIT,PDF_PAGE_FORMAT,true, 'UTF-8','false');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Class Timetable');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
?>