<?php
session_start(); // ✅ MUST

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('includes/config.php');
include_once('includes/functions.php');


if (isset($_POST['action']) && $_POST['action'] === 'get_sections') {

    $class_id = $_POST['class_id'] ?? '';

    if (empty($class_id)) {
        echo json_encode([
            'status' => false,
            'options' => '<option value="">Select section</option>'
        ]);
        exit;
    }

    $class_meta = get_metadata($class_id, 'section');
    $options = '<option value="">Select section</option>';

    if (!empty($class_meta)) {
        foreach ($class_meta as $meta) {

            // ✅ get_posts returns ARRAY
            $section_arr = get_posts(['id' => $meta->meta_value]);

            if (!empty($section_arr)) {
                $section = $section_arr[0]; // ✅ FIRST OBJECT

                $options .= '<option value="'.$section->id.'">'
                          . $section->title .
                          '</option>';
            }
        }
    }

    echo json_encode([
        'status' => true,
        'options' => $options
    ]);
    exit;
}


if (!empty($_POST['action']) && $_POST['action'] === 'fill_feedback') {

  $class_id   = $_POST['fill_class'] ?? '';
  $section_id = $_POST['fill_section'] ?? '';
  $subject_id = $_POST['fill_subject'] ?? '';
  $teacher_id = $_POST['fill_teacher'] ?? '';

  $count = 0;
  $data  = [];

  // Base query: students who gave feedback
  $sql = "
    SELECT 
      f.roll AS item_id,
      f.name,

      (
        SELECT mf3.meta_value
        FROM meta_feedback mf3
        WHERE mf3.item_id = f.roll
          AND mf3.meta_key = 'rate'
          AND EXISTS (
            SELECT 1 FROM meta_feedback s
            WHERE s.item_id = f.roll
              AND s.meta_key = 'subject_id'
              AND s.meta_value = ?
          )
          AND EXISTS (
            SELECT 1 FROM meta_feedback t
            WHERE t.item_id = f.roll
              AND t.meta_key = 'teacher_id'
              AND t.meta_value =?
          )
        LIMIT 1
      ) AS rate

    FROM feedback f
    WHERE 1=1
  ";
$params=[];
$types="";
$params[] = $subject_id;
$types   .= "s";

$params[] = $teacher_id;
$types   .= "s";
    if ($class_id != '') {
    $sql .= " AND f.class = ?";
    $params[]=$class_id;
    $types .= "s";
  }

  /* ---- OPTIONAL: section filter ---- */
  if ($section_id != '') {
    $sql .= " AND f.section = ?";
    $params[]=$section_id;
    $types .= "s";
  }

  $query = $con->prepare($sql);
$query->bind_param($types, ...$params);
  $query->execute();
  $query_result=$query->get_result();

  while ($row = $query_result->fetch_assoc()) {
    $count++;

    $data[] = [
      'Sno'          => $count,
      'Enroll_ID'    => $row['item_id'],
      'student_name' => ucfirst($row['name']),
      'Rating'       => $row['rate'] ?? 'Not Rated'
    ];
  }

  echo json_encode([
    "draw"            => intval($_POST['draw'] ?? 1),
    "recordsTotal"    => count($data),
    "recordsFiltered" => count($data),
    "data"            => $data
  ]);

  exit;
}
if(!empty($_POST['action']) && $_POST['action'] === 'view_attend'){

$class_id   = $_POST['fil_class'] ?? '';
$section_id = $_POST['fil_section'] ?? '';
$subject_id = $_POST['fil_subject'] ?? '';
$teacher_id = $_POST['fil_teacher'] ?? '';
$date       = $_POST['fil_dob'] ?? '';

$data = [];
$count = 0;

$sql = "
SELECT a.item_id, s.Name,
MAX(CASE WHEN a.meta_key='status' THEN a.meta_value END) as status
FROM attendance1 a
JOIN accounts s ON s.id = a.item_id
WHERE s.type='student'
";
$params=[];
$types="";
if($class_id!=''){
  $sql.=" AND EXISTS (
    SELECT 1 FROM attendance1 
    WHERE item_id=a.item_id AND meta_key='at_class' AND meta_value=?
  )";
  $params[]=$class_id;
  $types .="s";
}

