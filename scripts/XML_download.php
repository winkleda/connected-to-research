<?php

/*
** Download Grants.gov's XML data. 
** Gets yesterday's data in case today's isn't ready yet.
*/
function dl_grantsgov_xml(){
	ini_set('display_errors', 'On');

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

	// echo for debugging
	echo "destination folder:   " . $destination_path . '<br>';
	echo "destination filename: " . $destination . '<br>';
	echo "XML source URL:       " . $source_url . '<br>';

	// Copy the source file from grants.gov to our temp directory.
	echo "<br> Copying file from source to destination. Please wait... <br>";
	if (copy($source_url, $destination) == FALSE)
		die("Failed to copy file from source URL to server. <br>");
	echo "Copy successful. <br>";

	// Now extract the .zip file to get at the XML
	$zip = new ZipArchive;
	$result = $zip->open($destination);
	if ($result == TRUE) {
		echo "<br> Extracting XML from .zip archive. Please wait... <br>";
		// Extract the contents of the zip file
		if ($zip->extractTo($destination_path) == FALSE)
			die("Failed to extract zip file. <br>");
		// Close the zip file
		if($zip->close() == FALSE)
			die("Failed to close zip file. <br>");
		echo "Extract successful. <br>";
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
	ini_set('display_errors', 'On');

	$destination_path = "../temp_xml/";
	// Output file is '.fake.xml' to remind you that it's not real XML
	$destination = $destination_path . "FBOnightly.fake.xml";

	// The source URL from FBO.gov is simpler.
	// We can download the nightly files directly.
	// ftp://ftp.fbo.gov/FBOFeed20160112
	$source_url = "ftp://ftp.fbo.gov/FBOFeed";

	// Add the date to the filename. YYYYMMDD format expected.
	// FBO.gov uploads nightly, so we can only get yesterday's XML.
	$timestamp = time() - (24 * 60 * 60);
	$source_url .=  date("Ymd", $timestamp);

	// echo for debugging
	echo "destination folder:   " . $destination_path . '<br>';
	echo "destination filename: " . $destination . '<br>';
	echo "XML source URL:       " . $source_url . '<br>';

	// Copy the source file from grants.gov to our temp directory.
	echo "<br> Copying file from source to destination. Please wait... <br>";
	if (copy($source_url, $destination) == FALSE)
		die("Failed to copy file from source URL to server. <br>");
	echo "Copy successful. <br>";
}

// Keep test here until its ready
echo "Downloading nightly XML from FedBizOps <br>";
dl_fbogov_nightly();
echo "<br><br> Downloading XML from Grants.gov <br>";
dl_grantsgov_xml();
?>