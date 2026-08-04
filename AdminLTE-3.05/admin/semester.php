<?php include('includes/auth.php'); ?>
<?php checkRole(['super_admin','admin']); ?>

<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>


<?php
$institute_id = $_SESSION['institute_id'];

/* =========================================
   DELETE SEMESTER
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
            Semester deleted successfully
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
}

/* =========================================
   ADD SEMESTER
========================================= */
if(isset($_POST['submit'])){

    $course   = $_POST['course_name'] ?? null;
    $semester = trim($_POST['semester'] ?? '');

    $parent = $course;

    if(empty($semester)){

        echo "
        <div class='alert alert-danger m-3'>
            Semester name is required
        </div>";

    } else {

        // CHECK DUPLICATE
        $check = mysqli_query($con,"
            SELECT id
            FROM posts
            WHERE title='$semester'
            AND type='semester'
            AND institute_id='$institute_id'
        ");

        if(mysqli_num_rows($check) > 0){

            echo "
            <div class='alert alert-warning alert-dismissible fade show m-3'>
                Semester already exists
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";

        } else {

            // INSERT SEMESTER
            $stmt = $con->prepare("
                INSERT INTO posts
                (author,title,type,parent,institute_id)
                VALUES
                (1, ?, 'semester', ?, ?)
            ");

            $stmt->bind_param("sii",
                $semester,
                $parent,
                $institute_id
            );

            $stmt->execute();

            // INSERT METADATA
            $semester_id = mysqli_insert_id($con);

            $meta = $con->prepare("
                INSERT INTO metadata
                (item_id,meta_key,meta_value)
                VALUES
                (?,?,?)
            ");

            $key = 'course';

            $meta->bind_param("iss",
                $semester_id,
                $key,
                $course
            );

            $meta->execute();

            echo "
            <div class='alert alert-success alert-dismissible fade show m-3'>
                Semester added successfully
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Semester Management</title>

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

        .filter-box{
            background:#fff;
            padding:20px;
            border-radius:15px;
            box-shadow:0 4px 15px rgba(0,0,0,0.06);
        }

    </style>

</head>

<body>

<div class="container-fluid mt-4">

    <!-- PAGE TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="page-title">
            <i class="fa fa-layer-group"></i> Semester Management
        </h2>

    </div>

    <!-- MAIN CARD -->
    <div class="card shadow-lg main-card">

        <!-- HEADER -->
        <div class="top-header d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h4 class="mb-1">Semester List</h4>
                <small>Manage all semesters easily</small>
            </div>

            <div class="d-flex gap-2">

                <a href="semester.php?action=add"
                   class="btn btn-light btn-custom">

                    <i class="fa fa-plus"></i> Add Semester

                </a>
<?php if($_SESSION['user_type'] == 'super_admin'){ ?>

<a href="feilds.php" class="btn btn-dark btn-custom">
    <i class="fa fa-layer-group"></i> Dynamic Fields
</a>

<?php } ?>

            </div>

        </div>

        <!-- FILTER -->
        <div class="card-body pb-0">

            <div class="filter-box mb-4">

                <div class="row align-items-end">

                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Select Course
                        </label>

                        <select id="course_name"
                                onchange="go()"
                                class="form-select">

                            <option value="">
                                -- Select Course --
                            </option>

                            <?php

                            $select_name = mysqli_query($con,"
                                SELECT id,title
                                FROM posts
                                WHERE institute_id='$institute_id'
                                AND type='course'
                            ");

                            while($fetch = mysqli_fetch_assoc($select_name)){

                                $selected = (
                                    isset($_GET['course_name']) &&
                                    $_GET['course_name'] == $fetch['id']
                                ) ? 'selected' : '';

                                echo "
                                <option value='{$fetch['id']}' $selected>
                                    {$fetch['title']}
                                </option>";
                            }

                            ?>

                        </select>

                    </div>

                </div>

            </div>

            <!-- TABLE -->
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
                            AND form_type='semester'
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

                    $filter = "";

                    if(isset($_GET['course_name']) && $_GET['course_name'] != ''){

                        $course_id = $_GET['course_name'];

                        $filter = " AND d.meta_value='$course_id'";
                    }

                    $get = mysqli_query($con,"SELECT
    p.id,
    p.title,
    d.meta_value AS course
FROM posts p
LEFT JOIN metadata d
    ON d.item_id = p.id
    AND d.meta_key='course'
    AND d.meta_value IS NOT NULL
WHERE p.institute_id='$institute_id'
AND p.type='semester'
$filter
GROUP BY p.id
ORDER BY p.id ASC");

                    while($get_data = mysqli_fetch_assoc($get)){

                        $count++;

                        $name = $get_data['title'];

                     $course = '-';

if(!empty($get_data['course'])){

    $course_id = (int)$get_data['course'];

    $course_name = mysqli_query($con,"
        SELECT title 
        FROM posts 
        WHERE id='$course_id'
        LIMIT 1
    ");

    if($course_name && mysqli_num_rows($course_name) > 0){

        $course_fetch = mysqli_fetch_assoc($course_name);
        $course = $course_fetch['title'];
    }
}

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

        $value = $get_data['course'];

        if($value){
            $q = mysqli_query($con,"
                SELECT title FROM posts WHERE id='$value'
            ");
            $r = mysqli_fetch_assoc($q);
            $value = $r['title'] ?? '-';
        }

    }
    elseif($key == 'semester'){

        $value = $get_data['title'];
    }
    else{
        $value = '-';
    }

    echo "<td>$value</td>";
}
?>
                        <td>

                            <a href="semester.php?delete_id=<?= $get_data['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure to delete this semester?');">

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

            <div class="icon-box">

                <i class="fa fa-plus fa-lg"></i>

            </div>

            <div class="ms-3">

                <h3 class="section-title mb-0">
                    Add Semester
                </h3>

                <small class="text-muted">
                    Enter semester details properly
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
                    AND form_type='semester'
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

                <i class="fa fa-save"></i> Save Semester

            </button>

        </form>

    </div>

    <?php } ?>

</div>
<?php include('footer.php')?>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

function go(){

    var course = document.getElementById('course_name').value;

    window.location = `semester.php?course_name=${course}`;
}

</script>

</body>
</html>