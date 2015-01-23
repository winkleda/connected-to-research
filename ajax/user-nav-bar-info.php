<?php
ini_set('display_errors', 'On');
include '../scripts/connection.php';

session_start();
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
$email = $_SESSION['email'];

$sql = "SELECT * FROM ctr_user WHERE email = '$email'";
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();

$data = array(
	"firstName" => $user['name_f'],
	"lastName" => $user['name_l'],
	"userImgSrc" => $user['user_img_src'],
	"occupation" => "Research Scientist"
);

echo json_encode($data);

?>
