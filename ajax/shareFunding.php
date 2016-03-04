<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

$users = explode(",", $_GET['user']);
$id = $_GET['id'];

foreach($users as $user) {
	if (!($stmt = $mysqli->prepare(
		"INSERT INTO ctr_user_share_fund(shared_by, shared_to, fund_id) 
		VALUES (?, ?, ?)"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
    
	if (!$stmt->bind_param("sss", $_SESSION['email'], $user, $id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
    
	if (!$stmt->execute()) {
		echo "Failed to share funding_id " . $id . " for " . $user . ": " . $stmt->error . "<br>";
	}
    
    else {
		echo $_SESSION['email'] . " shared funding_id " . $id . "successfully. <br>";
	}
}

?>