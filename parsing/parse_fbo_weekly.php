<?php
/*
** Parses FBO's weekly XML files using XMLReader.
** These files are very large, so we can't load it all into memory at once.
**
** Important Note: FBO documentation isn't accurate, it just provides context.
** Helpful data: https://www.fbo.gov/index?&static=interface
** CLASSCOD: https://www.fbo.gov/?s=getstart&static=faqs&mode=list&tabmode=list#q17
*/

error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../scripts/XML_download.php");
include("../scripts/connection.php");

/*
** Important: Due to the size of the FBO XML file, this script takes longer than
** 30 seconds to execute. Give it an hour.
*/
if(!set_time_limit ( 60 * 60 ))
    echo "FAILED TO INCREATE TIME LIMIT<br>";
else
    echo "Increased script execution time limit<br>";

function clean_string($string, $mysqli)
{
    // Replace newlines with <br>
    $string = str_replace(["\r\n", "\n"], "<br>", $string);
    $string = strip_tags($string);
    $string = $mysqli->escape_string($string);
    return $string;
}

// Make an array of all the record types we support. There are no AMDCSS.
// MODs are listed under their corresponding record, not separately, like in the nightly files.
$types = array(
                "COMBINE",
                "PRESOL",
                "SRCSGT",
                "SNOTE",
               );
$path = "../temp_xml/FBOweekly.xml";

// If the weekly file was downloaded more than a day ago, update it.
if((time() - filemtime($path)) > (24*60*60))
{
    echo "downloading FBO nightly XML <br>\n";
    dl_fbogov_weekly();
    echo "Finished. <br><br><br>\n";
}

// Open XML file using XMLReader
$xml = new XMLReader();
if($xml->open($path) == FALSE)
    die("Failed to open XML file. <br>");
else
    echo "File open success <br>";

