<?php include('includes/config.php'); ?>
<?php include('header.php'); ?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

  <!-- Left navbar links -->
  <ul class="navbar-nav">

<li class="nav-item">

<button
class="nav-link border-0 bg-transparent"
data-widget="pushmenu"
role="button"
type="button">

<i class="fas fa-bars"></i>

</button>

</li>

    <li class="nav-item d-none d-sm-inline-block">
      <a href="dashboard.php" class="nav-link">Home</a>
    </li>

  </ul>

  <?php

  $institute_id = $_SESSION['institute_id'];

  $select = mysqli_query($con,"SELECT * FROM institutes WHERE id='$institute_id'");

  $data = mysqli_fetch_assoc($select);

  $institute_type = $data['system_type'];
  $short_name = $data['short'];
  $logo = $data['logo'];

  ?>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <li class="nav-item">
     <a href="../../logout.php" class="nav-link">
        Logout <i class="fa fa-sign-out-alt"></i>
      </a> 
    </li>

  </ul>

</nav>
<!-- /.navbar -->


<style>

.nav-sidebar .nav-link{
    padding:10px 14px !important;
    border-radius:8px;
    margin-bottom:4px;
}

.nav-sidebar .nav-link:hover{
    background:#2563eb !important;
    color:#fff !important;
}

.nav-sidebar .menu-open > .nav-link{
    background:#1d4ed8 !important;
    color:#fff !important;
}

.nav-sidebar .nav-treeview{
   display:none;
    padding-left:15px;
    overflow:hidden;

}

.brand-link{
    padding: 10px 12px !important;
    text-align: left;
}

.brand-link .brand-image{
    float: left;
    width: 38px;
    height: 38px;
    margin-left: .4rem;
    margin-right: .3rem;
    margin-top: -2px;
}
.brand-link .brand-text{
    display: inline-block;
    margin-left: 12px !important;   /* Increase/decrease as needed */
    line-height: 38px;
    vertical-align: middle;
}
.main-sidebar{
    overflow-x:hidden !important;
}

.sidebar{
    height:calc(100vh - 57px);
    overflow-y:auto;
    overflow-x:hidden;
}

.nav-sidebar .nav-treeview{
    display:none;
    padding-left:10px;
    overflow:hidden !important;
}

.content-wrapper{
    overflow-x:hidden;
}
</style>


<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

  <!-- Brand Logo -->
  <a href="" class="brand-link">

    <img
      src="../../assest/images/institute_image/<?php echo $logo ?>"
      alt="Logo"
      class="brand-image img-circle elevation-3"
      style="opacity:.8"
    >

    <span class="brand-text font-weight-light">
      <?php echo ucfirst($short_name) ?>
    </span>

  </a>

  <!-- Sidebar -->
  <div class="sidebar">

    <!-- Sidebar Menu -->
    <nav class="mt-2">

      <ul class="nav nav-pills nav-sidebar flex-column"
          
          role="menu"
          data-accordion="false">

        <!-- DASHBOARD -->

        <li class="nav-item">

          <a href="<?=$site_url?>teacher/dashboard.php" class="nav-link active">

            <i class="nav-icon fas fa-tachometer-alt"></i>

            <p>Dashboard</p>

          </a>

        </li>

        <!-- PROFILE -->

        <li class="nav-item has-treeview">

  <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-user"></i>

            <p>
              Profile Details
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="teacher_detail.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Teacher Profile</p>

              </a>

            </li>

          </ul>

        </li>

        <!-- CLASS ROUTINE -->

        <li class="nav-item has-treeview">

<a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-calendar"></i>

            <p>
              Class Routine
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/timetable.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Time Table</p>

              </a>

            </li>

          </ul>

        </li>

        <!-- RESULT -->

        <li class="nav-item has-treeview">

      <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-poll"></i>

            <p>
              Result
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/result.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Upload Marks</p>

              </a>

            </li>

               <li class="nav-item">

              <a href="<?=$site_url?>teacher/view_result.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>View Result</p>

              </a>

            </li>

          </ul>

        </li>

        <!-- ATTENDANCE -->

        <li class="nav-item has-treeview">

  <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-user-check"></i>

            <p>
              Attendance
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/attendance.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Regular Attendance</p>

              </a>

            </li>

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/scan_test.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Mark Exam Attendance</p>

              </a>

            </li>
        <li class="nav-item">

              <a href="<?=$site_url?>teacher/view_attendance.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>View Attendance</p>

              </a>

            </li>

          </ul>

        </li>

        <!-- ACCOUNTING -->
<!-- 
        <li class="nav-item has-treeview">

        <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-wallet"></i>

            <p>
              Accounting
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>student/student-fee.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Student Fee Detail</p>

              </a>

            </li>

          </ul>

        </li> -->

        <!-- STUDY MATERIAL -->

        <li class="nav-item has-treeview">

     <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-book"></i>

            <p>
              Study Material
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/study.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Study Material</p>

              </a>

            </li>

          </ul>

        </li>

        <!-- FEEDBACK -->

        <li class="nav-item has-treeview">

   <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-comments"></i>

            <p>
              Feedback
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/teacher_feedback.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Feedback</p>

              </a>

            </li>

          

          </ul>

        </li>


        <!-- NOTICE -->

        <li class="nav-item has-treeview">

 <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-bullhorn"></i>

            <p>
              Notice Board
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/teacher_notice.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>User Notice</p>

              </a>

            </li>

          </ul>

        </li>

        <!-- ADMIT CARD -->

        <li class="nav-item has-treeview">

  <a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-id-card"></i>

            <p>
              Admit Card
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/admit_card.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Admit Card</p>

              </a>

            </li>

          </ul>

        </li>

        <!-- HOME ASSIGNMENT -->

        <li class="nav-item has-treeview">

<a class="nav-link menu-toggle" style="cursor:pointer;">

            <i class="nav-icon fas fa-pencil-alt"></i>

            <p>
              Home Assignment
              <i class="right fas fa-angle-left"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">

              <a href="<?=$site_url?>teacher/teacher_assignment.php" class="nav-link">

                <i class="far fa-circle nav-icon"></i>

                <p>Assignment</p>

              </a>

            </li>

          </ul>

        </li>

      </ul>

    </nav>

  </div>

  <?php include('footer.php');?>
</aside>


<!-- Content Wrapper -->
<div class="content-wrapper">
<script>

$(document).ready(function(){

    $('.menu-toggle').click(function(){

        let parent = $(this).parent();

        let submenu = $(this).siblings('.nav-treeview');

        if(parent.hasClass('menu-open')){

            parent.removeClass('menu-open');

            submenu.stop(true,true).slideUp(200);

        } else {

            parent.addClass('menu-open');

    submenu.stop(true,true).slideDown(200);

        }

    });

});

</script>