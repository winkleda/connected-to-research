<?php
ini_set('display_errors', 'On');
include 'connection.php';
include 'parse.php'; 

/* Specifies which XML file should be read and inserted into database */
/* Returns array of parsed articles */
$articles = parseXML("xml_docs/article1.xml", "record");

/* Iterates through each article and adds it to database */
/* NOTE: lang and discipline attributes are not added! */
foreach ($articles as $article) {
	if (!($stmt = $mysqli->prepare(
		"INSERT INTO ctr_article(
			article_id,
			startpage,
			endpage,
			j_issue,
			j_volume, 
			type,
			title,
			a_date,
			keywords,
			reprint, 
			j_name,
			abstract,
			url,
			isbn_issn,
			notes,
			authors,
			availability) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {
		
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("iiiiissssssssssss", 
		$article->id,
		$article->startpage,
		$article->endpage,
		$article->issue,
		$article->volume,
		$article->type,
		$article->title, 
		$article->date, 
		$article->keywords,
		$article->reprint, 
		$article->journalfull, 
		$article->abstract, 
		$article->url, 
		$article->isbnorissn,
		$article->notes,
		$article->authors,
		$article->availability)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Failed to insert article " . $article->id . ": " . $stmt->error . "<br>";
	} else {
		echo "Article " . $article->id . " successfully inserted into database.<br>";
	}
}

?>