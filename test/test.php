<?php
    include('./Stats/GetLifetimeLocationStat.php');
    include('./utility/DocumentMaker.php');
    include('./utility/ServerData.php');
    
    $connection = new MongoClient($ConnectionString);

    $collection = $connection->selectCollection('peeveepee', 'location_unique_gladiators');

    $result = $collection->findOne(array('user_id' => 'zesty', 'loc_id' => 'Narnia'), array('_id' => true));

    var_dump($result);
?>