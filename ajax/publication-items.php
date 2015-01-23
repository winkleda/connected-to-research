<?php

// structrue of the data to be sent to the client
//
// <this will be an array of publication items>
// [ <you can put as many items as you want into this array, but thy need too folow the same structure that will be given for this one item >
//		{
//			"tagList" : [ < an array of the tags for that specific article > ];
//			"publication" : < the publication that the item is from >;
//			"title" : < the title for the publication item >;
//			"userInterest" : < how many users are interested in the item > ;
//			"authors" : [ < an array of the athors of the publicaiton item > ] ;
//			"abstract" : < the abstract for the article > ;
//			"imageSrc" : < the source of the image that will go with the article if there is one>;
//		}
// ]
//



$data = array(
	array(
		"tagList" => array(
			"Visual Analitics",
			"Vision",
			"Text Analitics"
		),
		"publication" => "IEEE VAST",
		"title" => "UTOPIAN: user-driven topic modeliing based on interactive nonnegative matrix factorization",
		"userInterest" => 22,
		"authors" => array(
			"Professor Farnsworth", "Philip J. Fry"
		),
		"abstract" => "something about some other things and it looks kinda cool because you have thiese things that show up and you dont know what is going on and then some other things happen like bill cosby showing up and giving you a sweater",
	 	"imageSrc" => "./img/test-image-1.jpg"

	
	),	
	array(
		"tagList" => array(
			"Visual Analitics",
			"Vision",
			"Text Analitics"
		),
		"publication" => "IEEE VAST",
		"title" => "UTOPIAN: user-driven topic modeliing based on interactive nonnegative matrix factorization",
		"userInterest" => 22,
		"authors" => array(
			"Professor Farnsworth", "Philip J. Fry"
		),
		"abstract" => "something about some other things and it looks kinda cool because you have thiese things that show up and you dont know what is going on and then some other things happen like bill cosby showing up and giving you a sweater",
	 	"imageSrc" => "./img/test-image-1.jpg"

	
	)	
);

echo json_encode($data);

?>
