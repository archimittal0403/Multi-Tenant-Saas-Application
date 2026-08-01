<?php include('includes/auth.php'); ?>
<?php checkRole(['super_admin','admin']); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>


<?php
$institute_id = $_SESSION['institute_id'];

/* =========================================
   DELETE SESSION
========================================= */
if(isset($_GET['delete_id'])){

    $delete_id = $_GET['delete_id'];

    $delete_post = mysqli_query($con,"
        DELETE FROM posts
        WHERE id='$delete_id'
    ");

    if($delete_post){

        echo "
        <div class='alert alert-success alert-dismissible fade show m-3'>
            Academic Session deleted successfully
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
}

/* =========================================
   ADD SESSION
========================================= */
if(isset($_POST['submit'])){

    $academic_session = trim($_POST['academic_session'] ?? '');

    $parent = 0;

    if(empty($academic_session)){

        echo "
        <div class='alert alert-danger m-3'>
            Academic Session is required
        </div>";

    } else {

        // CHECK DUPLICATE
        $check = $con->prepare("
            SELECT id
            FROM posts
            WHERE title=?
            AND type='session'
            AND institute_id=?
        ");

        $check->bind_param("si",
            $academic_session,
            $institute_id
        );

        $check->execute();

        $result = $check->get_result();

        if($result->num_rows > 0){

            echo "
            <div class='alert alert-warning alert-dismissible fade show m-3'>
                Session already exists
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";

        } else {

            // INSERT SESSION
            $stmt = $con->prepare("
                INSERT INTO posts
                (author,title,type,parent,institute_id)
                VALUES
                (1, ?, 'session', ?, ?)
            ");

            $stmt->bind_param("sii",
                $academic_session,
                $parent,
                $institute_id
            );

            $stmt->execute();

            echo "
            <div class='alert alert-success alert-dismissible fade show m-3'>
                Academic Session added successfully
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Academic Session Management</title>

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
            border-radius:20px;
            overflow:hidden;
        }

        .top-header{
            background:linear-gradient(135deg,#0d6efd,#198754);
            color:#fff;
            padding:22px;
        }

        .page-title{
            font-size:30px;
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
            padding:35px;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
        }

        .form-control,
        .form-select{
            border-radius:12px;
            min-height:50px;
        }

        textarea.form-control{
            min-height:120px;
        }

        .section-title{
            font-size:24px;
            font-weight:700;
        }

        .icon-box{
            width:60px;
            height:60px;
            border-radius:50%;
            background:#0d6efd;
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
        }

    </style>

</head>

<body>

<div class="container-fluid mt-4">

    <!-- PAGE TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="page-title">
            <i class="fa fa-calendar-days"></i> Academic Session Management
        </h2>

    </div>

    <!-- MAIN CARD -->
    <div class="card shadow-lg main-card">

        <!-- HEADER -->
        <div class="top-header d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h4 class="mb-1">Academic Session List</h4>
                <small>Manage all academic sessions easily</small>
            </div>

            <div class="d-flex gap-2">

                <a href="session.php?action=add"
                   class="btn btn-light btn-custom">

                    <i class="fa fa-plus"></i> Add Session

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
                            AND form_type='session'
                        ");

                        $field_array = [];

                        while($table_fetch = mysqli_fetch_assoc($table)){

                            $field_array[] = $table_fetch;

                            echo "<th>{$table_fetch['field_name']}</th>";
                        }

                        ?>

                        <th width="120">Action</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php

                    $count = 0;

                    $get = mysqli_query($con,"
                        SELECT *
                        FROM posts
                        WHERE institute_id='$institute_id'
                        AND type='session'
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

                            if($key == 'academic_session'){

                                $value = $row['title'];

                            } elseif($key == 'description'){

                                $value = $row['description'];

                            } else {

                                $value = '-';
                            }

                            echo "<td>$value</td>";
                        }

                        ?>

                        <!-- ACTION -->
                        <td>

                            <a href="session.php?delete_id=<?= $row['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure to delete this session?');">

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

    <!-- ADD SESSION FORM -->
    <?php
    if(isset($_GET['action']) && $_GET['action'] == 'add'){
    ?>

    <div class="form-wrapper mt-5">

        <div class="d-flex align-items-center mb-4">

            <div class="icon-box">

                <i class="fa fa-calendar-plus fa-lg"></i>

            </div>

            <div class="ms-3">

                <h3 class="section-title mb-0">
                    Add Academic Session
                </h3>

                <small class="text-muted">
                    Enter academic session details
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
                    AND form_type='session'
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

                <i class="fa fa-save"></i> Save Session

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