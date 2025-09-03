<?php 
session_start();
// set status to invalid
$_SESSION['status'] = 'invalid';

unset($_SESSION['username']);

echo "<script>window.location.href ='login.php'</script>";

?>