<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php
if(isset($_POST['edit'])){
  $edit_id=$_GET['edit_id'];
 foreach($_POST as $key=>$value){
  if($key=='edit')
    continue;

  update_usermeta($edit_id,$key,$value);

 }
    header("Location: student-profile.php?edit_id=".$edit_id);
    exit;
}
?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
 $institute_id=$_SESSION['institute_id'];
 $institute_code=$_SESSION['institute_code'];
 $institute_type=$_SESSION['system_type'];
 $std_id=$_SESSION['user_id'];

?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Student Profile :-</h1>
                      <a href="document.php?user_id=<?php echo $std_id; ?>"
   class="btn btn-primary btn-sm mx-4">+ Add document</a>
   <a href="student-profile.php?action=edit_mode&edit_id=<?php echo $std_id;?>" class="btn btn-dark btn-sm">
    Edit Profile
</a>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">student</a></li>
              <li class="breadcrumb-item active">Student Profile</li>
            </ol>
          </div><!-- /.col -->
       
        </div><!-- /.row -->
</div>
</div>
    <!-- /.content-header -->
     <!-- <?php
    // print_r($student);
   
 //print_r($stdmeta);
     ?> -->
       <section class="content">
        <form action="" method="POST">
      <div class="container-fluid">
        <div class="row">

        <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <?php $student_photo=get_usermeta($std_id,'student_photo');?>
                  <img class="profile-user-img img-fluid img-circle" src="../admin/uploads/student_photo/<?php echo $student_photo ?>" alt="User profile picture">
                </div>

                 <h3 class="profile-username text-center"><?php echo get_users(array('id'=>$std_id))[0]->Name ;?></h3>

                <p class="text-muted text-center"> <?php echo get_users(array('id'=>$std_id))[0]->type; ?> </p>
                <p class="text-muted text-center"><?php echo get_users(array('id'=>$std_id))[0]->roll_no;?></p>

           
<?php if($institute_type=='college'){?>
    <li class="list-group-item">
                    <b>Course:-</b> <a class="float-right"><?php $course_id=get_usermeta($std_id,'course_name');
                    $course=mysqli_query($con,"SELECT title FROM posts WHERE id='$course_id' AND institute_id='$institute_id'");
                    $row_course=mysqli_fetch_assoc($course);
                    echo $course_name=$row_course['title']; ?></a>
                  </li>
                
                      <li class="list-group-item">
                    <b>Branch:-</b> <a class="float-right"><?php 
                    $branch_id=get_usermeta($std_id,'branch_name');
                    $branch=mysqli_query($con,"SELECT title FROM posts WHERE id='$branch_id' AND institute_id='$institute_id'");
                    $row_branch=mysqli_fetch_assoc($branch);
                    echo $branch_name=$row_branch['title']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Semester:-</b> <a class="float-right"><?php 
                   echo $semester=get_usermeta($std_id,'semester');
                    ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Academic Session:-</b> <a class="float-right"><?php 
                    $session_id=get_usermeta($std_id,'session');
                    $session=mysqli_query($con,"SELECT title FROM posts WHERE id='$session_id' AND institute_id='$institute_id'");
                    $row_session=mysqli_fetch_assoc($session);
                    echo $session_name=$row_session['title']; ?></a>
                  </li>
<?php } else{?> 
<li class="list-group-item">
<b>Class:-</b> <a class="float-right"><?php $class_id=get_usermeta($std_id,'st_class');
                    $class=mysqli_query($con,"SELECT title FROM posts WHERE id='$class_id' AND institute_id='$institute_id'");
                    $row_class=mysqli_fetch_assoc($class);
                    echo $class_name=$row_class['title']; ?></a>
                  </li>
                
                      <li class="list-group-item">
                    <b>Section:-</b> <a class="float-right"><?php 
                    $section_id=get_usermeta($std_id,'st_section');
                    $section=mysqli_query($con,"SELECT title FROM posts WHERE id='$section_id' AND institute_id='$institute_id'");
                    $row_section=mysqli_fetch_assoc($section);
                    echo $section_name=$row_section['title']; ?></a>
                  </li>
                 
                  <li class="list-group-item">
                    <b>Academic Session:-</b> <a class="float-right">
<?php 
$year=date('Y');
$next_year=$year+1;
echo $academic_session=$year.'-'.$next_year;
?>

                    </a>
                  </li>
<?php } ?>
                   <li class="list-group-item">
                    <b>DOB:-</b> <a class="float-right"><?php echo get_usermeta($std_id,'dob'); ?></a>
                  </li>
                

                  <li class="list-group-item">
                    <b>Mobile:-</b> <a class="float-right"><?php echo get_usermeta($std_id,'mobile'); ?></a>
                  </li>
                  
                  <li class="list-group-item">
                    <b>email:-</b> <a class="float-right"><?php echo get_users(array('id'=>$std_id))[0]->email;?></a>
                  </li>
 <li class="list-group-item">
   <b>Addres:-</b> 
  <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="address" id="address" value="<?php echo ucfirst(get_usermeta($std_id,'address'));?>" class="form-control">
