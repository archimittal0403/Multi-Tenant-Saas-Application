
<?php
session_start();
 include('includes/config.php');
 include('includes/functions.php');
?> 

<?php

$institute_id = $_SESSION['institute_id'];
      $institute_type=$_SESSION['system_type'];

?>
<?php

 if(isset($_POST['type']) && $_POST['type'] == 'student' && isset($_POST['email']) && !empty($_POST['email'])){
  
      $name = isset($_POST['name'])?$_POST['name']:'';
   $st_photo = '';
if(!empty($_FILES['st_image']['name'])){
    $st_photo = time().'_'.$_FILES['st_image']['name'];
    move_uploaded_file($_FILES['st_image']['tmp_name'], "../uploads/student_photo/".$st_photo);
}
    $dob = isset($_POST['dob'])?$_POST['dob']:'';
    $mobile = isset($_POST['mobile'])?$_POST['mobile']:'';
    $email = trim(isset($_POST['email'])?$_POST['email']:'');
if(empty($email) || !filter_var($email,FILTER_VALIDATE_EMAIL)){
  echo json_encode([
    'success' => false,
    'message' => 'please enter a valid email address'
  ]);
  exit;
}
    $address = isset($_POST['address'])?$_POST['address']:'';
     $country = isset($_POST['country'])?$_POST['country']:'';
   $state = isset($_POST['state'])?$_POST['state']:'';
   $zip = isset($_POST['pincode'])?$_POST['pincode']:'';
 $password = date('dmY',strtotime($dob));
     $md_password = password_hash($password,PASSWORD_DEFAULT);
    

                  $father_name =isset($_POST['father_name'])?$_POST['father_name']:'';  
                   $father_mobile =isset($_POST['father_mobile'])?$_POST['father_mobile']:'';   
                   $father_email=isset($_POST['father_email'])?$_POST['father_email']:'';
                      $mother_name =isset($_POST['mother_name'])?$_POST['mother_name']:'';  
                           $mother_mobile =isset($_POST['mother_mobile'])?$_POST['mother_mobile']:''; 
                           $mother_email=isset($_POST['mother_email'])?$_POST['mother_email']:'';
                           $parent_address =isset($_POST['parent_address'])?$_POST['parent_address']:''; 
                            $parent_country =isset($_POST['parent_country'])?$_POST['parent_country']:'';  
                    $parent_state =isset($_POST['parent_state'])?$_POST['parent_state']:'';
                       $parent_pincode =isset($_POST['parent_pincode'])?$_POST['parent_pincode']:''; 


                   $school_name =isset($_POST['school_name'])?$_POST['school_name']:'';   
                    $class =isset($_POST['class'])?$_POST['class']:'';
                   $board=isset($_POST['board'])?$_POST['board']:'';
                     $total_mark =isset($_POST['total_mark'])?$_POST['total_mark']:'';
                      $obtain_mark=isset($_POST['obtain_mark'])?$_POST['obtain_mark']:''; 
                     $percent=isset($_POST['percentage'])?$_POST['percentage']:'';

$st_course=isset($_POST['st_course'])?$_POST['st_course']:'';
$st_branch=isset($_POST['st_branch'])?$_POST['st_branch']:'';
$session=isset($_POST['session'])?$_POST['session']:'';

                     $st_class=isset($_POST['st_class'])?$_POST['st_class']:'';
                      $st_section=isset($_POST['st_section'])?$_POST['st_section']:'';
                         $subject_stream=isset($_POST['subject_stream'])?$_POST['subject_stream']:'';
                    $doa =isset($_POST['doa'])?$_POST['doa']:'';
                         $type =isset($_POST['type'])?$_POST['type']:'';
                      $date_added =date('Y-m-d');
                           $payment_method=isset($_POST['payment_method'])?$_POST['payment_method']:'';
                     
   $check_query=$con->prepare("SELECT id FROM accounts WHERE email=? AND institute_id=?");
   $check_query->bind_param("si",$email,$institute_id);
   $check_query->execute();
$result=$check_query->get_result();
       if($result->num_rows>0){
    echo json_encode([
  'success' => false,
  'message' => 'Email already exists'
]);
exit;
       }
     else{

   

    if (empty($institute_id)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Institute ID missing. Please login again.'
    ]);
    exit;
}

$query = $con->prepare(
"INSERT INTO accounts (`name`,`email`,`password`,`type`,`institute_id`)
 VALUES (?,?,?,?,?)"
);
$query->bind_param("ssssi",$name,$email,$md_password,$type,$institute_id);
$query->execute();

