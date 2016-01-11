<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$email = $_POST['email'];
$password = $_POST['password'];

if (!($stmt = $mysqli->prepare("SELECT * FROM ctr_user WHERE email = ? AND password = ?"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("ss", $email, $password)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
} 
$stmt->store_result();

/* Checks to see if email and password exist in db */
if ($stmt->num_rows == 1) {
	$_SESSION['email'] = $email;
	echo "success";
} else {
	echo "Your email or password is invalid.";
}
$stmt->close();
?>