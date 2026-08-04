<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>
<?php
$institute_id=$_SESSION['institute_id'];
?>

<?php
// $sel_ay   = $_GET['academic_year'] ?? '';
// $sel_exam = $_GET['exam'] ?? '';
// $sel_room = $_GET['room_id'] ?? '';
// $sel_sem  = isset($_GET['semester']) ? explode(',', $_GET['semester']) : [];
?>

<?php
//  $rooms=mysqli_query($con,"SELECT id, room_no FROM create_room");
//   while($r=mysqli_fetch_assoc($rooms)){
//    $room=$r['room_no'];
//    $room_id=$r['id'];
//  }
?>
<?php

// $select=mysqli_query($con,"SELECT id, exam_type, academic_year, semester_id FROM create_exam");
// while($row=mysqli_fetch_assoc($select))
//     {

//        $academic_year= $row['academic_year'];
//        $exam_name=$row['exam_name'];
//       $sem_id= $row['semester_id']
      
        ?>
<div class="content-header">
  
  <div class="container-fluid">
    <div class="row">

      <div class="col-12">
        <h1 style="color:#0b3d91;" class=" mb-4 text-center"><u>Room Setting:</u></h1>
      </div>
</div>
 <div class="card shadow mb-1">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">🔍 Add Exam</h5>
        </div>
        <div class="card-body">
            <form  method="post">

   <!-- Course -->
    <div class="row">
    <div class="col-lg-4">
      <div class="form-group">
        <label>Select Course:</label>
        <select id="st_course" name="st_course" class="form-control">
          <option value="">Select Course</option>

          <?php
          $args = array('type'=>'course');
          $s_course_id = get_posts($args);

          foreach($s_course_id as $s_course){
              echo '<option value="'.$s_course->id.'">'.$s_course->title.'</option>';
          }
          ?>

        </select>
      </div>
    </div>

    <!-- Branch -->
    <div class="col-lg-4">
      <div class="form-group">
        <label>Select Branch:</label>
        <select id="st_branch" name="st_branch" class="form-control">
          <option value="">Select Branch</option>
        </select>
      </div>
    </div>

    <!-- Semester -->
    <div class="col-lg-4">
      <div class="form-group">
        <label>Select Session:</label>
        <select id="st_session" name="st_session" class="form-control">
          <option value="">Select Session</option>
          <?php
  
    $sessions = get_posts([
        'type'=>'session',
        'institute_id'=>$institute_id
    ]);

    $options = '<option value="">Select Academic Session</option>';

    if(!empty($sessions)){
        foreach($sessions as $session){
           echo '<option value="'.$session->id.'">'.$session->title.'</option>';
        }
    }

          ?> 
        </select>
      </div>
    </div>
</div>
<div class="row">
      <div class="col-lg-4">
      <div class="form-group">
        <label>Select Semester:</label>
        <select id="st_semester" multiple name="st_semester" class="form-control">
          <option value="">Select Semester</option>
        </select>
      </div>
    </div>
        <div class="col-lg-4">
      <div class="form-group">
        <label>Select Exam Type:</label>
        <select id="st_exam" name="st_exam" class="form-control">
          <option value="">Select Exam</option>
        </select>
      </div>
    </div>
    <div class="col-md-3 mb-3">
                <label for="form-label">
                Preferred Room
                </label>
    <select name="room_id" class="form-control">
<option value="">--Select Room--</option>
<?php
$rooms = mysqli_query($con,"SELECT id, room_no FROM create_room");
while($r = mysqli_fetch_assoc($rooms)){
    echo "<option value='{$r['id']}' "
        . ($sel_room == $r['id'] ? 'selected' : '')
        . ">{$r['room_no']}</option>";
}
?>
</select>
            </div>
            <?php //} ?>
            <div class="col-12">
  <button type="button" onclick="go('row')" class="btn btn-info">
  Generate Set Graph
</button>
      </div>
     
        </div>
    </div>
</div>
</form>

<?php 

