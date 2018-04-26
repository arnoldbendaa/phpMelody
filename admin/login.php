<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

session_start();
require_once('../config.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

if (is_user_logged_in() && (is_admin() || is_moderator() || is_editor())) 
{
	$redir = get_last_referer();
	if( $redir === false || $redir == '/index.php' || $redir == '/index.html')
	{ 	
		$redir = '/'. _ADMIN_FOLDER .'/index.php';
	}
	header('Location: '. _URL . $redir);
	exit();
}

if ($_POST['Login'] == 'Login')
{
	$user = trim($_POST['ausername']);
	$pass = $_POST['apassword'];
	$clearpass = $pass;
	$pass = md5($pass);
	$user = sanitize_user($user);

	if ($user != '' && $clearpass != '')
	{
		$user = secure_sql($user);
		
		$sql = "SELECT * FROM pm_users 
				WHERE username = '".$user."' 
				  AND password = '".$pass."'";
		$result = @mysql_query($sql);
		$count = @mysql_num_rows($result);
		$row = @mysql_fetch_assoc($result);
		@mysql_free_result($result);
		
		if ($count == 0) 
		{
			$error = pm_alert_error('Your username and password don\'t match.');
			@log_error('Failed attempt to log in to Admin Area. (Username used: <em>'.$user.'</em> / IP: <em>'.pm_get_ip().'</em>)', 'Admin login', 1);
		}
		else if ($count == 1) 
		{
			if ( ! in_array($row['power'], array(U_ADMIN, U_MODERATOR, U_EDITOR)))
			{
				$error = pm_alert_error('You do not have permission to access this area.');
				@log_error('Failed attempt to log in to Admin Area. (Username used: <em>'.$user.'</em> / IP: <em>'.pm_get_ip().'</em>)', 'Admin login', 1);
			}
			else 
			{
				log_user_in($user, $clearpass);
			
				$redir = get_last_referer();
	
				if( $redir === false || $redir == '/index.php' || $redir == '/index.html')
				{ 	
					$redir = '/'. _ADMIN_FOLDER .'/index.php';
				}
				header("Location: ". _URL . $redir);
				
				exit();
			}
		}
	}
	else
	{
		$error = pm_alert_error('Please enter your username and password.');
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title>Admin Area - Log in</title>
<link rel="shortcut icon" type="image/ico" href="img/favicon.ico" />
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-responsive.min.css" />
<!--[if lt IE 9]>
<script src="js/css3-mediaqueries.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="css/admin.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/admin-wrap.css" />

<link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css' media='all' />
<!--[if lt IE 9]>
<link rel="stylesheet" type="text/css" media="screen" href="css/admin-ie.css" />
<link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:700" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:700italic" rel="stylesheet" type="text/css">
<![endif]-->
<style type="text/css">
html{margin:0;padding:0}
body{background-color:#E6E6E6;color:#626262;font-family:'Helvetica Neue',Helvetica,Arial Geneva,sans-serif;font-size:12px;margin:0;padding:0}
.placeholder{color:#c3c3c3}
.login-wrap{margin:0 auto;padding:0}
.pm-logo{display:block;margin:50px auto;height:70px;background:url('img/login-logo.png') no-repeat top center;}
.login-wrap h1{margin:0 auto;padding:44px 0;text-align:center;text-shadow:0 -1px 0 #888;font-family:"Open Sans",Helvetiva,Verdana;font-weight:normal;font-size:22px;letter-spacing:-1px;color:#636c75;text-shadow:0 1px 0 #FFF;-moz-text-shadow:0 1px 0 #FFF;-webkit-text-shadow:0 1px 0 #FFF;display:block;width:300px}
.login-wrap label{font-size:12px}
.login-form{margin:0 auto;padding:35px 30px;background-color:#FFF;width:300px;border:1px solid #FFF;text-align:left;box-shadow:0 2px 2px #cdcdcd;-moz-box-shadow:0 2px 2px #cdcdcd;-webkit-box-shadow:0 2px 2px #cdcdcd;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px}
.login-input{display:block;margin-bottom:16px}
.footer-links{font-size:11px;font-weight:bold;-webkit-font-smoothing:antialiased;color:#757677;text-shadow:0 1px 0 #FFF;margin:0 auto;margin-top:12px;text-align:center}
.footer-links a,.footer-links a:hover,.footer-links a:visited{color:#45a4e1}
.showLoader { display: none; }
</style>
<!--[if !IE]>
<style type="text/css">
@-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
.fade-in{opacity:0;-webkit-animation:fadeIn ease-in 1;-moz-animation:fadeIn ease-in 1;animation:fadeIn ease-in 1;-webkit-animation-fill-mode:forwards;-moz-animation-fill-mode:forwards;animation-fill-mode:forwards;-webkit-animation-duration:.2s;-moz-animation-duration:.2s;animation-duration:.2s}
.fade-in.one{-webkit-animation-delay:.2s;-moz-animation-delay:.2s;animation-delay:.2s}
.fade-in.two{-webkit-animation-delay:.5s;-moz-animation-delay:.5s;animation-delay:.5s}
</style>
<![endif]-->
<script type="text/javascript">
window.onload = function() {
  document.getElementById("hocusfocus").focus();
}
</script>
</head>
<body>
<!--[if lt IE 9]>
<div class="alert alert-info alert-old-browser">The browser you are using could be limiting the potential of <?php echo _SITENAME; ?>. We strongly recommend that you upgrade to a newer/different browser.</div>
<![endif]-->
<div class="login-wrap">
  <div class="container">
    <div class="pm-logo fade-in one">
      <h1>
        <?php if(_SITENAME != "PHP Melody") { echo _SITENAME; } else { echo $config['homepage_title']; } ?>
      </h1>
    </div>
    <div class="row-fluid fade-in two">
      <div class="span12">
        <form action="login.php" method="post" name="login" onSubmit="return checkFields();" class="form-horizontal login-form">
          <?php echo $error; ?>
          <fieldset>
            <div class="login-input">
              <input type="text" name="ausername" class="span12 input-login" id="hocusfocus" placeholder="Username" autofocus>
            </div>
            <div class="login-input">
              <input type="password" name="apassword" class="span12 input-login" autocomplete="off" placeholder="Password">
            </div>
            <button type="submit" name="Login" value="Login" class="btn btn-blue" id="login">Sign in</button> <span class="showLoader"> <img src="<?php echo _URL; ?>/<?php echo (defined('_ADMIN_FOLDER')) ? _ADMIN_FOLDER : 'admin'; ?>/img/ico-loading.gif" width="16" height="16" /></span>
          </fieldset>
        </form>
        <div class="footer-links">
        Forgot your password? <a href="<?php echo _URL."/login."._FEXT."?do=forgot_pass";?>" target="_blank">Click here</a>.<br /><br />
        <a href="<?php echo _URL?>">&larr; Return to <?php echo _SITENAME; ?></a>
        </div>
        <!--<div align="center">Powered by PHP Melody <?php echo _PM_VERSION; ?></div>--> 
      </div>
    </div>
  </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script> 
<script type="text/javascript">
$('#login').click(function() {
	$('.showLoader').show().delay(1000).fadeOut();
	$(this).html('Signing in...');
});

$(document).ready(function(){$("[placeholder]").focus(function(){var input=$(this);if(input.val()==input.attr("placeholder")){input.val("");input.removeClass("placeholder")}}).blur(function(){var input=$(this);if(input.val()==""||input.val()==input.attr("placeholder")){input.addClass("placeholder");input.val(input.attr("placeholder"))}}).blur();$("[placeholder]").parents("form").submit(function(){$(this).find("[placeholder]").each(function(){var input=$(this);if(input.val()==input.attr("placeholder")){input.val("")}})})});
function checkFields(){missinginfo="";if(document.login.ausername.value==""){missinginfo+="\n     -  Username"}if(document.login.apassword.value==""){missinginfo+="\n     -  Password"}if(missinginfo!=""){missinginfo="These fields are empty:\n"+missinginfo+"\n";alert(missinginfo);return false}else{return true}};
</script>
</body>
</html>
