<?php include('includes/config.php')?>
<?php include('./header.php')?>
<?php include('./sidebar.php')?>
<?php include('includes/functions.php')?>



<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
       
          // $std_id=isset($_GET['std_id'])?$_GET['std_id']:''; 
           ?>
       
<div class="card">
  <div class="card-header">
    <h5>Student Details</h5>
  </div>
<div class="card-body">
  <?Php// $student=get_user_data($std_id); ?>
  <?php 
  // $student=get_user_data(array('id'=>$std_id))[0]->Name;
  $first = get_users(array('id'=>$std_id))[0]->Name?>
  <?php
  $second =get_users(array('id'=>$std_id))[0]->email?>
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
        
        print_r(get_usermeta('27','months',true));
          $months=array('january','febrary','march','april','may','june','july','august','september','october','november','december');
          foreach ($months as $key => $value) {
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
              <a href="#" data-toggle="modal" month="" data-target="#paynow-popup" class="btn btn-sm btn-primary ml-3 paynow-btn"><i class="fa fa-money-check-alt fa-fw mr-1"></i>Pay Now</a>
             <a href="?action=pay&month=<?php echo $value ?>&std_id=<?php echo $std_id ?>" class="btn btn-sm btn-primary btn-dark"><i class="fa fa-envelope fa-fw"></i>Send Message</a>
             <a href="?action=pay&month=<?php echo $value ?>&std_id=<?php echo $std_id ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash fa-fw "></i>Delete</a>
            </td>
        </tr>
          
          <?php } ?>
        </tbody>
        
        </table>
        </div>
        </div>
  
       
   </div>    
    </section>

    <?php

// merchant key provides by the payu
$MERCHANT_KEY="ofaS7w";


// merchant salt is provides by the payu
$SALT="BinAlMXk7nSnRxe79uJQEgWtPKQQKMKp";

//$MERCHANT_KEY="";
//$SALT="";
    $PAYU_BASE_URL="https://sandboxsecure.payu.in";


    $action='';
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
     $hash = '';

   $posted = array();
     if(!empty($_POST)) {
         // print_r($_POST);die;
       foreach($_POST as $key => $value) {    
        $posted[$key] = $value; 
            
        }
     }

    $formError = 0;

     if(empty($posted['txnid'])) {
    //     // Generate random transaction id
       $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    } else {
      $txnid = $posted['txnid'];
     }
     $hash = '';
    // Hash Sequence
    // what is udf1 is the user define feild
    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
    if(empty($posted['hash']) && sizeof($posted) > 0) {
        if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
          || empty($posted['service_provider'])
        ) {
            $formError = 1;
        } else {
            //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
            $hashVarsSeq = explode('|', $hashSequence);
            $hash_string = '';	
            foreach($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
            }

            $hash_string .= $SALT;


            $hash = strtolower(hash('sha512', $hash_string));
            $action = $PAYU_BASE_URL . '/_payment';
        }
    } 
    elseif(!empty($posted['hash'])) 
    {
        $hash = $posted['hash'];
        $action = $PAYU_BASE_URL . '/_payment';
    }
    
    ?>
    <script>
     var hash = '<?php echo $hash ?>';
     function submitPayuForm() {
       if(hash == '') {
         return;
       }
       var payuForm = document.forms.payuForm;
       payuForm.submit();
     }
  
  </script> 
    <div class="modal fade" id="paynow-popup" tabindex="-1" role="dialog" aria-labelledby="paynow-popup" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paynow-popupLabel">PayNow</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
     <form action="<?php echo $action ?>" method="post" name="payuForm">
    
       <input type="hidden" name="key"  value="<?php echo $MERCHANT_KEY?>" />
                        <input type="hidden" name="hash"  value="<?php echo $hash?>" />
                        <input type="hidden" name="txnid"  value="<?php echo $txnid?>" />
                <input type="hidden" name="amount" value="50">
      <input type="hidden" name="surl" value="http://localhost/studentmanagement/actions/success.php" size="64">
      <input type="hidden" name="furl" value="http://localhost/studentmanagement/actions/failure.php" sixe="64">
      <input type="hidden" name="service_provider" value="payu_paisa" size="64">
      <input type="hidden" name="productinfo" value="Fee Payment">
<div class="row">
  <div class="col-lg-6">
    <div class="form-group">
<!-- as in this readony name is used when the user can view/accesed the information but cannot change the data or either information by the user. -->
<label for="firstname">Full Name</label>
<input type="text"  name="firstname"  readonly class="form-control"  autocomplete="given-name" id="firstname" value="<?php echo ucwords($first) ?> ">
</div>
</div>
  <div class="col-lg-6">
    <div class="form-group">
<label for="email">Email Address</label>
<input type="email" name="email"  readonly class="form-control" autocomplete="given-email" id="email" value="<?php echo ucwords($second) ?>"/>
</div>
  </div>
  <div class="col-lg-6">
    <div class="form-group">
<label for="phone">Phone</label>
<input type="text" name="phone"  readonly class="form-control"  autocomplete="off" id="phone" value="1234589654"/>
</div>
  </div>
  <div class="col-lg-6">
    <div class="form-group">
<label for="month">Months</label>
<input type="text" class="form-control"  name="udf1" id="month" value="August"/>
</div>
  </div>
      <div class="col-lg-6">
                                <div class="form-group">
                               
                                    <h3><i class="fa fa-rupee-sign"></i> 500.00</h3>
                                </div>
                            </div>

  <div class="col-lg-6">
    <button type="submit" class="btn btn-success">Confirm & Pay </button>
        </div>
</div>
     </form>
      </div>
     
    </div>
  </div>
</div>

<script>

  jQuery(document).on('click', '.paynow-btn',function(){
 var month=jQuery(this).data('month');
 jQuery('month').val(month);
   })
  
  </script>



<?php include('footer.php')?>
