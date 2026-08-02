<?php
include('includes/auth.php');
checkRole('admin');

include('includes/config.php');
include('includes/functions.php');

include('header.php');
include('sidebar.php');

$institute_id = $_SESSION['institute_id'];
// get institute name

$institute_name=mysqli_query($con,"SELECT name FROM `institutes` WHERE id='$institute_id'");
$row_name=mysqli_fetch_assoc($institute_name);
$name=$row_name['name'];
$result = mysqli_query($con, "SELECT COUNT(*) AS total FROM accounts WHERE institute_id='$institute_id' AND type='student'");
$data = mysqli_fetch_assoc($result);

$totalStudents = $data['total'] ?? 0;

$pricePerStudent = 3;

$monthlyAmount = $totalStudents * $pricePerStudent;

$halfYearOriginal = $monthlyAmount * 6;
$halfYearDiscount = ($halfYearOriginal * 3) / 100;
$halfYearFinal = $halfYearOriginal - $halfYearDiscount;

$yearlyOriginal = $monthlyAmount * 12;
$yearlyDiscount = ($yearlyOriginal * 5) / 100;
$yearlyFinal = $yearlyOriginal - $yearlyDiscount;
?>

<style>
body{
    background: #f6f7fb;
}

/* PAGE HEADER */
.page-title{
    text-align:center;
    padding:30px 10px;
}
.page-title h1{
    font-weight:700;
    color:#1f2937;
}
.page-title p{
    color:#6b7280;
}

/* PRICING WRAPPER */
.pricing-wrapper{
    display:flex;
    justify-content:center;
    gap:25px;
    flex-wrap:wrap;
    padding:20px;
}

/* CARD */
.pricing-card{
    background:#fff;
    width:360px;
    border-radius:18px;
    padding:25px;
    transition:0.3s;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
    position:relative;
    border:1px solid #eee;
}

.pricing-card:hover{
    transform:translateY(-10px);
    box-shadow:0 20px 50px rgba(0,0,0,0.12);
}

/* POPULAR BADGE */
.badge-top{
    position:absolute;
    top:15px;
    right:15px;
    background:#4f46e5;
    color:white;
    font-size:12px;
    padding:5px 10px;
    border-radius:20px;
}

/* TITLE */
.plan-name{
    font-size:22px;
    font-weight:700;
    margin-bottom:5px;
}

.plan-sub{
    color:#6b7280;
    font-size:13px;
}

/* PRICE */
.price{
    font-size:34px;
    font-weight:800;
    margin:15px 0;
}

.old-price{
    color:#9ca3af;
    text-decoration:line-through;
}

/* FEATURES */
.features{
    margin-top:15px;
    text-align:left;
    font-size:14px;
    color:#374151;
}

.features div{
    margin-bottom:8px;
}

/* BUTTON */
.btn-pay{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    margin-top:20px;
}

.btn-green{
    background:#10b981;
    color:white;
}

.btn-blue{
    background:#4f46e5;
    color:white;
}

/* HIGHLIGHT CARD */
.highlight{
    border:2px solid #4f46e5;
    transform:scale(1.03);
}
</style>

<div class="page-title">
    <h1>Choose Your Plan</h1>
    <p>Simple pricing based on number of students</p>
</div>

<div class="pricing-wrapper">

    <!-- HALF YEAR -->
    <div class="pricing-card">

        <div class="plan-name">Half Year</div>
        <div class="plan-sub">6 Months Access</div>

        <div class="price">₹<?php echo number_format($halfYearOriginal,2); ?></div>

        <div class="features">
            <div>✔ Students: <?php echo $totalStudents; ?></div>
            <div>✔ Per Student: ₹<?php echo $pricePerStudent; ?></div>
            <div>✔ Duration: 6 Months</div>
            <div>✔ Basic Support</div>
        </div>

       <button class="btn btn-success btn-lg btn-block"
onclick="payNow(<?= $halfYearFinal ?>)">
    Pay Now
</button>
    </div>

    <!-- YEARLY (HIGHLIGHTED) -->
    <div class="pricing-card highlight">

        <div class="badge-top">Most Popular</div>

        <div class="plan-name">Yearly</div>
        <div class="plan-sub">12 Months Access</div>

        <div class="price">
            ₹<?php echo number_format($yearlyFinal,2); ?>
        </div>

        <div class="old-price">
            ₹<?php echo number_format($yearlyOriginal,2); ?>
        </div>

        <div class="features">
            <div>✔ Students: <?php echo $totalStudents; ?></div>
            <div>✔ Per Student: ₹<?php echo $pricePerStudent; ?></div>
            <div>✔ Save ₹<?php echo number_format($yearlyDiscount,2); ?></div>
            <div>✔ Priority Support</div>
        </div>

       <button class="btn-pay btn-blue"
onclick="payNow(<?= $yearlyFinal ?>)">
    Pay Now
</button>

    </div>

</div>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php include('footer.php'); ?>
<script>
function payNow(amount){

    var options = {
        "key": "rzp_test_T52AaTtp9oeyI5",
        "amount": amount * 100, // paise
        "currency": "INR",
        "name": "<?= $name ?>",
        "description": "Subscription Payment",
handler: function (response) {

    console.log(response);

    fetch("payment_success.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "payment_id=TEST123456&amount=" + amount
    });

    alert("Test Success");


        },
        "theme": {
            "color": "#4f46e5"
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();
}
</script>