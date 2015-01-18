
<?php

// the structure of the json data and what to send back
//
// everything in <> is a comment to say what should be there
//
// this needs to be sent as a whole array to hold all the filters
//
// [
//   {  < this is just one filter column there will be multiple but they will all have the same structure > 
//	  	"header":" < name for the column >;
//	  	"items" : < this is an array of all the items for the filter >
//	  		[  < items holds an array of json objects that hold the groupitem and the amount this can hold any amount of group item and amount objects >
//	  			{
//	  				"groupItem": < name of group item >;
//	  				"amount": <amount of items in that group>;
//	  			}
//	  		]
//   },
// ]
//  








$data = array(
	array(
		"header" => "Type",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => 130
			),
			array(
				"groupItem" => "Journal Articles",
				"amount" => 114
			),
			array(
				"groupItem" => "Cited Work",
				"amount" => 98
			),
			array(
				"groupItem" => "Read",
				"amount" => 23
			)
			
		)
	),
	array(
		"header" => "Date",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => 129
			),
			array(
				"groupItem" => "Journal Articles",
				"amount" => 114
			),
			array(
				"groupItem" => "Cited Work",
				"amount" => 98
			),
			array(
				"groupItem" => "Read",
				"amount" => 23
			)
			
		)
	),
	array(
		"header" => "Filter",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => 129
			),
			array(
				"groupItem" => "Journal Articles",
				"amount" => 114
			),
			array(
				"groupItem" => "Cited Work",
				"amount" => 98
			),
			array(
				"groupItem" => "Read",
				"amount" => 23
			)
			
		)
	),
	array(
		"header" => "Other",
		"items" => array(
			array(
				"groupItem" => "All",
				"amount" => 129
			),
			array(
				"groupItem" => "Journal Articles",
				"amount" => 114
			),
			array(
				"groupItem" => "Cited Work",
				"amount" => 98
			),
			array(
				"groupItem" => "Read",
				"amount" => 23
			)
			
		)
	)


);

// encodes the $data varible to json and then echos it back to the client
echo json_encode($data);

?>

