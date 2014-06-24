<?php
	include('./utility/DocumentMaker.php');

	$connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "locations");

	$arena_name = $_POST['arena_name'];
	$uid = $_POST['uid'];
	$lat = floatval($_POST['lat']);
	$lon = floatval($_POST['lon']);

	$result = $collection->findOne(array("name" => $arena_name));

	if($result == null)
	{
		/*
		// Insert into locations
		$doc = array(
			"name" => $arena_name,
			"date_created" => date("Y-m-d"),
			"created_by" => $uid,
			"total_event_count" => 0,
			"total_length" => 0,
			"unique_gladiators" => array(),
			"unique_gladiator_count" => 0,
			"loc" => array(
				"lat" => $lat,
				"lon" => $lon
			)
		);
		*/

		// Insert the arena
		$collection->insert(GetLocationDoc($uid, $arena_name, $lat, $lon));

		echo "<r><res>S</res></r>";
	}
	else
	{
		echo "<r><res>F</res><msg>err_arenaExists</msg></r>";
	}

	$connection->close();
?>