<?php

// the data structure that needs to be sent to the client
//
//  everything in <> is a comment about what should be in the item
//
//  { <there is not an array of the same items in this one since it is just for the current user>
//  	"firstName" : <first name of the user>;
//  	"lastName" : <last name of the user>;
//  	"userImgSrc" : < the image source of the user image >;
//  	"occupation" : < the occupation of the user >;
//  }
//  	



$data = array(

	"firstName" => "Hubert",
	"lastName" => "Farnsworth",
	"userImgSrc" => "./img/professor_farnsworth_image.png",
	"occupation" => "Research Scientist"

);

echo json_encode($data);

?>
