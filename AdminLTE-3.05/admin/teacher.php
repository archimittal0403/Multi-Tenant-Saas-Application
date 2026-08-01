<?php
include('includes/auth.php');
checkRole(['admin','super_admin']);
//echo "DEBUG SESSION: institute_id = " . ($_SESSION['institute_id'] ?? 'not set') . "<br>";
if(
    empty($_SESSION['user_id']) || 
    empty($_SESSION['user_type']) || 
    empty($_SESSION['institute_id'])
){
    header("Location: ../login.php");
    exit;
}
include('includes/config.php');
include('includes/functions.php');
require_once('includes/dynamic-form.php');
?>
<?php
if(isset($_GET['delete_teacher'])){

    $delete_id = $_GET['delete_teacher'];

    mysqli_query($con,"DELETE FROM accounts WHERE id='$delete_id'");

    mysqli_query($con,"DELETE FROM usermeta WHERE user_id='$delete_id'");

    $_SESSION['success_msg']="Teacher deleted successfully";

    header("Location: teacher.php?user=teacher");

    exit;
}
$classes = get_posts([
    'type' => 'class'
]);

$courses = get_posts([
    'type' => 'course'
]);

$branches = get_posts([
    'type' => 'branch'
]);

$semester = get_posts([
    'type' => 'semester'
]);
?>

<style>
body{
    background:#f4f7fc;
}

.content-wrapper{
    background:#f1f5f9 !important;
}

.content{
    padding:20px;
}

.container-fluid{
    max-width:100% !important;
}

.content-header{
    width:100%;
}

html,
body{
    min-height:100%;
    height:auto;
}

footer{
    width:100%;
    clear:both;
    margin-left:0 !important;
}

/* ===========================
   FILTER CARD
=========================== */

.filter-card{
    width:100%;
    background:#fff;
    border:1px solid #e2e8f0;
    border-radius:24px;
    padding:28px;
    margin-bottom:30px;
    box-shadow:0 8px 30px rgba(0,0,0,.06);
}

.filter-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.filter-header h4{
    margin:0;
    font-size:20px;
    font-weight:700;
}

.filter-header small{
    color:#64748b;
}

.filter-card label{
    font-weight:600;
    margin-bottom:6px;
}

.filter-card .form-control{
    height:46px;
    border-radius:10px;
}

.filter-actions{
    display:flex;
    gap:10px;
    align-items:center;
}

/* ===========================
   TOP ACTIONS
=========================== */

.top-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:12px;
    flex-wrap:wrap;
}

.top-actions .btn{
    min-width:150px;
    border-radius:10px;
}

/* ===========================
   TABLE CARD
=========================== */

.table-card{
    width:100%;
    background:#fff;
    border-radius:20px;
    padding:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.05);
    overflow-x:auto;
}

/* ===========================
   TABLE RESPONSIVE
=========================== */

.table-responsive{
    width:100%;
    overflow-x:auto;
    overflow-y:hidden;
    -webkit-overflow-scrolling:touch;
}

/* ===========================
   DATATABLE
=========================== */

#example{
    width:100% !important;
    min-width:1100px;
}

#example th,
#example td{
    white-space:nowrap;
    vertical-align:middle;
}

#example thead th{
    background:#f1f5f9;
    font-weight:700;
    border:none;
}

#example th:last-child,
#example td:last-child{
    min-width:170px;
    text-align:center;
}

/* ===========================
   ACTION BUTTONS
=========================== */

.action-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:8px 12px;
    margin:2px;
    border:none;
    border-radius:8px;
    font-size:13px;
    transition:.3s;
}

.action-btn:hover{
    transform:translateY(-2px);
}

.assign-btn{
    background:#22c55e;
    color:#fff;
}

.delete-btn{
    background:#ef4444;
    color:#fff;
}

/* ===========================
   DATATABLE SEARCH
=========================== */

.dataTables_wrapper .dataTables_filter input{
    border-radius:8px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button{
    border-radius:8px !important;
}

/* ===========================
   RESPONSIVE
=========================== */

@media(max-width:992px){

    .content{
        padding:15px;
    }

    .filter-card,
    .table-card{
        padding:18px;
    }

    #example{
        min-width:950px;
    }

}

@media(max-width:768px){

    .content{
        padding:10px;
    }

    .top-actions{
        flex-direction:column;
    }

    .top-actions .btn{
        width:100%;
    }

    .filter-card,
    .table-card{
        padding:15px;
    }

    #example{
        min-width:900px;
    }

}

