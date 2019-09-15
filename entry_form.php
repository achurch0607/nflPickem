<?php
require_once('includes/application_top.php');
require('includes/classes/team.php');

if (isset($_POST['action']) == 'Submit') {
   
	$week = $_POST['week'];
        $weeklyPool = $_POST['weeklyPool'];
	$cutoffDateTime = getCutoffDateTime($week);

	//update summary table
	$sql = "delete from " . DB_PREFIX . "picksummary where weekNum = " . $_POST['week'] . " and userID = " . $user->userID . ";";
	$mysqli->query($sql) or die('Error updating picks summary: ' . $mysqli->error);
	$sql = "insert into " . DB_PREFIX . "picksummary (weekNum, userID, showPicks) values (" . $_POST['week'] . ", " . $user->userID . ", 1);";
	$mysqli->query($sql) or die('Error updating picks summary: ' . $mysqli->error);

	//loop through non-expire weeks and update picks
	$sql = "select * from " . DB_PREFIX . "schedule where weekNum = " . $_POST['week'] . " and (DATE_ADD(NOW(), INTERVAL " . SERVER_TIMEZONE_OFFSET . " HOUR) < gameTimeEastern and DATE_ADD(NOW(), INTERVAL " . SERVER_TIMEZONE_OFFSET . " HOUR) < '" . $cutoffDateTime . "');";
	$query = $mysqli->query($sql);
	if ($query->num_rows > 0) {
		while ($row = $query->fetch_assoc()) {
			$sql = "delete from " . DB_PREFIX . "picks where userID = " . $user->userID . " and gameID = " . $row['gameID'];
			$mysqli->query($sql) or die('Error deleting picks: ' . $mysqli->error);

			if (!empty($_POST['game' . $row['gameID']])) {
				$sql = "insert into " . DB_PREFIX . "picks (userID, gameID, pickID, weeklyPool) values (" . $user->userID . ", " . $row['gameID'] . ", '" . $_POST['game' . $row['gameID']] ."', '" . $weeklyPool . "')";
				$mysqli->query($sql) or die('Error inserting picks: ' . $sql . ' ' . $mysqli->error);
			}
		}
	}
	header('Location: results.php?week=' . $_POST['week']);
	exit;
} else {
	$week = (int)$_GET['week'] ? (int)$_GET['week'] : '' ;
	if (empty($week)) {
		//get current week
		$week = (int)getCurrentWeek();
	}
	$cutoffDateTime = getCutoffDateTime($week);
	$firstGameTime = getFirstGameTime($week);
}
$activeTab = 'entry_form';
include('includes/header.php');
?>


        

            
        
    <div class="row">

<div class="row">
    <div class="col-md-12 school-options-dropdown text-center">
        <div class="dropdown btn-group">

            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Select A Week
              <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                             <?php
                          $i = 1;
                          while ($i <= 17) {
                              $weekLink = '<li role="presentation"><a class="dropdown-item" role="menuitem" href="entry_form.php?week='.$i.'">Week '.$i.'</a></li>';
                              echo $weekLink;
                              $i++;
                          }

                      ?>
            </ul>

        </div>      
        <div>
            <label class="radio-inline"><input type="radio" name="weeklyPool" value="1"><b>I'm in for this weeks $10 pool</b></label>
            <label class="radio-inline"><input type="radio" name="weeklyPool" value="0" checked><b>I'm out of this weeks $10 pool</b></label>
        </div>
    </div>
</div>
                    <div class="row">
            
                                <h2 class="text-center"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Week  <?php echo $week; ?> <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></h2>
                                <p class="text-center">Make Your Picks Below</p>
            
                    </div>
	<?php
	//get existing picks
	$picks = getUserPicks($week, $user->userID);

	//get show picks status
	$sql = "select * from " . DB_PREFIX . "picksummary where weekNum = " . $week . " and userID = " . $user->userID . ";";
	$query = $mysqli->query($sql);
	if ($query->num_rows > 0) {
		$row = $query->fetch_assoc();
		$showPicks = (int)$row['showPicks'];
	} else {
		$showPicks = 1;
	}

	//display schedule for week
	$sql = "select s.*, (DATE_ADD(NOW(), INTERVAL " . SERVER_TIMEZONE_OFFSET . " HOUR) > gameTimeEastern or DATE_ADD(NOW(), INTERVAL " . SERVER_TIMEZONE_OFFSET . " HOUR) > '" . $cutoffDateTime . "')  as expired ";
	$sql .= "from " . DB_PREFIX . "schedule s ";
	$sql .= "inner join " . DB_PREFIX . "teams ht on s.homeID = ht.teamID ";
	$sql .= "inner join " . DB_PREFIX . "teams vt on s.visitorID = vt.teamID ";
	$sql .= "where s.weekNum = " . $week . " ";
	$sql .= "order by s.gameTimeEastern, s.gameID";
	//echo $sql;
        $t = new team('LA');
