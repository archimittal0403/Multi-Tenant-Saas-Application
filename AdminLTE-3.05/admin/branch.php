<?php include('includes/auth.php'); ?>
<?php checkRole(['super_admin','admin']); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>


<?php
$institute_id = $_SESSION['institute_id'];

/* =========================================
   DELETE BRANCH
========================================= */
if(isset($_GET['delete_id'])){

    $delete_id = $_GET['delete_id'];

    $delete_post = mysqli_query($con,"
        DELETE FROM posts 
        WHERE id='$delete_id'
    ");

    mysqli_query($con,"
        DELETE FROM metadata 
        WHERE item_id='$delete_id'
    ");

    if($delete_post){

        echo "
        <div class='alert alert-success alert-dismissible fade show m-3'>
            Branch deleted successfully
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
}

/* =========================================
   ADD BRANCH
========================================= */
if(isset($_POST['submit'])){

    $branch_name = trim($_POST['branch_name'] ?? '');
    $course      = $_POST['course_name'] ?? null;
    $description = trim($_POST['description'] ?? '');

    $parent = (int)$course;

    if(empty($branch_name)){

        echo "
        <div class='alert alert-danger m-3'>
            Branch name is required
        </div>";

    } else {

        // INSERT BRANCH
        $stmt = $con->prepare("
            INSERT INTO posts
            (author,title,description,type,parent,institute_id)
            VALUES
            (1, ?, ?, 'branch', ?, ?)
        ");

        $stmt->bind_param("ssii",
            $branch_name,
            $description,
            $parent,
            $institute_id
        );

        $stmt->execute();

        $branch_id = mysqli_insert_id($con);

        // SAVE META DATA
        foreach($_POST as $key => $value){

            if(
                $key == 'submit' ||
                $key == 'branch_name' ||
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

            $meta->bind_param("iss",
                $branch_id,
                $key,
                $value
            );

            $meta->execute();
        }

        echo "
        <div class='alert alert-success alert-dismissible fade show m-3'>
            Branch added successfully
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Branch Management</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        body{
            background:#f4f6f9;
        }

        .main-card{
            border:none;
            border-radius:18px;
            overflow:hidden;
        }

        .top-header{
            background:linear-gradient(135deg,#0d6efd,#198754);
            color:#fff;
            padding:20px;
        }

        .page-title{
            font-size:28px;
            font-weight:700;
            color:#343a40;
        }

        .btn-custom{
            border-radius:10px;
            font-weight:600;
            padding:8px 16px;
        }

        .table thead{
            background:#212529;
            color:#fff;
        }

        .table tbody tr:hover{
            background:#eef4ff;
            transition:0.3s;
        }

        .badge-custom{
            background:#198754;
            padding:7px 10px;
            border-radius:8px;
            font-size:13px;
        }

        .form-wrapper{
            background:#fff;
            padding:30px;
            border-radius:18px;
            box-shadow:0 5px 18px rgba(0,0,0,0.08);
        }

        .form-control,
        .form-select{
            border-radius:12px;
            min-height:48px;
        }

        textarea.form-control{
            min-height:120px;
        }

        .section-title{
            font-size:24px;
            font-weight:700;
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
            <i class="fa fa-code-branch"></i> Branch Management
        </h2>

    </div>

    <!-- MAIN CARD -->
    <div class="card shadow-lg main-card">

        <!-- HEADER -->
        <div class="top-header d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h4 class="mb-1">Branch List</h4>
                <small>Manage institute branches easily</small>
            </div>

            <div class="d-flex gap-2">

                <a href="branch.php?action=add"
                   class="btn btn-light btn-custom">

                    <i class="fa fa-plus"></i> Add Branch

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
                            AND form_type='branch'
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
                        AND type='branch'
                        ORDER BY id Asc
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

                            // POSTS TABLE VALUES
                            if($key == 'branch_name'){

                                $value = $row['title'];

                            }

                            elseif($key == 'description'){

                                $value = $row['description'];

                            }

                            else{

                                // METADATA VALUES
                                $meta_q = mysqli_query($con,"
                                    SELECT meta_value
                                    FROM metadata
                                    WHERE item_id='{$row['id']}'
                                    AND meta_key='$key'
                                ");

                                $meta = mysqli_fetch_assoc($meta_q);

                                $value = $meta['meta_value'] ?? '-';
                            }

                            // SHOW COURSE NAME
                            if($key == 'course_name' && $value){

                                $q = mysqli_query($con,"
                                    SELECT title 
                                    FROM posts
                                    WHERE id='$value'
                                ");

                                $r = mysqli_fetch_assoc($q);

                                $value = $r['title'] ?? '-';
                            }

                            echo "<td>$value</td>";
                        }

                        ?>

                        <!-- ACTION -->
                        <td>

                            <a href="edit_branch.php?edit_id=<?= $row['id']; ?>"
                               class="btn btn-primary btn-sm action-btn">

                                <i class="fa fa-edit"></i>

                            </a>

                            <a href="branch.php?delete_id=<?= $row['id']; ?>"
                               class="btn btn-danger btn-sm action-btn"
                               onclick="return confirm('Are you sure to delete this branch?');">

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

    <div class="form-wrapper mt-5">

        <div class="d-flex align-items-center mb-4">

            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                 style="width:60px;height:60px;">

                <i class="fa fa-plus fa-lg"></i>

            </div>

            <div class="ms-3">

                <h3 class="section-title mb-0">
                    Add New Branch
                </h3>

                <small class="text-muted">
                    Fill branch details properly
                </small>

            </div>

        </div>

        <form method="POST">

            <div class="row">

                <?php

                $fields_query = mysqli_query($con,"
                    SELECT * 
                    FROM fields
                    WHERE institute_id='$institute_id'
                    AND form_type='branch'
                ");

                while($field = mysqli_fetch_assoc($fields_query)){

                ?>

                <div class="col-md-6 mb-4">

                    <label class="form-label fw-bold">
                        <?= $field['field_name'] ?>
                    </label>

                    <?php

                    // SELECT FIELD
                    if($field['field_type'] == 'select'){

                        if(str_starts_with($field['options'], "type:")){

                            $dynamic_type = explode(":", $field['options'])[1];

                            if($dynamic_type == "course"){

                                $res = mysqli_query($con,"
                                    SELECT id,title
                                    FROM posts
                                    WHERE institute_id='$institute_id'
                                    AND type='course'
                                ");

                                echo "
                                <select 
                                    name='{$field['field_key']}'
                                    class='form-select'
                                >";

                                echo "
                                <option value=''>
                                    -- Select {$field['field_name']} --
                                </option>";

                                while($r = mysqli_fetch_assoc($res)){

                                    echo "
                                    <option value='{$r['id']}'>
                                        {$r['title']}
                                    </option>";
                                }

                                echo "</select>";
                            }

                        } else {

                            $options = explode(',', $field['options']);

                            echo "
                            <select 
                                name='{$field['field_key']}'
                                class='form-select'
                            >";

                            foreach($options as $opt){

                                echo "
                                <option value='$opt'>
                                    $opt
                                </option>";
                            }

                            echo "</select>";
                        }

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

            <button type="submit"
                    name="submit"
                    class="btn btn-success btn-lg px-4">

                <i class="fa fa-save"></i> Save Branch

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