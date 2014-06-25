<?php
    include('./Stats/GetLifetimeLocationStat.php');
    include('./utility/DocumentMaker.php');
    //phpinfo();
    //echo $connection;
    
    $connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "user_stats");
    $length = 7.1;
    $id = 'tom';
    $loc = 'Narnia';
    $opId = 'dawg';

    $stats = $collection->findOne(array('_id' => $id), array('lifetime_longest' => true, '_id' => false));
        $valuesToSet = array();

    $collection = $connection->selectCollection("peeveepee", "achievements");
    //$collection->insert(GetAchievementDoc($id, $loc, $length, 'Lifetime Longest'));
    $collection->update(
                array('owner_id' => $id, 'name' => 'Lifetime Longest'),
                GetAchievementDoc($id, $loc, $length, 'Lifetime Longest')
            );
        //var_dump($achievementChecker);

    echo 'done';
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