<?php

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



$data = array(
	array(
		"title" => "Computer-supported Cooperate Work and Social Computing",
		"date" => "march 14-18",
		"location" => "Vancoover Washington, USA",
		"impactfactor" => "",
		"description" => "",
		"events" => array(
			array(
				"event" => "Papers",
				"date" => "june 10"
			),
			array(
				"event" => "workshops",
				"date" => "june 14"
			),	
			array(
				"event" => "interactive posters",
				"date" => "june 19"
			)
			
		)
	),

	array(
		"title" => "Delivery Confrenece",
		"date" => "may 10-14",
		"location" => "New New York, usa",
		"impactfactor" => 221,
		"description" => "Conferenc for delivery people",
		"events" => array(
			array(
				"event" => "Papers",
				"date" => "june 10"
			),
			array(
				"event" => "workshops",
				"date" => "june 14"
			),	
			array(
				"event" => "interactive posters",
				"date" => "june 19"
			)
		)
	),
	array(
		"title" => "computer-supported cooperate work and social computing",
		"date" => "march 14-18",
		"location" => "vancoover washington, usa",
		"impactfactor" => "",
		"description" => "",
		"events" => array(
			array(
				"event" => "Papers",
				"date" => "june 10"
			),
			array(
				"event" => "workshops",
				"date" => "june 14"
			),	
			array(
				"event" => "interactive posters",
				"date" => "june 19"
			)
			
		)
	),
	array(
		"title" => "computer-supported cooperate work and social computing",
		"date" => "march 14-18",
		"location" => "vancoover washington, usa",
		"impactfactor" => "",
		"description" => "",
		"events" => array(
			array(
				"event" => "Papers",
				"date" => "june 10"
			),
			array(
				"event" => "workshops",
				"date" => "june 14"
			),	
			array(
				"event" => "interactive posters",
				"date" => "june 19"
			)
			
		)
	)

);

echo json_encode($data);

?>
