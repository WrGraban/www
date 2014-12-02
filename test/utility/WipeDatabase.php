<?php
	include('DocumentMaker.php');
    include('ServerData.php');

    if(array_key_exists('pass', $_GET) && $_GET['pass'] == 'kaboom')
    {
    	$connection = new MongoClient($ConnectionString);

	    $colsToRemove = array(
	    	'users',
	    	'user_stats',
	    	'locations',
	    	'location_stats',
	    	'opponent_count',
	    	'location_unique_gladiators',
	    	'events',
	    	'achievements'
	    );

	    foreach($colsToRemove as $col)
	    {
	    	$collection = $connection->selectCollection("peeveepee", $col);
	    	$collection->remove(array());
	    }

	    echo 'database destroyed';

	    SetupFreshDB($connection);
    }
    else
    {
    	echo 'You do not have permission to perform such a destructive action';
    }

    function SetupFreshDB(&$connection)
    {
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
	        $collection = $connection->selectCollection('peeveepee', 'user_stats');
	        $collection->insert(GetStatsDoc($newID));
	    }
	    
	    ////////
	    // Create "The Void"
	    ////////
	    $collection = $connection->selectCollection("peeveepee", "locations");

	    $arena_name = "The Void";
		$uid = "zesty";
	    $tag = "Zesty";
		$lat = 23.806;
		$lon = 11.288;

		$collection->insert(GetLocationDoc($uid, $tag, $arena_name, $lat, $lon));

		////////
	    // Create "Narnia"
	    ////////
	    $arena_name = "Narnia";
	    $lat = 11.35;
	    $lon = 142.2;

		$collection->insert(GetLocationDoc($uid, $tag, $arena_name, $lat, $lon));

	    echo 'Complete';
	    
	    $connection->close();
    }
?>