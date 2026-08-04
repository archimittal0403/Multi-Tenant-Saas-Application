<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>


<?php include('header.php')?>
<?php include('sidebar.php')?>

<?php
$last_id=isset($_GET['last_id'])?$_GET['last_id']:'';
$subject_id=isset($_GET['subject_id'])?$_GET['subject_id']:'';
$exam_id=isset($_GET['exam_id'])?$_GET['exam_id']:'';
$date=isset($_GET['exam_date'])?$_GET['exam_date']:'';
$room_id=isset($_GET['room_id'])?$_GET['room_id']:'';
$year=date('Y',strtotime($date));
$next=$year+1;
$session=$year. "-" . $next;
$select_sem=mysqli_query($con,"SELECT semester_id FROM `create_exam` WHERE id='$exam_id' AND academic_year='$session'");
$fetch_sem_id=mysqli_fetch_assoc($select_sem);
$sem_id=$fetch_sem_id['semester_id'];
// select only those student those who are present in that particular room
$setting=mysqli_query($con,"SELECT id FROM `room_setting` WHERE exam_id='$exam_id' AND room_id='$room_id'");
$fetch_setting=mysqli_fetch_assoc($setting);
$setting_id=$fetch_setting['id'];
$last_std=mysqli_query($con,"SELECT end_roll FROM `room_arrangement` WHERE semester='$sem_id' AND setting_id='$setting_id'");
$last_fetch=mysqli_fetch_assoc($last_std);
$last_student=$last_fetch['end_roll'];
$students=[];
$select_sid=mysqli_query($con,"SELECT * FROM `accounts` WHERE semester_id='$sem_id' AND type='student' AND id>'$last_id'   ORDER BY roll_no ASC

    LIMIT 1");
 if(mysqli_num_rows($select_sid) == 0){
    $last_id=$_GET['last_id'];
    $last_roll=$last_id+1;
    $exam_id=$_GET['exam_id'];
    $select_sem=mysqli_query($con,"SELECT semester_id FROM `create_exam` WHERE id='$exam_id'");
    $fetch_sem=mysqli_fetch_assoc($select_sem);
    $get_semester=$fetch_sem['semester_id'];
    $data=$data = mysqli_query($con,"
SELECT r.room_no 
FROM room_arrangement ra
JOIN room_setting rs ON rs.id = ra.setting_id
JOIN create_room r ON r.id = rs.room_id
WHERE ra.semester = '$get_semester'
AND '$last_roll' BETWEEN ra.star_roll AND ra.end_roll
AND rs.exam_id = '$exam_id'
LIMIT 1
");
    $data_fetch=mysqli_fetch_assoc($data);
    $room_name=$data_fetch['room_no'];
    echo "<script>
alert('❌ Move to room $room_name');
window.location.href='Exam_attendance.php?exam_id=$exam_id&last_id=$last_roll';
</script>";
exit;
    
exit;
}
    else{
   $student = mysqli_fetch_assoc($select_sid);
    $student_id   = $student['id'];
    $student_name = $student['Name'];
    $roll_number=$student['id'];
    $semester=$student['semester_id'];
    $email=$student['email'];

    // 3️⃣ Student photo
    $photo_q = mysqli_query($con,"
        SELECT meta_value
        FROM usermeta
        WHERE user_id='$student_id'
        AND meta_key='student_profile'
        LIMIT 1
    ");

    $photo_row = mysqli_fetch_assoc($photo_q);
    $student_photo = $photo_row['meta_value'] ?? 'default.png';
}
?>
<?php 
if(isset($_POST['mark_att'])){

    $user_id    = $student_id;
    $exam_id    = $_GET['exam_id'];
    $subject_id = $_GET['subject_id'];
    $exam_date  = $_GET['exam_date'];
    $room_id    = $_GET['room_id'];

    $booklet_no = trim($_POST['bookno']);
    $barcode    = $_POST['barcode_hidden'] ?? '';
    $status     = "present";

    // 1️⃣ Barcode required
    if(empty($barcode)){
        echo "<script>alert('Scan barcode first')</script>";
    }

    // 2️⃣ Booklet required
    elseif(empty($booklet_no)){
        echo "<script>alert('Enter booklet number')</script>";
    }

    else{

        // 3️⃣ Duplicate attendance check
        $att_check = mysqli_query($con,"
            SELECT id FROM exam_attendance
            WHERE user_id='$user_id'
            AND exam_id='$exam_id'
            AND subject_id='$subject_id'
            AND exam_date='$exam_date'
        ");

        if(mysqli_num_rows($att_check) > 0){
            echo "<script>alert('Attendance already marked')</script>";
             echo "<script>
                    window.location.href='Exam_attendance.php?exam_id=$exam_id&exam_date=$exam_date&subject_id=$subject_id&room_id=$room_id&last_id=$user_id';
                    </script>";
        }

        // 4️⃣ ✅ Duplicate booklet check (IMPORTANT)
        else{
            $booklet_check = mysqli_query($con,"
                SELECT id FROM exam_attendance
                WHERE exam_id='$exam_id'
                AND subject_id='$subject_id'
                AND exam_date='$exam_date'
                AND room_id='$room_id'
                AND booklet_no='$booklet_no'
            ");

            if(mysqli_num_rows($booklet_check) > 0){
                echo "<script>alert('❌ Booklet already issued to another student')</script>";
            }

            else{
                // 5️⃣ Insert attendance
                $insert = mysqli_query($con,"
                    INSERT INTO exam_attendance
                    (user_id,exam_id,subject_id,exam_date,room_id,booklet_no,barcode,status)
                    VALUES
                    ('$user_id','$exam_id','$subject_id','$exam_date','$room_id','$booklet_no','$barcode','$status')
                ");

                if($insert){
                    echo "<script>alert('✅ Attendance marked')</script>";
                    echo "<script>
                    window.location.href='Exam_attendance.php?exam_id=$exam_id&exam_date=$exam_date&subject_id=$subject_id&room_id=$room_id&last_id=$user_id';
                    </script>";
                }
                else{
                    echo "<script>alert('Insert failed')</script>";
                }
            }
        }
    }
}
    
    ?>
    <?php
    if(isset($_POST['absent_att'])){
        $user_id=$student_id;
        $exam_id=$_GET['exam_id'];
        $subject_id=$_GET['subject_id'];
         $exam_date  = $_GET['exam_date'];
    $room_id    = $_GET['room_id'];
    $status='Absent';
    $attend_check=mysqli_query($con,"SELECT id FROM `exam_attendance` WHERE user_id='$user_id' AND exam_id='$exam_id' AND subject_id='$subject_id'
    AND exam_date='$exam_date'");
    if(mysqli_num_rows($attend_check)>0){
           echo "<script>alert('Attendance already marked')</script>";
    }
    $insert_query=mysqli_query($con,"INSERT INTO `exam_attendance` (user_id,exam_id,subject_id,exam_date,room_id,booklet_no,barcode,status) VALUES('$user_id','$exam_id','$subject_id','$exam_date',$room_id,'','','$status')");
     if($insert_query){
                    echo "<script>alert('✅ Attendance marked')</script>";
                    echo "<script>
                    window.location.href='Exam_attendance.php?exam_id=$exam_id&exam_date=$exam_date&subject_id=$subject_id&room_id=$room_id&last_id=$user_id';
                    </script>";
                }
                else{
                    echo "<script>alert('Insert failed')</script>";
                }
    }
    ?>
    <?php
    if(isset($_POST['']))
    ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
<h3 class="bg-primary text-white p-2 rounded">Exam Attendance System:-</h3>

            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
              <label for="form-label">
           Exam Name
                </label>
                <select name="exam_name" id="exam_name" >
                    <option value="">--Select Exam Name</option>
                   
                        <?php
                      
$year = date("Y");
$next = $year + 1;
$session = $year . "-" . $next;

                        $select_exam=mysqli_query($con,"SELECT exam_name,id FROM `create_exam` WHERE academic_year='$session'");
                        while($row_exam_name=mysqli_fetch_assoc($select_exam)){
                        $exam_name=$row_exam_name['exam_name'];
                        $exam_id=$row_exam_name['id'];
                        echo "<option value='$exam_id'> $exam_name</option>";
                        }
                        ?>
                  
                </select>
            </div>
            <div class="col-lg-4">
               <label for="form-label">
         Exam Date:
                </label>  
                <input type="date" id="exam_date" name="exam_date">
            </div>
            <div class="col-lg-4">
                <label for="form-label">Subject Name</label>
                <select name="subject_name" id="subject_name">
                    <option value="">--Select Subject</option>
                    
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                   <label for="form-label">
        Exam Room:
                </label> 
                <select name="exam_room" id="exam_room">
                    <option value="">--Select Room No</option>
                    <?php
                    $select_room=mysqli_query($con,"SELECT id,room_no FROM `create_room`");
                        while($row_select_room=mysqli_fetch_assoc($select_room)){
                            $room_no=$row_select_room['room_no'];
                            $room_id=$row_select_room['id'];
                              echo "<option value='$room_id'>$room_no</option>";
                        }
                  
                    ?>
                </select>
            </div>
       <button type="button" onclick="go()" class="btn btn-sm btn-primary">
   Select Exam
</button>
            </div> 
        </div>
    </div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <h3 class="bg-primary text-white p-2 rounded">Student Info</h3>
          
        <h4>Photo:-</h4>
<img src="uploads/student/<?php echo $student_photo;?>" width="120"alt="Student photo">
     <h4 class="mt-3">Student Name:-<?php echo $student_name ?></h4>
       <h4 class="mt-3">Email ID:-<?php echo $email ?></h4>
      <h4 class="mt-3"> Roll Number:-<?php echo $roll_number ?></h4>
       <h4 class="mt-3">Semester:-<?php echo $semester ?></h4>
        </div>
        <div class="col-lg-8">
             <h3 class="bg-primary text-white p-2 rounded">Attendance Entry</h3>
              <form action="" method="post">
             <button id="start_camera" class="btn btn-warning"> 📷 Start Camera Scan</button>
             <input type="hidden" name="barcode_hidden" id="barcode_hidden">
             <input type="text" id="barcode_input" name="barcode_input" class="form-control d-inline-block" style="width:220px" placeholder="Enter barcode">
             <small id="barcode_msg" class="fw-bold"></small>
             <div id="reader" style="width:300px; margin-top:10px;"></div>
            
             <h5 class="mt-4">Answer Booklet Number:-</h5>
             <input type="text" id="bookno" name="bookno" placeholder="Enter the Number">
             <button id="mark_att" name="mark_att" class="btn btn-success">Mark Present</button>
              <button id="absent_att" name="absent_att" class="btn btn-danger">Mark Absent</button>
</form>
        </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h3>Live Attendance</h3>
        <div class="row">
            <div class="col-lg-8">
    <div class="table-responsive">
<table class="table table-bordered w-100">
<thead>
    <tr>
        <th>S.NO</th>
        <th>Roll Number</th>
        <th>Student Name</th>
        <th>Barcode</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
</thead>
<tr>
       <?php
    if(isset($_POST['update_row'])){
        $update_id = $_POST['update_id'];
        $booklet=$_POST['book'];
        $status=$_POST['status'];
        // update query
        $update=mysqli_query($con,"UPDATE `exam_attendance` SET booklet_no='$booklet',status='$status' WHERE id='$update_id'");
if($update){
     echo "<script>alert('✅ update successfully')</script>";
                   
}
    }
    ?>
    <?php
    $select_data=mysqli_query($con,"SELECT e.id,e.user_id,a.Name,e.booklet_no,e.status FROM exam_attendance e JOIN accounts a ON a.id=e.user_id WHERE a.type='student'
    AND e.exam_id='$exam_id' AND e.subject_id='$subject_id' AND e.exam_date='$date' AND e.room_id='$room_id'");
    $count=1;
    while($fetch_data=mysqli_fetch_assoc($select_data)){
         $id= $fetch_data['id']; // ⭐ IMPORTANT
        $roll_number=$fetch_data['user_id'];
        $student_name=$fetch_data['Name'];
        $booklet=$fetch_data['booklet_no'];
        $status=$fetch_data['status'];

    ?>

    <td><?php echo $count++;?></td>
    <td><?php echo $roll_number ?></td>
    <td><?php echo $student_name?></td>
    <form action="" method="post">
        
    <td><input type="text" name="book" id="book" value="<?php echo $booklet?>"></td>
    <td><input type="text" name="status" id="status" value="<?php echo $status?>"></td>

    <td> <input type='hidden' name='update_id' value="<?php echo $id?>">
            <button type='submit' name='update_row' class='btn btn-success btn-sm'>Update</button>
</tr>
</form>
<?php } ?>
</table>
 
</div>
   
            </div>
            <div class="col-lg-4">
                <h4>Record:</h4>
                <p>Total Students:</p>
            </div>
        </div>
    
    </div>
</div>
<?php 
include('footer.php')?>

<script>
    function go(){
        var exam_name=document.getElementById('exam_name').value;
  var exam_date=document.getElementById('exam_date').value;
    var subject_name=document.getElementById('subject_name').value;
  var room_id   = document.getElementById('exam_room').value;
   
             window.location = `Exam_attendance.php?view_table&exam_id=${exam_name}&exam_date=${exam_date}&subject_id=${subject_name}&room_id=${room_id}`;
        
    }
</script>
<script>
$(document).ready(function(){

$('#exam_name').on('change', function(){
    var exam_id = $(this).val();

    if(exam_id!=''){
        $.ajax({
            url:'ajax1.php',
            type:'POST',
            data:{
                action:'get_subjects',
                exam_id:exam_id
            },
            success:function(response){
                console.log("Subjects:", response);
                $('#subject_name').html(response).trigger('change');
            }
        });
    }
});

});
</script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
document.getElementById("start_camera").addEventListener("click", function () {

    const html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {

            const cameraId = devices[0].id; // back camera usually

            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: 250
                },
                (decodedText) => {
                    console.log("Scanned:", decodedText);

                    // ✅ Yaha tum roll no / barcode value use karo
                 document.getElementById("barcode_input").value = decodedText;
document.getElementById("barcode_hidden").value = decodedText;

let msg = document.getElementById("barcode_msg");
msg.innerHTML = "✔ Barcode Scanned";
msg.style.color = "green";

                    html5QrCode.stop(); // scan hone ke baad band
                },
                (errorMessage) => {
                    // scan error ignore
                }
            );
        }
    }).catch(err => {
        console.log(err);
    });

});
</script>
<script>
document.getElementById("barcode_input").addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        e.preventDefault();

        let barcode = this.value.trim();

        if(barcode !== ""){
            fetch('verify_barcode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'barcode=' + encodeURIComponent(barcode)
            })
            .then(res => res.text())
            .then(response => {
                let msg = document.getElementById("barcode_msg");

                if(response === "valid"){
                    msg.innerHTML = "✔ Barcode Verified";
                    msg.style.color = "green";
                        // ✅ IMPORTANT — hidden field me value save
        document.getElementById("barcode_hidden").value = barcode;
                } else {
                    msg.innerHTML = "✖ Invalid Barcode";
                    msg.style.color = "red";
                }

                document.getElementById("barcode_input").value = "";
            });
        }
    }
});
</script>