
<?php
session_start();
require_once('includes/config.php');
include('includes/functions.php');
?>

<?php

$institute_id = $_SESSION['institute_id'];
      $institute_type=$_SESSION['system_type'];
      $institute_code=$_SESSION['institute_code'];

?>
<?php
    //  $institute_id = $_SESSION['institute_id'];
    //  $institute_type=$_SESSION['system_type'];
$logopath = $_SERVER['DOCUMENT_ROOT'] . '/student management/AdminLTE-3.05/admin/uploads/akglogo.png';
// echo $logopath;
if(file_exists($logopath)){
    $type=pathinfo($logopath,PATHINFO_EXTENSION);
    $data=file_get_contents($logopath);
$base64_logo='data:image/' . $type . ';base64,' . base64_encode($data);
}
else{
    $base64_logo='';
    }
?>
<?php

 if(isset($_POST['type']) && $_POST['type'] == 'student' && isset($_POST['email']) && !empty($_POST['email'])){
      $name = isset($_POST['name'])?$_POST['name']:'';
$upload_dir =  __DIR__ . '/uploads/student_photo/';

if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true);
}

$upload_dir = __DIR__ . '/uploads/student_photo/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Photo Mandatory
if (
    !isset($_FILES['st_image']) ||
    $_FILES['st_image']['error'] != UPLOAD_ERR_OK ||
    empty($_FILES['st_image']['name'])
) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Student photo is required.'
    ]);
    exit;
}

$st_photo = time() . '_' . basename($_FILES['st_image']['name']);

if (!move_uploaded_file($_FILES['st_image']['tmp_name'], $upload_dir . $st_photo)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to upload student photo.'
    ]);
    exit;
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
                   $mother_email=isset($_POST['mother_email'])?$_POST['mother_email']:'';
                      $mother_name =isset($_POST['mother_name'])?$_POST['mother_name']:'';  
                           $mother_mobile =isset($_POST['mother_mobile'])?$_POST['mother_mobile']:'';  
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
     if($institute_type=='school'){
      $usermeta = array(
       'dob'=>$dob,
       'student_photo'=>$st_photo,
        'mobile'=>$mobile,
       
        'st_class'=>$st_class,
        'session'=>$session,
        'address'=>$address,
        'country'=>$country,
        'state'=>$state,
        'pincode'=>$zip,
        'father_name'=>$father_name,
        'father_mobile'=>$father_mobile,
        'mother_name'=>$mother_name,
        'mother_mobile'=>$mother_mobile,
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
       
        'course_name'=>$st_course,
        'address'=>$address,
        'country'=>$country,
        'state'=>$state,
        'pincode'=>$zip,
        'father_name'=>$father_name,
        'father_mobile'=>$father_mobile,
        'mother_name'=>$mother_name,
        'mother_mobile'=>$mother_mobile,
        'parent_address'=>$parent_address,
        'parent_country'=>$parent_country,
        'parent_state'=>$parent_state,
        'parent_pincode'=>$parent_pincode,
        'school_name'=>$school_name,
        'board'=>$board,
        'total_mark'=>$total_mark,
        'obtain_mark'=>$obtain_mark,
         'percentage'=>$percent,
      
        'branch_name'=>$st_branch,
        'session'=>$session,
          'doa'=>$doa,
        'type'=>$type,


     );
     }
    //  echo json_encode($usermeta);die;

                      
   $check_query=$con->prepare("SELECT * FROM accounts WHERE email=? AND institute_id=?");
$check_query->bind_param("si",$email, $institute_id);
$check_query->execute();

