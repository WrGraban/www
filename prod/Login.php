<?php
    include('./utility/ServerData.php');

    $connection = new MongoClient($ConnectionString);
    
    if($connection == null)
    {
        BuildResponseXml("F", "err_noMongo", null);
        exit(1);
    }
    
    // Select collection
    $collection = $connection->selectCollection("peeveepee", "users");
    
    // Grab data from post
    $id = $_POST['id'];
    $pass = $_POST['pass'];
    
    $doc = $collection->findOne(array("_id" => $id), array("pass" => true, "account_type" => true, "tag" => true));
    
    if($doc == null)
    {
        BuildResponseXml("F", "err_noId", null, null);
    }
    else if($doc['pass'] == $pass)
    {
        BuildResponseXml("S", null, $doc['account_type'], $doc['tag']);
    }
    else
    {
        BuildResponseXml("F", "err_badPass", null, null);
    }
    
    $connection->close();
    
    function BuildResponseXml($result, $message, $accountType, $tag)
    {
        echo "<r>";
        echo "<res>" . $result . "</res>";
        
        if($result == "F")
        {
            echo "<msg>" . $message . "</msg>";
        }
        else if($result == "S")
        {
            // at -> Account Type
            echo "<at>" . $accountType . "</at>";
            echo "<tag>" . $tag . "</tag>";
        }
        
        echo "</r>";
    }
?>