if($section_id!=''){
  $sql.=" AND EXISTS (
    SELECT 1 FROM attendance1 
    WHERE item_id=a.item_id AND meta_key='at_section' AND meta_value=?
  )";
  $params[]=$section_id;
  $types .="s";
}

if($subject_id!=''){
  $sql.=" AND EXISTS (
    SELECT 1 FROM attendance1 
    WHERE item_id=a.item_id AND meta_key='at_subject' AND meta_value=?
  )";
   $params[]=$subject_id;
  $types .="s";
}

if($teacher_id!=''){
  $sql.=" AND EXISTS (
    SELECT 1 FROM attendance1 
    WHERE item_id=a.item_id AND meta_key='at_teacher' AND meta_value=?
  )";
   $params[]=$teacher_id;
  $types .="s";
}

if($date!=''){
  $sql.=" AND EXISTS (
    SELECT 1 FROM attendance1 
    WHERE item_id=a.item_id AND meta_key='dob' AND meta_value=?
  )";
   $params[]=$date;
  $types .="s";
}

$sql.=" GROUP BY a.item_id";

$query = $con->prepare($sql);
if(!empty($params)){
  $query->bind_param($types, ...$params);
}
$query->execute();
$result=$query->get_result();
while($row=$result->fetch_assoc()){
  $count++;
  $data[]=[
    'Sno'=>$count,
    'Enroll_ID'=>$row['item_id'],
    'Student_Name'=>$row['Name'],
    'Status'=>$row['status'] ?? 'Not Marked'
  ];
}
echo json_encode([
  "draw"=>intval($_POST['draw'] ?? 1),
  "recordsTotal"=>count($data),
  "recordsFiltered"=>count($data),
  "data"=>$data
]);
exit;
}
if(!empty($_POST['action']) && $_POST['action'] === 'update_attend'){

$class_id   = $_POST['fil_class'] ?? '';
$section_id = $_POST['fil_section'] ?? '';
$subject_id = $_POST['fil_subject'] ?? '';
$date       = $_POST['fil_dob'] ?? '';
$current_date=date('Y-m-d');
$data = [];
$count = 0;

if($date!=$current_date){
  echo json_encode([
    'error'=>'you can only update todays date attendance'
  ]);
  exit;
}
$sql = "
SELECT 
  a.item_id, 
  s.Name,
  MAX(CASE WHEN a.meta_key='status' THEN a.meta_value END) as status,
  MAX(CASE WHEN a.meta_key='at_class' THEN a.meta_value END) as at_class,
  MAX(CASE WHEN a.meta_key='at_section' THEN a.meta_value END) as at_section,
  MAX(CASE WHEN a.meta_key='at_subject' THEN a.meta_value END) as at_subject,
  MAX(CASE WHEN a.meta_key='dob' THEN a.meta_value END) as dob
FROM attendance1 a
JOIN accounts s ON s.id = a.item_id
WHERE s.type='student'
GROUP BY a.item_id
HAVING 1=1
";
$params=[];
$types="";
if($class_id!=''){
  $sql.=" AND at_class=?";
}
 $params[]=$class_id;
  $types .="s";

if($section_id!=''){
  $sql.=" AND at_section=?";
}
$params[]=$section_id;
$types .="s";

if($subject_id!=''){
  $sql.=" AND at_subject=?";
}
 $params[]=$subject_id;
  $types .="s";

if($date!=''){
  $sql .= " AND dob = ?";
}
 $params[]=$date;
  $types .="s";


$query = $con->prepare($sql);
if(!empty($params)){
  $query->bind_param($types, ...$params);
}
$query->execute();
$result=$query->get_result();
while($row=$result->fetch_assoc()){
  $count++;
    $statusText  = ($row['status'] == 'P') ? 'Present' : 'Absent';
  $statusClass = ($row['status'] == 'P') ? 'btn-success' : 'btn-danger';
  $data[]=[
    'Sno'=>$count,
    'Enroll_ID'=>$row['item_id'],
    'Student_Name'=>$row['Name'],
    
   'Status'=>'<button type="button" class="btn btn-sm btn-danger toggle-att" data-id="'.$row['item_id'].'"data-status="'.$row['status'].'">'.$statusText.'</button>
   <button class="btn btn-sm btn-primary upd-att" data-id="'.$row['item_id'].'">Update</button>'
  ];
}

echo json_encode([
  "draw"=>intval($_POST['draw'] ?? 1),
  "recordsTotal"=>count($data),
  "recordsFiltered"=>count($data),
  "data"=>$data
]);
exit;
}


