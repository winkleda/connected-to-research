<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

$email = $_SESSION['email'];

/* Gets associated research and event deadlines for the user */
if (!($stmt = $mysqli->prepare(
	"SELECT start_date, title
	FROM ctr_call_for_part c, ctr_user_red_link u
	WHERE c.p_id = u.research_id AND u.email = ?
	ORDER BY c.p_date"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("s", $email)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->execute();
$result = $stmt->get_result();

/* creates the array to send to the client */
$data = array();
$months = array();

$current_date = date("Y-m-d");

while($red = $result->fetch_assoc()){
	$temp_month = date("mY", strtotime($red['start_date']));
	$temp_month = intval($temp_month);
	$temp_year = date("Y", strtotime($red['start_date']));

	$months[$temp_month][] = ["date" => intval(date("d",strtotime($red['start_date']))), 
							"event" => $red["title"], 
							"year" => $temp_year];	
}

$current_month_num = intval(date("m"));
$months_keys = array_keys($months);
$current_year = intval(date("Y"));

foreach($months_keys as $month_key) {
	$current_month_array = array();

	$month_digit = intval(floor($month_key / 10000));
	$month_name = date("F", mktime( 0, 0, 0, $month_digit));
	$current_month_array["month"] = $month_name;
	$month_away = $month_digit - intval(date("n"));
	//this is to fix the negative months away bug
	$years_away = $months[$month_key][0]["year"]- $current_year;
		
	$month_away = $month_away + ($years_away * 12);
	
	if($month_away < 0){
		$month_away = $month_away + 12;
	}
	$current_month_array["monthAway"] = $month_away;
	$current_month_array["events"] = $months[$month_key];

	$data[] = $current_month_array;
}

function compareByMonthAway($a, $b) {
	return $a['monthAway'] - $b['monthAway'] ;
}

usort($data, 'compareByMonthAway');

echo json_encode($data);
?>
