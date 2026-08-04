<?php
session_start(); // ✅ MUST

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('includes/config.php');
include_once('includes/functions.php');


$institute_id = $_SESSION['institute_id'];
      $institute_type=$_SESSION['system_type'];
      $institute_code=$_SESSION['institute_code'];


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

// ================= SCHOOL SUBJECT =================

// ================= SCHOOL SUBJECT =================
if($_POST['action']=='get_school_subject'){

    $class_id = $_POST['class_id'] ?? '';
    $session  = $_POST['session'] ?? '';

    $options = '<option value="">Select Subject</option>';

    $query = mysqli_query($con,"
    SELECT p.id,p.title
    FROM posts p
    JOIN metadata m1 ON p.id=m1.item_id
    JOIN metadata m2 ON p.id=m2.item_id
    WHERE p.type='subject'
    AND m1.meta_key='class'
    AND m1.meta_value='$class_id'
    AND m2.meta_key='session'
    AND m2.meta_value='$session'
    AND p.institute_id='$institute_id'
    ");

    while($row=mysqli_fetch_assoc($query)){
        $options .= '<option value="'.$row['id'].'">'.$row['title'].'</option>';
    }

    echo json_encode([
        'status' => true,
        'options' => $options
    ]);


}
if(isset($_POST['action']) && $_POST['action']=== 'get_branch'){

    $course_id = $_POST['course_id'] ?? '';

    if (empty($course_id)) {
        echo json_encode([
            'status' => false,
            'options' => '<option value="">Select branch</option>'
        ]);
        exit;
    }

    $branches = get_posts([
        'type'=>'branch',
        'parent'=>$course_id
    ]);

    $options = '<option value="">Select branch</option>';

    if(!empty($branches)){
        foreach($branches as $branch){
            $options .= '<option value="'.$branch->id.'">'.$branch->title.'</option>';
        }
    }

    echo json_encode([
        'status' => !empty($branches),
        'options' => $options
    ]);
    exit;
}

if(isset($_POST['action']) && $_POST['action']=='get_subject'){

    $course_id = $_POST['course_id'];
    $branch_id = $_POST['branch_id'];
    $semester  = $_POST['semester'];
    $session   = $_POST['session'];

    if(empty($course_id) || empty($branch_id) || empty($semester) || empty($session)){
        echo json_encode([
            'status'=>false,
            'options'=>'<option value="">Select subject</option>'
        ]);
        exit;
    }

    // 1. get matching item_ids
 $q = mysqli_query($con,"
SELECT DISTINCT p.id
FROM posts p
JOIN metadata m1 ON p.id = m1.item_id
JOIN metadata m2 ON p.id = m2.item_id
JOIN metadata m3 ON p.id = m3.item_id
JOIN metadata m4 ON p.id = m4.item_id

WHERE 
p.type='subject'

AND m1.meta_key='course_name' AND m1.meta_value='$course_id'
AND m2.meta_key='branch_name' AND m2.meta_value='$branch_id'
AND m3.meta_key='semester' AND m3.meta_value='$semester'
AND m4.meta_key='session' AND m4.meta_value='$session'
");

    $item_ids = [];

    while($row = mysqli_fetch_assoc($q)){
        $item_ids[] = $row['id'];
    }

    // 2. default option
    $options = '<option value="">Select Subject</option>';

    if(!empty($item_ids)){

        $ids = implode(',', $item_ids);

        // 3. fetch subjects
        $sub_q = mysqli_query($con,"
            SELECT id, title 
            FROM posts 
            WHERE id IN ($ids)
        ");

        while($sub = mysqli_fetch_assoc($sub_q)){
            $options .= '<option value="'.$sub['id'].'">'.$sub['title'].'</option>';
        }
    }

    echo json_encode([
        'status'=> !empty($item_ids),
        'options'=> $options
    ]);
    exit;
}
if(isset($_POST['action']) && $_POST['action']=== 'get_semester'){

    $course_id = $_POST['course_id'] ?? '';

    if (empty($course_id)) {
        echo json_encode([
            'status' => false,
            'options' => '<option value="">Select semester</option>'
        ]);
        exit;
    }

    $semesters = get_posts([
        'type'=>'semester',
        'parent'=>$course_id
    ]);

    $options = '<option value="">Select semester</option>';

    if(!empty($semesters)){
        foreach($semesters as $semester){
      $total_sem=(int)$semester->title;
          for($i=1;$i<=$total_sem;$i++){//1-8
  $options .= '<option value="'.$i.'">'.$i.'</option>';
          }
           
        }
    }

    echo json_encode([
        'status' => !empty($semesters),
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
$sql="SELECT a.user_id,s.Name,a.status FROM
attendance a 
JOIN accounts s ON s.id=a.user_id
JOIN usermeta uc ON uc.user_id=a.user_id AND uc.meta_key='st_class'
JOIN usermeta us ON us.user_id=a.user_id AND us.meta_key='st_section'
WHERE s.type='student'
AND uc.meta_value=?
AND us.meta_value=?
AND a.subject_id=?
AND a.attendance_date=?";
$query=$con->prepare($sql);
$query->bind_param("ssis",$class_id,$section_id,$subject_id,$date);
$query->execute();
$result=$query->get_result();
while($row=$result->fetch_assoc()){
  $count++;

$statusText  = ($row['status']=='P') ? 'Present' : 'Absent';
$statusClass = ($row['status']=='P') ? 'btn-success' : 'btn-danger';

$data[]=[
  'Sno'=>$count,
  'Enroll_ID'=>$row['user_id'],
  'Student_Name'=>$row['Name'],
  'Status'=>'
<button type="button" class="btn btn-sm '.$statusClass.' toggle-att"
data-id="'.$row['user_id'].'" data-status="'.$row['status'].'">'.$statusText.'</button>

<button class="btn btn-sm btn-primary upd-att"
data-id="'.$row['user_id'].'"
data-subject_id="'.$subject_id.'"
data-date="'.$date.'">
Update
</button>'
];

}
echo json_encode([
  "draw"=>intval($_POST['draw'] ?? 1),
  "recordsTotal"=>count($data),
  "recordsFiltered"=>count($data),
  "data"=>$data
  ]);
  exit();
}


if(!empty($_POST['action']) && $_POST['action']=='update_attendace'){
  $status=$_POST['status'];
  $enroll_id=$_POST['enroll_id'];
  $subject_id=$_POST['subject_id'];
  $date=$_POST['date'];

 $update=$con->prepare("UPDATE `attendance` SET status=? WHERE user_id=? AND subject_id=? AND attendance_date=?");
 $update->bind_param("siis",$status,$enroll_id,$subject_id,$date);
 $update->execute();

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
  //   if($subject_id!=''){
  // $q=$con->prepare("SELECT semester FROM `courses` WHERE id=?");
  // $q->bind_param("i",$subject_id);
  // $q->execute();
  // $q_result=$q->get_result();
  // $r=$q_result->fetch_assoc();
  // $semester=$r['semester'];
  //   }
  //   if($semester!=''){
  //     $sql.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='semester' AND meta_value=?)";
  //         $params[]=$semester;
  // $types .="s";
  //   }
    $query=$con->prepare($sql);
   if(!empty($params)){
  $query->bind_param($types, ...$params);

  }
  $query->execute();
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
        $class_fetch=$at_class_id_result->fetch_assoc();
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
        'Action'=>'<button type="button" class="btn btn-sm btn-danger toggle-att" data-id="'.$enroll_id.'"data-status="A">Absent</button>
        <input type="hidden" name="att['.$enroll_id.']" value="A">'
     
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
     
    // chexck wjeater data is not thier
    $check=$con->prepare("SELECT subject_id,attendance_date FROM attendance WHERE subject_id=? AND attendance_date=? AND user_id=?");
    $check->bind_param("isi",$subject,$date,$student_id);
    $check->execute();
    $check_result=$check->get_result();
    if($check_result->num_rows>0){
continue;
exit;
    }else{
      $result=  $con->prepare("INSERT INTO `attendance` (user_id,subject_id,attendance_date,status) VALUES (?,?,?,?)");
        if(!$result){
          echo mysqli_error($con);
          exit;
        }
        $result->bind_param("iiss",$student_id,$subject,$date,$status
        );
    $result->execute();
    $result->close();
    }
    }
    echo json_encode(["status" => true, "message" => "success"]);
   
exit;

}
// upload the marks inside yteh system 

// get subject
if(!empty($_POST['action']) && $_POST['action']=='get_subjects'){

  $exam_id = $_POST['exam_id'] ?? '';

  // 1️⃣ exam → semester
  $exam_q = mysqli_query($con,"
      SELECT semester_id 
      FROM create_exam 
      WHERE id='$exam_id'
      LIMIT 1
  ");

  $exam = mysqli_fetch_assoc($exam_q);
  $semester = $exam['semester_id'] ?? '';

  // 2️⃣ semester → subjects
  $sub_q = mysqli_query($con,"
      SELECT id, name 
      FROM courses 
      WHERE semester='$semester'
  ");

  echo "<option value=''>--Select Subject--</option>";

  while($sub = mysqli_fetch_assoc($sub_q)){
      echo "<option value='{$sub['id']}'>{$sub['name']}</option>";
  }

  exit;
}