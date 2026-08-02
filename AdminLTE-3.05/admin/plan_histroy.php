<?php
include('includes/auth.php');
checkRole('admin');

include('includes/config.php');
include('includes/functions.php');

include('header.php');
include('sidebar.php');

// institute id (optional filter)
$institute_id = $_SESSION['institute_id'];

// fetch payment history
$query = mysqli_query($con, "
    SELECT * 
    FROM payment_history WHERE institute_id='$institute_id'
    ORDER BY id DESC
");
?>

<div class="container mt-4">

    <h3 class="mb-4">Payment History</h3>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Payment ID</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>Plan Type</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Expiry Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php if(mysqli_num_rows($query) > 0){ ?>

            <?php while($row = mysqli_fetch_assoc($query)){ ?>

                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['transaction_id']; ?></td>
                    <td><?php echo $row['payment_type']; ?></td>
                    <td>₹<?php echo $row['amount']; ?></td>
                    <td><?php echo $row['plan_type'];?></td>

                    <td>
                        <?php if($row['payment_status'] == 'success'){ ?>
                            <span class="badge badge-success">Success</span>
                        <?php } else { ?>
                            <span class="badge badge-danger">Failed</span>
                        <?php } ?>
                    </td>

                    <td>
                        <?php 
                            echo isset($row['payment_date']) 
                            ? $row['payment_date'] 
                            : date('Y-m-d H:i:s'); 
                        ?>
                    </td>
                    <td><?php echo $row['expiry_date'];?></td>

                    <td>
                        <a href="payment_receipt.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-sm btn-primary">
                        Generate Recepit
                        </a>

                        <a href="print_receipt.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-sm btn-success">
                            Print
                        </a>

                    </td>
                </tr>

            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="6" class="text-center">
                    No payment history found
                </td>
            </tr>

        <?php } ?>

        </tbody>
    </table>

</div>

<?php include('footer.php'); ?>