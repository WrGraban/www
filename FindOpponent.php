<?php
	$connection = new MongoClient();

	// Find the id of the location so we can index into the events
	$loc_name = $_POST['name'];
	$user_id = $_POST['uid'];
    $isanon = $_POST['anon'];

    //$loc_name = $_GET['name'];
    //$user_id = $_GET['uid'];

	$collection = $connection->selectCollection("peeveepee", "events." . $loc_name);

    $curson = null;

	// Next we'll have to get the opponent data
    // We must find a different player if the match is not anonymous
    if($isanon == "0")
    {
        $cursor = $collection->find(
            // query
            array("user_id" => array('$ne' => $user_id)),
            
            // fields to return
            array(
                "user_id" => true,
                "_id" => false,
                "timestamp" => true,
                "length" => true,
                "tag" => true
            )
        )->sort(array('timestamp' => -1)
        )->limit(1);
    }
    else
    {
        $cursor = $collection->find(
            // query (any)
            array(),
            
            // fields to return
            array(
                "user_id" => true,
                "_id" => false,
                "timestamp" => true,
                "length" => true,
                "tag" => true
            )
        )->sort(array('timestamp' => -1)
        )->limit(1);
    }

    $opponentEventDoc = $cursor->getNext();
    
    // Check the returned object
    if($opponentEventDoc != null)
    {
        // Build the response using the opponent data
        BuildResponseXml("S", null, $opponentEventDoc); // Success
    }
    else
    {
        BuildResponseXml("F", "msg_noOpp", null);
    }

    // Finally close the connection
    $connection->close();

    function BuildResponseXml($result, $message, $opponentEventDoc)
    {
        echo "<r>";
        echo "<res>" . $result . "</res>";
        
        if($result == "F") // Failure
        {
            echo "<msg>" . $message . "</msg>";
        }
        else
        {
            echo "<eId>" . $opponentEventDoc['user_id'] . "</eId>";
            echo "<eLen>" . $opponentEventDoc['length'] . "</eLen>";
            echo "<eTag>" . $opponentEventDoc['tag'] . "</eTag>";
            
            // TODO: Perhaps return the timestamp to display how long
            // ago the enemy created their event.
        }
        
        echo "</r>";
    }
?>