<?php
//header("Content-type: text/plain");
//header("Content-Disposition: attachment; filename='enw.enw'");

ini_set('display_errors', 'On');
include 'connection.php';

session_start();


/* Uncomment this line after GET variable is set!! */
//$article_id = $_POST['id'];

$article_id = 324; /* Temp article */

/* Queries specificed article */
$sql = "SELECT * 
		FROM ctr_article a
		WHERE a.id = $article_id";
$article = $mysqli->query($sql);

$metadata = $article->fetch_assoc();

/* Splits up mulitple authors */
$author_array = explode(";", $metadata['authors']);
$authors = "";
foreach($author_array as $author) {
	$authors .= "%A " . $author . "<br>"; 
}

$enw = "%0 $metadata[type]<br>" .
			"%T $metadata[title]<br>" .
			$authors .
			"%J $metadata[j_name]<br>" .
			"%V $metadata[j_volume]<br>" .
			"%N $metadata[j_issue]<br>" .
			"%P $metadata[startpage]-$metadata[endpage]<br>" .
			"%@ $metadata[isbn_issn]<br>" .
			"%D $metadata[a_date]";

/* Removes any tabs and prints results to enw file */
$enw = preg_replace('/\t/', '', $enw);

/* Create MLA citation for journal article */
if ($metadata['type'] == 'JOUR') {
	$mla_journ = 
		str_replace(";", ", ", $metadata['authors']) . 
		"\"$metadata[title].\" " . 
		"<i>$metadata[j_name]</i> " .
		"$metadata[j_volume]." . 
		"$metadata[j_issue]." . 
		"($metadata[a_date]): " .
		"$metadata[startpage]-$metadata[endpage]. " .
		"Print.";
}

$data = array("enw" => $enw, "mla_journ" => $mla_journ);
echo json_encode($data);
?>