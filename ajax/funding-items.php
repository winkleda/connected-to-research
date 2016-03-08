<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

//session by logged in user
$email = $_SESSION['email'];

//type of funding to filter by
$type = $_GET['type'];
//echo $type; //for testing

$agencyPrefix = "agency=";
$agencyAllPrefix = "agency=All";
$noticePrefix = "notice=";
$postDatePrefix = "postDate=";
$dueDatePrefix = "dueDate=";

if(strncmp($type, $agencyPrefix, strlen($agencyPrefix)) == 0)  {
    if(strncmp($type, $agencyAllPrefix, strlen($agencyAllPrefix)) == 0) {
        $query = "SELECT *, LEFT(description, 300) as description
            FROM ctr_funding_base b, ctr_user_fund_link u
            WHERE b.id = u.fund_id
            AND u.email = ?
            AND due_date >= CURDATE()
            GROUP BY agency
            ORDER BY b.post_date DESC";
    }
    else {
        $query = "SELECT *, LEFT(description, 300) as description
            FROM ctr_funding_base b, ctr_user_fund_link u
            WHERE b.id = u.fund_id
            AND u.email = ?
            AND b.agency=\"" . str_replace( $agencyPrefix, "", $type) . "\" 
            ORDER BY b.agency DESC";
//    echo $query; //for testing
    }
}
else if(strncmp($type, $noticePrefix, strlen($noticePrefix)) == 0)  {
    $query = "SELECT *
        FROM ctr_funding_base b, ctr_user_fund_link u
        WHERE b.id = u.fund_id
        AND u.email = ?
        AND b.notice=\"" . str_replace( $noticePrefix, "", $type) . "\" 
        ORDER BY b.post_date DESC";
}
else if(strncmp($type, $postDatePrefix, strlen($postDatePrefix)) == 0)  {
    $query = "SELECT *
        FROM ctr_funding_base b, ctr_user_fund_link u
        WHERE b.id = u.fund_id
        AND u.email = ?
        AND b.post_date=\"" . str_replace( $postDatePrefix, "", $type) . "\" 
        ORDER BY b.post_date DESC";
}
else if(strncmp($type, $dueDatePrefix, strlen($dueDatePrefix)) == 0)  {
    $query = "SELECT *
        FROM ctr_funding_base b, ctr_user_fund_link u
        WHERE b.id = u.fund_id
        AND u.email = ?
        AND b.due_date=\"" . str_replace( $dueDatePrefix, "", $type) . "\" ORDER BY b.due_date DESC";
}
else{
    switch($type) {
        case "recommended":
            $query = "SELECT *, LEFT(description, 300) as description 
                    FROM ctr_funding_base b, ctr_user_fund_link u
                    WHERE b.id = u.fund_id
                    AND email = ?
                    AND due_date >= CURDATE()
                    ORDER BY post_date DESC
                    LIMIT 0, 10";
            break;
//        case "shared":
//            $query = "SELECT *
//                    FROM ctr_funding_base b, ctr_user_share_fund s, ctr_user u
//                    WHERE b.id = s.fund_id
//                    AND s.shared_to = u.email
//                    AND u.email = ?";
        case "sourceFBO":
            $query = "SELECT *, LEFT(description, 300) as description 
                    FROM ctr_funding_base b, ctr_user_fund_link u
                    WHERE b.id = u.fund_id
                    AND u.email = ?
                    AND source = 'FedBizOpps'
                    AND due_date >= CURDATE()
                    ORDER BY post_date DESC
                    LIMIT 0, 5";
            break;
        case "sourceGrants":
            $query = "SELECT *, LEFT(description, 300) as description
                    FROM ctr_funding_base b, ctr_user_fund_link u
                    WHERE b.id = u.fund_id
                    AND u.email = ?
                    AND source = 'Grants'
                    AND due_date >= CURDATE()
                    ORDER BY post_date DESC
                    LIMIT 0, 5";
            break;
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
