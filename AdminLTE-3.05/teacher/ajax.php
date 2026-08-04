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


if(isset($_POST['action']) && $_POST['action'] === 'get_sections'){

    $class_id = $_POST['class_id'] ?? '';

    $options = '<option value="">Select Section</option>';

    if(empty($class_id)){

        echo json_encode([
            'status'  => false,
            'options' => $options
        ]);

        exit;
    }

    // DIRECT POSTS TABLE QUERY
    $sections = get_posts([
        'type'   => 'section',
        'parent' => $class_id
    ]);

    if(!empty($sections)){

        foreach($sections as $section){

            $options .= '
            <option value="'.$section->id.'">
                '.$section->title.'
            </option>';
        }
    }

    echo json_encode([
        'status'  => true,
        'options' => $options
    ]);

    exit;
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
if(isset($_POST['action']) && $_POST['action']=='get_report_data'){

    $session = $_POST['session'];

    $bar_labels = [];
    $bar_data   = [];

    // 🔹 Histogram (Subject-wise avg)
    $q1 = mysqli_query($con,"
        SELECT 
            m.subject_id,
            s.title as subject_name,
            AVG(m.rating) as avg_rating
        FROM meta_teacher m
        JOIN teacher_feedback f ON m.feedback_id = f.id
        JOIN posts s ON m.subject_id = s.id
        WHERE f.session = '$session'
        GROUP BY m.subject_id
    ");

    while($row = mysqli_fetch_assoc($q1)){
        $bar_labels[] = $row['subject_name'];
        $bar_data[]   = round($row['avg_rating'],2);
    }

    // 🔹 Pie Chart
    $pie_labels = ['Very Good','Good','Average','Bad'];
    $pie_data   = [0,0,0,0];

    $q2 = mysqli_query($con,"
        SELECT m.rating
        FROM meta_teacher m
        JOIN teacher_feedback f ON m.feedback_id = f.id
        WHERE f.session = '$session'
    ");

    while($row = mysqli_fetch_assoc($q2)){
        $r = $row['rating'];

        if($r >= 4) $pie_data[0]++;
        elseif($r >= 3) $pie_data[1]++;
        elseif($r >= 2) $pie_data[2]++;
        else $pie_data[3]++;
    }
$i = 1;
$table_html = "";

$i = 1;
$table_html = "";

$q3 = mysqli_query($con,"
    SELECT 
        ts.subject_id,
        s.title as subject_name,
        a.Name as teacher_name,
        a.roll_no,
        AVG(m.rating) as avg_rating

    FROM teacher_subjects ts

    JOIN accounts a 
        ON ts.teacher_id = a.id

    JOIN posts s 
        ON ts.subject_id = s.id

    LEFT JOIN teacher_feedback f 
        ON f.teacher_id = ts.teacher_id 
        AND f.session = ts.session_id

    LEFT JOIN meta_teacher m 
        ON m.feedback_id = f.id 
        AND m.subject_id = ts.subject_id

    WHERE ts.session_id = '$session'
    AND a.type = 'teacher'

    GROUP BY ts.subject_id, ts.teacher_id
");

while($row = mysqli_fetch_assoc($q3)){

    $avg = round($row['avg_rating'],1);

    if($avg >= 4){
        $level = "Very Good";
    } elseif($avg >= 3){
        $level = "Good";
    } elseif($avg >= 2){
        $level = "Average";
    } else {
        $level = "Bad";
    }

    $table_html .= "
    <tr>
        <td>".$i++."</td>
        <td>".$row['subject_name']."</td>
        <td>".$row['teacher_name']."</td>
        <td>".$row['roll_no']."</td>
        <td>".$avg."</td>
        <td>".$level."</td>
    </tr>";
}

    echo json_encode([
        'bar_labels'=>$bar_labels,
        'bar_data'=>$bar_data,
        'pie_labels'=>$pie_labels,
        'pie_data'=>$pie_data,
        'table_html' => $table_html
    ]);

    exit;
}
if(isset($_POST['action']) && $_POST['action']=='get_subject'){

    $parent_id = $_POST['branch_id'] ?? $_POST['class_id'] ?? '';

    $subjects = get_posts([
        'type'   => 'subject',
        'parent' => $parent_id
    ]);

    $options = '<option value="">Select Subject</option>';

    if(!empty($subjects)){
        foreach($subjects as $sub){
            $options .= '<option value="'.$sub->id.'">'.$sub->title.'</option>';
        }
    }

    echo json_encode([
        'status'  => !empty($subjects),
        'options' => $options
    ]);
    exit;
}
if(isset($_POST['action']) && $_POST['action']=='get_exam'){

    $course_id   = $_POST['course_id'] ?? 0;
    $branch_id   = $_POST['branch_id'] ?? 0;
    $semester_id = $_POST['semester_id'] ?? 0;
    $session_id  = $_POST['session_id'] ?? 0;

    $class_id    = $_POST['class_id'] ?? 0;
    $section_id  = $_POST['section_id'] ?? 0;

    // ===============================
    // COLLEGE QUERY
    // ===============================
    if($course_id && $branch_id && $semester_id && $session_id){

        $query = mysqli_query($con,"
            SELECT ce.*, et.exam_type
            FROM create_exam ce

            LEFT JOIN exam_type et 
            ON ce.exam_type_id = et.id

            WHERE ce.course_id='$course_id'
            AND ce.branch_id='$branch_id'
            AND ce.semester_id='$semester_id'
            AND ce.session_id='$session_id'

            AND ce.status='active'
            AND et.status='active'
            AND ce.institute_id='$institute_id'

            LIMIT 1
        ");

    }

    // ===============================
    // SCHOOL QUERY
    // ===============================
    else if($class_id && $section_id){

        $query = mysqli_query($con,"
            SELECT ce.*, et.exam_type
            FROM create_exam ce

            LEFT JOIN exam_type et
            ON ce.exam_type_id = et.id

            WHERE ce.class_id='$class_id'
            AND ce.section_id='$section_id'

            AND ce.status='active'
            AND et.status='active'
            AND ce.institute_id='$institute_id'

            LIMIT 1
        ");

    }

    else{

        echo json_encode([
            'status'=>false,
            'message'=>'Missing Filters'
        ]);
        exit;
    }

    // ===============================
    // RESPONSE
    // ===============================
    if(mysqli_num_rows($query)>0){

        $row=mysqli_fetch_assoc($query);

        echo json_encode([
            'status'=>true,
            'exam_id'=>$row['id'],
            'exam_type'=>$row['exam_type'],
            'start_date'=>$row['start_date'],
            'end_date'=>$row['end_date']
        ]);

    }else{

        echo json_encode([
            'status'=>false,
            'message'=>'No Exam Found'
        ]);
    }

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

if(isset($_POST['action']) && $_POST['action']=='update_single_attendance'){

    $meta_id = $_POST['meta_id'];
    $status  = $_POST['status'];

    mysqli_query($con,"
    UPDATE attendance_meta
    SET status='$status'
    WHERE id='$meta_id'
    ");
   updateSemesterAttendance(
        $user_id,
        $course_id,
        $branch_id,
        $session_id,
        $semester
    );
    echo json_encode([
        'status'=>true,
        'message'=>'Attendance Updated Successfully'
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
// 

if(isset($_POST['action']) && $_POST['action'] == 'save_attendance'){

    $course_id = $_POST['course_id'];
    $branch_id = $_POST['branch_id'];
    $session_id = $_POST['session_id'];
    $semester = $_POST['semester'];
    $subject_id = $_POST['subject_id'];
    $date = $_POST['attendance_date'];
$attendance_data = $_POST['attendance'] ?? [];


    $check = mysqli_query($con,"
SELECT id FROM attendance 
WHERE course_id='$course_id'
AND branch_id='$branch_id'
AND session_id='$session_id'
AND semester='$semester'
AND subject_id='$subject_id'
AND attendance_date='$date'
AND institute_id='$institute_id'
");

if(mysqli_num_rows($check) > 0){

    echo json_encode([
        'status' => false,
        'message' => 'Attendance already exists for this date'
    ]);
    exit;
}
    // 1. INSERT MAIN ATTENDANCE ROW
    $q = mysqli_query($con,"
        INSERT INTO attendance 
        (course_id, branch_id, session_id, semester, subject_id, attendance_date, institute_id)
        VALUES 
        ('$course_id','$branch_id','$session_id','$semester','$subject_id','$date','$institute_id')
    ");

    $attendance_id = mysqli_insert_id($con);

    // 2. INSERT STUDENT META
    foreach($attendance_data as $a){

        $user_id = $a['user_id'];
        $status  = $a['status'];

        mysqli_query($con,"
            INSERT INTO attendance_meta
            (attendance_id, user_id, status)
            VALUES
            ('$attendance_id','$user_id','$status')
        ");
    }
    
    echo json_encode([
        'status' => true,
        'message' => 'Attendance Saved Successfully'

    ]);

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
 $term_id=$_POST['term_id'] ?? '';
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
    // $semester = '';
    // if($subject_id!=''){

    //     $sub_q = $con->prepare("SELECT semester FROM `courses` WHERE id=? LIMIT 1");
    //     $sub_q->bind_param("i",$subject_id);
    //     $sub_q->execute();
    //     $sub_q_result=$sub_q->get_result();

    // if($sub_q_result && $sub_q_result->num_rows>0){
    //     $sub_row = $sub_q_result->fetch_assoc();
    //     $semester = $sub_row['semester'];
    // }
    // }
    // if($semester!=''){
    //   $sql1.=" AND id IN (SELECT user_id FROM `usermeta` WHERE meta_key='semester' AND meta_value=?)";
    //   $params[]=$semester;
    //   $types .="i";
    // }
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

// result marks
//first get the result_id from the resuly table
$result_id=0;
$select=mysqli_query($con,"SELECT result_id FROM `result` WHERE class_id='$class_id' AND section_id='$section_id' AND subject_id='$subject_id' AND term_id='$term_id'");
while($row_fet=mysqli_fetch_assoc($select)){
  $result_id=$row_fet['result_id'];
}
// get the marks from result-marks
$mark_value='';
$marks=mysqli_query($con,"SELECT marks FROM `result_marks` WHERE result_id='$result_id' AND student_id='$enroll_id'");
while($row=mysqli_fetch_assoc($marks)){
  $mark_value=$row['marks'];
}



        $data1[]=[
            'Sno'=>$count,
'Enroll_ID'=>$enroll_id,

'Student_Name'=>ucfirst($name),
'Marks'=>$mark_value
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
  $course_id=$_POST['course_id'] ?? '';
  $branch_id=$_POST['branch_id'] ?? '';
  $semester_id=$_POST['semester'] ??'';

                 $data=[];
                 $count=1;
                  $sql="SELECT * FROM accounts WHERE type=?";
                  $params=['student'];
                  $types = "s";
                  if($institute_type=='school'){
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
                  }
               
                   else{
if($course_id!=''){
 $sql .=" AND id IN(SELECT user_id FROM usermeta WHERE meta_key='st_course' AND meta_value=?)" ;
 $params[]=$course_id;
 $types .="s";
}
if($branch_id!=''){
  $sql .=" AND id IN(SELECT user_id FROM usermeta WHERE meta_key='st_branch' AND meta_value=?)";
  $params[]=$branch_id;
  $types .="s";
}
if($semester_id!=''){
  $sql .=" AND id IN(SELECT user_id FROM usermeta WHERE meta_key='semester' AND meta_value=?)";
  $params[]=$semester_id;
  $types .="s";
}
                   }
                       $query=$con->prepare($sql); 
                          $query->bind_param($types, ...$params);
                          $query->execute();
                          $query_result=$query->get_result();
                       while($row=$query_result->fetch_assoc()){ 
                        $count++;
                        $user_id=$row['id'];
                        if($institute_type=='school'){
                        $user_edit='<a href="user-account.php?class='.$class_id.'&section='.$section_id.'&edit_student='.$user_id.'" class ="btn btn-sm btn-success"><i class="fa fa-pencil-alt"></i></a>';
                        $user_delete='<a href="user-account.php?class='.$class_id.'&section='.$section_id.'&delete_student='.$user_id.'" class="btn btn-sm btn-success mx-2"><i class="fa fa-trash-alt"></i></a>' ;
                        
                        }
                        
                        else{
                          $user_edit='<a href="user-account.php?course='.$course_id.'&branch='.$branch_id.'&edit_student='.$user_id.'" class ="btn btn-sm btn-success"><i class="fa fa-pencil-alt"></i></a>';
                        $user_delete='<a href="user-account.php?course='.$course_id.'&branch='.$branch_id.'&semester_id='.$semester_id.'&delete_student='.$user_id.'" class="btn btn-sm btn-success mx-2"><i class="fa fa-trash-alt"></i></a>' ;
                        
                        }
                        
                        $dob=get_usermeta($user_id,'dob'); 
                        $phone=get_usermeta($user_id,'mobile');
                         $st_class=get_usermeta($user_id,'st_class'); 
                         $st_section=get_usermeta($user_id,'st_section');

                        $photo_meta = get_usermeta($row['id'], 'student_photo');

$photo = '';
$photo = '';

if (is_array($photo_meta)) {
    $photo = $photo_meta[0]->meta_value ?? '';
} else {
    $photo = $photo_meta ?? '';
}
                         // dyamic feilds fetch karo 
                         $fields=mysqli_query($con,"SELECT * FROM `fields` WHERE institute_id='$institute_id' AND form_type='student'");
                          $field_key=[];
                          while($f=mysqli_fetch_assoc($fields)){
                            $fields_key[]=$f['field_key'];

                          }
                         
                         // for every user
                         $row_data = [ 
'sno'=>$count++,
                           'roll_no'=> $row['roll_no'] , 
                        
                            'photo'=>'<img src="uploads/student_photo/'.$photo.'" width="60">', 
                            'name'=>$row['Name'], 
                            'email'=>$row['email'], 
                            'phone'=>$phone, 
                            'dob'=>$dob, 
                           
       
                           ]; 
                           foreach($fields_key as $key){
                            $value=get_usermeta($user_id,$key);
                            $row_data[$key]=$value ?? '';
                           }
                           $row_data['action']=$user_edit.''.$user_delete;
                           $data[]=$row_data;
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
    $term_id=$_POST['res_term'] ?? '';
    $marks      = $_POST['marks'] ?? [];

    if(empty($class_id) || empty($section_id) || empty($subject_id) || empty($term_id)){
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
         AND subject_id=?
         AND term_id=?"
    );
$check->bind_param("iiii",$class_id,$section_id,$subject_id,$term_id);
$check->execute();
$check_result=$check->get_result();
    if($check_result->num_rows > 0){
        $row = $check_result->fetch_assoc();
        $result_id = $row['result_id'];
    } else {
      $check->close();
       $stmt= $con->prepare(
            "INSERT INTO result (class_id,section_id,subject_id,term_id)
             VALUES(?,?,?,?)"
        );
        $stmt->bind_param("iiii",$class_id,$section_id,$subject_id,$term_id);
        $stmt->execute();
        if($stmt->error){
          echo $stmt->error;
          exit;
        }
        //$stmt_result=$stmt->get_result();
        $result_id = $stmt->insert_id;
       
    }

    // 2️⃣ Insert or Update Marks
    // print_r($_POST);
    // exit;
    foreach($marks as $student_id => $mark){
$mark=(int)$mark;
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

// // get subject 
// if(!empty($_POST['action']) && $_POST['action']=='get_subjects'){ 
//   $exam_id = $_POST['exam_id'] ?? '';
//   $exam_q = mysqli_query($con," SELECT semester_id FROM create_exam WHERE id='$exam_id' LIMIT 1 ");
//   $exam = mysqli_fetch_assoc($exam_q); 
//   $semester = $exam['semester_id'] ?? '';
//   $sub_q = mysqli_query($con," SELECT id, name FROM courses WHERE semester='$semester' "); 
//   echo "<option value=''>--Select Subject--</option>"; 
//   while($sub = mysqli_fetch_assoc($sub_q)){ 
//     echo "<option value='{$sub['id']}'>{$sub['name']}</option>"; 
//     } 
//     exit;
//      }