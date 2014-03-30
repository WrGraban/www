<?php

	$connection = new MongoClient();
    $collection = $connection->selectCollection("peeveepee", "locations");

    // HACK!
    $lat = 37.78643;
    $lon = -122.3997;

    $cursor = $collection->find(array("loc" => array("\$near" => array($lat, $lon))), array("name" => true));

    $returnXml = "<root>";

    while($cursor->hasNext())
    {
    	$loc = $cursor->getNext();
    	
    	$returnXml .= "<loc><id>";
    	$returnXml .= $loc["_id"];
    	$returnXml .= "</id><name>";
    	$returnXml .= $loc['name'];
    	$returnXml .= "</name></loc>";
    }

    $returnXml .= "</root>";

    echo $returnXml;

    $connection->close();
?>