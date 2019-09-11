<?php
require_once('includes/application_top.php');
require('includes/classes/team.php');

$activeTab = 'home';

include('includes/header.php');

if ($user->userName == 'admin') {
?>
    <img src="images/art_holst_nfl.jpg" width="192" height="295" alt="ref" style="float: right; padding-left: 10px;" />
    <h1>Welcome, Admin!</h1>
    <p><b>If you feel that the work I've done has value to you,</b> I would greatly appreciate a paypal donation (click button below).  I have spent many hours working on this project, and I will continue its development as I find the time.  Again, I am very grateful for any and all contributions.</p>
<?php
} else {
    //sets warning for current week
	if ($weekExpired) {
		//current week is expired, show message
		echo '	<div class="bg-warning">The current week is locked.  <a href="results.php">Check the Results &gt;&gt;</a></div>' . "\n";
	} else {
		//if all picks not submitted yet for current week
		$picks = getUserPicks($currentWeek, $user->userID);
		$gameTotal = getGameTotal($currentWeek);
		if (sizeof($picks) < $gameTotal) {
			echo '	<div class="alert alert-warning" style="text-align:center;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> YOU HAVE NOT MADE ALL OF YOUR PICKS FOR  WEEK ' . $currentWeek . '<a href="entry_form.php?week='. $currentWeek . '" > Enter  Your Picks Now</a><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></div>';
                }
	}

        //build summary for each week        
        $weekRow = indexPicksSummary();
        $i = 1;
        while($i <= count($weekRow)){
            if($weekRow[$i]['expired']){
                if(@$lastCompletedWeek >= (int)$weekRow[$i]['weekNum']){
                    $scoreTotal = '<li><span class="weekDetails-li"><i class="weekDetails weekDetails-check"></i></span><strong><b>Score: ' . $userScore . '/' . $weekTotal . ' (' . number_format(($userScore / $weekTotal) * 100, 2) . '%)</b></strong><a href="results.php?week='.$weekRow[$i]['weekNum'].'">See Results &raquo;</a></li>';
                } else {
                    $scoreTotal = '<li><span class="weekDetails-li"><i class="weekDetails weekDetails-check"></i></span><strong>Week is closed,</b> but scores have not yet been entered.</strong><a href="results.php?week='.$weekRow[$i]['weekNum'].'">See Results &raquo;</a>';
                }
                    
            }else {
			//week is not expired yet, check to see if all picks have been entered
			$picks = getUserPicks($weekRow[$i]['weekNum'], $user->userID);
			if (sizeof($picks) < (int)$weekRow[$i]['gamesTotal']) {
				//not all picks were entered
    
				if ((int)$currentWeek == (int)$weekRow[$i]['weekNum']) {
					//only show in red if this is the current week
					$tmpStyle = ' style="color: red;"';
				}
				$scoreTotal = '<li><span class="weekDetails-li" ><i class="weekDetails weekDetails-check"></i></span><strong style="color: red;"><b>Missing ' . ((int)$weekRow[$i]['gamesTotal'] - sizeof($picks)) . ' / ' . $weekRow[$i]['gamesTotal'] . ' picks.</b></strong></li>';
			} else {
				//all picks were entered
				$scoreTotal = '<li><span class="weekDetails-li" ><i class="weekDetails weekDetails-check"></i></span><strong><b>All picks entered. </b></strong><a href="entry_form.php?week=' . $weekRow[$i]['weekNum'] . '"> Change your picks &raquo;</a></li>';
			}
		}
            
        
?>
    
            <div class="row">
                <div id="content" class="col-md-12 col-xs-12">
                    <section class="pricing py-5">
                        <div class="container-fluid">
                            <div class="row">
                          <!-- Free Tier -->
                                <div class="col-lg-12">
                                    <div class="weekDetails mb-5 mb-lg-0">
                                        <div class="weekDetails-body">
                                            
                                            <h2 class="weekDetails-title text-uppercase text-center">Week <?=$weekRow[$i]['weekNum']?></h2>
                                            
                                            <h3 class="weekDetails-price text-center"><strong>Cutoff Time: <span class="period"><?=date('n/j g:i a', strtotime('-2 hours',strtotime($weekRow[$i]['cutoffTime'])))?> MST</span></strong></h3>
                                            <hr>
                                            <ul class="weekDetails-ul">
                                                <?=$scoreTotal;?>
                                                <li><span class="weekDetails-li"><i class="weekDetails weekDetails-check"></i></span><strong>First Game Starts <?=date('n/j g:i a', strtotime('-2 hours',strtotime($weekRow[$i]['firstGameTime'])))?> MST</strong></li>
                                                <li><span class="weekDetails-li"><i class="weekDetails weekDetails-check"></i></span><strong><?=$weekRow[$i]['gamesTotal']?> games this week.</strong></li>
                                            </ul>
                                            <a href="entry_form.php?week=<?=$weekRow[$i]['weekNum']?>" class="btn btn-block btn-primary text-uppercase">Enter  Week <?=$weekRow[$i]['weekNum']?> Picks Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
<?php
    $i++;
    }//end while
} //end if
require('includes/footer.php');
