<?php include('includes/config.php')?>

<?php 
if(isset($_POST['submit']))
{
$title=isset($_POST['title'])?$_POST['title']:'';
$from=isset($_POST['from'])?$_POST['from']:'';
$to=isset($_POST['to'])?$_POST['to']:'';
$status='publish';
$type='period';
$date_add= date('Y-m-d H:i:s');


$query=$con->prepare("INSERT INTO `posts` (`title`,`status`,`publish_date`,`type`) VALUE(?,?,?,?)");
$query->bind_param("ssis",$title,$status,$date_add,$type);
$query->execute();


if($query){
  // it use  to return the id which is generated in   the insert whuch is $query 
  $item_id=mysqli_insert_id($con);
}
$stmt=$con->prepare("INSERT INTO `metadata` (`meta_key`,`meta_value`,`item_id`) VALUES(?,?,?)");
$meta_key='from';
$stmt->bind_param("ssi",$meta_key,$from,$item_id);
$stmt->execute();
$meta_key='to';
$stmt->bind_param("ssi",$meta_key,$to,$item_id);
$stmt->execute();
$stmt->close();
header('Location: ../admin/dashboard.php');
}
?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>


<?php 

// if(isset($_POST['submit'])){
//     $title=$_POST['title'];
//     mysqli_query($con,"INSERT INTO section (title) VALUE ('$title')");
// }
?>
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Manage period :-</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Admin</a></li>
              <li class="breadcrumb-item active">periods</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

     <!-- Main content -->
     <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
      
             <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    period
</h2>
<div class="card-tools">
 
</div>
</div>
<div class="card-body">
        <!-- Info boxes -->
        <div class="table-responsive">
          <table class="table table-bordered">
            <thread>
              <tr>
                <th>S.No</th>
                <th>Title</th>
            
                <th>From</th>
                <th>To</th>

</tr>
</thread>
  <tbody>

  <?php

  $count=1;
  
          $args = array(
        'type' => 'period',
        'status' =>'publish',
      );
      $periods = get_posts($args);
foreach($periods as $period){ 
   $from=get_metadata($period->id,'from')[0]->meta_value;
 $to=get_metadata($period->id,'to')[0]->meta_value;
  ?>
<tr>
    <td><?=$count++?></td>
    <td><?=$period->title?></td>
    <td><?php echo date('h:i A',strtotime($from)) ?> </td>
    <td><?php echo date('h:i A',strtotime($to)) ?></td>
<?php } ?>
</td>
</td></td>
</tr>
</tbody>
</table>
</div>
</div>     
        </div> 
        <!-- /.row -->


      </div><!--/. container-fluid -->


<div class="col-lg-4">
<div class="card">
        <div class="card-header">
            <h2 class="card-title">
                Add New period :
</h2>
</div>
<div class="card-body">
    <!-- Info boxes -->
     <form action="" method="POST">
      <div class="form-group">
        <h5>Title -></h5>
        <input type="text" name="title" id=""   placeholder="Title" required-class="form-control">
    
    
      </div>

     
    <!-- Info boxes -->
     <form action="" method="POST">
      <div class="form-group">
        <h5>From -></h5>
        <input type="time" name="from" id=""   placeholder="From" required-class="form-control">
    
  
      </div>
      
    <!-- Info boxes -->
     <form action="" method="POST">
      <div class="form-group">
        <h5>To -></h5>
        <input type="time" name="to" id=""    placeholder="To" required-class="form-control">
    
     
      </div>

     


      <button name="submit" class="btn btn-success ">
        Submit
   </button>
      </form>

</div>     
    </div> 
   </div>



       
    </section>
<?php include('footer.php')?>
