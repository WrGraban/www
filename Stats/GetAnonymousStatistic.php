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
		$name; $query;

		switch($statType)
		{
		case "statAnon_totalEvents":
			$name = "evt_total";
			$query = 'stats.lifetime_event_count';
			break;
		case "statAnon_totalLength":
			$name = "total_len";
			$query = 'stats.lifetime_event_length';
			break;
		case "statAnon_win":
			$name = "win";
			$query = 'stats.lifetime_wins';
			break;
		case "statAnon_los":
			$name = "los";
			$query = 'stats.lifetime_losses';
			break;
		case "statAnon_tie":
			$name = "tie";
			$query = 'stats.lifetime_ties';
			break;
		}

		$doc = $collection->findOne(array("_id" => "anonymous"), array("_id" => false, $query => true));
    	$subStat = substr($query, strpos($query, '.') + 1);

    	$xmlReturn .= '<' . $name . '>' . $doc['stats'][$subStat] . '</' . $name . '></data></r>';
    	return $xmlReturn;
	}

?>