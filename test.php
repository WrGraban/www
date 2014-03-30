<?php

    //phpinfo();
    //$connection = new Mongo();
    
    //echo $connection;
    
    $connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "users");

    $test = $collection->findOne(array("_id" => "testy", "achievements.locations.loc" => "Narnia", "achievements.locations.name" => "Location Longest"), array('achievements.locations.$' => true));

    print_r($test);
    //var_dump($achievementChecker);

    $connection->close();
?>