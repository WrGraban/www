<?php

	$connection = new MongoClient();
    
    if($connection == null)
    {
        BuildResponseXml("Failure", "Could not connect to Mongo.");
        exit(1);
    }

    // TODO: Check for reserved names and let the user know
    
    $collection = $connection->selectCollection("peeveepee", "users");
    
    ////////
    // Create anonymous user
    ////////
    $tag = "Anonymous";
    $newID = "anonymous";
    $hashedPass = "367F5C8B68E8A1290B0EF501BA462A10B29E8D3EDA72A23701F0B0F7417751D0840E7DE7AC63F28890B6C5D936684D8E0746B0475272447BFC396FFBD1F0D56F";
    $email = "anon_has_no_email@4chan.org";

        // The tag is free!
    $anonUser = array(
        "_id" => $newID,
        "tag" => $tag,
        "account_type" => "Group", // HACK
        "date_created" => date("Y-m-d"),
        "achievements" => array(
            "lifetime" => array(),
            "locations" => array()
        ),
        "email" => $email,
        "pass" => $hashedPass,
        "stats" => array(
            "lifetime_event_count" => 0,
            "lifetime_event_length" => 0,
            "lifetime_highest_length" => 0,
            "lifetime_wins" => 0,
            "lifetime_losses" => 0,
            "lifetime_ties" => 0,
            "location_stats" => array(),
            "opponents" => array()
        ),
        "last_stat" => ""
    );
    
    $collection->insert($anonUser);
    
    ////////
    // Create "The Void"
    ////////
    $collection = $connection->selectCollection("peeveepee", "locations");

    $arena_name = "The Void";
	$uid = "zesty";
	$lat = 23.806;
	$lon = 11.288;

    $theVoidArena = array(
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

	$collection->insert($theVoidArena);

	////////
    // Create "Narnia"
    ////////
    $arena_name = "Narnia";
    $lat = 11.35;
    $lon = 142.2;

    $narniaArena = array(
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

	$collection->insert($narniaArena);
    
    $connection->close();

?>