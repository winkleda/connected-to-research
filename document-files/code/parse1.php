    while($xml->read())
    {
        // Read each element, skipping any that aren't interesting
        if(!($xml->nodeType == XMLReader::ELEMENT)
        || (!in_array($xml->name, $types)))
            continue;

        // echo "<br><br>&nbsp;&nbsp;&nbsp;" . $xml->name . '<br>';
        $node = new SimpleXMLElement($xml->readOuterXML());

        echo "NOTICE TYPE: " . clean_string($xml->name, $mysqli) . ". SOLNBR: "
        . $node->SOLNBR . "<br>";
    ...
