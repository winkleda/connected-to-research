<?php
ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$users = explode(",", $_GET['user']);
$id = $_GET['id'];
$type = $_GET['type'];

switch($type) {
    case "article":
        share_article($mysqli, $id, $users);
        break;
    case "funding":
        share_funding($mysqli, $id, $users);
        break;
}

function share_article($mysqli, $article_id, $user_list) {
    foreach($user_list as $user) {
        if (!($stmt = $mysqli->prepare(
            "INSERT INTO ctr_user_share(shared_by, shared_to, a_id) 
            VALUES (?, ?, ?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("ssi", $_SESSION['email'], $user, $article_id)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Failed to share article_id " . $article_id . " for " . $user . ": " . $stmt->error . "<br>";
        } else {
            echo $_SESSION['email'] . " shared article article_id " . $article_id . "<br>";
        }
    }
}

function share_funding($mysqli, $funding_id, $user_list) {
    foreach($user_list as $user) {
        if (!($stmt = $mysqli->prepare(
            "INSERT INTO ctr_user_share_fund(shared_by, shared_to, fund_id) 
            VALUES (?, ?, ?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("sss", $_SESSION['email'], $user, $funding_id)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Failed to share funding_id " . $funding_id . " for " . $user . ": " . $stmt->error . "<br>";
        } else {
            echo $_SESSION['email'] . " shared funding_id " . $funding_id . "<br>";
        }
    }
}
?>