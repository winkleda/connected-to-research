<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

$users = explode(",", $_GET['user']);
$article_ids =explode(",", $_GET['id']);

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

?>