if(!empty($_POST['action']) && $_POST['action']=='update_attendace'){
  $enroll_id=$_POST['enroll_id'];
  $status=$_POST['status'];
 update_attendance($enroll_id,'status',$status);

  echo json_encode([
    'success'=>true
    ]);

  exit;
}

if(!empty($_POST['action']) && $_POST['action']=='mark_attendance'){
    $class_id=$_POST['at_class']?? '';
    $section_id=$_POST['at_section']?? '';
    $subject_id=$_POST['at_subject']?? '';
$semester='';
    $count=0;
    $data=[];
    $sql="SELECT * FROM `accounts` WHERE type='student'";
    $params=[];
$types="";
    if($class_id!=''){
        $sql.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='st_class' AND meta_value=?)";
         $params[]=$class_id;
  $types .="s";
    }
    if($section_id!=''){
          $sql.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='st_section' AND meta_value=?)";
           $params[]=$section_id;
  $types .="s";
    }
    if($subject_id!=''){
  $q=$con->prepare("SELECT semester FROM `courses` WHERE id=?");
  $q->bind_param("i",$subject_id);
  $q->execute();
  $q_result=$q->get_result();
  $r=$q_result->fetch_assoc();
  $semester=$r['semester'];
    }
    if($semester!=''){
      $sql.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='semester' AND meta_value=?)";
          $params[]=$semester;
  $types .="s";
    }
    $query=$con->prepare($sql);
   if(!empty($params)){
  $query->bind_param($types, ...$params);
$query->execute();
  }
$query_result=$query->get_result();
    while($row_fetch=$query_result->fetch_assoc()){
        $count++;
        $name=$row_fetch['Name'];
        $enroll_id=$row_fetch['id'];
        $at_class=get_usermeta($enroll_id,'st_class');
        $at_class_id=$con->prepare("SELECT title FROM `posts` WHERE id=?");
        $at_class_id->bind_param("s",$at_class);
        $at_class_id->execute();
        $at_class_id_result=$at_class_id->get_result();
        $class_fetch=$at_class_id->fetch_assoc();
        $at_class_name=$class_fetch['title'];
   $at_section=get_usermeta($enroll_id,'st_section');
        $at_section_id=$con->prepare("SELECT title FROM `section` WHERE id=?");
        $at_section_id->bind_param("s",$at_section);
        $at_section_id->execute();
        $at_section_id_result=$at_section_id->get_result();
        $section_fetch=$at_section_id_result->fetch_assoc();
        $at_section_name=$section_fetch['title'] ?? 'N/A';
    $data[]=[
        'SNo'=>$count,
        'Enroll_ID'=>$enroll_id,
        'Class'=>$at_class_name,
        'Section'=>$at_section_name,
        'Student_name'=>$name,
        'Action'=>'<button type="button" class="btn btn-sm btn-danger toggle-att" data-id="'.$enroll_id.'"data-status="A">Absent</button>'
     
    ];
    }
