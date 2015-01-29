<?php
ini_set('display_errors', 'On');
include 'connection.php';

/* Resets auto-increment and creates accounts for 3 Scientists */
$sql = "INSERT INTO ctr_user(name_f, name_l, email, password, user_img_src, user_occ)
		VALUES
		('Scientist', 'Example 1', 'user1', '123', './img/profile/Scientist1.jpg', 'Toxicology'),
		('Scientist', 'Example 2', 'user2', '123', './img/profile/Scientist2.jpg', 'Catalyst'),
		('Scientist', 'Example 3', 'user3', '123', './img/profile/Scientist3.jpg', 'Social Media Analytics')";
$result = $mysqli->query($sql);
if(!$result) {
	echo "Query failed: " . mysql_error();
} else {
	echo "Accounts for scientists has been created.<br>";
}

/* Creates 3 articles for each user to fetch */

?>