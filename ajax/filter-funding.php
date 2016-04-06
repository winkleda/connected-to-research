<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];
$stmt = $mysqli->stmt_init();

//Recommended for the user
$funding_recommended = "SELECT count(*) as count
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND due_date >= CURDATE()
		AND email = ?";

//Shared funding from other users to the logged in user
$funding_shared = "SELECT count(*) as count
		FROM ctr_funding_base b, ctr_user_share_fund s
		WHERE b.id = s.fund_id
		AND due_date >= CURDATE()
		AND shared_to = '$email' ";

$funding_favorited = "SELECT count(*) as count
		FROM ctr_funding_base b, ctr_user_fav_fund f
		WHERE b.id = f.fund_id
		AND due_date >= CURDATE()
		AND email = ?";

//The funding source - FedBizOpps
$funding_sourceFBO = "SELECT count(*) as count
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND due_date >= CURDATE()
		AND source = 'FedBizOpps'
		AND email = ?";

//The funding source - Grants
$funding_sourceGrants = "SELECT count(*) as count
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND due_date >= CURDATE()
		AND source = 'Grants'
		AND email = ?";


//agency total for clicking on all
$funding_agency_total = "SELECT count(*) as count
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND due_date >= CURDATE()
		AND email = ?";

//query to filter by name and count
$agency_name = "SELECT agency, count(*) as count
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND due_date >= CURDATE()
		AND email = ?
		GROUP BY agency
		ORDER BY count DESC
		LIMIT 0, 10";

//The notice type for FBO
$funding_noticeFBO = "SELECT notice_type, count(*) as count
		FROM ctr_funding_fbo f, ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND b.id = f.sol_number
		AND due_date >= CURDATE()
		AND email = ?
		GROUP BY notice_type
		ORDER BY count DESC
		LIMIT 0, 10";

//The notice type for Grants
$funding_noticeGrants = "SELECT instrument_type, count(*) as count
		FROM ctr_funding_grants g, ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND b.id = g.opp_number
		AND due_date >= CURDATE()
		AND email = ?
		GROUP BY instrument_type
		ORDER BY count DESC
		LIMIT 0, 10";

//Posted Date
$posted_date = "SELECT post_date, count(*) as count
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND due_date >= CURDATE()
		AND email = ?
		GROUP BY post_date
		ORDER BY post_date DESC
		LIMIT 0, 5";
		//WHERE YEAR(post_date) = ?";

//Due Date
$due_date = "SELECT due_date, count(*) as count
		FROM ctr_funding_base b, ctr_user_fund_link u
		WHERE b.id = u.fund_id
		AND due_date >= CURDATE()
		AND email = ?
		GROUP BY due_date
		ORDER BY due_date ASC
		LIMIT 0, 5";
		//WHERE due_date = ?";


//assigning key => values to an array set to fields
//$fields = array("sourceFBO" => $funding_sourceFBO, 
//				"sourceGrants" => $funding_sourceGrants,
//				"agency" => $funding_agency_total,
//				"noticeFBO" => $funding_noticeFBO,
//				"noticeGrants" => $funding_noticeGrants,
//				"postedDate" => $posted_date,
//                "dueDate" => $due_date
//                );

//empty array to hold count(s) later
$count_value = array();

if($stmt->prepare($funding_recommended)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result !== false) {
		$columns = $result->fetch_assoc();
		$count_value["recommended"] = $columns['count'];
	}
}
if($stmt->prepare($funding_shared)){
	
	$stmt->execute();
	$result = $stmt->get_result();
	if($result !== false) {
		$columns = $result->fetch_assoc();
		$count_value["shared"] = $columns['count'];
	}
}
if($stmt->prepare($funding_favorited)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result !== false) {
		$columns = $result->fetch_assoc();
		$count_value["favorited"] = $columns['count'];
	}
}
if($stmt->prepare($funding_sourceFBO)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result !== false) {
		$columns = $result->fetch_assoc();
		$count_value["sourceFBO"] = $columns['count'];
	}
}
if($stmt->prepare($funding_sourceGrants)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result !== false) {
		$columns = $result->fetch_assoc();
		$count_value["sourceGrants"] = $columns['count'];
	}
}

$agencyArray = array();
$agencyPrefix = "agency=";

//filter for All Agencies
if($stmt->prepare($funding_agency_total)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result !== false) {
		
		$columns = $result->fetch_assoc();
		if($columns !== false){
			$agency = array(
				"groupItem" => "All",
				"amount" => $columns['count'],
				"filterName" => $agencyPrefix . 'All'
			   );
			array_push($agencyArray, $agency);
		}
	}
}
//filter for specific names
if($stmt->prepare($agency_name)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result !== false) {
		while(($columns = $result->fetch_assoc())) {
			$agency = array(
				"groupItem" => $columns["agency"],
				"amount" => $columns["count"],
				"filterName" => $agencyPrefix . $columns["agency"]
			);
			array_push($agencyArray, $agency);
		}
	}
}

$noticeArray = array();
$noticePrefix = "notice=";

//filter for notice types in FBO
if($stmt->prepare($funding_noticeFBO)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result !== false) {
		while(($columns = $result->fetch_assoc())) {
			$notice = array(
				"groupItem" => $columns["notice_type"],
				"amount" => $columns["count"],
				"filterName" => $noticePrefix . $columns["notice_type"]
			);
			array_push($noticeArray, $notice);
		}
	}
}
//filter for notice types in Grants
if($stmt->prepare($funding_noticeGrants)){
	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result !== false) {
		while(($columns = $result->fetch_assoc())) {
			$notice = array(
				"groupItem" => $columns["instrument_type"],
				"amount" => $columns["count"],
				"filterName" => $noticePrefix . $columns["instrument_type"]
			);
			array_push($noticeArray, $notice);
		}
	}
}

//if(isset($count_value['favorited'])){
//    echo 'it worked';
//}
//else{
//    echo 'it failed';
//}
	
$data = array(
	array(
		"header" => "Source",
		"items" => array(
			array(
				"groupItem" => "Recommended",
				"amount" => $count_value['recommended'],
				"filterName" => "recommended"
			),
			array(
				"groupItem" => "Shared To Me",
				"amount" => $count_value['shared'],
				"filterName" => "shared"
			),
			array(
				"groupItem" => "Favorited",
				"amount" => $count_value['favorited'],
				"filterName" => "favorited"
			),
			array(
				"groupItem" => "FedBizOpps",
				"amount" => $count_value['sourceFBO'],
				"filterName" => "sourceFBO"
			),
			array(
				"groupItem" => "Grants",
				"amount" => $count_value['sourceGrants'],
				"filterName" => "sourceGrants"
			)
		)
	),
	array(
		"header" => "Agency",
		"items" =>
			$agencyArray
	),
	array(
		"header" => "Notice Type",
		"items" =>
			$noticeArray
	),
);

$stmt->close();
$mysqli->close();

// encodes the $data to json and then echos it back to client
echo json_encode($data);
?>
