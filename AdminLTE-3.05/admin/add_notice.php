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
$class_id   = $_GET['class'] ?? '';
$section_id = $_GET['section'] ?? '';

$course_id  = $_GET['course_id'] ?? '';
$branch_id  = $_GET['branch_id'] ?? '';
$session    = $_GET['session'] ?? '';

?>
<?php
if(isset($_POST['submit_notice'])){

    $title = $_POST['title'];
    $description = $_POST['description'];

    $audience = isset($_POST['audience']) ? implode(',', $_POST['audience']) : 'all';
 $semester = isset($_POST['semesters'])
    ? implode(',', $_POST['semesters'])
    : 'all';

    $publish_date = $_POST['publish_date'];
    $expiry_date  = $_POST['expiry_date'];
    $is_pinned    = $_POST['is_pinned'] ?? 0;

    // FILE
    $file_name = '';
    if(!empty($_FILES['notice_file']['name'])){
        $file = $_FILES['notice_file']['name'];
        $tmp  = $_FILES['notice_file']['tmp_name'];

        $upload_dir = __DIR__.'/uploads/annoucement/';
        if(!is_dir($upload_dir)) mkdir($upload_dir,0777,true);

        $file_name = time().'_'.$file;
        move_uploaded_file($tmp, $upload_dir.$file_name);
    }

    // INSERT NOTICE
    $insert = mysqli_query($con,"
        INSERT INTO notices
        (title,description,file,publish_date,expiry_date,audience,is_pinned,status,semester,course,branch,session,class,section)
        VALUES
        ('$title','$description','$file_name','$publish_date','$expiry_date','$audience','$is_pinned','1','$semester',
        '$course_id','$branch_id','$session','$class_id','$section_id')
    ");

    $notice_id = mysqli_insert_id($con);

    /* ================= DYNAMIC FIELDS (LIKE STUDY) ================= */
    foreach($_POST as $key => $value){

        $skip = [
            'submit_notice','title','description','audience','semester',
            'publish_date','expiry_date','is_pinned',
            'notice_file','course_id','branch_id','session','class_id','section_id'
        ];

        if(in_array($key,$skip)) continue;
        if($value === '' || $value === null) continue;

     if(is_array($value)){
    $value = implode(',', $value);
}

$value = mysqli_real_escape_string($con, $value);

        mysqli_query($con,"
            INSERT INTO metadata (item_id,meta_key,meta_value)
            VALUES ('$notice_id','$key','$value')
        ");
    }

    echo "<script>alert('Notice Created Successfully');window.location='admin_notice.php';</script>";
}
?>

<!-- FORM -->


<h4>Create Notice</h4>
<a href="feilds.php" class="btn btn-dark mb-4">

Manage Fields

</a>

<div class="card shadow-lg mt-4">
  <div class="card-header bg-primary text-white">
    <h4 class="mb-0">📢 Create New Notice</h4>
  </div>

  <div class="card-body">

    <form action="" method="post" enctype="multipart/form-data">
      <div class="row">

        <!-- Title -->
        <div class="col-md-6 mb-3">
          <label class="font-weight-bold">Notice Title</label>
          <input type="text" name="title" class="form-control" placeholder="Enter notice title..." required>
        </div>

        <!-- Audience -->
        <div class="col-md-6 mb-3">
          <label class="font-weight-bold">Send To</label>
          <select name="audience[]" multiple class="form-control">
            <option value="students">👨‍🎓 Students</option>
            <option value="teachers">👩‍🏫 Teachers</option>
          </select>
        </div>

        <!-- Description -->
        <div class="col-md-12 mb-3">
          <label class="font-weight-bold">Description</label>
          <textarea name="description" class="form-control" rows="4" placeholder="Write your notice here..." required></textarea>
        </div>

        <!-- File -->
        <div class="col-md-6 mb-3">
          <label class="font-weight-bold">Attach File</label>
          <div class="custom-file">
            <input type="file" name="notice_file" class="custom-file-input">
            <label class="custom-file-label">Choose file</label>
          </div>
        </div>

        <!-- Publish Date -->
        <div class="col-md-3 mb-3">
          <label class="font-weight-bold">Publish Date</label>
          <input type="date" name="publish_date" class="form-control" required>
        </div>

        <!-- Expiry Date -->
        <div class="col-md-3 mb-3">
          <label class="font-weight-bold">Expiry Date</label>
          <input type="date" name="expiry_date" class="form-control">
        </div>

        <!-- Semester -->
        <div class="col-md-6 mb-3">
          <label class="font-weight-bold">Applicable Semester</label>
          <select name="semesters[]" multiple class="form-control">
            <option value="all">🌍 All</option>
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

        <!-- Pin -->
        <div class="col-md-6 mb-3 d-flex align-items-center">
          <div class="custom-control custom-switch mt-3">
            <input type="checkbox" class="custom-control-input" id="pinNotice" name="is_pinned" value="1">
            <label class="custom-control-label" for="pinNotice">
              📌 Pin this notice
            </label>
          </div>
        </div>

        <!-- Buttons -->
        <div class="col-12 d-flex justify-content-between mt-3">
          <a href="admin_notice.php" class="btn btn-secondary">
            ← Back
          </a>

          <button type="submit" name="submit_notice" class="btn btn-success px-4">
            🚀 Publish Notice
          </button>
        </div>
<!-- DYNAMIC FIELDS -->
<div class="col-12 mt-3">
<?php render_dynamic_form('notices'); ?>
</div>
      </div>
    </form>

  </div>
</div>
<?php
include('footer.php');
?>