<?php

	function BuildUserStatXML($statType, $id, &$connection)
    {
        $query = "";
        $responseXml = "";
        $elementName = 'stat';
        $collection = $connection->selectCollection('peeveepee', 'users');

        // Decide which stat to find based on the post data.
        switch($statType)
        {
        	case "statU_te":
        		$query = "stats.lifetime_event_count";
        		break;
        	case "statU_tl":
        		$query = "stats.lifetime_event_length";
                $elementName = 'human_time';
        		break;
        	case "statU_lon":
        		$query = "stats.lifetime_highest_length";
        		break;
        	case "statU_los":
        		$query = "stats.lifetime_losses";
        		break;
        	case "statU_win":
        		$query = "stats.lifetime_wins";
        		break;
        	case "statU_tie":
        		$query = "stats.lifetime_ties";
        		break;
        }

        $doc = $collection->findOne(array('_id' => $id), array('_id' => false, $query => true));
        $subStat = substr($query, strpos($query, '.') + 1);

        $responseXml .= '<r>';
        $responseXml .= '<msg>' . $statType . '</msg>';
        $responseXml .= '<data><' . $elementName . '>' . $doc['stats'][$subStat] . '</' .$elementName . '></data>';
        $responseXml .= '</r>';
        
        return $responseXml;
    }
?>