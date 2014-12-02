<?php
	include('DocumentMaker.php');
	include('ServerLocation.php');
	
	$connection = new MongoClient(GetServerAddress());
    $collection = $connection->selectCollection("peeveepee", "locations");

/*
	$arena_name = $_POST['arena_name'];
	$uid = $_POST['uid'];
	$lat = floatval($_POST['lat']);
	$lon = floatval($_POST['lon']);
*/
	$arena_name = "The Void";
	$uid = "zesty";
	$lat = 23.806;
	$lon = 11.288;

	$result = $collection->findOne(array("name" => $arena_name));

	if($result == null)
	{
		$collection->insert(GetLocationDoc($uid, $arena_name, $lat, $lon));
		echo "<r><res>S</res></r>";
	}
	else
	{
		echo "<r><res>F</res><msg>err_arenaExists</msg></root>";
	}

	$connection->close();
?>