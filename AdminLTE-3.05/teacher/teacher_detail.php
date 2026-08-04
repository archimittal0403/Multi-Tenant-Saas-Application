<?php include('includes/auth.php'); ?>
<?php checkRole('teacher'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>

<?php

// =====================================
// UPDATE PROFILE
// =====================================

if(isset($_POST['edit'])){

    $teacher_id = $_SESSION['user_id'];

    foreach($_POST as $key => $value){

        if($key == 'edit'){
            continue;
        }

        update_usermeta($teacher_id,$key,$value);
    }

    header("Location: teacher_detail.php");
    exit;
}

?>

<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$teacher_id = $_SESSION['user_id'];
$college_id = $_SESSION['institute_id'];
$system_type=$_SESSION['system_type'];
$query = $con->prepare("SELECT * FROM accounts WHERE id=? AND institute_id=?");
$query->bind_param("ii",$teacher_id,$college_id);
$query->execute();

$result = $query->get_result();

if($result->num_rows == 0){
    echo "Teacher not found";
    exit;
}

$teacher = $result->fetch_assoc();

$is_edit = isset($_GET['action']) && $_GET['action'] == 'edit_mode';

// =====================================
// USERMETA
// =====================================

$mobile        = get_usermeta($teacher_id,'mobile');
$dob           = get_usermeta($teacher_id,'dob');
$gender        = get_usermeta($teacher_id,'gender');
$qualification = get_usermeta($teacher_id,'qualification');
$experience    = get_usermeta($teacher_id,'experience');
$doj           = get_usermeta($teacher_id,'doj');
$blood_group   = get_usermeta($teacher_id,'blood_group');
$age           = get_usermeta($teacher_id,'age');

$course_id     = get_usermeta($teacher_id,'course_name');
$branch_id     = get_usermeta($teacher_id,'branch_name');

$semester      = get_usermeta($teacher_id,'semester');
$session=get_usermeta($teacher_id,'session');
$salary        = get_usermeta($teacher_id,'salary');
$bank          = get_usermeta($teacher_id,'bank');
$aco           = get_usermeta($teacher_id,'aco');
$ifsc          = get_usermeta($teacher_id,'ifsc');

$teacher_image = get_usermeta($teacher_id,'th_image');

// =====================================
// COURSE NAME
// =====================================

$course_name = '';

if(!empty($course_id)){

    $course = get_post([
        'id' => $course_id,
        'type' => 'course'
    ]);

    $course_name = $course->title ?? '';
}

// =====================================
// BRANCH NAME
// =====================================

$branch_name = '';

if(!empty($branch_id)){

    $branch = get_post([
        'id' => $branch_id,
        'type' => 'branch'
    ]);

    $branch_name = $branch->title ?? '';
}

?>

<style>

.content-wrapper{
    background:#f4f6f9;
}

.teacher-profile-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 4px 20px rgba(0,0,0,0.08);
}

.teacher-cover{
    height:130px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
}

.teacher-image{
    width:120px;
    height:120px;
    object-fit:cover;
    border-radius:50%;
    border:5px solid #fff;
    margin-top:-60px;
    background:#fff;
}

.teacher-name{
    font-size:24px;
    font-weight:700;
    color:#111827;
}

.teacher-role{
    color:#6b7280;
    font-size:15px;
}

.info-badge{
    display:inline-block;
    padding:6px 14px;
    border-radius:30px;
    background:#eff6ff;
    color:#2563eb;
    font-size:13px;
    font-weight:600;
}

.profile-info li{
    border:none !important;
    border-bottom:1px solid #f1f1f1 !important;
    padding:15px 10px;
}

.profile-info li:last-child{
    border-bottom:none !important;
}

.custom-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 4px 18px rgba(0,0,0,0.07);
}

.custom-card .card-header{
    background:#2563eb;
    color:#fff;
    border:none;
    padding:16px 20px;
}

.custom-card .card-title{
    font-size:18px;
    font-weight:600;
}

.custom-card .list-group-item{
    border:none;
    border-bottom:1px solid #f1f1f1;
    padding:18px 20px;
}

.custom-card .list-group-item:last-child{
    border-bottom:none;
}

.form-control{
    border-radius:10px;
    height:45px;
}

