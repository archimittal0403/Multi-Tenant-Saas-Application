<?php include('includes/auth.php'); ?>
<?php checkRole('student'); ?>
<?php include('includes/config.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('includes/functions.php'); ?>

<?php

$user_id      = $_SESSION['user_id'];
$institute_id = $_SESSION['institute_id'];

// GET STUDENT ADMIT CARDS

$get_admit = $con->prepare("

    SELECT *
    FROM admit_cards

    WHERE student_id=?
    AND institute_id=?

    ORDER BY id DESC

");

$get_admit->bind_param(
    "ii",
    $user_id,
    $institute_id
);

$get_admit->execute();

$admit_result = $get_admit->get_result();

?>

<style>


.admit-wrapper{
    padding:25px;
}

.page-title{
    font-size:32px;
    font-weight:700;
    color:#222;
    margin-bottom:25px;
}

.admit-card-box{
    background:#fff;
    border-radius:16px;
    padding:22px;
    box-shadow:0 3px 15px rgba(0,0,0,0.08);
    margin-bottom:20px;
    transition:0.3s;
}

.admit-card-box:hover{
    transform:translateY(-3px);
}

.exam-title{
    font-size:20px;
    font-weight:700;
    color:#4e73df;
    margin-bottom:15px;
}

.detail-row{
    margin-bottom:10px;
    font-size:14px;
    color:#555;
}

.detail-row strong{
    color:#222;
}

.download-btn{
    margin-top:15px;
    border-radius:10px;
    padding:10px 18px;
    font-weight:600;
}

.no-data{
    background:#fff;
    padding:40px;
    text-align:center;
    border-radius:16px;
    box-shadow:0 3px 15px rgba(0,0,0,0.08);
}

@media(max-width:768px){

    .page-title{
        font-size:24px;
    }

    .admit-card-box{
        padding:16px;
    }

}

</style>





<div class="container-fluid">

<div class="admit-wrapper">

<h2 class="page-title">

<i class="fas fa-id-card text-primary"></i>

My Admit Cards

</h2>

<?php

if($admit_result->num_rows > 0){

    while($row = $admit_result->fetch_assoc()){

?>

<div class="admit-card-box">

<div class="exam-title">
Admit Card
</div>

<div class="detail-row">
<strong>Session :</strong>
<?=$row['session']?>
</div>

<div class="detail-row">
<strong>Expiry Date :</strong>
<?=$row['expiry_date']?>
</div>

<div class="detail-row">
<strong>Generated On :</strong>
<?=date('d M Y',strtotime($row['created_at']))?>
</div>
<?php
$file=$row['pdf_path'];
$file_path="../teacher/".$file;
?>
<a href="<?=$file_path?>"
   target="_blank"
   class="btn btn-primary download-btn">

<i class="fas fa-download"></i>

Download Admit Card

</a>

</div>

<?php
    }

}else{
?>

<div class="no-data">

<h4>No Admit Card Found</h4>

<p class="text-muted">
Your admit card has not been generated yet.
</p>

</div>

<?php } ?>

</div>

</div>


</div>

<?php include('footer.php'); ?>