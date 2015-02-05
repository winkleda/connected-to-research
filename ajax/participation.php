<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* Find user id*/
$sql = "SELECT user_id 
		FROM ctr_user 
		WHERE email = '$_SESSION[email]'";
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();

/* Queries first 3 articles from database */
$sql = "SELECT * 
		FROM ctr_user_call_link c, ctr_call_for_part p
		WHERE c.call_id = p.p_id AND c.user_id = '$user[user_id]'";
$calls = $mysqli->query($sql);

// the data structure for the participation column
//
// everything in <> is a comment to be what is added
//
// [	< this will send an array of items to the client with all the participation information >
// 		< the following is just one item but all subseqent items will have the same structure and varibales >
// 		
// 		{
// 			"title" : <the title for the participation main event>;
// 			"date" : <date that the main even it on>;
// 			"location" : <the location where the event will be held>;
//			"impactfactor" : <the impact factor of the main event>;
//			"description" : < the description of the event >;
//			"events" : [ < this is an array of events to be held inside the main event there can be as many events in here as you want, it will have the same structrue as what is here>
//					{ < each one of these will be an array >
//						"event" : <name of the event that will be heald durring the participation>;
//						"date" : < the date of the specific event >;
//					}
//
//			];
//
//	
//
//
// ]


// INSERT INTO ctr_user_call_link (user_id, call_id)
// VALUES (1, 1),
// (1, 2),
// (1, 3),
// (1, 4),
// (1, 5),
// (1, 6),
// (1, 7),
// (1, 8),
// (1, 9),
// (1, 10),
// (1, 11),
// (1, 12),
// (1, 13),
// (1, 14),
// (1, 15),
// (1, 16),
// (1, 17),
// (1, 18),
// (1, 19),
// (1, 20)


//$data = array();
while ($call = $calls->fetch_assoc()) {
	$data[] = array(
		"title" => $call['title'],
		"date" => $call['p_date'],
		"location" => $call['location'],
		//"impactfactor" => "",
		"description" => $call['description'],
		// "events" => array(
		// 	array(
		// 		"event" => "Papers",
		// 		"date" => "june 10"
		// 	),
		// 	array(
		// 		"event" => "workshops",
		// 		"date" => "june 14"
		// 	),	
		// 	array(
		// 		"event" => "interactive posters",
		// 		"date" => "june 19"
		// 	)
		// )
	);
}
echo json_encode($data);

?>
