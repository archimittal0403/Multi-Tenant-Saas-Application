<?php include('includes/auth.php'); ?>
<?php checkRole('admin'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>

<?php 

$institute_id = $_SESSION['institute_id'] ?? '';

$teacher_id = $_POST['teacher_id'] ?? '';
$teacher_id = mysqli_real_escape_string($con, trim($teacher_id));

$frames = [];

/* ================= VALIDATION ================= */
if(empty($teacher_id)){
    die("Teacher ID missing ❌");
}

/* ================= GET FRAMES ================= */
if(!empty($_POST['photo'])){
    $frames = json_decode($_POST['photo'], true);

    if(!$frames || count($frames) < 2){
        die("Need more frames ❌");
    }

    foreach($frames as $i => $img){
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);

        $file = "temp/live_".time()."_$i.jpg";
        file_put_contents($file, base64_decode($img));
    }
}

/* ================= FETCH TEACHER ================= */
$get = mysqli_query($con, "
SELECT id, Name 
FROM accounts 
WHERE institute_id='$institute_id' 
AND roll_no='$teacher_id'
AND type='teacher'
LIMIT 1
");

if(!$get || mysqli_num_rows($get) == 0){
    die("Teacher not found ❌ (check roll_no)");
}

$teacher = mysqli_fetch_assoc($get);
$tid = $teacher['id'];

/* ================= GET DB PHOTO ================= */
$photo = get_usermeta($tid, 'th_image');

if(empty($photo)){
    die("Teacher photo missing in DB ❌");
}

$path = "uploads/teacher_photo/" . $photo;

if(!file_exists($path)){
    die("DB image file not found ❌");
}

/* ================= CURL TO PYTHON ================= */
if(!empty($frames)){

$postData = [
    "frames" => $frames
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5001/compare");
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$postFields = [
    "frames" => json_encode($frames),
    "db" => new CURLFile($path)
];

curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

$response = curl_exec($ch);

if($response === false){
    die("CURL Error: " . curl_error($ch));
}

curl_close($ch);

$result = json_decode($response, true);
// echo "<pre>";
// print_r($result);
// exit;
// echo "<pre>";
// print_r($result);
// exit;
// if ($result === null) {
//     echo "<h3>Raw Python Response</h3>";
//     echo "<pre>";
//     var_dump($response);
//     echo "</pre>";
//     exit;
// }

    /* ================= LIVENESS CHECK ================= */
    if(isset($result['liveness']) && !$result['liveness']){
        die("Blink your eyes 👀");
    }

    /* ================= MATCH CHECK ================= */
    $match = false;

    if(isset($result['match']) && $result['match'] == true){
        $match = true;
    }

    if(!$match){
        die("Face Not Matched ❌");
    }

    /* ================= INSERT ATTENDANCE ================= */
    $date = date('Y-m-d');

    $check = mysqli_query($con, "
    SELECT id FROM teacher_attendance 
    WHERE teacher_id='$tid' 
    AND attendance_date='$date' 
    AND institute_id='$institute_id'
    ");

    if(mysqli_num_rows($check) == 0){

        mysqli_query($con, "
        INSERT INTO teacher_attendance 
        (teacher_id, institute_id, attendance_date, status, photo)
        VALUES
        ('$tid', '$institute_id', '$date', 'present', '')
        ");
    }

}

/* ================= SUCCESS UI ================= */
?>

<div class="student-card">
    <div class="student-header">
        <h2>Teacher Verified ✔</h2>
        <p>Attendance Marked Successfully</p>
    </div>

    <div class="student-body">
        <div class="student-photo">
            <img src="uploads/teacher_photo/<?php echo $photo; ?>">
        </div>

        <h3><?php echo $teacher['Name']; ?></h3>
    </div>
</div>

<?php ?>