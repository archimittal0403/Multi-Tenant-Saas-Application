<?php
session_start();
if(!isset($_SESSION['login'])){
header("Location: ../login.php");
exit;
}
// role check function

function checkRole($type){
    if($_SESSION['user_type']!=$type){
header("Location: ../login.php");
exit;
    }
}
?>