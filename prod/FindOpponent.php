<?php
    include('./utility/DocumentMaker.php');
    include('./utility/ServerData.php');
    
	$connection = new MongoClient($ConnectionString);

	// Find the id of the location so we can index into the events
	$loc_name = $_POST['name'];
	$user_id = $_POST['uid'];
    $isanon = $_POST['anon'];

    //$loc_name = $_GET['name'];
    //$user_id = $_GET['uid'];

    // First things first, check to see if the location_stats document exists for this user
    $collection = $connection->selectCollection('peeveepee', 'location_stats');
    $doc = $collection->findOne(array('owner_id' => $user_id, 'loc_name' => $loc_name), array('_id' => true));

    if($doc == null)
    {
        $collection->insert(GetLocationStatsDoc($user_id, $loc_name));
    } 

	$collection = $connection->selectCollection("peeveepee", "events");

    $cursor = null;

	// Next we'll have to get the opponent data
    // We must find a different player if the match is not anonymous
    if($isanon == "0")
    {
        $cursor = $collection->find(
            // query
            array(
                "user_id" => array('$ne' => $user_id),
                "loc_name" => $loc_name
            ),
            
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