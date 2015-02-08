<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();

/* Queries articles linked to current user */
$sql = "SELECT * 
		FROM ctr_user_call_link c, ctr_call_for_part p
		WHERE c.origin = p.origin AND c.email = '$_SESSION[email]'";
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
