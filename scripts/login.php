<?php
ini_set('display_errors', 'On');

/* Create file that creates connnection to database */
//include 'connection.php';

session_start();

$email = $_POST['email'];
$password = $_POST['password'];

$_SESSION['email'] = $email;

if (!($stmt = $mysqli->prepare("SELECT * FROM User WHERE email = ? AND password = ?"))) {
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
	echo "success";
} else {
	echo "Your email or password is invalid.";
	session_unset($_SESSION['email']); 
	session_destroy(); 
}
$stmt->close();
?>