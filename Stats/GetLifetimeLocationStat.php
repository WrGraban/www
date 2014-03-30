<?php
	
	$connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "users");

/*
    $user_id = $_POST['uid'];
    $loc_id = $_POST['lid'];
    $stat = $_POST['stat'];
*/

    $user_id = $_GET['uid'];
    $loc_id = $_GET['lid'];
    $stat = $_GET['stat'];
    $query = "";

    // Decide which stat to find based on the post data.
    switch($stat)
    {
    	case "count":
    		$query = 'stats.location_stats.' . $loc_id . '.loc_event_count';
    		break;
    	case "length":
    		$query = 'stats.location_stats.' . $loc_id . '.loc_total_length';
    		break;
    	case "highest":
    		$query = 'stats.location_stats.' . $loc_id . '.loc_highest_length';
    		break;
    	case "losses":
    		$query = 'stats.location_stats.' . $loc_id . '.loc_losses';
    		break;
    	case "wins":
    		$query = 'stats.location_stats.' . $loc_id . '.loc_wins';
    		break;
    	case "ties":
    		$query = 'stats.location_stats.' . $loc_id . '.loc_ties';
    		break;
    }

    $doc = $collection->findOne(array("_id" => $user_id), array("_id" => false, $query => true));
    $subStat = substr($query, strrpos($query, '.') + 1);
    echo $doc['stats']['location_stats'][$loc_id][$subStat];
    $connection->close();
?>