<p>
<?php
ini_set('display_errors', 'On');
include 'connection.php';
//user names and passwords when you are using
//xampp can change these when doing stuff on the 
//server

// this is a break tag for the end of the 
// echos to make the page easier to read 
// for debugging and what not
$br = "<br/>";

// the following are the sql statements for table
// creation for the different tables for the database

// the user table creation sql 
$sql_create_user = "CREATE TABLE ctr_user (
	email  varchar(255) primary key,
	name_f varchar(255) not null,
	name_l varchar(255) not null,
	password varchar(255) not null,
	user_img_src varchar(255),
	user_occ varchar(255)
)";

//the user article link table creation sql
$sql_create_user_article_link = "CREATE TABLE ctr_user_article_link (
	email  varchar(255) primary key,
	origin varchar(255) not null,
	status varchar(255) not null
)";

// the create article sql
$sql_create_article = "CREATE TABLE ctr_article (
	id int unsigned auto_increment primary key,
	title varchar(255),
	type varchar(255),
	authors text,
	a_date smallint unsigned,
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
	discipline varchar(255),
	notes text,
	availability text,
	image_url varchar(255),
	origin varchar(255),
	UNIQUE(title) /* Ensures title of article is unique */
)";


// the user research and events link create table sql
$sql_create_user_red_link = "CREATE TABLE ctr_user_red_link (
	research_id int unsigned,
	email varchar(255)
)";

// the research events and deadlines create table sql
$sql_create_re_deadlines = "CREATE TABLE ctr_re_deadlines (
	re_date date,
	re_id int unsigned auto_increment primary key,
	re_title varchar(255) not null,
	location varchar(255),
	status varchar(255)
)";

// the call for participation create table sql
$sql_create_call_for_part = "CREATE TABLE ctr_call_for_part (
	p_date varchar(255),
	title varchar(255),
	location varchar(255),
	p_id int unsigned auto_increment primary key,
	impact_fact mediumint unsigned,
	description varchar(255),
	origin varchar(255),
	UNIQUE(title) /* Ensures there's no duplicate events */
)";

// call for participation item table creation sql
$sql_create_call_part_item = "CREATE TABLE ctr_call_part_item (
	title varchar(255),
	i_date date,
	link_id int unsigned,
	i_id int unsigned auto_increment primary key
)";

// call for participation item table creation sql
$sql_create_user_call_link = "CREATE TABLE ctr_user_call_link (
	email varchar(255),
	p_id int
)";

// user's favorite article
$sql_create_user_fav = "CREATE TABLE ctr_user_fav (
	email varchar(255),
	a_id int
)";

$sql_table_creation_array = array(
	$sql_create_user,
	$sql_create_user_article_link,
	$sql_create_article,
	$sql_create_user_red_link,
	$sql_create_re_deadlines,
	$sql_create_call_for_part,
	$sql_create_call_part_item,
	$sql_create_user_call_link,
	$sql_create_user_fav
);

$i = 1;
foreach ($sql_table_creation_array as $sql){
	if ($mysqli->query($sql) === TRUE) {
		echo "table " .$i. "  create successfully".$br;
	}else{
		echo "error creating table " .$i. ": ". $mysqli->error . $br;
	}
	$i++;
}

$mysqli->close();

?>
</p>
