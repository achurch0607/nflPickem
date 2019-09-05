<?php
error_reporting(E_ALL ^ E_NOTICE);
//session_start();

require_once('includes/application_top.php');

$_SESSION = array();

if(is_array($_POST) && sizeof($_POST) > 0){
	$login->validate_password();
}

//require_once('includes/header.php');
if(empty($_SESSION['logged']) || $_SESSION['logged'] !== 'yes') {
	header('Content-Type:text/html; charset=utf-8');
	header('X-UA-Compatible:IE=Edge,chrome=1'); //IE8 respects this but not the meta tag when under Local Intranet
?>
<!DOCTYPE html>
<html xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>NFL Pick 'Em</title>

	<base href="<?php echo SITE_URL; ?>" />
	<link rel="stylesheet" type="text/css" media="all" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="css/custom.css" />
        <link rel="stylesheet" type="text/css" media="all" href="css/all.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.countdown.css" />
	<!--link rel="stylesheet" type="text/css" media="all" href="css/all.css" /-->
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/modernizr-2.7.0.min.js"></script>
	<script type="text/javascript" src="js/svgeezy.min.js"></script>
	<script type="text/javascript" src="js/jquery.main.js"></script>
        <script type="text/javascript" src="js/jquery.jclock.js"></script>
	<script type="text/javascript" src="js/jquery.plugin.min.js"></script>
	<script type="text/javascript" src="js/jquery.countdown.min.js"></script>
	<style type="text/css">
	body { background-color: #eee; }
	.form-signin {
		max-width: 330px;
		padding: 15px;
		margin: 0 auto;
	}
	</style>
</head>

<body>
    <div class='bodyBackground'>
    <div class="container">
		<form class="form-signin" role="form" action="login.php" method="POST">
			<h2 class="form-signin-heading">NFL Pick 'Em Login</h2>
                      
			<?php
			if ($_REQUEST['login'] == 'failed') {
                            
				echo '<div class="text-center"><img src="/images/art_holst_nfl.jpg" class="img-thumbnail"></div>';
                                echo '<div class="text-center"><h2 style="color:yellow;"> LOGIN FAILED!</h2></div>';
			} 
			?>
			<p style="color:white;"><input type="text" name="username" class="form-control" placeholder="Username" required autofocus />
			<input type="password" name="password" class="form-control" placeholder="Password" required /></p>
			<!--label class="checkbox"><input type="checkbox" value="remember-me"> Remember me</label-->
			<p><button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button></p>
			<?php
			if (ALLOW_SIGNUP && SHOW_SIGNUP_LINK) {
				
                                echo '<p style="color:white;"><a href="signup.php" class="btn btn-sm btn-primary btn-block" >Create Account</a></p>';
			}
			?>
			<p style="color:white;"><a href="password_reset.php" class="btn btn-sm btn-primary btn-block" >Forgot Your Password?</a></p>
		</form>

                    <div id="firstGame" class="countdown bg-success"></div>
                    <script type="text/javascript">
                    //set up countdown for first game
                    var firstGameTime = new Date("<?php echo date('F j, Y H:i:00', strtotime('-2 hours',strtotime($firstGameTime))); ?>");
                    firstGameTime.setHours(firstGameTime.getHours() );
                    $('#firstGame').countdown({until: firstGameTime, description: 'Until Week 1 Starts!'});
                    </script>

    </div>
    </div> <!-- /container -->
</body>
</html>
<?php
//require('includes/footer.php');
}