.edit-btn{
    border-radius:10px;
    padding:10px 20px;
    font-weight:600;
}

.update-btn{
    border-radius:10px;
    padding:12px 28px;
    font-weight:600;
}

.section-label{
    font-weight:600;
    color:#111827;
}

.section-value{
    color:#4b5563;
}

</style>

<div class="content-header">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

            <div>

                <h2 class="font-weight-bold text-dark">
                    Teacher Profile
                </h2>

                <p class="text-muted mb-0">
                    Manage your profile details
                </p>

            </div>

            <div>

                <a href="teacher_detail.php?action=edit_mode"
                   class="btn btn-primary edit-btn">

                    <i class="fas fa-edit"></i>
                    Edit Profile

                </a>
       <a href="document.php?user_id=<?php echo $teacher_id; ?>"
   class="btn btn-primary btn-sm mx-4">+ Add document</a>
            </div>

        </div>

    </div>

</div>

<section class="content">

<form action="" method="POST">

<div class="container-fluid">

<div class="row">

<!-- ===================================== -->
<!-- LEFT CARD -->
<!-- ===================================== -->

<div class="col-md-4">

<div class="card teacher-profile-card">

<div class="teacher-cover"></div>

<div class="card-body text-center">

<?php if(!empty($teacher_image)){ ?>

<img class="teacher-image"
     src="../admin/uploads/teacher_photo/<?php echo $teacher_image; ?>"
     alt="Teacher Image">

<?php } else { ?>

<img class="teacher-image"
     src="uploads/default.png"
     alt="Teacher Image">

<?php } ?>

<h3 class="teacher-name mt-3">
    <?php echo ucfirst($teacher['Name']); ?>
</h3>

<p class="teacher-role">
    Teacher
</p>

<span class="info-badge">
    ID : <?php echo $teacher['roll_no']; ?>
</span>

<hr>

<ul class="list-group profile-info text-left">

<li class="list-group-item d-flex justify-content-between align-items-center">

<b>Email</b>

<span>
    <?php echo $teacher['email']; ?>
</span>

</li>

<li class="list-group-item">

<b>Mobile</b>

<?php if($is_edit){ ?>

<input type="text"
       name="mobile"
       value="<?php echo $mobile; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right">
    <?php echo $mobile; ?>
</span>

<?php } ?>

</li>

<li class="list-group-item">

<b>DOB</b>

<?php if($is_edit){ ?>

<input type="date"
       name="dob"
       value="<?php echo $dob; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right">
    <?php echo $dob; ?>
</span>

<?php } ?>

</li>

<li class="list-group-item">

<b>Gender</b>

<?php if($is_edit){ ?>

<input type="text"
       name="gender"
       value="<?php echo $gender; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right">
    <?php echo ucfirst($gender); ?>
</span>

<?php } ?>

</li>

<?php

