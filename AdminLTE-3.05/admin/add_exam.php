<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>

<?php
$institute_id=$_SESSION['institute_id'];
?>
<?php


if(isset($_POST['save_exam_type'])){
$exam_name = $_POST['exam_name'];
$max_marks = $_POST['max_marks'];
$status    = $_POST['status'];
    // check duplicate
    $check = mysqli_query($con,"SELECT * FROM exam_type
    WHERE exam_type='$exam_name' AND institute_id='$institute_id'");

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Exam Type already exists!')</script>";
    } else {
        mysqli_query($con,"INSERT INTO exam_type (exam_type,max_marks,institute_id,status) 
        VALUES ('$exam_name',$max_marks,$institute_id,'$status')");
        echo "<script>alert('Exam Type Added Successfully')</script>";
    }
}
?>


    <div class="container-fluid mt-4">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary"><i class="fas fa-file-alt"></i> Add Exam Type</h3>
        </div>

        <div class="row">
            
            <!-- 🔹 Add Form -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">➕ Add New Exam Type</h5>
                    </div>

                    <div class="card-body">
                        <form method="post">
                            
                            <div class="form-group mb-3">
                                <label>Exam Type Name</label>
                                <input type="text" name="exam_name" class="form-control" 
                                placeholder="e.g. ST1, CT1, PUT" required>
                            </div>
<div class="form-group mb-3">
    <label>Max Marks</label>
    <input type="number"
           name="max_marks"
           class="form-control"
           value="100"
           required>
</div>

<div class="form-group mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="active" selected>Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>
                            <button type="submit" name="save_exam_type" 
                            class="btn btn-success w-100">
                                Save
                            </button>

                        </form>
                    </div>
                </div>
            </div>

                 <!-- 🔹 List Table -->
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">📋 Existing Exam Types</h5>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>S.no</th>
                                    <th>Exam Name</th>
                                    <th>Max Marks</th>
<th>Status</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $i = 1;
                                $result = mysqli_query($con,"SELECT * FROM exam_type 
                                WHERE institute_id='$institute_id'");

                                while($row = mysqli_fetch_assoc($result)){
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['exam_type']; ?></td>
                                    <td><?php echo $row['max_marks']; ?></td>
                                    <td>
    <?php if($row['status']=='active'){ ?>
        <span class="badge badge-success">Active</span>
    <?php } else { ?>
        <span class="badge badge-danger">Inactive</span>
    <?php } ?>
</td>
                                    <td>
                                        <a href="?delete=<?php echo $row['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Delete this exam type?')">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
    </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


<?php
// Delete logic
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    mysqli_query($con,"DELETE FROM exam_type WHERE id='$id'");
    echo "<script>window.location='add_exam.php'</script>";
}
?>
<?php include('footer.php'); ?>