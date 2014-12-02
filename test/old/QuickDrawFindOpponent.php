<?php

    $connection = new MongoClient();
    
    if($connection == null)
    {
        BuildResponseXml("Failure", "Could not connect to Mongo.");
        exit(1);
    }
    
    $collection = $connection->selectCollection("peeveepee", "events.Narnia");
    
    // Get data from post
    $id = $_POST['id'];
    
    // Next we'll have to get the opponent data
    $cursor = $collection->find(
        // query
        array("user_id" => array('$ne' => $id)),
        
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
    
    $opponentEventDoc = $cursor->getNext();
    
    // Check the returned object
    if($opponentEventDoc != null)
    {
        // Build the response using the opponent data
        BuildResponseXml("Success", null, $opponentEventDoc);
    }
    else
    {
        BuildResponseXml("Failure", "Could not find opponent", null);
    }

    // Finally close the connection
    $connection->close();
    
    function BuildResponseXml($result, $message, $opponentEventDoc)
    {
        echo "<root>";
        echo "<result>" . $result . "</result>";
        
        if($result == "Failure")
        {
            echo "<message>" . $message . "</message>";
        }
        else
        {
            echo "<enemyId>" . $opponentEventDoc['user_id'] . "</enemyId>";
            echo "<enemyLength>" . $opponentEventDoc['length'] . "</enemyLength>";
            echo "<enemyTag>" . $opponentEventDoc['tag'] . "</enemyTag>";
            
            // TODO: Perhaps return the timestamp to display how long
            // ago the enemy created their event.
        }
        
        echo "</root>";
    }

?>