<?php

if(isset($_GET['delete_student'])){

    $delete_id = intval($_GET['delete_student']);

    // DELETE USERMETA
    $meta = $con->prepare("
        DELETE FROM usermeta
        WHERE user_id=?
    ");

    $meta->bind_param("i",$delete_id);
    $meta->execute();

    // DELETE ACCOUNT
    $acc = $con->prepare("
        DELETE FROM accounts
        WHERE id=?
    ");

    $acc->bind_param("i",$delete_id);

    if($acc->execute()){

        $_SESSION['success_msg'] =
        "Student Deleted Successfully";

    } else {

        $_SESSION['success_msg'] =
        "Delete Failed";
    }

    echo "<script>
    window.location='user-account.php?user=student';
    </script>";
}
?>