$field_query = mysqli_query($con,"
    SELECT * 
    FROM fields
    WHERE institute_id='$college_id'
    AND form_type='teacher'
    AND show_on LIKE '%teacher-details%'
    AND visibility='1'
    ORDER BY id ASC
");

while($field = mysqli_fetch_assoc($field_query)){

    $field_name = $field['field_name'];
    $field_key  = $field['field_key'];
    $field_key=strtolower($field_key);
    $field_type = $field['field_type'];
    $options    = $field['options'];

$value = get_usermeta($teacher_id,$field_key);

// 👇 only for relational fields (ID based)
if(in_array($field_key, ['course_name','branch_name','semester','session','class_name','section'])){

    if(!empty($value)){

        $typeMap = [
            'course_name' => 'course',
            'branch_name' => 'branch',
            'semester'    => 'semester',
            'session'     => 'session',
            'class_name'  =>'class',
            'section'      =>'section'
        ];

        $post = get_post([
            'id'   => $value,
            'type' => $typeMap[$field_key] ?? ''
        ]);

        if($post){
            $value = $post->title; // 🔥 B.Tech, CSE, 2022-2026
        }
    }
}

?>

<li class="list-group-item">

<b>
    <?php echo $field_name; ?>
</b>

<?php if($is_edit){ ?>

    <?php if($field_type == 'select'){ ?>

        <?php $option_array = explode(',',$options); ?>

        <select name="<?php echo $field_key; ?>"
                class="form-control mt-2">

            <option value="">
                Select <?php echo $field_name; ?>
            </option>

            <?php foreach($option_array as $opt){ ?>

            <option value="<?php echo $opt; ?>"
            <?php if($value == $opt) echo 'selected'; ?>>

                <?php echo $opt; ?>

            </option>

            <?php } ?>

        </select>

    <?php } elseif($field_type == 'textarea'){ ?>

        <textarea
            name="<?php echo $field_key; ?>"
            class="form-control mt-2"><?php echo $value; ?></textarea>

    <?php } else { ?>

        <input
            type="<?php echo $field_type; ?>"
            name="<?php echo $field_key; ?>"
            value="<?php echo $value; ?>"
            class="form-control mt-2">

    <?php } ?>

<?php } else { ?>

<span class="float-right">
    <?php echo !empty($value) ? $value : '-'; ?>
</span>

<?php } ?>

</li>

<?php } ?>

<li class="list-group-item">

<b>Age</b>

<?php if($is_edit){ ?>

<input type="text"
       name="age"
       value="<?php echo $age; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right">
    <?php echo $age; ?>
</span>

<?php } ?>

</li>

</ul>

</div>

</div>

</div>

<!-- ===================================== -->
<!-- RIGHT SIDE -->
<!-- ===================================== -->

<div class="col-md-8">

<!-- Academic Details -->

<div class="card custom-card mb-4">

<div class="card-header">
    <h3 class="card-title">
        Academic Details
    </h3>
</div>

<div class="card-body p-0">

<ul class="list-group">

<li class="list-group-item">

<b class="section-label">Qualification</b>

<?php if($is_edit){ ?>

<input type="text"
       name="qualification"
       value="<?php echo $qualification; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right section-value">
    <?php echo ucfirst($qualification); ?>
</span>

<?php } ?>

</li>

<li class="list-group-item">

<b class="section-label">Experience</b>

<?php if($is_edit){ ?>

<input type="text"
       name="experience"
       value="<?php echo $experience; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right section-value">
    <?php echo $experience; ?> Years
</span>

<?php } ?>

</li>
<?php if($system_type == 'college'){ ?>

<li class="list-group-item">
    <b>Course</b>
    <span class="float-right">
        <?php echo $course_name ?: '-'; ?>
    </span>
</li>

<li class="list-group-item">
    <b>Branch</b>
    <span class="float-right">
        <?php echo $branch_name ?: '-'; ?>
    </span>
</li>

<li class="list-group-item">
    <b>Semester</b>
    <span class="float-right">
        <?php echo $semester ?: '-'; ?>
    </span>
</li>

<li class="list-group-item">
    <b>Session</b>
    <span class="float-right">
        <?php echo $session ?: '-'; ?>
    </span>
</li>

<?php } else { ?>

<?php
$class_id   = get_usermeta($teacher_id,'class_name');
$section_id = get_usermeta($teacher_id,'section_name');

$class_name = '-';
$section_name = '-';

if(!empty($class_id)){
    $class = get_post([
        'id' => $class_id,
        'type' => 'class'
    ]);

    if($class){
        $class_name = $class->title;
    }
}

if(!empty($section_id)){
    $section = get_post([
        'id' => $section_id,
        'type' => 'section'
    ]);

    if($section){
        $section_name = $section->title;
    }
}
$session=get_usermeta($user_id,'session');
// $session_name = '-';

// if(!empty($session)){
//     $ses = get_post([
//         'id' => $session,
//         'type' => 'session'
//     ]);

//     if($ses){
//         $session_name = $ses->title;
//     }
// }
?>

<li class="list-group-item">
    <b>Class</b>
    <span class="float-right">
        <?php echo $class_name; ?>
    </span>
</li>

<li class="list-group-item">
    <b>Section</b>
    <span class="float-right">
        <?php echo $section_name; ?>
    </span>
</li>

<li class="list-group-item">
    <b>Session</b>
    <span class="float-right">
        <?php echo $session; ?>
    </span>
</li>

<?php } ?>
<li class="list-group-item">

<b class="section-label">Date Of Joining</b>

<?php if($is_edit){ ?>

<input type="date"
       name="doj"
       value="<?php echo $doj; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right section-value">
    <?php echo $doj; ?>
</span>

<?php } ?>

</li>

<?php

$field_query = mysqli_query($con,"
    SELECT * 
    FROM fields
    WHERE institute_id='$college_id'
    AND form_type='teacher'
    AND show_on LIKE '%teacher-academic%'
    AND visibility='1'
    ORDER BY id ASC
");

while($field = mysqli_fetch_assoc($field_query)){

    $field_name = $field['field_name'];
    $field_key  = $field['field_key'];
    $field_type = $field['field_type'];
    $options    = $field['options'];
$value = get_usermeta($teacher_id,$field_key);

// ✅ UNIVERSAL RELATIONAL MAPPING
$typeMap = [
    'course_name' => 'course',
    'branch_name' => 'branch',
    'semester'    => 'semester',
    'session'     => 'session',
    'class_name'  => 'class',
    'section'     => 'section'
];

if(isset($typeMap[$field_key]) && !empty($value) && is_numeric($value)){

    $post = get_post([
        'id'   => (int)$value,
        'type' => $typeMap[$field_key]
    ]);

    if(!empty($post) && !empty($post->title)){
        $value = $post->title;
    }
}


?>

<li class="list-group-item">

<b>
    <?php echo $field_name; ?>
</b>

<?php if($is_edit){ ?>

    <?php if($field_type == 'select'){ ?>

        <?php $option_array = explode(',',$options); ?>

        <select name="<?php echo $field_key; ?>"
                class="form-control mt-2">

            <option value="">
                Select <?php echo $field_name; ?>
            </option>

            <?php foreach($option_array as $opt){ ?>

            <option value="<?php echo $opt; ?>"
            <?php if($value == $opt) echo 'selected'; ?>>

                <?php echo $opt; ?>

            </option>

            <?php } ?>

        </select>

    <?php } elseif($field_type == 'textarea'){ ?>

        <textarea
            name="<?php echo $field_key; ?>"
            class="form-control mt-2"><?php echo $value; ?></textarea>

    <?php } else { ?>

        <input
            type="<?php echo $field_type; ?>"
            name="<?php echo $field_key; ?>"
            value="<?php echo $value; ?>"
            class="form-control mt-2">

    <?php } ?>

<?php } else { ?>

<span class="float-right">
    <?php echo !empty($value) ? $value : '-'; ?>
</span>

<?php } ?>

</li>

<?php } ?>


</ul>

</div>

</div>

<!-- Bank Details -->

<div class="card custom-card">

<div class="card-header">
    <h3 class="card-title">
        Bank Details
    </h3>
</div>

<div class="card-body p-0">

<ul class="list-group">

<li class="list-group-item">

<b class="section-label">Salary</b>

<?php if($is_edit){ ?>

<input type="text"
       name="salary"
       value="<?php echo $salary; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right section-value">
    ₹<?php echo $salary; ?>
</span>

<?php } ?>

</li>

<li class="list-group-item">

<b class="section-label">Bank Name</b>

<?php if($is_edit){ ?>

<input type="text"
       name="bank"
       value="<?php echo $bank; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right section-value">
    <?php echo strtoupper($bank); ?>
</span>

<?php } ?>

</li>

<li class="list-group-item">

<b class="section-label">Account Number</b>

<?php if($is_edit){ ?>

<input type="text"
       name="aco"
       value="<?php echo $aco; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right section-value">
    <?php echo $aco; ?>
</span>

<?php } ?>

</li>

<li class="list-group-item">

<b class="section-label">IFSC Code</b>

<?php if($is_edit){ ?>

<input type="text"
       name="ifsc"
       value="<?php echo $ifsc; ?>"
       class="form-control mt-2">

<?php } else { ?>

<span class="float-right section-value">
    <?php echo strtoupper($ifsc); ?>
</span>

<?php } ?>

</li>

</ul>

<?php if($is_edit){ ?>

<div class="p-4">

<button type="submit"
        name="edit"
        class="btn btn-primary update-btn">

    <i class="fas fa-save"></i>
    Update Profile

</button>

</div>

<?php } ?>

</div>

</div>

</div>

</div>

</div>

</form>

</section>

<?php include('footer.php'); ?>