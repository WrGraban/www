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
		$collection = $connection->selectCollection('peeveepee', 'users');
		$name = "stat";
		$query;

		switch($statType)
		{
		case "statAn_te":
			$query = 'stats.lifetime_event_count';
			break;
		case "statAn_tl":
			$name = "human_time";
			$query = 'stats.lifetime_event_length';
			break;
		case "statAn_win":
			$query = 'stats.lifetime_wins';
			break;
		case "statAn_los":
			$query = 'stats.lifetime_losses';
			break;
		case "statAn_tie":
			$query = 'stats.lifetime_ties';
			break;
		}

		$doc = $collection->findOne(array("_id" => "anonymous"), array("_id" => false, $query => true));
    	$subStat = substr($query, strpos($query, '.') + 1);

    	$xmlReturn .= '<' . $name . '>' . $doc['stats'][$subStat] . '</' . $name . '></data></r>';
    	return $xmlReturn;
	}

?>