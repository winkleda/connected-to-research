<?php
//require 'password.php';

$mysqli = new mysqli("mysql.cs.orst.edu", "cs440_dovzhika", "8899", "cs440_dovzhika");

if($mysqli->connect_errno) {
	echo "Connection failure: " . $mysqli->connect_error;
}
?>