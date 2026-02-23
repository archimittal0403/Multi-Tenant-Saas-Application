<?php include('includes/config.php')?>

<?php

if(isset($_POST['submit'])){
  $class_id=isset($_POST['class'])?$_POST['class']:'';
  $section_id=isset($_POST['section'])?$_POST['section']:'';
  $teacher_id=isset($_POST['teacher_id'])?$_POST['teacher_id']:'';
  $period_id=isset($_POST['period_id'])?$_POST['period_id']:'';
  $day_name=isset($_POST['day_name'])?$_POST['day_name']:'';
  $subject_id=isset($_POST['subject_id'])?$_POST['subject_id']:'';
  $date_add=('Y-m-d H:i:s');
  $status='publish';
  $author=1;
  $type='timetable';
  $title='timetable entry';
  //$query=mysqli_query($con,"INSERT INTO posts (`type`,`author`,`status`,`publish_date`) VALUES('$type','$author','$status','$date_add')");
  $query=$con->prepare("INSERT INTO `posts`(`author`, `title`, `description`, `type`, `status`,`parent`) VALUES (?,?,?,?,?,?)");
  $author='1';
  $describe='description';
  $type='timetable';
  $publish='publish';
  $parent='0';
  $query->bind_param("issssi",$author,$title,$describe,$type,$publish,$parent);
  $query->execute();
 
  if($query){
    $item_id=mysqli_insert_id($con);
  }
  $metadata = array(
    'class'=>$class_id,
    'section'=>$section_id,
    'teacher_id'=>$teacher_id,
    'period_id'=>$period_id,
    'day_name'=>$day_name,
    'subject_id'=>$subject_id,
  );
  foreach($metadata as $key => $value){
    $stxt=$con->prepare("INSERT INTO metadata (`item_id`,`meta_key`,`meta_value`) VALUES (?,?,?)");
    $stxt->bind_param("iss",$item_id,$key,$value);
    $stxt->execute();
  }
  header('Location:timetable.php');
}

