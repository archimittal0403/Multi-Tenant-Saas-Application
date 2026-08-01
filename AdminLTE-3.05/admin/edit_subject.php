<?php include('includes/auth.php'); ?>
<?php checkRole('admin'); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$institute_id = $_SESSION['institute_id'];
$edit_id = $_GET['edit_id'] ?? 0;

/* =========================================
   FETCH SUBJECT
========================================= */

$get_subject = mysqli_query($con,"
    SELECT *
    FROM posts
    WHERE id='$edit_id'
    AND type='subject'
    AND institute_id='$institute_id'
");

$subject = mysqli_fetch_assoc($get_subject);

if(!$subject){
    die("<div class='alert alert-danger m-4'>Subject not found</div>");
}

/* =========================================
   UPDATE SUBJECT
========================================= */

if(isset($_POST['update'])){

    $subject_name = trim($_POST['subject_name'] ?? '');

    if(empty($subject_name)){

        echo "
        <div class='alert alert-danger m-3'>
            Subject name is required
        </div>";

    } else {

        // UPDATE SUBJECT
        $stmt = $con->prepare("
            UPDATE posts
            SET title=?
            WHERE id=?
        ");

        $stmt->bind_param("si",
            $subject_name,
            $edit_id
        );

        $stmt->execute();

        // DELETE OLD META
        mysqli_query($con,"
            DELETE FROM metadata
            WHERE item_id='$edit_id'
        ");

        // SAVE NEW META
        foreach($_POST as $key => $value){

            if(
                $key == 'update' ||
                $key == 'subject_name'
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
                $edit_id,
                $key,
                $value
            );

            $meta->execute();
        }

        echo "
        <script>
            window.location.href='add_subject.php?msg=updated';
        </script>";
        exit;
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Subject</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        body{
            background:#f4f6f9;
        }

        .page-title{
            font-size:30px;
            font-weight:700;
            color:#343a40;
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

    <div class="form-wrapper">

        <div class="d-flex align-items-center mb-4">

            <div class="icon-box">
                <i class="fa fa-edit fa-lg"></i>
            </div>

            <div class="ms-3">

                <h3 class="section-title mb-0">
                    Edit Subject
                </h3>

                <small class="text-muted">
                    Update subject details properly
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
                    AND form_type='subject'
                ");

                while($field = mysqli_fetch_assoc($fields_query)){

                    $field_key = $field['field_key'];

                    // SUBJECT NAME
                    if($field_key == 'subject_name'){

                        $field_value = $subject['title'];

                    }

                    // DESCRIPTION
                    elseif($field_key == 'description'){

                        $field_value = $subject['description'];

                    }

                    // META VALUES
                    else{

                        $meta_q = mysqli_query($con,"
                            SELECT meta_value
                            FROM metadata
                            WHERE item_id='$edit_id'
                            AND meta_key='$field_key'
                        ");

                        $meta = mysqli_fetch_assoc($meta_q);

                        $field_value = $meta['meta_value'] ?? '';
                    }

                ?>

                <div class="col-md-6 mb-4">

                    <label class="form-label fw-bold">
                        <?= $field['field_name'] ?>
                    </label>

                    <?php

                    // SELECT FIELD
                    if($field['field_type'] == 'select'){

                        // DYNAMIC SELECT
                        if(str_starts_with($field['options'], "type:")){

                            $dynamic_type = explode(":", $field['options'])[1];

                            // PARENT SUPPORT
                            $selected_parent =
                                $_POST['class_name']
                                ?? $_POST['course_name']
                                ?? '';

                            // LOAD FILTERED DATA
                            if(!empty($field['source']) && !empty($selected_parent)){

                                $res = mysqli_query($con,"
                                    SELECT id,title
                                    FROM posts
                                    WHERE institute_id='$institute_id'
                                    AND type='$dynamic_type'
                                    AND parent='$selected_parent'
                                ");

                            } else {

                                $res = mysqli_query($con,"
                                    SELECT id,title
                                    FROM posts
                                    WHERE institute_id='$institute_id'
                                    AND type='$dynamic_type'
                                ");
                            }

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

                                $selected = ($field_value == $r['id'])
                                    ? 'selected'
                                    : '';

                                echo "
                                <option value='{$r['id']}' $selected>
                                    {$r['title']}
                                </option>";
                            }

                            echo "</select>";

                        }

                        // STATIC SELECT
                        else{

                            $options = explode(',', $field['options']);

                            echo "
                            <select
                                name='{$field['field_key']}'
                                class='form-select'
                            >";

                            echo "
                            <option value=''>
                                -- Select {$field['field_name']} --
                            </option>";

                            foreach($options as $opt){

                                $selected = ($field_value == $opt)
                                    ? 'selected'
                                    : '';

                                echo "
                                <option value='$opt' $selected>
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
                        >$field_value</textarea>";
                    }

                    // NORMAL INPUT
                    else{

                        echo "
                        <input
                            type='{$field['field_type']}'
                            name='{$field['field_key']}'
                            value='$field_value'
                            class='form-control'
                            placeholder='Enter {$field['field_name']}'
                        >";
                    }

                    ?>

                </div>

                <?php } ?>

            </div>

            <button type="submit"
                    name="update"
                    class="btn btn-success btn-lg px-4">

                <i class="fa fa-save"></i> Update Subject

            </button>

            <a href="add_subject.php"
               class="btn btn-secondary btn-lg px-4">

                Cancel

            </a>

        </form>

    </div>

</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>