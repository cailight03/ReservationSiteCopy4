<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the index.php page
header("Location: /ReservationSiteCopy4/login.php");
exit();
?>
