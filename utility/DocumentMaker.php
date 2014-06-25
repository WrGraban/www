<?php

	function GetUserDoc($newID, $tag, $email, $hashedPass)
	{
		$user = array(
            "_id" => $newID,
            "tag" => $tag,
            "account_type" => "Free", // It's free, beetches!
            "date_created" => date("Y-m-d"),
            "email" => $email,
            "pass" => $hashedPass
        );

        return $user;
	}

	function GetLocationDoc($creator_id, $arena_name, $lat, $lon)
	{
		$location = array(
			"name" => $arena_name,
			"date_created" => date("Y-m-d"),
			"created_by" => $creator_id,
			"total_event_count" => 0,
			"total_length" => 0,
			"unique_gladiator_count" => 0,
			"lat" => $lat,
			"lon" => $lon
		);

		return $location;
	}

	function GetStatsDoc($user_id)
	{
		$stats = array(
			"_id" => $user_id,
			"lifetime_event_count" => 0,
			"lifetime_event_length" => 0,
			"lifetime_longest" => 0,
			"lifetime_wins" => 0,
			"lifetime_losses" => 0,
			"lifetime_ties" => 0
		);

		return $stats;
	}

	function GetLocationStatsDoc($user_id, $loc_id)
	{
		$locStats = array(
			'owner_id' => $user_id,
			'loc_id' => $loc_id,
			'loc_event_count' => 0,
			'loc_longest' => 0,
			'loc_losses' => 0,
			'loc_wins' => 0,
			'loc_ties' => 0,
			'loc_total_length' => 0,
			'unique_gladiator_count' => 0
		);

		return $locStats;
	}

	function GetOpponentCountDoc($user_id, $opponent_id)
	{
		$oppCount = array(
			'owner_id' => $user_id,
			'opponent_id' => $opponent_id,
			'count' => 1
		);

		return $oppCount;
	}

	function GetUniqueGladiatorsDoc($loc_id, $user_id)
	{
		$uniqueGladiator = array(
			'loc_id' => $loc_id,
			'user_id' => $user_id
		);

		return $uniqueGladiator;
	}

	function GetEventDoc($uid, $loc_name, $length, $tag)
	{
		$event = array(
			"user_id" => $uid,
	        "timestamp" => new MongoDate(),
	        "loc_name" => $loc_name,
	        "length" => $length,
	        "tag" => $tag
		);

		return $event;
	}

	function GetAchievementDoc($user_id, $loc_name, $length, $achievement_name)
	{
		$achievement = array(
			'owner_id' => $user_id,
			"name" => $achievement_name,
			"length" => $length,
			"timestamp" => new MongoDate(),
			"loc_name" => $loc_name
		);

		return $achievement;
	}
?>