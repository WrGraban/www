<?php

	function BuildAnonXML($statType, &$connection)
	{
		/*
		"statAnon_totalEvents",
		"statAnon_totalLength",
		"statAnon_win",
		"statAnon_los",
		"statAnon_tie"
		*/

		$xmlReturn = '<r><msg>' . $statType . '</msg><data>';
		$collection = $connection->selectCollection('peeveepee', 'user_stats');
		$name = "stat";
		$query;

		switch($statType)
		{
		case "statAn_te":
			$query = 'lifetime_event_count';
			break;
		case "statAn_tl":
			$name = 'human_time';
			$query = 'lifetime_event_length';
			break;
		case "statAn_win":
			$query = 'lifetime_wins';
			break;
		case "statAn_los":
			$query = 'lifetime_losses';
			break;
		case "statAn_tie":
			$query = 'lifetime_ties';
			break;
		}

		$doc = $collection->findOne(array("_id" => "anonymous"), array("_id" => false, $query => true));

    	$xmlReturn .= '<' . $name . '>' . $doc[$query] . '</' . $name . '></data></r>';
    	return $xmlReturn;
	}

?>