@media(max-width:576px){

    .filter-card{
        padding:12px;
    }

    .table-card{
        padding:10px;
    }

    .filter-header{
        flex-direction:column;
        align-items:flex-start;
    }

    #example{
        min-width:850px;
    }

}
</style>
<?php
$institute_id = $_SESSION['institute_id'];
      $institute_type=$_SESSION['system_type'];
      $institute_code=$_SESSION['institute_code'];
      ?>

<?php

if(isset($_POST['type']) && $_POST['type']=='teacher' && isset($_POST['email']) && !empty($_POST['email'])){
}

 $user= $_GET['user'] ?? '';
$role = $_POST['role'] ?? '';
$department = $_POST['department'] ?? '';
$teacher_id = $_GET['teacher_id'] ?? '';
?>

<?php include('header.php')?>
<?php include('sidebar.php')?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dahboard</title>
</head>
<body>
<div class="container-fluid px-3 px-md-4">
<div class="row align-items-center mb-3">

<div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
   <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
            <h2 class="m-0 text-dark"> Manage Accounts :-</h2>
      <a href="teacher.php?user=<?=$user?>&action=add-new"
   class="btn btn-primary btn-sm shadow-sm ml-2">
   <i class="fa fa-plus"></i> Add Teacher
</a>

<?php if($_SESSION['user_type'] == 'super_admin'){ ?>

<a href="feilds.php" class="btn btn-dark btn-custom">
    <i class="fa fa-layer-group"></i> Dynamic Fields
</a>

<?php } ?>
</div>
          </div><!-- /.col -->
