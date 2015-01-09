<?php

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
