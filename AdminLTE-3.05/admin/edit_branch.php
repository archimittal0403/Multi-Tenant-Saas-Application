<?php include('includes/auth.php'); ?>
<?php checkRole('admin'); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$institute_id = $_SESSION['institute_id'];

$edit_id = $_GET['edit_id'] ?? '';

if(empty($edit_id)){
    die("Invalid Branch ID");
}

/* =========================================
   GET BRANCH
========================================= */

$get_branch = mysqli_query($con,"
    SELECT *
    FROM posts
    WHERE id='$edit_id'
    AND type='branch'
");

$branch = mysqli_fetch_assoc($get_branch);

if(!$branch){
    die("Branch not found");
}

/* =========================================
   UPDATE BRANCH
========================================= */

if(isset($_POST['update'])){

    $branch_name = trim($_POST['branch_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if(empty($branch_name)){

        echo "
        <div class='alert alert-danger m-3'>
            Branch name is required
        </div>";

    } else {

        $update = $con->prepare("
            UPDATE posts
            SET
            title=?,
            description=?
            WHERE id=?
        ");

        $update->bind_param(
            "ssi",
            $branch_name,
            $description,
            $edit_id
        );

        $update->execute();

        /* =========================================
           UPDATE META
        ========================================= */

        foreach($_POST as $key => $value){

            if(
                $key == 'update' ||
                $key == 'branch_name' ||
                $key == 'description'
            ){
                continue;
            }

            $check = mysqli_query($con,"
                SELECT id
                FROM metadata
                WHERE item_id='$edit_id'
                AND meta_key='$key'
            ");

            if(mysqli_num_rows($check) > 0){

                $meta_update = $con->prepare("
                    UPDATE metadata
                    SET meta_value=?
                    WHERE item_id=?
                    AND meta_key=?
                ");

                $meta_update->bind_param(
                    "sis",
                    $value,
                    $edit_id,
                    $key
                );

                $meta_update->execute();

            } else {

                $meta_insert = $con->prepare("
                    INSERT INTO metadata
                    (item_id,meta_key,meta_value)
                    VALUES
                    (?,?,?)
                ");

                $meta_insert->bind_param(
                    "iss",
                    $edit_id,
                    $key,
                    $value
                );

                $meta_insert->execute();
            }
        }

        echo "
        <div class='alert alert-success alert-dismissible fade show m-3'>
            Branch updated successfully
            <button type='button'
                    class='btn-close'
                    data-bs-dismiss='alert'>
            </button>
        </div>";

        // REFRESH DATA
        $get_branch = mysqli_query($con,"
            SELECT *
            FROM posts
            WHERE id='$edit_id'
        ");

        $branch = mysqli_fetch_assoc($get_branch);
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Branch</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
background:#f4f6f9;
}

.content-wrapper{
min-height:100vh;
}

.edit-card{
border:none;
border-radius:20px;
overflow:hidden;
box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

.edit-header{
background:linear-gradient(135deg,#0d6efd,#6610f2);
padding:25px;
color:#fff;
}

.edit-header h3{
font-weight:700;
margin-bottom:5px;
}

.edit-body{
padding:35px;
background:#fff;
}

.form-label{
font-weight:600;
margin-bottom:8px;
}

.form-control,
.form-select{
height:50px;
border-radius:12px;
border:1px solid #dcdfe3;
box-shadow:none !important;
}

textarea.form-control{
height:auto;
min-height:120px;
padding-top:12px;
}

.form-control:focus,
.form-select:focus{
border-color:#0d6efd;
}

.btn-update{
background:linear-gradient(135deg,#198754,#157347);
border:none;
padding:12px 28px;
font-weight:600;
border-radius:12px;
color:#fff;
}

.btn-back{
padding:12px 24px;
border-radius:12px;
font-weight:600;
}

.page-title{
font-size:28px;
font-weight:700;
color:#343a40;
}

</style>

</head>

<body>

<div class="container-fluid mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="page-title">
<i class="fa fa-edit"></i> Edit Branch
</h2>

<a href="branch.php"
   class="btn btn-dark btn-back">

<i class="fa fa-arrow-left"></i>
Back

</a>

</div>

<div class="card edit-card">

<div class="edit-header">

<h3>
Update Branch Details
</h3>

<small>
Modify branch information carefully
</small>

</div>

<div class="edit-body">

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

$key = $field['field_key'];

$value = '';

if($key == 'branch_name'){

    $value = $branch['title'];

}

elseif($key == 'description'){

    $value = $branch['description'];

}

else{

    $meta_q = mysqli_query($con,"
        SELECT meta_value
        FROM metadata
        WHERE item_id='$edit_id'
        AND meta_key='$key'
    ");

    $meta = mysqli_fetch_assoc($meta_q);

    $value = $meta['meta_value'] ?? '';
}
?>

<div class="col-md-6 mb-4">

<label class="form-label">
<?= $field['field_name'] ?>
</label>

<?php

/* =========================================
   SELECT
========================================= */

if($field['field_type'] == 'select'){

    // DYNAMIC TYPE
    if(str_starts_with($field['options'],"type:")){

        $dynamic_type = explode(":",$field['options'])[1];

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

                $selected = ($value == $r['id'])
                    ? 'selected'
                    : '';

                echo "
                <option value='{$r['id']}'
                        $selected>
                    {$r['title']}
                </option>";
            }

            echo "</select>";
        }

    } else {

        $options = explode(',',$field['options']);

        echo "
        <select
            name='{$field['field_key']}'
            class='form-select'
        >";

        foreach($options as $opt){

            $opt = trim($opt);

            $selected = ($value == $opt)
                ? 'selected'
                : '';

            echo "
            <option value='$opt'
                    $selected>
                $opt
            </option>";
        }

        echo "</select>";
    }
}

/* =========================================
   TEXTAREA
========================================= */

elseif($field['field_type'] == 'textarea'){

    echo "
    <textarea
        name='{$field['field_key']}'
        class='form-control'
        placeholder='Enter {$field['field_name']}'
    >$value</textarea>";
}

/* =========================================
   INPUT
========================================= */

else{

    echo "
    <input
        type='{$field['field_type']}'
        name='{$field['field_key']}'
        value='$value'
        class='form-control'
        placeholder='Enter {$field['field_name']}'
    >";
}
?>

</div>

<?php } ?>

</div>

<div class="mt-3 d-flex gap-2 flex-wrap">

<button type="submit"
        name="update"
        class="btn btn-update">

<i class="fa fa-save"></i>
Update Branch

</button>

<a href="branch.php"
   class="btn btn-secondary btn-back">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>