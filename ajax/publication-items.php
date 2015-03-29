<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* User currently logged in */
$email = $_SESSION['email'];

/* Specifies type of articles to retrieve*/
//$type = $_GET['type'];
$type = "recommended";

switch($type) {
	case "recommended":
		$query = "SELECT * 
				FROM ctr_article a, ctr_user_article_link u 
				WHERE a.id = u.id AND u.email = ?
				ORDER BY time_issued DESC";
		break;
	case "favorited":
		$query = "SELECT *
				FROM ctr_article a, ctr_user_fav u
				WHERE a.id = u.a_id AND u.email = ?
				ORDER BY time_issued DESC";
		break;
	case "shared":
		$query = "SELECT *
				FROM ctr_article a, ctr_user_share u
				WHERE a.id = u.a_id AND u.shared_to = ?
				ORDER BY time_issued DESC";
		break;
	default:
		$query = "SELECT * 
				FROM ctr_article a, ctr_user_article_link u 
				WHERE a.id = u.id AND u.email = ?
				ORDER BY time_issued DESC";
}

$stmt = $mysqli->stmt_init();
if(!$stmt->prepare($query)) {
	echo "Prepared failed: " . $stmt->error;
}

$stmt->bind_param("s", $email);
$stmt->execute();

$articles = $stmt->get_result();

$data = array();
while ($article = $articles->fetch_assoc()) {
	$keywords = explode(";", $article['keywords']);
	$authors = explode(";", $article['authors']);

	$data[] = array(
		"tagList" => $keywords,
		"publication" => $article['j_name'],
		"title" => $article['title'],
		"userInterest" => 0,
		"authors" => $authors,
		"abstract" => $article['abstract'],
		"articleID" => $article['id'],
		"imageSrc" => "./img/article_img/test-image-1.jpg"
	);
}

$stmt->close();
$mysqli->close();

echo json_encode($data);
?>
