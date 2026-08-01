<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//$site_url = 'http://localhost/student management/AdminLTE-3.05/';
$site_url = 'http://localhost/student management/AdminLTE-3.05/';
// Skip redirect check on login.php
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page != 'login.php') {
    // Check if user is logged in
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['user_id'])) {
        header("Location: {$site_url}login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $user_type = $_SESSION['user_type'] ?? null;

    // Redirect non-student users if they are in wrong folder
    if ($user_type && $user_type !== 'student') {
        $folder = $user_type; // admin/teacher
        if (strpos($_SERVER['REQUEST_URI'], $folder) === false) {
            header("Location: {$site_url}{$folder}/dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>Admin Dashboard</title>
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<!-- 
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css"> -->

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
 
 
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
