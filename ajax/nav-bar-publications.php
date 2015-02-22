<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* Queries articles linked to user */
$sql = "SELECT * 
		FROM ctr_article
		LIMIT 3";
$articles = $mysqli->query($sql);

$data = array();
while ($article = $articles->fetch_assoc()) {
	$authors = explode(";", $article['authors']);
	$data[] = array(
		"publication" => $article['j_name'],
		"title" => $article['title'],
		"authors" => $authors,
		"abstract" => $article['abstract'],
		"imageSrc" => "./img/article_img/test-image-1.jpg"

	);
}


echo json_encode($data);