$result=$check_query->get_result();
       if($result->num_rows>0){
  $parent_id= null;
if(!empty($father_email)){

    $check_parent = $con->prepare("SELECT id FROM accounts WHERE email=? AND institute_id=?");
    $check_parent->bind_param("si",$father_email,$institute_id);
    $check_parent->execute();
    $parent_result = $check_parent->get_result();

    if($parent_result->num_rows > 0){
        $row = $parent_result->fetch_assoc();
        $parent_id = $row['id'];
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
     }
}
   
// insert into usermeta // dynamic fields fetch again
$dynamic_fields = mysqli_query($con,
"SELECT * FROM fields WHERE institute_id='$institute_id' AND form_type='student'");

while($field = mysqli_fetch_assoc($dynamic_fields)){
    $key = $field['field_key'];

    if(isset($_POST[$key]) && $_POST[$key] != ''){
        
        $value = $_POST[$key];
$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $stmt = $con->prepare("INSERT INTO usermeta (user_id, meta_key, meta_value) VALUES (?,?,?)");
        $stmt->bind_param("iss",$user_id,$key,$value);
        $stmt->execute();
    }
}
if($parent_id){
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
if($institute_type=='college'){

    $session = get_usermeta($user_id,'session');

    $bar_id = get_usermeta($user_id,'st_branch');

    $barcode = get_metadata($bar_id,'branch_code');

    $branch_code = preg_replace('/[^0-9]/','',
        $barcode[0]->meta_value ?? ''
    );

    if(is_string($session) && strpos($session,'-') !== false){

        list($start_full,$last_full)=explode('-',$session);

        $start_year = substr($start_full,-2);

    } else {

        $start_year = date('y');
    }

    $instcode = substr($institute_code,-1);

    $prefix = $start_year.$instcode.$branch_code;

}
else{

    // SCHOOL PREFIX
    $prefix = date('y').substr($institute_code,-1);

}

$query=mysqli_query($con,
"SELECT roll_no FROM accounts
WHERE roll_no LIKE '$prefix%'
ORDER BY id DESC LIMIT 1");

$query_fetch=mysqli_fetch_assoc($query);

if($query_fetch){

    $last_roll = $query_fetch['roll_no'];

    $last_seq = substr($last_roll,-3);

    $new_seq = (int)$last_seq + 1;

} else {

    $new_seq = 1;
}

$new_seq = str_pad($new_seq,3,'0',STR_PAD_LEFT);

$roll_no = $prefix.$new_seq;

$stmt = $con->prepare(
"UPDATE accounts SET roll_no=? WHERE id=?"
);

$stmt->bind_param("si",$roll_no,$user_id);

$stmt->execute();

echo json_encode([
    'success' => true,
    'std_id' => $user_id
]);

exit;

} // close inner if
} // close else
} // close main POST if

    
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

<style>
#example {
    width: max-content !important;
    min-width: 100%;
}
table.dataTable th,
table.dataTable td {
    white-space: nowrap !important;
}
.upload-box{
    width:170px;
    height:60px;
    border:2px dashed #3b82f6;
    border-radius:16px;
    background:#f8fbff;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    cursor:pointer;
    transition:.3s;
    overflow:hidden;
    position:relative;
}

.upload-box:hover{
    background:#eef5ff;
}

.upload-box i{
    font-size:28px;
}

.upload-box p{
    font-size:14px;
}

.upload-box small{
    font-size:12px;
}
#previewImage{
    width:100%;
    height:100%;
    object-fit:cover;
    position:absolute;
    inset:0;
}
body{
    background:#f4f7fc;
}
.table-responsive{
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    display: block;
}

#example{
width:100% !important;
margin:0 !important;
}


.dataTables_wrapper{
width:100%;
margin:0;
}

.dataTables_filter{
text-align:right !important;
}

.dataTables_paginate{
justify-content:end !important;
}
.card{
border-radius:14px;
overflow:hidden;
}

.card-header{
padding:14px 20px;
}

.form-control{
border-radius:10px;
height:45px;
box-shadow:none !important;
}

textarea.form-control{
height:auto;
}

.table{
border-radius:12px;
overflow:hidden;
}

.btn{
border-radius:10px;
padding:8px 18px;
font-weight:600;
}

.dataTables_wrapper .dt-buttons{
margin-bottom:15px;
}

.content-header h1{
font-weight:700;
}

