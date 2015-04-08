<?php
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename='citation.enw'");

$enw = $_GET['enw'];
$enw = str_replace("<br>", "\r\n", $enw);
print $enw;

?>