<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

if(!($stmt = $mysqli->prepare(
	"SELECT a_id 
	FROM ctr_user_fav
	WHERE email = '$_SESSION[email]'"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
	echo "Failed to grab favorites: " . $stmt->error . "<br>";
}

$stmt->bind_result($id);

/* Inserts emails of users into array */
$ids = array();
while ($stmt->fetch()) {
    array_push($ids, $id);
}

echo json_encode($ids);
?>