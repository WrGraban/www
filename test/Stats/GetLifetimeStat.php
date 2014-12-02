<?php

	function BuildUserStatXML($statType, $id, &$connection)
    {
        $query = "";
        $responseXml = "";
        $elementName = 'stat';
        $collection = $connection->selectCollection('peeveepee', 'user_stats');

        // Decide which stat to find based on the post data.
        switch($statType)
        {
        	case "statU_te":
        		$query = "lifetime_event_count";
        		break;
        	case "statU_tl":
        		$query = "lifetime_event_length";
                $elementName = 'human_time';
        		break;
        	case "statU_lon":
        		$query = "lifetime_longest";
        		break;
        	case "statU_los":
        		$query = "lifetime_losses";
        		break;
        	case "statU_win":
        		$query = "lifetime_wins";
        		break;
        	case "statU_tie":
        		$query = "lifetime_ties";
        		break;
        }

        $doc = $collection->findOne(array('_id' => $id), array('_id' => false, $query => true));

        $responseXml .= '<r>';
        $responseXml .= '<msg>' . $statType . '</msg>';
        $responseXml .= '<data><' . $elementName . '>' . $doc[$query] . '</' .$elementName . '></data>';
        $responseXml .= '</r>';
        
        return $responseXml;
    }
?>