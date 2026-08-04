<?php include('includes/auth.php'); ?>
<?php checkRole('student'); ?>
<?php include('includes/config.php') ?>
<?php include('header.php') ?>
<?php include('sidebar.php') ?>
<?php include('includes/functions.php') ?>

<?php
$institute_id = $_SESSION['institute_id'];
$student_id = $_SESSION['user_id'];
?>

<style>

.attendance-card{
    background:#ffffff;
    border-radius:24px;
    padding:25px;
    box-shadow:0 8px 30px rgba(0,0,0,0.08);
    border:1px solid #e2e8f0;
    margin-top:20px;
}

.attendance-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    flex-wrap:wrap;
    gap:15px;
}

.attendance-title{
    font-size:28px;
    font-weight:bold;
    color:#0f172a;
}

.attendance-subtitle{
    color:#64748b;
    font-size:15px;
}

.custom-table{
    width:100%;
    border-collapse:collapse;
    overflow:hidden;
    border-radius:18px;
}

.custom-table thead{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
}

.custom-table thead th{
    padding:18px;
    font-size:15px;
    font-weight:600;
    border:none;
    text-align:center;
}

.custom-table tbody tr{
    transition:0.3s;
}

.custom-table tbody tr:nth-child(even){
    background:#f8fafc;
}

.custom-table tbody tr:hover{
    background:#eff6ff;
}

.custom-table tbody td{
    padding:18px;
    text-align:center;
    font-size:15px;
    color:#1e293b;
    border-bottom:1px solid #e2e8f0;
}

.status-badge{
    display:inline-block;
    padding:8px 18px;
    border-radius:30px;
    font-size:13px;
    font-weight:bold;
    text-transform:uppercase;
}

.present{
    background:#dcfce7;
    color:#15803d;
}

.absent{
    background:#fee2e2;
    color:#b91c1c;
}

.no-data{
    text-align:center;
    padding:30px;
    color:#64748b;
    font-size:18px;
}

@media(max-width:768px){

    .attendance-title{
        font-size:22px;
    }

    .custom-table{
        min-width:700px;
    }

    .table-responsive{
        overflow-x:auto;
    }

}

</style>

<div class="attendance-card">

    <div class="attendance-header">

        <div>
            <div class="attendance-title">
                Exam Attendance
            </div>

            <div class="attendance-subtitle">
                View your exam attendance records
            </div>
        </div>

    </div>

    <div class="table-responsive">

        <table class="custom-table">

            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Exam Name</th>
                    <th>Subject Name</th>
                    <th>Status</th>
                    <th>Exam Date</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $count = 1;

            $select = mysqli_query($con,"
            SELECT 
            ea.subject_name,
            ea.attendance_status,
            ea.created_at,
            et.exam_type

            FROM exam_attendance ea

            JOIN exam_type et
            ON ea.exam_id = et.id

            WHERE ea.student_id='$student_id'
            ");

            if(mysqli_num_rows($select) > 0)
            {
                while($row=mysqli_fetch_assoc($select))
                {
            ?>

                <tr>

                    <td><?= $count++ ?></td>

                    <td>
                        <?= $row['exam_type'] ?>
                    </td>

                    <td>
                        <?= $row['subject_name'] ?>
                    </td>

                    <td>

                        <span class="status-badge <?= strtolower($row['attendance_status']) ?>">

                            <?= $row['attendance_status'] ?>

                        </span>

                    </td>

                    <td>
                        <?= date('d M Y',strtotime($row['created_at'])) ?>
                    </td>

                </tr>

            <?php
                }
            }
            else
            {
            ?>

            <tr>
                <td colspan="5" class="no-data">
                    No attendance records found
                </td>
            </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>
<?php include('footer.php');?>