</div>
</div>
            <?php
          if(isset($_SESSION['success_msg'])){?>
            <div class="col-12">
            <small class="text-success" style="font-size:19px mt-3"><?=$_SESSION['success_msg']?></small>
            </div>
          <?php
          unset($_SESSION['success_msg']);
          }
          ?>
          <section class="content">
            <div class="container-fluid">
                <?php
                if(isset($_GET['action'])){
                  $class_id=$_GET['class'] ?? '';
                  $section_id=$_GET['section'] ?? '';
?>
                <div class="card">
                    <div class="card-body" id="form-container">
<?php  if($_GET['action']=='add-new'){?>
<div class="content">
<div class="container-fluid">

<form id="teacher" method="post" enctype="multipart/form-data">
<input type="hidden" name="type" value="teacher">

<!-- ================= PERSONAL INFO ================= -->
<div class="card">
<div class="card-header"><b>Personal Information</b></div>
<div class="card-body">
<div class="row">

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>Name</label>
<input type="text" name="name" class="form-control">
</div>

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>Email</label>
<input type="text" name="email" class="form-control">
</div>
<div class="col-xl-4 col-lg-4 col-md-6 col-12">

<label>Teacher Image</label>

<div id="uploadBox" class="upload-box">
<input type="file"
       name="th_image"
       id="th_image"
       hidden
       accept="image/*">

    <div id="uploadContent">
        <i class="fa fa-cloud-upload"></i>
        <p>Drag & Drop Image Here</p>
        <small>or click to browse</small>
    </div>

    <img id="previewImage" src="" style="display:none;">
</div>

</div>
<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>Mobile</label>
<input type="text" name="mobile" class="form-control">
</div>

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>DOB</label>
<input type="date" name="dob" class="form-control">
</div>

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>Gender</label>
<input type="text" name="gender" class="form-control">
</div>

</div>
</div>
</div>

<!-- ================= ACADEMIC ================= -->
<div class="card">
<div class="card-header"><b>Academic Information</b></div>
<div class="card-body">
<div class="row">

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>Qualification</label>
<input type="text" name="qualification" class="form-control">
</div>

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>Experience</label>
<input type="text" name="experience" class="form-control">
</div>

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
<label>Date of Joining</label>
<input type="date" name="doj" class="form-control">
</div>

<div class="col-xl-4 col-lg-4 col-md-6 col-12">
   <label>Role *</label>
   <select name="role" id="staff_role" class="form-control" required>
      <option value="">Select Role</option>
      <option value="teacher">Teacher</option>
      <option value="sports_teacher">Sports Teacher</option>
      <option value="librarian">Librarian</option>
      <option value="accountant">Accountant</option>
      <option value="receptionist">Receptionist</option>
   </select>
</div>
</div>
</div>
</div>

<!-- ================= Admission ================= -->
<div class="card mb-3">
<div class="card-header"><b><h4>Admission Details</h4></b></div>
<div class="card-body">
<div class="row">

<?php if($institute_type == 'school'){ ?>

    <!-- SCHOOL FIELDS -->

   <div class="col-xl-4 col-lg-4 col-md-6 col-12 form-group">
                    <label>Class</label>

                    <select name="class_name" id="filter_class" class="form-control">
                        <option value="">Select Class</option>

                        <?php foreach($classes as $class){ ?>
                            <option value="<?= $class->id ?>">
                                <?= $class->title ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

               <div class="col-md-4 form-group">
                    <label>Section</label>

                    <select name="section_name" id="filter_section" class="form-control">
                        <option value="">Select Section</option>
                    </select>
                </div>



<?php } else { ?>

    <!-- COLLEGE FIELDS -->

    <div class="col-md-4 form-group">
        <label>Course</label>

        <select name="course_name" id="st_course" class="form-control">
            <option value="">Select Course</option>

            <?php foreach ($courses as $course){ ?>
                <option value="<?= $course->id ?>">
                    <?= $course->title ?>
                </option>
            <?php } ?>

        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Branch</label>

        <select name="branch_name" id="st_branch" class="form-control">
            <option value="">Select Branch</option>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Session</label>

        <input type="text"
               name="session"
               placeholder="eg: 2026-2030"
               class="form-control">
    </div>
    <!-- <div class="col-md-4 form-group">
        <label for="">Semester</label>
               <input type="text"
               name="semester"
               placeholder="eg: 1,2,3,4,5"
               class="form-control">
    </div> -->

<?php } ?>

<div class="col-md-4 form-group">
    <label>Date of Admission</label>

    <input type="date" name="doa" class="form-control">
</div>

</div>
</div>
</div>
<!-- ================= DYNAMIC FIELDS ================= -->
<div class="card">
<div class="card-header"><b>Extra Fields</b></div>
<div class="card-body">
<div class="row">
<?php render_dynamic_form('teacher',$teacher_id); ?>
</div>
</div>
</div>

<!-- ================= BANK ================= -->
<div class="card">
<div class="card-header"><b>Bank Details</b></div>
<div class="card-body">
<div class="row">

<div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
<label>Salary</label>
<input type="text" name="salary" class="form-control">
</div>

<div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
<label>Bank</label>
<input type="text" name="bank" class="form-control">
</div>

<div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
<label>Account No</label>
<input type="text" name="aco" class="form-control">
</div>

<div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
<label>IFSC</label>
<input type="text" name="ifsc" class="form-control">
</div>

</div>
</div>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
</form>

</div>
</div>


                    </div>
                </div>
                <?php } ?>
                <?php } else { ?>
        
          
                <form action="" method="GET">

<div class="filter-card">

    <!-- HEADER -->
    <div class="filter-header">
        <div>
            <h4>
                🎓 Academic Filters 
            </h4>
            <small>
                Filter teachers by course, branch, semester & role
            </small>
        </div>

      
    </div>


  

       <?php
             $class_id = $_GET['class'] ?? '';
$section_id = $_GET['section'] ?? '';
$course_id=$_GET['course'] ?? '';
$branch_id=$_GET['branch'] ?? '';
$semester_id=$_GET['semester'] ?? '';
              ?>
                    <?php
               if($institute_type=='college'){?>
           
 <!-- FILTER GRID -->
    <div class="row">

        <!-- ROLE -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
            <label>Staff Role</label>
            <select name="staff_role" id="staff_role" class="form-control">
                <option value="">All Roles</option>
                <option value="teacher">Teacher</option>
                <option value="sports_teacher">Sports Teacher</option>
                <option value="accountant">Accountant</option>
                <option value="librarian">Librarian</option>
                <option value="receptionist">Receptionist</option>
            </select>
        </div>

        <!-- COURSE -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3 academic-only">
            <label>Course</label>
           <select name="st_course" id="st_course" class="form-control">

<option value="">Select Course</option>

<?php

$courses = get_posts(['type'=>'course']);

foreach($courses as $course){

?>

<option value="<?= $course->id ?>">

<?= $course->title ?>

</option>

<?php } ?>

</select>
        </div>

        <!-- BRANCH -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3 academic-only">
            <label>Branch</label>
            <select name="st_branch" id="st_branch" class="form-control">
                <option value="">Select Branch</option>
            </select>
        </div>

        <!-- SEMESTER -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3 academic-only">
            <label>Semester</label>
            <select name="st_semester" id="st_semester" class="form-control">
                <option value="">Select Semester</option>
            </select>
        </div>

</div>
      <!-- <div class=" justify-content-end">
     <button type="submit" class="btn btn-danger" >Apply</button>

      </div> -->
      <?php }
    else{
?>

  <div class="row">

        <!-- ROLE -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
     
            <label>Select Role</label>
            <select name="staff_role" id="staff_role" class="form-control">
                <option value="">All Roles</option>
                <option value="teacher">Teacher</option>
                <option value="sports_teacher">Sports Teacher</option>
                <option value="accountant">Accountant</option>
                <option value="receptionist">Receptionist</option>
                <option value="librarian">Librarian</option>
            </select>
      
    </div>

    <!-- CLASS -->
  
<div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3 academic-only">
    
            <label>Select Class</label>

            <select name="class" id="filter_class" class="form-control">
                <option value="">Select Class</option>

                <?php
                $args = array(
                    'type'=>'class'
                );

                $s_class_id = get_posts($args);

                foreach($s_class_id as $key => $s_class){

                    $selected = ($class_id==$s_class->id) ? 'selected' : '';

                    echo '<option value="'.$s_class->id.'" '.$selected.'>
                            '.$s_class->title.'
                          </option>';
                }
                ?>

            </select>
      
    </div>

    <!-- SECTION -->
<!-- CLASS -->
<div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3 academic-only">
    
            <label>Select Section</label>

       <select name="section" id="filter_section" class="form-control">
                <option value="">Select Section</option>
            </select>
        </div>
  

    <!-- BUTTON -->
    <!-- <div class="col-lg-3 col-md-6">
        <button type="button"
                class="btn btn-primary btn-block"
                onclick="table.ajax.reload();">

            <i class="fa fa-search"></i> Filter
        </button>
    </div> -->

</div>

<?php
}?>
  </div>


</form>

            <form action="post">
           <div class="table-card p-2">
    <div class="table-responsive">
    <table id="example" class="table table-hover table-striped">
                  <thead>
                    <?php
                    $show_page='teacher';
                    $table=mysqli_query($con,"SELECT * FROM `fields` WHERE institute_id='$institute_id' AND form_type='teacher' AND visibility=1 AND show_on='$show_page'");
                    ?>
                    <tr>
                      <th>SNO</th>
                      <th>
Teacher ID
                      </th>
                      <th>Teacher Photo</th>
                      <th>Teacher Name</th>
                      <?php
                      if($institute_type=='college'){
                       echo  "<th>Department</th>";
                       
                      }
                      ?>
                    <?php 
                    $field_array=[];
                    while($table_fetch=mysqli_fetch_assoc($table)){
                      $field_array[]=$table_fetch;
                      echo "<th>{$table_fetch['field_name']}</th>";
                    }
                    ?>
                    <th>Action</th>
                    <th>View Subject</th>
                    </tr>
                  </thead>
                </table>
</div>
              </div>
            </form>
</div>

</div>
             <?php  }?>
           
          </section>
</div>
</div>
            <?php include('footer.php')?>
<script>

$(document).ready(function(){

    // click open file
    $('#uploadBox').on('click', function(){
        $('#th_image').click();
    });

    // preview image
    $('#th_image').on('change', function(e){

        let file = e.target.files[0];

        if(!file) return;

        // only image check
        if(!file.type.startsWith('image/')){
            alert('Only image allowed');
            return;
        }

        let reader = new FileReader();

        reader.onload = function(event){

            $('#previewImage')
                .attr('src', event.target.result)
                .show();

            $('#uploadContent').hide();
        }

        reader.readAsDataURL(file);
    });

});

</script>

</body>
</html>


<script>

function toggleAcademicFilters(){

    let role = $('#staff_role').val();

    if(role == 'teacher' || role == 'sports_teacher'){

        $('.academic-only').show();

    } else {

        $('.academic-only').hide();

        // reset filters
        $('#filter_class').val('');
        $('#filter_section').val('');

        $('#st_course').val('');
        $('#st_branch').val('');
        $('#st_semester').val('');
    }
}

// on load
toggleAcademicFilters();

// on change
$(document).on('change','#staff_role',function(){
    toggleAcademicFilters();
});

</script>
<script>
jQuery(document).ready(function(){

  jQuery('#teacher').on('submit', function(e){
    e.preventDefault();

    console.log("FORM WORKING"); // 🔥 DEBUG

    var formData = new FormData(this);
    console.log([...formData]); // 🔥 DEBUG

    jQuery.ajax({
      type: 'POST',
      url: 'teacher_action.php?user=teacher',
      data: formData,
      contentType: false,
      processData: false,
      dataType: 'json',
 success: function(response){   // ✅ yahi hona chahiye
      console.log(response);

      if(response.success){
        alert("Saved successfully ✅");

        // 🔥 Redirect
        window.location.href = "teacher.php?user=teacher";
      } else {
        alert(response.message || 'Something went wrong');
      }
    },

      error: function(xhr){
        console.log(xhr.responseText);
        alert('AJAX failed – check console');
      }
    });

  });

});
</script>
<script>
$('#filter_class').on('change', function () {
    let class_id = $(this).val();

    $('#filter_section').html('<option>Loading...</option>');

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'get_sections',
            class_id: class_id
        },
      success: function (res) {
    if(res.status){
        $('#filter_section').html(res.options);
    } else {
        $('#filter_section').html('<option value="">No sections found</option>');
    }
},
error: function () {
    $('#filter_section').html('<option value="">Error loading sections</option>');
}

    });
});

