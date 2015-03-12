<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* Queries count statistics for filter */

/* Total recommended articles assigned to user */
$sql = "SELECT count(*) as count
 		FROM ctr_user_article_link
 		WHERE email = '$_SESSION[email]'";
$result = $mysqli->query($sql);
$total = $result->fetch_assoc();

/* Total articles favorited by user */
$sql = "SELECT count(*) as count
 		FROM ctr_user_fav
 		WHERE email = '$_SESSION[email]'";
$result = $mysqli->query($sql);
$favorite = $result->fetch_assoc();


/* Total articles shared to user*/
$sql = "SELECT count(*) as count
 		FROM ctr_user_share
 		WHERE shared_to = '$_SESSION[email]'";
$result = $mysqli->query($sql);
$shared = $result->fetch_assoc();

$data = array(
	array(
		"header" => "Type",
		"items" => array(
			array(
				"groupItem" => "Recommended",
				"amount" => $total['count']
			),
			array(
				"groupItem" => "Journal Articles",
				"amount" => 0
			),
			array(
				"groupItem" => "Cited Work",
				"amount" => 0
			),
			array(
				"groupItem" => "Favorited",
				"amount" => $favorite['count']
			),
			array(
				"groupItem" => "Shared",
				"amount" => $shared['count']
			)
		)
	),
	array(
		"header" => "Date",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => $total['count']
			),
			array(
				"groupItem" => "Since 2015",
				"amount" => 0
			),
			array(
				"groupItem" => "Since 2014",
				"amount" => 0
			),
			array(
				"groupItem" => "Before 2014",
				"amount" => 0
			)
		)
	),
);

// encodes the $data varible to json and then echos it back to the client
echo json_encode($data);

?>

