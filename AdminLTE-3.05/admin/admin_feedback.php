<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>
<?php 
    if(!isset($_GET['all_id'])){
    echo "id is missing";
}
?>
<?php  $institute_id=$_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
?>
<?php
if(isset($_GET['all_id'])){
    $id=$_GET['all_id'];
    $select_data=mysqli_query($con,"SELECT Name,type,email FROM `accounts` WHERE id='$id'");
$row_fetch=mysqli_fetch_assoc($select_data);
$name=$row_fetch['Name'];
$email=$row_fetch['email'];
$type=$row_fetch['type'];
}
?>
<?php
$questions=[];
$questions=[
    "Is the ERP system easy to use?",
    "Is the dashboard well organized?",
    "Is data entry simple and efficient?",
    "Does the system reduce manual work?",
    "Does the system crash frequently?",
    "Does the system handle large data properly?",
    "Is technical support available when needed?",
    "Are issues resolved on time?"
]
?>
<section class="content">
      <div class="container-fluid">
<div class="card">
 
    
      </div>
</div>
      
</section>
<div class="container mt-4">

    <div class="card shadow-lg border-0">

        <!-- Header -->
        <div class="card-header bg-gradient-primary text-white text-center">
            <h4 class="mb-0">Admin Feedback Form</h4>
            <small>System Evaluation</small>
        </div>

        <div class="card-body">

            <!-- Personal Info -->
            <div class="mb-4 p-3 bg-light rounded shadow-sm">
                <h5 class="mb-3"><u>Personal Details</u></h5>

                <div class="row">
                    <div class="col-md-4">
                        <strong>Name:</strong> <?php echo ucfirst($name);?>
                    </div>
                    <div class="col-md-4">
                        <strong>Type:</strong> <?php echo $type;?>
                    </div>
                    <div class="col-md-4">
                        <strong>Email:</strong> <?php echo $email;?>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="generate_pdf.php" method="post">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">

                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width:60%">Questions</th>
                                <th style="width:40%">Your Rating</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach($questions as $qindex=>$question){ ?>
                            <tr>

                                <!-- Question -->
                                <td style="font-weight:500;">
                                    <?php echo ($qindex+1).". ".$question; ?>
                                </td>

                                <!-- Rating -->
                                <td class="text-center">

                                    <div class="rating-box">

                                        <label class="rating-option bad">
                                            <input type="radio" name="rating[<?php echo $qindex ?>]" value="bad">
                                            😡 Bad
                                        </label>

                                        <label class="rating-option avg">
                                            <input type="radio" name="rating[<?php echo $qindex ?>]" value="average">
                                            😐 Average
                                        </label>

                                        <label class="rating-option good">
                                            <input type="radio" name="rating[<?php echo $qindex ?>]" value="good">
                                            😄 Very Good
                                        </label>

                                    </div>

                                </td>

                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>

                <!-- Submit -->
                <div class="text-end mt-4">
                    <button name="generate_pdf" class="btn btn-success px-4 py-2 shadow">
                        Generate PDF 📄
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>