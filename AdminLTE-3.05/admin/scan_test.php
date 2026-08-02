<?php
echo "PHP IS WORKING";
exit;
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>

<?php
$institute_id = $_SESSION['institute_id'];
?>

<!DOCTYPE html>
<html>

<head>

    <title>QR Exam Attendance</title>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:#f4f7fc;
            font-family:Arial, Helvetica, sans-serif;
        }

        .main-container{
            width:95%;
            max-width:1400px;
            margin:30px auto;
        }

        .page-header{
            margin-bottom:30px;
        }

        .page-title{
            font-size:34px;
            font-weight:bold;
            color:#1e293b;
        }

        .page-subtitle{
            color:#64748b;
            margin-top:8px;
            font-size:15px;
        }

        .step-card{
            background:#fff;
            border-radius:20px;
            padding:25px;
            margin-bottom:25px;
            box-shadow:0 5px 20px rgba(0,0,0,0.06);
        }

        .step-title{
            display:flex;
            align-items:center;
            gap:12px;
            font-size:24px;
            font-weight:bold;
            color:#0f172a;
            margin-bottom:25px;
        }

        .step-number{
            width:40px;
            height:40px;
            background:#2563eb;
            color:#fff;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:18px;
            font-weight:bold;
        }

        .scanner-layout{
            display:grid;
            grid-template-columns:380px 1fr;
            gap:30px;
            align-items:start;
        }

        .scanner-box{
            border:3px dashed #cbd5e1;
            border-radius:20px;
            padding:20px;
            background:#f8fafc;
            text-align:center;
        }


        .scan-text{
            margin-top:15px;
            color:#475569;
            font-size:15px;
        }

        .success-box{
            background:#dcfce7;
            color:#166534;
            padding:15px;
            border-radius:12px;
            font-weight:bold;
            margin-bottom:20px;
            display:none;
        }

        .student-card{
            background:#fff;
            border:1px solid #e2e8f0;
            border-radius:20px;
            overflow:hidden;
        }

        .student-header{
            background:#eff6ff;
            padding:18px 25px;
            font-size:20px;
            font-weight:bold;
            color:#1d4ed8;
            border-bottom:1px solid #dbeafe;
        }

        .student-body{
            padding:25px;
        }

        .student-top{
            display:flex;
            gap:25px;
            align-items:center;
            margin-bottom:25px;
        }

        .student-photo img{
            width:130px;
            height:130px;
            object-fit:cover;
            border-radius:15px;
            border:4px solid #e2e8f0;
        }

        .student-name{
            font-size:30px;
            font-weight:bold;
            color:#0f172a;
        }

        .student-email{
            margin-top:8px;
            color:#64748b;
            font-size:16px;
        }

        .details-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:18px;
        }

        .detail-box{
            background:#f8fafc;
            border-radius:15px;
            padding:18px;
            border:1px solid #e2e8f0;
        }

        .detail-label{
            color:#64748b;
            font-size:13px;
            margin-bottom:8px;
        }

        .detail-value{
            font-size:18px;
            font-weight:bold;
            color:#0f172a;
        }

        .exam-box{
            margin-top:20px;
        }

        .photo-box{
            margin-top:20px;
        }

        .capture-btn{
            background:#2563eb;
            color:#fff;
            border:none;
            padding:14px 28px;
            border-radius:12px;
            font-size:16px;
            font-weight:bold;
            cursor:pointer;
            margin-top:15px;
        }

        .submit-btn{
            width:100%;
            background:#16a34a;
            color:#fff;
            border:none;
            padding:18px;
            border-radius:15px;
            font-size:20px;
            font-weight:bold;
            cursor:pointer;
            margin-top:30px;
        }

        .submit-btn:hover{
            background:#15803d;
        }

        @media(max-width:900px){

            .scanner-layout{
                grid-template-columns:1fr;
            }

        }
.scanner-btn{

    border:none;
    padding:14px 22px;
    border-radius:12px;
    font-size:15px;
    font-weight:bold;
    cursor:pointer;
    margin-top:15px;
    margin-right:10px;
}

.start-btn{
    background:#2563eb;
    color:#fff;
}

.stop-btn{
    background:#ef4444;
    color:#fff;
}
    </style>

</head>

<body>

<div class="main-container">

    <div class="page-header">

        <div class="page-title">
            QR Exam Attendance System
        </div>

        <div class="page-subtitle">
            Scan student admit card and mark attendance instantly
        </div>

    </div>

    <!-- STEP 1 -->

    <div class="step-card">

        <div class="step-title">
            <div class="step-number">1</div>
            Scan Admit Card
        </div>

        <div class="scanner-layout">

            <!-- LEFT -->

            <div class="scanner-box">

                <div id="reader"></div>

                <div class="scan-text">
                    Point camera toward student QR code
                </div>
<br>

<button id="start-btn" class="scanner-btn start-btn">
    Start Scanning
</button>

<button id="stop-btn" class="scanner-btn stop-btn">
    Stop Scanning
</button>
            </div>

            <!-- RIGHT -->

            <div>

                <div class="success-box" id="scan_status">
                    Admit Card Scanned Successfully
                </div>

                <div id="student_data"></div>

            </div>

        </div>

    </div>

</div>

<script>

let html5QrCode;

let scannerRunning = false;
let currentRollNo = null;
let currentExam = null;
let currentSubject = null;

function onScanSuccess(decodedText)
{
    console.log("RAW QR:", decodedText);

    // 🔥 SCANNER STOP
    stopScanner();

    decodedText = decodedText.trim();

    // ================= STUDENT =================
    if(decodedText.startsWith("ROLLNO:"))
    {
        currentRollNo = decodedText.replace("ROLLNO:", "").trim();

        $('#scan_status')
        .show()
        .text("Student QR Scanned");

        loadStudentData();
    }

    // ================= EXAM =================
    else if(decodedText.startsWith("EXAM|"))
    {
        currentExam = decodeURIComponent(
            decodedText.split("|")[1]
        );

        $('#scan_status')
        .show()
        .text("Exam Selected: " + currentExam);

        if(currentRollNo)
        {
            loadStudentData();
        }
    }

    // ================= SUBJECT =================
    else if(decodedText.startsWith("SUBJECT|"))
    {
        currentSubject = decodeURIComponent(
            decodedText.split("|")[1]
        );

        $('#scan_status')
        .show()
        .text("Subject Selected: " + currentSubject);

        if(currentRollNo)
        {
            loadStudentData();
        }
    }
}
function loadStudentData()
{
    $.post('fetch-student.php',{

        roll_no: currentRollNo,
        exam: currentExam,
        subject: currentSubject

    },function(res){

        $('#student_data').html(res);

    });
}
function onScanFailure(error)
{
    // ignore scan errors
}

async function startScanner()
{
    if(scannerRunning) return;

    try {
        html5QrCode = new Html5Qrcode("reader");

        await html5QrCode.start(
            { facingMode: "environment" },   // 🔥 THIS IS KEY FIX
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess
        );

        scannerRunning = true;

    } catch(err) {
        console.log("SCAN ERROR:", err);
        alert("Camera issue: " + err);
    }
}

async function stopScanner()
{

    if(!scannerRunning)
    {
        return;
    }

    try{

        await html5QrCode.stop();

        await html5QrCode.clear();

        scannerRunning = false;

        $('#reader').html('');

    }
    catch(err)
    {
        console.log(err);
    }
}

$('#start-btn').click(function(){

    startScanner();

});

$('#stop-btn').click(function(){

    stopScanner();

});

</script>


</body>
</html>