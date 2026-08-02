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

$institute_id = $_SESSION['institute_id'] ?? 0;

$teacher_id = $_GET['edit_teacher'] ?? 0;
if(!$teacher_id){
    die("Invalid Teacher ID");
}

/* ================= FETCH DATA ================= */
$teacher = $con->query("SELECT * FROM accounts WHERE id='$teacher_id'")->fetch_assoc();

$meta = [];
$res = $con->query("SELECT meta_key, meta_value FROM usermeta WHERE user_id='$teacher_id'");
while($row = $res->fetch_assoc()){
    $meta[$row['meta_key']] = $row['meta_value'];
}

/* ================= UPDATE ================= */
if(isset($_POST['update_teacher'])){

    $name  = $_POST['name'];
    $email = $_POST['email'];

    // accounts update
    $con->query("UPDATE accounts 
        SET Name='$name', email='$email'
        WHERE id='$teacher_id'
    ");

    /* ================= SAFE META UPDATE (NO DELETE) ================= */
    foreach($_POST as $key => $value){

        if(in_array($key,['update_teacher','name','email'])) continue;
        if($value == '') continue;

        if(is_array($value)){
            $value = json_encode($value);
        }

        $check = $con->query("SELECT id FROM usermeta 
                              WHERE user_id='$teacher_id' 
                              AND meta_key='$key'");

        if($check->num_rows > 0){

            $con->query("UPDATE usermeta 
                        SET meta_value='$value'
                        WHERE user_id='$teacher_id'
                        AND meta_key='$key'");

        } else {

            $stmt = $con->prepare("INSERT INTO usermeta(user_id,meta_key,meta_value) VALUES (?,?,?)");
            $stmt->bind_param("iss",$teacher_id,$key,$value);
            $stmt->execute();
        }
    }

    /* ================= IMAGE UPDATE ================= */
    if(!empty($_FILES['th_image']['name'])){

        $img = time().'_'.$_FILES['th_image']['name'];
        move_uploaded_file($_FILES['th_image']['tmp_name'],
        "uploads/teacher_photo/".$img);

        $check = $con->query("SELECT id FROM usermeta 
                              WHERE user_id='$teacher_id' 
                              AND meta_key='th_image'");

        if($check->num_rows > 0){

            $con->query("UPDATE usermeta 
                        SET meta_value='$img'
                        WHERE user_id='$teacher_id'
                        AND meta_key='th_image'");
        } else {

            $con->query("INSERT INTO usermeta(user_id,meta_key,meta_value)
                        VALUES ('$teacher_id','th_image','$img')");
        }
    }

    echo "<script>
        window.location.href='teacher.php?user=teacher';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>

<body style="background:#f4f7fc;">

<div class="container mt-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">
    <h4>✏️ Edit Teacher</h4>
</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<!-- NAME -->
<div class="form-group mb-3">
    <label>Teacher Name</label>
    <input type="text" name="name" class="form-control"
    value="<?= $teacher['Name'] ?? '' ?>">
</div>

<!-- EMAIL -->
<div class="form-group mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control"
    value="<?= $teacher['email'] ?? '' ?>">
</div>

<!-- MOBILE -->
<div class="form-group mb-3">
    <label>Mobile</label>
    <input type="text" name="mobile" class="form-control"
    value="<?= $meta['mobile'] ?? '' ?>">
</div>

<!-- IMAGE -->
<div class="form-group mb-3">
    <label>Teacher Image</label><br>

    <?php if(!empty($meta['th_image'])){ ?>
        <img src="uploads/teacher_photo/<?= $meta['th_image'] ?>" width="80">
    <?php } ?>

    <input type="file" name="th_image" class="form-control mt-2">
</div>
<?php if($institute_type=='school'){ ?>

<!-- CLASS -->
<div class="form-group mb-3">
    <label>Class</label>
    <input type="text" name="class_name" class="form-control"
    value="<?= $meta['class_name'] ?? '' ?>">
</div>

<!-- SECTION -->
<div class="form-group mb-3">
    <label>Section</label>
    <input type="text" name="section_name" class="form-control"
    value="<?= $meta['section_name'] ?? '' ?>">
</div>

<!-- SESSION -->
<div class="form-group mb-3">
    <label>Session</label>
    <input type="text" name="session" class="form-control"
    value="<?= $meta['session'] ?? '' ?>">
</div>

<?php } else { ?>

<!-- COURSE -->
<div class="form-group mb-3">
    <label>Course</label>
    <input type="text" name="course_name" class="form-control"
    value="<?= $meta['course_name'] ?? '' ?>">
</div>

<!-- BRANCH -->
<div class="form-group mb-3">
    <label>Branch</label>
    <input type="text" name="branch_name" class="form-control"
    value="<?= $meta['branch_name'] ?? '' ?>">
</div>

<!-- SEMESTER -->
<div class="form-group mb-3">
    <label>Semester</label>
    <input type="text" name="semester" class="form-control"
    value="<?= $meta['semester'] ?? '' ?>">
</div>

<!-- SESSION -->
<div class="form-group mb-3">
    <label>Session</label>
    <input type="text" name="session" class="form-control"
    value="<?= $meta['session'] ?? '' ?>">
</div>

<?php } ?>
<!-- SALARY -->
<div class="form-group mb-3">
    <label>Salary</label>
    <input type="text" name="salary" class="form-control"
    value="<?= $meta['salary'] ?? '' ?>">
</div>

<!-- BANK -->
<div class="form-group mb-3">
    <label>Bank</label>
    <input type="text" name="bank" class="form-control"
    value="<?= $meta['bank'] ?? '' ?>">
</div>

<!-- ACCOUNT -->
<div class="form-group mb-3">
    <label>Account No</label>
    <input type="text" name="aco" class="form-control"
    value="<?= $meta['aco'] ?? '' ?>">
</div>

<!-- IFSC -->
<div class="form-group mb-3">
    <label>IFSC</label>
    <input type="text" name="ifsc" class="form-control"
    value="<?= $meta['ifsc'] ?? '' ?>">
</div>

<!-- BUTTON -->
<button type="submit" name="update_teacher" class="btn btn-success">
Update Teacher
</button>

<a href="teacher.php?user=teacher" class="btn btn-secondary">
Cancel
</a>

</form>

</div>
</div>

</div>
<?php
include('footer.php');
?>
</body>
</html>