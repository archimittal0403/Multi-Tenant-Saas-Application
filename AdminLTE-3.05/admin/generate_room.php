<?php
ob_start();
include('includes/config.php');
include('includes/functions.php');
require '../../vendor/autoload.php';

$room_id=$_GET['room_id'];
$exam_id=$_GET['exam_id'];

$select_exam=mysqli_query($con,"SELECT exam_name FROM `create_exam` WHERE id='$exam_id'");
$fetch_exam=mysqli_fetch_assoc($select_exam);
$exam_name=$fetch_exam['exam_name'];
// now get the room name

$select_room=mysqli_query($con,"SELECT room_no FROM `create_room` WHERE id='$room_id'");
$fetch_room=mysqli_fetch_assoc($select_room);
$room_name=$fetch_room['room_no'];

   $id_room = $_GET['room_id'];
               $mode = isset($_GET['mode']) ? $_GET['mode'] : '';

    $select_room=mysqli_query($con,
        "SELECT room_no, row, columns,capacity,whiteboard
         FROM create_room 
         WHERE id='$id_room'"
    );
    $room=mysqli_fetch_assoc($select_room);

    $room_no = $room['room_no'];
    $rows = $room['row'];
    $columns = $room['columns'];
    $whiteboard=$room['whiteboard'];
            $sem=isset($_GET['semester'])?$_GET['semester']:'';
            $sem_array=explode(',',$sem);
          $sem_list = "'" . implode("','", $sem_array) . "'";
            $std_id= mysqli_query($con,"
    SELECT a.id, a.Name,u.meta_value AS semester
    FROM accounts a
    INNER JOIN usermeta u 
        ON u.user_id = a.id
       AND u.meta_key = 'semester'
       AND u.meta_value IN ($sem_list)
    WHERE a.type='student'
    ORDER BY a.id
    
");
         $students= [];
while($s = mysqli_fetch_assoc($std_id)){
    $students[] = $s;
   
}
 //shuffle($students);
$seat_no = 0;
$grid = [];   // seat memory
$used = [];   // used students
// ✅ Table Layout
if($mode=='row'){
for($i=0;$i<$rows;$i++){

    for($j=0;$j<$columns;$j++){

        $placed = false;

        for($k=0;$k<count($students);$k++){

            if(in_array($k,$used)) continue;

            $sem = $students[$k]['semester'];

            $left_sem = $j>0 && isset($grid[$i][$j-1])
                        ? $grid[$i][$j-1]['semester'] : null;

            if($sem != $left_sem){
                $grid[$i][$j] = $students[$k];
                $used[] = $k;
                $placed = true;
                break;
            }
        }

        if(!$placed){
            $grid[$i][$j] = null;
        }
    }
}
}

// coulmn-wise anti-cheating a;lgorith
if($mode=='column'){

for($j=0;$j<$columns;$j++){   // पहले column loop

    for($i=0;$i<$rows;$i++){  // फिर row loop

        $placed = false;

        for($k=0;$k<count($students);$k++){

            if(in_array($k,$used)) continue;

            $sem = $students[$k]['semester'];

            // 🔥 check top
            $top_sem = $i>0 && isset($grid[$i-1][$j])
                        ? $grid[$i-1][$j]['semester'] : null;

            // 🔥 check left
            $left_sem = $j>0 && isset($grid[$i][$j-1])
                        ? $grid[$i][$j-1]['semester'] : null;

            if($sem != $top_sem && $sem != $left_sem){

                $grid[$i][$j] = $students[$k];
                $used[] = $k;
                $placed = true;
                break;
            }
        }

        if(!$placed){
            $grid[$i][$j] = null; // empty
        }
    }
}
}
$pdf=new TCPDF();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetAlpha(0.1);
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
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 16);

$text = "Exam: $exam_name   |   Room: $room_name";

$pdf->Cell(0, 10, $text, 0, 1, 'C');
$pdf->Ln(10); 
$html = "<table border='1' cellpadding='5' cellspacing='0' width='100%' style='border-collapse:collapse;'>";

$html .= "<tr>
            <th style='border:1px solid black;'>Row</th>";

for($j=0;$j<$columns;$j++){
    $html .= "<th style='border:1px solid black;'>Seat ".($j+1)."</th>";
}
$html .= "</tr>";

// ✅ PRINT TABLE
for($i=0;$i<$rows;$i++){

    $letter = chr(65+$i);
  $html .= "<tr>"; // ✅ ADD THIS
  
   $html .= "<tr>
            <th style='border:1px solid black;'>Row</th>";

    for($j=0;$j<$columns;$j++){

           $html .= "<td style='border:1px solid black;'>";
        $html .= "<b>$letter".($j+1)."</b><br>";

        if(isset($grid[$i][$j]) && $grid[$i][$j] != null){
            $html .= "Roll: ".$grid[$i][$j]['id']."<br>";
            $html .= "Sem: ".$grid[$i][$j]['semester'];
        } else {
            $html .= "Empty";
        }

        $html .= "</td>";
    }

    $html .= "</tr>";
}

$html .= "</table>";
$pdf->writeHTML($html, true, false, true, false, '');
// file name
$file_name=$room_name . "_" . $exam_name . "_" . time() .".pdf";
$file_path = __DIR__ . "/uploads/generate_pdf/" . $file_name;


$pdf->Output($file_path,'F');
$pdf->Output($file_name,'I');

// insert into database
$update_file=mysqli_query($con,"UPDATE `room_setting` SET setting_file='$file_name' WHERE exam_id='$exam_id' AND room_id='$room_id'");
if($update_file){
    echo "<script>alert('insertion done sucessfully')</script>";
}
?>