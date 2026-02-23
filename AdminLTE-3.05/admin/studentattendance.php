
<?php

include('includes/config.php');
include('includes/functions.php');
?>
<?php
if(isset($_POST['action']) && $_POST['action']=='get_subjects'){

    $semester  = $_POST['fil_semester'] ?? '';

    $subjects = [];

    $sub_query = mysqli_query($con,"
        SELECT name 
        FROM courses 
        WHERE semester='$semester'
    ");

    while($sub = mysqli_fetch_assoc($sub_query)){
        $subjects[] = $sub;
    }

    echo json_encode($subjects);
    exit;
}
?>
<?php

if(isset($_POST['action']) && $_POST['action']=='view_st'){


    $enroll_id = $_POST['enroll_id'] ?? '';
    $semester  = $_POST['fil_semester'] ?? '';

    $data = [];

    // 1️⃣ Semester subjects fetch karo
    $subjects = [];

    $sub_query = mysqli_query($con,"
        SELECT id, name 
        FROM courses 
        WHERE semester='$semester'
    ");

    while($sub = mysqli_fetch_assoc($sub_query)){
        $subjects[$sub['id']] = $sub['name'];
    }

    // 2️⃣ Student attendance fetch karo
    $select = mysqli_query($con,"
        SELECT meta_key, meta_value
        FROM attendance1
        WHERE item_id='$enroll_id'
    ");

    $attendance = [];

    while($row = mysqli_fetch_assoc($select)){
        $attendance[$row['meta_key']] = $row['meta_value'];
    }

    // 3️⃣ Row build karo
    $rowData = [];
    $rowData['Date'] = $attendance['dob'] ?? '';

    // sab subjects blank set karo
    foreach($subjects as $id => $name){
        $rowData[$name] = '';
    }

    // jis subject ka record hai usme status daalo
    if(isset($attendance['at_subject']) && isset($attendance['status'])){

        $subject_id = $attendance['at_subject'];

        if(isset($subjects[$subject_id])){
            $subject_name = $subjects[$subject_id];
            $rowData[$subject_name] = $attendance['status'];
        }
    }

    $data[] = $rowData;

    echo json_encode(["data"=>$data]);
    exit;
}
?>
<?php include('header.php')?>
<?php include('sidebar.php')?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <div class= "d-flex">
            <h1 class="m-0 text-dark"> View Attendance :-</h1>
           
<a href="attendance.php" class="btn btn-primary btn-sm mx-4">Go Back</a>
</div>
          </div> 
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Accounts</a></li>
              <li class="breadcrumb-item active">StAttendance</li>
            </div>
            </ol>
          </div><!-- /.col -->
           </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
     <form action="" method="get">
<div class="content-wrapper">
    <div class="card">
      <div class="card-body">
          <div class="row">
            <div class="col-lg-4">
              <div class="form-group">
              <label for="enroll_id">Enroll_ID</label>
             <input type="text" id="enroll_id" name="enroll_id" class="form-control">
            </div>
</div>
             <div class="col-lg-4">
            <label for="fil_semester">Select Semester</label>
            <select name="fil_semester" id="fil_semester" class="form-control">
<?php

$select=mysqli_query($con,"SELECT * FROM `courses`");
while($row_fetch=mysqli_fetch_assoc($select)){
 echo '<option value="'.$row_fetch['semester'].'">'.$row_fetch['semester'].'</option>';

}
?>
            </select>
          </div>

            <div class="col-lg-1">
             <div class="d-flex justify-content-end">
              <button type="button" id="applyFilter" class="btn btn-info">Apply</button>
</div>
            </div>
      </div>
    </div>
</div>
</form>
<div class="table-responsive">
  <table class="table table-bordered w-100" id="st_attendance">
    <thead>
      <tr>
        <th>Date</th>
        <?php 
        if(isset($_GET['fil_semester'])){
        $semester=$_GET['fil_semester'];
        $select=mysqli_query($con,"SELECT name FROM `courses` WHERE semester='$semester'");
        while($row_fetch=mysqli_fetch_assoc($select)){
          
   echo "<th>".$row_fetch['name']."</th>";
   //echo "<th>".$row_fetch['name']."</th>";
        }
     
        }
      ?>
     
    
      </tr>
    </thead>
  </table>
</div>
     <?php 
include('footer.php')?>
<script>
$('#applyFilter').on('click', function(e){

    e.preventDefault();

    var semester = $('#fil_semester').val();

    $.ajax({
        url: 'studentattendance.php',
        type: 'POST',
        dataType:'json',
        data: {
            action: 'get_subjects',
            fil_semester: semester
        },
        success: function(response){

            var columns = [{data:'Date', title:'Date'}];

            response.forEach(function(subject){
                columns.push({
                    data: subject.name,
                    title: subject.name
                });
            });

            $('#st_attendance').DataTable({
                destroy:true,
                ajax:{
                    url:'studentattendance.php',
                    type:'POST',
                    data:{
                        action:'view_st',
                        enroll_id: $('#enroll_id').val(),
                        fil_semester: semester
                    }
                },
                columns: columns
            });

        }
    });

});


  </script>