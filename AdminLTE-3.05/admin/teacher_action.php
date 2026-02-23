<?php
session_start();
include('includes/config.php');
include('includes/functions.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

if (empty($_SESSION['college_id'])) {
    echo json_encode(['success'=>false,'message'=>'college_id missing']);
    exit;
}

$type  = $_POST['type'] ?? '';
$email = $_POST['email'] ?? '';
$name  = $_POST['name'] ?? '';
$mobile=$_POST['mobile'] ?? '';
$address=$_POST['address'] ?? '';
 $dob=$_POST['dob'] ?? '';
 $qualification=$_POST['qualification'] ?? '';
 $experience=$_POST['experience'] ?? '';
 $gender=$_POST['gender'] ?? '';
 $class=$_POST['class'] ?? '';
 $section=$_POST['section'] ?? '';
 $subject=$_POST['subject'] ?? '';
 $salary=$_POST['salary'] ?? '';
 $bank=$_POST['bank'] ?? '';
 $account=$_POST['aco'] ?? '';
 $ifsc=$_POST['ifsc'] ?? '';


if ($type !== 'teacher' || $email == '') {
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit;
}

$college_id = $_SESSION['college_id'];
$password   = date('dmY', strtotime($_POST['dob'] ?? ''));
$md_pass    = password_hash($password, PASSWORD_DEFAULT);

/* 🔎 Duplicate email check */
$check = $con->prepare("SELECT id FROM accounts WHERE email=? AND college_id=?");
$check->bind_param("si",$email,$college_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'Email already exists']);
    exit;
}

/* ✅ INSERT account */
$insert =$con->prepare(
    "INSERT INTO accounts (type,email,password,Name,college_id)
     VALUES ('teacher',?,?,?,?)"
);
$insert->bind_param("sssi",$email,$md_pass,$name,$college_id);
$insert->execute();

if (!$insert) {
    echo json_encode(['success'=>false,'message'=>mysqli_error($con)]);
    exit;
}

/* 🆔 VERY IMPORTANT */
$user_id = mysqli_insert_id($con);


$usermeta=array(
  'college_id'=>$college_id,
  'phone'=>$mobile,
  'address'=>$address,
  'dob'=>$dob,
  'qualification'=>$qualification,
  'experience'=>$experience,
  'gender'=>$gender,
  'class'=>$class,
  'section'=>$section,
  'subject'=>$subject,
  'salary'=>$salary,
  'bank'=>$bank,
  'ano'=>$account,
  'ifsc'=>$ifsc,
);
foreach($usermeta as $key=>$value){
      if($value==''||$value=='null'){
    continue;
  }
    if(is_array($value)){
    $value=json_encode($value);
  }
  $query=$con->prepare("INSERT INTO `usermeta` (`user_id`,`meta_key`,`meta_value`) VALUES(?,?,?)");
  $query->bind_param("isi",$user_id,$key,$value);
  $query->execute();
}
$response=array(
  'success'=>'true',
  'std_id'=>$user_id
);
/* ✅ SUCCESS RESPONSE */
echo json_encode([
    'success' => true,
    'std_id'  => $user_id
]);
exit;
?>
