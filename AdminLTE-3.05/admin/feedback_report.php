<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>

<div class="container mt-5">
    
    <div class="card shadow-lg border-0">
        
        <!-- Header -->
        <div class="card-header bg-dark text-white text-center">
            <h4 class="mb-0">📊 Feedback Reports Dashboard</h4>
            <small>Select report type</small>
        </div>

        <!-- Body -->
        <div class="card-body">

            <div class="row text-center">

                <!-- Teacher Report Card -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title">👨‍🏫 Teacher Feedback</h5>
                            <p class="text-muted">
                                View feedback given by students for teachers subject-wise.
                            </p>
                            <a href="teacher_report.php" 
                               class="btn btn-danger px-4">
                                View Report
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Student Report Card -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title">🎓 Student Feedback</h5>
                            <p class="text-muted">
                                View feedback submitted by students course & semester wise.
                            </p>
                            <a href="student_report.php" 
                               class="btn btn-warning px-4">
                                View Report
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
<?php include('footer.php')?>