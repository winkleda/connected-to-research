<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../scripts/XML_download.php");
include("../scripts/connection.php");

echo "downloading FBO nightly XML <br>\n";
//dl_fbogov_nightly(); // Downloads "FBOnightly.xml"
echo "Finished. <br><br><br>\n";

$path = "../temp_xml/FBOnightly.xml";

//Due to the way the file is made, we can"t parse it using a normal XML parser.
//Instead, we have to read it line by line like a normal file.
$file = fopen($path, "r");
if($file == FALSE)
    die("failed to open file " . $path . "<br>");

// Need to check when we read a <PRESOL>, and continue reading until we get a </PRESOL>.
// When we find a PRESOL, switch to another loop that fills out all the relevant data.
// Then, when we reach the </PRESOL>, check if we have enough to insert.

while(($line = fgets($file)) !== FALSE)
{
    //Read through the file until we find a <PRESOL> element.
    if((strncmp($line, "<PRESOL>", strlen("<PRESOL>")) == 0)    // Presolicitation
    || (strncmp($line, "<SRCSGT>", strlen("<SRCSGT>")) == 0)    // Sources Sought
    || (strncmp($line, "<COMBINE>", strlen("<COMBINE>")) == 0)  // Combined Synopsis/Solicitation
    || (strncmp($line, "<SNOTE>", strlen("<SNOTE>")) == 0))     // Special Notice
    {
        //echo "########## NEW ###########\n";

        // Initialize variables used in each loop
        $type = trim($line, "<>\n ");
        $date = "";
        $year = "";
        $agency = "";
        $solnbr = "";
        $office = "";   
        $location = "";
        $zip = "";
        $classcod = "";
        $naics = "";
        $offadd = "";
        $subject = "";
        $respdate = "";
        $contact = "";
        $desc = "";
        $ntype = "";
        $awdmt = "";
        $setaside = "";
        $url = "";
        $awddate = "";
        
        // Once we do, read throguh all subsequent lines until we find a matching </PRESOL>
        while(($line = fgets($file)) !== FALSE)
        {
            //If we read a </PRESOL>, this element ended
            if((strncmp($line, "</PRESOL>", strlen("</PRESOL>")) == 0)
            || (strncmp($line, "</SRCSGT>", strlen("</SRCSGT>")) == 0)
            || (strncmp($line, "</COMBINE>", strlen("</COMBINE>")) == 0)
            || (strncmp($line, "</SNOTE>", strlen("</SNOTE>")) == 0))
                break;
            
            //remove extra characters
            $line = trim($line);
            
            // Try to find all elements that matter to our database
            if(strncmp($line, "<DATE>", strlen("<DATE>")) == 0)
                $date = $mysqli->escape_string(str_replace("<DATE>", "", $line));
            if(strncmp($line, "<YEAR>", strlen("<YEAR>")) == 0)
                $year = $mysqli->escape_string(str_replace("<YEAR>", "", $line));
            if(strncmp($line, "<AGENCY>", strlen("<AGENCY>")) == 0)
                $agency = $mysqli->escape_string(str_replace("<AGENCY>", "", $line));
            if(strncmp($line, "<OFFICE>", strlen("<OFFICE>")) == 0)
                $office = $mysqli->escape_string(str_replace("<OFFICE>", "", $line));
            if(strncmp($line, "<LOCATION>", strlen("<LOCATION>")) == 0)
                $location = $mysqli->escape_string(str_replace("<LOCATION>", "", $line));
            if(strncmp($line, "<ZIP>", strlen("<ZIP>")) == 0)
                $zip = $mysqli->escape_string(str_replace("<ZIP>", "", $line));
            if(strncmp($line, "<SOLNBR>", strlen("<SOLNBR>")) == 0)
                $solnbr = $mysqli->escape_string(str_replace("<SOLNBR>", "", $line));
            if(strncmp($line, "<CLASSCOD>", strlen("<CLASSCOD>")) == 0)
                $classcod = $mysqli->escape_string(str_replace("<CLASSCOD>", "", $line));
            if(strncmp($line, "<NAICS>", strlen("<NAICS>")) == 0)
                $naics = $mysqli->escape_string(str_replace("<NAICS>", "", $line));
            if(strncmp($line, "<OFFADD>", strlen("<OFFADD>")) == 0)
                $offadd = $mysqli->escape_string(str_replace("<OFFADD>", "", $line));
            if(strncmp($line, "<SUBJECT>", strlen("<SUBJECT>")) == 0)
                $subject = $mysqli->escape_string(str_replace("<SUBJECT>", "", $line));
            if(strncmp($line, "<RESPDATE>", strlen("<RESPDATE>")) == 0)
                $respdate = $mysqli->escape_string(str_replace("<RESPDATE>", "", $line));
            if(strncmp($line, "<CONTACT>", strlen("<CONTACT>")) == 0)
                $contact = $mysqli->escape_string(str_replace("<CONTACT>", "", $line));
            if(strncmp($line, "<NTYPE>", strlen("<NTYPE>")) == 0)
                $ntype = $mysqli->escape_string(str_replace("<NTYPE>", "", $line));
            if(strncmp($line, "<AWDAMT>", strlen("<AWDAMT>")) == 0)
                $awdmt = $mysqli->escape_string(str_replace("<AWDAMT>", "", $line));
            if(strncmp($line, "<SETASIDE>", strlen("<SETASIDE>")) == 0)
                $setaside = $mysqli->escape_string(str_replace("<SETASIDE>", "", $line));
            if(strncmp($line, "<URL>", strlen("<URL>")) == 0)
                $url = $mysqli->escape_string(str_replace("<URL>", "", $line));
            if(strncmp($line, "<AWDDATE>", strlen("<AWDDATE>")) == 0)
                $awddate = $mysqli->escape_string(str_replace("<AWDDATE>", "", $line));
            /* 
            ** DESC is spectial because it is sometimes duplicated in the file, but the second DESC
            ** is not important, and it can contain HTML tags. So we need to strip the <DESC>, then
            ** strip the tags, THEN convert HTML special characters into normal characters, THEN
            ** insert excape characters before we have an acceptable description.
            */
            if((strncmp($line, "<DESC>", strlen("<DESC>")) == 0)
                && ($desc == ""))
                $desc = $mysqli->escape_string(html_entity_decode(strip_tags(str_replace("<DESC>", "", $line), "<br>")));
        }
        
        if($ntype == "")
            $ntype = $type;
        
        // SQL conversion allows YYMMDD -> date type. DATE is MMDD, YEAR is YY. RESPDATE is MMDDYY, so rearrange the string in a stupid way because I dont know php
        $post_date = $year.$date;
        $due_date =  $respdate[4].$respdate[5].$respdate[0].$respdate[1].$respdate[2].$respdate[3];
        if($awddate !== "")
            $awddate =  $awddate[4].$awddate[5].$awddate[0].$awddate[1].$awddate[2].$awddate[3];
        
        // Construct the query using the fields we pulled out earlier.
        $base_query =    "INSERT INTO ctr_funding_base
                    (id, title, post_date, due_date, interests, agency, address, contact, office, description, url)
                    VALUES
                    ('".
                    $solnbr ."', '". $subject ."', '". $post_date ."', '". $due_date
                    ."', '', '". $agency ."', '". $offadd ."', '". $contact ."', '". 
                    $office ."', '". $desc ."', '" . $url ."');";
        $fbo_query = "INSERT INTO ctr_funding_fbo
                    (sol_number, notice_type, award_amount, award_date, set_aside)
                    VALUES
                    ('". $solnbr ."','". $ntype ."','". $awdmt ."','". $awddate ."','". $setaside ."');";
        
        // All we need is a solnbr to add something to our database
        if($solnbr !== "")
        {
            $result = $mysqli->query($base_query);
            if($result == FALSE)
                echo ("Failed to add to database: " . $mysqli->error . "<br>");
            echo $base_query . "<br>";
            $result = $mysqli->query($fbo_query);
            if($result == FALSE)
                echo ("Failed to add to database: " . $mysqli->error . "<br>");
            echo $fbo_query . "<br><br><br>";
        }
    }
}

echo "SUCCESSFUL END<br>";?>
