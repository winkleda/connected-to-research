<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* Queries articles linked to user */
$sql = "SELECT * 
		FROM ctr_article a, ctr_user_article_link u 
		WHERE a.id = u.id AND u.email = '$_SESSION[email]'
		ORDER BY time_issued DESC";
$articles = $mysqli->query($sql);

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

echo json_encode($data);

?>
