<?php
require('includes/application_top.php');
error_reporting(E_ALL);
$weeks = 17;
$schedule = array();

for ($week = 1; $week <= $weeks; $week++) {
	$url = "http://www.nfl.com/ajax/scorestrip?season=".SEASON_YEAR."&seasonType=REG&week=".$week;
	if ($xmlData = @file_get_contents($url)) {
		$xml = simplexml_load_string($xmlData);
		$json = json_encode($xml);
		$games = json_decode($json, true);
	} else {
		die('Error getting schedule from nfl.com.');
	}

	//build scores array, to group teams and scores together in games
	foreach ($games['gms']['g'] as $gameArray) {
		$game = $gameArray['@attributes'];

		//get game time (eastern)
		$eid = $game['eid']; //date
		$t = $game['t']; //time
		$date = DateTime::createFromFormat('Ymds g:i a', $eid.' '.$t.' pm');
		$gameTimeEastern = $date->format('Y-m-d H:i:00');

		//get team codes
		$away_team = $game['v'];
		$home_team = $game['h'];

		$schedule[] = array(
			'weekNum' => $week,
			'gameTimeEastern' => $gameTimeEastern,
			'homeID' => $home_team,
			'visitorID' => $away_team
		);
//                updateFromBuildSchedule($schedule);
	}
        
}

echo '<pre>';
print_r($schedule);
echo '</pre>';
exit;