?>
<?php

  if(isset($_POST['save_all'])){

    $class_id   = $_POST['class'];
    $section_id = $_POST['section'];

    foreach($_POST['teacher_name'] as $day => $periods){

        foreach($periods as $period_id => $teacher_name){
$subject_name = $_POST['subject_name'][$day][$period_id];
    if(empty($teacher_name) && empty($subject_name)){
        continue;
    }

            // Get teacher id
            $teacher_id = 0;
          
            $select_teacher = $con->prepare("SELECT id FROM accounts WHERE Name=?");
            $select_teacher->bind_param("s",$teacher_name);
            $select_teacher->execute();
       $teacher_result = $select_teacher->get_result();
if ($row = $teacher_result->fetch_assoc()) {
    $teacher_id = $row['id'];
}

            // Get subject id
            $subject_id = 0;
          
            $select_subject = $con->prepare("SELECT id FROM courses WHERE name=?");
            $select_subject->bind_param("s",$subject_name);
            $select_subject->execute();
            $subject_result=$select_subject->get_result();
            if ($row = $subject_result->fetch_assoc()) {
    $subject_id = $row['id'];
}
            // Check existing record
            $check=$con->prepare("SELECT p.id FROM posts p 
                INNER JOIN metadata m1 ON m1.item_id=p.id AND m1.meta_key='class' AND m1.meta_value=?
                INNER JOIN metadata m2 ON m2.item_id=p.id AND m2.meta_key='section' AND m2.meta_value=?
                INNER JOIN metadata m3 ON m3.item_id=p.id AND m3.meta_key='day_name' AND m3.meta_value=?
                INNER JOIN metadata m4 ON m4.item_id=p.id AND m4.meta_key='period_id' AND m4.meta_value=?
                WHERE p.type='timetable' AND p.status='publish'");
                $check->bind_param("iisi",$class_id,$section_id,$day,$period_id);
                $check->execute();
                $check_result=$check->get_result();

            if($check_result->num_rows>0){
$item_row=$check_result->fetch_assoc();
                $item_id=$item_row['id'];
if($teacher_id>0){
             $update_teacher = $con->prepare("
    UPDATE metadata 
    SET meta_value = ? 
    WHERE item_id = ? 
    AND meta_key = 'teacher_id'
");

$update_teacher->bind_param("ii", $teacher_id, $item_id);
$update_teacher->execute();

                    
}
if($subject_id>0){

            $update_subject=$con->prepare("UPDATE metadata SET meta_value=? 
                    WHERE item_id=? AND meta_key='subject_id'");
                    $update_subject->bind_param("ii",$subject_id,$item_id);
                    $update_subject->execute();
}

            } else {

                // Insert new post
                $query=$con->prepare("INSERT INTO posts(author,title,description,type,status,parent) VALUES (?,?,?,?,?,?)");
                $author=1;
                $title='timetable entry';
                $desc='description';
                $type='timetable';
                $status='publish';
                $parent=0;

                $query->bind_param("issssi",$author,$title,$desc,$type,$status,$parent);
                $query->execute();

                $item_id=mysqli_insert_id($con);

                $metadata = [
                    'class'=>$class_id,
                    'section'=>$section_id,
                    'teacher_id'=>$teacher_id,
                    'period_id'=>$period_id,
                    'day_name'=>$day,
                    'subject_id'=>$subject_id
                ];

                foreach($metadata as $key=>$value){
                    $stmt=$con->prepare("INSERT INTO metadata(item_id,meta_key,meta_value) VALUES (?,?,?)");
                    $stmt->bind_param("iss",$item_id,$key,$value);
                    $stmt->execute();
                }
            
        }
        }
    }

    header("Location:timetable.php?class=$class_id&section=$section_id");
    exit;
}


?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>



<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6 d-flex">
            <h1 class="m-0 text-dark"> Manage Time Table :-</h1>
            <a href="?action=add" class="btn btn-success mx-3 btn-sm">Add table</a>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Admin</a></li>
              <li class="breadcrumb-item active">Time table</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

     <!-- Main content -->
     <section class="content">
      <div class="container-fluid">

      <?php if(isset($_GET['action'])&& $_GET['action']=='add'){ ?>

<div class="card">
  <div class="card-body">
<form action="" method="post">
 <div class="row">
            <div class="col-lg">
<div class="form-group">
 <label for="class">Select Class:-</label>
    <select require name="class" id="class" class="form-control">
        <option value="">select class</option>

             <?php
      $args = array(
        'type' => 'class',
        'status' =>'publish',
      );
 
        $classes=get_posts($args); 
        foreach($classes as $key => $class) { ?>
<option value="<?php echo $class->id ?>"><?php echo $class->title ?></option>
<?php } ?>
    </select>
     

        </div>
            </div>
          <div class="col-lg">
                 <div class="form-group" id="section-container" >
    <label for="section">Select Section:-</label>
    <select require name="section" id="section" class="form-control">
        <option value="">Select section</option>
             <?php
      $args = array(
        'type' => 'section',
        'status' =>'publish',
      );
 
        $section=get_posts($args); 
        foreach($section as $key => $section) { ?>
<option value="<?php echo $section->id ?>"><?php echo $section->title ?></option>
<?php } ?>
    </select>
    </div>
            </div> 

                 <div class="col-lg">
                 <div class="form-group" id="section-container">
    <label for="teacher_id">Select Teacher:-</label>
    <select require name="teacher_id" id="teacher_id" class="form-control">
        <option value="">Select teacher</option>
       <?php
$query=$con->prepare("SELECT id,Name FROM accounts WHERE type=?");
$type='teacher';
$query->bind_param("s",$type);
$query->execute();
$result=$query->get_result();

       while($t=$result->fetch_object()){
        echo '<option value="'.$t->id.'">'.$t->Name.'</option>';
       }
       ?>
    </select>
    </div>
            </div> 

                 <div class="col-lg">
                 <div class="form-group" id="section-container">
    <label for="period_id">Select period:-</label>
    <select require name="period_id" id="period_id" class="form-control">
        <option value="">Select period</option>
                  <?php
      $args = array(
        'type' => 'period',
        'status' =>'publish',
      );
 
        $periods=get_posts($args); 
        foreach($periods as $key => $period) { ?>
<option value="<?php echo $period->id ?>"><?php echo $period->title ?></option>
<?php } ?>
    </select>
    </div>
            </div> 
                 <div class="col-lg">
                 <div class="form-group" id="section-container">
    <label for="day_name">Select Day:-</label>
    <select require name="day_name" id="day_name" class="form-control">
        <option value="">Select section</option>
        <?php
       
        $days =['monday','tuesday','wednesday','thrusday','friday','saturday'];
  
foreach($days as $key => $day) { ?>
<option value="<?php echo $day?>"><?php echo $day ?></option>
<?php } ?>

     
    </select>
    </div>
        </div> 

     
                 <div class="col-lg">
                 <div class="form-group" id="section-container">
    <label for="subject_id">Select Subject:-</label>
    <select require name="subject_id" id="subject_id" class="form-control">
        <option value="">Select subject</option>
        <option value="9">English</option>
        <option value="10">Science</option>
    </select>
    </div>
            </div> 
           
            <div class="col-lg">
              <div class="form-group">
                <label for="">&nbsp;</label>
                 <!-- &nbsp is used to prevent the line break between the two element  -->
               <input type="submit" name="submit" value="submit" class="btn btn-primary form-control"> 
</div>
</div>
        </div>
      </form>
  </div>
</div>

      
      <?php } else { ?>
        <div class="card">
            <div class="card-body">
       <form action="" method="get">

        <?php
        $class_id=isset($_GET['class'])?$_GET['class']:2;
$section_id=isset($_GET['section'])?$_GET['section']:3;
?>
        <div class="row">
            <div class="col-lg-6">
<div class="form-group">
 <label for="class">Select Class:-</label>
    <select require name="class" id="class" class="form-control">
        <option value="">select class</option>

             <?php
      $args = array(
        'type' => 'class',
        'status' =>'publish',
      );
 
        $classes=get_posts($args); 
        foreach($classes as $key => $class) { 
          $selected= ($class_id == $class->id)? 'selected': '';
        echo '<option value="' . $class->id . '" '.$selected.'>' . $class->title . '</option>';

        } ?> 

    </select>
     

        </div>
            </div>
          <div class="col-lg-6">
                 <div class="form-group" id="section-container">
    <label for="section">Select Section:-</label>
    <select require name="section" id="section" class="form-control">
        <option value="">Select section</option>
           <?php
                            $args = array(
                            'type' => 'section',
                            'status' => 'publish',
                            );
                              $sections = get_posts($args);
                            foreach ($sections as $section) {
                                $selected = ($section_id ==  $section->id)? 'selected': '';
                            echo '<option value="' . $section->id . '" '.$selected.'>' . $section->title . '</option>';
                            } ?>

    </select>
    </div>
            </div> 
           
            <div class="col-auto">
              <button class="btn btn-primary">Apply</button>
                          </div>
        </div>
</form>
   </div>
</div>
</div>
<a href="generate_timetable.php?class=<?php echo $class_id; ?>&section=<?php echo $section_id; ?>" 
   class="btn btn-primary mb-3" target="_blank">
   Generate PDF
</a>
<div class="card">
    <div class="card-body">
       <form action="timetable.php" method="post">

<input type="hidden" name="class" value="<?php echo $class_id; ?>">
<input type="hidden" name="section" value="<?php echo $section_id; ?>">
        <table class="table table-bordered">
          
                <thead>
                    <tr>
                        <th>Timming</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thrusday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
</tr>

</thead>

  <tbody>
<?php
$args = array(
    'type'   => 'period',
    'status' => 'publish',
);

$periods = get_posts($args);

foreach ($periods as $period) {

    $from = get_meta_value($period->id, 'from');
    $to   = get_meta_value($period->id, 'to');
?>
<tr>
    <!-- Period timing -->
    <td>
        <?php echo date('h:i A', strtotime($from)) . ' - ' . date('h:i A', strtotime($to)); ?>
    </td>

    <?php
    $days = ['monday','tuesday','wednesday','thursday','friday','saturday'];

    foreach ($days as $day) {

  $query = $con->prepare("
            SELECT p.id
            FROM posts p
            INNER JOIN metadata d ON d.item_id=p.id AND d.meta_key='day_name' AND d.meta_value=?
            INNER JOIN metadata pe ON pe.item_id=p.id AND pe.meta_key='period_id' AND pe.meta_value=?
            INNER JOIN metadata c ON c.item_id=p.id AND c.meta_key='class' AND c.meta_value=?
            INNER JOIN metadata s ON s.item_id=p.id AND s.meta_key='section' AND s.meta_value=?
            WHERE p.type='timetable' AND p.status='publish'
        ");
        $period_id=$period->id;
$query->bind_param("siii",$day,$period_id,$class_id,$section_id);

$query->execute();
  $result=$query->get_result();

        if ($result->num_rows> 0) {
        
            $tt = $result->fetch_object();
        ?>
            <td>
               <!-- <form action="timetable.php" method="post"> -->
        
             
    <input type="text" name="teacher_name[<?php echo $day; ?>][<?php echo $period->id;?>]" class="form-control" value="
    <?php 
    $teacher_id=get_meta_value($tt->id,'teacher_id');
    $teacher_name=get_user_data($teacher_id);
  
    echo !empty($teacher_name)?$teacher_name[0]->Name:'';
    ?>">
  <input type="text" name="subject_name[<?php echo $day; ?>][<?php echo $period->id; ?>]" class="form-control" value="
  <?php 
  $subject_id=get_meta_value($tt->id,'subject_id');
  $subject=$con->prepare("SELECT name FROM `courses` WHERE id=?");
  $subject->bind_param("i",$subject_id);
  $subject->execute();
  $subject_result=$subject->get_result();
  while($row_fetch=$subject_result->fetch_assoc()){
    $subject_name=$row_fetch['name'];
  }
  echo !empty($subject_name)?$subject_name:'';
  ?>">
   

    <!-- <button type="submit" name="save" class="btn btn-sm btn-success mt-1">Save</button> -->
               
            </td>
        <?php } else { ?>
            <td class="text-muted">
    <input type="text" name="teacher_name[<?php echo $day; ?>][<?php echo $period->id;?>]" class="form-control" value="">
   
  <input type="text" name="subject_name[<?php echo $day; ?>][<?php echo $period->id; ?>]" class="form-control" value="">
   

             </td>
             
        <?php } 
    } ?>
</tr>
 
<?php } ?>

</tbody>

<?php } ?>
</table>

     <button type="submit" name="save_all" class="btn btn-sm btn-success mt-1">Save</button>
</form>
    </div>
</div>



    </section>
<?php include('footer.php')?>




  
