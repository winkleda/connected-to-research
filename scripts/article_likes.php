<?php
function get_likes($id, $mysqli) {
	$query = "SELECT count(*) AS count
			FROM ctr_user_fav
			WHERE a_id = ?";

	$stmt = $mysqli->stmt_init();
	if(!$stmt->prepare($query)) {
		echo "Prepared failed: " . $stmt->error;
	}

	$stmt->bind_param("s", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$total = $result->fetch_assoc();

	return $total['count'];
}

?>