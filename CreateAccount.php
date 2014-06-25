<?php
    include('./utility/DocumentMaker.php');

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
        // Add the user
        $collection->insert(GetUserDoc($newID, $tag, $email, $hashedPass));

        // Add the stats for the specific user
        $collection = $connection->selectCollection('peeveepee', 'user_stats');
        $collection->insert(GetStatsDoc($newID));
        
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