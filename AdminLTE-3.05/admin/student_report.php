
<?php
include('includes/auth.php');
checkRole('admin');

include('includes/config.php');
include('includes/functions.php');

include('header.php');
include('sidebar.php');

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];
?>

<style>

.chart-card{
    position:relative;
    height:350px;
    width:100%;
    overflow:hidden;
}

.chart-card canvas{
    width:100% !important;
    height:100% !important;
}

.table-responsive{
    overflow-x:auto;
}

.card{
    border-radius:10px;
}

.card-header{
    font-weight:600;
}

@media(max-width:768px){

    .chart-card{
        height:280px;
    }

    .btn{
        width:100%;
    }

    table{
        min-width:700px;
    }
}

</style>

<div class="container-fluid">

<!-- ================= FILTER ================= -->

<div class="card shadow mb-3">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">🔍 Filteration</h5>
    </div>

    <div class="card-body">

        <form>

            <div class="row">

                <?php if($institute_type == 'college'){ ?>

                <!-- COURSE -->
                <div class="col-lg-3 col-md-6 col-12 mb-3">

                    <label>Select Course</label>

                    <select id="st_course" class="form-control">

                        <option value="">Select Course</option>

                        <?php
                        $args = array('type'=>'course');
                        $courses = get_posts($args);

                        foreach($courses as $course){
                            echo '<option value="'.$course->id.'">'.$course->title.'</option>';
                        }
                        ?>

                    </select>

                </div>

                <!-- BRANCH -->
                <div class="col-lg-3 col-md-6 col-12 mb-3">

                    <label>Select Branch</label>

                    <select id="st_branch" class="form-control">
                        <option value="">Select Branch</option>
                    </select>

                </div>

<!-- SESSION -->

<div class="col-lg-3">

<div class="form-group">

<label>Select Session</label>

<select id="st_session" class="form-control">

<option value="">Select Session</option>

<?php

$sessions = get_posts([
'type'=>'session',
'institute_id'=>$institute_id
]);

foreach($sessions as $session){

?>

<option value="<?= $session->id ?>"
>

<?= $session->title ?>

</option>

<?php } ?>

</select>

</div>
</div>
                <!-- SEMESTER -->
                <div class="col-lg-3 col-md-6 col-12 mb-3">

                    <label>Select Semester</label>

                    <select id="st_semester" class="form-control">
                        <option value="">Select Semester</option>
                    </select>

                </div>

                <?php } else { ?>


<!-- CLASS -->
<div class="col-12 col-md-6 col-lg-3">

<label>Select Class</label>

<select id="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){
?>

<option value="<?= $c->id ?>">
    <?= $c->title ?>
</option>

<?php } ?>

</select>

</div>

<!-- SECTION -->
<div class="col-12 col-md-6 col-lg-3">

<label>Select Section</label>

<select id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>

<!-- SUBJECT -->
<div class="col-lg-3">



<label>Academic Session</label>

<input type="text"
id="academic_session"
class="form-control"
placeholder="2025-2026">


</div>

<?php } ?>

                <!-- BUTTON -->
                <div class="col-12 text-center mt-2">

                    <button 
                    type="button"
                    id="apply_filter"
                    class="btn btn-danger px-4">

                    Apply Filter

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- ================= CHART ================= -->

<div class="card shadow mt-3">

    <div class="card-header bg-success text-white">
        Student Feedback Report
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead id="report_head">

                    <tr>
                        <th colspan="5" class="text-center">
                            Apply Filter To Load Report
                        </th>
                    </tr>

                </thead>

                <tbody id="report_table">

                    <tr>
                        <td colspan="5" class="text-center">
                            No Data
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>
<?php include('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let barChart;
let pieChart;

// ================= BAR CHART =================

function loadBarChart(labels,data){

    if(barChart){
        barChart.destroy();
    }

    barChart = new Chart(document.getElementById('barChart'),{

        type:'bar',

        data:{
            labels:labels,

            datasets:[{
                label:'Average Rating',
                data:data,
                backgroundColor:'#007bff'
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,

            scales:{
                y:{
                    beginAtZero:true,
                    max:5
                }
            }
        }

    });
}

// ================= PIE CHART =================

function loadPieChart(labels,data){

    if(pieChart){
        pieChart.destroy();
    }

    pieChart = new Chart(document.getElementById('pieChart'),{

        type:'pie',

        data:{
            labels:labels,

            datasets:[{
                data:data,

                backgroundColor:[
                    '#28a745',
                    '#007bff',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }

    });
}

</script>

<script>

$(document).ready(function(){

// ================= COLLEGE =================

$('#st_course').on('change',function(){

    let course_id = $(this).val();

    $('#st_branch').html('<option>Loading...</option>');
    $('#st_semester').html('<option>Loading...</option>');

    $.post('ajax.php',{

        action:'get_branch',
        course_id:course_id

    },function(res){

        $('#st_branch').html(res.options);

    },'json');

    $.post('ajax.php',{

        action:'get_semester',
        course_id:course_id

    },function(res){

        $('#st_semester').html(res.options);

    },'json');

});


// ================= SCHOOL =================

// ================= SCHOOL CLASS CHANGE =================

$('#st_class').on('change',function(){

    let class_id = $(this).val();

    $('#st_section').html('<option>Loading...</option>');

    $('#school_subject').html('<option>Select Subject</option>');

    // LOAD SECTION

    $.post('ajax.php',{

        action:'get_sections',
        class_id:class_id

    },function(res){

        $('#st_section').html(res.options);

    },'json');

});


// ================= LOAD SCHOOL SUBJECT =================

$('#st_section').on('change',function(){

    let class_id   = $('#st_class').val();

    let section_id = $('#st_section').val();

    $('#school_subject').html('<option>Loading...</option>');

    $.ajax({

        url:'ajax.php',

        type:'POST',

        dataType:'json',

        data:{
            action:'get_subject',
            class_id:class_id,
            section_id:section_id
        },

        success:function(res){

            console.log(res);

            if(res.status){

                $('#school_subject').html(res.options);

            }else{

                $('#school_subject').html('<option>No Subject Found</option>');
            }
        }
    });

});

// ================= APPLY FILTER =================
$('#apply_filter').on('click',function(){

    let formData = {
        action:'get_student_report'
    };

<?php if($institute_type=='college'){ ?>

    formData.course_id = $('#st_course').val();
    formData.branch_id = $('#st_branch').val();
    formData.semester  = $('#st_semester').val();
    formData.session   = $('#st_session').val();

<?php } else { ?>

    formData.class_id   = $('#st_class').val();
    formData.section_id = $('#st_section').val();
    formData.session    = $('#academic_session').val();

<?php } ?>

    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',
        data:formData,

 
        success:function(res){

          
    $('#report_head').html(res.thead);
    $('#report_table').html(res.tbody);

            loadBarChart(
                res.bar_labels,
                res.bar_data
            );

            loadPieChart(
                res.pie_labels,
                res.pie_data
            );
        }

    });

});

});
</script>