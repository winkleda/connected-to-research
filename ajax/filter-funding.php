<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];
$stmt = $mysqli->stmt_init();

//The funding source - FedBizOpps
$funding_sourceFBO = "SELECT count(*) as count
        FROM ctr_funding_fbo";

//The funding source - Grants
$funding_sourceGrants = "SELECT count(*) as count
        FROM ctr_funding_grants";

//The agency the funding is from
$funding_agency = "SELECT agency, count(*) as count
        FROM ctr_funding_base
        ORDER BY count DESC";

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
        WHERE YEAR(post_date) = ?";

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
                "dueDate" => $due_date
                );

//empty array to hold count(s) later, might not need
//$count_value = array();

if($stmt->prepare($funding_sourceFBO)){
    
    $stmt->execute();
    $result = $stmt->get_result();
    if($result !== false) {
        $columns = $result->fetch_assoc();
        $count_value["sourceFBO"] = $columns['count'];
    }   
}
if($stmt->prepare($funding_sourceGrants)){
    
    $stmt->execute();
    $result = $stmt->get_result();
    if($result !== false) {
        $columns = $result->fetch_assoc();
        $count_value["sourceGrants"] = $columns['count'];
    }
}
if($stmt->prepare($funding_agency)){
    
    $stmt->execute();
    $result = $stmt->get_result();
    if($result !== false) {
        $columns = $result->fetch_assoc();
        $count_value["agency"] = $columns['count'];
    }
}
if($stmt->prepare($funding_noticeFBO)){
    
    $stmt->execute();
    $result = $stmt->get_result();
    if($result !== false) {
        $columns = $result->fetch_assoc();
        $count_value["noticeFBO"] = $columns['count'];
    }
}
if($stmt->prepare($funding_noticeGrants)){
    
    $stmt->execute();
    $result = $stmt->get_result();
    if($result !== false) {
        $columns = $result->fetch_assoc();
        $count_value["noticeGrants"] = $columns['count'];
    }
    
}
if($stmt->prepare($posted_date)){
    
    $stmt->bind_param("s", $post_date);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result !== false) {
        $columns = $result->fetch_assoc();
        $count_value["postedDate"] = $columns['count'];
    }
}
if($stmt->prepare($due_date)){
    
    $stmt->bind_param("s", $due_date);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result !== false) {
        $columns = $result->fetch_assoc();
        $count_value["dueDate"] = $columns['count'];
    } 
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
