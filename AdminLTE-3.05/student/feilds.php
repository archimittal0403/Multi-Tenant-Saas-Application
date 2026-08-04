<?php include('includes/auth.php');
checkRole('student');?>

<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>
<?php
$institute_id=$_SESSION['institute_id'];

?>
<?php
if(isset($_POST['add_field'])){
    $field_name = $_POST['field_name'];
    $form_type=strtolower($_POST['form_type']);
 $field_key = strtolower(str_replace(' ', '_', $field_name));
    $field_type = $_POST['field_type'];
    $options = $_POST['options'];
    $source=$_POST['source'];
    $visiblity=$_POST['visiblity'];
    $showon=$_POST['show'];

    $stmt = $con->prepare("INSERT INTO fields (field_name,field_key,field_type, options, institute_id,form_type,source,visibility,show_on) VALUES (?, ?,?, ?, ?,?,?,?,?)");
    $stmt->bind_param("ssssissis", $field_name, $field_key,$field_type, $options, $institute_id,$form_type,$source,$visiblity,$showon);
    $stmt->execute();

    echo "<div class='alert alert-success'>Field added successfully!</div>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Field</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Add New Field</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Field Name</label>
                    <input type="text" name="field_name" class="form-control" placeholder="e.g. Branch Name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Field Type</label>
                    <select name="field_type" class="form-control">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="textarea">Textarea</option>
                        <option value="select">Select</option>
                          <option value="select">file</option>
                    </select>
                </div>

                 <div class="mb-3">
                    <label class="form-label">Form Type</label>
                    <input type="text" name="form_type" class="form-control" placeholder="course,branch">
                </div>
                <div class="mb-3">
                    <label class="form-label">Options (for select, comma separated)</label>
                    <input type="text" name="options" class="form-control" placeholder="Option1,Option2,Option3">
                </div>
 <div class="mb-3">
                    <label class="form-label">Source</label>
                    <input type="text" name="source" class="form-control" placeholder="course,branch">
                </div>
                 <div class="mb-3">
                    <label class="form-label">visibility</label>
                    <input type="text" name="visiblity" class="form-control" placeholder="0,1">
                </div>
                 <div class="mb-3">
                    <label class="form-label">Show on </label>
                    <input type="text" name="show" class="form-control" placeholder="user-document">
                </div>
                <button type="submit" name="add_field" class="btn btn-success">Add Field</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>