$user_id = $con->insert_id;

    }
     if($institute_type=='school'){
      $usermeta = array(
       'dob'=>$dob,
       'st_photo'=>$st_photo,
        'mobile'=>$mobile,
        'payment_method'=>$payment_method,
        'st_class'=>$st_class,
        'address'=>$address,
        'country'=>$country,
        'state'=>$state,
        'pincode'=>$zip,
        'father_name'=>$father_name,
        'father_mobile'=>$father_mobile,
        'father_email'=>$father_email,
        'mother_name'=>$mother_name,
        'mother_mobile'=>$mother_mobile,
        'mother_email'=>$mother_email,
        'parent_address'=>$parent_address,
        'parent_country'=>$parent_country,
        'parent_state'=>$parent_state,
        'parent_pincode'=>$parent_pincode,
        'school_name'=>$school_name,
        'board'=>$board,
        'total_mark'=>$total_mark,
        'obtain_mark'=>$obtain_mark,
         'percentage'=>$percent,
      
        'st_section'=>$st_section,
        'subject_stream'=> $subject_stream,
          'doa'=>$doa,
        'type'=>$type,


     );
     }
     else{
         $usermeta = array(
       'dob'=>$dob,
       'student_photo'=>$st_photo,
        'mobile'=>$mobile,
        'payment_method'=>$payment_method,
        'st_course'=>$st_course,
        'address'=>$address,
        'country'=>$country,
        'state'=>$state,
        'pincode'=>$zip,
        'father_name'=>$father_name,
        'father_mobile'=>$father_mobile,
        'father_email'=>$father_email,
        'mother_name'=>$mother_name,
        'mother_mobile'=>$mother_mobile,
        'mother_email'=>$mother_email,
        'parent_address'=>$parent_address,
        'parent_country'=>$parent_country,
        'parent_state'=>$parent_state,
        'parent_pincode'=>$parent_pincode,
        'school_name'=>$school_name,
        'board'=>$board,
        'total_mark'=>$total_mark,
        'obtain_mark'=>$obtain_mark,
         'percentage'=>$percent,
      
        'st_branch'=>$st_branch,
        'session'=>$session,
          'doa'=>$doa,
        'type'=>$type,


     );
     }
    //  echo json_encode($usermeta);die;

                      
   $check_query=$con->prepare("SELECT * FROM accounts WHERE email=? AND institute_id=?");
$check_query->bind_param("si",$father_email, $institute_id);
$check_query->execute();

$result=$check_query->get_result();
       if($result->num_rows>0){
     echo json_encode([
  'success' => false,
  'message' => 'parent email already exists'
]);
exit;
       }
     else{
$md_password=password_hash($father_mobile,PASSWORD_DEFAULT);
$institute_id=$_SESSION['institute_id'];
     $query=$con->prepare("INSERT INTO accounts (`name`,`email`,`password`,`type`,`institute_id`) VALUES (?,?,?,?,?)"); 
     $type='parent';
     $query->bind_param("ssssi",$father_name,$father_email,$md_password,$type,$institute_id);
 
    if(!$query->execute()){
    die($query->error);
}
         $parent_id=$con->insert_id;
     
     $child= [$user_id];
     $child=serialize($child);
     $stxtchild=$con->prepare("INSERT INTO usermeta (`user_id`,`meta_key`,`meta_value`) VALUES (?,'children',?)");
  
      $stxtchild->bind_param("is",$parent_id,$child);
     if(!$stxtchild->execute()){
        die($stxtchild->error);
     }
    
    }

  $stmt=$con->prepare("INSERT INTO usermeta (`user_id`, `meta_key`, `meta_value`) VALUES (?,?,?)");
    foreach($usermeta as $key => $value){
       if ($value === '' || $value === null) {
        continue;
    }

    if (is_array($value)) {
        $value = json_encode($value);
    }

      $stmt->bind_param("iss",$user_id,$key,$value);
      if(!$stmt->execute()){
        die($stmt->error);
      }
    }
      $response = array(
     'success' => TRUE,
     'std_id' =>$user_id
    );
header('Content-Type: application/json');
echo json_encode($response);
exit;

     }
   

?>

<?php
$classes = get_posts([
    'type' =>'class'
   

]);
$courses=get_posts([
  'type' =>'course'
]);
$branches=get_posts([
  'type' =>'branch'
]);
$semester=get_posts([
  'type' =>'semester'
]);

$user = $_GET['user'] ?? '';


?>