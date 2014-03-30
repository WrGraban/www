<?php

	function UpdateUserStats($id, $length, $loc_name, $result, $connection)
	{
		$collection = $connection->selectCollection("peeveepee", "users");
    
	    // Holds the values I will be incrementing
	    $valuesToMod = array(
	    	"stats.lifetime_event_length" => $length,
			"stats.lifetime_event_count" => 1,
			"stats.location_stats." . $loc_name . ".loc_total_length" => $length,
			"stats.location_stats." . $loc_name . ".loc_highest_length" => $length,
			"stats.location_stats." . $loc_name . ".loc_event_count" => 1,
			"stats.location_stats." . $loc_name . ".loc_wins" => 0,
			"stats.location_stats." . $loc_name . ".loc_losses" => 0,
			"stats.location_stats." . $loc_name . ".loc_ties" => 0,
		);
	    
	    // Only used if the player gets a new high score
	    $valuesToSet = array();
	    $isLocHighScore = false;
	    $isLifeHighScore = false;
	    
	    // Update the wins/losses/ties accordingly
	    if($result == 'w')
	    {
	        $valuesToMod["stats.lifetime_wins"] = 1;
	        $valuesToMod["stats.location_stats." . $loc_name . ".loc_wins"] = 1;
	    }
	    else if($result == 'l')
	    {
	        $valuesToMod["stats.lifetime_losses"] = 1;
	        $valuesToMod["stats.location_stats." . $loc_name . ".loc_losses"] = 1;
	    }
	    else if($result == 't')
	    {
	        $valuesToMod["stats.lifetime_ties"] = 1;
	        $valuesToMod["stats.location_stats." . $loc_name . ".loc_ties"] = 1;
	    }

	    /////////////////////////
	    // Check Achievements
	    
	    // Did they get a high score for that location/lifetime?
	    $highScores = $collection->findOne(
	        array("_id" => $id),
	        array("stats.lifetime_highest_length" => true,
	              "stats.location_stats." . $loc_name . ".loc_highest_length" => true)
	        );
	    
	    // Check lifetime high score
	    if($highScores['stats']['lifetime_highest_length'] < $length)
	    {
	        $valuesToSet['stats.lifetime_highest_length'] = $length;
	        $isLifeHighScore = true;
	    }
	    
	    // Check loc high score
	    if($highScores['stats']['location_stats'][$loc_name]['loc_highest_length'] < $length)
	    {
	        $valuesToSet["stats.location_stats." . $loc_name . ".loc_highest_length"] = $length;
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
	}

?>