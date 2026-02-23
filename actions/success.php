
<?php include ('../includes/config.php'); ?>
<?php include ('../includes/functions.php'); ?>
<?php 
//session_start();
//require('config.php');
// extract($_REQUEST);
$status=$_POST["status"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"];
$txnid=$_POST["txnid"];
$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];
$month=$_POST["udf1"];
$salt="MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDZ2o5D0k/2uFAIvlHCY8grn0/QoNVEK6c3EaJIQxIoXtwsQTviaa0zFKVTSbq/D7pIRRZsG4dfmDDjyGPyMhh13uQT+h2T8iJPC0nmeibuMjD4poG54eLXlpeLNROoyr7cmZ6rVpJ7mocv88UV0PkCm9qc0BVTBu7NnR12wjJw1b+DtydpISIbicilLKMTWsX3jB/+rnJcyfl/2iBdDYax7bcnesVWkb6QCbwVGjRZ8LBJq7cygJwcxUWP8EOkkT8leQobcIjauWfLgs/JmndTL86oNOqyK0oC+sW9QJjQ+By9yr61EFkzS0+lYd6GqX2xQUmsZ5x2wSKIClehiSyHAgMBAAECggEABL7FdYW5tyqJl78pgb3xqL2WG/m823jF8iWgrslDNnzmd9JN7x8VJjUvarKC7lF14cgbdmxdFQB4JCYDw01DCiI6PpMmnMfb5nPc/revbGXMekYKMlXQvphpoZ8c2ALeiGiSM7I1i5qwkiNRqil8mwlxCBRiOoY+olznPOR6+8kpzgkUEYXkfbM9JIABQhhcwi59nhz9DT4tJMrYczlUOgXbquyxsrPUlpXS2fGZqD989BF9n9NKK5K0NYPL7JE666XjzQjUn5PU5ChtRBN94VT7+Az/hS99piKPSf7Gvrr9hHpRJZMatgNbUTZpcJiVUPh+WkP1cv0DbWiXq1lqrQKBgQDevkXWBFifBGZPqZ+Ejm7eVrsoxVli78j/SbGT6ZJ+uuc3bB6s6Xoy6YzHD4K/t93GzSO1CSqrbxerDSEoY1SRzREh5NU9DRcBuMxQKIP96uWI1JknOQhCOJYnJ3R1p1kNuEO0K/LFJNtur6hL6IQt0IOBXRYcPoL3D0Qy86VgxQKBgQD6YWUaxKUAVsLW6TuQvMM429y/hZ68tgquibruiNxrKdn9ZQjzrl1YG1chACFvdambSVdfzpucxkUAPntHTkz/roU8yHi+89c35XarS/KGh8miziUICSP08BsEKxTncWcP1ZXQXGqNIYYlivrSunn5P+mSX5T7V6ITAbE8oYAU2wKBgAU3ka0sluLKmJbfEzRonaIph+KxlKFjmmKYWEdtnhfHyuiaaLGGGm2c2MZEz7wr0073uRFhcJVpWbWQ7ijjArUTh4YytOfkKZjJukdaW5UT7mJhEGFBzba3WpT6MJkc3VIb8cIDkYEaluUlyhxVCtuD2cCq7Hym3ixpOCC1Yjc9AoGAPMGrhg5Xt7hV+U6JXLrhaIe5jmP/O+20s9vKF53wVrTGJ/3/aRpTnlCsGN7uPhzANmVapW3+RtG55rBCu+/dMWIJBNHksvl6rR6qaDC6vjhvDmmVhNiq+AHXnHBT10Gmb6o1e2UhmfObmLqb2MzVUk1XAWTeQwtl0dTkTEEoUmcCgYBWkFD/XVrYOZEfuvCJAj4GsMMv3x41um7aeDC6J/5uLsoYCgDed/9BJYk4Adoe3S1UoGzqFEaSyNiWnpRMKcLxXFDZQRjqCrGy2pWyw8tRL36oA203qeFa4r6gLN84h5F/lwdn2bxOw4RNer1qDQZyDDYav6Xx60oHo38pT8XRuw==";

$date=date('Y-m-d');
// without :'' it will show an syntax error
$user_id=isset($_SESSION['user_id'])?$_SESSION['user_id']:'';
$query=mysqli_query($con,"INSERT INTO `posts` (`title`,`type`,`publish_date`,`status`,`author`) Values ('$title','payment','$date','$status','$user_id')");

if($query){
    $item_id=mysqli_insert_id($con);
}

$payment_data =array(
    'txt_id'=> $txnid,
    'amount'=> $amount,
    'firstname'=> $firstname,
    'product_info'=> $product_info,
    'status'=> $status 
);

foreach($payment_data as $key => $value){
mysqli_query($con,"INSERT INTO `metadata` (`item_id`,`meta_key`,`meta_value`) Values ('$item_id','$key','$value')");
}
$old_months=get_usermeta($user_id,'months',true);
if($old_months){
//mysqli_query($con,"INSERT INTO `metadata` (`item_id`,`meta_key`,`meta_value`) Values ('$item_id','payment','$months')");
}

else{
    $months=array($month);
    mysqli_query($con,"INSERT INTO `usermeta` (`user_id`,`meta_key`,`meta_value`) Values ('$user_id','months','$month')");
}

if (isset($_POST["additionalCharges"])) 
{
       $additionalCharges=$_POST["additionalCharges"];
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
  } else {
        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
         }
		 $hash = hash("sha512", $retHashSeq);
       
	  
         if(0){
            echo "Invalid transaction please try again";

         }
         else{
            echo "<h3>Thankyou your order status is ". $status .".</h3>";
                echo "<h4> your Transaction Id for this is ". $txnid .".</h4>";
             echo "<h4>We have recevied a payment of RS ". $amount .". your order will soon be snipped.</h4>";

         }
         echo '<pre>';
print_r($_POST);
// 	  // echo $status." ".$txnid;
		
?>