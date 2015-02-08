<?php
ini_set('display_errors', 'On');
include 'connection.php';

/* test data to add to the data base */
$RED_info = [
	array(
		"re_date" => "2015-02-14",
		"re_id"		=> 1,
		"re_title"		=> "toxicology lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-02-09",
		"re_id"		=> 2,
		"re_title"		=> "fungi lecture",
		"location" => "room 104",
		"status" => "active"
	),
	array(
		"re_date" => "2015-02-29",
		"re_id"		=> 3,
		"re_title"		=> "social lecture",
		"location" => "room 109",
		"status" => "active"
	),
	array(
		"re_date" => "2015-03-14",
		"re_id"		=> 4,
		"re_title"		=> "desert lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-04-14",
		"re_id"		=> 5,
		"re_title"		=> "atomic lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-04-19",
		"re_id"		=> 6,
		"re_title"		=> "toxicology lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-03-19",
		"re_id"		=> 7,
		"re_title"		=> "survey lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-02-21",
		"re_id"		=> 8,
		"re_title"		=> "state lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-04-02",
		"re_id"		=> 9,
		"re_title"		=> "toxicology lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-03-26",
		"re_id"		=> 10,
		"re_title"		=> "another lecture",
		"location" => "room 101",
		"status" => "active"
	),
	array(
		"re_date" => "2015-03-03",
		"re_id"		=> 11,
		"re_title"		=> "Physics lecture",
		"location" => "WNGR 1041",
		"status" => "active"
	)
	
];

$RED_links = [
	array(
		"research_id" => 1,
		"email" => "user1",
		"id" => 1
	),
	array(
		"research_id" => 2,
		"email" => "user1",
		"id" => 2
	),
	array(
		"research_id" => 3,
		"email" => "user1",
		"id" => 3
	),
	array(
		"research_id" => 4,
		"email" => "user1",
		"id" => 4
	),
	array(
		"research_id" => 5,
		"email" => "user1",
		"id" => 5
	),
	array(
		"research_id" => 6,
		"email" => "user1",
		"id" => 6
	),
	array(
		"research_id" => 7,
		"email" => "user1",
		"id" => 7
	),
	array(
		"research_id" => 8,
		"email" => "user1",
		"id" => 8
	),
	array(
		"research_id" => 9,
		"email" => "user1",
		"id" => 9
	),
	array(
		"research_id" => 10,
		"email" => "user1",
		"id" => 10
	),
	array(
		"research_id" => 11,
		"email" => "user1",
		"id" => 11
	),
	array(
		"research_id" => 1,
		"email" => "user2",
		"id" => 12
	),
	array(
		"research_id" => 2,
		"email" => "user2",
		"id" => 13
	),
	array(
		"research_id" => 3,
		"email" => "user2",
		"id" => 14
	),
	array(
		"research_id" => 4,
		"email" => "user2",
		"id" => 15
	),
	array(
		"research_id" => 5,
		"email" => "user2",
		"id" => 16
	),
	array(
		"research_id" => 6,
		"email" => "user2",
		"id" => 17
	),
	array(
		"research_id" => 7,
		"email" => "user2",
		"id" => 18
	),
	array(
		"research_id" => 8,
		"email" => "user2",
		"id" => 19
	),
	array(
		"research_id" => 9,
		"email" => "user2",
		"id" => 20
	),
	array(
		"research_id" => 3,
		"email" => "user2",
		"id" => 21
	),
	array(
		"research_id" => 4,
		"email" => "user2",
		"id" => 22
	),
	array(
		"research_id" => 5,
		"email" => "user3",
		"id" => 23
	),
	array(
		"research_id" => 6,
		"email" => "user3",
		"id" => 24
	),
	array(
		"research_id" => 7,
		"email" => "user3",
		"id" => 25
	),
	array(
		"research_id" => 8,
		"email" => "user3",
		"id" => 26
	),
	array(
		"research_id" => 9,
		"email" => "user3",
		"id" => 27
	),
	array(
		"research_id" => 10,
		"email" => "user3",
		"id" => 28
	),
	array(
		"research_id" => 11,
		"email" => "user3",
		"id" => 29
	)
];

foreach($RED_info as $info){

	if (!($stmt = $mysqli->prepare(
		"INSERT INTO ctr_re_deadlines(
			re_date,
			re_id,
			re_title,
			location,
			status) 
		VALUES (?, ?, ?, ?, ?)"))) {
		
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("sssss", 
		$info["re_date"],
		$info["re_id"],
		$info["re_title"],
		$info["location"],
		$info["status"])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Failed to insert call " . $info["re_id"] . ": " . $stmt->error . "<br>";
	} else {
		echo "call " . $info["re_id"] . " successfully inserted into database.<br>";
	}


}

foreach($RED_links as $info){

	if (!($stmt = $mysqli->prepare(
		"INSERT INTO ctr_user_red_link(
			research_id,
			email) 
		VALUES (?, ?)"))) {
		
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("is", 
		$info["research_id"],
		$info["email"]
		)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Failed to insert call " . $info["research_id"] . ": " . $stmt->error . "<br>";
	} else {
		echo "call " . $info["research_id"] . " successfully inserted into database.<br>";
	}


}


?>
