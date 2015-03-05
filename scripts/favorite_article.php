<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$id = $_GET['id'];

if (!($stmt = $mysqli->prepare(
	"INSERT INTO ctr_user_fav(
		email,
		a_id) 
	VALUES (?, ?)"))) {
	
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("si", $_SESSION['email'], $id)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

if (!$stmt->execute()) {
	echo "Failed to insert article " . $id . ": " . $stmt->error . "<br>";
} else {
	echo "Article " . $_GET['id'] . " was favorited.";
}
?>