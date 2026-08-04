

<?php include('includes/auth.php'); ?>
<?php checkRole('student'); ?>
<?php include('includes/config.php'); ?>

<?php include('includes/functions.php'); ?>

<?php 
$user_id=$_SESSION['user_id'];
?>
<?php
$sql = "SELECT * FROM chat_messages 
WHERE user_id=? 
ORDER BY id ASC";

$stmt = $con->prepare($sql);

$stmt->bind_param("i",$user_id);

$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc())
{?>

<div class="msg-user">
   <?= $row['message'] ?>
</div>

<div class="msg-ai">
   <?= $row['reply'] ?>
</div>
    
<?php } ?>

