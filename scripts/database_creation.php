<?php

$serverName = "localhost";
$userName = "";
$password = "";

$conn = new mysqli($serverName, $userName, $password);

if ($conn->connect_error){
	die("connection failed: " . $conn->connect_error);	
}
echo "Connected Successfully";

?>
