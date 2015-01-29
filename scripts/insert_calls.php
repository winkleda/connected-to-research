<?php
ini_set('display_errors', 'On');
include 'connection.php';
include 'parse.php'; 

/* Specifies which XML file should be read and inserted into database */
/* Returns array of parsed calls */
$origin = "calls1.xml";

$calls = parseXML("../xml_docs/" . $origin, "cfp");

/* Iterates through each call and adds it to database */
/* NOTE: lang and discipline attributes are not added! */
foreach ($calls as $call) {
	if (!($stmt = $mysqli->prepare(
		"INSERT INTO ctr_call_for_part(
			p_date,
			title,
			location,
			description,
			origin) 
		VALUES (?, ?, ?, ?, ?)"))) {
		
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("sssss", 
		$call->when,
		$call->event,
		$call->where,
		$call->desc,
		$origin)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Failed to insert call " . $call->event . ": " . $stmt->error . "<br>";
	} else {
		echo "call " . $call->event . " successfully inserted into database.<br>";
	}
}

?>