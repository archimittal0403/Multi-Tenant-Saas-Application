<?php include('includes/auth.php');
checkRole('admin');?>

<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('includes/dynamic-form.php'); ?>
<?php  $institute_id=$_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
$course_id=$_GET['course_id'] ?? '';
$branch_id=$_GET['branch_id'] ?? '';
$session=$_GET['session'] ?? '';
 $class_id = $_GET['class'] ?? 0;
    $section_id = $_GET['section'] ?? 0;
?>
<?php
if(isset($_POST['save'])){
  $audience=implode(',',$_POST['audience']);
if($institute_type == 'college'){
    $class_id = 0;
    $section_id = 0;

    $semester = isset($_POST['semesters']) 
        ? implode(',', $_POST['semesters']) 
        : '';
}
else{
    $class_id = $_GET['class'] ?? 0;
    $section_id = $_GET['section'] ?? 0;

    $semester = '';
}
  $question=$_POST['questions'];
  $type=$_POST['type'];
  foreach($question as $q){
    if(!empty(trim($q))){
  // insert query
  $insert = mysqli_query($con, "
INSERT INTO feedback_questions 
(
    question,
    audience,
    course_id,
    branch_id,
    class_id,
    section_id,
    session,
    semester,
    type,
    institute_id
)
VALUES
(
    '$q',
    '$audience',
    '$course_id',
    '$branch_id',
    '$class_id',
    '$section_id',
    '$session',
    '$semester',
    '$type',
    '$institute_id'
)
");
  if($insert){
    echo "<script>alert('Insertion done sucessfully')</script>";
    echo "<script>window.open('feedback_faq.php','_self')</script>";
  }
}
  }
   /* ================= DYNAMIC FIELDS (LIKE STUDY) ================= */
    foreach($_POST as $key => $value){

        $skip = [
            'submit_notice','title','description','audience','semester',
            'publish_date','expiry_date','is_pinned',
            'notice_file','course_id','branch_id','session','class_id','section_id'
        ];

        if(in_array($key,$skip)) continue;
        if($value === '' || $value === null) continue;

        $value = mysqli_real_escape_string($con,$value);

        mysqli_query($con,"
            INSERT INTO metadata (item_id,meta_key,meta_value)
            VALUES ('$notice_id','$key','$value')
        ");
    }

    echo "<script>alert('Notice Created Successfully');window.location='admin_notice.php';</script>";
}

?>
 <h3 class="text-center mt-2">Add Feedback Question</h3>
 <a href="feilds.php" class="btn btn-dark mb-4">

Manage Fields

</a>
<button type="button" id="add_row" class="btn btn-sm btn-success mb-2">+Add Row</button>
 <div class="card">
<div class="card-body">
  <form action="" method="POST">
    <div class="row">
          <div class="col-md-6 mb-3">
          <label class="font-weight-bold">Send To</label>
          <select name="audience[]" multiple class="form-control">
            <option value="students">👨‍🎓 Students</option>
            <option value="teachers">👩‍🏫 Teachers</option>
          </select>
        </div>
     <?php
     if($institute_type=='college'){?>
  <div class="col-md-6 mb-3">
          <label class="font-weight-bold">Applicable Semester</label>
          <select name="semesters[]" multiple class="form-control">
           
           <?php
           $semester=get_posts([
            'type'=>'semester',
            'parent'=>$course_id
           ]);
         if(!empty($semester)){
            foreach($semester as $sem){
                $total_sem=(int)$sem->title;
                for($i=1;$i<=$total_sem;$i++){
                    echo "<option value='".$i."'>Semester ".$i."</option>";
                }
            }
         }
           
           ?>
          </select>
        </div>
        <?php } ?>
</div>

<div class="row">
  <div class="col-lg-6">
    <label for="type">Type</label>
    <input type="text" id="type" name="type" placeholder="eg exam,feedback" class="form-control ">
  </div>
</div>
<div class="conatiner" id="question_container">

<!-- DYNAMIC FIELDS -->
<div class="col-12 mt-3">
<?php render_dynamic_form('add_question'); ?>
</div>
</div>
<div class="form-group">
  <button type="submit" id="save" name="save" class="btn btn-danger">save</button>
</div>
</form>
</div>
 </div>
<?php
    include('footer.php');
?>
 <script>
    let count=0;
    document.getElementById('add_row').addEventListener('click',function(){
count++;
let html=`
<div class="d-flex mb-2" id="row_${count}">
<input type="text" name="questions[]" class="form-control" placeholder="Enter your Question in ${count}">
<button type="button" class="btn btn-danger ml-2" onclick="removeRow(${count})">
 ❌
 </button>
 </div> `;
 document.getElementById("question_container").insertAdjacentHTML("beforeend",html);
    });
    function removeRow(id){
      document.getElementById("row_"+id).remove();
    }
 </script>
