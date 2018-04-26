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
$form_load_time = (int) $_SESSION['register-form'];
$_SESSION['register-form'] = time();

@header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
@header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
@header( 'Pragma: no-cache' );

require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');
if ($config['spambot_prevention'] == 'recaptcha')
{
	require('include/recaptcha/autoload.php');
	$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_private_key']);
}
$modframework->trigger_hook('register_top');

$meta_title = $lang['create_account'].' - '._SITENAME;
$meta_description = $lang['register_msg3'];

if( is_user_logged_in() ) {
	header("Location: "._URL. "/index."._FEXT);
	exit();
}
// Initialize some variables
$form_action = 'register.'._FEXT;
$errors = array();
$nr_errors = 0;
$logged_in = 0;
$locations = array();
$show_countries_list = 1;
load_countries_list();

$terms_toa_page = get_page_by_name('terms-toa');
$smarty->assign('terms_page', $terms_toa_page);
$smarty->assign('display_form', 'register');
$smarty->assign('spambot_prevention', $config['spambot_prevention']);

if( isset($_POST['Register']) && $config['allow_registration'] == '1')
{
	$config['register_time_to_submit'] = (int) $config['register_time_to_submit'];
	$time_to_submit = time() - $form_load_time;

	// check the honeypot
	if ((strlen($_POST['website']) != 10) || ( ! ctype_digit($_POST['website']))
		|| ($config['register_time_to_submit'] > 0 && ($time_to_submit < $config['register_time_to_submit'] || $time_to_submit > 1200)))
	{
		if ($conn_id)
		{
			@mysql_close($conn_id);
		}
		header('Location: '. _URL .'/register.'. _FEXT);
		exit();
	}
	unset($_POST['website']);

	// check CAPTCHA code
	if ($config['spambot_prevention'] == 'recaptcha')
	{
		$response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		
		if ( ! $response->isSuccess())
		{
			$errors['captcha'] = $lang['register_err_msg1'];
		}
	}
	
	if ($config['spambot_prevention'] == 'securimage')
	{
		include (ABSPATH ."include/securimage/securimage.php");
		$securimage = new Securimage();

		if ( ! $x = $securimage->check($_POST['imagetext']))
		{
			$errors['captcha'] = $lang['register_err_msg1'];
		}
	}

	$required_fields = array('email' => $lang['your_email'],
							 'username' => $lang['username'], 
							 'pass' => $lang['password'], 
							 'confirm_pass' => $lang['confirm_pass'], 
							 'name' => $lang['your_name'],
							 );
	foreach( $_POST as $key => $value) {
		$value = trim($value);
		if(array_key_exists(strtolower($key), $required_fields) && empty($value) )
			$errors[$key] = $required_fields[$key]." ".$lang['register_err_msg8'];
	}
	$inputs = array();
	foreach($_POST as $key => $val)
	{
		$val = trim($val);
		$val = specialchars($val, 1);
		$inputs[$key] = $val;
	}
	$smarty->assign('inputs', $inputs);
	if($show_countries_list && $_POST['country'] == '-1') {
		$errors['country'] = $lang['choose_country'];
	}
	
	$nr_errors = count($errors);
	if($nr_errors == 0) {
		
		// @since v2.3 
		foreach ($_POST as $k => $v)
		{
			$_POST[$k] = str_ireplace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($v));
		}
		
		// grab the fields - values in variables and filter them for safety
		$email = 		trim($_POST['email']);
		$username = 	sanitize_user($_POST['username']);
		$pass =			$_POST['pass'];
		$conf_pass = 	$_POST['confirm_pass'];
		$name = sanitize_name(trim($_POST['name']));
		$name = secure_sql($name);
		$gender = secure_sql($_POST['gender']);
		$location = secure_sql( (int) $_POST['country']);
		// check if the requried fields are valid
		if($var = validate_email($email)) {
			if($var == 1) 
				$errors['email'] = $lang['register_err_msg2'];
			if($var == 2)
				$errors['email'] = $lang['register_err_msg3'];
		}

		if($var = check_username($username)) { 
			if($var == 1)
				$errors['username'] = $lang['register_err_msg4'];
			if($var == 2)
				$errors['username'] = $lang['register_err_msg5'];
			if($var == 3)
				$errors['username'] = $lang['register_err_msg6'];
		}
		
		if( strcmp($pass, $conf_pass)) { 
			$errors['pass'] = $lang['register_err_msg7'];
		}

	}// end if(nr_errors == 0);
	$modframework->trigger_hook('register_fields');
	$nr_errors = count($errors);
	
	if( $nr_errors )
	{
		$modframework->trigger_hook('register_show_form');
		
		if ($config['spambot_prevention'] == 'recaptcha')
		{
			$smarty->assign('recaptcha_html', recaptcha_get_html($config['recaptcha_public_key']));
		}
		$smarty->assign('form_action', $form_action);
		$smarty->assign('errors', $errors);
		$smarty->assign('show_countries_list', $show_countries_list);
		$smarty->assign('countries_list', $_countries_list);
		// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
		$smarty->assign('meta_title', htmlspecialchars($meta_title));
		$smarty->assign('meta_description', htmlspecialchars($meta_description));
		$smarty->assign('template_dir', $template_f);
		$smarty->display('user-auth.tpl');
		exit();
	}
	else { 
		// prepare everything for mysql
		$email 		= prepare_for_mysql($email);
		$username 	= prepare_for_mysql($username);
		$time_now 	= time();

		$sql = "INSERT INTO pm_users (username, password, email, name, gender, country, reg_date, last_signin, reg_ip, about, power, activation_key) VALUES ";
		
		$ip = addslashes(pm_get_ip());

		if ($config['account_activation'] == AA_ADMIN)
		{
			$sql .= "('".$username."', '".md5($pass)."', '".$email."', '".$name."', '".$gender."', '".$location."', '".$time_now."', '".$time_now."', '".$ip."', '', '".U_INACTIVE."', '')";
		}
		else if($config['account_activation'] == AA_USER)
		{
			$activation_key = '';
			$activation_key = generate_activation_key();
			$sql .= "('".$username."', '".md5($pass)."', '".$email."', '".$name."', '".$gender."', '".$location."', '".$time_now."', '".$time_now."', '".$ip."', '', '".U_INACTIVE."', '".$activation_key."')";
		
		}
		else if($config['account_activation'] == AA_DISABLED)
		{
			$sql .= "('".$username."', '".md5($pass)."', '".$email."', '".$name."', '".$gender."', '".$location."', '".$time_now."', '".$time_now."', '".$ip."', '', '".U_ACTIVE."', '')";
		}
		
		$result = @mysql_query($sql);
		if( ! $result )
		{
			$errors[] = $lang['login_msg11'].' <a href="'. _URL .'/contact_us.'. _FEXT .'">'. $lang['contact_us'] . "</a>";
			$smarty->assign('errors', $errors);
			$smarty->display('user-auth.tpl');
			exit();
		}
		$user_id = @mysql_insert_id();
		
		insert_playlist($user_id, PLAYLIST_TYPE_WATCH_LATER, array());
		insert_playlist($user_id, PLAYLIST_TYPE_FAVORITES, array());
		insert_playlist($user_id, PLAYLIST_TYPE_LIKED, array());
		insert_playlist($user_id, PLAYLIST_TYPE_HISTORY, array());

		if (_MOD_SOCIAL && $user_id)
		{
			log_activity(array('user_id' => $user_id, 'activity_type' => ACT_TYPE_JOIN));
		}

		//	MAILS
		if ($config['account_activation'] == AA_ADMIN)
		{
			// ** SENDING EMAIL ** //
	
			require_once("include/class.phpmailer.php");
			
				//*** DEFINING E-MAIL VARS
				$mailsubject = sprintf($lang['mailer_subj6'], _SITENAME);
				
				$array_content[]=array("mail_username", $username);  
				$array_content[]=array("mail_password", $pass);
				$array_content[]=array("mail_ip", $ip);
				$array_content[]=array("mail_sitename", _SITENAME);
				$array_content[]=array("mail_url", _URL);
				//*** END DEFINING E-MAIL VARS
			
			if(file_exists('./email_template/'.$_language_email_dir.'/email_registration_pending.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/'.$_language_email_dir.'/email_registration_pending.txt');
			}
			elseif(file_exists('./email_template/english/email_registration_pending.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/english/email_registration_pending.txt');
			}
			elseif(file_exists('./email_template/email_registration_pending.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/email_registration_pending.txt');
			}
			else
			{
				@log_error('Email template "email_registration_pending.txt" not found!', 'Register Page', 1);
				$mail = TRUE;
			}
			if($mail !== TRUE)
			{
				@log_error($mail, 'Register Page', 1);
			}
			// ** END SENDING EMAIL ** //
			
			$msg = $lang['register_msg6'];
		}
		else if($config['account_activation'] == AA_USER)
		{	
			$activation_link  =    _URL;
			$activation_link .=    "/login." . _FEXT;
			$activation_link .=    "?do=activate&u=" . $user_id . "&key=" . $activation_key;
			
			// ** SENDING EMAIL ** //
	
			require_once("include/class.phpmailer.php");
			
				//*** DEFINING E-MAIL VARS
				$mailsubject = sprintf($lang['mailer_subj4'], _SITENAME);
				
				$array_content[]=array("mail_username", $username);  
				$array_content[]=array("mail_password", $pass);
				$array_content[]=array("mail_ip", $ip);
				$array_content[]=array("mail_sitename", _SITENAME);
				$array_content[]=array("mail_url", _URL);
				$array_content[]=array("mail_activation_link", $activation_link);
				//*** END DEFINING E-MAIL VARS
			
			if(file_exists('./email_template/'.$_language_email_dir.'/email_registration2.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/'.$_language_email_dir.'/email_registration2.txt');
			}
			elseif(file_exists('./email_template/english/email_registration2.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/english/email_registration2.txt');
			}
			elseif(file_exists('./email_template/email_registration2.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/email_registration2.txt');
			}
			else
			{
				@log_error('Email template "email_registration2.txt" not found!', 'Register Page', 1);
				$mail = TRUE;
			}
			if($mail !== TRUE)
			{
				@log_error($mail, 'Register Page', 1);
			}
			// ** END SENDING EMAIL ** //		
			$msg = $lang['register_msg4'];
			$modframework->trigger_hook('register_done_active');
		}
		else if ($config['account_activation'] == AA_DISABLED)
		{	
			// ** SENDING EMAIL ** //
	
			require_once("include/class.phpmailer.php");
			
				//*** DEFINING E-MAIL VARS
				$mailsubject = sprintf($lang['mailer_subj1'], _SITENAME);
				
				$array_content[]=array("mail_username", $username);  
				$array_content[]=array("mail_password", $pass);
				$array_content[]=array("mail_ip", $ip);
				$array_content[]=array("mail_sitename", _SITENAME);
				$array_content[]=array("mail_url", _URL);
				//*** END DEFINING E-MAIL VARS
			
			if(file_exists('./email_template/'.$_language_email_dir.'/email_registration.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/'.$_language_email_dir.'/email_registration.txt');
			}
			elseif(file_exists('./email_template/english/email_registration.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/english/email_registration.txt');
			}
			elseif(file_exists('./email_template/email_registration.txt'))
			{
				$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/email_registration.txt');
			}
			else
			{
				@log_error('Email template "email_registration.txt" not found!', 'Register Page', 1);
				$mail = TRUE;
			}
			if($mail !== TRUE)
			{
				@log_error($mail, 'Register Page', 1);
			}
			// ** END SENDING EMAIL ** //
			$modframework->trigger_hook('register_done_activation');
			
			$msg = $lang['register_msg5'];
		}

		unset($_SESSION['register-form']);

		$smarty->assign('success', 1);
		$smarty->assign('msg', $msg);
		$modframework->trigger_hook('register_done_display');
		$redir = get_last_referer();
		if( $redir === false){ 	
			$redir = '/index.'._FEXT;
		}
		$smarty->assign('redir', _URL . $redir);
		$smarty->assign('meta_title', htmlspecialchars($meta_title));
		$smarty->assign('meta_description', htmlspecialchars($meta_description));
		$smarty->assign('template_dir', $template_f);
		$smarty->display('user-auth.tpl');
	}
}
else
{ 

	$modframework->trigger_hook('register_show_form');
	
	if ($config['spambot_prevention'] == 'recaptcha')
	{
		$smarty->assign('recaptcha_html', recaptcha_get_html($config['recaptcha_public_key']));
	}	
	$smarty->assign('form_action', $form_action);
	$smarty->assign('show_group', $show_group);
	$smarty->assign('errors', $errors);
	$smarty->assign('show_countries_list', $show_countries_list);
	$smarty->assign('countries_list', $_countries_list);
	$smarty->assign('locations', $locations);
	$smarty->assign('meta_title', htmlspecialchars($meta_title));
	$smarty->assign('meta_description', htmlspecialchars($meta_description));
	$smarty->assign('template_dir', $template_f);

	$smarty->display('user-auth.tpl');

}