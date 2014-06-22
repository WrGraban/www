<?php
    $connection = new MongoClient();
    
    if($connection == null)
    {
        BuildResponseXml("Failure", "Could not connect to Mongo.");
        exit(1);
    }

    // TODO: Check for reserved names and let the user know
    
    $collection = $connection->selectCollection("peeveepee", "users");
    /*
    $tag = $_POST['tag'];
    $newID = str_replace(' ', '', strtolower($tag));
    $hashedPass = $_POST['pass'];
    $email = $_POST['email'];
    */
    $tag = "Anonymous";
    $newID = "anonymous";
    $hashedPass = "367F5C8B68E8A1290B0EF501BA462A10B29E8D3EDA72A23701F0B0F7417751D0840E7DE7AC63F28890B6C5D936684D8E0746B0475272447BFC396FFBD1F0D56F";
    $email = "anon_has_no_email@4chan.org";
    
    $doc = $collection->findOne(array("_id" => $newID));
    
    if($doc == null)
    {
        // The tag is free!
        $newDoc = array(
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
            )
        );
        
        $collection->insert($newDoc);
        
        BuildResponseXml("Success", null);
    }
    else
    {
        BuildResponseXml("Tag Taken", null);
    }
    
    $connection->close();
    
    function BuildResponseXml($result, $message)
    {
        echo "<root>";
        echo "<result>" . $result . "</result>";
        
        if($result == "Failure")
        {
            echo "<message>" . $message . "</message>";
        }
        
        echo "</root>";
    }
?>