<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // not logged in
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['status']) || $_SESSION['status'] == "invalid") {
    $_SESSION['status'] = 'invalid';

    unset($_SESSION['username']);
    session_destroy();

    echo "<script>window.location.href ='login.php'</script>";
    exit; // <-- make sure script stops after redirect
    
}
?>
