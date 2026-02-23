<?php

if (!function_exists('get_the_teachers')) {
    function get_the_teachers() {
        // function body
    }
}


function get_timetable($day,$period_id,$class_id,$section_id){
    global $con;
    $select="SELECT p.id FROM posts p 
    INNER JOIN metadata m1 ON m1.item_id=p.id AND m1.meta_key='day_name' AND m1.meta_value=?
    INNER JOIN metadata m2 ON m2.item_id=p.id AND m2.meta_key='period_id' AND m2.meta_value=?
    INNER JOIN metadata m3 ON m3.item_id=p.id AND m3.meta_key='class' AND m3.meta_value=?
       INNER JOIN metadata m4 ON m4.item_id=p.id AND m4.meta_key='section' AND m4.meta_value=?
       WHERE p.type='timetable' AND p.status='publish'
    ";
    $stmt=$con->prepare($select);
    $stmt->bind_param("siii",$day,$period_id,$class_id,$section_id);
    $stmt->execute();
    $result=$stmt->get_result();
    if($result->num_rows==0){
        return [];
    }
    $post=$result->fetch_assoc();
    $post_id=$post['id'];
    // select teacher_id from metadata
    $meta=$con->prepare("SELECT meta_key,meta_value FROM `metadata` WHERE item_id=? AND meta_key IN ('teacher_id','subject_id')");
    $meta->bind_param("i",$post_id);
    $meta->execute();
    $meta_result=$meta->get_result();
    $data=[];
    while($row=$meta_result->fetch_assoc()){
        if($row['meta_key']=='teacher_id'){
            $tid=intval($row['meta_value']);
                $teacher=$con->prepare("SELECT Name FROM `accounts` WHERE id=?");
                $teacher->bind_param("i",$tid);
                $teacher->execute();
                $teacher_result=$teacher->get_result();
             
               if($teacher_row = $teacher_result->fetch_assoc()){
                $data['teacher'] = $teacher_row['Name'];
            }
            }
            if($row['meta_key']=='subject_id'){
                $sid=intval($row['meta_value']);
                    $subject=$con->prepare("SELECT name FROM `courses` WHERE id=?");
                    $subject->bind_param("i",$sid);
                    $subject->execute();
                    $subject_result=$subject->get_result();
                               if($subject_row = $subject_result->fetch_assoc()){
                $data['subject'] = $subject_row['name'];
                }
            }
            }
            return $data;
            echo $sql;
print_r($values);
exit;
        }
    



function get_the_classes()
{

    $con=mysqli_connect('localhost', 'root', '', 'sms_projects');

if(!$con){
    echo 'connection failed';
}
    $output= array();

     $query=$con->prepare('SELECT * FROM classes');
     $query->execute();
     $result=$query->get_result();
     while($row =$result->fetch_object() ){
        $output[]= $row;
     }
     return $output;
}


