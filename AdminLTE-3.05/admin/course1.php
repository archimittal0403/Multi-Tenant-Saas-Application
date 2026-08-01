<?php include('includes/auth.php'); ?>
<?php checkRole(['admin','super_admin']); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>


<?php
$institute_id = $_SESSION['institute_id'];

/* =========================
   ADD COURSE
========================= */
if(isset($_POST['submit'])){

    $course_name = trim($_POST['course_name']);
 $description = trim($_POST['description'] ?? '');

    if(empty($course_name)){
        echo "<div class='alert alert-danger m-3'>Course name is required</div>";
    } else {

        // CHECK EXISTING COURSE
        $check = $con->prepare("SELECT id FROM posts WHERE title=? AND type='course' AND institute_id=?");
        $check->bind_param("si", $course_name, $institute_id);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){

            echo "
            <div class='alert alert-warning alert-dismissible fade show m-3'>
                Course already exists
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";

        } else {

            // INSERT COURSE
            $stmt = $con->prepare("
                INSERT INTO posts 
                (author,title,description,type,parent,institute_id) 
                VALUES 
                (1, ?, ?, 'course', 0, ?)
            ");

            $stmt->bind_param("ssi", $course_name, $description, $institute_id);
            $stmt->execute();

            $course_id = mysqli_insert_id($con);

            // SAVE META DATA
            foreach($_POST as $key => $value){

                if(
                    $key == 'submit' ||
                    $key == 'course_name' ||
                    $key == 'description'
                ){
                    continue;
                }

                if($value == '') continue;

                $meta = $con->prepare("
                    INSERT INTO metadata
                    (item_id, meta_key, meta_value)
                    VALUES
                    (?, ?, ?)
                ");

                $meta->bind_param("iss", $course_id, $key, $value);
                $meta->execute();
            }

            echo "
            <div class='alert alert-success alert-dismissible fade show m-3'>
                Course added successfully
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Course Management</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        body{
            background:#f4f6f9;
        }

        .page-title{
            font-size:28px;
            font-weight:700;
            color:#343a40;
        }

        .main-card{
            border:none;
            border-radius:18px;
            overflow:hidden;
        }

        .card-header-custom{
            background: linear-gradient(135deg,#0d6efd,#198754);
            color:#fff;
            padding:20px;
        }

        .btn-custom{
            border-radius:10px;
            padding:8px 16px;
            font-weight:600;
        }

        .table thead{
            background:#212529;
            color:#fff;
        }

        .table tbody tr:hover{
            background:#f1f5ff;
            transition:0.3s;
        }

        .form-control,
        .form-select{
            border-radius:10px;
            min-height:45px;
        }

        textarea.form-control{
            min-height:120px;
        }

        .form-section{
            background:#fff;
            padding:25px;
            border-radius:16px;
            box-shadow:0 4px 15px rgba(0,0,0,0.08);
        }

        .badge-custom{
            background:#198754;
            font-size:13px;
            padding:7px 10px;
            border-radius:8px;
        }

        .action-btn i{
            font-size:14px;
        }

    </style>

</head>

<body>

<div class="container-fluid mt-4">

    <!-- PAGE TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="page-title">
            <i class="fa fa-book-open"></i> Course Management
        </h2>

    </div>

    <div class="card shadow-lg main-card">

        <!-- HEADER -->
        <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h4 class="mb-1">Course List</h4>
                <small>Manage all institute courses easily</small>
            </div>

            <div class="d-flex gap-2">

                <a href="course1.php?action=add" class="btn btn-light btn-custom">
                    <i class="fa fa-plus"></i> Add Course
                </a>
<?php if($_SESSION['user_type'] == 'super_admin'){ ?>

<a href="feilds.php" class="btn btn-dark btn-custom">
    <i class="fa fa-layer-group"></i> Dynamic Fields
</a>

<?php } ?>

            </div>

        </div>

        <!-- TABLE -->
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>
                    <tr>

                        <th width="70">#</th>

                        <?php
                        $table = mysqli_query($con,"
                            SELECT * 
                            FROM fields 
                            WHERE institute_id='$institute_id' 
                            AND form_type='course'
                        ");

                        $field_array = [];

                        while($table_fetch = mysqli_fetch_assoc($table)){

                            $field_array[] = $table_fetch;

                            echo "<th>{$table_fetch['field_name']}</th>";
                        }
                        ?>

                        <th width="150">Action</th>

                    </tr>
                    </thead>

                    <tbody>

                    <?php

                    $count = 0;

                    $get = mysqli_query($con,"
                        SELECT * 
                        FROM posts 
                        WHERE institute_id='$institute_id' 
                        AND type='course'
                        ORDER BY id ASC
                    ");

                    while($row = mysqli_fetch_assoc($get)){

                        $count++;
                    ?>

                    <tr>

                        <td>
                            <span class="badge badge-custom">
                                <?= $count ?>
                            </span>
                        </td>

                        <?php

                        foreach($field_array as $f){

                            $key = $f['field_key'];

                            if($key == 'course_name'){
                                $value = $row['title'];
                            }

                            elseif($key == 'description'){
                                $value = $row['description'];
                            }

                            else{

                                $meta_q = mysqli_query($con,"
                                    SELECT meta_value
                                    FROM metadata
                                    WHERE item_id='{$row['id']}'
                                    AND meta_key='$key'
                                ");

                                $meta = mysqli_fetch_assoc($meta_q);

                                $value = $meta['meta_value'] ?? '-';
                            }

                            echo "<td>$value</td>";
                        }

                        ?>

                        <td>

                            <a href="edit_course1.php?edit_id=<?= $row['id']; ?>"
                               class="btn btn-primary btn-sm action-btn">

                                <i class="fa fa-edit"></i>

                            </a>

                            <a href="delete_course1.php?delete_id=<?= $row['id']; ?>"
                               class="btn btn-danger btn-sm action-btn"
                               onclick="return confirm('Are you sure you want to delete this course?');">

                                <i class="fa fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    <!-- ADD FORM -->
    <?php
    if(isset($_GET['action']) && $_GET['action'] == 'add'){
    ?>

    <div class="form-section mt-5">

        <div class="d-flex align-items-center mb-4">

            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                 style="width:55px;height:55px;">

                <i class="fa fa-plus fa-lg"></i>

            </div>

            <div class="ms-3">
                <h3 class="mb-0">Add New Course</h3>
                <small class="text-muted">Fill all required course details</small>
            </div>

        </div>

        <form method="POST">

            <div class="row">

                <?php

                $field_query = mysqli_query($con,"
                    SELECT * 
                    FROM fields 
                    WHERE institute_id='$institute_id'
                    AND form_type='course'
                ");

                while($field = mysqli_fetch_assoc($field_query)){

                ?>

                <div class="col-md-6 mb-4">

                    <label class="form-label fw-bold">
                        <?= $field['field_name'] ?>
                    </label>

                    <?php

                    // SELECT FIELD
                    if($field['field_type'] == 'select'){

                        $options = explode(',', $field['options']);

                        echo "<select name='{$field['field_key']}' class='form-select'>";

                        echo "<option value=''>Select {$field['field_name']}</option>";

                        foreach($options as $opt){

                            echo "<option value='$opt'>$opt</option>";
                        }

                        echo "</select>";
                    }

                    // TEXTAREA
                    elseif($field['field_type'] == 'textarea'){

                        echo "
                        <textarea 
                            name='{$field['field_key']}'
                            class='form-control'
                            placeholder='Enter {$field['field_name']}'
                        ></textarea>";
                    }

                    // INPUT FIELD
                    else{

                        echo "
                        <input 
                            type='{$field['field_type']}'
                            name='{$field['field_key']}'
                            class='form-control'
                            placeholder='Enter {$field['field_name']}'
                        >";
                    }

                    ?>

                </div>

                <?php } ?>

            </div>

            <button type="submit" name="submit" class="btn btn-success btn-lg px-4">

                <i class="fa fa-save"></i> Save Course

            </button>

        </form>

    </div>

    <?php } ?>

</div>
<?php include('footer.php')?>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>