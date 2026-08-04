```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/auth.php');
checkRole('student');

include('includes/config.php');
include('includes/functions.php');

$user_id       = $_SESSION['user_id'];
$institute_id  = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$message  = isset($_POST['message']) 
    ? strtolower(trim($_POST['message'])) 
    : '';

$response = "Sorry, we can not get you the answer";

// =====================================
// HYBRID VARIABLES
// =====================================

if($institute_type == 'college'){

    $course_id = get_usermeta($user_id,'course_name');
    $branch_id = get_usermeta($user_id,'branch_name');
    $semester  = get_usermeta($user_id,'semester');
    $session   = get_usermeta($user_id,'session');

}else{

    $class_id   = get_usermeta($user_id,'st_class');
    $section_id = get_usermeta($user_id,'st_section');
    $session    = get_usermeta($user_id,'session');

}

// =====================================
// FAQ
// =====================================

$select_query = mysqli_query($con,"
SELECT *
FROM chatbot_faq
WHERE status=1
");

while($row_fetch = mysqli_fetch_assoc($select_query)){

    $keywords = explode(',', strtolower($row_fetch['keyword']));

    foreach($keywords as $keyword){

        $keyword = trim($keyword);

        if($keyword != '' && strpos($message,$keyword) !== false){

            $response = $row_fetch['answer'];
            break 2;
        }
    }
}

// =====================================
// COURSE / CLASS
// =====================================

if(
    strpos($message,'course') !== false ||
    strpos($message,'class') !== false
){

    if($institute_type == 'college'){

        $course_res = mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='$course_id'
        ");

        $row_course = mysqli_fetch_assoc($course_res);

        $course_name = $row_course['title'] ?? 'Unknown';

        $response = "You are studying in $course_name course.";

    }else{

        $class_res = mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='$class_id'
        ");

        $row_class = mysqli_fetch_assoc($class_res);

        $class_name = $row_class['title'] ?? 'Unknown';

        $response = "You are studying in Class $class_name.";
    }
}

// =====================================
// SEMESTER / SECTION
// =====================================

if(
    strpos($message,'semester') !== false ||
    strpos($message,'section') !== false
){

    if($institute_type == 'college'){

        $response = "You are in Semester $semester.";

    }else{

        $section_res = mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='$section_id'
        ");

        $row_section = mysqli_fetch_assoc($section_res);

        $section_name = $row_section['title'] ?? 'Unknown';

        $response = "You are in Section $section_name.";
    }
}

// =====================================
// SESSION
// =====================================

if(strpos($message,'session') !== false){

    $response = "Your academic session is $session";
}

// =====================================
// SUBJECTS
// =====================================

if(
    strpos($message,'subject') !== false ||
    strpos($message,'subjects') !== false
){

    if($institute_type == 'college'){

        $subject_res = mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE parent='$branch_id'
        AND type='subject'
        AND institute_id='$institute_id'
        ");

    }else{
$subject_res = mysqli_query($con,"
SELECT title
FROM posts
WHERE class_id='$class_id'
AND section_id='$section_id'
AND type='subject'
AND institute_id='$institute_id'
");

    }

    $subjects = [];

    while($row = mysqli_fetch_assoc($subject_res)){

        $subjects[] = $row['title'];
    }

    if(count($subjects) > 0){

        $response = "Your subjects are: " . implode(", ",$subjects);

    }else{

        $response = "No subjects found.";
    }
}

// =====================================
// ACCOUNT DETAILS
// =====================================

$select = mysqli_query($con,"
SELECT *
FROM accounts
WHERE id='$user_id'
");

$account = mysqli_fetch_assoc($select);

$email   = $account['email'] ?? '';
$roll_no = $account['roll_no'] ?? '';

if(strpos($message,'roll') !== false){

    $response = "Your roll number is $roll_no";
}

if(strpos($message,'email') !== false){

    $response = "Your registered email is $email";
}

// =====================================
// USER META
// =====================================

$user_data = [];

$select_query = mysqli_query($con,"
SELECT *
FROM usermeta
WHERE user_id='$user_id'
");

while($row_fetch=mysqli_fetch_assoc($select_query)){

    $user_data[$row_fetch['meta_key']] = $row_fetch['meta_value'];
}

$dob      = $user_data['dob'] ?? '';
$mobile   = $user_data['mobile'] ?? '';
$address  = $user_data['address'] ?? '';

if(strpos($message,'dob') !== false){

    $response = "Your DOB is $dob";
}

if(strpos($message,'mobile') !== false){

    $response = "Your mobile number is $mobile";
}

if(strpos($message,'address') !== false){

    $response = "Your address is $address";
}

// =====================================
// FEEDBACK
// =====================================

$feedback_check = mysqli_query($con,"
SELECT created_at
FROM student_feedback
WHERE user_id='$user_id'
AND institute_id='$institute_id'
");

if(
    mysqli_num_rows($feedback_check)>0 &&
    strpos($message,'feedback') !== false
){

    $feedback_row = mysqli_fetch_assoc($feedback_check);

    $response = "You already submitted feedback on " . $feedback_row['created_at'];
}

// =====================================
// ACTIVE EXAM
// =====================================

$exam_query = mysqli_query($con,"
SELECT *
FROM exam_type
WHERE status='active'
AND institute_id='$institute_id'
");

$exam_row = mysqli_fetch_assoc($exam_query);

$exam_id    = $exam_row['id'] ?? 0;
$exam_type  = $exam_row['exam_type'] ?? '';
$max_marks  = $exam_row['max_marks'] ?? 0;

if(
    strpos($message,'exam') !== false &&
    strpos($message,'type') !== false
){

    $response = "Current exam is $exam_type";
}

if(
    strpos($message,'max marks') !== false
){

    $response = "Maximum marks are $max_marks";
}

// =====================================
// DATE SHEET
// =====================================

if(
    strpos($message,'datesheet') !== false ||
    strpos($message,'exam date') !== false
){

    if($institute_type == 'college'){

        $datesheet_query = mysqli_query($con,"
        SELECT *
        FROM exam_datesheet
        WHERE exam_id='$exam_id'
        AND course_id='$course_id'
        AND branch_id='$branch_id'
        AND semester_id='$semester'
        AND session_id='$session'
        AND institute_id='$institute_id'
        ");

    }else{

        $datesheet_query = mysqli_query($con,"
        SELECT *
        FROM exam_datesheet
        WHERE exam_id='$exam_id'
        AND class_id='$class_id'
        AND section_id='$section_id'
        AND session_id='$session'
        AND institute_id='$institute_id'
        ");
    }

    $responses = [];

    while($row = mysqli_fetch_assoc($datesheet_query)){

        $subject_id = $row['subject_id'];

        $subject_q = mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='$subject_id'
        ");

        $subject_row = mysqli_fetch_assoc($subject_q);

        $subject_name = $subject_row['title'] ?? '';

        $responses[] =
            $row['exam_date'] . " : " . $subject_name;
    }

    if(count($responses)>0){

        $response =
            "Exam Datesheet:<br><br>" .
            implode("<br>",$responses);

    }else{

        $response = "No datesheet found.";
    }
}

// =====================================
// RESULT
// =====================================

if(
    strpos($message,'result') !== false ||
    strpos($message,'marks') !== false ||
    strpos($message,'percentage') !== false ||
    strpos($message,'grade') !== false
){

    if($institute_type == 'college'){

        $result_query = mysqli_query($con,"
        SELECT
        posts.title as subject_name,
        result_marks.marks

        FROM results

        INNER JOIN result_marks
        ON results.id=result_marks.result_id

        INNER JOIN posts
        ON results.subject_id=posts.id

        WHERE result_marks.student_id='$user_id'
        AND results.course_id='$course_id'
        AND results.branch_id='$branch_id'
        AND results.semester_id='$semester'
        AND results.session_id='$session'
        AND results.exam_id='$exam_id'
        ");

    }else{

        $result_query = mysqli_query($con,"
        SELECT
        posts.title as subject_name,
        result_marks.marks

        FROM results

        INNER JOIN result_marks
        ON results.id=result_marks.result_id

        INNER JOIN posts
        ON results.subject_id=posts.id

        WHERE result_marks.student_id='$user_id'
        AND results.class_id='$class_id'
        AND results.section_id='$section_id'
       AND results.session_id='$session'
        AND results.exam_id='$exam_id'
        ");
    }

    $responses = [];

    $total = 0;
    $count = 0;

    while($row = mysqli_fetch_assoc($result_query)){

        $responses[] =
            $row['subject_name'] .
            " : " .
            $row['marks'];

        $total += $row['marks'];

        $count++;
    }

    $grand_total = $count * $max_marks;

    if($grand_total > 0){

        $percentage =
            ($total / $grand_total) * 100;

    }else{

        $percentage = 0;
    }

    if(count($responses)>0){

        $response =
        "Your Result:<br><br>" .
        implode("<br>",$responses) .
        "<br><br>Total : $total
        <br>Percentage : " .
        round($percentage,2) . "%";

    }else{

        $response = "No result found.";
    }
}

// =====================================
// ATTENDANCE
// =====================================

preg_match('/\d{4}-\d{2}-\d{2}/',$message,$match);

$user_date = $match[0] ?? '';

if(
    strpos($message,'attendance') !== false
){

$attendance_query = mysqli_query($con,"
SELECT
attendance.subject_id,
attendance_meta.status

FROM attendance

INNER JOIN attendance_meta
ON attendance.id=attendance_meta.attendance_id

WHERE attendance.attendance_date='$user_date'
AND attendance_meta.user_id='$user_id'
AND attendance.class_id='$class_id'
AND attendance.section_id='$section_id'
AND attendance.institute_id='$institute_id'
");

    $responses = [];

    while($row = mysqli_fetch_assoc($attendance_query)){

        $subject_id = $row['subject_id'];

        $subject_q = mysqli_query($con,"
        SELECT title
        FROM posts
        WHERE id='$subject_id'
        ");

        $subject_row = mysqli_fetch_assoc($subject_q);

        $subject_name =
            $subject_row['title'] ?? '';

        $responses[] =
            $subject_name .
            " : " .
            $row['status'];
    }

    if(count($responses)>0){

        $response =
            "Attendance on $user_date:<br>" .
            implode("<br>",$responses);

    }else{

        $response = "No attendance found.";
    }
}

// =====================================
// AI FALLBACK
// =====================================

if(trim($response) == "Sorry, we can not get you the answer"){

   $api_key = "YOUR_GROQ_API_KEY";

    $data = [

        "model" => "llama-3.1-8b-instant",

        "messages" => [

            [
                "role" => "system",
                "content" =>
                "You are a helpful ERP assistant."
            ],

            [
                "role" => "user",
                "content" => $message
            ]
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL,
    "https://api.groq.com/openai/v1/chat/completions");

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    curl_setopt($ch,CURLOPT_POST,true);

    curl_setopt($ch,CURLOPT_HTTPHEADER,[

        "Content-Type: application/json",

        "Authorization: Bearer " . $api_key
    ]);

    curl_setopt($ch,CURLOPT_POSTFIELDS,
    json_encode($data));

    $result = curl_exec($ch);

    $response_data = json_decode($result,true);

    if(
        isset(
            $response_data['choices'][0]['message']['content']
        )
    ){

        $response =
        $response_data['choices'][0]['message']['content'];
    }

    curl_close($ch);
}

// =====================================
// SAVE CHAT
// =====================================

$response = mysqli_real_escape_string($con,$response);

$message = mysqli_real_escape_string($con,$message);

mysqli_query($con,"
INSERT INTO chat_messages
(
    user_id,
    message,
    reply,
    institute_id
)
VALUES
(
    '$user_id',
    '$message',
    '$response',
    '$institute_id'
)
");

echo $response;
?>
```
