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

if( !is_user_logged_in() )
{
	header("Location: "._URL. "/index."._FEXT);
	exit();
}
if($logged_in)
{
	$query = mysql_query("SELECT * FROM pm_users WHERE id = '". secure_sql($userdata['id']) ."'");
	$rows = mysql_num_rows($query);
	$r = mysql_fetch_array($query);
	mysql_free_result($query);

	if($rows == 0)
	{
		header("Location: "._URL."");
		exit();
	}
	$userdata['about'] = str_replace("<br />", "\n", $userdata['about']);
 
	$smarty->assign('userdata', $userdata);
	$smarty->assign('form_action', 'edit_profile.'._FEXT);

	if(isset($_POST['save']))
	{
		$errors 	= array();
		$links 		= array();
		$link_patterns	= array('website' => '/^(((http(s?))\:)?\/\/)?(www\.|[a-zA-Z]+\.)*[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,6})(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+:&%\$#\=~_\-]+))*$/',
								'youtube' => '/^(https?:\/\/)?(www\.)?youtube.com\//',
								'facebook' => '/^(https?:\/\/)?(www\.)?facebook.com\//',
								'twitter' => '/^(https?:\/\/)?(www\.)?twitter.com\//',
								'instagram' => '/^(https?:\/\/)?(www\.)?instagram.com\//',
								'google_plus' => '/^(https?:\/\/)?plus\.google.com\//'
								);
		$nr_errors	= 0;
		$success 	= 0;
		
		// @since v2.3 
		foreach ($_POST as $k => $v)
		{
			if ($k == 'aboutme')
			{
				$_POST[$k] = str_ireplace(array("%0a", "%0d"), '', stripslashes($v));
			}
			else
			{
				$_POST[$k] = str_ireplace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($v));
			}
		}

		$aboutme	= $_POST['aboutme'];
		$pass		= md5($_POST['current_pass']);
		$new_pass	= $_POST['new_pass'];
		$email		= trim($_POST['email']);
		$name		= trim($_POST['name']);
		$gender		= secure_sql($_POST['gender']);
		$country	= secure_sql( (int) $_POST['country']);
		$links['website']	= trim($_POST['website']);
		$links['youtube']	= trim($_POST['youtube']);
		$links['facebook']	= trim($_POST['facebook']);
		$links['twitter']	= trim($_POST['twitter']);
		$links['instagram']	= trim($_POST['instagram']);
		$links['google_plus'] = trim($_POST['google_plus']);
		
		$inputs = array();
		foreach($_POST as $key => $val)
		{
			$val = trim($val);
			$val = specialchars($val, 1);
			$inputs[$key] = $val;
		}
		$smarty->assign('inputs', $inputs);

		$modframework->trigger_hook('edit_profile_pre');
		if(isset($aboutme))
		{
			$aboutme = removeEvilTags($aboutme);
			$aboutme = word_wrap_pass($aboutme);
			$aboutme = secure_sql($aboutme);
			$aboutme = specialchars($aboutme, 1);
			$aboutme = str_replace('\n', "<br />", $aboutme);
		}
		if(strcmp($name, $userdata['name']) != 0)
		{
			$name = removeEvilTags($name);
			$name = secure_sql($name);
			$name = specialchars($name, 1);
		}
		else
		{
			$name = secure_sql($userdata['name']);
			$name = specialchars($name, 0);
		}
		if ( ! in_array($gender, array('male', 'female')))
		{
			$gender = '';
		}

		$email = secure_sql($email);
		$email_validation = validate_email($email);

		switch($email_validation)
		{
			case 1:
				$errors['email'] = $lang['register_err_msg2'];
			break;
			case 2:
				if( strcmp($email, $userdata['email']) != 0 )
					$errors['email'] = $lang['register_err_msg3'];
			break;
		}

		if ($new_pass != '' && strcmp($pass, $userdata['password']) != 0)
		{
			$errors['current_pass'] = $lang['ep_msg6'];
		}
		if($country == -1 || $country == '')
		{
			$errors['country'] = $lang['ep_msg7'];
		}
 
		foreach ($links as $k => $v)
		{
			if (strlen($v) > 0 && strpos($v, 'http') !== 0)
			{
				$links[$k] = 'https://'. $v;
			}
		}

		foreach ($link_patterns as $field => $pattern)
		{
			if (strlen($links[$field]) > 0)
			{
				if ( ! preg_match($pattern, $links[$field]))
				{
					switch ($field)
					{
						default:
							
							$errors[$field] = $lang['profile_msg_social_link'];
							
						break;
						
						case 'facebook':
							
							$errors[$field] = $lang['profile_social_fb'] . ': '. $lang['profile_msg_social_link'];
							
						break;
						case 'website':
						case 'youtube':
						case 'twitter':
						case 'instagram':
						case 'google_plus':
							
							$errors[$field] = $lang['profile_social_'. $field] . ': '. $lang['profile_msg_social_link'];
							
						break;
					}
				}
			}
		}

		$nr_errors = count($errors);
		if( $nr_errors == 0 )
		{
			foreach ($links as $k => $v)
			{
				$links[$k] = htmlspecialchars($v);
			}

			$sql = "UPDATE pm_users SET ";

			if($new_pass != '')
			{
				$sql .= "password = '".md5($new_pass)."', ";
			}
			$sql .= "name = '".$name."', gender = '".$gender."', country = '".$country."', email = '".$email."', about = '".$aboutme."' ";
			$sql .= ", social_links = '". secure_sql(serialize($links)) ."' ";
			$modframework->trigger_hook('edit_profile_sql');
			$sql .= " WHERE id = '".$userdata['id']."'";
			$result = @mysql_query($sql);
			$modframework->trigger_hook('edit_profile_post');

			if( !$result )
			{
				$errors['failure'] = $lang['ep_msg8'];
				$success = 0;
			}
			else
			{
				$success = 1;
			}
		}
		else
		{
			$success = 0;
		}
		$smarty->assign('nr_errors', $nr_errors);
		$smarty->assign('errors', $errors);
		$smarty->assign('success', $success);
		if( $new_pass != '' )
		{
			$smarty->assign('changed_pass', 1);
		}
	}
}
// define meta tags & common variables
$meta_title = $lang['edit_profile'];
$meta_description = '';
// end

$show_countries_list = 1;
load_countries_list();

$smarty->assign('show_countries_list', $show_countries_list);
$smarty->assign('countries_list', $_countries_list);
$modframework->trigger_hook('edit_profile_display');

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->display('profile-edit.tpl');
