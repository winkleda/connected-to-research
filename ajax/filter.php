<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* Queries count statistics for filter */

/* Total articles assigned to user */
$sql = "SELECT count(*) as total
 		FROM ctr_user_article_link
 		WHERE email = '$_SESSION[email]'";
$result = $mysqli->query($sql);
$total = $result->fetch_assoc();

/* Total articles where type is journ */


$data = array(
	array(
		"header" => "Type",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => $total['total']
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
				"amount" => 0
			)
		)
	),
	array(
		"header" => "Date",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => $total['total']
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

