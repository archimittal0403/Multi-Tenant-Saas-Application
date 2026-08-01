<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
$institute_id = $_SESSION['institute_id'];
?>

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



<div class="main-container">

    <div class="page-header">

        <div class="page-title">
          Mark Teacher Attendance
        </div>

        <div class="page-subtitle">
            Scan and Mark the Attendance
        </div>

    </div>

    <!-- STEP 1 -->

    <div class="step-card">

        <div class="step-title">
            <div class="step-number">1</div>
            Scan ID card
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

</div>  <?php include('footer.php')?>
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
let html5QrCode;
let scannerRunning = false;
let lastScan = "";

function startScanner() {

    if(scannerRunning) return;

    html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: 250
        },
        onScanSuccess
    ).then(() => {
        scannerRunning = true;
    }).catch(err => {
        alert(err);
    });
}

function stopScanner() {
    if(!scannerRunning) return;

    html5QrCode.stop().then(() => {
        html5QrCode.clear();
        scannerRunning = false;
        $('#reader').html('');
    });
}
// ================= FETCH TEACHER ONLY =================
function fetchTeacherAttendance(id)
{
    $.post('fetch-student.php', {
        teacher_id: id
    }, function(res){
        $('#student_data').html(res);
    });
}

// ================= BUTTONS =================
$('#start-btn').click(function(){
    startScanner();
});

$('#stop-btn').click(function(){
setTimeout(() => {
    stopScanner();
}, 300);
});
function onScanSuccess(decodedText)
{
    decodedText = decodedText.trim();

setTimeout(() => {
    lastScan = "";
}, 3000);
    lastScan = decodedText;

    stopScanner();

    let teacher_id = "";

    // ✅ case 1: Teacher|KH104976
    if(decodedText.startsWith("Teacher|") || decodedText.startsWith("TEACHER|"))
    {
        teacher_id = decodedText.split("|")[1];
    }

    // ✅ case 2: TEACHERID:KH104976
    else if(decodedText.startsWith("TEACHERID:"))
    {
        teacher_id = decodedText.replace("TEACHERID:", "").trim();
    }

    else {
        $('#scan_status')
        .show()
        .text("Invalid Teacher QR ❌");
        return;
    }

    $('#scan_status')
    .show()
    .text("Teacher Scanned ✔");

    // 👉 next step: camera open
    openCameraAndMark(teacher_id);
}
function openCameraAndMark(teacher_id)
{
    let video = document.createElement('video');
    let canvas = document.createElement('canvas');

    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {

        video.srcObject = stream;

        // 🔥 YAHAN LAGANA HAI (IMPORTANT)
        video.style.width = "640px";
        video.style.height = "480px";
        video.setAttribute("playsinline", true);

        video.play();

        $('#student_data').html('');
        $('#student_data').append(video);

        setTimeout(() => {

video.onloadedmetadata = function () {

    let w = video.videoWidth;
    let h = video.videoHeight;

    console.log("REAL CAMERA:", w, h);

    // FORCE SAFE SIZE (important)
    canvas.width = 640;
    canvas.height = 480;
};
            let ctx = canvas.getContext('2d');
            let frames = [];
            let count = 0;

            let interval = setInterval(() => {

      let sx = 0;
let sy = 0;


let vw = video.videoWidth;
let vh = video.videoHeight;

let scale = Math.min(canvas.width / vw, canvas.height / vh);

let x = (canvas.width / 2) - (vw / 2) * scale;
let y = (canvas.height / 2) - (vh / 2) * scale;

ctx.clearRect(0, 0, canvas.width, canvas.height);
ctx.drawImage(video, 0, 0, vw, vh, x, y, vw * scale, vh * scale);
         frames.push(canvas.toDataURL('image/jpeg', 0.4));

                count++;

                if(count >= 4)
                {
                    clearInterval(interval);
              video.pause();
video.srcObject = null;
video.remove();

                    markAttendance(teacher_id, JSON.stringify(frames));
                }

            }, 400);

        }, 2000);

    })
    .catch(err => {
        alert("Camera not allowed");
    });
}
function markAttendance(teacher_id, photo)
{
    $.post('fetch-student.php', {
        teacher_id: teacher_id,
        photo: photo
    }, function(res){

        $('#student_data').html(res);

        $('#scan_status')
        .text("Attendance Marked ✔ Present");
          setTimeout(function(){
            window.location.href = "attendance.php";
        }, 4500);
    });
}
</script>