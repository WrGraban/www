<?php

	function BuildLocStatXML($statType, $userID, $locName, &$connection)
	{
		// Created by
		// Date created
		// Total event count
		// Total length
		// Unique gladiator count
		$xmlReturn = '<r><msg>' . $statType . '</msg><data>';
        $collection = $connection->selectCollection('peeveepee', 'locations');
        $name = 'stat';
        $query = '';

        // Special case
        $isAge = false;

        switch($statType)
        {
        case 'statLo_cb': // Created by
        	$query = 'creator_tag';
        	break;
        case 'statLo_dc': // Date created
        	$name = 'date';
        	$query = 'date_created';
        	break;
        case 'statLo_te': // Total events
        	$query = 'total_event_count';
        	break;
        case 'statLo_tl': // Total length
        	$name = 'human_time';
        	$query = 'total_length';
        	break;
        case 'statLo_ugc': // Unique Gladiator Count
        	$query = 'unique_gladiator_count';
        	break;
        case 'statLo_age': // The age of the arena
        	$isAge = true;
        	$query = 'date_created';
        	$name = 'human_time';
        	break;
        }

        $doc = $collection->findOne(array('name' => $locName), array('_id' => false, $query => true));

        if($isAge == false)
        {
        	// If it's the date I'll need to get the 'sec' so the server message parser client-side can read it correctly
       		$xmlReturn .= '<' . $name . '>' . ($name == 'date' ? date('Y-M-d h:i:s', $doc[$query]->sec) : $doc[$query]) . '</' . $name . '></data></r>';
        }
        else
        {
        	$now = new MongoDate();
        	$deltaSeconds = $now->sec - $doc[$query]->sec;
        	$xmlReturn .= '<' . $name . '>' . $deltaSeconds . '</' . $name . '></data></r>';
        }
        

        return $xmlReturn;
	}

?>