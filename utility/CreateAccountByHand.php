<?php
    include('DocumentMaker.php');

    $connection = new MongoClient();
    
    if($connection == null)
    {
        BuildResponseXml("Failure", "Could not connect to Mongo.");
        exit(1);
    }

    // TODO: Check for reserved names and let the user know
    
    $collection = $connection->selectCollection("peeveepee", "users");

    $tag = "Anonymous";
    $newID = "anonymous";
    $hashedPass = "367F5C8B68E8A1290B0EF501BA462A10B29E8D3EDA72A23701F0B0F7417751D0840E7DE7AC63F28890B6C5D936684D8E0746B0475272447BFC396FFBD1F0D56F";
    $email = "anon_has_no_email@4chan.org";
    
    $doc = $collection->findOne(array("_id" => $newID));
    
    if($doc == null)
    {
        $collection->insert(GetUserDoc($newID, $tag, $email, $hashedPass));

        $collection = $connection->selectCollection('peeveepee', 'user_stats');
        $collection->insert(GetStatsDoc($newID));
        
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