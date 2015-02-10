<?php
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename='citation.enw'");

ini_set('display_errors', 'On');
include 'connection.php';

session_start();

$article_id = 324; /* Dependent on which article click originated from */

/* Queries specificed article */
$sql = "SELECT * 
		FROM ctr_article a
		WHERE a.id = $article_id";
$article = $mysqli->query($sql);

$metadata = $article->fetch_assoc();

$citation = "%0 $metadata[type]
			%T $metadata[title]
			%A $metadata[authors]
			%J $metadata[j_name]
			%V $metadata[j_volume]
			%N $metadata[j_issue]
			%P $metadata[startpage]-$metadata[endpage]
			%@ $metadata[isbn_issn]
			%D $metadata[a_date]";

/* Removes any tabs and prints results to enw file */
$citation = preg_replace('/\t/', '', $citation);
print $citation;
?>