</script>
<script>
  $('#st_course').on('change',function(){
    let course_id=$(this).val();
    console.log(course_id);
    $('#st_branch').html('<option>Loading...</option>');
$.ajax({
    url:'ajax.php',
    type:'POST',
    dataType:'json',
    data:{
        action:'get_branch',
        course_id:course_id
    },
    success:function(res){
        if(res.status){
            $('#st_branch').html(res.options);
        } else {
            $('#st_branch').html('<option value="">No branch found</option>');
        }
    },
    error: function(xhr){
        console.log(xhr.responseText); // debug
        $('#st_branch').html('<option value="">Error loading branches</option>');
    }
});
  });
</script>
<script>
$('#st_course').on('change',function(){
    let course_id=$(this).val();
    console.log(course_id);
    $('#st_semester').html('<option>Loading...</option>');
$.ajax({
    url:'ajax.php',
    type:'POST',
    dataType:'json',
    data:{
        action:'get_semester',
        course_id:course_id
    },
    success:function(res){
        if(res.status){
            $('#st_semester').html(res.options);
        } else {
            $('#st_semester').html('<option value="">No semester found</option>');
        }
    },
    error: function(xhr){
        console.log(xhr.responseText); // debug
        $('#st_branch').html('<option value="">Error loading branches</option>');
    }
});
  });

