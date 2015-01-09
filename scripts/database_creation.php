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
	name_f varchar(255) not null,
	name_l varchar(255) not null,
	email  varchar(255) not null,
	password varchar(255) not null,
	user_img_src varchar(255),
	user_occ varchar(255)
)";

//the user article link table creation sql
$sql_create_user_article_link = "CREATE TABLE user_article_link (
	id int unsigned auto_increment primary key,
	article_id int unsigned not null,
	user_id int unsigned not null,
	status varchar(255) not null
)";

// the create article sql
$sql_create_article = "CREATE TABLE article (
	type varchar(255),
	id int unsigned auto_increment primary key,
	title text not null,
	a_date date,
	keywords text,
	reprint varchar(255),
	startpage mediumint unsigned,
	endpage mediumint unsigned,
	j_name varchar(255),
	article_id mediumint unsigned,
	j_issue smallint unsigned,
	j_volume smallint unsigned,
	abstract text,
	url varchar(255),
	isbn_issn varchar(255),
	lang varchar(255),
	discipline varchar(255)
)";


// the user research and events link create table sql
$sql_create_user_red_link = "CREATE TABLE user_red_link (
	research_id int unsigned,
	user_id int unsigned,
	id int unsigned auto_increment primary key
)";

// the research events and deadlines create table sql
$sql_create_re_deadlines = "CREATE TABLE re_deadlines (
	re_date date,
	re_id int unsigned auto_increment primary key,
	title varchar(255) not null,
	location varchar(255),
	status varchar(255)
)";

// the call for participation create table sql
$sql_create_call_for_part = "CREATE TABLE call_for_part (
	p_date date,
	title varchar(255),
	location varchar(255),
	p_id int unsigned auto_increment primary key,
	impact_fact mediumint unsigned,
	description varchar(255)
)";

// call for participation item table creation sql
$sql_create_call_part_item = "CREATE TABLE call_part_item (
	title varchar(255),
	i_date date,
	link_id int unsigned,
	i_id int unsigned auto_increment primary key
)";


$sql_table_creation_array = array(
	$sql_create_user,
	$sql_create_user_article_link,
	$sql_create_article,
	$sql_create_user_red_link,
	$sql_create_re_deadlines,
	$sql_create_call_for_part,
	$sql_create_call_part_item
);



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

//
$i = 1;
foreach ($sql_table_creation_array as $sql){
	if ($conn->query($sql) === TRUE) {
		echo "table " .$i. "  create successfully".$br;
	}else{
		echo "error creating table " .$i. ": ". $conn->error . $br;
	}
	$i++;
}

$conn->close();

?>
</p>