<?php } 
else{ ?>
                   <a class="float-right"><?php echo ucfirst(get_usermeta($std_id,'address')); ?></a>
                    <?php } ?>
                  </li>
<li class="list-group-item">
                    <b>State:-</b> 
                      <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="state" id="state" value="<?php echo ucfirst(get_usermeta($std_id,'state'));?>" class="form-control">
<?php } 
else{ ?>
                    <a class="float-right"><?php echo ucfirst(get_usermeta($std_id,'state')); ?></a>
                  </li>
                   <?php } ?>
<li class="list-group-item">

                    <b>Country:-</b>
                                        <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="country" id="country" value="<?php echo ucfirst(get_usermeta($std_id,'country'));?>" class="form-control">
<?php } 
else{ ?>
                    <a class="float-right"><?php echo ucfirst(get_usermeta($std_id,'country')); ?></a>
                  </li>
                     <?php } ?>
<li class="list-group-item">
                    <b>Zip Code:-</b> 
                      <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="pincode" id="pincode" value="<?php echo ucfirst(get_usermeta($std_id,'pincode'));?>" class="form-control">
<?php } 
else{ ?>
                      <a class="float-right"><?php echo ucfirst(get_usermeta($std_id,'pincode')); ?></a>
                  </li>
                  <?php } ?>
                </ul>

                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

           
           
            <!-- /.card -->
          </div>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Parent's Information</h3>
        </div>
        <div class="card-body">
               <ul class="list-group list-group-unbordered mb-3">
               
                 <li class="list-group-item">
                    <b class="px-5">Father's Name:-</b> 
                                <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="father_name" id="father_name" value="<?php echo ucfirst(get_usermeta($std_id,'father_name'));?>" class="form-control">
<?php } 
else{ ?>
                    <a class="mx-4"><?php   echo ucfirst(get_usermeta($std_id,'father_name')) ;?></a>
                     <?php } ?>

                      <b class="px-5">Father's Phone-No:-</b> 
                        <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="father_mobile" id="father_mobile" value="<?php echo ucfirst(get_usermeta($std_id,'father_mobile'));?>" class="form-control">
<?php } 
else{ ?>
<a class="mx-4"><?php  echo ucfirst(get_usermeta($std_id,'father_mobile'));?></a>
                  </li>
 <?php } ?>
                       <li class="list-group-item mt-3">
                    <b class="px-5">Mother's Name:-</b> 
                                    <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="mother_name" id="mother_name" value="<?php echo ucfirst(get_usermeta($std_id,'mother_name'));?>" class="form-control">
<?php } 
else{ ?>
                    <a class="mx-4"><?php   echo ucfirst(get_usermeta($std_id,'mother_name')); ?></a>
                     <?php } ?>

                      <b class="px-5">Mobile's Phone-No:-</b> 
                                                   <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="mother_mobile" id="mother_mobile" value="<?php echo ucfirst(get_usermeta($std_id,'mother_mobile'));?>" class="form-control">
<?php } 
else{ ?>
                      <a class="mx-4"><?php echo ucfirst(get_usermeta($std_id,'mother_mobile'));?></a>
                  </li>
 <?php } ?>
                 <li class="list-group-item mt-3">
                    <b class="px-5">parents's Address:-</b>
                                                 <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="parent_address" id="parent_address" value="<?php echo ucfirst(get_usermeta($std_id,'parent_address'));?>" class="form-control">
<?php } 
else{ ?>
 <a class="mx-3"><?php echo ucfirst(get_usermeta($std_id,'parent_address'));?></a>
 <?php } ?>
                      <b class="px-5">parents's State:-</b> 
                                                   <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="parent_state" id="parent_state" value="<?php echo ucfirst(get_usermeta($std_id,'parent_state'));?>" class="form-control">
<?php } 
else{ ?><a class="mx-4"><?php  echo ucfirst(get_usermeta($std_id,'parent_state'));?></a>
                     
                  </li>
                  <?php } ?>
                 <li class="list-group-item mt-3">
                      <b class="px-5 mt-2">parents's Country:-</b> 
   <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="parent_country" id="parent_country" value="<?php echo ucfirst(get_usermeta($std_id,'parent_country'));?>" class="form-control">
