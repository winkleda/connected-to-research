<?php
/*
** dl_grantsgov_xml - Downloads yesterday's XML dump as 'GrantsDBExtract.xml'
** dl_fbogov_nightly - Downloads yesterdays XML dump as 'FBOnightly.xml'
** dl_fbogov_weekly - Downloads FBO's weekly XML dump as 'FBOweekly.xml'
** 
** Beware: FBO's weekly dump might be big enough to exceed our school server data limit.
*/


/*
** Download Grants.gov's XML data. 
** Gets yesterday's data in case today's isn't ready yet.
*/
function dl_grantsgov_xml(){
	$destination_path = "../temp_xml/";
	$destination = $destination_path . "grants-gov.zip";

	// The source URL, from grants.gov
	// this line includes everything but the filename.
	$source_url = "http://www.grants.gov/web/grants/xml-extract.html?p_p_id=xmlextract_WAR_grantsxmlextractportlet_INSTANCE_5NxW0PeTnSUa&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2";

	// Add the download info
	$source_url .= "&download=GrantsDBExtract";

	// Now add the dates to the filename. YYYYMMDD format expected.
	// use yesterday's date to avoid being ahead of grants.gov
	$timestamp = time() - (24 * 60 * 60);
	$source_url .=  date("Ymd", $timestamp) . ".zip";

	// Copy the source file from grants.gov to our temp directory.
	if (copy($source_url, $destination) == FALSE)
		die("Failed to copy file from source URL to server. <br>");

	// Now extract the .zip file to get at the XML
	$zip = new ZipArchive;
	$result = $zip->open($destination);
	if ($result == TRUE) {
		// Extract the contents of the zip file
		if ($zip->extractTo($destination_path) == FALSE)
			die("Failed to extract zip file. <br>");
		// Close the zip file
		if($zip->close() == FALSE)
			die("Failed to close zip file. <br>");
	}
	else
		die("Failed to open zip file: returned \"" . $result . "\" <br>");

	// Rename the file so its easier to refer to
	system("mv " . $destination_path . "GrantsDBExtract* " . $destination_path . "GrantsDBExtract.xml"); 
}

/*
** Download FBO.gov's XML data. 
** Gets yesterday's data in case today's isn't ready yet.
*/
function dl_fbogov_nightly(){
	$destination_path = "../temp_xml/";
	$destination = $destination_path . "FBOnightly.xml";

	// The source URL from FBO.gov is simpler.
	// We can download the nightly files directly.
	// EXAMPLE: ftp://ftp.fbo.gov/FBOFeed20160112
	$source_url = "ftp://ftp.fbo.gov/FBOFeed";

	// Add the date to the filename. YYYYMMDD format expected.
	// FBO.gov uploads nightly, so we can only get yesterday's XML.
	$timestamp = time() - (24 * 60 * 60);
	$source_url .=  date("Ymd", $timestamp);

	// Copy the source file from grants.gov to our temp directory.
	if (copy($source_url, $destination) == FALSE)
		die("Failed to copy file from source URL to server. <br>");
}

/*
** Download FBO's weekly XML
** Gets whatever is there, because the filename doesn't change.
*/
function dl_fbogov_weekly(){
	$destination_path = "../temp_xml/";
	$destination = $destination_path . "FBOweekly.xml";

	// The source URL from FBO.gov is simpler.
	// We can download the nightly files directly.
	$source_url = "ftp://ftp.fbo.gov/datagov/FBOFullXML.xml";

	// Copy the source file from grants.gov to our temp directory.
	if (copy($source_url, $destination) == FALSE)
		die("Failed to copy file from source URL to server. <br>");
}
?>