</style>
<?php include('header.php')?>
<?php include('sidebar.php')?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <h1 class="m-0 text-dark"> Manage Accounts</h1>
      <a href="user-account.php?user=<?=$user?>&action=add-new"
   class="btn btn-primary btn-sm shadow-sm">
   <i class="fa fa-plus"></i> Add Student
</a>
<?php if($_SESSION['user_type'] == 'super_admin'){ ?>

<a href="feilds.php" class="btn btn-dark btn-sm shadow-sm">
    <i class="fa fa-layer-group"></i> Dynamic Fields
</a>

<?php } ?>
</div>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Accounts</a></li>
              <li class="breadcrumb-item active"><?php echo ucfirst($user); ?></li>

            </ol>
          </div><!-- /.col -->
          <?php
          if(isset($_SESSION['success_msg'])){?>
            <div class="col-12">
            <small class="text-success" style="font-size:19px mt-3"><?=$_SESSION['success_msg']?></small>
            </div>
          <?php
          unset($_SESSION['success_msg']);
          }
          ?>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

     <!-- Main content -->
  
        <?php 
        if(isset($_GET['action'])){
          
            ?>
        <div class="card">
<div class="card-body" id="form-container">
<?php if($_GET['action'] == 'add-new'){ ?>
<form action="" id="user-account" method="post" enctype="multipart/form-data">
<input type="hidden" name="type" value="student">

<!-- ================= Student Info ================= -->
<div class="card shadow-sm border-0 mb-4">
<div class="card-header bg-primary text-white">
<h4 class="mb-0">Student Information</h4>
</div>
<div class="card-body">
<div class="row">

<div class="col-md-6 form-group">
<label>Full Name</label>
<input type="text" name="name" class="form-control">
</div>

<div class="col-md-6 form-group">
<label>Student Image</label>

<div id="uploadBox" class="upload-box">
<input type="file"
       name="st_image"
       id="st_image"
       hidden
       accept="image/*"
       capture="environment">

    <div id="uploadContent">
        <i class="fa fa-cloud-upload"></i>
        <p>Drag & Drop Image Here</p>
        <small>or click to browse</small>
    </div>

    <img id="previewImage" src="" style="display:none;">
</div>
</div>

<div class="col-md-4 form-group">
<label>DOB</label>
<input type="date" name="dob" class="form-control">
</div>

<div class="col-md-4 form-group">
<label>Mobile</label>
<input type="text" name="mobile" class="form-control" maxlength="10" minlength="10" pattern="[0-9]{10}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
</div>

<div class="col-md-4 form-group">
<label>Email</label>
<input type="text" name="email" class="form-control">
</div>

<div class="col-md-12 form-group">
<label>Address</label>
<input type="text" name="address" class="form-control">
</div>

<div class="col-md-4 form-group">
<label>Country</label>
<select name="country" id="country" class="form-control">
    <option value="">Select Country</option>
    <option value="India">India</option>
    <option value="USA">USA</option>
    <option value="Canada">Canada</option>
    <option value="Australia">Australia</option>
    <option value="UK">UK</option>
</select>
</div>

<div class="col-md-4 form-group">
<label>State</label>
<select name="state" id="state" class="form-control">
    <option value="">Select State</option>
</select>
</div>

<div class="col-md-4 form-group">
<label>Pincode</label>
<input type="text" name="pincode" class="form-control">
</div>

</div>
</div>
</div>

<!-- ================= Parent Info ================= -->
<div class="card mb-3">
<div class="card-header"><b><h4>Parent Information</h4></b></div>
<div class="card-body">
<div class="row">

<div class="col-md-6 form-group">
<label>Father Name</label>
<input type="text" name="father_name" class="form-control">
</div>

<div class="col-md-6 form-group">
<label>Father Mobile</label>
<input type="text" name="father_mobile" class="form-control" maxlength="10" minlength="10" pattern="[0-9]{10}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
</div>

<div class="col-md-6 form-group">
<label>Father Email</label>
<input type="text" name="father_email" class="form-control">
</div>

<div class="col-md-6 form-group">
<label>Mother Name</label>
<input type="text" name="mother_name" class="form-control">
</div>

<div class="col-md-6 form-group">
<label>Mother Email</label>
<input type="text" name="mother_email" class="form-control">
</div>

<div class="col-md-6 form-group">
<label>Mother Mobile</label>
<input type="text" name="mother_mobile" class="form-control" maxlength="10" minlength="10" pattern="[0-9]{10}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
</div>

<div class="col-md-12 form-group">
<label>Parent Address</label>
<input type="text" name="parent_address" class="form-control">
</div>

<div class="col-md-4 form-group">
<label>Country</label>
<select name="parent_country" id="parent_country" class="form-control">
    <option value="">Select Country</option>
    <option value="India">India</option>
    <option value="USA">USA</option>
    <option value="Canada">Canada</option>
    <option value="Australia">Australia</option>
    <option value="UK">UK</option>
</select>
</div>

<div class="col-md-4 form-group">
<label>State</label>
<select name="parent_state" id="parent_state" class="form-control">
    <option value="">Select State</option>
</select>
</div>

<div class="col-md-4 form-group">
<label>Pincode</label>
<input type="text" name="parent_pincode" class="form-control">
</div>

</div>
</div>
</div>

<!-- ================= Qualification ================= -->
<div class="card mb-3">
<div class="card-header"><b><h4>Last Qualification</h4></b></div>
<div class="card-body">
<div class="row">

<div class="col-md-6 form-group">
<label>School Name</label>
<input type="text" name="school_name" class="form-control">
</div>

<div class="col-md-3 form-group">
<label>Last Class</label>
<input type="text" name="class" class="form-control">
</div>

<div class="col-md-3 form-group">
<label>Board</label>
<input type="text" name="board" class="form-control">
</div>

<div class="col-md-3 form-group">
<label>Total Marks</label>
<input type="number" id="total_mark" name="total_mark" class="form-control">
</div>

<div class="col-md-3 form-group">
<label>Obtained Marks</label>
<input type="number" id="obtain_mark" name="obtain_mark" class="form-control">
</div>

<div class="col-md-3 form-group">
<label>Percentage</label>
<input type="text" id="percentage" name="percentage" class="form-control" readonly>
</div>

</div>
</div>
</div>


<!-- ================= Admission ================= -->
<div class="card mb-3">
<div class="card-header"><b><h4>Admission Details</h4></b></div>
<div class="card-body">
<div class="row">

<?php if($institute_type == 'school'){ ?>

    <!-- SCHOOL FIELDS -->

       <div class="col-md-4 form-group">
                    <label>Class</label>

                    <select name="st_class" id="filter_class" class="form-control">
                        <option value="">Select Class</option>

                        <?php foreach($classes as $class){ ?>
                            <option value="<?= $class->id ?>">
                                <?= $class->title ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

               <div class="col-md-4 form-group">
                    <label>Section</label>

                    <select name="st_section" id="filter_section" class="form-control">
                        <option value="">Select Section</option>
                    </select>
                </div>



<?php } else { ?>

    <!-- COLLEGE FIELDS -->

    <div class="col-md-4 form-group">
        <label>Course</label>

        <select name="st_course" id="st_course" class="form-control">
            <option value="">Select Course</option>

            <?php foreach ($courses as $course){ ?>
                <option value="<?= $course->id ?>">
                    <?= $course->title ?>
                </option>
            <?php } ?>

        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Branch</label>

        <select name="st_branch" id="st_branch" class="form-control">
            <option value="">Select Branch</option>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Session</label>

        <input type="text"
               name="session"
               placeholder="eg: 2026-2030"
               class="form-control">
    </div>
    <!-- <div class="col-md-4 form-group">
        <label for="">Semester</label>
               <input type="text"
               name="semester"
               placeholder="eg: 1,2,3,4,5"
               class="form-control">
    </div> -->

<?php } ?>

<div class="col-md-4 form-group">
    <label>Date of Admission</label>

    <input type="date" name="doa" class="form-control">
</div>

</div>
</div>
</div>

<!-- ================= Dynamic Fields ================= -->

<?php
$dynamic_fields_form = mysqli_query($con,
"SELECT * FROM fields 
WHERE institute_id='$institute_id' 
AND form_type='student'
AND visibility=1 

ORDER BY id ASC");
?>

<?php if(mysqli_num_rows($dynamic_fields_form) > 0){ ?>

<div class="card mb-3">
    <div class="card-header">
        <h4><b>Additional Information</b></h4>
    </div>

    <div class="card-body">
        <div class="row">

            <?php while($field = mysqli_fetch_assoc($dynamic_fields_form)){ ?>

                <div class="col-md-4 form-group">

                    <label>
                        <?= $field['field_name'] ?>
                    </label>

                    <?php if($field['field_type'] == 'text'){ ?>

                        <input type="text"
                               name="<?= $field['field_key'] ?>"
                               class="form-control">

                    <?php } elseif($field['field_type'] == 'number'){ ?>

                        <input type="number"
                               name="<?= $field['field_key'] ?>"
                               class="form-control">

                    <?php } elseif($field['field_type'] == 'date'){ ?>

                        <input type="date"
                               name="<?= $field['field_key'] ?>"
                               class="form-control">

                    <?php } elseif($field['field_type'] == 'textarea'){ ?>

                        <textarea
                            name="<?= $field['field_key'] ?>"
                            class="form-control"></textarea>

                    <?php } ?>

                </div>

            <?php } ?>

        </div>
    </div>
</div>

<?php } ?>
<!-- ================= Submit ================= -->
<button type="submit" class="btn btn-primary px-5 py-2 shadow">
<i class="fa fa-save"></i> Register Student
</button>

</form>


        </div>
       
        </div>
        <?php } elseif($_GET['action'] == 'fee-payment'){ ?>
        <!-- <form action="" id="registration-fee" method="post">
          <div class="row">
            <div class="col-lg-4">
              <div class="form-group">
                <label for="">Reciept Number </label>
                <input type="text" name="reciept_number" placeholder="Enter your Recipt Number" class="form-control"/>
        </div>
          <div class="form-group">
                <label for="">Registration Fees </label>
                <input type="text" name="registration_fee" placeholder="Registration Fee" class="form-control"/>
        </div>
          
            </div>
              
          </div>
          <input type="hidden" name="std_id" value="<?php echo isset($_GET['std_id'])?$_GET['std_id']:''?>">
          <button type="submit" class="btn btn-primary">Submit</button>
          
        </form> -->
          <?php } ?>
        <?php }  else { ?>
        <!-- Info boxes -->
         <div class="card">
          <div class="card-body">
           
              <?php
             $class_id = $_GET['class'] ?? '';
$section_id = $_GET['section'] ?? '';
$course_id=$_GET['course'] ?? '';
$branch_id=$_GET['branch'] ?? '';
$semester_id=$_GET['semester'] ?? '';
              ?>
             <div class="card">
    <div class="card-body">

        <form action="" method="get">

            <div class="row">

<?php if($institute_type=='college'){ ?>

                <div class="col-md-3 form-group">
                    <label>Course</label>

                    <select name="st_course" id="st_course" class="form-control">
                        <option value="">Select Course</option>

                        <?php foreach($courses as $course){ ?>
                            <option value="<?= $course->id ?>">
                                <?= $course->title ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

                <div class="col-md-3 form-group">
                    <label>Branch</label>

                    <select name="st_branch" id="st_branch" class="form-control">
                        <option value="">Select Branch</option>
                    </select>
                </div>

                <div class="col-md-3 form-group">
                    <label>Semester</label>

                    <select name="semester" id="st_semester" class="form-control">
                        <option value="">Select Semester</option>
                    </select>
                </div>

                <div class="col-md-3 form-group">
                    <label>Session</label>

                    <select name="session" id="st_session" class="form-control">
                        <option value="">Select Session</option>

                        <?php
                        $sessions = get_posts([
                            'type'=>'session',
                            'institute_id'=>$institute_id
                        ]);

                        if(!empty($sessions)){
                            foreach($sessions as $session){
                                echo '<option value="'.$session->id.'">'.$session->title.'</option>';
                            }
                        }
                        ?>

                    </select>
                </div>

<?php } else { ?>

<?php if($institute_type=='school'){ ?>

                <div class="col-md-4 form-group">
                    <label>Class</label>

                    <select name="st_class" id="filter_class" class="form-control">
                        <option value="">Select Class</option>

                        <?php foreach($classes as $class){ ?>
                            <option value="<?= $class->id ?>">
                                <?= $class->title ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

               <div class="col-md-4 form-group">
                    <label>Section</label>

                    <select name="st_section" id="filter_section" class="form-control">
                        <option value="">Select Section</option>
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label>Session</label>

               <select name="session" id="filter_session" class="form-control">
                        <option value="">Select Session</option>

                        <?php
                        $sessions = get_posts([
                            'type'=>'session',
                            'institute_id'=>$institute_id
                        ]);

                        if(!empty($sessions)){
                            foreach($sessions as $session){
                                echo '<option value="'.$session->id.'">'.$session->title.'</option>';
                            }
                        }
                        ?>

                    </select>
                </div>

<?php } ?>

<?php } ?>

            </div>

        </form>

      
    </div>
</div>
         <form method="post">
        <div style="overflow-x:auto; width:100%;">
<table class="table table-hover table-striped align-middle w-100" id="example">

  <thead>
    <?php
    $table=mysqli_query($con,"SELECT * FROM `fields` WHERE institute_id='$institute_id' AND form_type='student'");
    ?>
    <tr>
      <th>sno</th>
 <?php if($institute_type == 'college'){ ?>

    <th>Roll No</th>

<?php } else { ?>

    <th>Student ID</th>

<?php } ?>
      <th>Student Photo</th>
      <th>Student Name</th>
      <th>Student's Email</th>
      <th>Phone NO</th>
      <th>DOB</th>
      <?php
    $feild_array=[];
while($table_fetch=mysqli_fetch_assoc($table)){
$feild_array[]=$table_fetch;

    echo "<th>{$table_fetch['field_name']}</th>";

}
      ?>
      <th>Action</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

       
       
        </div>
        </form>

        </div>
        <!-- /.row -->   

       <div class="container my-4">
      <?php
      if(isset($_GET['edit_student'])){
include('edit_user.php');
      }
      ?>
      </div>
      <div class="container">
        <?php
        if(isset($_GET['delete_student'])){
          include('delete_user.php');
        }
        ?>
      </div>
      <?php } ?>
   
<?php include('footer.php')?>
<script>

$(document).ready(function(){

    // click open file
    $('#uploadBox').on('click', function(){
        $('#st_image').click();
    });

    // preview image
    $('#st_image').on('change', function(e){

        let file = e.target.files[0];

        if(!file) return;

        // only image check
        if(!file.type.startsWith('image/')){
            alert('Only image allowed');
            return;
        }

        let reader = new FileReader();

        reader.onload = function(event){

            $('#previewImage')
                .attr('src', event.target.result)
                .show();

            $('#uploadContent').hide();
        }

        reader.readAsDataURL(file);
    });

});

</script>

<script>
$(document).ready(function () {

    // =========================================
    // DYNAMIC TABLE COLUMNS
    // =========================================

    var dynamicFields = <?= json_encode($feild_array ?? []) ?>;

    var cols = [];

    cols.push({ data: 'sno' });

    <?php if($institute_type == 'college'){ ?>
        cols.push({ data: 'roll_no' });
    <?php } else { ?>
        cols.push({ data: 'roll_no' });
    <?php } ?>

    cols.push({ data: 'photo' });
    cols.push({ data: 'name' });
    cols.push({ data: 'email' });
    cols.push({ data: 'phone' });
    cols.push({ data: 'dob' });

    dynamicFields.forEach(function(field){
        cols.push({
            data: field.field_key,
            defaultContent: ''
        });
    });

    cols.push({ data: 'action' });

    // =========================================
    // DATATABLE INIT
    // =========================================

    let table = null;

    if ($('#example').length) {

        table = $('#example').DataTable({

            processing: true,
            serverSide: false,
            destroy: true,
         responsive: false,
scrollX: true,
scrollCollapse: true,
autoWidth: false,
            dom: 'Bfrtip',

        buttons: [

    {
        extend: 'excelHtml5',
        text: '<i class="fa fa-file-excel"></i> Excel',
        className: 'btn btn-success btn-sm'
    },

    {
        text: '<i class="fa fa-file-pdf"></i> Generate PDF',
        className: 'btn btn-danger btn-sm',
        action: function () {

            let url = '';

            <?php if($institute_type == 'college'){ ?>

                url = 'pdf.php?type=student_report'
                    + '&course='   + ($('#st_course').val() || '')
                    + '&branch='   + ($('#st_branch').val() || '')
                    + '&semester=' + ($('#st_semester').val() || '')
                    + '&session='  + ($('#st_session').val() || '');

            <?php } else { ?>

                url = 'pdf.php?type=student_report'
                    + '&class='   + ($('#filter_class').val() || '')
                    + '&section=' + ($('#filter_section').val() || '')
                    + '&session=' + ($('#filter_session').val() || '');

            <?php } ?>

            window.open(url, '_blank');

        }
    }

],

            ajax: {

                url: 'ajax.php',
                type: 'POST',

        data: function(d){
  if(
            $('#filter_class').val()=='' ||
            $('#filter_section').val()=='' ||
            $('#filter_session').val()==''
        ){
            return false;
        }

    d.action = 'get_user_details';

    <?php if($institute_type=='school'){ ?>

        d.class_id   = $('#filter_class').val();
        d.section_id = $('#filter_section').val();
        d.session_id = $('#filter_session').val();

        console.log("School Session:", d.session_id);

    <?php } else { ?>

        d.course_id   = $('#st_course').val();
        d.branch_id   = $('#st_branch').val();
        d.session_id  = $('#st_session').val();
        d.semester_id = $('#st_semester').val();

        console.log("College Session:", d.session_id);

    <?php } ?>
},

                error: function(xhr){
                    console.log(xhr.responseText);
                }
            },

            columns: cols

        });

    }

    // =========================================
    // TABLE RELOAD
    // =========================================

    function reloadTable(){
        if(table){
            table.ajax.reload(null, false);
        }
    }

    // =========================================
    // SCHOOL FILTERS
    // =========================================

    $(document).on('change', '#filter_class', function(){

        let class_id = $(this).val();

        $('#filter_section').html(
            '<option value="">Loading...</option>'
        );

        $.ajax({

            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',

            data: {
                action: 'get_sections',
                class_id: class_id
            },

            success: function(res){

                if(res.status){

                    $('#filter_section').html(res.options);

                } else {

                    $('#filter_section').html(
                        '<option value="">No Section Found</option>'
                    );
                }

                reloadTable();
            },

            error: function(xhr){

                console.log(xhr.responseText);

                $('#filter_section').html(
                    '<option value="">Error Loading</option>'
                );
            }

        });

    });

    $(document).on(
        'change',
        '#filter_section, #filter_session',
        function(){
            reloadTable();
        }
    );

    // =========================================
    // COURSE -> BRANCH
    // =========================================

    $(document).on('change', '#st_course', function(){

        let course_id = $(this).val();

        $('#st_branch').html(
            '<option value="">Loading...</option>'
        );

        $('#st_semester').html(
            '<option value="">Loading...</option>'
        );

        // =========================
        // LOAD BRANCH
        // =========================

        $.ajax({

            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',

            data: {
                action: 'get_branch',
                course_id: course_id
            },

            success: function(res){

                if(res.status){

                    $('#st_branch').html(res.options);

                } else {

                    $('#st_branch').html(
                        '<option value="">No Branch Found</option>'
                    );
                }
            },

            error: function(xhr){

                console.log(xhr.responseText);

                $('#st_branch').html(
                    '<option value="">Error Loading</option>'
                );
            }

        });

        // =========================
        // LOAD SEMESTER
        // =========================

        $.ajax({

            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',

            data: {
                action: 'get_semester',
                course_id: course_id
            },

            success: function(res){

                if(res.status){

                    $('#st_semester').html(res.options);

                } else {

                    $('#st_semester').html(
                        '<option value="">No Semester Found</option>'
                    );
                }
            },

            error: function(xhr){

                console.log(xhr.responseText);

                $('#st_semester').html(
                    '<option value="">Error Loading</option>'
                );
            }

        });

        reloadTable();

    });

    $(document).on(
        'change',
        '#st_branch, #st_semester, #st_session',
        function(){
            reloadTable();
        }
    );

    // =========================================
    // STUDENT FORM SUBMIT
    // =========================================

    $(document).on('submit', '#user-account', function(e){

        e.preventDefault();

        let formdata = new FormData(this);

        $.ajax({

            type: 'POST',
            url: 'user-account.php',

            data: formdata,

            dataType: 'json',

            processData: false,
            contentType: false,

            beforeSend: function(){

                $('#user-account button[type="submit"]')
                    .prop('disabled', true)
                    .html(
                        '<i class="fa fa-spinner fa-spin"></i> Processing...'
                    );
            },

            success: function(response){

                console.log(response);

                $('#user-account button[type="submit"]')
                    .prop('disabled', false)
                    .html(
                        '<i class="fa fa-save"></i> Register Student'
                    );

                if(response.success){

                    alert('Student Registered Successfully');

                    window.location.href =
                        'user-account.php?user=student&std_id='
                        + response.std_id;

                } else {

                    alert(response.message || 'Something went wrong');
                }
            },

            error: function(xhr){

                console.log(xhr.responseText);

                $('#user-account button[type="submit"]')
                    .prop('disabled', false)
                    .html(
                        '<i class="fa fa-save"></i> Register Student'
                    );

                alert('AJAX Failed. Check Console');
            }

        });

    });

});

// ================= COUNTRY -> STATE =================

const states = {
    India: [
        "Uttar Pradesh",
        "Delhi",
        "Haryana",
        "Punjab",
        "Rajasthan",
        "Maharashtra",
        "Gujarat",
        "Bihar",
        "Madhya Pradesh"
    ],

    USA: [
        "California",
        "Texas",
        "Florida",
        "New York"
    ],

    Canada: [
        "Ontario",
        "Quebec",
        "Alberta"
    ],

    Australia: [
        "Queensland",
        "Victoria",
        "Tasmania"
    ],

    UK: [
        "England",
        "Scotland",
        "Wales"
    ]
};

// STUDENT COUNTRY
$('#country').on('change', function(){

    let country = $(this).val();

    $('#state').html('<option value="">Select State</option>');

    if(states[country]){

        states[country].forEach(function(state){

            $('#state').append(
                `<option value="${state}">${state}</option>`
            );

        });

    }

});

// PARENT COUNTRY
$('#parent_country').on('change', function(){

    let country = $(this).val();

    $('#parent_state').html('<option value="">Select State</option>');

    if(states[country]){

        states[country].forEach(function(state){

            $('#parent_state').append(
                `<option value="${state}">${state}</option>`
            );

        });

    }

});

// ================= AUTO PERCENTAGE =================

$('#total_mark, #obtain_mark').on('keyup change', function(){

    let total = parseFloat($('#total_mark').val());

    let obtain = parseFloat($('#obtain_mark').val());

    if(total > 0 && obtain >= 0){

        let percentage = (obtain / total) * 100;

        $('#percentage').val(
            percentage.toFixed(2)
        );

    } else {

        $('#percentage').val('');
    }

})
</script>
   

  