<?php include('includes/auth.php'); ?>
<?php checkRole('admin'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

?>

<style>

body{
    background:#f4f7fc;
}

.qr-card{
    border-radius:20px;
    border:none;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
}

.qr-title{
    font-weight:700;
    color:#111827;
}

.filter-card{
    background:#fff;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.filter-card label{
    font-weight:600;
    margin-bottom:6px;
    display:block;
    color:#374151;
}

.filter-card .form-control{
    height:48px;
    border-radius:12px;
}

.table-card{
    background:#fff;
    border-radius:20px;
    padding:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    overflow-x:auto;
}

#example{
    width:100% !important;
}

#example th{
    background:#f1f5f9;
    white-space:nowrap;
}

#example td{
    vertical-align:middle;
    white-space:nowrap;
}

.qr-btn{
    border-radius:10px;
    padding:8px 16px;
    font-weight:600;
}

@media(max-width:768px){

    .table-card{
        overflow-x:auto;
    }

    #example{
        min-width:850px;
    }

}

</style>


<div class="container-fluid">

<div class="card qr-card">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h3 class="qr-title">Generate Teacher QR Code</h3>
        <p class="text-muted mb-0">
            Filter teachers and generate QR codes
        </p>
    </div>
</div>

<!-- FILTER CARD -->
<div class="filter-card">

<?php if($institute_type=='college'){ ?>

<div class="row">

    <!-- ROLE -->
    <div class="col-lg-3 col-md-6 mb-3">
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
    <div class="col-lg-3 col-md-6 mb-3 academic-only">
        <label>Course</label>

        <select id="st_course" class="form-control">
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
    <div class="col-lg-3 col-md-6 mb-3 academic-only">
        <label>Branch</label>

        <select id="st_branch" class="form-control">
            <option value="">Select Branch</option>
        </select>
    </div>

    <!-- SEMESTER -->
    <div class="col-lg-3 col-md-6 mb-3 academic-only">
        <label>Semester</label>

        <select id="st_semester" class="form-control">
            <option value="">Select Semester</option>
        </select>
    </div>

</div>

<?php } else { ?>

<div class="row">

    <!-- ROLE -->
    <div class="col-lg-4 col-md-6 mb-3">
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
    <div class="col-lg-4 col-md-6 mb-3 academic-only">
        <label>Select Class</label>

        <select name="class" id="filter_class" class="form-control">

            <option value="">Select Class</option>

            <?php

            $args = array(
                'type'=>'class'
            );

            $classes = get_posts($args);

            foreach($classes as $class){

                echo '<option value="'.$class->id.'">
                        '.$class->title.'
                      </option>';
            }

            ?>

        </select>
    </div>

    <!-- SECTION -->
    <div class="col-lg-4 col-md-6 mb-3 academic-only">

        <label>Select Section</label>

        <select name="section" id="filter_section" class="form-control">
            <option value="">Select Section</option>
        </select>

    </div>

</div>

<?php } ?>

</div>

<!-- TABLE -->
<div class="table-card">

<table class="table table-hover table-striped align-middle w-100" id="example">

<thead>

<tr>

<th>SNO</th>
<th>Teacher ID</th>
<th>Teacher Name</th>
<th>Generate QR Code</th>

</tr>

</thead>

</table>

</div>

</div>
</div>

</div>


<?php include('footer.php'); ?>

<script>

function toggleAcademicFilters(){

    let role = $('#staff_role').val();

    if(role == 'teacher' || role == 'sports_teacher'){

        $('.academic-only').show();

    } else {

        $('.academic-only').hide();

        $('#filter_class').val('');
        $('#filter_section').val('');

        $('#st_course').val('');
        $('#st_branch').val('');
        $('#st_semester').val('');
    }
}

toggleAcademicFilters();

$(document).on('change','#staff_role',function(){
    toggleAcademicFilters();
});

</script>

<!-- SCHOOL SECTION -->
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

<!-- COURSE -->
<script>

$('#st_course').on('change',function(){

    let course_id=$(this).val();

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

        error:function(){

            $('#st_branch').html('<option value="">Error loading branches</option>');
        }

    });

});

</script>

<!-- SEMESTER -->
<script>

$('#st_course').on('change',function(){

    let course_id=$(this).val();

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

        error:function(){

            $('#st_semester').html('<option value="">Error loading semester</option>');
        }

    });

});

</script>

<!-- DATATABLE -->
<script>

var table = $('#example').DataTable({

    processing:true,
    serverSide:false,

    ajax:{

        url:'teacher_qr_action.php',
        type:'POST',

        data:function(d){

            d.action      = 'get_teacher_qr';

            d.role        = $('#staff_role').val();

            d.class_id    = $('#filter_class').val();
            d.section_id  = $('#filter_section').val();

            d.course_id   = $('#st_course').val();
            d.branch_id   = $('#st_branch').val();
            d.semester_id = $('#st_semester').val();
        }
    },

    columns:[

        {data:'sno'},
        {data:'teacher_id'},
        {data:'name'},
        {data:'qr'}

    ]

});

$('#staff_role').change(function(){
    table.ajax.reload();
});

$('#filter_class, #filter_section').change(function(){
    table.ajax.reload();
});

$('#st_course, #st_branch, #st_semester').change(function(){
    table.ajax.reload();
});

</script>