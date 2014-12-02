<?php
	include('./utility/DocumentMaker.php');
    include('./utility/ServerData.php');

	$connection = new MongoClient($ConnectionString);
    $collection = $connection->selectCollection("peeveepee", "locations");

	$arena_name = $_POST['arena_name'];
	$uid = $_POST['uid'];
	$tag = $_POST['tag'];
	$lat = floatval($_POST['lat']);
	$lon = floatval($_POST['lon']);

	$result = $collection->findOne(array("name" => $arena_name));

	if($result == null)
	{
		// Insert the arena
		$collection->insert(GetLocationDoc($uid, $tag, $arena_name, $lat, $lon));

		echo "<r><res>S</res></r>";
	}
	else
	{
		echo "<r><res>F</res><msg>err_arenaExists</msg></r>";
	}

	$connection->close();
?>