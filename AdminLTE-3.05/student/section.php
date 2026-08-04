<?php include('../includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('../includes/functions.php')?>


<?php 

if(isset($_POST['submit'])){
    $title=$_POST['title'];
    mysqli_query($con,"INSERT INTO section (title) VALUE ('$title')");
}
?>
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Manage Sections :-</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Admin</a></li>
              <li class="breadcrumb-item active">Sections</li>
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
                    Classes
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
                <th>Name</th>
            
                <th>Email</th>
                <th>Action</th>
</tr>
</thread>
  <tbody>

  <?php

  $count=1;
  
          $args = array(
        'type' => 'section',
        'status' =>'publish',
      );
      $sections = get_posts($args);
foreach($sections as $section){ ?>
<tr>
    <td><?=$count++?></td>
    <td><?=$section->title?></td>
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
                Add New section
</h2>
</div>
<div class="card-body">
    <!-- Info boxes -->
     <form action="" method="POST">
      <div class="form-group">
        <h5>Title</h5>
        <input type="text" name="title" id=""  class="px-4 py-1" placeholder="Title" required-class="form-control">
    
      </div>
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