echo json_encode([
    "draw"=>intval($_POST['draw'] ?? 1),
    "recordsTotal"=>count($data),
  "recordsFiltered"=>count($data),
    "data"=>$data
]);
exit;
}
if(!empty($_POST['action']) && $_POST['action']=='saveAttendance'){
    $attendance=$_POST['att'] ?? [];
    $date=$_POST['dob']?? date('Y-m-d');
    $class=$_POST['at_class']?? '';
    $section=$_POST['at_section']?? '';
    $teacher=$_POST['at_teacher']?? '';
    $subject=$_POST['at_subject']?? '';
    if(empty($attendance)){
    echo json_encode(["status"=>false,"message"=>"Attendance empty"]);
    exit;
}
    foreach($attendance as $student_id=>$status){
     
      $result=  $con->prepare("INSERT INTO `attendance1` (`item_id`,`meta_key`,`meta_value`) 
        VALUES
        (?,?,?),
        (?,?,?),
        (?,?,?),
        (?,?,?),
        (?,?,?),
        (?,?,?)
        ");
        if(!$result){
          echo mysqli_error($con);
          exit;
        }
        $result->bind_param("ississississississ",
         $student_id, $status_key, $status,
    $student_id, $dob_key, $date,
    $student_id, $class_key, $class,
    $student_id, $section_key, $section,
    $student_id, $teacher_key, $teacher,
    $student_id, $subject_key, $subject);
    $result->execute();
    $result->close();
    }
    echo json_encode(["status" => true, "message" => "success"]);
exit;

}

if (!empty($_POST['action']) && $_POST['action'] === 'get_parent') {

    $class_id   = $_POST['class_id'] ?? '';
    $section_id = $_POST['section_id'] ?? '';
    $name       = $_POST['name'] ?? '';
    $enroll_id  = $_POST['enroll_id'] ?? '';

    $data  = [];
    $count = 0;

    // ✅ BASE QUERY (MISSING BEFORE)
    $sql = "SELECT * FROM accounts WHERE type=?";
$params=['student'];
$types="s";
   if ($name != '') {
        $sql .= " AND Name = ?";
        $params[]=$name;
        $types .="s";
    }

    if ($enroll_id != '') {
        $sql .= " AND id = ?";
        $params[]=$enroll_id;
        $types .="s";
    }
  if($class_id!=''){
    $sql.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='st_class' AND meta_value=?)";
    $params[]=$class_id;
    $types .="s";
  }
  if($section_id!=''){
    $sql.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='st_section' AND meta_value=?)";
    $params[]=$section_id;
    $types .="s";
  }
  $query=$con->prepare($sql);
  if(!$query){
    echo $con->error;
    exit;
  }
      $query->bind_param($types, ...$params);
      $query->execute();
      $query_result=$query->get_result();
  while($row_fetch=$query_result->fetch_assoc()){
    $count++;
    $enroll_id=$row_fetch['id'];
    $student_name=$row_fetch['Name'];
    $parent_id=0;
    $childParent= "%i:$enroll_id;%";
  $q = $con->prepare(
            "SELECT user_id 
             FROM usermeta 
             WHERE meta_key='children' 
             AND meta_value LIKE ?"
        );
        $q->bind_param("s",$childParent);
        $q->execute();
        $q_result=$q->get_result();

        if ($pr = $q_result->fetch_assoc()) {
            $parent_id = $pr['user_id'];
        }

           $father_name = $father_mobile = '';
        $mother_name = $mother_mobile = '';
        $parent_address = '';
        $parent_email = 'N/A';
  if ($parent_id) {
            $p = $con->prepare("SELECT email FROM accounts WHERE id=?");
            $p->bind_param("i",$parent_id);
            $p->execute();
            $p_result=$p->get_result();
            $row_email=$p_result->fetch_assoc();
            $parent_email = $row_email['email'] ?? 'N/A';
        }
        if($parent_id){
        $meta_q =$con->prepare(            
          "SELECT meta_key, meta_value 
             FROM usermeta 
             WHERE user_id=?
               AND meta_key IN (
                   'father_name',
                   'father_mobile',
                   'mother_name',
                   'mother_mobile',
                   'parent_address'
               )"
        );
$meta_q->bind_param("i",$enroll_id);
$meta_q->execute();
$meta_q_result=$meta_q->get_result();
        while ($meta = $meta_q_result->fetch_assoc()) {
            switch ($meta['meta_key']) {
                case 'father_name':
                    $father_name = $meta['meta_value'];
                    break;

                case 'father_mobile':
                    $father_mobile = $meta['meta_value'];
                    break;

                case 'mother_name':
                    $mother_name = $meta['meta_value'];
                    break;

                case 'mother_mobile':
                    $mother_mobile = $meta['meta_value'];
                    break;

                case 'parent_address':
                    $parent_address = $meta['meta_value'];
                    break;
            }
        }
        }
$data[]=[
   'SNO'=>$count,

'Father Name'=>ucfirst($father_name) ?: 'n/a',
'Father Mobile'=>$father_mobile ?: 'n/a',
'Mother Name'=>ucfirst($mother_name) ?: 'n/a',
'Mother Mobile'=>$mother_mobile ?: 'n/a',
'Address'=>$parent_address ?: 'n/a',
  'Email Address' => $parent_email ,
  'Action' =>'<a href="edit_parent.php?id='.$enroll_id.'" class="btn btn-sm btn-info">Edit</a>'

];
  }
   echo json_encode([
        "draw"=>intval($_POST['draw'] ?? 1),
        "recordsTotal"=>count($data),
                          "recordsFiltered"=>count($data), 
                          "data"=>$data
    ]);
    exit;
}
if(!empty($_POST['action']) && $_POST['action']=='get_result_details'){
    
    $class_id=$_POST['class_id'] ?? '';
    $section_id=$_POST['section_id'] ?? '';
    $subject_id=$_POST['subject_id'] ?? '';

    $data1=[];
    $count=0;
    $sql1="SELECT * FROM `accounts` WHERE type=?";
$params=['student'];
$types = "s";
    if($class_id!=''){
        $sql1.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='st_class' AND meta_value=?)";
        $params[]=$class_id;
        $types .="s";
    }
    if($section_id!=''){
        $sql1.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='st_section' AND meta_value=?)";
        $params[]=$section_id;
        $types .="s";
    } 
    $semester = '';
    if($subject_id!=''){

        $sub_q = $con->prepare("SELECT semester FROM `courses` WHERE id=? LIMIT 1");
        $sub_q->bind_param("i",$subject_id);
        $sub_q->execute();
        $sub_q_result=$sub_q->get_result();

    if($sub_q_result && $sub_q_result->num_rows>0){
        $sub_row = $sub_q_result->fetch_assoc();
        $semester = $sub_row['semester'];
    }
    }
    if($semester!=''){
      $sql1.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='semester' AND meta_value=?)";
      $params[]=$semester;
      $types .="i";
    }
    $query=$con->prepare($sql1);
    $query->bind_param($types, ...$params);
    $query->execute();
    $query_result=$query->get_result();
    while($row_fetch=$query_result->fetch_assoc()){
$count++;
    $enroll_id=$row_fetch['id'];
    $name=$row_fetch['Name'];
    $res_class=get_usermeta($enroll_id,'st_class');
$res_class_id=$con->prepare("SELECT title FROM posts WHERE id=?");
$res_class_id->bind_param("s",$res_class);
$res_class_id->execute();
$res_class_id_result=$res_class_id->get_result();
$class_fetch=$res_class_id_result->fetch_assoc();
$res_class_name=$class_fetch['title'];
$res_section=get_usermeta($enroll_id,'st_section');
$res_section_id=$con->prepare("SELECT title FROM section WHERE id=?");
$res_section_id->bind_param("s",$res_section);
$res_section_id->execute();
$res_section_id_result=$res_section_id->get_result();
$section_fetch=$res_section_id_result->fetch_assoc();
$res_section_name=$section_fetch['title']?? 'N/A';
        $data1[]=[
            'Sno'=>$count,
'Enroll_ID'=>$enroll_id,

'Student_Name'=>ucfirst($name),
'Marks'=>'n/a'
        ];
    }

    echo json_encode([
        "draw"=>intval($_POST['draw'] ?? 1),
        "recordsTotal"=>count($data1),
                          "recordsFiltered"=>count($data1), 
                          "data"=>$data1
    ]);
    exit;
}
 if (!empty($_POST['action']) && $_POST['action'] === 'get_user_details') {
        $class_id   = $_POST['class_id'] ?? '';
  $section_id = $_POST['section_id'] ?? '';

                 $data=[];
                  $sql="SELECT * FROM accounts WHERE type=?";
                  $params=['student'];
                  $types = "s";
                   if($class_id!=''){
                      $sql.=" AND id IN (SELECT user_id FROM usermeta WHERE meta_key='st_class' AND meta_value=?)"; 
                      $params[]=$class_id;
                      $types .="s";
                     } 
                     if($section_id!=''){ 
                        $sql.=" AND id IN (SELECT user_id FROM usermeta WHERE meta_key='st_section' AND meta_value=?)";
                        $params[]=$section_id;
                        $types .="s";
                      }
                       $query=$con->prepare($sql); 
                          $query->bind_param($types, ...$params);
                          $query->execute();
                          $query_result=$query->get_result();
                       while($row=$query_result->fetch_assoc()){ 
                        $user_id=$row['id'];
                        $user_edit='<a href="user-account.php?class='.$class_id.'&section='.$section_id.'&edit_student='.$user_id.'" class ="btn btn-sm btn-success"><i class="fa fa-pencil-alt"></i></a>';
                        $user_delete='<a href="user-account.php?class='.$class_id.'&section='.$section_id.'&delete_student='.$user_id.'" class="btn btn-sm btn-success mx-2"><i class="fa fa-trash-alt"></i></a>' ;
                        $dob=get_usermeta($user_id,'dob'); 
                        $phone=get_usermeta($user_id,'mobile');
                         $st_class=get_usermeta($user_id,'st_class'); 
                         $st_section=get_usermeta($user_id,'st_section');
                         $data[] = [ 
                           'enroll'=> $row['id'] , 
                           'class'=>$st_class, 
                           'section'=>$st_section,
                            'photo'=>'<img src="dist/img/akg-logo.png" width="40">', 
                            'name'=>$row['Name'], 
                            'email'=>$row['email'], 
                            'phone'=>$phone, 
                            'dob'=>$dob, 
                            'action'=>$user_edit.''.$user_delete,
       
                           ]; 
                        }
                        echo json_encode([ 
                           "draw" => intval($_POST['draw'] ?? 1),
                         "recordsTotal"=>count($data),
                          "recordsFiltered"=>count($data), 
                          "data"=>$data 
                        ]);
                         exit; 
                        }
