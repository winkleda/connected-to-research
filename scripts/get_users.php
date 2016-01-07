<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

if(!($stmt = $mysqli->prepare(
	"SELECT email 
	FROM ctr_user
	WHERE email <> '$_SESSION[email]'"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
	echo "Failed to grab users: " . $stmt->error . "<br>";
}

$stmt->bind_result($name);

/* Inserts emails of users into array */
$names = array();
while ($stmt->fetch()) {
    array_push($names, $name);
}

echo json_encode($names);
?>