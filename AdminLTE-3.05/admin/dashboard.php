<?php

include('includes/auth.php');
checkRole(['admin','super_admin']);
include('includes/config.php');
include('includes/functions.php');

$institute_id = $_SESSION['institute_id'];

// check plan expiry 
// $current_year=date('Y-m-d');
// $plan=mysqli_query($con,"SELECT plan_expiry FROM `accounts` WHERE institute_id='$institute_id'");
// $row=mysqli_fetch_assoc($plan);
// $plan_expiry=$row['plan_expiry'];
// if()

$instQuery = $con->prepare("
SELECT system_type,name 
FROM institutes 
WHERE id=?
");

$instQuery->bind_param("i",$institute_id);
$instQuery->execute();

$institute = $instQuery->get_result()->fetch_assoc();

$system_type = $institute['system_type'];
$institute_name = $institute['name'];

$courses_label = 'Courses';

if($system_type=='school')
{
   $courses_label='Classes';
}

if($system_type=='coaching')
{
   $courses_label='Batches';
}
/* =========================
   TOTAL STUDENTS
========================= */
$query = $con->prepare("SELECT COUNT(*) as total FROM accounts WHERE type='student' AND institute_id=?");
$query->bind_param("i", $institute_id);
$query->execute();
$students = $query->get_result()->fetch_assoc()['total'];

/* =========================
   TOTAL TEACHERS
========================= */
$query = $con->prepare("SELECT COUNT(*) as total FROM accounts WHERE type='teacher' AND institute_id=?");
$query->bind_param("i", $institute_id);
$query->execute();
$teachers = $query->get_result()->fetch_assoc()['total'];

/* =========================
   TOTAL COURSES
========================= */
$query = $con->prepare("SELECT COUNT(*) as total FROM posts WHERE institute_id=? AND `type`=?");
$type = 'course';
$query->bind_param("is", $institute_id, $type);
$query->execute();
$courses = $query->get_result()->fetch_assoc()['total'];

/* =========================
   TOTAL CLASSES
========================= */
$query = $con->prepare("SELECT COUNT(*) as total FROM posts WHERE type='class' AND institute_id=?");
$query->bind_param("i", $institute_id);
$query->execute();
$classes = $query->get_result()->fetch_assoc()['total'];


$query=$con->prepare("SELECT COUNT(*) as total FROM posts WHERE type='branch' AND institute_id=?");
$query->bind_param("i",$institute_id);
$query->execute();
$branch=$query->get_result()->fetch_assoc()['total'];
?>

<?php include('header.php') ?>
<?php include('sidebar.php') ?>

<style>
.dashboard-title{
    font-size: 32px;
    font-weight: 700;
    color: #343a40;
}

.dashboard-subtitle{
    color: #6c757d;
    font-size: 14px;
}

.custom-info-box{
    border-radius: 18px;
    overflow: hidden;
    transition: 0.3s ease;
    border: none;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
}

.custom-info-box:hover{
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.custom-info-box .info-box-icon{
    width: 80px;
    font-size: 28px;
    color: #fff;
}

.custom-info-box .info-box-content{
    padding: 15px;
}

.custom-info-box .info-box-text{
    font-size: 15px;
    font-weight: 600;
    color: #6c757d;
}

.custom-info-box .info-box-number{
    font-size: 30px;
    font-weight: 700;
    margin-top: 5px;
    color: #343a40;
}

.gradient-blue{
    background: linear-gradient(135deg,#36d1dc,#5b86e5);
}

.gradient-red{
    background: linear-gradient(135deg,#ff512f,#dd2476);
}

.gradient-green{
    background: linear-gradient(135deg,#11998e,#38ef7d);
}

.gradient-orange{
    background: linear-gradient(135deg,#f7971e,#ffd200);
}

.custom-card{
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
}

.custom-card .card-header{
    background: #fff;
    border-bottom: 1px solid #f1f1f1;
    padding: 18px 22px;
}

.custom-card .card-title{
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}

.welcome-card{
    border-radius: 20px;
    padding: 30px;
    background: linear-gradient(135deg,#667eea,#764ba2);
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(102,126,234,0.3);
}

.welcome-card h2{
    font-weight: 700;
    margin-bottom: 10px;
}

.welcome-card p{
    opacity: 0.9;
    margin: 0;
}
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">

        <div class="welcome-card">
       <h2>
Welcome to <?= $institute_name ?>
</h2>
            <p>
               Manage students, teachers and academic records from one dashboard.
            </p>
        </div>

        <div class="row mb-3">

            <div class="col-sm-6">
                <h1 class="dashboard-title">Dashboard</h1>
                <div class="dashboard-subtitle">
                    Institute Management System Overview
                </div>
            </div>

          

        </div>
    </div>
</div>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">

            <!-- Students -->
            <div class="col-lg-3 col-md-6 col-sm-12">

                <div class="info-box custom-info-box">

                    <span class="info-box-icon gradient-blue">
                        <i class="fas fa-user-graduate"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Students</span>

                        <span class="info-box-number">
                            <?= $students ?>
                        </span>
                    </div>

                </div>

            </div>

            <!-- Teachers -->
            <div class="col-lg-3 col-md-6 col-sm-12">

                <div class="info-box custom-info-box">

                    <span class="info-box-icon gradient-red">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Teachers</span>

                        <span class="info-box-number">
                            <?= $teachers ?>
                        </span>
                    </div>

                </div>

            </div>

            <!-- Courses -->
             <?php 
if($system_type=='school'){?>
<div class="col-lg-3 col-md-6 col-sm-12">

    <div class="info-box custom-info-box">

        <span class="info-box-icon gradient-green">
            <i class="fas fa-users"></i>
        </span>

        <div class="info-box-content">
            <span class="info-box-text">Total Classes</span>

            <span class="info-box-number">
                <?= $classes ?>
            </span>
        </div>

    </div>

</div>
<?php } else {?>
            <div class="col-lg-3 col-md-6 col-sm-12">

                <div class="info-box custom-info-box">

                    <span class="info-box-icon gradient-green">
                        <i class="fas fa-book-open"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total <?= $courses_label ?></span>

                        <span class="info-box-number">
                            <?= $courses ?>
                        </span>
                    </div>

                </div>

            </div>
<?php } ?>

<?php if($system_type=='school'){ ?>

<div class="col-lg-3 col-md-6 col-sm-12">
    <div class="info-box custom-info-box">

        <span class="info-box-icon gradient-orange">
            <i class="fas fa-users"></i>
        </span>

        <div class="info-box-content">
            <span class="info-box-text">Total Sections</span>

            <span class="info-box-number">
                <?= $classes ?>
            </span>
        </div>

    </div>
</div>

<?php } else { ?>

<div class="col-lg-3 col-md-6 col-sm-12">
    <div class="info-box custom-info-box">

        <span class="info-box-icon gradient-orange">
            <i class="fas fa-code-branch"></i>
        </span>

        <div class="info-box-content">
            <span class="info-box-text">Total Branches</span>

            <span class="info-box-number">
                <?= $branch ?>
            </span>
        </div>

    </div>
</div>

<?php } ?>
        </div>

        <!-- Calendar -->
        <div class="card custom-card mt-4">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-2 text-primary"></i>
                    My Calendar
                </h3>
            </div>

            <div class="card-body">
                <div id="calendar" style="min-height:650px;"></div>
            </div>

        </div>

    </div>
</section>

<?php include('footer.php') ?>