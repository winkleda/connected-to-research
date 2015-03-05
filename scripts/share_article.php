<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$users = explode(",", $_GET['user']);
$id = $_GET['id'];

foreach($users as $user) {
	if (!($stmt = $mysqli->prepare(
		"INSERT INTO ctr_user_share(shared_by, shared_to, a_id) 
		VALUES (?, ?, ?)"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmt->bind_param("ssi", $_SESSION['email'], $user, $id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	if (!$stmt->execute()) {
		echo "Failed to share id " . $id . " for " . $user . ": " . $stmt->error . "<br>";
	} else {
		echo $_SESSION['email'] . " shared article id " . $id . "<br>";
	}
}

?>