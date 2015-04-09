<?php
ini_set('display_errors', 'On');
include 'connection.php';

/* Specifies which XML file should be read and inserted into database */
/* Returns array of parsed articles */

$data = simplexml_load_string($_POST['xml']);
$type = $_POST['type'];

/* Option selected from radio button */
switch ($type) {
	case "articles":
		insert_articles($mysqli, $data);
		break;
	case "calls":
		insert_calls($mysqli, $data);
		break;
}

function insert_articles($mysqli, $articles) {
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
				image_url) 
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
			$article->image_url)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " 
			. $stmt->error;
		}

		if (!$stmt->execute()) {
			echo "Failed to insert article " . $article->id . ": " 
			. $stmt->error . "<br>";
		} else {
			echo "<strong>INSERTED</strong> " . $article->title . "<br>" . 
			"ID: " . $mysqli->insert_id . "<br><br>";
		}
	}
}

function insert_calls($mysqli, $calls) {
	/* Iterates through each call and adds it to database */
	/* NOTE: lang and discipline attributes are not added! */
	foreach ($calls as $call) {
		if (!($stmt = $mysqli->prepare(
			"INSERT INTO ctr_call_for_part(
				p_date,
				title,
				location,
				description) 
			VALUES (?, ?, ?, ?)"))) {
			
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if (!$stmt->bind_param("ssss", 
			$call->when,
			$call->event,
			$call->where,
			$call->desc)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " 
			. $stmt->error;
		}

		if (!$stmt->execute()) {
			echo "Failed to insert call " . $call->event . ": " 
			. $stmt->error . "<br>";
		} else {
			echo "<strong>INSERTED</strong> " . $call->event . "<br>" .
			"ID: " . $mysqli->insert_id . "<br><br>";
		}
	}
}
?>