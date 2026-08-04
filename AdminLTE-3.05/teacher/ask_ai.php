<?php
session_start();
include('includes/config.php');
include('includes/functions.php');
//echo $_SESSION['user_id'];


?>
 <!-- now we will get the answer from the data we have saved indide our database -->
 
<?php
$user_id=$_SESSION['user_id'];
$message = isset($_POST['message']) ? strtolower(trim($_POST['message'])) : '';
$response="Sorry,we can not get you the answer";
$select_query=mysqli_query($con,"SELECT * FROM `chatbot_faq` WHERE status=1");
while($row_fetch=mysqli_fetch_assoc($select_query)){
  $keywords=explode(',',strtolower($row_fetch['keyword']));
  $question=explode(" ",$message);
  foreach($keywords as $keyword){
    if(strlen($keyword)>=2 && 
    in_array($keyword,$question)){
      $response=$row_fetch['answer'];
      break 2;
    }
  }
}
// 
// in tbis we will get the details for the couses of that partiular semester
if(preg_match('/subject|courses/i',$message)){

    $semester = get_usermeta($user_id,'semester');

    $course = mysqli_query($con,
        "SELECT name FROM `courses` 
         WHERE semester='$semester'");

    $subjects = [];

    while($row = mysqli_fetch_assoc($course)){
        $subjects[] = $row['name'];
    }

    if(count($subjects) > 0){

        if(preg_match('/how many|total/i',$message)){
            $response = "You have "
                . count($subjects)
                . " subjects in your semester.";
        }

        elseif(preg_match('/name|list|show/i',$message)){
            $response = "Your subjects are: "
                . implode(", ", $subjects);
        }

    } else {
        $response = "No subjects found for your semester.";
    }
}
  
// get the user-details
$user_id=$_SESSION['user_id'];
$user_data=[];
$select_query=mysqli_query($con,"SELECT * FROM `usermeta` WHERE user_id='$user_id'");
while($row_fetch=mysqli_fetch_assoc($select_query)){
    $user_data[$row_fetch['meta_key']]=$row_fetch['meta_value'];
}
        $class_id=$user_data['st_class'];
        $class=mysqli_query($con,"SELECT title FROM `posts` WHERE id='$class_id'");
        $fetch_class=mysqli_fetch_assoc($class);
        $class_name=$fetch_class['title'];
        $section_id=$user_data['st_section'];
        $section=mysqli_query($con,"SELECT title FROM `section` WHERE id='$section_id'");
        $fetch_section=mysqli_fetch_assoc($section);
        $section_name=$fetch_section['title'];
        $dob=$user_data['dob'];
        $doa=$user_data['doa'];
        $mobile=$user_data['mobile'];
        $address=$user_data['address'];
        $country=$user_data['country'];
        $state=$user_data['state'];
        $pincode=$user_data['pincode'];
   if(preg_match('/dob/i',$message) && $dob != ''){
    $response = "Your registered date of birth is $dob";
} 
elseif(preg_match('/class/i',$message) && $class_name != ''){
    $response = "Your registered Class is $class_name";
} 
elseif(preg_match('/section/i',$message) && $section_name != ''){
    $response = "Your registered Section is $section_name";
} 
elseif(preg_match('/mobile/i',$message) && $mobile != ''){
    $response = "Your registered mobile no is $mobile";
} 
elseif(preg_match('/address/i',$message) && $address != ''){
    $response = "Your registered address is $address";
} 
elseif(preg_match('/country/i',$message) && $country != ''){
    $response = "Your registered country is $country";
} 
elseif(preg_match('/state/i',$message) && $state != ''){
    $response = "Your registered state is $state";
} 
elseif(preg_match('/pincode|pin/i',$message) && $pincode != ''){
    $response = "Your registered pincode is $pincode";
} 
else{
    $response="Your information is not available";
}
    

// now we will work over attendance to check my status on the specific data and specific subject
$user_id=$_SESSION['user_id'];
preg_match('/\b\d{2}\/\d{2}\/\d{2}\b/', $message, $date_match);
$user_date = $date_match[0] ?? null;
if($user_date){
$user_date=date('Y-m-d',strtotime(str_replace('/','-',$user_date)));
}
preg_match('/subject\s+([a-zA-Z0-9\-]+)/i', $message, $subject_match);
$user_subject = $subject_match[1] ?? null;
if(preg_match('/attendance|status/i',$message)){
    
$attendance=[];
$select_query=mysqli_query($con,"SELECT * FROM `attendance1` WHERE item_id='$user_id'");
while($row_fetch=mysqli_fetch_assoc($select_query)){
$attendance[$row_fetch['meta_key']]=$row_fetch['meta_value'];
}
$status=$attendance['status'];
$dob=$attendance['dob'];
$subject_id=$attendance['at_subject'];

$subject=mysqli_query($con,"SELECT name FROM `courses` WHERE id='$subject_id'");
$row_fetch=mysqli_fetch_assoc($subject);
$subject_name=$row_fetch['name'];
// debug karo
// echo $user_date;
// echo $user_subject;
// echo $status;
// echo $dob;
// echo $subject_name;
if(
    $user_date==$dob &&
    str_replace(['-',' '],'',strtolower($user_subject)) ==
    str_replace(['-',' '],'',strtolower($subject_name))
){
    $response="You are $status on $dob for $subject_name";
}else{
    $response="No matching attendance record found.";
}
}
//


//insert into chat-message

$insert_chat=mysqli_query($con,"INSERT INTO `chat_messages` (user_id,message,reply) VALUES($user_id,'$message','$response')");
?>