if(isset($_GET['action']) && $_GET['action']=='add-new'){

    $id_room = $_GET['room_id'];
$exam_id=$_GET['exam'];
$mode=$_GET['mode'];
$semester=$_GET['semester'];
    $select_room=mysqli_query($con,
        "SELECT room_no, row, columns,capacity,whiteboard
         FROM create_room 
         WHERE id='$id_room'"
    );
    $room=mysqli_fetch_assoc($select_room);
if(!$room){
   echo "<h3 style='color:red'>⚠ Room not found. Please select room.</h3>";
   exit;
}

    $room_no = $room['room_no'];
    $rows = $room['row'];
    $columns = $room['columns'];
    $whiteboard=$room['whiteboard'];
?>
<h3 class="text-center">Setting Arrangement</h3>

<div class="row">
    <div class="col-lg-5">
    <h4 class="ml-2 text-center">Room <?php echo $room_no;?> </h4>
    <h4 class="ml-2">Seating Layout:-</h4>
    <table border="1" cellpadding="6" width="100%" 
           style="text-align:center;border-collapse:collapse;" class="ml-2">

<?php
    for($i=1;$i<=$rows;$i++){

        $letter = chr(64+$i); // A,B,C

        echo "<tr>";

        echo "<td style='font-weight:bold;background:#f1f3f5;width:80px;'>
                Row $letter
              </td>";

        for($j=1;$j<=$columns;$j++){
            echo "<td>$letter$j</td>";
        }

        echo "</tr>";
    }
?>

    </table>

    <h4 class="mt-4 ml-2">Max Room Capacity:-<?php $nbsp;  echo ($rows*$columns).'Students'?></h4>
    <h4 class="ml-2">Total Rows:-<?php echo $rows?></h4>
    <h4 class="ml-2">Total Columns:-<?php echo $columns?></h4>
    <h4 class="ml-2">No of WhiteBoard:-<?php echo $whiteboard?></h4>
  
</div>
<div class="col-lg-6">
<h5 class="text-center">Student Setting Arrangement</h5>
   <button type="button" onclick="go('row')" class="btn btn-info">
 Row-Wise
</button>
<button type="button"  onclick="go('column')" class="btn btn-info">Column-Wise</button>

<a href="generate_room.php?room_id=<?php echo $id_room?>&exam_id=<?php echo $exam ?>&semester=<?php echo $semester ?>&mode=<?php echo $mode ?>" class="btn btn-success ml-5">Generate PDF</a>
<table border="1" cellpadding="6" width="100%"
        style="text-align:center;border-collapse:collapse;" class="ml-2">
        
            <?php
                $id_room = $_GET['room_id'];
               $mode = isset($_GET['mode']) ? $_GET['mode'] : '';

    $select_room=mysqli_query($con,
        "SELECT room_no, row, columns,capacity,whiteboard
         FROM create_room 
         WHERE id='$id_room'"
    );
    $room=mysqli_fetch_assoc($select_room);

    $room_no = $room['room_no'];
    $rows = $room['row'];
    $columns = $room['columns'];
    $whiteboard=$room['whiteboard'];
            $sem=isset($_GET['semester'])?$_GET['semester']:'';
            $sem_array=explode(',',$sem);
          $sem_list = "'" . implode("','", $sem_array) . "'";
            $std_id= mysqli_query($con,"
    SELECT a.id, a.Name,u.meta_value AS semester
    FROM accounts a
    INNER JOIN usermeta u 
        ON u.user_id = a.id
       AND u.meta_key = 'semester'
       AND u.meta_value IN ($sem_list)
    WHERE a.type='student'
    ORDER BY a.id
    
");
         $students= [];
while($s = mysqli_fetch_assoc($std_id)){
    $students[] = $s;
}
$seat_no = 0;
$grid = [];   // seat memory
$used = [];   // used students
// ✅ Table Layout
if($mode=='row'){
for($i=0;$i<$rows;$i++){

    echo "<tr>";
    $letter = chr(65+$i);

    echo "<td style='font-weight:bold;background:#f1f3f5;'>Row $letter</td>";

    for($j=0;$j<$columns;$j++){

        $placed = false;

        // student dhundo
        for($k=0;$k<count($students);$k++){

            if(in_array($k,$used)) continue;

            $sem = $students[$k]['semester'];

            // ✅ LEFT CHECK ONLY
            $left_sem = $j>0 && isset($grid[$i][$j-1])
                        ? $grid[$i][$j-1]['semester'] : null;

            if($sem != $left_sem){
                $grid[$i][$j] = $students[$k];
                $used[] = $k;
                $placed = true;
                break;
            }
        }

        echo "<td>";
        echo "<b>$letter".($j+1)."</b><br>";

        if($placed){
            echo "Roll: ".$grid[$i][$j]['id']."<br>";
            echo "Sem: ".$grid[$i][$j]['semester'];
        } else {
            echo "Empty";
        }

        echo "</td>";
    }

    echo "</tr>";
}
}

// coulmn-wise anti-cheating a;lgorith
if($mode=='column'){

for($j=0;$j<$columns;$j++){   // पहले column loop

    for($i=0;$i<$rows;$i++){  // फिर row loop

        $placed = false;

        for($k=0;$k<count($students);$k++){

            if(in_array($k,$used)) continue;

            $sem = $students[$k]['semester'];

            // 🔥 check top
            $top_sem = $i>0 && isset($grid[$i-1][$j])
                        ? $grid[$i-1][$j]['semester'] : null;

            // 🔥 check left
            $left_sem = $j>0 && isset($grid[$i][$j-1])
                        ? $grid[$i][$j-1]['semester'] : null;

            if($sem != $top_sem && $sem != $left_sem){

                $grid[$i][$j] = $students[$k];
                $used[] = $k;
                $placed = true;
                break;
            }
        }

        if(!$placed){
            $grid[$i][$j] = null; // empty
        }
    }
}
}
// ✅ PRINT TABLE (IMPORTANT)
for($i=0;$i<$rows;$i++){

    echo "<tr>";
    $letter = chr(65+$i);

    echo "<td style='font-weight:bold;background:#f1f3f5;'>Row $letter</td>";

    for($j=0;$j<$columns;$j++){

        echo "<td>";
        echo "<b>$letter".($j+1)."</b><br>";

        if(isset($grid[$i][$j]) && $grid[$i][$j] != null){
            echo "Roll: ".$grid[$i][$j]['id']."<br>";
            echo "Sem: ".$grid[$i][$j]['semester'];
        } else {
            echo "Empty";
        }

        echo "</td>";
    }

    echo "</tr>";
}
            ?>

</table>


</div>
</div>
<?php
if(isset($_POST['save'])){
    $exam_id=$_GET['exam'];
    $room_id=$_GET['room_id'];
    $semester=$_GET['semester'];
    // insert into room-setting
    $insert=mysqli_query($con,"INSERT INTO room_setting (room_id,exam_id) values('$room_id','$exam_id')");
  
  $setting_id = mysqli_insert_id($con);
  // insrt into room-arrangement
  $sem_array=explode(',',$_GET['semester']);
  foreach($sem_array as $sem){
    $q=mysqli_query($con,"SELECT MIN(id) as start,
    MAX(id) as end,
    COUNT(*) as total
    FROM `accounts` WHERE semester_id='$sem'");
    $data=mysqli_fetch_assoc($q);
    $start=$data['start'];
    $end=$data['end'];
    $total=$data['total'];
    // insert into room arrangement
    $insert_setting=mysqli_query($con,"INSERT INTO `room_arrangement` (setting_id,semester,star_roll,end_roll,total) VALUES($setting_id,$sem,$start,$end,$total)");
  if($insert_setting){
   echo "<script>alert('Room Arrangement save sucessfully')</script>";
  }
    }


  }
}
?>
<form action="" method="POST">
<div class="form-group">
    <button type="submit" name="save" id="save" class="btn btn-success">Save</button>
</div>

</form>

<script>
function go(mode){
  let ay   = document.getElementById('academic').value;
  let exam = document.querySelector('[name=exam_name]').value;
  let room = document.querySelector('[name=room_id]').value;
  let semSelect = document.getElementById('sem');
  // we will bw able to sleet the more than one semeater at  a time
  let sem = Array.from(semSelect.selectedOptions).map(o => o.value).join(',');

  window.location =
   `room_arrange.php?action=add-new&academic_year=${ay}&exam=${exam}&room_id=${room}&semester=${sem}&mode=${mode}`;
}
</script>
<?php
include('footer.php');
?>
<script>

$(document).ready(function(){

// ================= LOAD SUBJECT =================
function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();
    let subject =$('#st_subject').val();
    let exam=$('#st_exam').val();

    if(course_id && branch_id && semester && session){

        $('#st_subject').html('<option>Loading...</option>');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_subject',
                course_id: course_id,
                branch_id: branch_id,
                semester: semester,
                session: session
            },
            success: function(res){
                if(res.status){
                    $('#st_subject').html(res.options);
                } else {
                    $('#st_subject').html('<option>No Subject Found</option>');
                }
            }
        });
    }
        if(course_id && branch_id && semester && session){

        $('#st_exam').html('<option>Loading...</option>');

    $.ajax({
    url: 'ajax.php',
    type: 'POST',
    dataType: 'json',
    data: {
        action: 'get_exam',
        course_id: course_id,
        branch_id: branch_id,
        semester_id: semester,
        session_id: session
    },
  success: function(res){

    if(res.status){

        // exam dropdown
        $('#st_exam').html(
            '<option value="">Select Exam</option>' +
            '<option value="'+res.exam_id+'">'+res.exam_type+'</option>'
        );

        // 🔥 DATE RANGE GENERATE
        let start = new Date(res.start_date);
        let end   = new Date(res.end_date);

        let options = '<option value="">Select Exam Date</option>';

        while(start <= end){

            let year  = start.getFullYear();
            let month = String(start.getMonth() + 1).padStart(2, '0');
            let day   = String(start.getDate()).padStart(2, '0');

            let formatted = `${year}-${month}-${day}`;

            options += `<option value="${formatted}">${formatted}</option>`;

            start.setDate(start.getDate() + 1);
        }

        // 🔥 IMPORTANT: dropdown me set karo
        $('#exam_date').html(options);

    } else {

        $('#st_exam').html('<option>No Exam Found</option>');
        $('#exam_date').html('<option>No Dates</option>');
    }
}
});
    }
}


// ================= COURSE CHANGE =================
$('#st_course').on('change', function(){

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

    // 🔥 IMPORTANT: reset subject properly

}); // ✅ YE MISSING THA


// ================= AUTO SUBJECT LOAD =================
$('#st_branch, #st_semester, #st_session').on('change', function(){
    loadSubject();
});

}); // document ready end

</script>
