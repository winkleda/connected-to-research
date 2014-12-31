<?php
ini_set('display_errors', 1);

$xml = simplexml_load_file("example1.xml") or die("Error: Cannot create object");

foreach($xml->children() as $entry) { 
	echo $entry->id . ", "; 
	echo $entry->title . ", "; 
	echo $entry->authors . "<br><br>";
}

?>