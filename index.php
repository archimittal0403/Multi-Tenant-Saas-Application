<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
include('includes/config.php');
include('header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IRISERP - Smart Education ERP</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;700&display=swap"
          rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Space Grotesk',sans-serif;
}

html{
    scroll-behavior:smooth;
}

body{
    background:#030712;
    color:white;
    overflow-x:hidden;
    position:relative;
}

/* BACKGROUND */

.bg-grid{
    position:fixed;
    width:100%;
    height:100%;
    background-image:
    linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
    background-size:40px 40px;
    z-index:-5;
}

body::before{
    content:'';
    position:fixed;
    width:500px;
    height:500px;
    background:#2563eb;
    border-radius:50%;
    filter:blur(160px);
    top:-200px;
    left:-150px;
    opacity:.35;
    z-index:-3;
}

body::after{
    content:'';
    position:fixed;
    width:500px;
    height:500px;
    background:#9333ea;
    border-radius:50%;
    filter:blur(160px);
    bottom:-200px;
    right:-150px;
    opacity:.35;
    z-index:-3;
}

a{
    text-decoration:none;
}

/* NAVBAR */

.navbar{
    background:rgba(3,7,18,.65);
    backdrop-filter:blur(20px);
    border-bottom:1px solid rgba(255,255,255,.08);
    padding:18px 0;
}

.navbar-brand{
    font-size:34px;
    font-weight:700;
    color:white !important;
}

.navbar-brand span{
    color:#38bdf8;
}

.nav-link{
    color:#cbd5e1 !important;
    margin-left:24px;
    font-weight:500;
    transition:.3s;
    position:relative;
}

.nav-link::after{
    content:'';
    position:absolute;
    width:0%;
    height:2px;
    background:#38bdf8;
    left:0;
    bottom:-5px;
    transition:.3s;
}

.nav-link:hover::after{
    width:100%;
}

.nav-link:hover{
    color:#38bdf8 !important;
}

.login-btn{
    background:linear-gradient(135deg,#2563eb,#9333ea);
    color:white;
    padding:13px 30px;
    border-radius:14px;
    font-weight:600;
    transition:.3s;
    display:inline-block;
    box-shadow:0 10px 30px rgba(37,99,235,.35);
}

.login-btn:hover{
    transform:translateY(-4px);
    color:white;
}

/* HERO */

.hero{
    min-height:100vh;
    display:flex;
    align-items:center;
    padding-top:100px;
}

.hero-tag{
    display:inline-flex;
    align-items:center;
    gap:10px;
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.08);
    padding:10px 18px;
    border-radius:50px;
    color:#38bdf8;
    margin-bottom:30px;
}

.hero h1{
    font-size:88px;
    font-weight:700;
    line-height:1;
    letter-spacing:-3px;
}

