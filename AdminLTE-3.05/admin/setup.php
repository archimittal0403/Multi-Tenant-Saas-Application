<?php
session_start();
include('./includes/config.php');

$institute_id = $_SESSION['institute_id'];

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $type = $_POST['system_type'];
$short=$_POST['short_name'];
$logo=$_FILES['logo']['name'];
$tmp_logo=$_FILES['logo']['tmp_name'];
move_uploaded_file("../../assest/images/institute_image/".$logo,$tmp_logo);
$address=$_POST['address'];
$phone_no=$_POST['phone'];
   $insert=mysqli_query($con,"INSERT INTO institutes (name,system_type,short,logo,address,phone,is_setup_done) VALUES('$name','$type','$short','$logo','$address','$phone_no',1)");
    // mysqli_query($con, "UPDATE institutes 
    // SET name='$name', system_type='$type', is_setup_done=1 
    // WHERE id='$institute_id'");
$institute_id=mysqli_insert_id($con);
mysqli_query($con,"UPDATE accounts SET institute_id=$institute_id WHERE id='$user_id'");
$_SESSION['institute_id']=$institute_id;
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Setup Institute</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <div class="card shadow p-4">
    <h3 class="text-center mb-4">🏫 Setup Your Institute</h3>

    <form method="POST" enctype="multipart/form-data">

      <div class="mb-3">
        <label>Institute Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Short Name</label>
        <input type="text" name="short_name" class="form-control" placeholder="AKG, DU" required>
      </div>

      <div class="mb-3">
        <label>System Type</label>
        <select name="system_type" class="form-control" required>
          <option value="">Select Type</option>
          <option value="school">School</option>
          <option value="college">College</option>
        </select>
      </div>

      <div class="mb-3">
        <label>Institute Logo</label>
        <input type="file" name="logo" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" required></textarea>
      </div>

      <div class="mb-3">
        <label>Phone Number</label>
        <input type="text" name="phone" class="form-control" required>
      </div>

      <button type="submit" name="submit" class="btn btn-success w-100">
        Save & Continue
      </button>

    </form>
  </div>
</div>

</body>
</html>