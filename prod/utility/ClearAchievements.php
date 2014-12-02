<?php
	include('ServerLocation.php');

	// NOTE: This should NOT exist on the actual server.  Used only for a test environment.
	$connection = new MongoClient(GetServerAddress());

	// Manually change this for each player who needs achievements wiped
	$targetId = "zesty";

	$collection = $connection->selectCollection("peeveepee", "users");

	$emptyAchievements = array('lifetime' => array(), 'locations' => array());

	$collection->update(array("_id" => $targetId), array('$set' => array("achievements" => $emptyAchievements)));

	$connection->close();
?>