<?php

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

echo json_encode($data);
?>
