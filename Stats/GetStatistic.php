<?php
	include("GetAnonymousStatistic.php");

	$connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "users");

    $id = $_POST['id'];
    $loc = $_POST['loc'];
    $isAnon = $_POST['isanon'];

    $isLocStat = rand(0, 1);

    // Declare the variable that will hold all of the possible
    $statArray;

    if($isAnon == "1")
    {
    	///////////////
    	// Anonymous stat
    	$statArray = [
    		"statAnon_totalEvents",
    		"statAnon_totalLength",
    		"statAnon_win",
    		"statAnon_los",
    		"statAnon_tie"
    	];

    	$stat = $statArray[rand(0, count($statArray) - 1)];
    	echo BuildAnonXML($stat, $connection);
    }
    else
    {
    	if($isLocStat == 1)
    	{
    		////////////////////
    		// Location Stat
    	}
    	else
    	{
    		////////////////////
    		// User Stat
    	}
    }

    $connection->close();

    // This function can take two arrays
    function BuildResponseXML($msg, $dataName, $dataValue)
    {
    	if(is_array($dataName) == true && is_array($dataValue) == true)
    	{
    		if(count($dataName) != count($dataValue))
	    	{
	    		echo 'YOU FUCKED UP, ZESTY!  2nd and 3rd arguments in GetStatistic::BuildResponseXML should be of the same length...';
	    	}
	    	else
	    	{
	    		echo '<r>';
				echo '<msg>' . $msg . '</msg>';
		    	echo '<data>';
		    		while(count($dataName) != 0)
		    		{
		    			$name = array_pop($dataName);
		    			$value = array_pop($dataValue);

		    			echo '<' . $name . '>' . $value . '</' . $name . '>';
		    		}
		    	echo '</data>';
		    	echo '</r>';
	    	}
    	}
    	else if(is_array($dataName) == false && is_array($dataValue) == false)
    	{
    		echo '<r>';
			echo '<msg>' . $msg . '</msg>';
	    	echo '<data>';
	    	echo '<' . $dataName . '>' . $dataValue . '</' . $dataName . '>';
	    	echo '</data>';
	    	echo '</r>';
    	}
    }
?>