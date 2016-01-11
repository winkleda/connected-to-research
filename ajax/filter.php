<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* User currently logged in */
$email = $_SESSION['email'];

$stmt = $mysqli->stmt_init();

/* Total recommended articles assigned to user */
$num_recommended = "SELECT count(*) as count
 		FROM ctr_user_article_link
 		WHERE email = ?";

/* Total articles favorited by user */
$num_favorited = "SELECT count(*) as count
 		FROM ctr_user_fav
 		WHERE email = ?";

/* Total articles shared to user*/
$num_shared = "SELECT count(*) as count
 		FROM ctr_user_share
 		WHERE shared_to = ?";

/* Total articles from 2015 recommended to user */
$num_at2015 = "SELECT count(*) as count
		FROM ctr_article a, ctr_user_article_link u 
		WHERE a.a_date = 2015 AND a.id = u.id AND u.email = ?";

/* Total articles from 2014 recommended to user */
$num_at2014 = "SELECT count(*) as count
		FROM ctr_article a, ctr_user_article_link u 
		WHERE a.a_date = 2014 AND a.id = u.id AND u.email = ?";

/* Total articles before 2014 recommended to user */
$num_pre2014 = "SELECT count(*) as count
		FROM ctr_article a, ctr_user_article_link u 
		WHERE a.a_date < 2014 AND a.id = u.id AND u.email = ?";

$categories = array("recommended" => $num_recommended, 
					"favorited" => $num_favorited,
					"shared" => $num_shared,
					"at2015" => $num_at2015,
					"at2014" => $num_at2014,
					"pre2014" => $num_pre2014); 
$count_value = array();

/* Executes above queries and stores count values in array */
foreach($categories as $key => $query) {
	
	if(!$stmt->prepare($query)) {
		echo "Prepared failed: " . $stmt->error;
	}

	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$columns = $result->fetch_assoc();
	$count_value[$key] = $columns['count'];
}

$data = array(
	array(
		"header" => "Type",
		"items" => array(
			array(
				"groupItem" => "Recommended",
				"amount" => $count_value['recommended'],
				"filterName" => "recommended"
			),
			array(
				"groupItem" => "Journal Articles",
				"amount" => 0,
				"filterName" => "journalArticle"
			),
			array(
				"groupItem" => "Cited Work",
				"amount" => 0,
				"filterName" => "citedWork"
			),
			array(
				"groupItem" => "Favorited",
				"amount" => $count_value['favorited'],
				"filterName" => "favorited"
			),
			array(
				"groupItem" => "Shared",
				"amount" => $count_value['shared'],
				"filterName" => "shared"
			)
		)
	),
	array(
		"header" => "Date",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => $count_value['recommended'],
				"filterName" => "recommended"
			),
			array(
				"groupItem" => "From 2015",
				"amount" => $count_value['at2015'],
				"filterName" => "at2015"
			),
			array(
				"groupItem" => "From 2014",
				"amount" => $count_value['at2014'],
				"filterName" => "at2014"
			),
			array(
				"groupItem" => "Before 2014",
				"amount" => $count_value['pre2014'],
				"filterName" => "pre2014"
			)
		)
	),
);

$stmt->close();
$mysqli->close();

// encodes the $data varible to json and then echos it back to the client
echo json_encode($data);
?>

