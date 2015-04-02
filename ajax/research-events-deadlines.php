<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

$email = $_SESSION['email'];

/* Gets associated research and event deadlines for the user */
if (!($stmt = $mysqli->prepare(
	"SELECT p_date, title
	FROM ctr_call_for_part c, ctr_user_red_link u
	WHERE c.p_id = u.research_id AND u.email = ?"))) {
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

$month_to_num = array(
	"Jan" => "01",
	"Feb" => "02",
	"Mar" => "03",
	"Apr" => "04",
	"May" => "05",
	"Jun" => "06",
	"Jul" => "07",
	"Aug" => "08",
	"Sep" => "09",
	"Oct" => "10",
	"Nov" => "11",
	"Dec" => "12");

while($red = $result->fetch_assoc()){
	$output = preg_split("/[\s,-]+/", $red["p_date"]);  //split the string by comma, space and dash
	$formatted_date = $output[2] . "-" . $month_to_num[$output[0]] . "-" . $output[1];

	$temp_month = date("m", strtotime($formatted_date));
	$temp_month = intval($temp_month);

	$months[$temp_month][] = ["date" => intval(date("d",strtotime($formatted_date))), "event" => $red["title"]];	
}

$current_month_num = intval(date("m"));
$months_keys = array_keys($months);

foreach($months_keys as $month_key) {
	$current_month_array = array();
	$month_name = date("F", mktime( 0, 0, 0, $month_key));
 	$current_month_array["month"] = $month_name;
	$month_away = $month_key - intval(date("n"));
	$current_month_array["monthAway"] = $month_away;
	$current_month_array["events"] = $months[$month_key];

	$data[] = $current_month_array;	
	
}

echo json_encode($data);
?>
