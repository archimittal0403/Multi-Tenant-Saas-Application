<?php
include('includes/auth.php');
checkRole('teacher');

include('includes/config.php');
include('includes/functions.php');
include('header.php');
include('sidebar.php');

$teacher_id   = $_SESSION['user_id'];
$institute_id = $_SESSION['institute_id'];

$doj = get_usermeta($teacher_id,'doj');

$total_days   = 0;
$present_days = 0;
$absent_days  = 0;

$attendance = [];

$query = mysqli_query($con,"
SELECT *
FROM teacher_attendance
WHERE teacher_id = '$teacher_id'
AND institute_id = '$institute_id'
AND attendance_date >= '$doj'
ORDER BY attendance_date ASC
");

while($row = mysqli_fetch_assoc($query))
{
    $attendance[] = $row;

    $total_days++;

    if(strtolower($row['status']) == 'present')
    {
        $present_days++;
    }
    else
    {
        $absent_days++;
    }
}

$percentage = 0;

if($total_days > 0)
{
    $percentage = round(($present_days / $total_days) * 100, 2);
}
?>

<div class="container-fluid">
<h3>View Attendace</h3>
    <div class="row">

        <div class="col-md-3">
            <div class="small-box bg-info">
                
                <div class="inner">
                    <h3><?= $total_days ?></h3>
                    <p>Total Attendance</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= $present_days ?></h3>
                    <p>Present</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= $absent_days ?></h3>
                    <p>Absent</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= $percentage ?>%</h3>
                    <p>Percentage</p>
                </div>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Attendance</h3>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php

                if(!empty($attendance))
                {
                    $i = 1;

                    foreach($attendance as $row)
                    {
                ?>

                    <tr>

                        <td><?= $i++ ?></td>

                        <td>
                            <?= date('d-m-Y',strtotime($row['attendance_date'])) ?>
                        </td>

                        <td>

                            <?php if($row['status']=='present'){ ?>

                                <span class="badge badge-success">
                                    Present
                                </span>

                            <?php } else { ?>

                                <span class="badge badge-danger">
                                    Absent
                                </span>

                            <?php } ?>

                        </td>

                    </tr>

                <?php
                    }
                }
                else
                {
                    echo '<tr>
                            <td colspan="3" class="text-center">
                                No Attendance Found
                            </td>
                          </tr>';
                }
                ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<?php include('footer.php'); ?>