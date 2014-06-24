<?php
	
	$connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "users");

    function BuildLifeLocationStatXML($stat, $user_id, $loc_id, &$connection)
    {
        echo $loc_id;
        $xmlReturn = '<r><msg>' . $stat . '</msg><data>';
        $collection = $connection->selectCollection('peeveepee', 'users');
        $name = "stat";
        $query = '';

        // Decide which stat to find based on the post data.
        switch($stat)
        {
        	case "statLiLo_te":
        		$query = 'stats.location_stats.' . $loc_id . '.loc_event_count';
        		break;
        	case "statLiLo_tl":
                $name = "human_time";
        		$query = 'stats.location_stats.' . $loc_id . '.loc_total_length';
        		break;
        	case "statLiLo_lon":
        		$query = 'stats.location_stats.' . $loc_id . '.loc_highest_length';
        		break;
        	case "statLiLo_los":
        		$query = 'stats.location_stats.' . $loc_id . '.loc_losses';
        		break;
        	case "statLiLo_win":
        		$query = 'stats.location_stats.' . $loc_id . '.loc_wins';
        		break;
        	case "statLiLo_tie":
        		$query = 'stats.location_stats.' . $loc_id . '.loc_ties';
        		break;
        }

        $doc = $collection->findOne(array("_id" => $user_id), array("_id" => false, $query => true));
        var_dump($doc);
        $subStat = substr($query, strrpos($query, '.') + 1);
        
        $xmlReturn .= '<' . $name . '>' . $doc['stats']['location_stats']['$'][$loc_id][$subStat] . '</' . $name . '></data></r>';
    }
?>