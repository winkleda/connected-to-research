<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../scripts/connection.php");
include("../scripts/XML_download.php");

$path = "../temp_xml/GrantsDBExtract.xml";

if((time() - filemtime($path)) > (24*60*60))
{
    echo "downloading Grants.gov XML file <br>\n";
    dl_grantsgov_xml();
    echo "Finished. <br><br><br>\n";
}

$db = new XMLReader();
$db->open($path);
$doc = new DOMDocument;

if(!set_time_limit ( 60 * 60 ))
    echo "FAILED TO INCREATE TIME LIMIT<br>";
else
    echo "Increased script execution time limit<br>";

function clean_string($string, $mysqli)
{
    // Replace newlines with <br>
    $string = str_replace(["\r\n", "\n"], "<br>", $string);
    $string = strip_tags($string, "<br><p></p>");
    $string = $mysqli->escape_string($string);
    return $string;
}

echo "Output of Database for Grant objects:\n";

//Go to the first grant object
while ($db->read() && $db->name !== 'FundingOppSynopsis');

while ($db->name == 'FundingOppSynopsis')
{
	$node = simplexml_import_dom($doc->importNode($db->expand(), true));
	
	global $mysqli;
	$name = "Grants";
	
	$post_date = substr($node->PostDate, 4, 4).substr($node->PostDate, 0, 2).substr($node->PostDate, 2, 2);
	$due_date = substr($node->ApplicationsDueDate, 4, 4).substr($node->ApplicationsDueDate, 0, 2).substr($node->ApplicationsDueDate, 2, 2);
	$interest = $node->EligibilityCategory;
        for ($i = 1; $i < count($node->EligibilityCategory); $i++) {
                $interest .= ", ";
                $interest .= $node->EligibilityCategory[$i];
        }

	$instrument = $node->FundingInstrumentType;

    if($instrument == "G") $instrument = "Grant";
    else if ($instrument = "CA") $instrument = "Cooperative Agreement";
    else if ($instrument = "O") $instrument = "Other";
    else if ($instrument = "PC") $instrument = "Procurement Contract";

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
	 '". clean_string($node->FundingOppNumber, $mysqli) ."', 
	 '". clean_string($name, $mysqli) ."',
	 '". clean_string($node->FundingOppTitle, $mysqli) ."', 
	 '". clean_string($post_date, $mysqli) ."', 
	 '". clean_string($due_date, $mysqli) ."', 
	 '". $interest ."', 
	 '". clean_string($node->Agency, $mysqli) ."', 
	 '". clean_string($node->Location, $mysqli) ."', 
	 '". clean_string($node->AgencyContact, $mysqli) ."', 
	 '". clean_string($node->Office, $mysqli) ."', 
	 '". clean_string($node->FundingOppDescription, $mysqli) ."', 
	 '". clean_string($node->ObtainFundingOppText, $mysqli) ."');";
	
    if($interest == "99")
        $interest = "Unrestricted";
    else if($interest == "00")
        $interest = "State governments";
    else if($interest == "01")
        $interest = "County governments";
    else if($interest == "02")
        $interest = "City or township governments";
    else if($interest == "04")
        $interest = "Special district governments";
    else if($interest == "05")
        $interest = "Independent school districts";
    else if($interest == "06")
        $interest = "Public and State controlled institutions of higher education";
    else if($interest == "07")
        $interest = "Native American tribal governments";
    else if($interest == "08")
        $interest = "Public housing authorities/Indian housing authorities";
    else if($interest == "11")
        $interest = "Native American tribal organizations";
    else if($interest == "12")
        $interest = "Nonprofits having a 501 (c) (3) status with the IRS, other than institutions of higher education";
    else if($interest == "13")
        $interest = "Nonprofits that do not have a 501 (c) (3) status with the IRS, other than institutions of higher education";
    else if($interest == "20")
        $interest = "Private institutions of higher education";
    else if($interest == "21")
        $interest = "Individuals";
    else if($interest == "22")
        $interest = "For-profit organizations other than small businesses";
    else if($interest == "23")
        $interest = "Small businesses";
    else if($interest == "25")
        $interest = "Others";
    
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
	 '". clean_string($node->FundingOppNumber, $mysqli) ."', 
	 '". clean_string($node->ApplicationsDueDateExplanation, $mysqli) ."', 
	 '". clean_string($node->EstimatedFunding, $mysqli) ."', 
	 '". clean_string($node->AwardCeiling, $mysqli) ."', 
	 '". clean_string($node->AwardFloor, $mysqli) ."', 
	 '". clean_string($node->OtherCategoryExplanation, $mysqli) ."', 
	 '". $instrument ."', 
	 '". clean_string($node->NumberOfAwards, $mysqli) ."', 
	 '". $interest ."', 
	 '". clean_string($node->AdditionalEligibilityInfo, $mysqli) ."', 
	 '". clean_string($node->CostSharing, $mysqli) ."');";

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

echo "<br><br><br>". "COMPLETED PARSING!" . "<br>";

?>
