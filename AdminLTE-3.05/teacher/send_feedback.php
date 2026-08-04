<?php

header('Content-Type: application/json');

require_once 'email.php'; // 👈 IMPORTANT

$name      = $_POST['name'] ?? '';
$email     = $_POST['email'] ?? '';
$institute = $_POST['institute'] ?? '';
$rating    = $_POST['rating'] ?? '';
$message   = $_POST['message'] ?? '';

$to = "archimittalpkw@gmail.com";

$subject = "New ERP Feedback Received";

$body = "
<b>New ERP Feedback</b><br><br>

<b>Name:</b> $name <br>
<b>Email:</b> $email <br>
<b>Institute:</b> $institute <br>
<b>Rating:</b> $rating / 5 <br><br>

<b>Message:</b><br>$message
";

// ✅ PHPMailer function call
$result = send_otp($to, $subject, $body);

echo json_encode([
    "status" => $result,
    "message" => $result ? "Sent successfully" : "Mail failed"
]);