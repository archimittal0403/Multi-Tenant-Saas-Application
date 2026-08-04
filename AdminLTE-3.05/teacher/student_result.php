<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>



 <div class="content-header">
         <div class="container-fluid">
       
        <div class="row ">

          <div class="col-sm-6">
<div class="d-flex">
            <h1 class="m-0 text-dark"> View Attendance :-</h1>
          
            <!-- <a href="feedback.php?&action=add-new"
   class="btn btn-primary btn-sm mx-4">Fill feedback</a> -->
</div>
</div>
             <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Attendance</a></li>
              <li class="breadcrumb-item active">student</li>
            </ol>
          </div><!-- /.col -->


</div>
</div>
</div>
      <div class="card">
                  <div class="card-body">
                    <form action="std_result.php" method="GET">
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group">
                            <label for="all_id">Enter ID:-</label>
                            <input type="text" id="all_id" name="all_id" placeholder="Enter your ID" class="form-control">
                          </div>
                        </div>
                         <div class="col-lg-6">
                          <div class="form-group">
                            <label for="sem_id">Enter semester:-</label>
                            <input type="text" id="sem_id" name="sem_id" placeholder="Enter your ID" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-6">
                           <div class=" justify-content-end">
                            <button type="submit" class="btn btn-danger">Apply</button>
</div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                
 
