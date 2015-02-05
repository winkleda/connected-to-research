<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

// the data structure that needs to be sent to the client.
//
// everything in <> is a comment
//
// [ <this is in an array of items for the events and deadlines>
// 		<this is just one item of the array but the array can hold as many as needed>
// 		{
// 			"month" : <month of the year that you have the events>;
// 			"montAway" : <how many months away the month is>;
// 			"events: [ < an array of events that are in the month, just putting one in here but can have as many as needed>
//				{
//					"date" : <day of the month that the event is>;
//					"event" : < the event name >;
//				}
// 			];
// 		};
// ]
//

session_start();


// Find user id 
$get_user_id = "SELECT user_id
		FROM ctr_user
		WHERE email = '$_SESSION[email]'";
$result = $mysqli->query($get_user_id);
$user = $result->fetch_assoc();



// 1 should be user[user_id]
/* gets associated research and event deadlines for the user */
$get_user_reds = "
	SELECT *
	FROM ctr_re_deadlines a, ctr_user_red_link u
	WHERE a.re_id = u.research_id AND u.user_id = '$user[user_id]' AND re_date >= CURDATE() 
	ORDER BY re_date ASC";

$user_red = $mysqli->query($get_user_reds);

/* creates the array to send to the client */
$data = array();
$months = array();

$current_date = date("Y-m-d");

while($red = $user_red->fetch_assoc()){
	$temp_month = date("m",strtotime($red["re_date"]));
	$temp_month = intval($temp_month);

	$months[$temp_month][] = ["date" => intval(date("d",strtotime($red["re_date"]))), "event" => $red["re_title"]];	
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

/*
$data = array(
	array(
		"month" => "November",
		"monthAway" => 1,
		"events" => array(
			array(
				"date" => 5,
				"event" => "Company Meeting"
			),
			array(
				"date" => 11,
				"event" => "Company Meeting"
			),
			array(
				"date" => 16,
				"event" => "Company Meeting"
			),
			array(
				"date" => 24,
				"event" => "Company Meeting"
			)
		)
	),
	array(
		"month" => "December",
		"monthAway" => 2,
		"events" => array(
			array(
				"date" => 5,
				"event" => "Company Meeting"
			),
			array(
				"date" => 11,
				"event" => "Company Meeting"
			),
			array(
				"date" => 16,
				"event" => "Company Meeting"
			),
			array(
				"date" => 24,
				"event" => "Company Meeting"
			)
		)
	),
	array(
		"month" => "January",
		"monthAway" => 3,
		"events" => array(
			array(
				"date" => 5,
				"event" => "Company Meeting"
			),
			array(
				"date" => 11,
				"event" => "Company Meeting"
			),
			array(
				"date" => 16,
				"event" => "Company Meeting"
			),
			array(
				"date" => 24,
				"event" => "Company Meeting"
			)
		)
	),
	array(
		"month" => "Febuary",
		"monthAway" => 4,
		"events" => array(
			array(
				"date" => 5,
				"event" => "Company Meeting"
			),
			array(
				"date" => 11,
				"event" => "Company Meeting"
			),
			array(
				"date" => 16,
				"event" => "Company Meeting"
			),
			array(
				"date" => 25,
				"event" => "Company Meeting"
			)
		)
	),

	);
 */

echo json_encode($data);
?>
