<?php

	$connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "users");
    
    $id = $_POST['id'];
    $stat = $_POST['stat'];

    $query = "";

    // Decide which stat to find based on the post data.
    switch($stat)
    {
    	case "lec":
    		$query = "stats.lifetime_event_count";
    		break;
    	case "lel":
    		$query = "stats.lifetime_event_length";
    		break;
    	case "lhl":
    		$query = "stats.lifetime_highest_length";
    		break;
    	case "los":
    		$query = "stats.lifetime_losses";
    		break;
    	case "win":
    		$query = "stats.lifetime_wins";
    		break;
    	case "tie":
    		$query = "stats.lifetime_ties";
    		break;
    }

    $doc = $collection->findOne(array("_id" => $id), array("_id" => false, $query => true));
    $subStat = substr($query, strpos($query, '.') + 1);
    echo $doc['stats'][$subStat];

    $connection->close();
?>