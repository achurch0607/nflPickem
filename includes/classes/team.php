<?php
/*
// question class written by Kevin Roth 6/19/2008
// http://www.kevinroth.com/
*/

class team {
	var $teamID = '';
	var $divisionID = 0;
	var $city = '';
	var $team = '';
	var $teamName = '';

	// Class constructor
	function team($teamID) {
		return $this->getTeam($teamID);
	}

	function getTeam($teamID) {
		global $mysqli;
		$sql = "select * from " . DB_PREFIX . "teams where teamID = '" . $teamID . "';";
		$query = $mysqli->query($sql) or die($sql);
		if ($row = $query->fetch_assoc()) {
			$this->teamID = $teamID;
			$this->divisionID = $row['divisionID'];
			$this->city = $row['city'];
			$this->team = $row['team'];
			if (!empty($row['displayName'])) {
				$this->teamName = $row['displayName'];
			} else {
				$this->teamName = $row['city'] . ' ' . $row['team'];
			}
			return true;
		} else {
			return false;
		}
	}
        
        function getTeamRecord(){
            $score = array('homeScore' => 0, 'visitorScore' => 0, 'homeID' => '', 'visitorID' => '', 'homeWin' => 0, 'homeLoss' => 0, 'visitorWin' => 0, 'visitorLoss' => 0);
            global $mysqli;
            $sql = "select * from " . DB_PREFIX . "schedule WHERE (homeID = '$this->teamID 'OR visitorID = '$this->teamID') AND (homeScore != 0 OR visitorScore != 0);";
            $query = $mysqli->query($sql) or die($sql . ' died getting team record');

            while($row = $query->fetch_assoc()) {

                $data = ($row);
                if($data['homeID'] == $this->teamID) {
                     $data['homeScore'] > $data['visitorScore'] ? $score['homeWin']++ : $score['homeLoss']++;
                }
                if($data['visitorID'] == $this->teamID){
                    $data['homeScore'] < $data['visitorScore'] ? $score['visitorWin']++ : $score['visitorLoss']++;
                }
            }

            $totalGames = count($score);
            $score['wins'] = $score['homeWin'] + $score['visitorWin'];
            $score['loss'] = $score['homeLoss'] + $score['visitorLoss'];

            return $score;
         
        }

}
