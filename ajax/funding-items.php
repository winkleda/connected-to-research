<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];

//type of funding to filter by
$type = $_GET['type'];

switch($type) {
	case "sourceFBO":
		$query = "SELECT * 
				FROM ctr_funding_fbo f, ctr_funding_base b 
				WHERE b.id = f.sol_number AND u.email = ?
				ORDER BY post_date DESC";
		break;
	case "sourceGrants":
		$query = "SELECT *
				FROM ctr_funding_grants g, ctr_funding_base b
				WHERE b.id = g.opp_number AND u.email = ?
				ORDER BY post_date DESC";
		break;
	case "agency":
		$query = "SELECT *
				FROM ctr_funding_base
				ORDER BY agency DESC AND post_date DESC";
		break;
	case "noticeFBO":
		$query = "SELECT * 
				FROM ctr_funding_fbo f, ctr_funding_base b
                WHERE b.id = f.sol_number 
				ORDER BY notice_type AND post_date DESC";
		break;
	case "noticeGrants":
		$query = "SELECT * 
				FROM ctr_funding_grants g, ctr_funding_base b
                WHERE b.id = g.opp_number
				ORDER BY instrument_type AND post_date DESC";
		break;
	case "postedDate":
		$query = "SELECT * 
				FROM ctr_funding_base
				ORDER BY post_date DESC";
		break;
	case "dueDate":
		$query = "SELECT * 
				FROM ctr_funding_base
				ORDER BY due_date DESC";
		break;
	default:
		$query = "SELECT * 
				FROM ctr_funding_base
				ORDER BY post_date DESC";
}

$stmt = $mysqli->stmt_init();
if(!$stmt->prepare($query)) {
	echo "Prepared failed: " . $stmt->error;
}

//no params to bind
$stmt->execute();
$fundings = $stmt->get_result();

$data = array();
while($funding = $fundings->fetch_assoc()) {
    $data[] = array(
//        "funding" => $fundings[''],
        "title" => $fundings['title'],
        "url" => $fundings['url'],
        "agency" => $fundings['agency'],
        "postDate" => $fundings['post_date'],
        "dueDate" => $fundings['due_date'],
        "description" => $fundings['description'],
        "source" => $fundings['source']
    );
}


$stmt->close();
$mysqli->close();

echo json_encode($data);
?>
