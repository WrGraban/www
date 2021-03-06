<?php
    
    include('Stats/AchievementChecker.php');
    include('./utility/DocumentMaker.php');
	include('./utility/ServerData.php');

    $connection = new MongoClient($ConnectionString);

    ////////////////
    // POST data
    ////////////////
    $id = $_POST['id'];
    $tag = $_POST['tag'];
    $length = floatval($_POST['len']);
    $result = $_POST['res'];
    $loc_name = $_POST['lname'];
    $op_id = $_POST['opid'];
    $isAnonBattle = $_POST['isanon'];

    // Used by Achievement Checker to build the response xml
    $responseXml = "";

    // For some reason Unity was not allowing me to submit a value of null to the opid field
    // I had to HACK around it and send the string "null" which I will check for and convert
    // to the actual value.  As an added note, "null" should be on the list of restricted names
    $hasOpponent = true;

    if($op_id == "null")
        $hasOpponent = false;


    ////////////////
    // Insert Event
    ////////////////

    // Set the collection based on the loc_name
    $collection = $connection->selectCollection("peeveepee", "events");
    
    // Insert the document
    $collection->insert(GetEventDoc($id, $loc_name, $length, $tag));

    $collection = $connection->selectCollection("peeveepee", "location_stats");

    // Test to see if it's the first time they are using this location
    //$locExists = $collection->findOne(array("_id" => $id, "stats.location_stats.name" => $loc_name), array("_id" => true));
    // Test to see if it's the first time they are using this location
    $locExists = $collection->findOne(
        // query
        array(
            'owner_id' => $id,
            'loc_name' => $loc_name
        ),

        // fields to return
        array('_id' => true)
    );

    if($locExists == null)
        $collection->insert(GetLocationStatsDoc($id, $loc_name));

    ////////////////
    // User Stats
    ////////////////

    // Holds the values I will be incrementing
    $userStatsToMod = array(
        "lifetime_event_length" => $length,
        "lifetime_event_count" => 1
    );

    // I think I need a different array for the special location_stats update
    $locStatsToMod = array(
        'loc_total_length' => $length,
        'loc_event_count' => 1
    );

    // Update the wins/losses/ties accordingly
    if($result == 'w')
    {
        $userStatsToMod["lifetime_wins"] = 1;
        $locStatsToMod['loc_wins'] = 1;
    }
    else if($result == 'l')
    {
        $userStatsToMod["lifetime_losses"] = 1;
        $locStatsToMod['loc_losses'] = 1;
    }
    else if($result == 't')
    {
        $userStatsToMod["lifetime_ties"] = 1;
        $locStatsToMod['loc_ties'] = 1;
    }

    // Update user stats
    $collection = $connection->selectCollection('peeveepee', 'user_stats');
    $collection->update(array('_id' => $id), array('$inc' => $userStatsToMod));

    // Update the location stats associated with the user
    $collection = $connection->selectCollection('peeveepee', 'location_stats');
    $collection->update(array('owner_id' => $id, 'loc_name' => $loc_name), array('$inc' => $locStatsToMod));

    if($hasOpponent == true)
    {
        // Select the opponent count collection
        $collection = $connection->selectCollection('peeveepee', 'opponent_count');

        // Update the opponents array
        $opponentFound = $collection->findOne(array('owner_id' => $id, 'opponent_id' => $op_id), array('_id' => true));

        // Check for the special cases of 'new opponent'
        if($opponentFound == null)
            $collection->insert(GetOpponentCountDoc($id, $op_id));
        else
            $collection->update(array('owner_id' => $id, 'opponent_id' => $op_id), array('$inc' => array('count' => 1)));
    }

    ////////////////
    // Location Stats
    ////////////////
    $collection = $connection->selectCollection("peeveepee", "locations");

    // Increment the event count and the total length
    $locValuesToMod = array(
        "total_event_count" => 1,
        "total_length" => $length
    );

    // Update the entry
    $collection->update(array("name" => $loc_name), array('$inc' => $locValuesToMod));

    $collection = $connection->selectCollection('peeveepee', 'location_unique_gladiators');

    $result = $collection->findOne(array('user_id' => $id, 'loc_id' => $loc_name), array('_id' => true));

    if($result == null)
    {
        // This is the first time this  has battled in this arena
        $collection->insert(GetUniqueGladiatorsDoc($loc_name, $id));

        // Change the collection!  (Should I have multiple variables, one for each collection?  Speed v. Memory)
        $collection = $connection->selectCollection('peeveepee', 'locations');

        // Update the unique gladiator count
        $collection->update(array('name' => $loc_name), array('$inc' => array('unique_gladiator_count' => 1)));
    }

    /////////////////////////
    // Check Achievements
    if($isAnonBattle == "0")
        CheckAchievements($connection, $id, $length, $loc_name, $op_id);
    else
        echo "<r></r>"; // No need to return an achievement array!  They're anonymous.

    // Finally close the connection
    $connection->close();
?>