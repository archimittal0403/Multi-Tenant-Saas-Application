<?php
include('includes/auth.php');
checkRole('student');
include('includes/config.php');
 include('includes/functions.php'); 

$user_id = $_SESSION['user_id'];
$institute_id = $_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
?>

<?php include('header.php')?>
<?php include('sidebar.php')?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<style>

body{
    background:#f4f6f9;
}

/* DASHBOARD CARD */

.dashboard-card{
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    height:100%;
    transition:0.3s;
    border:1px solid #eef1f5;
}

.dashboard-card:hover{
    transform:translateY(-3px);
}

.dashboard-title{
    font-size:22px;
    font-weight:700;
    color:#1e293b;
    margin-bottom:20px;
}

/* PROFILE */

.profile-img{
    width:110px;
    height:110px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #e2e8f0;
}

.profile-name{
    font-size:24px;
    font-weight:700;
    color:#0f172a;
    margin-top:15px;
}

.profile-info{
    font-size:15px;
    color:#475569;
    line-height:28px;
}

.view-btn{
    border-radius:10px;
    padding:10px 20px;
    font-weight:600;
}

/* NOTICE */

.notice-box{
    background:#f8fafc;
    padding:18px;
    border-radius:12px;
    border-left:5px solid #2563eb;
}

.notice-title{
    font-size:20px;
    font-weight:700;
    color:#0f172a;
}

.notice-date{
    color:#64748b;
    font-size:14px;
}

/* FEEDBACK */

.feedback-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#f8fafc;
    padding:15px;
    border-radius:12px;
    margin-bottom:12px;
}

.feedback-status{
    padding:6px 15px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.submitted{
    background:#dcfce7;
    color:#166534;
}

.pending{
    background:#fee2e2;
    color:#b91c1c;
}

/* CALENDAR */

#calendar{
    min-height:600px;
}

/* HEAD */

.main-heading{
    font-size:28px;
    font-weight:700;
    color:#0f172a;
}

.sub-heading{
    color:#64748b;
    font-size:15px;
}

</style>

<div class="content-header">
    <div class="container-fluid">

        <div class="mb-4">
            <h1 class="main-heading">
                Welcome Back 👋
            </h1>

            <div class="sub-heading">
                Student Dashboard Panel
            </div>
        </div>

        <div class="row">

            <!-- PROFILE -->

            <div class="col-lg-6 mb-4">

                <div class="dashboard-card">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h3 class="dashboard-title">
                            <i class="fas fa-user-circle text-primary"></i>
                            My Profile
                        </h3>

                        <a href="student-profile.php" class="btn btn-primary btn-sm">
                            View Profile
                        </a>

                    </div>

                    <div class="row align-items-center">

                        <div class="col-md-4 text-center">
  <?php $student_photo=get_usermeta($user_id,'student_photo');?>
                  <img class="profile-user-img img-fluid img-circle" src="../admin/uploads/student_photo/<?php echo $student_photo ?>" alt="User profile picture">

                        </div>

                        <div class="col-md-8">
<?php
$selct_details=mysqli_query($con,"SELECT * FROM `accounts` WHERE id='$user_id'");
$row_details=mysqli_fetch_assoc($selct_details);
$Name=$row_details['Name'];
$roll_no=$row_details['roll_no'];

?>
                            <div class="profile-name">
                                <?php 
                                echo $Name;
                                 ?>
                            </div>

                            <div class="profile-info mt-3">

                                <b>Roll No:</b>
                                <?php echo $roll_no; ?>

                                <br>

                                <b>Institute ID:</b>
                                <?php $institute_id=$_SESSION['institute_id']; 
                                $selet=mysqli_query($con,"SELECT name FROM `institutes` WHERE id='$institute_id'");
                                $row=mysqli_fetch_assoc($selet);
                                echo $institute_name=$row['name'];
                                     
                                ?>

                                <br>

                                <b>Role:</b>
                                Student

                                <br>

                                <b>Status:</b>
                                Active
                                <br>
<?php
if($institute_type=='college'){
       $get_semester=get_usermeta($user_id,'semester');
    ?>
   <b>Active Semester:</b>
<?php
   echo $get_semester;?>
<?php }?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- NOTICE BOARD -->

            <div class="col-lg-6 mb-4">

                <div class="dashboard-card">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h3 class="dashboard-title">
                            <i class="fas fa-bullhorn text-primary"></i>
                            Notice Board
                        </h3>

                        <a href="user_notice.php" class="btn btn-outline-primary btn-sm">
                            View More
                        </a>

                    </div>

                    <?php

                    $notice = mysqli_query($con,"
                    SELECT * FROM notices
                    WHERE institute_id='$institute_id'
                    ORDER BY id DESC
                    LIMIT 1
                    ");

                    $notice_row = mysqli_fetch_assoc($notice);

                    ?>

                    <div class="notice-box">

                        <div class="notice-title">
                            <?php
                            echo $notice_row['title'] ?? 'No Notice Available';
                            ?>
                        </div>

                        <p class="mt-3 text-muted">

                            <?php
                            echo $notice_row['description'] ?? 'No latest notice found.';
                            ?>

                        </p>

                        <div class="notice-date">

                            <i class="fas fa-calendar"></i>

                            <?php
                            echo $notice_row['created_at'] ?? '';
                            ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- SECOND ROW -->

        <div class="row">

            <!-- FEEDBACK -->

            <div class="col-lg-4 mb-4">

                <div class="dashboard-card">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h3 class="dashboard-title">
                            <i class="fas fa-star text-warning"></i>
                            Status
                        </h3>

                    
                    </div>

                    <?php

                    $feedback_query = mysqli_query($con,"
                    SELECT * FROM student_feedback
                    WHERE institute_id='$institute_id'
                    ORDER BY id DESC
                    LIMIT 4
                    ");

                    while($feedback = mysqli_fetch_assoc($feedback_query)){

                        $form_id = $feedback['id'];

                        $check_feedback = mysqli_query($con,"
                        SELECT * FROM student_feedback
                        WHERE 
                         user_id='$user_id'
                        ");

                        $status = mysqli_num_rows($check_feedback) > 0;

                    ?>

                    <div class="feedback-item">

                        <div>

                            <b>
                                FeebBack Update
                            </b>

                        </div>

                        <div>

                            <?php if($status){ ?>

                                <span class="feedback-status submitted">
                                    Submitted
                                </span>

                            <?php } else { ?>

                                <span class="feedback-status pending">
                                    Pending
                                </span>

                            <?php } ?>

                        </div>

                    </div>

                    <?php } ?>

                </div>

            </div>

            <!-- CALENDAR -->

            <div class="col-lg-8 mb-4">

                <div class="dashboard-card">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h3 class="dashboard-title">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            My Calendar
                        </h3>

                    </div>

                    <div id="calendar"></div>

                </div>

            </div>

        </div>

    </div>
</div>
      <?php include('footer.php');?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        height: 650,

        selectable: true,

        editable: true,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

     
    });

    calendar.render();

});

</script>
