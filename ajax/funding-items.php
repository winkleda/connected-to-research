<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];

//type of funding to filter by
$type = $_GET['type'];
//echo $type;

$agencyPrefix = "agency=";
if(strncmp($type, $agencyPrefix, strlen($agencyPrefix)) == 0)  {
    $query = "SELECT *
        FROM ctr_funding_base b, ctr_user_fund_link u
        WHERE b.id = u.fund_id
        AND u.email = ?
        AND b.agency=\"" . str_replace( $agencyPrefix, "", $type) . "\" ORDER BY b.agency DESC";
    echo $query;
}
else{
switch($type) {
//    case "recommended":
//        $query = "SELECT * 
//				FROM ctr_user_fund_link
//                LIMIT 0, 5";
//        break;
	case "sourceFBO":
		$query = "SELECT * 
				FROM ctr_funding_base b, ctr_user_fund_link u
				WHERE b.id = u.fund_id
                AND u.email = ?
                AND source = 'FedBizOpps'
                AND due_date >= CURDATE()
				ORDER BY post_date DESC
                LIMIT 0, 5";
		break;
	case "sourceGrants":
		$query = "SELECT *
				FROM ctr_funding_base b, ctr_user_fund_link u
                WHERE b.id = u.fund_id
                AND u.email = ?
				AND source = 'Grants'
                AND due_date >= CURDATE()
				ORDER BY post_date DESC
                LIMIT 0, 5";
		break;
//	case $agencyPrefix . "All":
//		$query = "SELECT *
//				FROM ctr_funding_base
//				ORDER BY agency DESC AND post_date DESC";
//		break;
//	case "noticeFBO":
//		$query = "SELECT * 
//				FROM ctr_funding_fbo f, ctr_funding_base b
//                WHERE b.id = f.sol_number 
//				ORDER BY notice_type AND post_date DESC";
//		break;
//	case "noticeGrants":
//		$query = "SELECT * 
//				FROM ctr_funding_grants g, ctr_funding_base b
//                WHERE b.id = g.opp_number
//				ORDER BY instrument_type AND post_date DESC";
//		break;
//	case "postedDate":
//		$query = "SELECT * 
//				FROM ctr_funding_base
//				ORDER BY post_date DESC";
//		break;
//	case "dueDate":
//		$query = "SELECT * 
//				FROM ctr_funding_base
//				ORDER BY due_date DESC";
//		break;
	default:
		$query = "SELECT * 
				FROM ctr_funding_base
				ORDER BY post_date DESC";
}
}
    
$stmt = $mysqli->stmt_init();
if(!$stmt->prepare($query)) {
	echo "Prepared failed: " . $stmt->error;
}

//echo $query;

$stmt->bind_param("s", $email);
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
