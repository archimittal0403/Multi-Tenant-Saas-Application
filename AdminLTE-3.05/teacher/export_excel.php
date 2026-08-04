<?php
include('includes/config.php');
// flow to create excel sheet
//load the excel library-> create excel object->set header
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// excel object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
// create header

$sheet->setCellValue('A1','Enroll_id');
$sheet->setCellValue('B1','Student_Name');
$sheet->setCellValue('C1','Marks');

//fetch data from database
$select=$con->prepare("SELECT rm.student_id,s.Name,rm.marks FROM result_marks as rm JOIN accounts as s ON rm.student_id=s.id");
$select->execute();
$select_result=$select->get_result();
$row=2;
while($data=$select_result->fetch_assoc()){
$sheet->setCellValue('A'.$row,$data['student_id']);
$sheet->setCellValue('B'.$row,$data['Name']);
$sheet->setCellValue('c'.$row,$data['marks']);
$row++;
}

$filename="student_result.xlsx";
//yeh bata denga ki excel file aa raha hai varna yeh esko garbage m daal dega
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//yeh bata  raha h ki file lo download karo  and name yeh filename rakh do
header('Content-Disposition: attachment;filename="'.$filename.'"');
//now yeh hummari file ko cache m daal deh=ga or haar baar new file generate hogi
header('Cache-Control: max-age=0');
//yeh batage ki file spreadsheet h or usko excel format  m convert karo
$writer = new Xlsx($spreadsheet);
// import line
// esma file raha ulpad nahi hoga buss direct hummara browser ya phir sysytem m download hogi
$writer->save('php://output');
exit;
?>
