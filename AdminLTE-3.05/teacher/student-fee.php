<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>



<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Student Fee Details:-</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Admin</a></li>
              <li class="breadcrumb-item active">Student Fee Details</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

     <!-- Main content -->
     <section class="content">
      <div class="container-fluid">
        <?php 
        if(isset($_GET['action']) && $_GET['action'] =='view'){
           $std_id=isset($_GET['std_id'])?$_GET['std_id']:''; 
           ?>
       
<div class="card">
  <div class="card-header">
    <h5>Student Details</h5>
  </div>
<div class="card-body">
  <?php 
  $first = get_users(array('id'=>$std_id))[0]->Name?>
<strong>
  Student Name: </strong> <?php echo ucwords($first) ?> </br>
  <strong>
  Class: </strong><?php echo get_user_metadata($std_id)['st_class'] ?> </br>

    <strong>
  Mobile NO: </strong><?php echo get_user_metadata($std_id)['mobile'] ?>
        </div>
        <div class="card-header">
          Fee Details
        </div>
  <div class="card-body">
<table class="table table-bordered">
  <thead>
<tr>
  <th>S.NO</th>
  <th>Month</th>
  <th>Fee Status</th>
    <th>Action</th>
        </tr>
        </thead>
        <tbody>
          
          <?php 
        
          $month=array('january','febrary','march','april','may','june','july','august','september','october','november','december');
          foreach ($month as $key => $value) {
         // this is a property of date  used in the php where F tell us about the current month .
         
            $highlight='';
          if(date('F') == ucwords($value)){
$highlight ='class="bg-success"';
          } ?>
            <tr <?php echo $highlight ?> >
            <td><?php echo ++$key ?></td>
            <td><?php echo ucwords($value) ?></td>
            <td></td>
            <td>
              <!-- // that we can use the bootstrap property inside our font-awesome -->
                <a href="?action=pay&month=<?php echo $value ?>&std_id=<?php echo $std_id ?>" class="btn btn-sm btn-warning ml-5"><i class="fa fa-eye fa-fw "></i>View</a>
              <a href="?action=pay&month=<?php echo $value ?>&std_id=<?php echo $std_id ?>" class="btn btn-sm btn-primary ml-3"><i class="fa fa-money-check-alt fa-fw mr-1"></i>Pay Now</a>
             <a href="?action=pay&month=<?php echo $value ?>&std_id=<?php echo $std_id ?>" class="btn btn-sm btn-primary btn-dark"><i class="fa fa-envelope fa-fw"></i>Send Message</a>
             <a href="?action=pay&month=<?php echo $value ?>&std_id=<?php echo $std_id ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash fa-fw "></i>Delete</a>
            </td>
        </tr>
          
          <?php } ?>
        </tbody>
        
        </table>
        </div>
        </div>
    <?php    }  else { ?>
       <table class="table table-bordered">
        <thead>
          <th class="text-center">S.NO</th>
          <th class="text-center">Student Name</th>
          <th class="text-center">Last Payment</th>
              <th class="text-center">Due Payment</th>
                <th class="text-center">Payment Status</th>
                  <th class="text-center">Action</th>
                  
</thead>
<tbody>

  <?php  
  
  $students=get_users(array('type'=>'student'));
  foreach($students as $key => $students){ ?>

  
   
  <tr>
  <td><?php echo ++$key ?></td>
    <td><?php echo $students->Name ?></td>
  
            <td></td>
            <td></td>
           
            <td>
              <a href="?action=view?>" class="btn btn-sm btn-info"><i class="fa fa-eye fa-fw"></i>Pay Now</a>
              <!-- <a href="" class="btn btn-xs btn-dark"><i class="fa fa-pencil-alt fa-fw"></i></a> -->
            </td>
</tr>
<?php } ?>
</tbody>
<?php } ?>
</table>
   </div>



       
    </section>
<?php include('footer.php')?>
