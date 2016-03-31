<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$id = $_GET['id'];
$email = $_SESSION['email'];

if (!($stmt = $mysqli->prepare(
	"DELETE FROM ctr_user_fod_link
        WHERE fund_id = ?
        AND email = '$_SESSION[email]' "))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

echo "DELETE FROM ctr_user_fod_link
        WHERE fund_id = ?
        AND email = '$_SESSION[email]' ";

if (!$stmt->bind_param("s", $id)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

if (!$stmt->execute()) {
	echo "Failed to delete funding deadline " . $id . ": " . $stmt->error . "<br>";
} 
else {
	echo "funding deadline " . $id . " successfully deleted from database.<br>";
}

?>
