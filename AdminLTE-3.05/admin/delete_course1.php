
<?php include('includes/auth.php');
checkRole('admin');?>

<?php include('includes/config.php')?>
<?php
if(isset($_GET['delete_id'])){
    $delete_id=$_GET['delete_id'];
    $delete_post=mysqli_query($con,"DELETE  FROM posts WHERE id='$delete_id'");
    $delete_meta=mysqli_query($con,"DELETE  FROM metadata WHERE meta_key='duration' AND item_id='$delete_id'");
    $delete_meta=mysqli_query($con,"DELETE  FROM metadata WHERE meta_key='department' AND item_id='$delete_id'");
    if($delete_post || $delete_meta){
          echo "<div class='alert alert-success'>course already addeded Successfully</div>";
 echo "<script>window.location='course1.php?success=1';</script>";
    }
}