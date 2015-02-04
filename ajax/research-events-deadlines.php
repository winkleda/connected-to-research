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

/*   !!!!! commenting out for now for test puropses

// Find user id 
$get_user_id = "SELECT user_id
		FROM ctr_user
		WHERE email = '$_SESSION[email]'";
$result = $mysqli->query($get_user_id);
$user = $result->fetch_assoc();


 */

// 1 should be user[user_id]
/* gets associated research and event deadlines for the user */
$get_user_reds = "
	SELECT *
	FROM ctr_re_deadlines a, ctr_user_red_link u
	WHERE a.re_id = u.research_id AND u.user_id = 1 
	ORDER BY re_date ASC";

$user_red = $mysqli->query($get_user_reds);

/* creates the array to send to the client */
$data = array();
$months = array(
	1 => array(),
	2 => array(),
	3 => array(),
	4 => array(),
	5 => array(),
	6 => array(),
	7 => array(),
	8 => array(),
	9 => array(),
	10 => array(),
	11 => array(),
	12 => array()
);


while($red = $user_red->fetch_assoc()){
	echo "<p>";
	var_dump($red);
	echo "</p>";	
	
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