//      
        
        
	$query = $mysqli->query($sql) or die($mysqli->error);
	if ($query->num_rows > 0) {
		echo '<form name="entryForm" action="entry_form.php" method="post" onsubmit="return checkform();">' . "\n";
		echo '<input type="hidden" name="week" value="' . $week . '" />' . "\n";
		//echo '<table cellpadding="4" cellspacing="0" class="table1">' . "\n";
		//echo '	<tr><th>Home</th><th>Visitor</th><th align="left">Game</th><th>Time / Result</th><th>Your Pick</th></tr>' . "\n";
		echo '		<div class="row">'."\n";
		echo '			<div class="col-xs-12">'."\n";
		$i = 0;
                
                
		while ($row = $query->fetch_assoc()) {
			$scoreEntered = false;
			$homeTeam = new team($row['homeID']);
			$visitorTeam = new team($row['visitorID']);
			$homeScore = (int)$row['homeScore'];
			$visitorScore = (int)$row['visitorScore'];
			$rowclass = (($i % 2 == 0) ? ' class="altrow"' : '');
			echo '				<div class="matchup">' . "\n";
			echo '					<div class="row bg-row1">'."\n";
			if (!empty($homeScore) || !empty($visitorScore)) {
				//if score is entered, show score
				$scoreEntered = true;
				if ($homeScore > $visitorScore) {
					$winnerID = $row['homeID'];
				} else if ($visitorScore > $homeScore) {
					$winnerID = $row['visitorID'];
				};
				//$winnerID will be null if tie, which is ok
				echo '					<div class="col-xs-12 center"><b>Final: ' . $row['visitorScore'] . ' - ' . $row['homeScore'] . '</b></div>' . "\n";
			} else {
				//else show time of game
				echo '					<div class="col-xs-12 center">' . date('D n/j g:i a', strtotime( '-2 hours', strtotime($row['gameTimeEastern']))) . ' MST</div>' . "\n";
			}
			echo '					</div>'."\n";
			echo '					<div class="row versus">' . "\n";
			echo '						<div class="col-xs-1"></div>' . "\n";
			echo '						<div class="col-xs-4">'."\n";
			echo '							<label for="' . $row['gameID'] . $visitorTeam->teamID . '" class="label-for-check"><div class="team-logo"><img src="images/logos/'.$visitorTeam->teamID.'.svg" onclick="document.entryForm.game'.$row['gameID'].'[0].checked=true;" /></div></label>' . "\n";
			echo '						</div>'."\n";
			echo '						<div class="col-xs-2 col-md-2">@</div>' . "\n";                                                                     
			echo '						<div class="col-xs-4">'."\n";
			echo '							<label for="' . $row['gameID'] . $homeTeam->teamID . '" class="label-for-check"><div class="team-logo"><img src="images/logos/'.$homeTeam->teamID.'.svg" onclick="document.entryForm.game' . $row['gameID'] . '[1].checked=true;" /></div></label>'."\n";
			echo '						</div>' . "\n";
			echo '						<div class="col-xs-1"></div>' . "\n";
			echo '					</div>' . "\n";
			if (!$row['expired']) {
				echo '					<div class="row bg-row2">'."\n";
				echo '						<div class="col-xs-1"></div>' . "\n";
				echo '						<div class="col-xs-4 center">'."\n";
				echo '							<input type="radio" class="check-with-label" name="game' . @$row['gameID'] . '" value="' . $visitorTeam->teamID . '" id="' . @$row['gameID'] . $visitorTeam->teamID . '"' . ((@$picks[$row['gameID']]['pickID'] == $visitorTeam->teamID) ? ' checked' : '') . ' />'."\n";
				echo '						</div>'."\n";
                                if($user->userID == 2 || $user->userID == 3){
				echo '						<div class="col-xs-2 center" style="font-size: 0.8em;"><a class="btn btn-primary btn-sm" href="historicalMatchup.php?visitor=' . $visitorTeam->teamID . '&home=' . $homeTeam->teamID .'"> historical matchups </a></div>' . "\n";
                                };
                                echo '						<div class="col-xs-2"></div>' . "\n";
				echo '						<div class="col-xs-4 center">'."\n";
				echo '							<input type="radio" class="check-with-label" name="game' . @$row['gameID'] . '" value="' . $homeTeam->teamID . '" id="' . @$row['gameID'] . $homeTeam->teamID . '"' . ((@$picks[$row['gameID']]['pickID'] == $homeTeam->teamID) ? ' checked' : '') . ' />' . "\n";
				echo '						</div>' . "\n";
				echo '						<div class="col-xs-1"></div>' . "\n";
				echo '					</div>' . "\n";
			}
			echo '					<div class="row bg-row3">'."\n";
			echo '						<div class="col-xs-6 center">'."\n";
			echo '							<div class="team">' . $visitorTeam->city . ' ' . $visitorTeam->team . '</div>'."\n";
			$visitorTeamRecord = $visitorTeam->getTeamRecord();
                        $homeTeamRecord = $homeTeam->getTeamRecord();
                        
//                        print_r($homeTeamRecord);
			if (!empty($visitorTeamRecord)) {
				echo '							<div class="record">Record: ' . $visitorTeamRecord['wins'] . ' W - ' . $visitorTeamRecord['loss'].' L </div>'."\n";
                                echo '							<div class="record"> Away Record: ' . $visitorTeamRecord['visitorWin'] . ' W - ' . $visitorTeamRecord['visitorLoss'].' L </div>'."\n";
			}
			$teamStreak = trim(getTeamStreak($visitorTeam->teamID));
			if (!empty($teamStreak)) {
                                echo '							<div class="streak">Streak: ' . $teamStreak . '</div>'."\n";
			}
			echo '						</div>'."\n";
			echo '						<div class="col-xs-6 center">' . "\n";
			echo '							<div class="team">' . $homeTeam->city . ' ' . $homeTeam->team . '</div>'."\n";
			$teamRecord = $homeTeam->getTeamRecord();
                        
			if (!empty($homeTeamRecord)) {
                            
				echo '							<div class="record">Record: ' . $homeTeamRecord['wins'] . ' W - ' . $homeTeamRecord['loss'].' L </div>'."\n";
                                echo '							<div class="record"> Home Record: ' . $homeTeamRecord['homeWin'] . ' W - ' . $homeTeamRecord['homeLoss'].' L </div>'."\n";
			}
			$teamStreak = trim(getTeamStreak($homeTeam->teamID));
			if (!empty($teamStreak)) {
				echo '							<div class="streak">Streak: ' . $teamStreak . '</div>'."\n";
			}
			echo '						</div>' . "\n";
			echo '					</div>'."\n";
			if ($row['expired']) {
				//else show locked pick
				echo '					<div class="row bg-row4">'."\n";
				$pickID = getPickID($row['gameID'], $user->userID);
				if (!empty($pickID)) {
					$statusImg = '';
					$pickTeam = new team($pickID);
					$pickLabel = $pickTeam->teamName;
				} else {
					$statusImg = '<img src="images/cross_16x16.png" width="16" height="16" alt="" />';
					$pickLabel = 'None Selected';
				}
				if ($scoreEntered) {
					//set status of pick (correct, incorrect)
					if ($pickID == $winnerID) {
						$statusImg = '<img src="images/check_16x16.png" width="16" height="16" alt="" />';
					} else {
						$statusImg = '<img src="images/cross_16x16.png" width="16" height="16" alt="" />';
					}
				}
				echo '						<div class="col-xs-12 center your-pick" style="background-color:gray"><b>Your Pick:</b></br />';
				echo $statusImg . ' ' . $pickLabel . ' ' . $statusImg;
				echo '</div>' . "\n";
				echo '					</div>' . "\n";
			}
			echo '				</div>'."\n";
			$i++;
		}
		echo '		</div>' . "\n";
		echo '		</div>' . "\n";
                echo '<label class="radio-inline"><input type="radio" name="weeklyPool" value="1">In for the weekly pool</label>
                        <label class="radio-inline"><input type="radio" name="weeklyPool" value="0" checked>Out for the weekly pool</label>';
		echo '<p class="noprint"><button type="submit" name="action" value="Submit" class="btn btn-primary btn-lg btn-block">Submit Picks</button></p>' . "\n";
		echo '</form>' . "\n";
	}

echo '	</div>'."\n"; // end col
echo '	</div>'."\n"; // end entry-form row

//echo '<div id="comments" class="row">';
//include('includes/comments.php');
//echo '</div>';

include('includes/footer.php');
?>     
<script src="js/entry_form.js"></script>