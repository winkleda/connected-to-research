<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* Queries articles linked to user */
$sql = "SELECT * 
		FROM ctr_article a, ctr_user_article_link u 
		WHERE a.origin = u.origin AND u.email = '$_SESSION[email]'
		LIMIT 5";
$articles = $mysqli->query($sql);

// structure of the data to be sent to the client
//
// <this will be an array of publication items>
// [ <you can put as many items as you want into this array, but thy need too folow the same structure that will be given for this one item >
//		{
//			"tagList" : [ < an array of the tags for that specific article > ];
//			"publication" : < the publication that the item is from >;
//			"title" : < the title for the publication item >;
//			"userInterest" : < how many users are interested in the item > ;
//			"authors" : [ < an array of the athors of the publicaiton item > ] ;
//			"abstract" : < the abstract for the article > ;
//			"imageSrc" : < the source of the image that will go with the article if there is one>;
//		}
// ]
//

$data = array();
while ($article = $articles->fetch_assoc()) {
	$keywords = explode(";", $article['keywords']);
	$authors = explode(";", $article['authors']);

	$data[] = array(
		"tagList" => $keywords,
		"publication" => $article['j_name'],
		"title" => $article['title'],
		"userInterest" => 22,
		"authors" => $authors,
		"abstract" => $article['abstract'],
		"imageSrc" => "./img/article_img/test-image-1.jpg"
	);
}

echo json_encode($data);

?>