</script>
<script>
  var dynamicFeilds=<?php echo json_encode($field_array);?>;

</script>
<script>
  var cols=[];
  cols.unshift({data:'sno'});
  cols.push({data:'roll_no'});
  cols.push({data:'photo'});
  cols.push({data:'name'});
<?php if($institute_type=='college'){ ?>
cols.push({data:'department'});
<?php } ?>
dynamicFeilds.forEach(function(field){
  cols.push({data:field.field_key});
});
cols.push({
  data: 'action',
  orderable: false,
  searchable: false
});
cols.push({
  data: 'view_subject',
  orderable: false,
  searchable: false
});
var table = $('#example').DataTable({  
    responsive: {
    details: false
},
scrollX: true,
scrollCollapse: true,
autoWidth: false, // ❌ OFF
    ajax: {
        url: "teacher_action.php",
        type: "POST",
        data: function (d) {

            d.action = "get_teacher_details";
            d.role = $('#staff_role').val();
            d.class_id = $('#filter_class').val();
            d.section_id = $('#filter_section').val();
            d.course_id = $('#st_course').val();
            d.branch_id = $('#st_branch').val();
            d.semester_id = $('#st_semester').val();
        }
    },

    columns: cols,

    language: {
        emptyTable: "No data available"
    },

    initComplete: function () {
        // force recalculation (VERY IMPORTANT for scroll bug fix)
        this.api().columns.adjust();
    }
    
});
$('#filter_class, #filter_section').change(function(){
   table.ajax.reload();
});
$('#st_course, #st_branch').change(function(){
  table.ajax.reload();
});
$('#st_course, #st_semester').change(function(){
  table.ajax.reload();
});
$('#staff_role').change(function(){
   table.ajax.reload();
});

console.log(dynamicFeilds);
</script>