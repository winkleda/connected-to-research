<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];
$stmt = $mysqli->stmt_init();

//grab users
$funding_shareUser = "SELECT *
		FROM ctr_user
		WHERE email <> '$_SESSION[email]' ";

$shareUsersArray = array();

if($stmt->prepare($funding_shareUser)){
	
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result !== false) {
		while($columns = $result->fetch_assoc()) {
			$users = array(
				"email" => $columns["email"],
				"firstName" => $columns["name_f"],
				"lastName" => $columns["name_l"]
			);
			array_push($shareUsersArray, $users);
		}
	}
}

$stmt->close();
$mysqli->close();

// encodes the $data to json and then echos it back to client
echo json_encode($shareUsersArray);
?>
