<?php

include('includes/config.php');

?>


<?php include('header.php'); ?>

<?php

$institute_id = $_SESSION['institute_id'];

$select=mysqli_query($con,"
SELECT * FROM institutes 
WHERE id='$institute_id'
");

$data=mysqli_fetch_assoc($select);
?>

<?php 
$institute_type=$data['system_type'];
$short_name=$data['short'];
$logo=$data['logo'];

?>
<style>
.nav-sidebar .nav-link{
    padding:10px 14px !important;
    border-radius:8px;
    margin-bottom:4px;
    transition:0.3s;
}

.nav-sidebar .nav-link:hover{
    background:#2563eb !important;
    color:#fff !important;
}

.nav-sidebar .nav-link.active{
    background:#1d4ed8 !important;
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

.brand-image{
    width:35px !important;
    height:35px !important;
    object-fit:cover;
    margin-left:0 !important;
    margin-right:10px !important;
    float:none !important;
}

.brand-text{
    font-size:22px;
    margin-left:0 !important;
    padding-left:0 !important;
}
</style>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

  <!-- Left navbar links -->
  <ul class="navbar-nav">

    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
      <a href="dashboard.php" class="nav-link">
        Home
      </a>
    </li>

  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <li class="nav-item">
      <a href="../../logout.php" class="nav-link">
        Logout <i class="fa fa-sign-out-alt"></i>
      </a>
    </li>

  </ul>

</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

  <!-- Brand Logo -->
  <a href="" class="brand-link">

    <img 
    src="../../assest/images/institute_image/<?php echo $logo ?>" 
    alt="Logo"
    class="brand-image img-circle elevation-3"
    style="opacity:.8">

    <span class="brand-text font-weight-light">
      <?php echo ucfirst($short_name) ?>
    </span>

  </a>

  <!-- Sidebar -->
  <div class="sidebar">

    <!-- Sidebar Menu -->
    <nav class="mt-2">

      <ul class="nav nav-pills nav-sidebar flex-column"
          data-widget="treeview"
          role="menu"
          data-accordion="false">

        <!-- Dashboard -->
        <li class="nav-item">

          <a href="<?=$site_url?>admin/dashboard.php" 
             class="nav-link active">

            <i class="nav-icon fas fa-tachometer-alt"></i>

            <p>Dashboard</p>

          </a>

        </li>

        <!-- Manage Accounts -->
        <li class="nav-item has-treeview">

          <a href="#" class="nav-link">

            <i class="nav-icon fas fa-users"></i>

            <p>
              Manage Accounts
              <i class="fas fa-angle-left right"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <?php
            sidebarMenu(
              $site_url.'admin/teacher.php?user=teacher',
              'fas fa-chalkboard-teacher',
              'Teachers'
            );

            sidebarMenu(
              $site_url.'admin/user-account.php?user=student',
              'fas fa-user-graduate',
              'Students'
            );
            ?>

          </ul>

        </li>

        <!-- Academic Section -->

        <?php if($institute_type=='college'){ ?>

        <li class="nav-item has-treeview">

          <a href="#" class="nav-link">

            <i class="nav-icon fas fa-school"></i>

            <p>
              Academic Management
              <i class="fas fa-angle-left right"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <?php

            sidebarMenu(
              $site_url.'admin/course1.php',
              'fas fa-book-open',
              'Courses'
            );

            sidebarMenu(
              $site_url.'admin/branch.php',
              'fas fa-code-branch',
              'Branches'
            );

            sidebarMenu(
              $site_url.'admin/session.php',
              'fas fa-calendar',
              'Academic Session'
            );

            sidebarMenu(
              $site_url.'admin/semester.php',
              'fas fa-layer-group',
              'Semester'
            );

            sidebarMenu(
              $site_url.'admin/add_subject.php',
              'fas fa-book',
              'Subjects'
            );

            ?>

          </ul>

        </li>

        <?php } else { ?>

        <li class="nav-item has-treeview">

          <a href="#" class="nav-link">

            <i class="nav-icon fas fa-school"></i>

            <p>
              Academic Management
              <i class="fas fa-angle-left right"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <?php

            sidebarMenu(
              $site_url.'admin/classes.php',
              'fas fa-school',
              'Classes'
            );

            sidebarMenu(
              $site_url.'admin/section.php',
              'fas fa-users',
              'Sections'
            );

            sidebarMenu(
              $site_url.'admin/add_subject.php',
              'fas fa-book',
              'Subjects'
            );
 sidebarMenu(
              $site_url.'admin/session.php',
              'fas fa-book',
              'Session'
            );
            ?>

          </ul>

        </li>

        <?php } ?>

        <!-- Routine -->
        <li class="nav-item has-treeview">

          <a href="#" class="nav-link">

            <i class="nav-icon fas fa-clock"></i>

            <p>
              Class Routine
              <i class="fas fa-angle-left right"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <?php

            sidebarMenu(
              $site_url.'admin/period.php',
              'fas fa-clock',
              'Periods'
            );

            sidebarMenu(
              $site_url.'admin/timetable.php',
              'fas fa-calendar-alt',
              'Time Table'
            );

            ?>

          </ul>

        </li>

        <!-- Attendance -->
        <li class="nav-item has-treeview">

          <a href="#" class="nav-link">

            <i class="nav-icon fas fa-user-check"></i>

            <p>
              Attendance
              <i class="fas fa-angle-left right"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <?php

            sidebarMenu(
              $site_url.'admin/attendance.php',
              'fas fa-check-circle',
              'Attendance'
            );

            ?>

          </ul>

        </li>

        <!-- Accounting -->
        <li class="nav-item has-treeview">

          <a href="#" class="nav-link">

            <i class="nav-icon fas fa-money-bill"></i>

            <p>
              Accounting
              <i class="fas fa-angle-left right"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <?php

            sidebarMenu(
              $site_url.'admin/student-fee.php',
              'fas fa-wallet',
              'Student Fee'
            );

            ?>

          </ul>

        </li>

        <!-- Study Material -->
        <li class="nav-item has-treeview">

          <a href="#" class="nav-link">

            <i class="nav-icon fas fa-book"></i>

            <p>
              Study Material
              <i class="fas fa-angle-left right"></i>
            </p>

          </a>

          <ul class="nav nav-treeview">

            <?php

            sidebarMenu(
              $site_url.'admin/study.php',
              'fas fa-file-pdf',
              'Study Material'
            );

            ?>

          </ul>

        </li>

        <!-- Feedback -->
<li class="nav-item has-treeview">

    <a href="#" class="nav-link">

        <i class="nav-icon fas fa-comments"></i>

        <p>
            Feedback
            <i class="fas fa-angle-left right"></i>
        </p>

    </a>

    <ul class="nav nav-treeview">

        <?php

        sidebarMenu(
            $site_url.'admin/feedback_faq.php',
            'fas fa-question-circle',
            'Feedback Question'
        );

        sidebarMenu(
            $site_url.'admin/feedback_report.php',
            'fas fa-chart-bar',
            'Feedback Report'
        );

        sidebarMenu(
            $site_url.'admin/All_feedback_form.php',
            'fas fa-list',
            'Feedback Forms'
        );

        ?>

    </ul>

</li>

<!-- Notice Board -->
<li class="nav-item has-treeview">

    <a href="#" class="nav-link">

        <i class="nav-icon fas fa-bullhorn"></i>

        <p>
            Notice Board
            <i class="fas fa-angle-left right"></i>
        </p>

    </a>

    <ul class="nav nav-treeview">

        <?php

        sidebarMenu(
            $site_url.'admin/admin_notice.php',
            'fas fa-bell',
            'Admin Notice'
        );

        ?>

    </ul>

</li>

<!-- Examination -->
<li class="nav-item has-treeview">

    <a href="#" class="nav-link">

        <i class="nav-icon fas fa-file-alt"></i>

        <p>
            Examination
            <i class="fas fa-angle-left right"></i>
        </p>

    </a>

    <ul class="nav nav-treeview">

        <?php

        sidebarMenu(
            $site_url.'admin/admin_create.php',
            'fas fa-plus-circle',
            'Create Exam'
        );

        sidebarMenu(
            $site_url.'admin/exam_datesheet.php',
            'fas fa-calendar',
            'Exam Datesheet'
        );

        sidebarMenu(
            $site_url.'admin/admit_card.php',
            'fas fa-id-card',
            'Admit Card'
        );

        ?>

    </ul>

</li>



<!-- QR Generation -->
<li class="nav-item has-treeview">

    <a href="#" class="nav-link">

        <i class="nav-icon fas fa-qrcode"></i>

        <p>
            QR Generation
            <i class="fas fa-angle-left right"></i>
        </p>

    </a>

    <ul class="nav nav-treeview">

        <?php

        sidebarMenu(
            $site_url.'admin/qr-mangement.php',
            'fas fa-qrcode',
            'QR Management'
        );

        ?>

    </ul>

</li>
      
<li class="nav-item has-treeview">

    <a href="#" class="nav-link">

        <i class="nav-icon fas fa-credit-card"></i>

        <p>
            Subscription Plan
            <i class="fas fa-angle-left right"></i>
        </p>

    </a>


    <ul class="nav nav-treeview">

        <?php

        sidebarMenu(
            $site_url.'admin/subscription.php',
            'fas fa-layer-group',
            'Subscription'
        );


        sidebarMenu(
            $site_url.'admin/payment_receipt.php?institute_id='.$institute_id,
            'fas fa-file-invoice-dollar',
            'Payment History'
        );

        ?>

    </ul>

</li>
   
<li class="nav-item has-treeview">

    <a href="#" class="nav-link">

        <i class="nav-icon fas fa-certificate"></i>

        <p>
          Certification
            <i class="fas fa-angle-left right"></i>
        </p>

    </a>


    <ul class="nav nav-treeview">

        <?php

        sidebarMenu(
            $site_url.'admin/transfer.php',
            'fas fa-layer-group',
            'Certification'
        );


    

        ?>

    </ul>

</li>
      </ul>
      

    </nav>

  </div>

</aside>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <script>
 $(document).ready(function () {

    $(document).on('click', function (e) {

        let sidebar = $('.main-sidebar');
        let toggleBtn = $('[data-widget="pushmenu"]');

        // outside click detect
        if (
            !sidebar.is(e.target) &&
            sidebar.has(e.target).length === 0 &&
            !toggleBtn.is(e.target) &&
            toggleBtn.has(e.target).length === 0
        ) {

            // mobile sidebar open ho to close karo
            if ($('body').hasClass('sidebar-open')) {
                $('[data-widget="pushmenu"]').trigger('click');
            }
        }
    });

});
    </script>
  <!-- <script>

$(document).ready(function(){

    $('.has-treeview > .nav-link').click(function(e){

        e.preventDefault();

        let parent = $(this).parent();

        let submenu = $(this).siblings('.nav-treeview');

        if(parent.hasClass('menu-open')){

            parent.removeClass('menu-open');

            submenu.slideUp(200);

        } else {

            $('.has-treeview').removeClass('menu-open');
            $('.nav-treeview').slideUp(200);

            parent.addClass('menu-open');

            submenu.slideDown(200);

        }

    });

});

</script> -->