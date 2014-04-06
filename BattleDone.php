<?php
    
    include('Stats/AchievementChecker.php');

	$connection = new MongoClient();

    

    // USED FOR TESTING
    ///////////////////
    //$id = $_GET['id'];
    //$tag = $_GET['tag'];
    //$length = floatval($_GET['length']);
    //$result = $_GET['result'];
    //$loc_name = $_GET['loc_name'];

    ////////////////
    // POST data
    ////////////////
    $id = $_POST['id'];
    $tag = $_POST['tag'];
    $length = floatval($_POST['length']);
    $result = $_POST['result'];
    $loc_name = $_POST['loc_name'];
    $op_id = $_POST['opid'];
    $isAnonBattle = $_POST['isanon'];

    // Used by Achievement Checker to build the response xml
    $responseXml = "";

    // For some reason Unity was not allowing me to submit a value of null to the opid field
    // I had to HACK around it and send the string "null" which I will check for and convert
    // to the actual value.  As an added note, "null" should be on the list of restricted names
    $hasOpponent = true;

    if($op_id == "null")
    {
        $hasOpponent = false;
    }


    ////////////////
    // Insert Event
    ////////////////

    // Set the collection based on the loc_name
    $collection = $connection->selectCollection("peeveepee", "events." . $loc_name);

    // Create the new document
    $newDoc = array(
        "user_id" => $id,
        "timestamp" => new MongoDate(),
        // We don't need the loc_id because the loc_id is used in the subcollection
        //"loc_id" => $locId,
        "length" => $length,
        "tag" => $tag
    );
    
    // Insert the document
    $collection->insert($newDoc);

    ////////////////
    // User Stats
    ////////////////
    $collection = $connection->selectCollection("peeveepee", "users");

    // Test to see if it's the first time they are using this location
    $locExists = $collection->findOne(array("_id" => $id, "stats.location_stats.name" => $loc_name), array("_id" => true));

    if($locExists == null)
    {
        // We have to create the empty data set and insert it!
        // TODO: Use upsert?
        $defaultLocStats = array(
            "name" => $loc_name,
            "loc_event_count" => 0,
            "loc_highest_length" => 0,
            "loc_losses" => 0,
            "loc_wins" => 0,
            "loc_ties" => 0,
            "loc_total_length" => 0
        );

        $collection->update(array("_id" => $id), array('$push' => array("stats.location_stats" => $defaultLocStats)));
    }

    // I need to 
    
    // Holds the values I will be incrementing
    $userStatsToMod = array(
        "stats.lifetime_event_length" => $length,
        "stats.lifetime_event_count" => 1,
        
    );

    // I think I need a different array for the special location_stats update
    $locStatsToMod = array(
        'stats.location_stats.$.loc_total_length' => $length,
        'stats.location_stats.$.loc_event_count' => 1,
    );
    
    // Update the wins/losses/ties accordingly
    if($result == 'w')
    {
        $userStatsToMod["stats.lifetime_wins"] = 1;
        $locStatsToMod['stats.location_stats.$.loc_wins'] = 1;
    }
    else if($result == 'l')
    {
        $userStatsToMod["stats.lifetime_losses"] = 1;
        $locStatsToMod['stats.location_stats.$.loc_losses'] = 1;
    }
    else if($result == 't')
    {
        $userStatsToMod["stats.lifetime_ties"] = 1;
        $locStatsToMod['stats.location_stats.$.loc_ties'] = 1;
    }

    // Double update because of array ?wildcard? work.  TODO: Find a way to do this in a single update?
    $collection->update(array("_id" => $id), array('$inc' => $userStatsToMod));
    $collection->update(array("_id" => $id, "stats.location_stats.name" => $loc_name), array('$inc' => $locStatsToMod));

    if($hasOpponent == true)
    {
        // Update the opponents array
        $opponentFound = $collection->findOne(array("_id" => $id, "stats.opponents.name" => $op_id), array("_id" => true));

        // Check for the special cases of 'new opponent'
        if($opponentFound == null)
        {
            $collection->update(array("_id" => $id), array('$push' => array("stats.opponents" => array("name" => $op_id, "count" => 1))));
        }
        else
        {
            $collection->update(array("_id" => $id, "stats.opponents.name" => $op_id), array('$inc' => array('stats.opponents.$.count' => 1)));
        }
    }

    ////////////////
    // Location Stats
    ////////////////
    $collection = $connection->selectCollection("peeveepee", "locations");

    $locDoc = $collection->findOne(array(
        "name" => $loc_name
    ));

    //echo $locDoc;

    $locValuesToMod = array(
        "total_event_count" => 1,
        "total_length" => $length
    );

    // Update the total event count
    $collection->update(array("name" => $loc_name), array('$inc' => $locValuesToMod));

    $found = false;

    // Loop through the unique_gladiators array to see if this one is new
    foreach($locDoc['unique_gladiators'] as $name)
    {
        if($name == $id)
        {
            $found = true;
        }
    }

    if($found == false)
    {
        // Push the name into the array
        $collection->update(array("name" => $loc_name), array('$addToSet' => array("unique_gladiators" => $id)));
        // Increment ugc
        $collection->update(array("name" => $loc_name), array('$inc' => array("unique_gladiator_count" => 1)));
    }

    /////////////////////////
    // Check Achievements
    if($isAnonBattle == "0")
    {
        CheckAchievements($connection, $id, $length, $loc_name, $op_id);
    }
    else
    {
        echo "<r></r>"; // No need to return an achievement array!  They're anonymous.
    }

    // Finally close the connection
    $connection->close();
?>