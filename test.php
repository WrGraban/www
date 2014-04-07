<?php

    //phpinfo();
    //$connection = new Mongo();
    
    //echo $connection;
    
    $connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "users");

    $test = $collection->findOne(array("_id" => "testy"), array("achievements.lifetime" => true));

    $date = $test['achievements']['lifetime'][0]['ts']->sec;

    
    print_r(date(DATE_ISO8601, $date));
    //var_dump($achievementChecker);

    $connection->close();
?>