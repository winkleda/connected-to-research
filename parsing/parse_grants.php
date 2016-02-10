<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../scripts/connection.php");

$db = new XMLReader();
$db->open('test1.xml');

$doc = new DOMDocument;

echo "Output of Database for Grant objects:\n";

//Go to the first grant object
while ($db->read() && $db->name !== 'FundingOppSynopsis');

while ($db->name == 'FundingOppSynopsis')
{
	$node = simplexml_import_dom($doc->importNode($db->expand(), true));
	
	global $mysqli;
	$name = "Grants";
	
	$post_date = substr($node->PostDate, 4, 4).substr($node->PostDate, 0, 2).substr($node->PostDate, 2, 2);
	$due_date = substr($node->ApplicationsDueDate, 4, 4).substr($node->ApplicationsDueDate, 0, 2).substr($node->ApplicationDueDate, 2, 2);
	
	$interest = $node->EligibilityCategory;
        for ($i = 1; $i < count($node->EligibilityCategory); $i++) {
                $interest .= ", ";
                $interest .= $node->EligibilityCategory[$i];
        }

	$instrument = $node->FundingInstrumentType;
 	for ($i = 1; $i < count($node->FundingInstrumentType); $i++) {
                $instrument .= ", ";
                $instrument .= $node->FundingInstrumentType[$i];
        }
	
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
	 '". $node->FundingOppNumber ."', 
	 '". $name ."',
	 '". $node->FundingOppTitle ."', 
	 '". $post_date ."', 
	 '". $due_date ."', 
	 '". $interest ."', 
	 '". $node->Agency ."', 
	 '". $node->Location ."', 
	 '". $node->AgencyContact ."', 
	 '". $node->Office ."', 
	 '". addslashes($node->FundingOppDescription) ."', 
	 '". $node->ObtainFundingOppText ."');";
	
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
	 '". $node->FundingOppNumber ."', 
	 '". $node->ApplicationsDueDateExplanation ."', 
	 '". $node->EstimatedFunding ."', 
	 '". $node->AwardCeiling ."', 
	 '". $node->AwardFloor ."', 
	 '". $node->OtherCategoryExplanation ."', 
	 '". $instrument ."', 
	 '". $node->NumberOfAwards ."', 
	 '". $interest ."', 
	 '". $node->AdditionalEligibilityInfo ."', 
	 '". $node->CostSharing ."');";

	//echo $base_query . "<br>";
	//echo $grant_query . "<br><br><br>";
	
	echo $node->FundingOppNumber . "<br>";

	$results = $mysqli->query($base_query);	
	if ($results == FALSE)
		echo ("Failed to add to database BASE: " . $mysqli->error . "<br>");
	
	$results = $mysqli->query($grant_query);
	if ($results == FALSE)
		echo ("Failed to add to database GRANT: " . $mysqli->error . "<br>");

	
	//Go to the next grant
	$db->next('FundingOppSynopsis');
}

?>
