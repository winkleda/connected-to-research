<?php
ini_set('display_errors', 'On');
include 'connection.php';

// List all image types we can download, in terms of HTTP content types.
$valid_image_types = array(
    'image/png',
    'image/png',
    'image/jpg',
    'image/jpeg',
    'image/jpe',
    'image/gif',
    'image/tif',
    'image/tiff',
    'image/svg',
    'image/ico',
    'image/icon',
    'image/x-icon'
);

// Construct image name from supplied agency name.
if(!isset($_GET['image']) || !isset($_GET['agency'])){
    echo "Failed to get image: need an agency and an image URL";
    return -1;
}
$image_name = str_replace(" ", "_", $_GET['agency']);
$image_url = $_GET['image'];

// Get the HTTP headers from the URL and check if the headers returned a file
// type. If they did, check if the type is an image. If it is, then we can try
// to download it. Otherwise, we should quit.
$url_headers = get_headers($image_url, 1);
if(!isset($url_headers['Content-Type'])){
    echo "Could not check file type: failed to get HTTP Headers.";
    return -1;
}
$type = strtolower($url_headers['Content-Type']);
if(!in_array($type, $valid_image_types)){
    echo "Could not download image: improper file type.";
    return -1;
}

// Now we know we have an image. So we download it with the same extension.
$extension = str_replace("image/", "", $type);
$file_name = $image_name. "." .$extension;
$image_destination = "../img_agency/" . $file_name;

$result = file_put_contents($image_destination, file_get_contents($image_url));
if(!$result){
    echo "Failed to download image.";
    return -1;
}

$query = "INSERT INTO ctr_agency_images SET
        agency = '" .$_GET['agency']. "',
        image = '" .$file_name. "';";

$result = $mysqli->query($query);
if(!$result){
	echo "Query failed: " . $mysqli->error;
} 
else{
	echo "Image for " .$_GET['agency']. " has been uploaded as " .$file_name;
}
?>