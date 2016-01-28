<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];
$stmt = $mysqli->stmt_init();

//The funding source - FedBizOpps 
$funding_sourceFBO = "SELECT count(*) as count
        FROM ctr_funding_fbo f JOIN ctr_funding_base b
        WHERE b.id = f.sol_number";

//The funding source - Grants
$funding_sourceGrants = "SELECT count(*) as count
        FROM ctr_funding_grants g JOIN ctr_funding_base b
        WHERE b.id = g.opp_number";

//The agency the funding is from
$funding_agency = "SELECT agency, count(*) as count
        FROM ctr_funding_base
        GROUP BY agency";

//The notice type for FBO
$funding_noticeFBO = "SELECT notice_type, count(*) as count
        FROM ctr_funding_fbo
        GROUP BY notice_type";

//The notice type for Grants
$funding_noticeGrants = "SELECT instrument_type, count(*) as count
        FROM ctr_funding_grants
        GROUP BY instrument_type";

//Posted Date
$posted_date = "SELECT count(*) as count
        FROM ctr_funding_base
        WHERE post_date = ?";

//Due Date
$due_date = "SELECT count(*) as count
        FROM ctr_funding_base
        WHERE due_date = ?";

//assigning key => values to an array set to fields
$fields = array("sourceFBO" => $funding_sourceFBO, 
				"sourceGrants" => $funding_sourceGrants,
				"agency" => $funding_agency,
				"noticeFBO" => $funding_noticeFBO,
				"noticeGrants" => $funding_noticeGrants,
				"postedDate" => $posted_date,
                "dueDate" => $due_date);

//empty array to hold count(s) later
$count_value = array();

//function executes the queries above, then stores the counts in the empty array
foreach($fields as $key => $query) {
	
	if(!$stmt->prepare($query)) {
		echo "preparing stmt failed: " . $stmt->error;
	}
//need to check the binding parameters again
	$stmt->bind_param("ssss", $agency, $notice_type, $post_date, $due_date);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$columns = $result->fetch_assoc();
	$count_value[$key] = $columns['count'];
}

$data = array(
	array(
		"header" => "Source",
		"items" => array(
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
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => $count_value['agency'],
				"filterName" => "agency"
			)
		)
	),
    array(
		"header" => "Notice Type",
		"items" => array(
			array(
				"groupItem" => "FedBizOpps",
				"amount" => $count_value['noticeFBO'],
				"filterName" => "noticeFBO"
			),
			array(
				"groupItem" => "Grants",
				"amount" => $count_value['noticeGrants'],
				"filterName" => "noticeGrants"
			)
		)
	),
    array(
		"header" => "Date",
		"items" => array(
			array(
				"groupItem" => "Posted Date",
				"amount" => $count_value['postedDate'],
				"filterName" => "postedDate"
			),
			array(
				"groupItem" => "Due Date",
				"amount" => $count_value['dueDate'],
				"filterName" => "dueDate"
			)
		)
	),
);

$stmt->close();
$mysqli->close();

// encodes the $data to json and then echos it back to client
echo json_encode($data);
?>
