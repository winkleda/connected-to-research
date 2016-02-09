<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../scripts/connection.php");

class Grants {
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
    $dup = 0;
    for ($i = 0; $i < count($values); $i++) {
        if ($values[$i]["tag"] == $values[$i+1]["tag"]) {
            $Grant[$values[$i]["tag"]][$dup] = $values[$i]["value"];
	    $dup = $dup + 1;
	} elseif ($values[$i]["tag"] == $values[$i-1]["tag"]) {
	    $Grant[$values[$i]["tag"]][$dup] = $values[$i]["value"];
	    $dup = 0;
  	} else {
	    $Grant[$values[$i]["tag"]] = $values[$i]["value"];
	} 
    }
    return new Grants($Grant);
}

function print_db($db) 
{ 
	global $mysqli;
	$name = "Grants";
	foreach($db as $in) {
		$post_date = $in->{'PostDate'}[4].($in->{'PostDate'}[5]).($in->{'PostDate'}[6]).($in->{'PostDate'}[7]).($in->{'PostDate'}[0]).($in->{'PostDate'}[1]).($in->{'PostDate'}[2]).($in->{'PostDate'}[3]);
		$due_date = $in->{'ApplicationsDueDate'}[4].($in->{'ApplicationsDueDate'}[5]).($in->{'ApplicationsDueDate'}[6]).($in->{'ApplicationsDueDate'}[7]).($in->{'ApplicationsDueDate'}[0]).($in->{'ApplicationsDueDate'}[1]).($in->{'ApplicationsDueDate'}[2]).($in->{'ApplicationsDueDate'}[3]);
		
		$base_query = "INSERT INTO ctr_funding_base
		(id,
		 source, 
		 title, 
		 post_date, 
		 due_date, 
		 interests, 
		 agency, 
		 address, 
		 contact, 
		 office, 
		 description, 
		 url) 
		 VALUES (
		 '". $in->{'FundingOppNumber'} ."', 
		 '". $name ."',
		 '". $in->{'FundingOppTitle'} ."', 
		 '". $post_date ."', '". $due_date ."', 
		 '". $in->{'EligibilityCategory'} ."', 
		 '". $in->{'Agency'} ."', 
		 '". $in->{'Location'} ."', 
		 '". $in->{'AgencyContact'} ."', 
		 '". $in->{'Office'} ."', 
		 '". addslashes($in->{'FundingOppDescription'}) ."', 
		 '". $in->{'ObtainFundingOppText'} ."');";
		
		$grant_query = "INSERT INTO ctr_funding_grants
		(opp_number, 
		 due_date_explanation, 
                 funding_total, 
         	 award_ceiling, 
		 award_floor, 
		 category_explanation, 
		 instrument_type, 
		 award_number, 
		 elegibility_category, 
		 eligibility_info, cost_sharing)
		 VALUES (
		 '". $in->{'FundingOppNumber'} ."', 
		 '". $in->{'ApplicationsDueDateExplanation'} ."', 
		 '". $in->{'EstimatedFunding'} ."', 
		 '". $in->{'AwardCeiling'} ."', 
		 '". $in->{'AwardFloor'} ."', 
		 '". $in->{'OtherCategoryExplanation'} ."', 
		 '". $in->{'FundingInstrumentType'} ."', 
		 '". $in->{'NumberOfAwards'} ."', 
		 '". $in->{'EligibilityCategory'} ."', 
		 '". $in->{'AdditionalEligibilityInfo'} ."', 
		 '". $in->{'CostSharing'} ."');";
	
		echo $base_query . "<br>";
		echo $grant_query . "<br><br><br>";
		
		$results = $mysqli->query($base_query);
		if ($results == FALSE)
			echo ("Failed to add to database BASE: " . $mysqli->error . "<br>");
		
		$results = $mysqli->query($grant_query);
		if ($results == FALSE)
			echo ("Failed to add to database GRANT: " . $mysqli->error . "<br>");
	}
}

$db = readXML("test1.xml");
echo "Output of Database for Grant objects:\n";
print_db($db);
//print_r($db);

?>
