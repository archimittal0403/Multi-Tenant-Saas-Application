<?php

include('includes/auth.php');
checkRole('super_admin');

include('includes/config.php');

// ==========================
// Institute Select
// ==========================
if(isset($_POST['select'])){

    $institute_id = intval($_POST['institute_id']);

    $stmt = $con->prepare("SELECT * FROM institutes WHERE id=?");
    $stmt->bind_param("i",$institute_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows){

        $inst = $result->fetch_assoc();

        $_SESSION['institute_id']   = $inst['id'];
        $_SESSION['system_type']    = $inst['system_type'];
        $_SESSION['institute_code'] = $inst['institute_code'];

        header("Location: dashboard.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>Select Institute</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

    background:#f4f6f9;

}

.card{

    border-radius:15px;

    margin-top:80px;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

Select Institute

</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">

Choose Institute

</label>

<select name="institute_id" class="form-select" required>

<option value="">Select Institute</option>

<?php

$get=mysqli_query($con,"SELECT id,name FROM institutes ORDER BY name ASC");

while($row=mysqli_fetch_assoc($get)){

?>

<option value="<?= $row['id']; ?>">

<?= $row['name']; ?>

</option>

<?php } ?>

</select>

</div>

<button class="btn btn-success w-100" name="select">

Continue

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>