.hero h1 span{
    background:linear-gradient(to right,#38bdf8,#9333ea);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

.hero p{
    color:#94a3b8;
    font-size:18px;
    line-height:2;
    margin-top:30px;
    max-width:650px;
}

.hero-btns{
    margin-top:45px;
}

.btn-main{
    background:linear-gradient(135deg,#2563eb,#9333ea);
    color:white;
    padding:18px 40px;
    border-radius:16px;
    display:inline-block;
    font-weight:600;
    margin-right:18px;
    transition:.3s;
    box-shadow:0 12px 40px rgba(37,99,235,.35);
}

.btn-main:hover{
    transform:translateY(-5px);
    color:white;
}

.btn-glass{
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.08);
    color:white;
    padding:18px 40px;
    border-radius:16px;
    display:inline-block;
    transition:.3s;
}

.btn-glass:hover{
    background:rgba(255,255,255,.08);
    color:white;
}

/* DASHBOARD */

.dashboard-box{
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.08);
    border-radius:30px;
    padding:30px;
    backdrop-filter:blur(20px);
    box-shadow:0 20px 80px rgba(0,0,0,.45);
    position:relative;
    overflow:hidden;
}

.dashboard-box::before{
    content:'';
    position:absolute;
    width:180px;
    height:180px;
    background:#2563eb;
    filter:blur(100px);
    top:-60px;
    right:-60px;
    opacity:.4;
}

.small-card{
    background:#0f172a;
    border-radius:24px;
    padding:24px;
    margin-bottom:20px;
    position:relative;
    z-index:2;
}

.small-card p{
    color:#94a3b8;
}

.small-card h2{
    font-size:42px;
    font-weight:700;
}

.analytics-img{
    width:100%;
    border-radius:20px;
}

.chart-line{
    width:100%;
    height:10px;
    background:#1e293b;
    border-radius:30px;
    overflow:hidden;
    margin-top:15px;
}

.chart-fill{
    width:92%;
    height:100%;
    background:linear-gradient(to right,#38bdf8,#9333ea);
}

/* SECTION */

.section{
    padding:120px 0;
}

.section-title{
    text-align:center;
    margin-bottom:80px;
}

.section-title h2{
    font-size:60px;
    font-weight:700;
}

.section-title p{
    color:#94a3b8;
    margin-top:18px;
}

/* FEATURE */

.feature-card{
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.07);
    border-radius:28px;
    padding:40px;
    transition:.4s;
    height:100%;
    position:relative;
    overflow:hidden;
}

.feature-card::before{
    content:'';
    position:absolute;
    width:120px;
    height:120px;
    background:#9333ea;
    filter:blur(80px);
    top:-50px;
    right:-50px;
    opacity:.25;
}

.feature-card:hover{
    transform:translateY(-12px);
    border-color:#38bdf8;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
}

.feature-icon{
    width:80px;
    height:80px;
    border-radius:22px;
    background:linear-gradient(135deg,#2563eb,#9333ea);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:32px;
    margin-bottom:28px;
}

.feature-card h4{
    font-size:26px;
    margin-bottom:15px;
}

.feature-card p{
    color:#94a3b8;
    line-height:1.9;
}

/* MODULE */

.module-card{
    background:linear-gradient(145deg,#0f172a,#111827);
    border-radius:28px;
    padding:40px 25px;
    text-align:center;
    transition:.35s;
    border:1px solid rgba(255,255,255,.06);
    position:relative;
    overflow:hidden;
}

.module-card::before{
    content:'';
    position:absolute;
    width:100%;
    height:4px;
    background:linear-gradient(to right,#38bdf8,#9333ea);
    top:0;
    left:0;
}

.module-card:hover{
    transform:translateY(-10px) scale(1.03);
}

.module-card i{
    font-size:46px;
    margin-bottom:20px;
    color:#38bdf8;
}

.module-card h5{
    font-size:24px;
}

/* PORTAL */

.portal-card{
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.06);
    border-radius:30px;
    padding:45px;
    text-align:center;
    transition:.4s;
    overflow:hidden;
    position:relative;
}

.portal-card:hover{
    transform:translateY(-12px);
    border-color:#38bdf8;
}

.portal-card i{
    font-size:70px;
    margin-bottom:25px;
    background:linear-gradient(to right,#38bdf8,#9333ea);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

.portal-card h4{
    font-size:30px;
    margin-bottom:15px;
}

.portal-card p{
    color:#94a3b8;
    margin-bottom:25px;
}

/* FOOTER */

footer{
    background:#020617;
    padding:100px 0 30px;
    border-top:1px solid rgba(255,255,255,.06);
}

.footer-logo{
    font-size:46px;
    font-weight:700;
}

.footer-logo span{
    color:#38bdf8;
}

.footer-social a{
    width:55px;
    height:55px;
    background:#0f172a;
    border-radius:50%;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    margin-left:10px;
    color:white;
    transition:.3s;
}

.footer-social a:hover{
    background:linear-gradient(135deg,#2563eb,#9333ea);
    transform:translateY(-5px);
}

.copyright{
    margin-top:60px;
    padding-top:25px;
    border-top:1px solid rgba(255,255,255,.06);
    color:#94a3b8;
}

/* RESPONSIVE */

@media(max-width:991px){

    .hero{
        text-align:center;
        padding-top:140px;
    }

    .hero h1{
        font-size:58px;
    }

    .dashboard-box{
        margin-top:60px;
    }

    .section-title h2{
        font-size:42px;
    }

}

</style>

</head>

<body>

<div class="bg-grid"></div>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg fixed-top">

    <div class="container">

        <a class="navbar-brand" href="#">
            IRIS<span>ERP</span>
        </a>

        <button class="navbar-toggler bg-light"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#modules">Modules</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#portal">Portals</a>
                </li>

                <li class="nav-item ms-lg-4 mt-3 mt-lg-0">

                    <?php if(isset($_SESSION['login'])){ ?>

                        <a href="AdminLTE-3.05/admin/dashboard.php"
                           class="login-btn">

                            Dashboard

                        </a>

                    <?php } else { ?>

                        <a href="login.php"
                           class="login-btn">

                            Login

                        </a>

                    <?php } ?>

                </li>

            </ul>

        </div>

    </div>

</nav>

<!-- HERO -->

<section class="hero">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <div class="hero-tag">
                    🚀 NEXT GENERATION EDUCATION ERP
                </div>

                <h1>
                    Smart
                    <span>Digital ERP</span>
                    Platform
                </h1>

                <p>
                    IRISERP helps schools, colleges and universities automate
                    admissions, attendance, examinations, fees, analytics,
                    staff management and complete academic operations
                    from one intelligent cloud platform.
                </p>

                <div class="hero-btns">

                    <a href="login.php" class="btn-main">
                        Get Started
                    </a>

                    <a href="#features" class="btn-glass">
                        Explore Features
                    </a>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="dashboard-box">

                    <div class="row">

                        <div class="col-6">

                            <div class="small-card">

                                <p>Total Students</p>

                                <h2>25K+</h2>

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="small-card">

                                <p>Institutions</p>

                                <h2>450+</h2>

                            </div>

                        </div>

                    </div>

                    <div class="small-card">

                        <h5 class="mb-4">
                            Analytics Dashboard
                        </h5>

                        <img src="https://cdn-icons-png.flaticon.com/512/2620/2620277.png"
                             class="analytics-img">

                    </div>

                    <div class="small-card">

                        <div class="d-flex justify-content-between">

                            <span>Attendance Tracking</span>

                            <span class="text-info">
                                92%
                            </span>

                        </div>

                        <div class="chart-line">
                            <div class="chart-fill"></div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- FEATURES -->

<section class="section" id="features">

    <div class="container">

        <div class="section-title">

            <h2>Advanced ERP Features</h2>

            <p>
                Powerful cloud based tools for modern institutions
            </p>

        </div>

        <div class="row g-4">

            <?php

            $features = [

                ['fa-user-graduate','Student Management'],
                ['fa-calendar-check','Attendance Tracking'],
                ['fa-money-bill-wave','Fees Management'],
                ['fa-file-lines','Exam & Results'],
                ['fa-chalkboard-user','Teacher Portal'],
                ['fa-table-list','Timetable'],
                ['fa-chart-line','Analytics'],
                ['fa-user-shield','Role Access']

            ];

            foreach($features as $f){

            ?>

            <div class="col-lg-3 col-md-6">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="fa <?= $f[0] ?>"></i>
                    </div>

                    <h4><?= $f[1] ?></h4>

                    <p>
                        Smart automation and intelligent management
                        system with secure cloud accessibility.
                    </p>

                </div>

            </div>

            <?php } ?>

        </div>

    </div>

</section>

<!-- MODULES -->

<section class="section" id="modules">

    <div class="container">

        <div class="section-title">

            <h2>ERP Modules</h2>

            <p>
                Complete ecosystem for institutions
            </p>

        </div>

        <div class="row g-4">

            <?php

            $modules = [

                'Admissions',
                'Library',
                'Transport',
                'Hostel',
                'Online Classes',
                'Certificates',
                'HR & Payroll',
                'Notifications'

            ];

            foreach($modules as $m){

            ?>

            <div class="col-lg-3 col-md-6">

                <div class="module-card">

                    <i class="fa fa-cube"></i>

                    <h5><?= $m ?></h5>

                </div>

            </div>

            <?php } ?>

        </div>

    </div>

</section>

<!-- PORTALS -->

<section class="section" id="portal">

    <div class="container">

        <div class="section-title">

            <h2>Access Portals</h2>

            <p>
                Secure role based login system
            </p>

        </div>

        <div class="row g-4">

            <?php

            $portals = [

                ['fa-user-shield','Admin Portal'],
                ['fa-chalkboard-user','Teacher Portal'],
                ['fa-user-graduate','Student Portal'],
                ['fa-users','Parent Portal']

            ];

            foreach($portals as $p){

            ?>

            <div class="col-lg-3 col-md-6">

                <div class="portal-card">

                    <i class="fa <?= $p[0] ?>"></i>

                    <h4><?= $p[1] ?></h4>

                    <p>
                        Secure dashboard access with modern UI.
                    </p>

                    <a href="login.php" class="btn-main">
                        Login
                    </a>

                </div>

            </div>

            <?php } ?>

        </div>

    </div>

</section>

<!-- FOOTER -->

<footer>

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <div class="footer-logo">
                    IRIS<span>ERP</span>
                </div>

                <p class="mt-4 text-secondary">
                    Smart cloud ERP platform for schools,
                    colleges and universities.
                </p>

            </div>

            <div class="col-lg-6 text-lg-end mt-5 mt-lg-0">

                <div class="footer-social">

                    <a href="#">
                        <i class="fab fa-linkedin"></i>
                    </a>

                    <a href="#">
                        <i class="fab fa-github"></i>
                    </a>

                    <a href="#">
                        <i class="fab fa-youtube"></i>
                    </a>

                    <a href="#">
                        <i class="fab fa-instagram"></i>
                    </a>

                </div>

            </div>

        </div>

        <div class="copyright text-center">

            © 2026 IRISERP —
            All Rights Reserved

        </div>

    </div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
include('footer.php');
?>