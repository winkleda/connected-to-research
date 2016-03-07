<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$id = $_GET['id'];

if (!($stmt = $mysqli->prepare(
	"INSERT INTO ctr_user_fav_fund(email, fund_id) 
	VALUES (?, ?)"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("ss", $_SESSION['email'], $id)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmt->execute()) {
	echo "Failed to favorite funding_id " . $id . " for " . $_SESSION['email'] . ": " . $stmt->error . "<br>";
} else {
	echo $_SESSION['email'] . " favorited funding_id " . $id . "<br>";
}
    
?>