// upload the marks inside yteh system 
if(!empty($_POST['action']) && $_POST['action'] == 'save_marks'){

    $class_id   = $_POST['res_class'] ?? '';
    $section_id = $_POST['res_section'] ?? '';
    $subject_id = $_POST['res_subject'] ?? '';
    $marks      = $_POST['marks'] ?? [];

    if(empty($class_id) || empty($section_id) || empty($subject_id)){
        echo json_encode([
            "status"=>false,
            "message"=>"Please select class/section/subject"
        ]);
        exit;
    }

    // 1️⃣ Get or Create Result
    $check = $con->prepare(
        "SELECT result_id FROM result
         WHERE class_id=?
         AND section_id=?
         AND subject_id=?"
    );
$check->bind_param("iii",$class_id,$section_id,$subject_id);
$check->execute();
$check_result=$check->get_result();
    if($check_result->num_rows > 0){
        $row = $check_result->fetch_assoc();
        $result_id = $row['result_id'];
    } else {
       $stmt= $con->prepare(
            "INSERT INTO result (class_id,section_id,subject_id)
             VALUES(?,?,?)"
        );
        $stmt->bind_param("iii",$class_id,$section_id,$subject_id);
        $stmt->execute();
        //$stmt_result=$stmt->get_result();
        $result_id = mysqli_insert_id($con);
    }

    // 2️⃣ Insert or Update Marks
    foreach($marks as $student_id => $mark){

        $exist =$con->prepare(
            "SELECT id FROM result_marks
             WHERE result_id=?
             AND student_id=?"
        );
        $exist->bind_param("ii",$result_id,$student_id);
        $exist->execute();
        $exist_result=$exist->get_result();

        if($exist_result->num_rows> 0){

           $stpt=$con->prepare(
                "UPDATE result_marks
                 SET marks=?
                 WHERE result_id=?
                 AND student_id=?"
            );
$stpt->bind_param("iii",$mark,$result_id,$student_id);
$stpt->execute();
$stpt_result=$stpt->get_result();

        } else {

           $insert=$con->prepare(
                "INSERT INTO result_marks (result_id,student_id,marks)
                 VALUES(?,?,?)"
            );
            $insert->bind_param("iii",$result_id,$student_id,$mark);
            $insert->execute();
            
        }
    }

    echo json_encode([
        "status"=>true,
        "message"=>"Marks saved successfully"
   
    ]);
    exit;
}
