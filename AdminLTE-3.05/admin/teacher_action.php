<?php
session_start();
//print_r($_SESSION);

include('includes/config.php');
require_once('includes/functions.php');


$institute_id = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'] ?? '';
$institute_code=$_SESSION['institute_code'];
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

if (empty($_SESSION['institute_id'])) {
    echo json_encode(['success'=>false,'message'=>'institute_id missing']);
    exit;
}
if (!empty($_POST['action']) && $_POST['action'] === 'get_teacher_details') {

  
        $class_id   = $_POST['class_id'] ?? '';
  $section_id = $_POST['section_id'] ?? '';
  $course_id=$_POST['course_id'] ?? '';
  $branch_id=$_POST['branch_id'] ?? '';
  $semester_id=$_POST['semester_id'] ??'';
$role = $_POST['role'] ?? '';

$sql = "SELECT * FROM accounts WHERE type='teacher' AND institute_id=?";
$params = [$institute_id];
$types = "i";
$data = [];
$count = 1;

if ($role != '') {
    $sql .= " AND id IN (
        SELECT user_id FROM usermeta 
        WHERE meta_key='role' AND meta_value=?
    )";
    $params[] = $role;
    $types .= "s";
}

   if($institute_type == 'school'){

   if($class_id != ''){
      $sql .= " AND id IN (
         SELECT user_id FROM usermeta 
         WHERE meta_key='class_name' AND meta_value=?
      )";
      $params[] = $class_id;
      $types .= "s";
   }

   if($section_id != ''){
      $sql .= " AND id IN (
         SELECT user_id FROM usermeta 
         WHERE meta_key='section_name' AND meta_value=?
      )";
      $params[] = $section_id;
      $types .= "s";
   }
}

               
                   else{


if($course_id!=''){
 $sql .=" AND id IN(SELECT user_id FROM usermeta WHERE meta_key='course_name' AND meta_value=?)" ;
 $params[]=$course_id;
 $types .="s";
}
if($branch_id!=''){
  $sql .=" AND id IN(SELECT user_id FROM usermeta WHERE meta_key='branch_name' AND meta_value=?)";
  $params[]=$branch_id;
  $types .="s";
}
  if($institute_type=='college'){
$depart_meta = get_metadata($branch_id,'department');
$depart = '';

if(is_array($depart_meta)){
    $depart = $depart_meta[0]->meta_value ?? '';
}

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
                      
                        $user_id=$row['id'];
                        if($institute_type=='school'){
                 $user_edit = '<a href="teacher_edit.php?edit_teacher='.$user_id.'" class="btn btn-sm btn-success">
<i class="fa fa-pencil-alt"></i></a>';
$user_delete = '<a href="teacher.php?delete_teacher='.$user_id.'" class="btn btn-sm btn-danger mx-2">
<i class="fa fa-trash-alt"></i></a>';
  $assign_subject = "<a href='assign_subject.php?teacher_id=".$row['id']."&class_id=".$class_id."&section_id=".$section_id."' 
class='btn btn-success btn-sm'>
Assign Subject
</a>";
                        }
                        
                        else{
      $user_edit = '<a href="teacher_edit.php?edit_teacher='.$user_id.'" class="btn btn-sm btn-success">
<i class="fa fa-pencil-alt"></i></a>';
$user_delete = '<a href="teacher.php?delete_teacher='.$user_id.'" class="btn btn-sm btn-danger mx-2">
<i class="fa fa-trash-alt"></i></a>';
  $assign_subject = "<a href='assign_subject.php?teacher_id=".$row['id']."' 
class='btn btn-success btn-sm'>
<i class='fa fa-plus'></i> Assign Subject
</a>";
                        }
                        
                        $dob=get_usermeta($user_id,'dob'); 
                        $phone=get_usermeta($user_id,'mobile');
                         $st_class=get_usermeta($user_id,'st_class'); 
                     
                         $st_section=get_usermeta($user_id,'st_section');

                        $photo_meta = get_usermeta($row['id'], 'th_image');

$photo = '';


if (is_array($photo_meta)) {
    $photo = $photo_meta[0]->meta_value ?? '';
} else {
    $photo = $photo_meta ?? '';
}
                         // dyamic feilds fetch karo 
                         $fields=mysqli_query($con,"SELECT * FROM `fields` WHERE institute_id='$institute_id' AND form_type='teacher' AND visibility=1 AND show_on='teacher'");
                          $field_key=[];
                          while($f=mysqli_fetch_assoc($fields)){
                            $field_key[]=$f['field_key'];

                          }
                         
                         // for every user
                         $row_data = [ 
'sno'=>$count++,
                           'roll_no'=> $row['roll_no'] , 
                        
                            'photo'=>'<img src="uploads/teacher_photo/'.$photo.'" width="60">', 
                            'name'=>$row['Name'], 
                            'email'=>$row['email'], 
                            'phone'=>$phone, 
                            'dob'=>$dob, 
                         
    'department'=> $depart ?? '',
      
                           ]; 
                           foreach($field_key as $key){
                            $value=get_usermeta($user_id,$key);
                     
       

    // 🔥 FIX for branch
 
                            $row_data[$key]=$value ?? '';
                           }
      $row_data['action'] = $assign_subject . " " . $user_edit . " " . $user_delete;
   
  $row_data['action'] = $assign_subject . " " . $user_edit . " " . $user_delete;

/* ================= VIEW SUBJECT BUTTON ================= */

if($institute_type == 'school'){

    $view_subject = '
    <a href="view_teacher_subject.php?teacher_id='.$user_id.'&class_id='.$class_id.'&section_id='.$section_id.'" 
       class="btn btn-info btn-sm">
       <i class="fa fa-book"></i> View Subject
    </a>';

}else{

    $view_subject = '
    <a href="view_teacher_subject.php?teacher_id='.$user_id.'&course_id='.$course_id.'&branch_id='.$branch_id.'&semester_id='.$semester_id.'" 
       class="btn btn-info btn-sm">
       <i class="fa fa-book"></i> View Subject
    </a>';
}

$row_data['view_subject'] = $view_subject;

$data[] = $row_data;
                        }
                        echo json_encode([ 
                           "draw" => intval($_POST['draw'] ?? 1),
                         "recordsTotal"=>count($data),
                          "recordsFiltered"=>count($data), 
                          "data"=>$data 
                        ]);
                         exit; 
                        }


