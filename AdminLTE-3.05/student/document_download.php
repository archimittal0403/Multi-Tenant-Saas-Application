<?php
session_start();
include('includes/config.php');

// check login (optional but recommended)
if(!isset($_SESSION['user_id'])){
    die("Unauthorized access");
}

if(isset($_GET['file'])){

    $file = basename($_GET['file']);
$path = __DIR__ . "/uploads/documents/" . $file;

    if(file_exists($path)){

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Content-Length: ' . filesize($path));

        readfile($path);
        exit;

    }else{
        echo "File not found";
    }

}else{
    echo "Invalid request";
}