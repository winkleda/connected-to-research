<?php
ini_set('display_errors', 'On');
include 'connection.php';
include 'parse.php'; 

/* Specifies which XML file should be read and inserted into database */
/* Returns array of parsed articles */
$origin = "article1.xml";

$articles = parseXML("../xml_docs/" . $origin, "record");

/* Iterates through each article and adds it to database */
/* NOTE: lang and discipline attributes are not added! */
foreach ($articles as $article) {
	if (!($stmt = $mysqli->prepare(
		"INSERT INTO ctr_article(
			article_id,
			a_date,
			startpage,
			endpage,
			j_issue,
			j_volume, 
			type,
			title,
			keywords,
			reprint, 
			j_name,
			abstract,
			url,
			isbn_issn,
			notes,
			authors,
			availability,
			origin) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {
		
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("iiiiiissssssssssss", 
		$article->id,
		$article->date,
		$article->startpage,
		$article->endpage,
		$article->issue,
		$article->volume,
		$article->type,
		$article->title,  
		$article->keywords,
		$article->reprint, 
		$article->journalfull, 
		$article->abstract, 
		$article->url, 
		$article->isbnorissn,
		$article->notes,
		$article->authors,
		$article->availability,
		$origin)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Failed to insert article " . $article->id . ": " . $stmt->error . "<br>";
	} else {
		echo "Article " . $article->id . " successfully inserted into database.<br>";
	}
}

?>