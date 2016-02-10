<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$dbhost = 'oniddb.cws.oregonstate.edu';
$dbname = 'nguyenc5-db';
$dbuser = 'nguyenc5-db';
$dbpass = '61WpUnCnU7pDUl8g';

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if($mysqli->connect_errno) {
	//echo "Connection failure: " . $mysqli->connect_error;
	die('<div class="alert alert-danger" role="alert">Fatal Error: could not connect to database: ' . $mysqli->connect_error . '</div>');
}
//else{
//	echo "Connection Success! <br>";
//}

?>