/*
** The XML mixed COMBINE, PRESOL, and everything else in no particular order.
** But records are not changed directly. Instead, there is a CHANGES attribute for records that have been updated.
** So for every record, we have to check if any cahnges have been made, and update it if so.
** Also, we can't parse the records in a straightforward way.
*/
/*
    First parse all of the COMBINE type.
    According to the documentation...
    REQUIRED: DATE
              YEAR
              ZIP
              CLASSCOD
              SUBJECT
              SOLNBR
              CONTACT
    OPTIONAL: NAICS
              OFFADD
              RESPDATE
              DESC
              LINK
              EMAIL:
                ADDRESS
                DESC
              SETASIDE
    note: LINK is supposed to have an URL and DESC, like EMAIL, but it DOESNT.
    note: YEAR does not exist. Instead, DATE contains an MMDDYYYY formatted date.
*/
echo "Beginning parse...<br><br><br>";
while($xml->read())
{
    // Read each element, skipping any that aren't interesting
    if(!($xml->nodeType == XMLReader::ELEMENT)
    || (!in_array($xml->name, $types)))
        continue;

    // echo "<br><br>&nbsp;&nbsp;&nbsp;" . $xml->name . '<br>';
    $node = new SimpleXMLElement($xml->readOuterXML());

    // The due date is silly. Reformat from MMDDYYYY to YYYYMMDD
    // NOTE: documentation says the date is separated into DATE and YEAR fields, but it isn't.
    $due_date = "";
    if(!empty($node->RESPDATE))
        $due_date = date_format(date_create_from_format('mdY', $node->RESPDATE), 'Y-m-d');

    $post_date = date_format(date_create_from_format('mdY', $node->DATE), 'Y-m-d');

    // For some odd reason, some records have no solnbr.
    // Even though te documentation says its required.
    $solnbr = $mysqli->escape_string($node->SOLNBR);
    if($solnbr == "")
        continue;

    // Construct a list of values to update based on what is in this element
    $base_values = [];
    $fbo_values = [];

    //$fbo_values[] = "notice_type = '" . clean_string($xml->name, $mysqli) . "'";
    $notice_type = clean_string($xml->name, $mysqli);
    if($notice_type == "COMBINE")
        $notice_type = "Combined Solicitation";
    else if($notice_type == "PRESOL")
        $notice_type = "Presolicitation";
    else if($notice_type == "SRCSGT")
        $notice_type = "Sources Sought";
    else if($notice_type == "SNOTE")
        $notice_type = "Special Notice";
    $fbo_values[] = "notice_type = '" . $notice_type . "'";
    $base_values[] = "source = 'FedBizOpps'";
    $base_values[] = "post_date = '" . clean_string($post_date, $mysqli) . "'";
    $base_values[] = "title = '" . clean_string($node->SUBJECT, $mysqli) . "'";
    $base_values[] = "contact = '" . clean_string($node->CONTACT, $mysqli) . "'";

    echo "NOTICE TYPE: " . $notice_type . ". SOLNBR: " . $node->SOLNBR . "<br>";

    if(!empty($node->OFFADD))
        $base_values[] = "address = '" . clean_string($node->OFFADD, $mysqli) . "'";
    if(!empty($node->AGENCY))
        $base_values[] = "agency = '" . clean_string($node->AGENCY, $mysqli) . "'";
    if(!empty($node->DESC))
        $base_values[] = "description = '" . clean_string($node->DESC, $mysqli) . "'";
    if(!empty($node->LINK))
        $base_values[] = "url = '" . clean_string($node->LINK, $mysqli) . "'";
    if(!empty($node->SETASIDE))
        $fbo_values[] = "set_aside = '" . clean_string($node->SETASIDE, $mysqli) . "'";
    if(!empty($node->AWDDATE))
        $fbo_values[] = "award_date = '". clean_string($node->AWDDATE, $mysqli) . "'";
    if(!empty($node->RESPDATE))
        $base_values[] = "due_date = '" . clean_string($due_date, $mysqli) . "'";

    $office = [];
    if(!empty($node->OFFICE))
        $office[] = clean_string($node->OFFICE, $mysqli);
    if(!empty($node->LOCATION))
        $office[] = clean_string($node->OFFICE, $mysqli);
    if(!empty($office))
        $base_values[] = "office = '" . implode(":", $office) . "'";

    $interests="";
    if(!empty($node->CLASSCOD))
        $interests .= "cc:". clean_string($node->CLASSCOD, $mysqli);
    if(!empty($node->NAICS))
        $interests .= ";naics:". clean_string($node->NAICS, $mysqli);
    $base_values[] = "interests = '" . $interests . "'";

    // Before anything, check if this record is already in the database
    $fbo_query = "SELECT * FROM ctr_funding_base WHERE id = '". $solnbr ."'";
    $result = $mysqli->query($fbo_query);
    if($result == FALSE)
        echo ("Failed to query database: " . $mysqli->error . "<br>");

    // If it already is, then update the record to the master copy in the FBO database.
    if($result->num_rows > 0)
    {
        // echo "Already in <br>";
        $fbo_query = "UPDATE ctr_funding_fbo SET "
                     . implode(", ", $fbo_values)
                     . " WHERE sol_number='".$solnbr."';";
        $base_query = "UPDATE ctr_funding_base SET "
                     . implode(", ", $base_values)
                     . " WHERE id='".$solnbr."';";
    }
    else
    {
        $base_values[] = "id = '" . $solnbr . "'";
        $fbo_values[] = "sol_number = '" . $solnbr . "'";
        $fbo_query = "INSERT INTO ctr_funding_fbo SET "
                     . implode(", ", $fbo_values) .";";
        $base_query = "INSERT INTO ctr_funding_base SET "
                     . implode(", ", $base_values) .";";
    }
	
    // Execute whatever query we have
    $result = $mysqli->query($base_query);
    if($result == FALSE)
    {
        echo "DB query failed: " . $mysqli->error . "<br>";
        echo "Query: <br>" . $base_query . "<br><br>";
    }

    $result = $mysqli->query($fbo_query);
    if($result == FALSE)
    {
        echo "DB query failed: " . $mysqli->error . "<br>";
        echo "Query: <br>" . $base_query . "<br><br>";
    }

    // When an opp is changed, the record in the XML is not changed.
    // Instead, it gets a list of CHANGEs added to its record.
    // We need to iterate through these changes and apply them to construct the up-to-date record.
    if(!empty($node->CHANGES->MOD))
    {
        foreach($node->CHANGES->MOD as $mod)
        {
            /*
            ** For each MOD, update the relevant record in the database.
            */
            $base_values = [];
            $fbo_values = [];
            $post_date = clean_string(date_format(date_create_from_format('mdY', $node->DATE), 'Y-m-d'), $mysqli);

            $desc = "<br>Description modified on "
                    . $post_date
                    . "<br>" . clean_string($node->DESC, $mysqli);

            $base_values[] = "post_date = '" . clean_string($post_date, $mysqli) . "'";
            $base_values[] = "title = '" . clean_string($node->SUBJECT, $mysqli) . "'";
            $base_values[] = "contact = '" . clean_string($node->CONTACT, $mysqli) . "'";

            if(!empty($node->OFFADD))
                $base_values[] = "address = '" . clean_string($node->OFFADD, $mysqli) . "'";
            if(!empty($node->AGENCY))
                $base_values[] = "agency = '" . clean_string($node->AGENCY, $mysqli) . "'";
            if(!empty($node->OFFICE))
                $base_values[] = "office = '" . clean_string($node->OFFICE, $mysqli) . "'";
            if(!empty($node->DESC))
                $base_values[] = "description = '" . clean_string($node->DESC, $mysqli) . "'";
            if(!empty($node->LINK))
                $base_values[] = "url = '" . clean_string($node->LINK, $mysqli) . "'";
            if(!empty($node->SETASIDE))
                $fbo_values[] = "set_aside = '" . clean_string($node->SETASIDE, $mysqli) . "'";
            if(!empty($node->AWDDATE))
                $fbo_values[] = "award_date = '". clean_string($node->AWDDATE, $mysqli) . "'";
            if(!empty($xml->name))
                $fbo_values[] = "notice_type = '" . clean_string($xml->name, $mysqli) . "'";
            if(!empty($node->LOCATION))
                $base_values[] = "office = '" . clean_string($node->OFFICE, $mysqli) . "'";

            if(!empty($node->RESPDATE))
            {
                $due_date = clean_string(date_format(date_create_from_format('mdY', $node->RESPDATE), 'Y-m-d'), $mysqli);
                $base_values[] = "due_date = '" . clean_string($due_date, $mysqli) . "'";
            }

            $interests="";
            if(!empty($node->CLASSCOD))
                $interests .= "cc:". clean_string($node->CLASSCOD, $mysqli);
            if(!empty($node->NAICS))
                $interests .= ";naics:". clean_string($node->NAICS, $mysqli);
            $base_values[] = "interests = '" . $interests . "'";


            // Construct UPDATE query
            $base_query = "UPDATE ctr_funding_base SET "
                          . implode(", ", $base_values)
                          . " WHERE id='".$solnbr."';";

            $fbo_query = "UPDATE ctr_funding_fbo SET "
                          . implode(", ", $fbo_values)
                          . " WHERE sol_number='".$solnbr."';";
            // echo "MOD: <br>" . $fbo_query . "<br>" . $base_query . "<br>";
			
			// Execute whatever query we have
			$result = $mysqli->query($base_query);
			if($result == FALSE)
			{
				echo "DB query failed: " . $mysqli->error . "<br>";
				echo "Query: <br>" . $base_query . "<br><br>";
			}

			$result = $mysqli->query($fbo_query);
			if($result == FALSE)
			{
				echo "DB query failed: " . $mysqli->error . "<br>";
				echo "Query: <br>" . $base_query . "<br><br>";
			}
        }
    }

    $xml->next();
}
echo "Finished reading notices <br>";

echo "<br><br><br>#########<br>SUCCESS<br>#########";
?>
