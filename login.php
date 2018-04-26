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

@header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
@header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
@header( 'Pragma: no-cache' );

require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
// define meta tags & common variables
$meta_title = $lang['login']." - "._SITENAME;
$meta_description = '';
// end

// Initialize some variables
$errors = array();
$nr_errors = 0;
$logged_in = 0;
load_countries_list();

$smarty->register_function('list_categories', 'list_categories');
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->assign('show_countries_list', 1);
$smarty->assign('countries_list', $_countries_list);

$mode = '';
$success = 0;
$redir = '';
$mode = ($_GET['do']) ? $_GET['do'] : '';

switch($mode){ 
	default:
	case 'login':
		
		$smarty->assign('display_form', 'login');

		// get the last referer so that we can redirect the user to his last visited page after logging him in.
		$redir = get_last_referer();
		if( $redir === false){ 	
			$redir = '/index.'._FEXT;
		}
		$dobreak = false;
		$modframework->trigger_hook('login_login_pre');
		if($dobreak) break;
		//	check if user is already logged in
		//	if he already is, redirect him to index page
		if (is_user_logged_in()) 
		{
			header("Location: ". _URL . $redir);
			exit();
		}
		
		//	check if the form has been submitted
		if( isset($_POST['Login'])) 
		{
			$email = $username = '';
			if (strpos($_POST['username'], '@') !== false && strlen($_POST['username']) > 5)
			{
				$email = trim($_POST['username']);
				$email = str_replace("\'", "''", $email);
				$email = secure_sql($email);
				
				if (is_real_email_address($email))
				{
					$sql = "SELECT username 
							FROM pm_users 
							WHERE email LIKE '$email'";
				
					if ($result = @mysql_query($sql))
					{
						$row = mysql_fetch_assoc($result);
						mysql_free_result($result);
						$username = $row['username'];
					}
				}
			}
			else
			{
				$username = sanitize_user(trim($_POST['username']), 0);
			}
			$pass = $_POST['pass'];

			if (empty($username))
			{
				$errors['username'] = $lang['login_msg1'];
				
				if ($email != '')
				{
					$errors['username'] = $lang['login_msg3'];
				}
			}
			if (empty($pass))
			{
				$errors['pass'] = $lang['login_msg2'];
			}
			
			if ( ! confirm_login($username, $pass, false) && $username != '' && $pass != '')
			{
				$errors[] = $lang['login_msg3'];
			}
			
			if (count($errors) == 0)
			{
				$user_id = username_to_id($username);
				$ban = banlist($user_id);

				if ($ban['user_id'] == $user_id && $user_id != '')
				{				
					$errors[] = sprintf($lang['login_msg16'], $ban['reason']);
				}
			}
			
			if (count($errors) > 0)
			{
				$smarty->assign('errors', $errors);
				$smarty->display('user-auth.tpl');
				exit();
			}
			else 
			{
				// this means everything is ok!
				// log him in.
				$dobreak = false;
				$modframework->trigger_hook('login_login_mid');
				if($dobreak) break;
				if( is_user_account_active('', $username) == 0 )
				{
					if ($config['account_activation'] == AA_USER)
					{
						$errors[] = $lang['login_msg4'];
					}
					
					if ($config['account_activation'] == AA_ADMIN)
					{
						$errors[] = $lang['login_msg17'];
					}
					
					$smarty->assign('success', 0);
					$smarty->assign('errors', $errors);
				}
				else
				{
					log_user_in($username, $pass);
					header("Location: ". _URL . $redir);
					exit();
				}
				$dobreak = false;
				$modframework->trigger_hook('login_login_post');
				if($dobreak) break;
				$smarty->display('user-auth.tpl');
				exit();
			}
		}
		else { 
			// show the form. 
			$dobreak = false;
			$modframework->trigger_hook('login_login_show');
			if($dobreak) break;
			$smarty->display('user-auth.tpl');
			exit();
		}
	break; // end 'login'
	
	case 'facebook':
		
		$smarty->assign('display_form', 'login');
		$errors = array();
		$fb_graph_version = 'v2.6';
		
		if ($config['oauth_facebook'] == 0)
		{
			// show PHP Melody login form if Facebook Login is disabled
			$dobreak = false;
			$modframework->trigger_hook('login_login_show');
			if($dobreak) break;
			
			$smarty->display('user-auth.tpl');
			exit();
		}
		
		$redir = get_last_referer();
		if( $redir === false){ 	
			$redir = '/index.'._FEXT;
		}
		
		if (strpos($redir, '#') !== false) // clean facebook URL junk
		{
			$redir = explode("#", $redir);
			$redir = $redir[0];
		}
		
		if (is_user_logged_in()) 
		{
			header('Location: '. _URL . $redir);
			exit();
		}
		
		if (empty($_GET['step']))
		{
			// (re)generate state id 
			$_SESSION['fb_login_state'] = md5($time_now . rand(0, 99999));
			
			// 'scope' param ref: https://developers.facebook.com/docs/facebook-login/permissions
			$fb_login_url = 'https://www.facebook.com/dialog/oauth?client_id='. $config['oauth_fb_app_id'] .'&scope=public_profile,email&redirect_uri='. urlencode(_URL .'/login.php?do=facebook&step=confirm') .'&state='. $_SESSION['fb_login_state'];
			
			if (isset($_GET['re-ask'])) // ask for permission again
			{
				$fb_login_url .= '&auth_type=rerequest';
			}
			
			header('Location: '. $fb_login_url, true, 302);
			exit();
		}
		else
		{
			if ($_GET['code'] != '')
			{
				if ($_SESSION['fb_login_state'] == $_GET['state'])
				{
					// exchange code for an access token
					include(ABSPATH . 'include/httpful/bootstrap.php');
					try {
						$fb_token_url = 'https://graph.facebook.com/'. $fb_graph_version .'/oauth/access_token?client_id='. $config['oauth_fb_app_id'] .'&redirect_uri='. urlencode(_URL .'/login.php?do=facebook&step=confirm') .'&client_secret='. $config['oauth_fb_app_secret'] .'&code='. $_GET['code'];						
						$response = \Httpful\Request::get($fb_token_url)->withoutStrictSSL()->send();
						$fb_token_data = json_decode($response->raw_body, true);
						
						if (array_key_exists('error', $fb_token_data))
						{
							$errors[] = '<strong>Facebook Error:</strong> '. htmlspecialchars($fb_token_data['error']['message']);
							$smarty->assign('errors', $errors);
							$smarty->assign('success', 0);
							$smarty->display('user-auth.tpl');
							exit();
						}
					} catch (Exception $e) {
						$errors[] = 'Login #'. __LINE__ . ': '. $e->getMessage();
					}
					
					// get user account information
					try {
						$fb_userdata_url = 'https://graph.facebook.com/'. $fb_graph_version .'/me?fields=email,picture,name,gender,cover,link&access_token='. $fb_token_data['access_token'] .'&client_id='. $config['oauth_fb_app_id'] .'&client_secret='. $config['oauth_fb_app_secret'];
						$response = \Httpful\Request::get($fb_userdata_url)->withoutStrictSSL()->send();
						$fb_userdata = json_decode($response->raw_body, true);
					} catch (Exception $e) {
						$errors[] = 'Login #'. __LINE__ . ': '. $e->getMessage();
					}
					
					if (array_key_exists('error', $fb_userdata))
					{
						$errors[] = '<strong>Facebook Error:</strong> '. htmlspecialchars($fb_userdata['error']['message']);
						$smarty->assign('errors', $errors);
						$smarty->assign('success', 0);
						$smarty->display('user-auth.tpl');
						exit();
					}
					
					if (trim($fb_userdata['email']) == '')
					{
						$errors[] = $lang['provide_access_to_email'];
					}
					
					if (array_key_exists('id', $fb_userdata))
					{
						// check if user exists
						$sql = "SELECT * 
								FROM pm_users 
								WHERE fb_user_id = '". secure_sql((int) $fb_userdata['id']) ."'";
						$result = @mysql_query($sql); 
						
						if (@mysql_num_rows($result) == 0)
						{
							$sql = "SELECT * 
									FROM pm_users 
									WHERE email = '". secure_sql($fb_userdata['email']) ."'";
							$result = @mysql_query($sql);
							
							if (@mysql_num_rows($result) == 0) 
							{
								// email & facebook_user_id are both unknown => create new account
								$username_base = $username = sanitize_user($fb_userdata['name']);
								$username_appendix = 1;
								while (check_username($username) !== false) // check for duplicate username
								{
									$username = $username_base . $username_appendix++;
								}
								$email = trim($fb_userdata['email']);
								$name = sanitize_name(trim($fb_userdata['name']));
								$name = secure_sql($name);
								$pass =	generate_unique_id();
								$about = '';
								$ip = pm_get_ip();
								
								if ($fb_userdata['bio'] != '')
								{
									$about = trim($fb_userdata['bio']);
									$about = htmlspecialchars($about);
								}
								
								$links = array();
								$links['website']	= '';
								$links['youtube']	= '';
								$links['facebook']	= trim($fb_userdata['link']);
								$links['twitter']	= '';
								$links['instagram']	= '';
								$links['google_plus'] = '';
								
								$email = prepare_for_mysql($email);
								$username = prepare_for_mysql($username);
								
								$sql = "INSERT INTO pm_users (username, password, email, name, gender, reg_date, last_signin, reg_ip, about, power, social_links, fb_user_id, fb_access_token) ";
								$sql .= "VALUES  ('". $username ."', 
												  '". md5($pass) ."', 
												  '". $email ."', 
												  '". $name ."', 
												  '". secure_sql($fb_userdata['gender']) ."',
												  '".$time_now."',
												  '".$time_now."',
												  '". secure_sql($ip) ."',
												  '". secure_sql($about) ."',
												  '".U_ACTIVE."',
												  '". secure_sql(serialize($links)) ."',
												  '". secure_sql((int) $fb_userdata['id']) ."',
												  '". secure_sql($fb_token_data['access_token']) ."')";
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
								
								// download avatar
								$avatar = 'default.gif';
								if (isset($fb_userdata['picture']['data']['url']))
								{
									$avatar_url = $fb_userdata['picture']['data']['url'];
									
									// try getting the 200x200 avatar
									try {
										$url = 'https://graph.facebook.com/'. $fb_graph_version .'/'. $fb_userdata['id'] .'/picture?type=large';
										$response = \Httpful\Request::head($url)->withoutStrictSSL()->send();
										
										if ($response->meta_data['redirect_url'] != '')
										{
											$avatar_url = $response->meta_data['redirect_url'];
										}
									} catch (Exception $e) {}

									//$avatar_url = explode('?', $fb_userdata['picture']['data']['url']);
									//$avatar_url = $avatar_url[0];
									$avatar_ext = 'jpg'; //pm_get_file_extension($avatar_url);
									$avatar_id = rand(0, 1000) .'-'. $user_id;
									$avatar_filename = 'avatar'. $avatar_id .'.'. $avatar_ext;
									$img = null;
									
									if ( ! function_exists('\phpmelody\sources\src_localhost\download_thumb'))
									{
										require_once('./'. _ADMIN_FOLDER .'/src/localhost.php');
									}
									
									if ($img = \phpmelody\sources\src_localhost\download_thumb($avatar_url, _AVATARS_DIR_PATH, $avatar_id))
									{
										@rename($img, _AVATARS_DIR_PATH . $avatar_filename);
										$avatar = $avatar_filename;
										
										list($width_full, $height_full) = @getimagesize(_AVATARS_DIR_PATH . $avatar_filename);
							
										if ($width_full > THUMB_W_AVATAR || $height_full > THUMB_H_AVATAR)
										{
											include(ABSPATH . _ADMIN_FOLDER .'/img.resize.php');
											
											$img_resizer = new resize_img();
											$img_resizer->sizelimit_x = THUMB_W_AVATAR;
											$img_resizer->sizelimit_y = THUMB_H_VIDEO; 
											
											if ($img_resizer->resize_image(_AVATARS_DIR_PATH . $avatar_filename) !== false)
											{
												$avatar_ext = pm_get_file_extension($avatar_filename, true);
												$img_resizer->save_resizedimage(_AVATARS_DIR_PATH, str_replace($avatar_ext, '', $avatar_filename));
											}
										}
									}
								}
								
								// download cover
								if (array_key_exists('cover', $fb_userdata) && isset($fb_userdata['cover']['source']))
								{
									
								}
								
								$sql = "UPDATE pm_users 
										   SET avatar = '". secure_sql($avatar) ."' 
										 WHERE id = ". $user_id;
								$result = @mysql_query($sql);
								
								require_once(ABSPATH .'include/class.phpmailer.php');
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
									@log_error('Email template "email_registration.txt" not found!', 'Facebook Login', 1);
									$mail = TRUE;
								}
								if ($mail !== TRUE)
								{
									@log_error($mail, 'Facebook Login', 1);
								}
								
								// get user data
								$userdata = get_basic_user_data($user_id);
							}
							else
							{
								// user found so link the facebook account with the PHP Melody account
								$userdata = mysql_fetch_assoc($result);
								$userdata['fb_user_id'] = (int) $fb_userdata['id'];
								$userdata['fb_access_token'] = $fb_token_data['access_token'];
								
								$sql_update = "UPDATE pm_users 
												  SET fb_user_id = ". secure_sql((int) $fb_userdata['id']) .",
												      fb_access_token = '". secure_sql($fb_token_data['access_token']) ."'
												WHERE id = ". $userdata['id'];
								$result = @mysql_query($sql_update);
							}
						}
						else
						{
							$userdata = mysql_fetch_assoc($result);
							mysql_free_result($result);
						}
						
						// log user in
						$ban = banlist($userdata['id']);
						if ($ban['user_id'] == $userdata['id'] && $userdata['id'] != '')
						{
							$errors[] = sprintf($lang['login_msg16'], $ban['reason']);
						}
						
						if (count($errors) == 0)
						{
							// new access token?
							if ($userdata['fb_access_token'] != $fb_token_data['access_token'])
							{
								$userdata['fb_access_token'] = $fb_token_data['access_token'];
								
								$sql_update = "UPDATE pm_users 
											  	  SET fb_access_token = '". secure_sql($fb_token_data['access_token']) ."'
												WHERE id = ". $userdata['id'];
								$result = @mysql_query($sql_update);
							}
							
							log_user_in($userdata['username'], $userdata['password'], true, true, true);
							header('Location: '. _URL . $redir);
							exit();
						}
					}
					else
					{
						$errors[] = '<strong>Facebook Error:</strong> Account not found or access denied.';
					}
				}
				else
				{
					if ($_GET['error_reason'] == 'user_denied')
					{
						// user cancelled login; redirect to previous page
						header('Location: '. _URL . $redir);
						exit();
					}
					else
					{
						$errors[] = '<strong>Facebook Error:</strong> '. htmlspecialchars($_GET['error_description']);
					}
				}
			}
		}
		
		if (count($errors) > 0)
		{
			$smarty->assign('errors', $errors);
			$smarty->assign('success', 0);
		}
		
		$smarty->display('user-auth.tpl');
		exit();
		
	break; // end 'facebook'
	
	case 'twitter':
		
		error_reporting(0); // tmhOAuth throws insignificant Warnings and messes up header()
		
		$smarty->assign('display_form', 'login');
		$errors = array();
		$userdata = null;
		
		if ($config['oauth_twitter'] == 0)
		{
			// show PHP Melody login form if Twitter Login is disabled
			$dobreak = false;
			$modframework->trigger_hook('login_login_show');
			if($dobreak) break;
			
			$smarty->display('user-auth.tpl');
			exit();
		}
		
		$redir = get_last_referer();
		if( $redir === false){ 	
			$redir = '/index.'._FEXT;
		}
		
		if (is_user_logged_in()) 
		{
			header('Location: '. _URL . $redir);
			exit();
		}
		
		@chmod(ABSPATH .'include/oauth/', 0755); // @since v2.7 to enable cURL to update the certificate when needed
		include(ABSPATH .'include/oauth/tmhOAuth.php');
		include(ABSPATH .'include/oauth/tmhUtilities.php');
		
		$oauth = new tmhOAuth(array(
					'consumer_key'    => $config['oauth_twitter_consumer_key'], 
					'consumer_secret' => $config['oauth_twitter_consumer_secret'],
				));
		
		if (isset($_SESSION['twitter_access_token']) && $_SESSION['twitter_access_token'] !== false)
		{
			$oauth->config['user_token'] = $_SESSION['twitter_access_token']['oauth_token'];
			$oauth->config['user_secret'] = $_SESSION['twitter_access_token']['oauth_token_secret'];
			
			// verify credentials
			$http_status = $oauth->request('GET', $oauth->url('1.1/account/verify_credentials'));
			
			if ($http_status == 401)
			{
				$http_status = tmhUtilities::auto_fix_time_request($oauth, 'GET', $oauth->url('1.1/account/verify_credentials'));
			}
			
			if ($http_status == 200)
			{
				if (PHP_INT_SIZE < 8) // 32-bit version of PHP
				{					
					$json_data = preg_replace('/"id":([0-9]+),/', '', $oauth->response['response']);
					$twitter_userdata = json_decode($json_data, true);
					unset($json_data);
				}
				else // 64-bit version of PHP
				{
					// note: Win servers might limit integers to 32 bit so if this causes json_decode to throw an error, add a server check too
					$twitter_userdata = json_decode($oauth->response['response'], true);
				}
				
				$sql = "SELECT * 
						FROM pm_users 
						WHERE twitter_user_id = ". secure_sql($twitter_userdata['id_str']);
				$result = @mysql_query($sql);
				
				if (mysql_num_rows($result) == 0)
				{
					// ask for their email address
					$email = trim($_POST['email']);
					
					if ($email != '')
					{
						if ($validate_email = validate_email($email)) 
						{
							if ($validate_email == 1)
							{
								$errors['email'] = $lang['register_err_msg2']; // Email Address is not valid
							}
							
							if ($validate_email == 2)
							{
								$errors['email'] = $lang['register_err_msg3']; // Email Address is already in use
							}
						}
					}
					
					if (empty($email) || count($errors) > 0)
					{
						$smarty->assign('errors', $errors);
						$smarty->assign('inputs', $_POST);
						$smarty->assign('twitter_userdata', $twitter_userdata);
						$smarty->assign('twitter_avatar_url', str_replace(array('https:', '_normal.'), array('', '.'), $twitter_userdata['profile_image_url_https']));
						$smarty->assign('display_form', 'twitter');
						$smarty->display('user-auth.tpl');
						exit();
					}
					
					// create new account
					$username_base = $username = sanitize_user($twitter_userdata['screen_name']);
					$username_appendix = 1;
					while (check_username($username) !== false) // check for duplicate username
					{
						$username = $username_base . $username_appendix++;
					}
					$name = sanitize_name(trim($twitter_userdata['name']));
					$name = secure_sql($name);
					$pass =	generate_unique_id();
					$gender = '';
					$about = '';
					$ip = pm_get_ip();
					
					if ($twitter_userdata['description'] != '')
					{
						$about = trim($twitter_userdata['description']);
						$about = htmlspecialchars($about);
					}
					
					$links = array();
					$links['website']	= '';
					$links['youtube']	= '';
					$links['facebook']	= '';
					$links['twitter']	= 'https://twitter.com/'. $twitter_userdata['screen_name'];
					$links['instagram']	= '';
					$links['google_plus'] = '';
					
					$email = prepare_for_mysql($email);
					$username = prepare_for_mysql($username);
					$time_now = time();
					
					$sql = "INSERT INTO pm_users (username, password, email, name, gender, reg_date, last_signin, reg_ip, about, power, social_links, twitter_user_id) ";
					$sql .= "VALUES  ('". $username ."',
									  '". md5($pass) ."', 
									  '". $email ."', 
									  '". $name ."', 
									  '". $gender ."', 
									  '". $time_now ."', 
									  '". $time_now ."', 
									  '". secure_sql($ip) ."', 
									  '". secure_sql($about) ."', 
									  '". U_ACTIVE ."', 
									  '". secure_sql(serialize($links)) ."', 
									  '". secure_sql($twitter_userdata['id_str']) ."')";
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
					
					// download avatar
					$avatar = 'default.gif';
					if (isset($twitter_userdata['profile_image_url']))
					{
						$avatar_url = str_replace('_normal.', '.', $twitter_userdata['profile_image_url']);
						
						$avatar_ext = pm_get_file_extension($avatar_url);
						$avatar_ext = ($avatar_ext == 'jpeg') ? 'jpg' : $avatar_ext;
						$avatar_id = rand(0, 1000) .'-'. $user_id;
						$avatar_filename = 'avatar'. $avatar_id .'.'. $avatar_ext;
						$img = null;
						
						if ( ! function_exists('\phpmelody\sources\src_localhost\download_thumb'))
						{
							require_once('./'. _ADMIN_FOLDER .'/src/localhost.php');
						}
						
						if ($img = \phpmelody\sources\src_localhost\download_thumb($avatar_url, _AVATARS_DIR_PATH, $avatar_id))
						{
							@rename($img, _AVATARS_DIR_PATH . $avatar_filename);
							$avatar = $avatar_filename;
							
							list($width_full, $height_full) = @getimagesize(_AVATARS_DIR_PATH . $avatar_filename);
							
							if ($width_full > THUMB_W_AVATAR || $height_full > THUMB_H_AVATAR)
							{
								//resize_then_crop(_AVATARS_DIR_PATH . $avatar_filename, _AVATARS_DIR_PATH . THUMB_W_AVATAR, THUMB_H_AVATAR, $new_height, 255, 255, 255);
								include(ABSPATH . _ADMIN_FOLDER .'/img.resize.php');
								
								$img_resizer = new resize_img();
								$img_resizer->sizelimit_x = THUMB_W_AVATAR;
								$img_resizer->sizelimit_y = THUMB_H_VIDEO; 
								
								if ($img_resizer->resize_image(_AVATARS_DIR_PATH . $avatar_filename) !== false)
								{
									$avatar_ext = pm_get_file_extension($avatar_filename, true);
									$img_resizer->save_resizedimage(_AVATARS_DIR_PATH, str_replace($avatar_ext, '', $avatar_filename));
								}
							}
						}
					}
					
					// download cover
					if ($twitter_userdata['profile_banner_url'] != '')
					{
						
					}
					
					$sql = "UPDATE pm_users 
							   SET avatar = '". secure_sql($avatar) ."' 
							 WHERE id = ". $user_id;
					$result = @mysql_query($sql);
		
					require_once(ABSPATH .'include/class.phpmailer.php');
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
						@log_error('Email template "email_registration.txt" not found!', 'Twitter Login', 1);
						$mail = TRUE;
					}
					if ($mail !== TRUE)
					{
						@log_error($mail, 'Twitter Login', 1);
					}
					
					// get user data
					$userdata = get_basic_user_data($user_id);
				}
				else
				{
					$userdata = mysql_fetch_assoc($result);
					mysql_free_result($result);
				}
				
				// log user in
				$ban = banlist($userdata['id']);
				if ($ban['user_id'] == $userdata['id'] && $userdata['id'] != '')
				{				
					$errors[] = sprintf($lang['login_msg16'], $ban['reason']);
				}
				
				if (count($errors) == 0)
				{
					unset($_SESSION['twitter_access_token']);
					log_user_in($userdata['username'], $userdata['password'], true, true, true);
					header('Location: '. _URL . $redir);
					exit();
				}
			}
			else // $http_status != 200 and 401
			{
				$errors[] = '<strong>Twitter Error:</strong> Twitter API not available at the moment. Please try again later.'; 
			}
		}
		else if ($_SESSION['twitter_oauth_token'] !== false  && isset($_REQUEST['oauth_verifier']))
		{
			$oauth->config['user_token'] = $_SESSION['twitter_oauth_token'];
  			$oauth->config['user_secret'] = $_SESSION['twitter_oauth_token_secret'];
			
			$request_params = array('oauth_verifier' => $_REQUEST['oauth_verifier']); //, 'include_email' => 'true'); -- include_email requires app whitelisting by Twitter; use 'true' (string) instead of bool
			$http_status = $oauth->request('POST', $oauth->url('oauth/access_token', ''), $request_params);
			
			if ($http_status == 401)
			{
				$http_status = tmhUtilities::auto_fix_time_request($oauth, 'POST', $oauth->url('oauth/access_token', ''), $request_params);
			}
			
			if ($http_status == 200)
			{
				$_SESSION['twitter_access_token'] = $oauth->extract_params($oauth->response['response']);
				unset($_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret']);
				header('Location: '. _URL .'/login.php?do=twitter');
			}
			else
			{
				$errors[] = '<strong>Twitter Error:</strong> Twitter API not available at the moment. Please try again later.';
			}
		}
		else
		{
			// obtain a request token
			$request_params = array('oauth_callback' => _URL .'/login.php?do=twitter');
			
			$http_status = $oauth->request('POST', $oauth->url('oauth/request_token', '') , $request_params);
			
			if ($http_status == 401)
			{
				$http_status = tmhUtilities::auto_fix_time_request($oauth, 'POST', $oauth->url('oauth/request_token', '') , $request_params);
			}
			
			if ($http_status == 200)
			{
				$response = $oauth->extract_params($oauth->response['response']);
				
				$_SESSION['twitter_oauth_token'] = $response['oauth_token'];
				$_SESSION['twitter_oauth_token_secret'] = $response['oauth_token_secret'];
				
				$force_login = (isset($_REQUEST['force'])) ? '&force_login=1' : '';
				header('Location: '. $oauth->url('oauth/authenticate', '') .'?oauth_token='. $response['oauth_token'] . $force_login);
				exit();
				
			}
			else
			{
				$errors[] = '<strong>Twitter Error:</strong> Twitter API not available at the moment. Please try again later.';
			}
		} // end obtain request token
		
		if (count($errors) > 0)
		{
			$smarty->assign('errors', $errors);
			$smarty->assign('success', 0);
		}
		
		$smarty->display('user-auth.tpl');
		exit();
		
	break; // end 'twitter'
	
	case 'register':
		header("Location: " ._URL. "/register."._FEXT);
		exit();
	break;
	
	
	case 'logout':
		$dobreak = false;
		$modframework->trigger_hook('login_logout');
		if($dobreak) break;
		logout();
		$redir = get_last_referer();
		if( $redir === false){ 	
			$redir = '/index.'._FEXT;
		}
		header("Location: " ._URL. $redir);
		exit();
	break;
	
	
	case 'forgot_pass':

		$smarty->assign('display_form', 'forgot_pass');
		
		if(is_user_logged_in()) { 
			logout();
		}
		$dobreak = false;
		$modframework->trigger_hook('login_forgotpass_pre');
		if($dobreak) break;
		if (isset($_POST['Send']))
		{
			// @since v2.3 
			foreach ($_POST as $k => $v)
			{
				$_POST[$k] = str_ireplace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($v));
			}
			
			$email = $username = '';
			if (strpos($_POST['username_email'], '@') !== false)
			{
				$email = trim($_POST['username_email']);
			}
			else
			{
				$username = trim($_POST['username_email']);
			}
			$inputs = array();
			
			foreach($_POST as $k => $v)
			{
				$inputs[$k] = htmlspecialchars($v);
			}
			$smarty->assign('inputs', $inputs);
			
			if( empty($email) && empty($username) )
			{
				$errors['username_email'] = $lang['login_msg8'];
			}
			elseif ($email != '')
			{
				$validation = validate_email($email);
				
				if ($validation == 1)
				{
					$errors['email'] = $lang['register_err_msg2'];
				}
				else if ($validation == false)
				{
					$errors['email'] = $lang['login_msg7'];
				}
				
			}
			else
			{
				$validation = check_username($username);

				if ($validation == 1)
				{
					$errors['username'] = $lang['register_err_msg4'];
				}
				else if ($validation == 2)
				{
					$errors['username'] = $lang['register_err_msg5'];
				}
			}
			
			if (count($errors) > 0)
			{
				$smarty->assign('errors', $errors);
				$smarty->assign('success', 0);
				$smarty->display('user-auth.tpl');
				exit();
			}
			
			$sql = "SELECT id, username, name, email, power, activation_key
						FROM pm_users 
						WHERE ";
			if ($email != '')
			{
				$email = $email;
				$email = stripslashes($email);
				$email = secure_sql($email);

				$sql .= " email LIKE '". $email ."'";
			}
			else
			{
				$username = stripslashes($username);
				$username = strtolower($username);
				$username = secure_sql($username);
				
				$sql .= " LOWER(username) = '". $username ."'";
			}

			$result = @mysql_query($sql);
			$user = @mysql_fetch_assoc($result);
			@mysql_free_result($result);
			
			$dobreak = false;
			$modframework->trigger_hook('login_forgotpass_send');
			if($dobreak) break;
			
			if ($user == false)
			{
				$errors[] = $lang['login_msg8']; // user not found
			}
			else if( $user['power'] == U_INACTIVE )
			{
				if ($user['activation_key'] != '')
				{
					$errors[] = $lang['login_msg4'];
				}
				else
				{
					$errors[] = $lang['login_msg17'];
				}
			}
			else
			{
				$new_pass = array();
				$new_pass = reset_password($user['email']);
				
				if( ! $new_pass ) {
					$errors[] = $lang['login_msg9'];
					$smarty->assign('errors', $errors);
					$smarty->assign('success', 0);
					$smarty->display('user-auth.tpl');
					exit();
				}
				else
				{
					$email = $user['email'];
					
					$activation_link  =    _URL;
					$activation_link .=    "/login." . _FEXT;
					$activation_link .=    "?do=pwdreset&u=" . $user['id'] . "&key=" . $new_pass['key'];
					
					if (preg_match("/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", pm_get_ip()) !== false)
					{
						$ip = pm_get_ip();
					}
					else
					{
						$ip = 'Unknown';
					}
					
					require_once("include/class.phpmailer.php");
						//*** DEFINING E-MAIL VARS
						$mailsubject = sprintf($lang['mailer_subj3'], _SITENAME);
						
						$array_content[]=array("mail_username", $user['username']);  
						$array_content[]=array("mail_new_pass", $new_pass['pass']);
						$array_content[]=array("mail_ip", $ip);
						$array_content[]=array("mail_sitename", _SITENAME);
						$array_content[]=array("mail_url", _URL);
						$array_content[]=array("mail_activation_link", $activation_link);
						//*** END DEFINING E-MAIL VARS
						if(file_exists('./email_template/'.$_language_email_dir.'/email_forgot_password.txt'))
						{
							$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/'.$_language_email_dir.'/email_forgot_password.txt');
						}
						elseif(file_exists('./email_template/english/email_forgot_password.txt'))
						{
							$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/english/email_forgot_password.txt');
						}
						elseif(file_exists('./email_template/email_forgot_password.txt'))
						{
							$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/email_forgot_password.txt');
						}
						else
						{
							@log_error('Email template "email_forgot_password.txt" not found!', 'User Login Page', 1);
							$mail = TRUE;
						}
						if($mail !== TRUE)
						{
							@log_error($mail, 'User Login Page', 1);
						}
					
					// ** END SENDING EMAIL ** //
					$smarty->assign('success', 1);
					$smarty->display('user-auth.tpl');
					exit();
				}
			}
			
			$smarty->assign('errors', $errors);
			$smarty->assign('success', 0);
			$smarty->display('user-auth.tpl');
			exit();
		}// end if $_POST['send'] == 'send'
		else{ 
		
			// show the form;
			$smarty->assign('success', 0);
			$smarty->display('user-auth.tpl');
			exit();
		}
	break;
	case 'pwdreset':
	case 'activate':
		
		$dobreak = false;
		$modframework->trigger_hook('login_activate_pre');
		if($dobreak) break;
		if(is_user_logged_in()) {
			header("Location: "._URL. "/index."._FEXT);
			exit();
		}
		
		$user_id	= (int) $_GET['u'];
		$key		= trim($_GET['key']);
		$success	= 0;

		if($user_id == '' || $key == '')
		{
			$errors[] = 'Invalid request.';
		}
		else
		{
			$sql = "SELECT * FROM pm_users WHERE id = '".secure_sql($user_id)."'";
			$result = mysql_query($sql);
			if( ! $result )
			{
				$errors[] = $lang['login_msg11'].' <a href="'. _URL .'/contact_us.'. _FEXT .'">'. $lang['contact_us'] . "</a>";
				$smarty->assign('errors', $errors);
				$smarty->display('user-auth.tpl');
				exit();
			}
			$user = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			if($mode == 'activate')
			{
				if($user == '' || is_array($user) === FALSE)
				{
					$errors[] = $lang['login_msg12'];
				}
				elseif($user['power'] != U_INACTIVE)
				{
					$errors[] = $lang['login_msg13'];
				}
				elseif($user['activation_key'] == '' || (strcmp($user['activation_key'], $key) != 0))
				{
					$errors[] = $lang['login_msg14'];
				}
				else
				{
					$sql = "UPDATE pm_users SET power = '".U_ACTIVE."', activation_key = '' WHERE id = '".$user['id']."'";
					$result = @mysql_query($sql);
					$dobreak = false;
					$modframework->trigger_hook('login_activate_post');
					if($dobreak) break;
					if( ! $result )
					{
						$errors[] = $lang['login_msg11'].' <em>' . $config['contact_mail'] . '</em>';
					}
					else
					{
						$success = 1;
					}
				}
			}
			elseif($mode == 'pwdreset')
			{
				if($user == '' || is_array($user) === FALSE)
				{
					$errors[] = $lang['login_msg12'];
				}
				elseif($user['activation_key'] == '' || (strcmp($user['activation_key'], $key) != 0))
				{
					$errors[] = $lang['login_msg14'];
				}
				else
				{
					$sql = "UPDATE pm_users SET password = '".$user['new_password']."', activation_key = '', new_password = '' WHERE id = '".$user['id']."'";
					$dobreak = false;
					$modframework->trigger_hook('login_pwdreset_post');
					if($dobreak) break;
					$result = @mysql_query($sql);
					if( ! $result )
					{
						$errors[] = $lang['login_msg11'].' <em>' . $config['contact_mail'] . '</em>';
					}
					else $success = 1;
				}
			}
		}
		
		$smarty->assign('errors', $errors);
		$smarty->assign('success', $success);
		if($mode == 'activate')
		{
			$smarty->assign('display_form', 'activate_acc');
			$smarty->display('user-auth.tpl');
		}
		elseif($mode == 'pwdreset')
		{
			$smarty->assign('display_form', 'pwdreset');
			$smarty->display('user-auth.tpl');
		}
		exit();
	break;	

}
exit();