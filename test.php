<?php
    include('./Stats/GetLifetimeLocationStat.php');
    include('./utility/DocumentMaker.php');
    //phpinfo();
    //echo $connection;
    
    $connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "locations");

    $doc = $collection->findOne(array('name' => 'Narnia'), array('_id' => false, 'date_created' => true));
    $date = $doc['date_created'];
    $now = new MongoDate();
    echo $now->sec;
    echo '<br/>' . $doc['date_created']->sec;
    $diff = $now->sec - $doc['date_created']->sec;

    var_dump($diff);

    $connection->close();
    

    //echo rand(0, 1);
/*
    $arr = 1;
    test($arr);

    function test($param)
    {
        while(count($param) != 0)
        {
            $item = array_pop($param);
            echo $item;
        }
    }*/

?>