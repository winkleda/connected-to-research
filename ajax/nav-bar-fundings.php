<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();
$email = $_SESSION['email'];
//
$sql = "SELECT *
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND email = '$_SESSION[email]'
		AND due_date >= CURDATE()
		ORDER BY post_date DESC
		LIMIT 3";

$fundings = $mysqli->query($sql);

$data = array();
while ($funding = $fundings->fetch_assoc()) {
	$data[] = array(
		"title" => $funding['title']
	);
}

echo json_encode($data);
