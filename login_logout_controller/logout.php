<?php 
session_start();

session_unset();
session_destroy();

header("Location: ../client_pages/index.php");
?>