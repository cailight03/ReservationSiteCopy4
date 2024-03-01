<?php
	require 'constants.php';
	
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if (!$connection) { die("no connection made."); }

?>