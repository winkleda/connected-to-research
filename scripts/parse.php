<?php
/* Source of parse: http://php.net/manual/en/function.xml-parse-into-struct.php */
ini_set('display_errors', 1);

/* Object for holding metadata of single article */
class Article {
	function Article ($article) {
		foreach($article as $key=>$value)
			$this->$key = $article[$key];
	}
}

/* */
function parseXML($filename) {
	$data = implode("", file($filename));
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $values, $tags);
	xml_parser_free($parser);

	/* Grabs all elements within <record> tag */
	foreach ($tags as $key=>$value) {
		if ($key == "record") {
			$record = $value;
			for ($i = 0; $i < count($record); $i += 2) {
				$offset = $record[$i] + 1;
				$len = $record[$i + 1] - $offset;
				$articles[] = parseRecord(array_slice($values, $offset, $len));
			}
		} else {
			continue;
		}
	}
	return $articles;
}

/* Creates and populates Article object */
function parseRecord($article_info) {
	for ($i = 0; $i < count($article_info); $i++) {
		$article[$article_info[$i]["tag"]] = $article_info[$i]["value"];
	}
	return new Article($article);
}

?>