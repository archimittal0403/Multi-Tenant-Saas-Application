<?php include('../includes/config.php')?>

<?php 
if(isset($_POST['submit'])){
  // echo '<pre>';
  // print_r($_FILES);
  // echo '</pre>';

  $name=$_POST['name'];
  $category=$_POST['category'];
  $duration=$_POST['duration'];
  $image=$_FILES["thumbnail"]["name"];
  $date=date('Y-m-d');
  $target_dir = "uploads/";
  $_SESSION['success_msg']='courses has been sucessfully added';
  $target_file = $target_dir . basename($_FILES["thumbnail"]["name"]);
 
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$uploadOk = 1;


// and this strtolower tell wheather our file is jpg,jpeg
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}
// Check file size
if ($_FILES["thumbnail"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}



// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
    mysqli_query($con,"INSERT INTO courses (`name`,`category`,`duration`,`image`,`date`) VALUES('$name', '$category', '$duration', '$image', '$date')");
    $_SESSION['success_msg'] = 'course has been uploaded successfully';
    header('Location: courses.php');
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}

}
?>
<?php include('header.php')?>
  <?php 
 include('sidebar.php')?>  

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Manage Courses :-</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Admin</a></li>
              <li class="breadcrumb-item active">courses</li>
            </ol>
          </div><!-- /.col -->
          <?php
          if(isset($_SESSION['success_msg'])){?>
            <div class="col-12">
            <small class="text-success" style="font-size:16px"><?=$_SESSION['success_msg']?></small>
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
     <section class="content">
      <div class="container-fluid">


      <?php
      if(isset($_REQUEST['action'])){ ?>

        <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                Add New Courses
</h2>
</div>
<div class="card-body">
    <!-- Info boxes -->
     <form action="" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <h5>Course Name</h5>
        <input type="text" name="name" id=""  class="form-control" placeholder="course name" required-class="form-control">
    
      </div>
      <div class="form-group mt-4">
        <h5>Course Category</h5>
       <select name="category" id="" class="form-control">
        <option value="">Select Category</option>
        <option value="programmming">Programming</option>
        <option value="web development">Web development & desigining</option>
        <option value="App-development">App development</option>
       </select>
    
      </div>

      <div class="form-group mt-4">
      <h5>Duration</h5>
  <input type="text" name="duration" id="duration" class="form-control" placeholder="course duration">
      </div>

<div class="form-group mt-4">
  <input type="file" name="thumbnail" id="thumbnail" >
      </div>
      <button name="submit" class="btn btn-success">
        Submit
   </button>
      </form>

</div>     
    </div> 
      

  

    <?php }else { ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    Courses
</h2>
<div class="card-tools">
 <a href="?action=add-new" class="btn btn-success btn-sm"><i class="fa fa-plus mr-2"></i>Add New</a>
</div>
</div>
<div class="card-body">
        <!-- Info boxes -->
        <div class="table-responsive">
          <table class="table table-bordered">
            <thread>
              <tr>
                <th>S.No</th>
                <th>Image</th>
                <th>Course Name</th>
            
                <th>Category</th>
                <th>Duration</th>
                <th>Date</th>
</tr>
</thread>
  <tbody>
    <?php
    $count=1;
    $curse_query=mysqli_query($con,'SELECT * FROM courses');

    while($course=mysqli_fetch_object($curse_query)){?>
      <tr>
      <td><?=$count++?></td>
      <td><img src="uploads/<?=$course->image?>" height="70" alt=""></td>
  <td><?=$course->name?></td>
  <td><?=$course->category?></td>
  <td><?=$course->duration?></td>
  <td><?=$course->date?></td>
  
  </tr>
  <?php } ?>
    </tbody>
</table>
</div>
</div>     
        </div> 
        <!-- /.row -->

     <?php } ?>
      </div><!--/. container-fluid -->
    </section>
<?php include('footer.php')?>
