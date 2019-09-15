<?php
require('includes/application_top.php');
require('includes/classes/team.php');
?><!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>NFL Pick 'Em</title>

	<base href="<?php echo SITE_URL; ?>" />
	<link rel="stylesheet" type="text/css" media="all" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="css/custom.css" />
	<!--link rel="stylesheet" type="text/css" media="all" href="css/all.css" /-->
	<!--link rel="stylesheet" type="text/css" media="screen" href="css/jquery.countdown.css" /-->
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/modernizr-2.7.0.min.js"></script>
	<script type="text/javascript" src="js/svgeezy.min.js"></script>
	<script type="text/javascript" src="js/jquery.main.js"></script>
</head>
<?php
$week = (int)getCurrentWeek();
$visitor = new team($_REQUEST['visitor']);
$home = new team($_REQUEST['home']);
$matchup = historicalMatchups($home->teamID, $visitor->teamID);
$matchupTotals = getMatchupTotals($matchup, $home->teamID, $visitor->teamID);

$totalPoints = $matchupTotals[$visitor->teamID]['pf'] + $matchupTotals[$visitor->teamID]['pa'];
$gamesCount = count($matchupTotals[$home->teamID]);

$overUnder = overUnder($totalPoints, $gamesCount);

echo '<pre>';
echo 'Over Under ' . $overUnder;

//print_r($matchupTotals);
echo '</pre>';
?>
<body class=''>
                <div class="container">


<div class="row">
      <div class="col-md-4 mb-5">
        <div class="card h-100">
            <div class="card-body">
            <h2 class="card-title"><?=$visitor->city . ' ' . $visitor->team?> </h2>
            <p>W/L <?=$matchupTotals[$visitor->teamID]['w']?> / <?=$matchupTotals[$visitor->teamID]['l']?></p>
            <div class="team-logo"><img src="images/logos/<?=$visitor->teamID?>.svg" height="150" width="150"/></div>
            <table class="table table-striped">
                    <thead>
                        <tr>
                          <th>PF</th>
                          <th>PA</th>
                          <th> Visitor W/L</th>
                        </tr>
                        <tr>
                            <?php print_r($matchupTotals[$visitor->teamID]); ?>
                            <td><?=$matchupTotals[$visitor->teamID]['pf']?></td>
                            <td><?=$matchupTotals[$visitor->teamID]['pa']?></td>
                            <td><?=$matchupTotals[$visitor->teamID]['visitorWin']?> / <?=$matchupTotals[$visitor->teamID]['visitorLoss']?> </td>
                        </tr>    
                    </thead>
                </table>
          </div>
          <div class="card-footer">
            <a href="#" class="btn btn-primary btn-sm">More Info</a>
          </div>
        </div>
      </div>
      <!-- /.col-md-4 -->
      <div class="col-md-4 mb-5">
        <div class="card h-100">
          <div class="card-body">
            <h2 class="card-title">Historical Matchups</h2>
            
            <table class="table table-striped">
    <thead>
      <tr>
        <th>Visitor</th>
        <th>Visitor Score</th>
        <th>Game Time</th>
        <th>Home</th>
        <th>Home Score</th>
      </tr>
    </thead>
    <tbody>
        <?php
 foreach($matchup as $k => $v) {
                    echo '<tr>';
                    echo '<td>'. $v['visitorID'] . '</td>';
                    echo '<td>'. $v['visitorScore'] . '</td>';
                    echo '<td>'. $v['gameTimeEastern'] . '</td>';
                    echo '<td>'. $v['homeID'] . '</td>';
                    echo '<td>'. $v['homeScore'] . '</td>';
                    echo '</tr>';
//                    echo '<p class="card-text">' . $v['visitorID'] . ' - - ' . $v['visitorScore']. '  ' .$v['gameTimeEastern'] . '  ' .$v['homeID'] . '  ' . $v['homeScore'] . '</p>' ;
                }
                ?>
    </tbody>
  </table>
          </div>
          <div class="card-footer">
            <a href="#" class="btn btn-primary btn-sm">More Info</a>
          </div>
        </div>
      </div>
      <!-- /.col-md-4 -->
      <div class="col-md-4 mb-5">
        <div class="card h-100">
            <div class="card-body">
            <h2 class="card-title"><?=$home->city . ' ' . $home->team ?></h2>
            <p>W/L  <?=$matchupTotals[$home->teamID]['w']?> / <?=$matchupTotals[$home->teamID]['l']?></p>
            <div class="team-logo"><img src="images/logos/<?=$home->teamID?>.svg" height="150" width="150" /></div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                          <th>Points For</th>
                          <th>Points Against</th>
                          <th>Home W/L</th>
                        </tr>
                        <tr>
                            <td><?=$matchupTotals[$home->teamID]['pf']?></td>
                            <td><?=$matchupTotals[$home->teamID]['pa']?></td>
                            <td><?=$matchupTotals[$home->teamID]['homeWin']?> / <?=$matchupTotals[$home->teamID]['homeLoss']?></td>
                        </tr>    
                    </thead>
                </table>
          </div>
          
          <div class="card-footer">
            <a href="#" class="btn btn-primary btn-sm">More Info</a>
          </div>
        </div>
      </div>
      <!-- /.col-md-4 -->

    </div>
    </div> <!-- /container -->
</body>
</html>
