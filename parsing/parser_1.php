<?php

class Grants {
    var $PostDate; 
    
    function Grants ($db) 
    {
	//Get each key-value pair/
        foreach ($db as $k=>$v)
            $this->$k = $db[$k];
    }
}

/*
 * Objective: Read XML file. Parse the data into an array.
 * 
 * Functions:
 * implode(separator,array): Returns a string from the elements of an
 *			     array
 *     params: (Optional) separator
 *             (Required) array: The array to join a string
 * file(path,include_path,context): Reads file into an array. Each array
 *				    Each array element contains a line 
 *				    from the file, with newline still
 *				    attached.
 *     params: (Required) path: Specifies the file to read
 * xml_parser_into_struct(parser,xml,value_arr,index_arr)
 * Description: Parses XML data into an array.
 *     params: (Required) parser
 *             (Required) xml
 *	       (Required) value_arr: Specifies the target array for the XML
 *				     data.
 *	       (Optional) index_arr: Specifies the target array for index
 *				     data.
 */
function readXML($filename) 
{
    $data = implode("", file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
    xml_parser_free($parser);

    foreach ($tags as $key=>$val) {
        if ($key == "FundingOppSynopsis") {
            $fundRanges = $val;
            for ($i=0; $i < count($fundRanges); $i+=2) {
                $offset = $fundRanges[$i] + 1;
                $len = $fundRanges[$i + 1] - $offset;
                $db[] = parseGrant(array_slice($values, $offset, $len));
            }
        } else {
            continue;
        }
    }
    return $db;
}

function parseGrant($values) 
{
    for ($i = 0; $i < count($values); $i++) {
        $Grant[$values[$i]["tag"]] = $values[$i]["value"];
    }
    return new Grants($Grant);
}

$db = readXML("simple1.xml");
echo "Output of Database for Grant objects:\n";
print_r($db);

?>
