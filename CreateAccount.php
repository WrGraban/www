<?php
    $connection = new MongoClient();
    
    if($connection == null)
    {
        BuildResponseXml("F", "err_noMongo");
        exit(1);
    }

    // TODO: Check for reserved names and let the user know it cannot be taken
    // null
    // anonymous
    
    $collection = $connection->selectCollection("peeveepee", "users");
    $tag = $_POST['tag'];
    $newID = str_replace(' ', '', strtolower($tag));
    $hashedPass = $_POST['pass'];
    $email = $_POST['email'];
    
    $doc = $collection->findOne(array("_id" => $newID));
    
    if($doc == null)
    {
        // The tag is free!
        $newDoc = array(
            "_id" => $newID,
            "tag" => $tag,
            "account_type" => "Free", // HACK
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
        
        $collection->insert($newDoc);
        
        BuildResponseXml("S", null);
    }
    else
    {
        BuildResponseXml("F", "err_tagTaken");
    }
    
    $connection->close();
    
    function BuildResponseXml($result, $message)
    {
        echo "<r>";
        echo "<res>" . $result . "</res>";
        
        if($result == "F")
        {
            echo "<msg>" . $message . "</msg>";
        }
        
        echo "</r>";
    }
?>