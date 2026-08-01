<?php include('includes/auth.php');
checkRole('admin');?>

<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>


<?php
if(isset($_POST['update'])){
    $update_id=$_POST['id'];
$update_title=$_POST['title'];
$update_duration=$_POST['duration'];
$update_department=$_POST['department'];
$update_description=$_POST['description'];
// update query

$update_posts=mysqli_query($con,"UPDATE posts SET title='$update_title', description='$update_description' WHERE id='$update_id'");

$update_metadata=mysqli_query($con,"UPDATE metadata SET meta_value='$update_duration' WHERE meta_key='duration' AND item_id='$update_id'");


$update_metadata=mysqli_query($con,"UPDATE metadata SET meta_value='$update_department' WHERE meta_key='department' AND item_id='$update_id'");
if($update_posts || $update_metadata){
          echo "<div class='alert alert-success'>course already addeded Successfully</div>";
 echo "<script>window.location='course1.php?success=1';</script>";
}
}

?>
<?php
if(isset($_GET['edit_id'])){
    $edit_id=$_GET['edit_id'];
   $get = mysqli_query($con,"
SELECT 
    p.id,
    p.title,
    p.description,
    d.meta_value AS duration,
    dept.meta_value AS department
FROM posts p
LEFT JOIN metadata d 
    ON d.item_id = p.id AND d.meta_key='duration'
LEFT JOIN metadata dept 
    ON dept.item_id = p.id AND dept.meta_key='department'
WHERE p.id='$edit_id'
");
               $get_data=mysqli_fetch_assoc($get);
}
                
            
             ?>
             
                


<div class="container mt-4">

    <div class="card shadow-lg border-0 rounded-3">

        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa fa-edit"></i> Edit Course
            </h5>

            <a href="course1.php" class="btn btn-dark btn-sm">
                ← Back
            </a>
        </div>

        <!-- Body -->
        <div class="card-body">

            <form method="POST">

                <input type="hidden" name="id" value="<?= $get_data['id']; ?>">

                <div class="row">

                    <!-- Course Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Course Name</label>
                        <input type="text" name="title" class="form-control"
                               value="<?= $get_data['title']; ?>" required>
                    </div>

                    <!-- Duration -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Duration</label>
                        <input type="text" name="duration" class="form-control"
                               value="<?= $get_data['duration']; ?>" required>
                    </div>

                    <!-- Department -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Department</label>
                        <input type="text" name="department" class="form-control"
                               value="<?= $get_data['department']; ?>" >
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Description</label>
                          <input type="text" name="description" class="form-control"
                               value="<?= $get_data['description']; ?>" >
                    </div>

                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end gap-2">

                    <a href="course1.php" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" name="update" class="btn btn-success">
                        <i class="fa fa-save"></i> Update Course
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>