<?php
include '../scripts/connection.php';

// This function will return an array with funding opp data in it.
function get_data($mysqli, $fundings) {
    $data = array();
    while($opp = $fundings->fetch_assoc()) {
        $item = array(
            "id" => $opp['id'],
            "source" => $opp['source'],
            "title" => $opp['title'],
            "post_date" => $opp['post_date'],
            "due_date" => $opp['due_date'],
            "agency" => $opp['agency'],
            "address" => $opp['address'],
            "contact" => $opp['contact'],
            "office" => $opp['office'],
            "url" => $opp['url'],
            "description" => $opp['description']
        );
        if($opp['source'] == 'FedBizOpps'){
            $item = array_merge($item, 
                array(
                    "notice_type" => $opp['notice_type'],
                    "set_aside" => $opp['set_aside']
                )
            );
        }    
        if($opp['source'] == 'Grants'){
            $item = array_merge($item, 
                array(
                    "due_date_explanation" => $opp['due_date_explanation'],
                    "funding_total" => $opp['funding_total'],
                    "award_ceiling" => $opp['award_ceiling'],
                    "award_floor" => $opp['award_floor'],
                    "category_explanation" => $opp['category_explanation'],
                    "instrument_type" => $opp['instrument_type'],
                    "award_number" => $opp['award_number'],
                    "elegibility_category" => $opp['elegibility_category'],
                    "eligibility_info" => $opp['eligibility_info'],
                    "cost_sharing" => $opp['cost_sharing'],
                )
            );
        }
        
        $data[] = $item;
    }
    return $data;
}


//Base query:
// Do two queries, one for Grants and another for FBO.
$query_fbo = 
    "
    SELECT * FROM ctr_funding_base b, ctr_funding_fbo f
    WHERE b.id = f.sol_number
    ";
$query_grants = 
    "
    SELECT * FROM ctr_funding_base b, ctr_funding_grants g
    WHERE b.id = g.opp_number
    ";
// Then we add additional WHERE clauses for the input parameters

//Support ?q:
if(isset($_GET['q'])) {
    $query_string = mysqli_real_escape_string($mysqli, $_GET['q']);
    $query_common_params = 
        "
           b.id             = '%".$query_string."%'
        OR b.title          LIKE '%".$query_string."%'
        OR b.description    LIKE '%".$query_string."%'
        ";
    $query_grants_params = $query_common_params . 
        "
        OR g.due_date_explanation LIKE '%".$query_string."%'
        OR g.category_explanation LIKE '%".$query_string."%'
        OR g.eligibility_info LIKE '%".$query_string."%'
        ";
    // Now add the params to the original query:
    $query_fbo = $query_fbo . ' AND (' . $query_common_params . ')' ;
    $query_grants = $query_grants . ' AND (' . $query_grants_params . ')';
}

// Support PostDateBegin:
if(isset($_GET['PostDateBegin'])) {
    $milli = intval($_GET['PostDateBegin'], 10);
    $seconds = $milli/1000;
    $date = date("Y-m-d H:i:s", $seconds);
    
    $query_fbo = $query_fbo . " AND b.post_date > '" . $date . "'";
    $query_grants = $query_grants . " AND b.post_date > '" . $date . "'";
}
if(isset($_GET['PostDateEnd'])) {
    $milli = intval($_GET['PostDateEnd'], 10);
    $seconds = $milli/1000;
    $date = date("Y-m-d H:i:s", $seconds);
    
    $query_fbo = $query_fbo . " AND b.post_date < '" . $date . "'";
    $query_grants = $query_grants . " AND b.post_date < '" . $date . "'";
}
if(isset($_GET['DueDateBegin'])) {
    $milli = intval($_GET['DueDateBegin'], 10);
    $seconds = $milli/1000;
    $date = date("Y-m-d H:i:s", $seconds);
    
    $query_fbo = $query_fbo . " AND b.due_date > '" . $date . "'";
    $query_grants = $query_grants . " AND b.due_date > '" . $date . "'";
}
else {
    $query_fbo = $query_fbo . " AND b.due_date > CURDATE()";
    $query_grants = $query_grants . " AND b.due_date > CURDATE()";
}
if(isset($_GET['DueDateEnd'])) {
    $milli = intval($_GET['DueDateEnd'], 10);
    $seconds = $milli/1000;
    $date = date("Y-m-d H:i:s", $seconds);
    
    $query_fbo = $query_fbo . " AND b.due_date < '" . $date . "'";
    $query_grants = $query_grants . " AND b.due_date < '" . $date . "'";
}

$query_fbo = $query_fbo . " LIMIT 0,100";
$query_grants = $query_grants . " LIMIT 0,100";

echo $query_fbo . '<br><br>';
echo $query_grants . '<br><br>';

$queries = array(
    $query_fbo,
    $query_grants
);

$data = array();
foreach($queries as $query) {
    echo "QUERY: " . $query . "<br>";
    
    $stmt = $mysqli->stmt_init();
    if(!$stmt->prepare($query))
        echo "Prepared failed: " . $stmt->error;
    if(!$stmt->execute())
        echo "Execution failed: " . $stmt->error;
    $fundings = $stmt->get_result();
    $data = array_merge($data, get_data($mysqli, $fundings));
}
$stmt->close();


$mysqli->close();
echo "data: <br>";

/* 
** "That said, quotes " will produce invalid JSON, but this is only an issue if 
** you're using json_encode() and just expect PHP to magically escape your 
** quotes. You need to do the escaping yourself."
*/
//echo json_encode($data, JSON_HEX_QUOT);
$asd = json_encode($data, JSON_HEX_QUOT);
file_put_contents("json.json", $asd);

?>