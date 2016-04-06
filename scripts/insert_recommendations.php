<?php
ini_set('display_errors', 'On');
include 'connection.php';

/* user1 is a toxicologist. */
$user1 = 'user1';
$user1_classcodes = array(
//   'A',     // Research & Development
  'Q',      // Medical services
);
$user1_naics = array(
    '621511',   // Toxicology health laboratories
    '541712',   // Research and Development in the Physical, Engineering, and Life Sciences (except Biotechnology)
);
$query = "SELECT * FROM ctr_funding_base WHERE ";
foreach($user1_classcodes as $code){
    $query_list[] = "(interests LIKE BINARY '%cc:%" . $code . "%' AND interests NOT LIKE BINARY '%;naics:%" . $code . "%')";
}
foreach($user1_naics as $code){
    $query_list[] = "(interests LIKE BINARY '%naics:" . $code . "%')";
}
$query = $query . implode(" OR ", $query_list);

$result = $mysqli->query($query);
while($row = $result->fetch_assoc()) {
    $query = "INSERT INTO ctr_user_fund_link SET email = '" . $user1 . "', fund_id = '" . $row['id'] . "'";
    
    if(!$mysqli->query($query)) {
        echo "Query failed: " . $mysqli->error . '<br>';
    } else {
        echo "email " . $user1 . " has been associated with opportunity ". $row['id'] . " .<br>";
    }
}



?>

