<?php
    include('DocumentMaker.php');

	$connection = new MongoClient();

    // TODO: Check for reserved names and let the user know
    
    $collection = $connection->selectCollection("peeveepee", "users");
    
    ////////
    // Create anonymous user
    ////////
    $tag = "Anonymous";
    $newID = "anonymous";
    $hashedPass = "367F5C8B68E8A1290B0EF501BA462A10B29E8D3EDA72A23701F0B0F7417751D0840E7DE7AC63F28890B6C5D936684D8E0746B0475272447BFC396FFBD1F0D56F";
    $email = "anon_has_no_email@4chan.org";

    $doc = $collection->findOne(array("_id" => $newID));
    
    if($doc == null)
    {
        $collection->insert(GetUserDoc($newID, $tag, $email, $hashedPass));

        // Insert his stats
        $collection = $connection->selectCollection('peeveepee', 'stats');
        $collection->insert(GetStatsDoc($newID));
    }
    
    ////////
    // Create "The Void"
    ////////
    $collection = $connection->selectCollection("peeveepee", "locations");

    $arena_name = "The Void";
	$uid = "zesty";
	$lat = 23.806;
	$lon = 11.288;

	$collection->insert(GetLocationDoc($uid, $arena_name, $lat, $lon));

	////////
    // Create "Narnia"
    ////////
    $arena_name = "Narnia";
    $lat = 11.35;
    $lon = 142.2;

	$collection->insert(GetLocationDoc($uid, $arena_name, $lat, $lon));
    
    $connection->close();

?>