<?php
	
    function BuildLifeLocationStatXML($stat, $user_id, $loc_name, &$connection)
    {
        $xmlReturn = '<r><msg>' . $stat . '</msg><data>';
        $collection = $connection->selectCollection('peeveepee', 'location_stats');
        $name = "stat";
        $query = '';

        // Decide which stat to find based on the post data.
        switch($stat)
        {
        	case "statLiLo_te":
        		$query = 'loc_event_count';
        		break;
        	case "statLiLo_tl":
                $name = "human_time";
        		$query = 'loc_total_length';
        		break;
        	case "statLiLo_lon":
        		$query = 'loc_longest';
        		break;
        	case "statLiLo_los":
        		$query = 'loc_losses';
        		break;
        	case "statLiLo_win":
        		$query = 'loc_wins';
        		break;
        	case "statLiLo_tie":
        		$query = 'loc_ties';
        		break;
        }

        $doc = $collection->findOne(array("owner_id" => $user_id, 'loc_name' => $loc_name), array("_id" => false, $query => true));
        
        $xmlReturn .= '<' . $name . '>' . $doc[$query] . '</' . $name . '></data></r>';

        return $xmlReturn;
    }
?>