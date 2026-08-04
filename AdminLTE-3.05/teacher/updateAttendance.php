<?php include('includes/auth.php'); ?>
<?php checkRole('teacher'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$class              = $_GET['class'] ?? '';
$section            = $_GET['section'] ?? '';
$academic_session   = $_GET['academic_session'] ?? '';

$course       = $_GET['course'] ?? '';
$branch       = $_GET['branch'] ?? '';
$session      = $_GET['session'] ?? '';
$semester     = $_GET['semester'] ?? '';
$subject_id   = $_GET['subject_id'] ?? '';

$date = $_GET['attendance_date'] ?? date('Y-m-d');


// ================= QUERY =================

if($institute_type == 'college'){

$query = mysqli_query($con,"

SELECT 
a.id,
a.Name,
a.roll_no,
am.status,
am.id as meta_id

FROM attendance att

JOIN attendance_meta am 
ON att.id = am.attendance_id

JOIN accounts a
ON a.id = am.user_id

WHERE att.course_id='$course'
AND att.branch_id='$branch'
AND att.session_id='$session'
AND att.semester='$semester'
AND att.subject_id='$subject_id'
AND att.attendance_date='$date'

");

}else{

$query = mysqli_query($con,"

SELECT 
a.id,
a.Name,
a.roll_no,
am.status,
am.id as meta_id

FROM attendance att

JOIN attendance_meta am 
ON att.id = am.attendance_id

JOIN accounts a
ON a.id = am.user_id

WHERE att.class_id='$class'
AND att.section_id='$section'
AND att.academic_session='$academic_session'
AND att.subject_id='$subject_id'
AND att.attendance_date='$date'

");

}

?>



<div class="content-header">
<div class="container-fluid">

<div class="row mb-2">

<div class="col-sm-6">

<div class="d-flex align-items-center">

<h1 class="m-0 text-dark">
Update Attendance :-
</h1>

<a href="attendance.php?action=add-new"
class="btn btn-primary btn-sm mx-4">
Go Back
</a>

</div>
</div>

<div class="col-sm-6">

<ol class="breadcrumb float-sm-right">

<li class="breadcrumb-item">
<a href="#">Accounts</a>
</li>

<li class="breadcrumb-item active">
Attendance
</li>

</ol>

</div>

</div>
</div>
</div>




<div class="card">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover w-100" id="update_attendance">

<thead class="bg-dark text-white">

<tr>

<th>S.No</th>
<th>Roll No</th>
<th>Student Name</th>
<th>Attendance</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php 

$i=1;

while($row=mysqli_fetch_assoc($query)){ 

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $row['roll_no'] ?></td>

<td><?= $row['Name'] ?></td>

<td>

<button

class="btn btn-sm toggle-attendance
<?= ($row['status']=='present') 
? 'btn-success' 
: 'btn-danger' ?>"

data-id="<?= $row['meta_id'] ?>"

data-status="<?= $row['status'] ?>">

<?= ucfirst($row['status']) ?>

</button>

</td>

<td>

<button class="btn btn-primary btn-sm update-btn">

Update

</button>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>



<?php include('footer.php'); ?>



<script>

$(document).on('click','.toggle-attendance',function(){

    let btn = $(this);

    let status = btn.data('status');

    if(status == 'present'){

        btn.removeClass('btn-success')
           .addClass('btn-danger')
           .text('Absent')
           .data('status','absent');

    }else{

        btn.removeClass('btn-danger')
           .addClass('btn-success')
           .text('Present')
           .data('status','present');
    }

});

</script>



<script>

$(document).on('click','.update-btn',function(){

    let row = $(this).closest('tr');

    let btn = row.find('.toggle-attendance');

    let updateBtn = $(this);

    updateBtn.prop('disabled',true)
             .text('Updating...');

    $.ajax({

        url:'ajax.php',

        type:'POST',

        dataType:'json',

        data:{

            action:'update_single_attendance',

            meta_id: btn.data('id'),

            status: btn.data('status')
        },

        success:function(res){

            alert(res.message);

            if(res.status){

                updateBtn
                .removeClass('btn-primary')
                .addClass('btn-success')
                .text('Updated');

            }else{

                updateBtn
                .prop('disabled',false)
                .text('Update');
            }

        },

        error:function(){

            alert('Server Error');

            updateBtn
            .prop('disabled',false)
            .text('Update');
        }

    });

});

</script>