function get_post(array $args = [], string $type ='object')
{
    global $con;
    $condition="";
    $values=[];
    $types="";
    if(!empty($args))
    {
        $condition_ar=[];
        foreach($args as $k => $v)
        {
            ///$v = mysqli_real_escape_string($con,$v);
            $condition_ar[] = "$k = ?";
            $values[]=$v;
            $types.="s";

        }
    
    $condition = "WHERE " . implode(" AND ", $condition_ar);

    }

    
    $sql = "SELECT * FROM posts $condition";
    $query = $con->prepare($sql);
      if (!empty($values)) {
        $query->bind_param($types, ...$values);
    }
    $query->execute();
    $result = $query->get_result();
 return ($type === 'array')
        ? $result->fetch_assoc()
        : $result->fetch_object();
}
function get_posts(array $args = [], string $type = 'object')
{
    global $con;

    $condition_ar = [];
    $values = [];
    $types = "";

    if (!empty($args)) {
        foreach ($args as $k => $v) {
            $condition_ar[] = "$k = ?";
            $values[] = $v;
            if(is_int($v)){
                $types .="i";
            }
            else{
                $types .="s";
            }
          
        }
    }

    if (isset($_SESSION['college_id'])) {
        $condition_ar[] = "college_id = ?";
        $values[] = $_SESSION['college_id'];
        $types .= "i";
    }

    $condition = !empty($condition_ar)
        ? "WHERE " . implode(" AND ", $condition_ar)
        : "";

    $sql = "SELECT * FROM posts $condition";
    $stmt = $con->prepare($sql);

    if (!empty($values)) {
        $stmt->bind_param($types, ...$values);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ RETURN MULTIPLE ROWS
    $data = [];
    while ($row = ($type === 'array')
        ? $result->fetch_assoc()
        : $result->fetch_object()
    ) {
        $data[] = $row;
    }

    return $data;
}


function get_metadata($item_id,$meta_key='',$type='object'){ 
    global $con; 
    
    if(!empty($meta_key)){ 
        $query = $con->prepare("SELECT * FROM metadata WHERE item_id = ? AND meta_key = ?");
        $query->bind_param("is",$item_id,$meta_key);
    } else {
        $query = $con->prepare("SELECT * FROM metadata WHERE item_id = ?");
        $query->bind_param("i",$item_id);
    }

    $query->execute();
    $result = $query->get_result();

    return data_output($result , $type); 
}

function get_meta_value($item_id, $key) { 
    $meta = get_metadata($item_id, $key); 
    
    if (!empty($meta) && isset($meta[0]->meta_value)) {
        return $meta[0]->meta_value;
    }

    return null;
}


function data_output($query, $type='object'){
    $output=[];
    if($type == 'object'){
        while($result = $query->fetch_object()){
            $output[]=$result;

        }
    }
    else{
          while($result = $query->fetch_assoc()){
            $output[]=$result;

        }
    }
    return $output;
}

// function data_output($result, $type='object'){
//     $output=[];
//     while($row = ($type=='array' ? $result->fetch_assoc() : $result->fetch_object())){
//         $output[]=$row;
//     }
//     return $output;
// }

function get_user_data($user_id,$type='object'){
    global $con;

    $query=$con->prepare("SELECT * FROM accounts WHERE id = ?");
    $query->bind_param("i",$user_id);
    $query->execute();
    $result=$query->get_result();
  return data_output($result,$type);
}
function get_users($args = array(),$type ='object'){
    global $con;
      $condition = "";
      $values=[];
      $types="";
    if(!empty($args)){
         
        foreach($args as $k => $v)
        {
            $v = (string)$v;
            $condition_ar[] = "$k = ?";
             $values[]=$v;
               $types.="s";
        }
      if (!empty($condition_ar)) {
    $condition = "WHERE " . implode(" AND ", $condition_ar);
}

    }
 $query= $con->prepare("SELECT * FROM accounts $condition");
  if (!empty($values)) {
        $query->bind_param($types, ...$values);
    }
    $query->execute();
    $result=$query->get_result();
    $data=[];
 while($row=($type === 'array')
        ? $result->fetch_assoc()
        : $result->fetch_object()
 ){
$data[]=$row;
 }
 return $data;
}


function get_user_metadata($user_id){
    global $con;
    $output=[];
    $query=$con->prepare("SELECT * FROM usermeta WHERE `user_id`=?");
    $query->bind_param("i",$user_id);
    $query->execute();
    $outcome=$query->get_result();
    while($result=$outcome->fetch_object()){
$output[$result->meta_key]=$result->meta_value;
    }
   return $output;
  
}
function get_usermeta($user_id, $meta_key, $single = true)
{
    global $con;

    if (empty($user_id) || empty($meta_key)) {
        return false;
    }

    $query = $con->prepare(
        "SELECT meta_value FROM usermeta WHERE user_id = ? AND meta_key = ?"
    );
    $query->bind_param("is", $user_id, $meta_key);
    $query->execute();
    $result = $query->get_result();

    // ✅ single value
    if ($single === true) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['meta_value'];
        }
        return null; // ✅ IMPORTANT
    }

    // ✅ multiple values
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[] = $row['meta_value'];
    }

    return $values;
}


