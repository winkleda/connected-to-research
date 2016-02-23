<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];
$stmt = $mysqli->stmt_init();

$funding_recommended = "SELECT count(*) as count
        FROM ctr_user_fund_link
        WHERE email = ?";

//$funding_favorited = "SELECT count(*) as count
//        FROM ctr_user_fav_funding
//        WHERE email = ?";

//The funding source - FedBizOpps
$funding_sourceFBO = "SELECT count(*) as count
        FROM ctr_funding_fbo";

//The funding source - Grants
$funding_sourceGrants = "SELECT count(*) as count
        FROM ctr_funding_grants";


//agency total for clicking on all
$funding_agency_total = "SELECT count(*) as count
        FROM ctr_funding_base";

//query to filter by name and count
$agency_name = "SELECT agency, count(*) as count
        FROM ctr_funding_base
        GROUP BY agency
        ORDER BY count DESC
        LIMIT 0, 10";

//The notice type for FBO
$funding_noticeFBO = "SELECT notice_type, count(*) as count
        FROM ctr_funding_fbo
        GROUP BY notice_type
        ORDER BY count DESC
        LIMIT 0, 10";

//The notice type for Grants
$funding_noticeGrants = "SELECT instrument_type, count(*) as count
        FROM ctr_funding_grants
        GROUP BY instrument_type
        ORDER BY count DESC
        LIMIT 0, 10";

//Posted Date
$posted_date = "SELECT post_date, count(*) as count
        FROM ctr_funding_base
        GROUP BY post_date
        ORDER BY count DESC
        LIMIT 0, 5";
        //WHERE YEAR(post_date) = ?";

//Due Date
$due_date = "SELECT due_date, count(*) as count
        FROM ctr_funding_base
        GROUP BY due_date
        ORDER BY count DESC
        LIMIT 0, 5";
        //WHERE due_date = ?";


//assigning key => values to an array set to fields
$fields = array("sourceFBO" => $funding_sourceFBO, 
				"sourceGrants" => $funding_sourceGrants,
				"agency" => $funding_agency_total,
				"noticeFBO" => $funding_noticeFBO,
				"noticeGrants" => $funding_noticeGrants,
				"postedDate" => $posted_date,
                "dueDate" => $due_date
                );

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

$agencyArray = array();
$agencyPrefix = "agency=";

//filter for All Agencies
if($stmt->prepare($funding_agency_total)){
    
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

$postDateArray = array();
$dueDateArray = array();
$postDatePrefix = "postDate=";
$dueDatePrefix = "dueDate=";

//filter by post date
if($stmt->prepare($posted_date)){
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result !== false) {
        while(($columns = $result->fetch_assoc())) {
            $postDate = array(
                "groupItem" => $columns["post_date"],
                "amount" => $columns["count"],
                "filterName" => $postDatePrefix . $columns["post_date"]
            );
            array_push($postDateArray, $postDate);
        }   
    } 
}
//filter by due date
if($stmt->prepare($due_date)){
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result !== false) {
        while(($columns = $result->fetch_assoc())) {
            $dueDate = array(
                "groupItem" => $columns["due_date"],
                "amount" => $columns["count"],
                "filterName" => $dueDatePrefix . $columns["due_date"]
            );
            array_push($dueDateArray, $dueDate);
        }   
    } 
}

$data = array(
	array(
		"header" => "Source",
		"items" => array(
			array(
				"groupItem" => "Recommended",
				"amount" => $count_value['recommended'],
				"filterName" => "recommended"
			),
//			array(
//				"groupItem" => "Favorited",
//				"amount" => $count_value['favorited'],
//				"filterName" => "favorited"
//			),
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
    array(
		"header" => "Post Date",
		"items" =>
            $postDateArray
	),
    array(
		"header" => "Due Date",
		"items" =>
            $dueDateArray
	),
);

$stmt->close();
$mysqli->close();

// encodes the $data to json and then echos it back to client
echo json_encode($data);
?>
