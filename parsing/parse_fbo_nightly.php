<?php
/*
** Parse the data from an FBO nightly dump.
** Helpful data: https://www.fbo.gov/index?&static=interface
** CLASSCOD: https://www.fbo.gov/?s=getstart&static=faqs&mode=list&tabmode=list#q17
*/

error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../scripts/XML_download.php");
include("../scripts/connection.php");

$path = "../temp_xml/FBOnightly.xml";

// If the nightly file was downloaded more than a day ago, update it.
if((time() - filemtime($path)) > (24*60*60))
{
    echo "downloading FBO nightly XML <br>\n";
    dl_fbogov_nightly();
    echo "Finished. <br><br><br>\n";
}

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
    || (strncmp($line, "<AMDCSS>", strlen("<AMDCSS>")) == 0)    // Amendment to a Previous Combined Solicitation
    || (strncmp($line, "<MOD>", strlen("<MOD>")) == 0)          // Modification to a Previous Base Notice
    || (strncmp($line, "<AWARD>", strlen("<AWARD>")) == 0)      // Award Notice
    || (strncmp($line, "<JA>", strlen("<JA>")) == 0)            // Justification and Approval (J&A)
    || (strncmp($line, "<ITB>", strlen("<ITB>")) == 0)          // Intent to Bundle Requirements (DoD Funded)
    || (strncmp($line, "<FAIROPP>", strlen("<FAIROPP>")) == 0)  // Fair Opportunity / Limited Sources Justification
    || (strncmp($line, "<FSTD>", strlen("<FSTD>")) == 0)        // Foreign Government Standard
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
        //$zip = "";
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
        
        $base_query = "";
        $fbo_query = "";
        
        // Once we do, read through all subsequent lines until we find a matching </PRESOL>
        while(($line = fgets($file)) !== FALSE)
        {
// Tag for goto
top:
            //If we read a </PRESOL>, this element ended
            if((strncmp($line, "</PRESOL>", strlen("</PRESOL>")) == 0)
            || (strncmp($line, "</SRCSGT>", strlen("</SRCSGT>")) == 0)
            || (strncmp($line, "</COMBINE>", strlen("</COMBINE>")) == 0)
            || (strncmp($line, "</AMDCSS>", strlen("</AMDCSS>")) == 0)
            || (strncmp($line, "</MOD>", strlen("</MOD>")) == 0)
            || (strncmp($line, "</AWARD>", strlen("</AWARD>")) == 0) 
            || (strncmp($line, "</JA>", strlen("</JA>")) == 0)       
            || (strncmp($line, "</ITB>", strlen("</ITB>")) == 0)       
            || (strncmp($line, "</FAIROPP>", strlen("</FAIROPP>")) == 0)  
            || (strncmp($line, "</FSTD>", strlen("</FSTD>")) == 0)       
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
            // ZIP is already included in OFFADD so we don't need it here
            //if(strncmp($line, "<ZIP>", strlen("<ZIP>")) == 0)
            //    $zip = $mysqli->escape_string(str_replace("<ZIP>", "", $line));
            if(strncmp($line, "<SOLNBR>", strlen("<SOLNBR>")) == 0)
                $solnbr = $mysqli->escape_string(str_replace("<SOLNBR>", "", $line));
            if(strncmp($line, "<CLASSCOD>", strlen("<CLASSCOD>")) == 0)
                $classcod = $mysqli->escape_string(str_replace("<CLASSCOD>", "", $line));
            if(strncmp($line, "<NAICS>", strlen("<NAICS>")) == 0)
                $naics = $mysqli->escape_string(str_replace("<NAICS>", "", $line));
            if(strncmp($line, "<SUBJECT>", strlen("<SUBJECT>")) == 0)
                $subject = $mysqli->escape_string(str_replace("<SUBJECT>", "", $line));
            if(strncmp($line, "<RESPDATE>", strlen("<RESPDATE>")) == 0)
                $respdate = $mysqli->escape_string(str_replace("<RESPDATE>", "", $line));
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
            ** Some fields are special because they can be multiple lines 
            ** and may contain HTML. So after reading either, we need to 
            ** continue reading until the next tag, then start over from the top
            */
            if((strncmp($line, "<DESC>", strlen("<DESC>")) == 0) && ($desc == ""))
            {
            $desc = $line;
                while(($line = fgets($file)) !== FALSE)
                {
                    // DESC is always followed by a LINK
                    if(strncmp($line, "<LINK>", strlen("<LINK>")) == 0)
                        break;
                    $desc = $desc . $line;
                }
                $desc = str_replace("<DESC>", "", $desc);
                $desc = str_replace(["\r\n", "\n"], "<br>", $desc);
                $desc = strip_tags($desc, "<br><p></p>");
                $desc = html_entity_decode($desc);
                $desc = $mysqli->escape_string($desc);
                
                goto top;
            }
            if((strncmp($line, "<CONTACT>", strlen("<CONTACT>")) == 0) && ($contact == ""))
            {
                $contact = $line;
                while(($line = fgets($file)) !== FALSE)
                {
                    // CONTACT is always followed by a DESC
                    if(strncmp($line, "<DESC>", strlen("<DESC>")) == 0)
                        break;
                    $contact = $contact . $line;
                }
                $contact = str_replace("<CONTACT>", "", $contact);
                $contact = str_replace(["\r\n", "\n"], "<br>", $contact);
                $contact = strip_tags($contact, "<br><p></p>");
                $contact = html_entity_decode($contact);
                $contact = $mysqli->escape_string($contact);
                
                goto top;
            }
            if((strncmp($line, "<OFFADD>", strlen("<OFFADD>")) == 0) && ($offadd == ""))
            {
                $offadd = $line;
                while(($line = fgets($file)) !== FALSE)
                {
                    if(strncmp($line, "<SUBJECT>", strlen("<SUBJECT>")) == 0)
                        break;
                    $offadd = $offadd . $line;
                }
                $offadd = str_replace("<OFFADD>", "", $offadd);
                $offadd = str_replace(["\r\n", "\n"], "<br>", $offadd);
                $offadd = strip_tags($offadd, "<br><p></p>");
                $offadd = html_entity_decode($offadd);
                $offadd = $mysqli->escape_string($offadd);
                
                goto top;
            }
        }
        
        if($ntype == "")
            $ntype = $type;
        // convert notice type from something like PRESOL to Presolicitation
        if($ntype !== "") {
            if($ntype == "PRESOL")
                $ntype = "Presolicitation";
            else if($ntype == "SRCSGT")
                $ntype = "Sources Sought";
            else if($ntype == "COMBINE")
                $ntype = "Combined Synopsis/Solicitation";
            else if($ntype == "SNOTE")
                $ntype = "Special Notice";
            else if($ntype == "AWARD")
                $ntype = "Award Notice";
            else if($ntype == "JA")
                $ntype = "Justification and Approval (J&A)";
            else if($ntype == "ITB")
                $ntype = "Intent to Bundle Requirements (DoD Funded)";
            else if($ntype == "FAIROPP")
                $ntype = "Fair Opportunity / Limited Sources Justification";
            else if($ntype == "FSTD")
                $ntype = "Foreign Government Standard";
        
        // SQL conversion allows YYMMDD -> date type. DATE is MMDD, YEAR is YY. RESPDATE is MMDDYY, so rearrange the string in a stupid way because I dont know php
        $post_date = $year.$date;
        $due_date =  $respdate[4].$respdate[5].$respdate[0].$respdate[1].$respdate[2].$respdate[3];
        if($awddate !== "")
            $awddate =  $awddate[4].$awddate[5].$awddate[0].$awddate[1].$awddate[2].$awddate[3];
        
        // If the type is AMDCSS then we need to update a record already in our database.
        // In that case, be sure to concat any existing items
        if(($type == 'AMDCSS')
            || ($type == 'MOD'))
        {
            // Initialize empty array of values
            $base_values = ["source = 'FedBizOpps'"];
            $fbo_values = [];
            
            // Required fields which will be not null
            $base_values[] = "address = '". $offadd . "'";
            $base_values[] = "title = '". $subject . "'";
            $base_values[] = "contact = '". $contact . "'";
            
            // Modify the description to note the update
            $desc = "<br>Description modified on "
                    . date_format(date_create_from_format('ymd', $post_date), 'Y-m-d')
                    . "<br>" . $desc;
            
            // optionally null fields
            if(($naics !== "") && ($classcod !== ""))
                $base_values[] = "interests='cc:" . $classcod . ";naics:" . $naics . "'";
            if($offadd !== "")
                $base_values[] = "address = '". $offadd . "'";
            if($respdate !== "")
                $base_values[] = "due_date = '". $due_date . "'";
            if($url !== "")
                $base_values[] = "url = '". $url . "'";
            if($setaside !== "")
                $fbo_values[] = "set_aside = '". $setaside . "'";
            if($desc !== "")
                $base_values[] = "description=concat(ifnull(description,''), '". $desc ."')";

            // convert notice type from something like PRESOL to Presolicitation
            if($ntype !== "") {
                if($ntype == "PRESOL")
                    $ntype = "Presolicitation";
                else if($ntype == "SRCSGT")
                    $ntype = "Sources Sought";
                else if($ntype == "COMBINE")
                    $ntype = "Combined Synopsis/Solicitation";
                else if($ntype == "SNOTE")
                    $ntype = "Special Notice";
                else if($ntype == "AWARD")
                    $ntype = "Award Notice";
                else if($ntype == "JA")
                    $ntype = "Justification and Approval (J&A)";
                else if($ntype == "ITB")
                    $ntype = "Intent to Bundle Requirements (DoD Funded)";
                else if($ntype == "FAIROPP")
                    $ntype = "Fair Opportunity / Limited Sources Justification";
                else if($ntype == "FSTD")
                    $ntype = "Foreign Government Standard";

                $fbo_values[] = "notice_type = '". $ntype . "'";
            }
            
            // Construct queries to update using any values
            $base_query = "UPDATE ctr_funding_base SET "
                          . implode(", ", $base_values)
                          . " WHERE id='".$solnbr."';";
                          
            $fbo_query = "UPDATE ctr_funding_fbo SET "
                          . implode(", ", $fbo_values)
                          . " WHERE sol_number='".$solnbr."';";
            
            echo "AMDCSS/MOD: ";
        }
        else 
        {
            // Construct the query using the fields we pulled out earlier.
            $base_query =    "INSERT INTO ctr_funding_base
                        (source, id, title, post_date, due_date, interests, agency, address, contact, office, description, url)
                        VALUES
                        ('FedBizOpps', '".
                        $solnbr ."','". 
                        $subject ."','". 
                        $post_date ."','". 
                        $due_date ."','". 
                        "cc:" . $classcod . ",naics:" . $naics ."','".
                        $agency ."','". 
                        $offadd ."','". 
                        $contact ."','". 
                        $office ."','". 
                        $desc ."','" . 
                        $url ."');";
            $fbo_query = "INSERT INTO ctr_funding_fbo
                        (sol_number, notice_type, award_amount, award_date, set_aside)
                        VALUES
                        ('". 
                        $solnbr ."','". 
                        $ntype ."','". 
                        $awdmt ."','". 
                        $awddate ."','". 
                        $setaside ."');";
        }
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
