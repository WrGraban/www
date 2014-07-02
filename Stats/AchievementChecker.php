<?php

	function CheckAchievements(&$connection, $id, $length, $loc, $opId)
	{
		$collection = $connection->selectCollection("peeveepee", "user_stats");

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
		//$lifetimeStats = $collection->findOne(array("_id" => $id), array("stats.lifetime_longest" => true, "_id" => false));
		$stats = $collection->findOne(array('_id' => $id), array('lifetime_longest' => true, '_id' => false));
		$valuesToSet = array();

		/////////////////////////////////////
		////////// LIFETIME LONGEST
		//if($lifetimeStats['stats']['lifetime_highest_length'] < $length)
		if($stats['lifetime_longest'] < $length)
		{
			echo "<ach><tit>ach_lifeLonTit</tit><msg>";

			$valuesToSet['lifetime_longest'] = $length;

			// Swap to the achievements collection
			$collection = $connection->selectCollection('peeveepee', 'achievements');

			// Check to see if they have gotten this achievement before
			$achievementChecker = $collection->findOne(
				array('owner_id' => $id, 'name' => 'Lifetime Longest'),
				array('owner_id' => false, '_id' => false, 'name' => false)
			);

			// Check to see if it was the first time they got this achievement
			if($achievementChecker == null)
			{
				echo "ach_first</msg>";
				
				$collection->insert(GetAchievementDoc($id, $loc, $length, 'Lifetime Longest'));
			}
			else
			{
				$date = $achievementChecker['timestamp']->sec;

				// I am going to have to shorten this as much as possible to lower data usage and allow localization
				// ^ Victory
				echo 'ach_lifeLon</msg>';
				echo '<data>';
				echo '<date>' . date('Y-M-d h:i:s', $date) . '</date>';
				echo '<loc>' . $achievementChecker['loc_name'] . '</loc>';
				echo '<len>' . $achievementChecker['length'] . '</len>';
				echo '</data>';

				$collection->update(
					array('owner_id' => $id, 'name' => 'Lifetime Longest'),
					GetAchievementDoc($id, $loc, $length, 'Lifetime Longest')
				);
			}

			// Update the stats collection with the 
			$collection = $connection->selectCollection('peeveepee', 'user_stats');
			$collection->update(array('_id' => $id), array('$set' => array('lifetime_longest' => $length)));

			echo "</ach>";
		}
		
		///////////////////////////////
		///////////////////////////////
		////////// LOCATION ///////////
		///////////////////////////////
		///////////////////////////////

		// Swap to location_stats
		$collection = $connection->selectCollection('peeveepee', 'location_stats');
		// Find the user's longest at this location
		$locationStats = $collection->findOne(
			array('owner_id' => $id, 'loc_name' => $loc), 
			array('_id' => false, 'loc_longest' => true)
		);

		if($locationStats['loc_longest'] < $length)
		{
			echo "<ach><tit>ach_locLonTit</tit><msg>";

			$collection = $connection->selectCollection('peeveepee', 'achievements');

			// Try to find the document
			$achievementChecker = $collection->findOne(
				array('owner_id' => $id, 'name' => 'Location Longest', 'loc_name' => $loc),
				array('_id' => false, 'length' => true, 'timestamp' => true)
			);

			if($achievementChecker == null)
			{
				echo "ach_locFirst</msg>";

				$collection->insert(GetAchievementDoc($id, $loc, $length, 'Location Longest'));
			}
			else
			{
				echo 'ach_locLon</msg>';
				$date = $achievementChecker['timestamp']->sec;

				echo '<data>';
				echo '<date>' . date('Y-M-d h:i:s', $date) . '</date>';
				echo '<len>' . $achievementChecker['length'] . '</len>';
				echo '</data>';
				
				// Update the achievement
				$collection->update(
					array('owner_id' => $id, 'loc_name' => $loc),
					GetAchievementDoc($id, $loc, $length, 'Location Longest')
				);
			}

			// Select LocStats
			$collection = $connection->selectCollection('peeveepee', 'location_stats');
			// Update the user's locStats with the new longest
			$collection->update(
				array('owner_id' => $id, 'loc_name' => $loc),
				array('$set' => array('loc_longest' => $length))
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