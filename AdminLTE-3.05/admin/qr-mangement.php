<?php include('includes/auth.php'); ?>
<?php checkRole('admin'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php')?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>

<style>
.qr-page {
    padding: 25px;
}

.qr-title-box {
    background: linear-gradient(135deg, #4e73df, #224abe);
    padding: 30px;
    border-radius: 15px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
}

.qr-title-box h2 {
    margin: 0;
    font-weight: 700;
}

.qr-title-box p {
    margin-top: 8px;
    opacity: 0.9;
}

.qr-card-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
}

.qr-card {
    flex: 1;
    min-width: 250px;
    background: #fff;
    border-radius: 18px;
    padding: 35px 25px;
    text-align: center;
    transition: 0.3s;
    box-shadow: 0px 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #f1f1f1;
}

.qr-card:hover {
    transform: translateY(-6px);
    box-shadow: 0px 10px 25px rgba(0,0,0,0.12);
}

.qr-icon {
    width: 80px;
    height: 80px;
    margin: auto;
    margin-bottom: 20px;
    border-radius: 50%;
    background: #eef3ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 35px;
    color: #4e73df;
}

.qr-card h4 {
    font-weight: 700;
    margin-bottom: 10px;
}

.qr-card p {
    color: #777;
    font-size: 14px;
    min-height: 45px;
}

.qr-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 22px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.student-btn {
    background: #4e73df;
    color: white;
}

.teacher-btn {
    background: #1cc88a;
    color: white;
}

.exam-btn {
    background: #f6c23e;
    color: black;
}

.qr-btn:hover {
    opacity: 0.9;
    transform: scale(1.03);
    color: white;
}

.exam-btn:hover {
    color: black;
}

@media(max-width:768px) {
    .qr-card-wrapper {
        flex-direction: column;
    }
}
</style>

<div class="main-content">
    <div class="qr-page">

        <!-- HEADER -->
        <div class="qr-title-box">
            <h2>QR Management</h2>
            <p>
                Generate and manage Student, Teacher and Exam QR Codes
            </p>
        </div>

        <!-- CARDS -->
        <div class="qr-card-wrapper">

            <!-- STUDENT QR -->
            <div class="qr-card">
                <div class="qr-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>

                <h4>Generate Student QR</h4>

                <p>
                    Generate QR Codes for students using course, branch,
                    semester and section filters.
                </p>

                <a href="student-qr.php" class="qr-btn student-btn">
                    Generate QR
                </a>
            </div>

            <!-- TEACHER QR -->
            <div class="qr-card">
                <div class="qr-icon" style="background:#e9fff7;color:#1cc88a;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>

                <h4>Generate Teacher QR</h4>

                <p>
                    Generate QR Codes for teachers and faculty members.
                </p>

                <a href="teacher-qr.php" class="qr-btn teacher-btn">
                    Generate QR
                </a>
            </div>

            <!-- EXAM QR -->
            <div class="qr-card">
                <div class="qr-icon" style="background:#fff8e5;color:#f6c23e;">
                    <i class="fas fa-file-alt"></i>
                </div>

                <h4>Generate Exam QR</h4>

                <p>
                    Generate secure QR Codes for exams and hall tickets.
                </p>

                <a href="exam-qr.php" class="qr-btn exam-btn">
                    Generate QR
                </a>
            </div>

        </div>

    </div>
</div>

<?php include('footer.php'); ?>