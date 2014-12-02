<?php

    $connection = new MongoClient();
    
    if($connection == null)
    {
        BuildResponseXml("Failure", "Could not connect to Mongo.");
        exit(1);
    }
    
    // Moved to the top so i can send debug info back through xml
    echo "<root>";
    
    $collection = $connection->selectCollection("peeveepee", "events.Narnia");
    
    // Get data from post
    $id = $_POST['id'];
    $tag = $_POST['tag'];
    $length = floatval($_POST['length']);
    $result = $_POST['result'];
    
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
    
    // Update the stats
    $collection = $connection->selectCollection("peeveepee", "users");
    
    // Holds the values I will be incrementing
    $valuesToMod = array("stats.lifetime_event_length" => $length,
                  "stats.lifetime_event_count" => 1,
                  "stats.location_stats.Narnia.loc_total_length" => $length,
                  "stats.location_stats.Narnia.loc_event_count" => 1);
    
    // Only used if the player gets a new high score
    $valuesToSet = array();
    $isLocHighScore = false;
    $isLifeHighScore = false;
    
    // Update the wins/losses/ties accordingly
    if($result == 'w')
    {
        $valuesToMod['stats.lifetime_wins'] = 1;
        $valuesToMod['stats.location_stats.Narnia.loc_wins'] = 1;
    }
    else if($result == 'l')
    {
        $valuesToMod['stats.lifetime_losses'] = 1;
        $valuesToMod['stats.location_stats.Narnia.loc_losses'] = 1;
    }
    else
    {
        $valuesToMod['stats.lifetime_ties'] = 1;
        $valuesToMod['stats.location_stats.Narnia.loc_ties'] = 1;
    }
    
    // Did they get a high score for that location/lifetime?
    $highScores = $collection->findOne(
        array("_id" => $id),
        array("stats.lifetime_highest_length" => true,
              "stats.location_stats.Narnia.loc_highest_length" => true)
        );
    
    // Check lifetime high score
    if($highScores['stats']['lifetime_highest_length'] < $length)
    {
        $valuesToSet['stats.lifetime_highest_length'] = $length;
        $isLifeHighScore = true;
    }
    
    // Check loc high score

    if($highScores['stats']['location_stats']['Narnia']['loc_highest_length'] < $length)
    {
        $valuesToSet['stats.location_stats.Narnia.loc_highest_length'] = $length;
        $isLocHighScore = true;
    }
    
    //
    if($isLocHighScore == true || $isLifeHighScore == true)
    {
        $retval = $collection->update(
            array("_id" => $id),
            array('$inc' => $valuesToMod)
        );
        
        $retval = $collection->update(array("_id" => $id), array('$set' => $valuesToSet));
    }
    else
    {
        $retval = $collection->update(
            array("_id" => $id),
            array('$inc' => $valuesToMod)
        );
    }
    
    BuildXmlResponse($isLifeHighScore, $isLocHighScore);

    // Finally close the connection
    $connection->close();
    
    function BuildXmlResponse($lifeHighScore, $locHighScore)
    {
        
        echo "<achievements>";
        
        if($lifeHighScore != false)
        {
            echo "<achievement>Life High Score</achievement>";
        }
        
        if($locHighScore != false)
        {
            echo "<achievement>Location High Score</achievement>";
        }
        
        echo "</achievements>";
        echo "</root>";
    }
?>