$upload_dir =  __DIR__ . '/uploads/teacher_photo/';

if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true);
}

if(!empty($_FILES['th_image']['name'])){
    $th_photo = time().'_'.$_FILES['th_image']['name'];
    move_uploaded_file($_FILES['th_image']['tmp_name'], $upload_dir.$th_photo);
}
$type  = $_POST['type'] ?? '';
$email = $_POST['email'] ?? '';
$name  = $_POST['name'] ?? '';
$mobile=$_POST['mobile'] ?? '';
$address=$_POST['address'] ?? '';
 $dob=$_POST['dob'] ?? '';
 $qualification=$_POST['qualification'] ?? '';
 $experience=$_POST['experience'] ?? '';
 $doj=$_POST['doj'] ?? '';
 $gender=$_POST['gender'] ?? '';
 $class=$_POST['class'] ?? '';
 $course=$_POST['course'] ?? '';
 $branch=$_POST['branch'] ?? '';
 $semester=$_POST['semester'] ?? '';
 $section=$_POST['section'] ?? '';
 $subject=$_POST['subject'] ?? '';
 $salary=$_POST['salary'] ?? '';
 $bank=$_POST['bank'] ?? '';
 $account=$_POST['aco'] ?? '';
 $role = $_POST['role'] ?? '';
 $ifsc=$_POST['ifsc'] ?? '';


if ($type !== 'teacher' || $email == '') {
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit;
}


$password   = date('dmY', strtotime($_POST['dob'] ?? ''));
$md_pass    = password_hash($password, PASSWORD_DEFAULT);

/* 🔎 Duplicate email check */
$check = $con->prepare("SELECT id FROM accounts WHERE email=? AND institute_id=?");
$check->bind_param("si",$email,$institute_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'Email already exists']);
    exit;
}

/* ✅ INSERT account */
$insert =$con->prepare(
    "INSERT INTO accounts (type,email,password,Name,institute_id)
     VALUES ('teacher',?,?,?,?)"
);
$insert->bind_param("sssi",$email,$md_pass,$name,$institute_id);
$insert->execute();

if (!$insert) {
    echo json_encode(['success'=>false,'message'=>mysqli_error($con)]);
    exit;
}

/* 🆔 VERY IMPORTANT */
$user_id = mysqli_insert_id($con);


// generate_teacher_id 
$dob = $_POST['dob']; // 🔥 direct form se lo

$year_full = date('y', strtotime($dob));


$name_part = explode(' ', $name);
$first_name = strtoupper(substr($name_part[0], 0, 2));

$teacher_id = $first_name . $institute_code . $year_full . $user_id;
// NOw enter or insert the teacher id into our account table

$insert_rool = mysqli_query($con,"UPDATE accounts SET roll_no='$teacher_id' WHERE id='$user_id'");


foreach($_POST as $key =>$value){

    if(in_array($key, ['name','email','type'])){
        continue;
    }

    if($value==''){
        continue;
    }

    if(is_array($value)){
        $value = json_encode($value);
    }

    $query=$con->prepare("INSERT INTO usermeta (user_id,meta_key,meta_value) VALUES(?,?,?)");
    $query->bind_param("iss",$user_id,$key,$value);
    $query->execute();
}
if(!empty($th_photo ?? '')){
    $query=$con->prepare("INSERT INTO usermeta (user_id,meta_key,meta_value) VALUES(?,?,?)");
    $meta_key='th_image';
    $query->bind_param("iss",$user_id,$meta_key,$th_photo);
    $query->execute();
}
//save_dynamic_fields($user_id, 'teacher');
  

/* ✅ SUCCESS RESPONSE */
echo json_encode([
    'success' => true,
    'std_id'  => $user_id
]);

 