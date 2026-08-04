<?php include('includes/config.php')?>
<?php include('./header.php')?>
<?php include('./sidebar.php')?>
<?php include('includes/functions.php')?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">  Manage Attendance:-</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Admin</a></li>
              <li class="breadcrumb-item active">attendance</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="container-fluid">
      
       
<div class="card">
  <div class="card-header">
    <h5>Student Details</h5>
  </div>
<div class="card-body">
  <?Php $student=get_user_data($std_id); ?>
  <?php 
  // $student=get_user_data(array('id'=>$std_id))[0]->Name;
  $first = get_users(array('id'=>$std_id))[0]->Name?>
  <?php
  //$second =get_users(array('id'=>$std_id))[0]->email?>
<strong>
  Student Name: </strong> <?php echo ucwords($first) ?> </br>
  <strong>
   Class: </strong> 5<?php // echo get_user_metadata($std_id)['st_class'] ?> </br> 

    <strong>
  <!-- Mobile NO: </strong><?php //echo get_user_metadata($std_id)['mobile'] ?> -->
        </div>
        <div class="card-header">
          Attendance Status
        </div>
 <div class="card-body">
  <table class="table table-bordered">
    <thead>
      
        <tr>
          
          <th>Date</th>
        <th>Status</th>
        <th>Signed in Time</th>
        <th>Signed Out Time</th>
</tr>

      
    </thead>
    <tbody>
      <?php
      //$current_month= strtolower(date('F'));
      $current_month=strtolower(date('F'));
      //$current_month='september';
      $current_year=date('Y');

      $sql= "SELECT * FROM `attendance` WHERE `attendance_month`='$current_month' AND year(current_session)=$current_year";
      $query=mysqli_query($con,$sql); 

      $row=mysqli_fetch_object($query);
      foreach(unserialize($row->attendance_value) as $date => $value){ ?>
      <tr>
      
        <td><?php echo    $date;?></td>
        <td><?php echo ($value['signin_at']) ?'Present' : 'Absent' ;?></td>
        <td><?php echo ($value['signout_at']) ? date('d-m-y h:i:s', $value['signin_at']) : '' ;?></td>
        <td><?php echo ($value['signout_at']) ?date('d-m-y h:i:s', $value['signout_at']) : '' ; ?></td>
      </tr>
      <?php } ?>
</tbody>
  </table>
</div>
        </div>
  
       
   </div>    
    </section>











<?php include('footer.php')?>