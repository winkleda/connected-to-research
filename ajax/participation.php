<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* User currently logged in */
$email = $_SESSION['email'];

/* Queries calls for participation linked to current user */
$query = "SELECT * 
		FROM ctr_user_call_link c, ctr_call_for_part p
		WHERE c.p_id = p.p_id AND c.email = ?
		ORDER BY start_date ASC";

$stmt = $mysqli->stmt_init();
if(!$stmt->prepare($query)) {
	echo "Prepared failed: " . $stmt->error;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$calls = $stmt->get_result();

while ($call = $calls->fetch_assoc()) {
	$data[] = array(
		"title" => $call['title'],
		"date" => $call['p_date'],
		"location" => $call['location'],
		"description" => $call['description'],
		"id" => $call['p_id']
	);
}

$stmt->close();
$mysqli->close();

echo json_encode($data);
?>
