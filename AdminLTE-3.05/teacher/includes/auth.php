<?php
session_start();

// Login Check
if(empty($_SESSION['user_id']) || empty($_SESSION['user_type'])){
    header("Location: ../login.php");
    exit;
}

// Role Check
function checkRole($roles){

    // अगर string आया है तो array बना दो
    if(!is_array($roles)){
        $roles = [$roles];
    }

    // Session role
    $userRole = strtolower($_SESSION['user_type']);

    // सारे roles lowercase कर दो
    $roles = array_map('strtolower', $roles);

    // Check
    if(!in_array($userRole, $roles)){
        header("Location: ../login.php");
        exit;
    }
}
?>