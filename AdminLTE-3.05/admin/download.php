<?php

ob_start();

session_start();

error_reporting(0);

include('includes/config.php');

if(!isset($_SESSION['user_id'])){
    die("Unauthorized Access");
}

if(!isset($_GET['file']) || empty($_GET['file'])){
    die("Invalid File");
}

$file = basename($_GET['file']);

$file_path = __DIR__ . '/uploads/assignment/' . $file;

if(!file_exists($file_path)){
    die("File Not Found");
}

$file_size = filesize($file_path);

ob_clean();

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$file.'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Accept-Ranges: bytes');
header('Content-Length: ' . $file_size);

flush();

readfile($file_path);

exit;
?>