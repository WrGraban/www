<?php
    include('./Stats/GetLifetimeLocationStat.php');
    //phpinfo();
    //echo $connection;
    
    $connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "users");

    $statArray = [
            "statAnon_totalEvents",
            "statAnon_totalLength",
            "statAnon_win",
            "statAnon_los",
            "statAnon_tie"
        ];

    $stat = $statArray[rand(0, count($statArray) - 1)];
    echo BuildLifeLocationStatXML("statLiLo_tl", "zesty", "Narnia", $connection);

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