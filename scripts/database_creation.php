<p>
<?php
//user names and passwords when you are using
//xampp can change these when doing stuff on the 
//server

$serverName = "localhost";
$userName = "root";
$password = "";

// this is a break tag for the end of the 
// echos to make the page easier to read 
// for debugging and what not
$br = "<br/>";

// the following are the sql statements for table
// creation for the different tables for the database

// the user table creation sql 
$sql_create_user = "CREATE TABLE user (
	user_id int unsigned auto_increment primary key,
	name_f varchar not null,
	name_l varchar not null,
	email  varchar not null,
	password varchar not null,
	user_img_src varchar,
	user_occ varchar
)";

//the user article link table creation sql
$sql_create_user_article_link = "CREATE TABLE user_article_link (
	id int unsigned auto_increment primary key,
	article_id int not null,
	user_id int not null,
	status varchar not null
)";




$conn = new mysqli($serverName, $userName, $password);

if ($conn->connect_error){
	die("connection failed: " . $conn->connect_error.$br  ) ;	
}
echo "Connected Successfully ".$br;

$db_name = "connected_to_research";

$create_db_sql = "CREATE DATABASE " . $db_name;

if ($conn->query($create_db_sql)){
	echo "database was created".$br;
}else{
	echo "error creating database: ". $conn->error.$br;
}

$conn->close();


$conn = new mysqli($serverName, $userName, $password, $db_name);

if ($conn->connect_error){
	die("connection failed: " . $conn->connect_error.$br );	
}
echo "Connected Successfully to ". $db_name . $br;

$sql = "CREATE TABLE MyTable (
	id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	firstname VARCHAR(30) NOT NULL,
	lastname VARCHAR(30) NOT NULL,
	email VARCHAR(50),
	reg_dat TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
	echo "table mytable created successfully".$br;
}else{
	echo "error creating table: ". $conn->error . $br;
}
$conn->close();

?>
</p>