<?php } 
else{ ?>
                      <a class="mx-4"><?php echo ucfirst(get_usermeta($std_id,'parent_country'));?></a>
                      <?php } ?>
                        <b class="px-5 mt-2">parents's pincode:-</b>
                                                                           <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="parent_pincode" id="parent_pincode" value="<?php echo ucfirst(get_usermeta($std_id,'parent_pincode'));?>" class="form-control">
<?php } 
else{ ?> <a class="mx-4"><?php  echo ucfirst(get_usermeta($std_id,'parent_pincode'));?></a>
                  </li>
                  <?php } ?>
                 
</ul>


</div>
    </div>

    <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Student's Qualification</h3>
        </div>
        <div class="card-body">
               <ul class="list-group list-group-unbordered mb-3">
               
                 <li class="list-group-item">
                    <b class="px-5">School Name:-</b> 
                    <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="school_name" id="school_name" value="<?php echo ucfirst(get_usermeta($std_id,'school_name'));?>" class="form-control">
<?php } 
else{ ?>
                    <a class="mx-4"><?php  echo ucfirst(get_usermeta($std_id,'school_name'));?></a>
                    <?php } ?>
                      <b class="px-5">Board:-</b> 
                                  <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="board" id="board" value="<?php echo ucfirst(get_usermeta($std_id,'board'));?>" class="form-control">
<?php } 
else{ ?>
                      <a class="mx-4"><?php  echo ucfirst(get_usermeta($std_id,'board'));;?></a>
                  </li>
                  <?php } ?>

                

                 <li class="list-group-item mt-3">
                    <b class="px-5">Marks's Obtained:-</b> 
                                <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="obtain_mark" id="obtain_mark" value="<?php echo ucfirst(get_usermeta($std_id,'obtain_mark'));?>" class="form-control">
<?php } 
else{ ?>
                    <a class="mx-3"><?php echo ucfirst(get_usermeta($std_id,'obtain_mark'));?></a>
                    <?php } ?>
                      <b class="px-5">Percentage:-</b> 
                                  <?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
    <input type="text" name="school_name" id="school_name" value="<?php echo ucfirst(get_usermeta($std_id,'parent_country'));?>" class="form-control">
<?php } 
else{ ?><a class="mx-4"><?php  echo ucfirst(get_usermeta($std_id,'percentage')).'%';?></a>
                     
                  </li>
                  <?php } ?>
                  
                
</ul>

 <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Extra Details</h3>
        </div>
        <div class="card-body">
               <ul class="list-group list-group-unbordered mb-3">
     
                         <ul class="list-group list-group-unbordered mb-3">
            <?php 
$fields = get_dynamic_fields('student','user-profile');
?>

<?php foreach($fields as $field): ?>

<?php
$key = $field['field_key'];
$value = get_usermeta($std_id,$key) ?? '';
$is_edit = isset($_GET['action']) && $_GET['action']=='edit_mode';
$is_editable = $field['is_edit'] ?? 0;
?>

<li class="list-group-item">
    <b><?php echo $field['field_name']; ?> -</b>

    <span class="float-right">

    <?php if($is_edit && $is_editable == 1){ ?>

        <!-- INPUT FIELD -->
        <?php if($field['field_type'] == 'select'){ ?>

<select name="<?php echo $key; ?>" class="form-control">

<option value="">Select</option>

<?php

$options = explode(',',$field['options']);

foreach($options as $opt){

?>

<option value="<?php echo $opt; ?>"
<?php if($value == $opt) echo 'selected'; ?>>

<?php echo $opt; ?>

</option>

<?php } ?>

</select>

<?php } elseif($field['field_type'] == 'textarea'){ ?>

<textarea name="<?php echo $key; ?>"
class="form-control"><?php echo $value; ?></textarea>

<?php } else { ?>

<input type="<?php echo $field['field_type']; ?>"
       name="<?php echo $key; ?>"
       value="<?php echo $value; ?>"
       class="form-control">

<?php } ?>

    <?php } else { ?>

        <!-- DISPLAY MODE -->
        <?php 
$typeMap = [
    'course_name' => 'course',
    'branch_name' => 'branch',
    'semester'    => 'semester',
    'session'     => 'session',
    'st_class'    => 'class',
    'st_section'  => 'section'
];

if(isset($typeMap[$key]) && !empty($value)){

    $post = get_post([
        'id'   => $value,
        'type' => $typeMap[$key]
    ]);

    echo $post->title ?? $value;

} else {

    echo $value;
}
        ?>

    <?php } ?>

    </span>
</li>
                  
       <?php endforeach; ?>         
</ul>
<?php
  if(isset($_GET['action']) && $_GET['action']=='edit_mode'){?>
  <button type="submit" id="edit" name="edit" class="btn btn-warning">Edit Form</button>
<?php } ?>
</div>
</div>
</div>
</div>
</form>
</section>
<?php include('footer.php')?>
      

  

      