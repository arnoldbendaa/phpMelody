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
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');
if ($config['spambot_prevention'] == 'recaptcha')
{
	require('include/recaptcha/autoload.php');
	$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_private_key']);
}
// define meta tags & common variables
$meta_title = $lang['contact_us'].' - '._SITENAME;
$meta_description = '';
// end

$post_email = trim(secure_sql($_POST['your_email']));
$post_name = trim(secure_sql(html_entity_decode($_POST['your_name'])));
$importance = trim(secure_sql($_POST['importance']));
$topic = trim(secure_sql($_POST['select']));
$msg = trim($_POST['msg']);
$msg = removeEvilTags($msg);
$ip = secure_sql(pm_get_ip());

$smarty->assign('spambot_prevention', $config['spambot_prevention']);

if (isset($_POST['Submit']))
{
	foreach($_POST as $k => $v)
	{
		// @since v2.3
		if ( $k == 'msg')
		{
			$v = str_ireplace(array("%0a", "%0d"), '', stripslashes($v));
		}
		else
		{
			$v = str_ireplace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($v));
		}
		
		$_POST[$k] = htmlspecialchars($v);
	}
	if($logged_in == 0 && $config['spambot_prevention'] != 'none')
	{
		// check CAPTCHA code
		if ($config['spambot_prevention'] == 'recaptcha')
		{
			$response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
			$valid = $response->isSuccess();
		}
		
		if ($config['spambot_prevention'] == 'securimage')
		{
			include (ABSPATH ."include/securimage/securimage.php");
			$img = new Securimage();
			$valid = $img->check($_POST['imagetext']);
		}

	}
	else $valid = true;
	
	if(empty($msg) || $msg == '') {
		$err_msg = $lang['contact_msg2'];
		$smarty->assign('err_msg', $err_msg);
	}
	if(!is_real_email_address($post_email)) {
		$err_email = $lang['register_err_msg2'];
		$smarty->assign('err_email', $err_email);
	}
	if(!$valid) {
		$err_captcha = $lang['register_err_msg1'];
	} elseif ($valid && is_real_email_address($post_email) && !$err_msg) {
		$confirm_send = '1';
		$smarty->assign('confirm_send', $confirm_send);

			// ** SENDING EMAIL ** //

			require_once("include/class.phpmailer.php");
			
				//*** DEFINING E-MAIL VARS
				$mailsubject = sprintf($lang['mailer_subj2'], $post_name, _SITENAME);
				
				$array_content[]=array("post_email", $post_email);  
				$array_content[]=array("post_name", $post_name);
				$array_content[]=array("importance", $importance);
				$array_content[]=array("topic", $topic);
				$array_content[]=array("msg", $msg);
				$array_content[]=array("ip", $ip);
				//*** END DEFINING E-MAIL VARS
			
			if(file_exists('./email_template/'.$_language_email_dir.'/email_for_webmaster.txt'))
			{
				$mail = send_a_mail($array_content, _EMAIL, $mailsubject, 'email_template/'.$_language_email_dir.'/email_for_webmaster.txt', $post_email);
			}
			elseif(file_exists('./email_template/english/email_for_webmaster.txt'))
			{
				$mail = send_a_mail($array_content, _EMAIL, $mailsubject, 'email_template/english/email_for_webmaster.txt', $post_email);
			}			
			elseif(file_exists('./email_template/email_for_webmaster.txt'))
			{
				$mail = send_a_mail($array_content, _EMAIL, $mailsubject, 'email_template/email_for_webmaster.txt', $post_email);
			}
			else
			{
				@log_error('Email template "email_for_webmaster.txt" not found!', 'Contact Us', 1);
				$mail = TRUE;
			}
			
			if($mail !== TRUE)
			{
				@log_error($mail, 'Contact Us page', 1);
			}
			// ** END SENDING EMAIL ** //
	}
} 
// ASSIG ERRORS 
$smarty->assign('err_captcha', $err_captcha);
// END ERRORS 

if ($config['spambot_prevention'] == 'recaptcha')
{
	$recaptcha_html = recaptcha_get_html($config['recaptcha_public_key']);
	$smarty->assign('recaptcha_html', $recaptcha_html);
}

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->display('contact.tpl');
?>