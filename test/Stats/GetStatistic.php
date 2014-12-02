<?php
	include('GetAnonymousStatistic.php');
	include('GetLifetimeStat.php');
	include('GetLifetimeLocationStat.php');
    include('GetLocationStat.php');
    include('../utility/ServerData.php');

    $connection = new MongoClient($ConnectionString);

    $id = $_POST['id'];
    $loc = $_POST['loc'];
    /////////////////
	/// Values:
	/// 0	-	Anonymous
	/// 1	-	Lifetime
	/// 2	-	LifetimeLocation
	/// 3	-	Location
    $statType = $_POST['type'];
    //////////////////
    $stat = $_POST['stat'];

    // HACK: Testing user stats
    $isLocStat = 1;// rand(0, 1);

    // Declare the variable that will hold all of the possible
    $statArray;

    $retXml = '';

    switch($statType)
    {
    case '0':
    	$retXml = BuildAnonXML($stat, $connection);
    	break;
    case '1':
    	$retXml = BuildUserStatXML($stat, $id, $connection);
    	break;
    case '2':
    	$retXml = BuildLifeLocationStatXML($stat, $id, $loc, $connection);
    	break;
    case '3':
        $retXml = BuildLocStatXML($stat, $id, $loc, $connection);
        break;
    default:
        $retXml = "<r>FAILURE</r>";
        break;
    }

    echo $retXml;

    $connection->close();

    /*
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
    */
?>