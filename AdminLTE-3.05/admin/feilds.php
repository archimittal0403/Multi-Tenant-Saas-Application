<?php include('includes/auth.php');
checkRole('super_admin'); ?>

<?php include('includes/config.php') ?>
<?php include('includes/functions.php') ?>
<?php include('header.php') ?>
<?php include('sidebar.php') ?>

<?php
$institute_id = $_SESSION['institute_id'];

if(isset($_POST['add_field'])){

    $field_name = trim($_POST['field_name']);
    $form_type = strtolower(trim($_POST['form_type']));
    $field_key = strtolower(str_replace(' ','_',$field_name));

    $field_type = $_POST['field_type'];
    $options = $_POST['options'];
    $source = $_POST['source'];

    $stmt = $con->prepare("
        INSERT INTO fields
        (field_name,field_key,field_type,options,institute_id,form_type,source)
        VALUES (?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssssiss",
        $field_name,
        $field_key,
        $field_type,
        $options,
        $institute_id,
        $form_type,
        $source
    );

    $stmt->execute();

    $success = "Field Added Successfully!";
}
?>

<style>

.content-wrapper{
background:#f4f6f9;
min-height:100vh;
}

.page-header{
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:15px;
margin-bottom:20px;
}

.page-title{
font-size:28px;
font-weight:700;
color:#343a40;
margin:0;
}

.custom-card{
border:none;
border-radius:18px;
overflow:hidden;
box-shadow:0 4px 20px rgba(0,0,0,0.08);
background:#fff;
}

.custom-card .card-header{
background:linear-gradient(135deg,#007bff,#0056d2);
padding:18px 25px;
border:none;
}

.custom-card .card-header h4{
margin:0;
font-size:22px;
font-weight:600;
color:#fff;
}

.custom-card .card-body{
padding:30px;
}

.form-label{
font-weight:600;
margin-bottom:8px;
color:#343a40;
}

.form-control,
.form-select{
height:48px;
border-radius:12px;
border:1px solid #dcdfe3;
box-shadow:none !important;
transition:0.3s;
}

textarea.form-control{
height:auto;
min-height:100px;
padding-top:12px;
}

.form-control:focus,
.form-select:focus{
border-color:#007bff;
box-shadow:0 0 0 0.15rem rgba(0,123,255,.15) !important;
}

.btn-save{
background:linear-gradient(135deg,#28a745,#1e7e34);
border:none;
padding:12px 30px;
font-size:16px;
font-weight:600;
border-radius:12px;
color:#fff;
transition:0.3s;
}

.btn-save:hover{
transform:translateY(-2px);
box-shadow:0 6px 18px rgba(40,167,69,.25);
}

.info-box{
background:#eef5ff;
border-left:5px solid #007bff;
padding:15px 18px;
border-radius:12px;
margin-bottom:25px;
}

.info-box small{
font-size:14px;
color:#555;
}

.alert{
border-radius:12px;
font-weight:600;
}

@media(max-width:768px){

.custom-card .card-body{
padding:20px;
}

.page-title{
font-size:22px;
}

.btn-save{
width:100%;
}

}

</style>



<div class="container-fluid">

<div class="page-header">

<div>
<h1 class="page-title">
Dynamic Fields Management
</h1>

<p class="text-muted mb-0">
Create custom fields for forms like student, teacher, course, branch etc.
</p>
</div>

</div>

<?php if(isset($success)){ ?>
<div class="alert alert-success">
<?= $success ?>
</div>
<?php } ?>

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="custom-card card">

<div class="card-header">
<h4>
<i class="fa fa-plus-circle mr-2"></i>
Add New Field
</h4>
</div>

<div class="card-body">

<div class="info-box">
<small>
Example:
Student Form → Blood Group, Aadhaar Number, Hostel Name etc.
</small>
</div>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-4">
<label class="form-label">
Field Name
</label>

<input type="text"
       name="field_name"
       class="form-control"
       placeholder="e.g. Blood Group"
       required>
</div>

<div class="col-md-6 mb-4">
<label class="form-label">
Field Type
</label>

<select name="field_type" class="form-select">

<option value="text">Text</option>

<option value="number">Number</option>

<option value="textarea">Textarea</option>

<option value="select">Select Dropdown</option>

<option value="date">Date</option>

</select>
</div>

<div class="col-md-6 mb-4">
<label class="form-label">
Form Type
</label>

<input type="text"
       name="form_type"
       class="form-control"
       placeholder="student, teacher, course"
       required>
</div>

<div class="col-md-6 mb-4">
<label class="form-label">
Source
</label>

<input type="text"
       name="source"
       class="form-control"
       placeholder="course, branch, student">
</div>

<div class="col-12 mb-4">
<label class="form-label">
Options
</label>

<textarea
name="options"
class="form-control"
placeholder="For select fields only (Example: A+,B+,O+,AB+)"></textarea>
</div>

<div class="col-12">
<button type="submit"
        name="add_field"
        class="btn btn-save">

<i class="fa fa-save mr-2"></i>
Save Field

</button>
</div>

</div>

</form>

</div>
</div>

</div>

</div>

</div>



<?php include('footer.php') ?>