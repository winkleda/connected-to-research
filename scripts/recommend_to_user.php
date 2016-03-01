<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

$type = $_GET['type'];
$users = explode(",", $_GET['user']);
$ids = explode(",", $_GET['id']);

switch ($type) {
	case "article":
		assign_articles($mysqli, $ids, $users);
		break;
	case "call":
		assign_calls($mysqli, $ids, $users);
		break;
    case "funding":
        assign_funding($mysqli, $ids, $users);
        break;
}

function assign_articles($mysqli, $article_ids, $users) {
	foreach($users as $user) {
		foreach($article_ids as $article_id) {
			if (!($stmt = $mysqli->prepare("INSERT INTO ctr_user_article_link(email,id) VALUES (?, ?)"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			if (!$stmt->bind_param("ss", $user, $article_id)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			if (!$stmt->execute()) {
				echo "Failed to insert id " . $article_id . " for " . $user . ": " . $stmt->error . "<br>";
			} else {
				echo $user . " assigned article id " . $article_id . "<br>";
			}
		}
	}
}

function assign_calls($mysqli, $call_ids, $users) {
	foreach($users as $user) {
		foreach($call_ids as $call_id) {
			if (!($stmt = $mysqli->prepare("INSERT INTO ctr_user_call_link(email,p_id) VALUES (?, ?)"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			if (!$stmt->bind_param("ss", $user, $call_id)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			if (!$stmt->execute()) {
				echo "Failed to insert id " . $call_id . " for " . $user . ": " . $stmt->error . "<br>";
			} else {
				echo $user . " assigned call id " . $call_id . "<br>";
			}
		}
	}
}

function assign_funding($mysqli, $funding_ids, $users) {
	foreach($users as $user) {
		foreach($funding_ids as $funding_id) {
			if (!($stmt = $mysqli->prepare("INSERT INTO ctr_user_fund_link(email,fund_id) VALUES (?, ?)"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			if (!$stmt->bind_param("ss", $user, $funding_id)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			if (!$stmt->execute()) {
				echo "Failed to insert id " . $funding_id . " for " . $user . ": " . $stmt->error . "<br>";
			} else {
				echo $user . " assigned article id " . $funding_id . "<br>";
			}
		}
	}
}
?>