<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$id = $_GET['id'];
$email = $_SESSION['email'];

if (!($stmt = $mysqli->prepare(
	"INSERT INTO ctr_user_fod_link(fund_id, email) 
	VALUES (?, ?)"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("ss", $id, $email)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

if (!$stmt->execute()) {
	echo "Failed to insert funding deadline " . $id . ": " . $stmt->error . "<br>";
}
else {
	echo "funding deadline " . $id . " successfully inserted into database.<br>";
}

?>
