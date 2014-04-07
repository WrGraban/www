<?php
	
	function CheckAchievements($connection, $id, $length, $loc, $opId)
	{
		$collection = $connection->selectCollection("peeveepee", "users");

		// Begin the return XmlDoc
		echo "<r>";

		///////////////////////////////
		///////////////////////////////
		////////// LIFETIME ///////////
		///////////////////////////////
		///////////////////////////////

		// NOTE:  I was using $in, but since my achievements are stored in an unordered array, it was having trouble
		//	with some of the logic.  For now I'm just going to grab each subdocument separately.  TODO: Investigate
		//	if this is wise or not.

		// This will get much bigger eventually, but I only need to check these two stats for achievements right now
		$lifetimeStats = $collection->findOne(array("_id" => $id), array("stats.lifetime_highest_length" => true, "_id" => false));

		$valuesToSet = array();

		/////////////////////////////////////
		////////// LIFETIME LONGEST
		if($lifetimeStats['stats']['lifetime_highest_length'] < $length)
		{
			echo "<ach><tit>ach_lifeLonTit</tit><msg>";

			$valuesToSet['stats.lifetime_highest_length'] = $length;

			$achievementChecker = $collection->findOne(
				array("_id" => $id, "achievements.lifetime.name" => "Lifetime Longest"),
				array("achievements.lifetime.$" => true, "_id" => false)
			);

			//var_dump($achivementChecker);

			// Check to see if it was the first time they got this achievement
			if($achievementChecker == null)
			{
				echo "ach_first</msg>";

				$data = array(
					"name" => "Lifetime Longest",
					"length" => $length,
					"ts" => new MongoDate(),
					"loc" => $loc
				);
				
				$collection->update(array("_id" => $id), array('$push' => array("achievements.lifetime" => $data)));
			}
			else
			{
				// SNEAKY: Since findOne only returns a single doc, i know that the timestamp i'm looking for is at index 0
				$date = $achievementChecker['achievements']['lifetime'][0]['ts']->sec;

				/*
				echo "On " . date('l', $date) . " the " . date('jS', $date) . " of " . date('F, Y', $date) . " you battled at " .
					$achievementChecker['achievements']['lifetime'][0]['loc'] . " for " .
					$achievementChecker['achievements']['lifetime'][0]['length'] . " seconds.  But today you have beat " .
					"that score!  Congratulations!!</msg>";
				*/
				// I am going to have to shorten this as much as possible to lower data usage and allow localization
				echo 'ach_lifeLon</msg>';
				echo '<data>';
				echo '<date>' . date('Y-M-d h:i:s', $date) . '</date>';
				echo '<loc>' . $achievementChecker['achievements']['lifetime'][0]['loc'] . '</loc>';
				echo '<len>' . $achievementChecker['achievements']['lifetime'][0]['length'] . '</len>';
				echo '</data>';

				$newValues = array(
					"name" => "Lifetime Longest",
					"length" => $length,
					"ts" => new MongoDate(),
					"loc" => $loc
				);

				// I think i'll need to update immediately so that the 'unknown index operator' works.
				// TODO: Test ways to get around so many updates/findOnes
				$collection->update(array("_id" => $id, "achievements.lifetime.name" => "Lifetime Longest"),
				 array('$set' => array('achievements.lifetime.$' => $newValues)));
			}

			$collection->update(array("_id" => $id), array('$set' => array("stats.lifetime_highest_length" => $length)));

			echo "</ach>";
		}
		
		///////////////////////////////
		///////////////////////////////
		////////// LOCATION ///////////
		///////////////////////////////
		///////////////////////////////

		$locationStats = $collection->findOne(array("_id" => $id, "stats.location_stats.name" => $loc), array("_id" => false, "stats.location_stats.$.loc_highest_length" => true));

		if($locationStats['stats']['location_stats'][0]['loc_highest_length'] < $length)
		{
			echo "<ach><tit>ach_locLonTit</tit><msg>";

			$achievementChecker = $collection->findOne(
				array("_id" => $id, "achievements.locations.loc" => $loc, "achievements.locations.name" => "Location Longest"), 
				array("_id" => false, "achievements.locations.$" => true)
			);

			if($achievementChecker == null)
			{
				echo "ach_locFirst</msg>";

				$data = array(
					"name" => "Location Longest",
					"ts" => new MongoDate(),
					"loc" => $loc,
					"length" => $length
				);

				$collection->update(array("_id" => $id), array('$push' => array("achievements.locations" => $data)));
			}
			else
			{

				/*
				echo "On " . date('l', $date) . " the " . date('jS', $date) . " of " . date('F, Y', $date) . " you battled " .
					"here for " . $achievementChecker['achievements']['locations'][0]['length'] . " seconds.  But today you have beat " .
					"that score!  Congratulations!!</msg>";
				*/
				echo 'ach_locLon</msg>';
				$date = $achievementChecker['achievements']['locations'][0]['ts']->sec;

				echo '<data>';
				echo '<date>' . $date . '</date>';
				echo '<len>' . $achievementChecker['achievements']['locations'][0]['length'] . '</len>';
				echo '</data>';
				

				$data = array(
					"name" => "Location Longest",
					"loc" => $loc,
					"ts" => new MongoDate(),
					"length" => $length
				);

				$collection->update(
					array("_id" => $id, "achievements.locations.name" => "Location Longest", "achievements.locations.loc" => $loc),
					array('$set' => array("achievements.locations.$" => $data))
				);
			}

			$collection->update(
				array("_id" => $id, "stats.location_stats.name" => $loc),
				 array('$set' => array('stats.location_stats.$.loc_highest_length' => $length))
			);

			echo "</ach>";
		}

/*
		/////////////////////////////////////
		////////// LANDMARK LIFETIME EVENT COUNT

		// Finally set the new data into the stats's document!
		if(empty($valuesToSet) == false)
		{
			$collection->update(array("_id" => $id), array('$set' => $valuesToSet), array("upsert" => true));
		}
*/
		echo "</r>";
	}

	// The purpose of this function is to determine if a particular number is 'special' and deserving of
	//	an achievement.  This should only be used for integer values.
	//	10, 25, 50, 100, and every 50 afterwards.
	//	TODO: Have a special case for 'really special' like multiples of 500
	function IsImportantNumber($n)
	{
		if($n == 10 || $n == 25 || $n % 50 == 0)
			return true;

		return false;
	}


?>