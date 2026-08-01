<?php
$con = mysqli_connect('127.0.0.1', 'root', '', 'sms_projects', 3306);

if (!$con) {
    die('Connection failed: ' . mysqli_connect_error());
}

?>