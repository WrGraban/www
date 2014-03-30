<?php

    $connection = new MongoClient();
    
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
    
    $doc = $collection->findOne(array("_id" => $id), array("pass" => true, "account_type" => true));
    
    if($doc == null)
    {
        BuildResponseXml("F", "err_noId", null);
    }
    else if($doc['pass'] == $pass)
    {
        BuildResponseXml("S", null, $doc['account_type']);
    }
    else
    {
        BuildResponseXml("F", "err_badPass", null);
    }
    
    $connection->close();
    
    function BuildResponseXml($result, $message, $accountType)
    {
        echo "<r>";
        echo "<res>" . $result . "</res>";
        
        if($result == "F")
        {
            echo "<msg>" . $message . "</msg>";
        }
        else
        {
            // at -> Account Type
            echo "<at>" . $accountType . "</at>";
        }
        
        echo "</r>";
    }
?>