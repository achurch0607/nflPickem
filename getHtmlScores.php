<?php
//(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('cli only');
require('includes/application_top.php');

//$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die('error connecting to db');
//$mysqli->set_charset('utf8');
//if ($mysqli) {
//	//check for presence of install folder
//	if (is_dir('install')) {
//		//do a query to see if db installed
//		//$testQueryOK = false;
//		$sql = "select * from  " . DB_PREFIX . "teams";
//		//die($sql);
//		if ($query = $mysqli->query($sql)) {
//			//query is ok, display warning
//			$warnings[] = 'For security, please delete or rename the install folder.';
//		} else {
//			//tables not not present, redirect to installer
//			header('location: ./install/');
//			exit;
//		}
//		$query->free();
//	}
//} else {
//	die('Database not connected.  Please check your config file for proper installation.');
//}

//load source code, depending on the current week, of the website into a variable as a string
$url = "http://www.nfl.com/liveupdate/scorestrip/ss.xml";
if ($xmlData = file_get_contents($url)) {
	$xml = simplexml_load_string($xmlData);
	$json = json_encode($xml);
	$games = json_decode($json, true);
}

//return;
//build scores array, to group teams and scores together in games
$scores = array();
$week = $games['gms']['@attributes']['w'];

foreach ($games['gms']['g'] as $gameArray) {
	$game = $gameArray['@attributes'];
	if ($game['q'] == 'F' || $game['q'] == 'FO') {
		$overtime = (($game['q'] == 'FO') ? 1 : 0);
		$away_team = $game['v'];
		$home_team = $game['h'];
		$away_score = (int)$game['vs'];
		$home_score = (int)$game['hs'];

		$winner = ($away_score > $home_score) ? $away_team : $home_team;
		$gameID = getGameIDByTeamID($week, $home_team);
		if (is_numeric(strip_tags($home_score)) && is_numeric(strip_tags($away_score))) {
			if ($game['q'] == 'F' || $game['q'] == 'FO') {
				$scores[] = array(
					'gameID' => $gameID,
					'awayteam' => $away_team,
					'visitorScore' => $away_score,
					'hometeam' => $home_team,
					'homeScore' => $home_score,
					'overtime' => $overtime,
					'winner' => $winner
				);
			}
		}
	}
}

//see how the scores array looks
//echo '<pre>' . print_r($scores, true) . '</pre>';
//print_r($scores);
 updateScores($scores, $mysqli);
 return;

//game results and winning teams can now be accessed from the scores array
//e.g. $scores[0]['awayteam'] contains the name of the away team (['awayteam'] part) from the first game on the page ([0] part)