function get_attendance($item_id,$meta_key,$single=true){
    global $con;
 if(!empty($item_id) && !empty($meta_key)){
$query=$con->prepare("SELECT * FROM `attendance1` WHERE `item_id`=? AND `meta_key`=?");
$query->bind_param("is",$item_id,$meta_key);
$query->execute();
$result=$query->get_result();

 }
 else{
    return false;

 }
 if($single){
    if($result->num_rows > 0){
     return $result->fetch_object()->meta_value;
}
else{
    return $result->fetch_object();
}
    }
    else{
         return $result->fetch_object();
    }
 }
 

function update_usermeta($user_id,$meta_key,$meta_value){
    global $con;
        $select=$con->prepare("SELECT * FROM `usermeta` WHERE user_id=? AND meta_key=?");
        $select->bind_param("is",$user_id,$meta_key);
$select->execute();
$result=$select->get_result();
$check=$result->num_rows;
    if($check>0){
         $update_query=$con->prepare("UPDATE `usermeta` SET meta_value=? WHERE user_id=? AND meta_key=?");
         $update_query->bind_param("sis",$meta_value,$user_id,$meta_key);
         $update_query->execute();
  if($update_query){
    echo '<script>alert("updation of details has been successfully")</script>';
    echo '<script>window.open("dashboard.php","_self")</script>';
  }
    }
}
function update_attendance($item_id,$meta_key,$meta_value){
    global $con;
    $select=mysqli_query($con,"SELECT * FROM `attendance1` WHERE item_id=$item_id AND meta_key='$meta_key'");
    $check=mysqli_num_rows($select);
    if($check>0){
        return mysqli_query($con,"UPDATE `attendance1` SET meta_value='$meta_value' WHERE item_id='$item_id' AND meta_key='$meta_key'");
          
    }
  
    return false;
}
function get_parent($user_id,$meta_key,$single=true){
    global $con;
    if(empty($user_id) || empty($meta_key)){
        return false;
    }
    $query=mysqli_query($con,"SELECT * FROM `usermeta` WHERE user_id='$user_id' AND meta_key='$meta_key'");
    if($single){
        $row=mysqli_fetch_assoc($query);
        return $row['meta_value'] ?? false;
    }
    else{
        $value=[];
        while($row=mysqli_fetch_assoc($query)){
            $value=$row['meta_value'];
        }
        return $value;

    }
}

function delete_usermeta($user_id,$meta_key){
    global $con;
   
        $select=$con->prepare("SELECT * FROM `usermeta` WHERE user_id=? AND meta_key=?");
        $select->bind_param("is",$user_id,$meta_key);
        $select->execute();
        $result=$select->get_result();
    $check=$result->num_rows;
    if($check>0){
         $delete_query=$con->prepare("DELETE FROM `usermeta` WHERE user_id=? AND meta_key=?");
         $delete_query->bind_param("is",$user_id,$meta_key);
         $delete_query->execute();
        if($delete_query){
            echo '<script>alert("Deleting the details of the user")</script>';
            echo '<script>window.open("user-account.php","_self")</script>';
        }
    }
}
?>
<?php
function delete_section_meta($class_id,$con){
        $sql=$con->prepare("DELETE from `metadata` WHERE item_id=? AND meta_key=? ");
        $meta_key='section';
        $sql->bind_param("is",$class_id,$meta_key);
$sql->execute();
   
}

function insert_section_meta($class_id,$section_id,$con){

      $sql=$con->prepare("INSERT INTO metadata(item_id,meta_key,meta_value) value(?,?,?)");
      $meta_section='section';
$sql->bind_param("isi",$class_id,$meta_section,$section_id);
$sql->execute();
}
?>