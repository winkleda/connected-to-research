<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

$email = $_SESSION['email'];

//get funding deadlines for the user
if (!($stmt = $mysqli->prepare(
	"SELECT due_date, title
	FROM ctr_funding_base b, ctr_user_fund_link u
	WHERE b.id = u.fund_id AND u.email = ?
	ORDER BY b.due_date"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("s", $email)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->execute();
$result = $stmt->get_result();

//array for data and months
$data = array();
$months = array();

$current_date = date("Y-m-d");

//everything from here on needs more testing
while($fund = $result->fetch_assoc()){
    //convert due_date from string to time in order of monthYear, set $temp_month as due_date 
	$temp_month = date("mY", strtotime($fund['due_date']));
    //return integer value of $temp_month
	$temp_month = intval($temp_month);
    //convert due_date from string to time with only Year and set to $temp_year
	$temp_year = date("Y", strtotime($fund['due_date']));

	$months[$temp_month][] = ["date" => intval(date("d", strtotime($fund['due_date']))), 
                              "event" => $fund["title"], 
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
