<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];

$source_list = [];
$agency_list = [];
$notice_list = [];

// Check if we have any source filters toggled
if(!isset($_GET['source']) || $_GET['source'] == '')
	$source = [];
else
	$source = explode(' ANDALSO ', $_GET['source']);
// Check if we have any agency filters toggled
if(!isset($_GET['agency']) || $_GET['agency'] == '')
	$agency = [];
else
	$agency = explode(' ANDALSO ', $_GET['agency']);
// Check if we have any notice type filters toggled
if(!isset($_GET['notice']) || $_GET['notice'] == '')
	$notice = [];
else
	$notice = explode(' ANDALSO ', $_GET['notice']);

// Early bail if we have no toggles selected
if(empty($source) && empty($agency) && empty($notice)){
	return 0;
}

foreach ($source as $i){
	if(strncmp($i, "sourceFBO", strlen("sourceFBO")) == 0){
		// select items from base where source is 'Grants'
		$source_list[] = "SELECT u.fund_id 
						 FROM ctr_user_fund_link u
						 WHERE u.fund_id = b.id
						 AND u.email = '".$email."'";
	}
	else if(strncmp($i, "sourceGrants", strlen("sourceGrants")) == 0){
		// select items from base where source is 'Grants'
		$source_list[] = "SELECT u.fund_id 
						 FROM ctr_user_fund_link u
						 WHERE u.fund_id = b.id
						 AND b.source = 'Grants'
						 AND u.email = '".$email."'";
	}
	else if(strncmp($i, "recommended", strlen("recommended")) == 0){
		// select all items where base.id is in fund_link.fund_id
		$source_list[] = "SELECT u.fund_id 
						 FROM ctr_user_fund_link u
						 WHERE u.fund_id = b.id
						 AND u.email = '".$email."'";
	}
	else if(strncmp($i, "shared", strlen("shared")) == 0){
		// Select where base.id is share.fund_id
		$source_list[] = "SELECT s.fund_id 
						 FROM ctr_user_share_fund s
						 WHERE s.fund_id = b.id
						 AND s.shared_to = '".$email."'";
	}
	else if(strncmp($i, "favorited", strlen("favorited")) == 0){
		// select items with base.id is fav.fund_id
		$source_list[] = "SELECT f.fund_id 
						 FROM ctr_user_fav_fund f
						 WHERE f.fund_id = b.id
						 AND f.email = '".$email."'";
	}
}
foreach ($agency as $i){
	// If we have 'ALL' selected, it doesn't matter what else we select.
	if(strncmp($i, "All", strlen("All")) == 0){
		// Empty the agency_list.
		$agency_list = [];
		break;
	}
	// No 'else' because there is a break in the 'if'.
	if($i != '')
		$agency_list[] = "b.agency = '" . $i . "'";
}
foreach ($notice as $i){
	if($i != '')
		$notice_list[] = "fbo.notice_type = '" . $i . "'";
}

$query = "SELECT b.*, LEFT(description, 300) as description 
		  FROM ctr_funding_base b WHERE (b.id 
		  IN (". implode(") OR b.id IN (", $source_list) ."))";
if(!empty($agency_list))
	$query = $query . " AND (" . implode(" OR ", $agency_list) . ")";

if(!empty($notice_list))
	$query = $query . " AND b.id IN (
						SELECT fbo.sol_number
						FROM ctr_funding_fbo fbo
						WHERE b.id = fbo.sol_number
						AND (" . implode(" OR ", $notice_list) . "))";

$query = $query . " AND due_date >= CURDATE() 
					LIMIT 0, 30"; 
						
// echo "<br><br> TOTAL QUERY: <br>" . $query . "<br><br>";

$stmt = $mysqli->stmt_init();
if(!$stmt->prepare($query)) {
	echo "Prepared failed: " . $stmt->error;
}
$stmt->execute();
$fundings = $stmt->get_result();
$data = array();
while($funding = $fundings->fetch_assoc()) {
	$data[] = array(
		"title" => $funding['title'],
		"url" => $funding['url'],
		"agency" => $funding['agency'],
		"postDate" => $funding['post_date'],
		"dueDate" => $funding['due_date'],
		"description" => $funding['description'],
		"source" => $funding['source'],
		"id" => $funding['id']
	);
}
$stmt->close();
$mysqli->close();


echo json_encode($data);

?>
