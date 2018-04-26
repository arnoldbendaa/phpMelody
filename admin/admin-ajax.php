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

if ( ! defined('U_ADMIN'))
{
	define('U_ADMIN', 1);
}

if ( ! doing_cron())
{
	if ( ! $logged_in || ! (is_admin() || is_moderator() || is_editor()))
	{
		exit('Access denied.');
	}
}

$illegal_chars = array(">", "<", "&", "'", '"');

$message = '';
$page	 = '';



if ($_GET['p'] != '' || $_POST['p'] != '')
{
	$page = ($_GET['p'] != '') ? $_GET['p'] : $_POST['p'];
}

if ($_GET['do'] != '' || $_POST['do'] != '')
{
	$action = ($_GET['do'] != '') ? $_GET['do'] : $_POST['do'];
}

if ($page == '')
{
	exit('Page param is required.');
}

switch ($page)
{
	case 'addvideo':
		
		switch ($action)
		{
			case 'checkurl':
				
				if ($_POST['url'] == '')
				{
					exit();
				}
				
				if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
				{
					exit();
				}
				if (is_editor() || (is_moderator() && !mod_can('manage_videos')))
				{
					exit();
				}
				
				$msg = '';
				$msg_color = '';
				
				$url = trim($_POST['url']);
				$url = secure_sql($url);
				$uniq_id = '';
				
				if (strpos($url, 'youtube.com'))
				{
					preg_match("/v=([^(\&|$)]*)/", $url, $matches);
					$url = '%youtube.com/watch?v='. $matches[1];
				}
			
				$sql = "SELECT uniq_id FROM pm_videos_urls 
						WHERE direct = '". $url ."'";
				$result = @mysql_query($sql);
				if ( ! $result)
				{
					$msg = 'MySQL error';
					$msg_color = 'red';
				}
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					$uniq_id = $row['uniq_id'];
					
					$msg = 'This URL was already found into your database! <a href="modify.php?vid='. $uniq_id. '">Edit</a> video.';
					$msg_color = 'red';
				}
				else
				{
					$msg = '';
					$msg_color = 'green';
				}
				mysql_free_result($result);
				
				if (strlen($msg) > 0)
				{
					echo '<small><i><span style="color: '. $msg_color .';">'. $msg .'</span></i></small>';
				}
				
				exit(); // the end
				
			break;
			
			case 'generate-video-slug':
				
				if ($_POST['video-title'] != '')
				{
					$text = trim($_POST['video-title']);
					$text = sanitize_title($text);
					$text = urldecode($text); 
					exit($text);
				}
				
				exit();
				
			break;

			case 'delete-subtitle': // @since v2.3

				if( ! (is_admin() || (is_moderator() && mod_can('manage_videos'))))
				{
					exit(json_encode(array('type' => 'error',
										   'html' => pm_alert_error('You do not have permission to perform this action.', array('id' => '_error'))
										)
									)
					);
				}

				$sub_id = (int) $_POST['sub-id'];
				if ( ! $sub_id)
				{
					exit(json_encode(array('type' => 'error',
										   'html' => pm_alert_error('Invalid ID provided.', array('id' => '_error'), true)
										)
								)
					);
				}

				$sql = "SELECT * FROM pm_video_subtitles
						WHERE id = ". $sub_id;
				$result = mysql_query($sql);
				$sub = mysql_fetch_assoc($result);
				mysql_free_result($result);

				if ( ! $sub)
				{
					exit(json_encode(array('type' => 'error',
											'html' => pm_alert_error('Subtitle not found.', array('id' => '_error'), true)
										)
								)
					);
				}

				$sql = "DELETE FROM pm_video_subtitles
						WHERE id = ". $sub_id;
				if ( ! @mysql_query($sql))
				{
					exit(json_encode(array('type' => 'error',
											'html' => pm_alert_error('Failed to update database: <code>'. mysql_error() .'</code>.', array('id' => '_error'), true)
										)
									)
					);
				}

				if($sub['filename'] != '')
				{
					@unlink( _SUBTITLES_DIR_PATH . $sub['filename']);
				}

				exit(json_encode(array('type' => 'success')));

			break;
		}
		
	break;
	
	case 'metadata':
		
		if( ! (is_admin() || (is_moderator() && mod_can('manage_videos'))))
		{
			exit(json_encode(array('type' => 'error',
								   'html' => pm_alert_error('You do not have permission to perform this action.', array('id' => '_error'))
								  )
							)
				);
		}

		$response_type = 'success'; // success, error
		$error_msg = $html = '';
		
		switch ($action)
		{
			case 'add-meta':
				
				$meta_id = 0;
				
				if ($_POST['meta_key_select'] != '' && $_POST['meta_key_select'] != '_nokey')
				{
					$key = trim($_POST['meta_key_select']);
				}
				else
				{
					$key = trim($_POST['meta_key']);					
				}
				$key = substr($key, 0, 255);

				if (strlen($key) > 0)
				{
					$_POST['meta_value'] = str_replace('"', '&quot;', $_POST['meta_value']);
					$_POST['meta_key'] = $key;
					
					if (is_meta_key_reserved($key))
					{
						$error_msg = 'Names starting with an underscore "_" are reserved for the system.'; 
					}
					else
					{
						$meta_id = add_meta((int) $_POST['item_id'], $_POST['item_type'], $key, $_POST['meta_value']);
					
						if ($meta_id)
						{
							$html = admin_custom_fields_row($meta_id, $_POST);
						}
					}
				}
				else
				{
					$error_msg = '"Custom name" field is required.';
				}
				
				if ($error_msg != '')
				{
					$html = pm_alert_error($error_msg, array('id' => '_error_'));
					$response_type = 'error';
				}

				exit(json_encode(array('type' => $response_type, 'html' => $html, 'meta_id' => $meta_id)));
				
			break;

			case 'update-meta':
				
				$meta_id = (int) $_POST['meta_id'];
				
				if ( ! $meta_id)
				{
					$error_msg = 'Invalid meta_id provided.';
				}
				else
				{
					if (is_meta_key_reserved($_POST['meta_key']))
					{
						$error_msg = 'Names starting with an underscore "_" are reserved for the system.';
					}
					else
					{
						$_POST['meta_value'] = str_replace('"', '&quot;', $_POST['meta_value']);
						$update = update_meta(0, 0, $_POST['meta_key'], $_POST['meta_value'], $meta_id);
						
						if ($update)
						{
							$html = pm_alert_success('Updated');
						}
						else
						{
							$error_msg = 'An error occurred while updating. Please try again.';
						}
					}
				}
				
				if ($error_msg != '')
				{
					$response_type = 'error';
					$html = pm_alert_error($error_msg, array('id' => '_error_'));
				}

				exit(json_encode(array('type' => $response_type, 'html' => $html, 'meta_id' => $meta_id)));

			break;
						
			case 'delete-meta':
			
				$meta_id = (int) $_POST['meta_id'];
				
				if ($meta_id)
				{
					$deleted = delete_meta(0, 0, '', $meta_id);
				}
				
				exit(json_encode(array('type' => $response_type, 'html' => '', 'meta_id' => $meta_id)));
				
			break;
			
			default: 
				exit();
			break;
		}
		
	break; // end case 'metadata';

	case 'manage-categories': // @since 2.2
		
		if ( ! is_admin())
		{
			$ajax_msg = 'Sorry, you do not have access to this area.';
			exit(json_encode(array(	'success' => false, 
									'alert_type' => 'error',
									'msg' => $ajax_msg, 
									'html' => pm_alert_error($ajax_msg))));
		}
		
		switch ($action)
		{
			case 'add-video-category':
			case 'add-article-category':
			
				$create_category_select_html = ''; // will hold the updated 'Create in' dropdown 

				switch ($action)
				{
					case 'add-video-category':
						
						$result = insert_category($_POST, 'video');
						
					break;
					
					case 'add-article-category':
						
						$result = insert_category($_POST, 'article');
						
					break;
				}
				
				if ($result['type'] == 'error')
				{
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $result['msg'], 
											'html' => pm_alert_error($result['msg'], false, true))));
				}

				$_POST['current_selection'][] = $result['id'];
				
				switch ($action)
				{
					case 'add-video-category':
						
						$categories_dropdown_options = array(
														'attr_name' => 'category[]',
														'attr_id' => 'main_select_category must',
														'attr_class' => 'category_dropdown span12',
														'select_all_option' => false,
														'spacer' => '&mdash;',
														'selected' => $_POST['current_selection'],
														'other_attr' => 'multiple="multiple"'
														);
						
		
						$ajax_msg = categories_dropdown($categories_dropdown_options);
						$categories_dropdown_options = array(
														'first_option_text' => '&ndash; Parent Category &ndash;', 
														'first_option_value' => '-1',
														'attr_name' => 'add_category_parent_id',
														'attr_id' => '',
														'attr_class' => '',
														'select_all_option' => true,
														'spacer' => '&mdash;'
														);
						$create_category_select_html = categories_dropdown($categories_dropdown_options);
						
					break;
					
					case 'add-article-category':
						
						 $categories_dropdown_options = array(
														'db_table' => 'art_categories',
														'attr_name' => 'categories[]',
														'attr_id' => 'main_select_category',
														'attr_class' => 'category_dropdown span12',
														'select_all_option' => false,
														'spacer' => '&mdash;',
														'selected' => $_POST['current_selection'], 
														'other_attr' => 'multiple="multiple" size="3"',
														'option_attr_id' => 'check_ignore'
														);
						unset($_article_categories);
						$ajax_msg = categories_dropdown($categories_dropdown_options);
						$categories_dropdown_options = array(
															'db_table' => 'art_categories',
															'first_option_text' => '&ndash; Parent Category &ndash;', 
															'first_option_value' => '-1',
															'attr_name' => 'add_category_parent_id',
															'attr_id' => '',
															'attr_class' => '',
															'select_all_option' => true,
															'spacer' => '&mdash;'
															);
						$create_category_select_html = categories_dropdown($categories_dropdown_options); 
						
					break;
				}
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'create_category_select_html' => $create_category_select_html,
										'msg' => $result['msg'],
										'html' =>  $ajax_msg)));
				
			break;
			
			case 'delete':
				
				if ( ! csrfguard_check_referer('_admin_catmanager'))
				{
					$ajax_msg = 'Invalid token or session expired. Please refresh this page and try again.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => pm_alert_error($ajax_msg, false, true))));
				}
				
				$id = (int) $_POST['id'];
				$category_type = $_POST['type'];
				
				if ($id > 0)
				{
					$result = delete_category($id, $category_type);
					
					$nonce = csrfguard_raw('_admin_catmanager');
					
					if ($result['type'] == 'error')
					{
						exit(json_encode(array(	'success' => false, 
												'alert_type' => 'error',
												'_pmnonce' => $nonce['_pmnonce'],
												'_pmnonce_t' => $nonce['_pmnonce_t'],
												'msg' => $result['msg'], 
												'html' => pm_alert_error($result['msg'], false, true))));
					}
					else
					{
						exit(json_encode(array(	'success' => true, 
												'alert_type' => 'success',
												'_pmnonce' => $nonce['_pmnonce'],
												'_pmnonce_t' => $nonce['_pmnonce_t'],
												'msg' => $result['msg'],
												'html' => pm_alert_success($result['msg'], false, true))));
					}
				}
				
			break;
			
			case 'organize':
				
				$sql_table = ($_POST['type'] == 'article') ? 'art_categories' : 'pm_categories';
				
				$tree = $_POST['tree'];
				$total_items = count($tree);
				
				$order = array($tree[1]['item_id'] => array('id' => $tree[1]['item_id'],
															'parent_id' => 0,
															'position' => 1
															)); 
				for ($i = 2; $i < $total_items; $i++)
				{
					$position = 0;
					$parent_id = ($tree[$i]['parent_id'] != '') ? (int) $tree[$i]['parent_id'] : 0;  
					foreach ($order as $category_id => $c)
					{
						if ($c['parent_id'] == $parent_id && $position < $c['position'])
						{
							$position = (int) $c['position'];
						}
					}
					$position++;
					
					$order[$tree[$i]['item_id']] = array('id' => $tree[$i]['item_id'],
														 'parent_id' => (int) $tree[$i]['parent_id'],
														 'position' => (int) $position
														);
				}
				
				if (count($order) > 0)
				{
					$errors = array();
					
					foreach ($order as $category_id => $c)
					{
						$sql = "UPDATE ". $sql_table ." 
								   SET parent_id = ". $c['parent_id'] .", 
									   position = ". $c['position'] ."
								 WHERE id = $category_id";
						if ( ! $result = mysql_query($sql))
						{
							$errors[] = 'An MySQL error occurred while updating your category: '. mysql_error();
						}
					}
					if (count($errors) > 0)
					{
						exit(json_encode(array(	'success' => false, 
												'alert_type' => 'error',
												'msg' => implode('<br />', $errors),
												'html' => pm_alert_error($errors, false, true))));
					}
				}
				
				$ajax_msg = 'The new order was saved.';
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'msg' => $ajax_msg,
										'html' => pm_alert_success($ajax_msg, false, true))));
					
			break;
			
			case 'mark-featured':
				
				$category_id = (int) $_POST['id'];
				if ( ! $category_id)
				{
					$ajax_msg = 'Missing category ID';
					exit(json_encode(array(	'success' => false,
											'alert_type' => 'error', 
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg, false, true))));
				}
				
				$featured_categories = ($config['homepage_featured_categories'] != '') ? unserialize($config['homepage_featured_categories']) : array();
				
				if ( ! in_array($category_id, $featured_categories))
				{
					// add
					$featured_categories[] = $category_id;
				}
				else
				{
					// remove
					foreach ($featured_categories as $k => $id)
					{
						if ($id == $category_id)
						{
							unset($featured_categories[$k]);
							break;
						}
					}
				}
				
				$update = update_config('homepage_featured_categories', serialize($featured_categories));
				
				if ($update !== true)
				{
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $update[0],
											'html' =>pm_alert_error($update[0], false, true))));
				}
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'msg' => 'Videos from this category will now appear on your homepage',
										'html' => '')));
				
			break;
			
			case 'delete-image':
				
				if ( ! csrfguard_check_referer('_admin_catmanager'))
				{
					$ajax_msg = 'Invalid token or session expired. Please refresh this page and try again.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => pm_alert_error($ajax_msg, false, true))));
				}
				
				$id = (int) $_POST['id'];
				
				$nonce = csrfguard_raw('_admin_catmanager');
				
				if ($id > 0)
				{
					$sql = "SELECT image 
							FROM pm_categories 
							WHERE id = $id";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ($row['image'] != '' && file_exists(_THUMBS_DIR_PATH . $row['image']))
					{
						unlink(_THUMBS_DIR_PATH . $row['image']);
					}
					
					$sql = "UPDATE pm_categories 
							   SET image = '' 
							WHERE id = $id";
					@mysql_query($sql);	
					
					exit(json_encode(array(	'success' => true, 
											'alert_type' => 'success',
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => '',
											'html' => '')));
				}
				
				$ajax_msg = 'Invalid category ID provided.';
				exit(json_encode(array(	'success' => false, 
										'alert_type' => 'error',
										'_pmnonce' => $nonce['_pmnonce'],
										'_pmnonce_t' => $nonce['_pmnonce_t'],
										'msg' => $ajax_msg,
										'html' => pm_alert_error($ajax_msg, false, true))));
			break;
		}
		
	break;

	case 'page':
		
		switch ($action)
		{
			case 'delete':
				
				if( ! $logged_in || ! is_admin())
				{
					echo pm_alert_error('Sorry, you do not have access to this area.');
					exit();
				}
					
				if ( ! csrfguard_check_referer('_admin_pages'))
				{
					echo pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
					exit();
				}
				
				$result = delete_page($_GET['id']);				
				if ($result['type'] == 'error')
				{
					echo pm_alert_error($result['msg'], false, true);
				}
				else
				{
					echo csrfguard_form('_admin_pages');
					echo pm_alert_success($result['msg'], false, true);
				}
				
				exit();
				
			break;
			
		}
		
	break;

	case 'articles':
		
		// test permissions for moderators; editors and admins are allowed.
		if (is_moderator() && mod_cannot('manage_articles'))
		{
			echo pm_alert_error('Sorry, you do not have access to this area.');
			exit();
		}
				
		switch ($action)
		{
			case 'delete': // delete an article 
				
				if ( ! csrfguard_check_referer('_admin_articles'))
				{
					echo pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
					exit();
				}
					
				$id = (int) $_GET['id'];
				if ($id > 0)
				{
					$result = delete_article($id);
					
					if ($result['type'] == 'error')
					{
						echo pm_alert_error($result['msg']);
					}
					else
					{
						// refresh token
						echo csrfguard_form('_admin_articles');
						echo pm_alert_success($result['msg']);
					}
				}

			break;
			
			case 'generate-article-slug':
				
				if ($_POST['title'] != '')
				{
					$text = trim($_POST['title']);
					$text = sanitize_title($text);
					$text = urldecode($text); 

					exit($text);
				}
				
				exit();

			break;
			
			default: 
				exit();
			break;
		}
		
	break;

	case 'layout-settings': // settings_theme.php
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.';
			exit(json_encode(array('success' => false, 'msg' => pm_alert_error($ajax_msg))));
		}
		
		switch ($action)
		{
			case 'delete-logo':
				
				if ($config['custom_logo_url'] == '')
				{
					exit(json_encode(array('success' => false, 'msg' => '')));
				}
				$tmp_parts = explode('/', $config['custom_logo_url']);
				$filename = array_pop($tmp_parts);
				
				if (is_writeable( ABSPATH . _UPFOLDER ))
				{
					$filepath = ABSPATH . _UPFOLDER .'/'. $filename;
				}
				else
				{
					$filepath = _THUMBS_DIR_PATH . $filename;
				}
				if (file_exists($filepath))
				{
					unlink($filepath);
				}
				update_config('custom_logo_url','');
				
				echo json_encode(array('success' => true,
										'msg' => pm_alert_success('The logo was deleted.')
									  ));
				exit();
				
			break;
		}

	break;

	case 'settings': // settings.php
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.';
			
			echo json_encode(array('message' => pm_alert_error($ajax_msg)));
			exit();
		}
		
		switch ($action)
		{
			case 'testmail':
				
				extract($_POST);
				$alert_type = 'success';
				
				if (empty($mail_server) || empty($mail_port) || empty($mail_user) || empty($mail_pass) || empty($contact_email))
				{
					$ajax_msg = 'Please fill in all the required details.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => '')));
				}
			
				require_once(ABSPATH .'include/class.phpmailer.php');
			
			
				$mail = new PHPMailer();
				$mail->setLanguage('en', ABSPATH .'/include/phpmailer/language/');
			
				if ($mail_smtp == '1')
				{
					$mail->IsSMTP();
				}
			
				$mail->Subject = 'Test email from '. _SITENAME;
				$mail->Host 	= $mail_server;
				$mail->SMTPAuth = true;
				$mail->Port 	= $mail_port;
				$mail->Username = $mail_user;
				$mail->Password = $mail_pass;
				$mail->setFrom($contact_email, html_entity_decode(_SITENAME, ENT_QUOTES));
				$mail->CharSet = "UTF-8";
				$mail->AddAddress($contact_email);
				$mail->IsHTML(false);
			
				$mailcontent = "Hey!\n\nThis is a test mail sent from your site powered by PHP Melody.\nIf you've received this email you can rest assured. Your e-mail settings are OK and PHP Melody can send emails.\n\nYey! :)";
			
				$mail->Body = $mailcontent;
			
				if ( ! @$mail->send())
				{
					$ajax_msg = $mail->ErrorInfo;
					$alert_type = 'error';
				}
				else
				{
					$ajax_msg = 'Test mail delivered successfully to <strong>'. $contact_email .'</strong>.<br />Check your Inbox (and/or the spam box) for the confirmation email.<br />Remember to <strong>Save</strong> your settings if everything is fine.';
				}
			
				exit(json_encode(array(	'success' => false, 
										'alert_type' => $alert_type,
										'msg' => $ajax_msg, 
										'html' => '')));
			break;
			
			case 'test-fb-app':
				
				$fb_app_id = $_POST['oauth_fb_app_id'];
				$fb_app_secret = $_POST['oauth_fb_app_secret'];
				$ajax_msg = '';
				
				if (empty($fb_app_id) || empty($fb_app_secret))
				{
					$ajax_msg = "Please fill in both 'Facebook App ID' and 'Facebook App Secret' fields before running this check.";
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => '')));
				}
				
				include(ABSPATH . 'include/httpful/bootstrap.php');
				
				try {
					$fb_test_url = 'https://graph.facebook.com/'. $inputs['oauth_fb_app_id'];
					$response = \Httpful\Request::get($fb_test_url)->withoutStrictSSL()->send();
					$app_data = @json_decode($response->raw_body, true);
					
					if ( ! $app_data || array_key_exists('error', $app_data))
					{
						if (strpos(strtolower($app_data['error']['message']), 'unsupported get request') !== false)
						{
							$ajax_msg = '<strong>Facebook Error:</strong> '. $app_data['error']['message']; 
							$ajax_msg .= '<br /><br />';
							$ajax_msg .= '<strong>Tip:</strong> If your app is not in \'Public\' mode, visit the <a href="https://developers.facebook.com/apps/'. $fb_app_id .'/review-status/" target="_blank">App Review</a> page and make the necessary adjustments.';
						}
						else
						{
							$ajax_msg = '<strong>Facebook Error:</strong> '. (( ! $app_data) ? 'unknown error' : $app_data['error']['message']);
						}
					}
				} catch (Exception $e) {
					$ajax_msg = 'Error: '. $e->getMessage() ."\n<br />File: ". $e->getFile() ."\n<br />Line: ". $e->getLine();
				}
				
				if ($ajax_msg != '')
				{
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => '')));
				}
				
				$test_credentials_url = 'https://graph.facebook.com/oauth/access_token?client_id='. $fb_app_id .'&client_secret='. $fb_app_secret .'&type=client_cred';
				$response = \Httpful\Request::get($test_credentials_url)->withoutStrictSSL()->send();
				
				if (strpos($response->raw_body, 'access_token=') === false)
				{
					$app_data = @json_decode($response->raw_body, true);
					
					if ( ! $app_data || array_key_exists('error', $app_data))
					{
						// Error validating client secret
						// An unknown error has occurred
						$ajax_msg = '<strong>Facebook Error:</strong> '. (( ! $app_data) ? 'unknown error' : $app_data['error']['message']);
						exit(json_encode(array(	'success' => false, 
										'alert_type' => 'error',
										'msg' => $ajax_msg, 
										'html' => '')));
					}
				}
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'msg' => 'Facebook App Status: <strong>OK</strong>', 
										'html' => '')));
			break;
		}
		
	break;
	
	case 'utilities':
		
		switch ($action)
		{
			case 'sanitize-title':
				
				if ($_POST['text'] != '')
				{
					$text = trim($_POST['text']);
					$text = sanitize_title($text);
					exit($text);
				}
				
				exit();
				
			break;
		}

	break;
	
	case 'readlog':
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.';
			exit(json_encode(array('success' => false, 'msg' => pm_alert_error($ajax_msg))));
		}
			
		switch ($action)
		{
			case 'mark-all-read':
				if ( ! csrfguard_check_referer('_admin_readlog'))
				{
					exit(json_encode(array('success' => false, 'msg' => pm_alert_error('Invalid token or session expired. Please refresh this page and try again.'))));
				}
				
				if (mysql_query("UPDATE pm_log SET msg_type = '0'"))
				{
					update_config('unread_system_messages', 0);
					exit(json_encode(array('success' => true, 'msg' => '')));
				}
				else
				{
					exit(json_encode(array('success' => false, 'msg' => pm_alert_error('An error occurred while performing your request.<br /><strong>MySQL reported:</strong> '. mysql_error()))));
				}
				
			break;
			
			case 'delete-all':
				
				if ( ! csrfguard_check_referer('_admin_readlog'))
				{
					exit(json_encode(array('success' => false, 'msg' => pm_alert_error('Invalid token or session expired. Please refresh this page and try again.'))));
				}
				
				if (mysql_query("TRUNCATE TABLE pm_log"))
				{
					exit(json_encode(array('success' => true, 'msg' => '')));
				}
				else
				{
					exit(json_encode(array('success' => false, 'msg' => pm_alert_error('An error occurred while performing your request.<br /><strong>MySQL reported:</strong> '. mysql_error()))));
				}
						
			break;
		}
		
	break;
	
	case 'searchlog':
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.';
			exit(json_encode(array('success' => false, 'msg' => pm_alert_error($ajax_msg))));
		}
		
		switch ($action)
		{
			case 'delete-all':
				
				if ( ! csrfguard_check_referer('_admin_searchlog'))
				{
					exit(json_encode(array('success' => false, 'msg' => pm_alert_error('Invalid token or session expired. Please refresh this page and try again.'))));
				}
				
				if (mysql_query("TRUNCATE TABLE pm_searches"))
				{
					exit(json_encode(array('success' => true, 'msg' => '')));
				}
				else
				{
					exit(json_encode(array('success' => false, 'msg' => pm_alert_error('An error occurred while performing your request.<br /><strong>MySQL reported:</strong> '. mysql_error()))));
				}
				
			break;
		}
		
	break;
	
	
	case 'import-subscriptions':

		switch ($action)
		{
			case 'subscribe':
	
				if ( ! is_admin() && ! ( is_moderator() && mod_can('manage_videos')))
				{
					$ajax_msg = 'Sorry, you do not have access to this area.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => pm_alert_error($ajax_msg))));
				}
				
				$sub_name = trim($_POST['name']);
				$sub_type = $_POST['type'];
				$_POST['params'] = trim($_POST['params']);
				$sub_params_serialized = stripslashes($_POST['params']);
				$sub_params = unserialize($sub_params_serialized);
				
				if (empty($sub_name))
				{
					$sub_name = urldecode($_POST['keyword']) .' - '. date('F j, Y g:i A');
				}

				if (empty($sub_params_serialized))
				{
					$ajax_msg = 'Missing subscription parameters. Please reload the page and try again.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg)))); 
				}
				
//				if ( ! csrfguard_check_referer('_admin_import_subscriptions'))
//				{
//					$ajax_msg = 'Invalid token or session expired. Please refresh this page and try again.';
//					exit(json_encode(array(	'success' => false, 
//											'msg' => $ajax_msg,
//											'html' => pm_alert_error($ajax_msg)))); 
//				}
				
				$nonce = csrfguard_raw('_admin_import_subscriptions');

				if ($sub_type == 'user' || $sub_type == 'user-favorites' || $sub_type == 'user-playlist')
				{
					// "Save this user" is the desired behavior @since v2.3.1
					$sub_params['action'] = 'search';
					$sub_type = 'user';
					

					// Get user profile avatar
					switch ($sub_params['data_source'])
					{
						default:
						case 'youtube':
						case 'youtube-channel':

							if ( ! class_exists('PhpmelodyYoutube'))
							{
								include(ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/autoload.php');
							}

							$google_client = new Google_Client();
							$google_client->setDeveloperKey($config['youtube_api_key']);

							$youtube_api = new PhpmelodyYouTube($google_client);
							
							$args = array('pm-user-type' => ($sub_params['data_source'] == 'youtube') ? 'user' : 'channel');
							$avatar_url = $youtube_api->pm_get_user_avatar_url($sub_params['username'], $args);
							
							if (is_array($avatar_url) && $avatar_url['error'])
							{
								$ajax_msg = htmlentities($avatar_url['error']['message']);
								exit(json_encode(array(	'success' => false, 
														'alert_type' => 'error',
														'_pmnonce' => $nonce['_pmnonce'],
														'_pmnonce_t' => $nonce['_pmnonce_t'],
														'msg' => $ajax_msg,
														'html' =>  pm_alert_error($ajax_msg),
														'sub_id' => $sub_id)));
							}
		
							$sub_params['profile_avatar_url'] = $avatar_url;
							
						break;
						
						
						case 'dailymotion':
							
							include(ABSPATH . _ADMIN_FOLDER .'/src/dailymotion-sdk/autoload.php');
		
							$dailymotion_api = new PhpmelodyDailymotion();

							$sub_params['profile_avatar_url'] = $dailymotion_api->pm_get_user_avatar_url($sub_params['username']);

						break;
						
						case 'vimeo':
							
							include(ABSPATH . _ADMIN_FOLDER .'/src/vimeo-sdk/autoload.php');
							
							$vimeo_api = new PhpmelodyVimeo(null, null, $config['vimeo_api_token']);
							
							$sub_params['profile_avatar_url'] = $vimeo_api->pm_get_user_avatar_url($sub_params['username']);
							
						break;
					}
					
					$sub_params_serialized = serialize($sub_params);
				}

				$sql = "INSERT INTO pm_import_subscriptions (sub_name, sub_type, last_query_time, last_query_results, user_id, data) 
							 VALUES ('". secure_sql($sub_name) ."',
									'". $sub_type ."',
									 0,
									 0,
									 ". $userdata['id'] .",
									 '". secure_sql($sub_params_serialized) ."'
									)";

				if ( ! ($result = mysql_query($sql)))
				{
					$ajax_msg = 'An error occurred while performing your request.<br /><strong>MySQL reported:</strong> '. mysql_error();
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}

				$ajax_msg = 'Subscribed'; 
				exit(json_encode(array(	'success' => true,
										'alert_type' => 'success',
										'_pmnonce' => $nonce['_pmnonce'],
										'_pmnonce_t' => $nonce['_pmnonce_t'], 
										'msg' => $ajax_msg, 
										'html' => pm_alert_success($ajax_msg),
										'sub_id' => mysql_insert_id())));
			break;
			
			case 'unsubscribe':
				
				include(ABSPATH .'include/cron_functions.php');
				
				if ( ! is_admin() && ! ( is_moderator() && mod_can('manage_videos')))
				{
					$ajax_msg = 'Sorry, you do not have access to this area.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => pm_alert_error($ajax_msg))));	
				}
				
				$sub_id = (int) $_POST['sub-id'];
				
				if ( ! $sub_id)
				{
					$ajax_msg = 'Invalid subscription ID provided.'; 
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
//				if ( ! csrfguard_check_referer('_admin_import_subscriptions'))
//				{
//					$ajax_msg = 'Invalid token or session expired. Please refresh this page and try again.';
//					exit(json_encode(array(	'success' => false, 
//											'msg' => $ajax_msg,
//											'html' => pm_alert_error($ajax_msg)))); 
//				}				
				
				$nonce = csrfguard_raw('_admin_import_subscriptions');
				
				if (is_moderator())
				{
					$sql = "SELECT user_id 
							FROM pm_import_subscriptions 
							WHERE sub_id = $sub_id";
				
					if ( ! $result = mysql_query($sql))
					{
						$ajax_msg = 'An error occurred while performing your request.<br /><strong>MySQL reported:</strong> '. mysql_error();
						exit(json_encode(array(	'success' => false, 
												'alert_type' => 'error',
												'_pmnonce' => $nonce['_pmnonce'],
												'_pmnonce_t' => $nonce['_pmnonce_t'],
												'msg' => $ajax_msg, 
												'html' => pm_alert_error($ajax_msg))));
					}
					
					$sub = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ((int) $userdata['id'] != (int) $sub['user_id'])
					{
						$ajax_msg = 'You can manage your own subscriptions only.';
						exit(json_encode(array(	'success' => false, 
												'alert_type' => 'error',
												'_pmnonce' => $nonce['_pmnonce'],
												'_pmnonce_t' => $nonce['_pmnonce_t'],
												'msg' => $ajax_msg,
												'html' => pm_alert_error($ajax_msg)))); 
					}
				}
				
				$sql = "DELETE FROM pm_import_subscriptions 
						WHERE sub_id = $sub_id";
				if ( ! mysql_query($sql))
				{
					$ajax_msg = 'An error occurred while performing your request.<br /><strong>MySQL reported:</strong> '. mysql_error();
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				if ($job_id = check_cron_job_exists($sub_id, 'import'))
				{
					delete_cron_job($job_id);
				}
				
				$ajax_msg = 'Unsubscribed';
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'_pmnonce' => $nonce['_pmnonce'],
										'_pmnonce_t' => $nonce['_pmnonce_t'],
										'msg' => $ajax_msg,
										'html' => pm_alert_success($ajax_msg)))); 

			break;
			
			case 'get-results':
					
				$sub_id = (int) $_GET['sub-id'];

				if ( ! $sub_id)
				{
					$ajax_msg = 'Invalid subscription ID provided.'; 
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				if ( ! is_admin() && ! ( is_moderator() && mod_can('manage_videos')))
				{
					$ajax_msg = 'Sorry, you do not have access to this area.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg, 
											'html' => pm_alert_error($ajax_msg),
											'sub_id' => $sub_id)));	
				}
				
				$sql = "SELECT sub_type, last_query_time, last_query_results, data 
						FROM pm_import_subscriptions 
						WHERE sub_id = $sub_id ";
				if ( ! ($result = mysql_query($sql)))
				{
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => 'MySQL error: '. mysql_error(), 
											'html' => pm_alert_error('An error occurred while performing your request.<br /><strong>MySQL reported:</strong> '. mysql_error()),
											'sub_id' => $sub_id)));
				}
				
				$sub = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				if (import_subscription_cache_fresh($sub['last_query_time']))
				{
					$ajax_msg = ($sub['last_query_results'] == 0) ? 'None' : number_format($sub['last_query_results']);
					exit(json_encode(array(	'success' => true, 
											'alert_type' => '',
											'msg' => $ajax_msg,
											'html' => $ajax_msg,
											'sub_id' => $sub_id)));
				}
				
				$query_params = unserialize($sub['data']);

				switch ($query_params['data_source'])
				{
					default:
					case 'youtube':
					case 'youtube-channel':
						
						if ( ! class_exists('PhpmelodyYouTube'))
						{
							include(ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/autoload.php');
						}

						$google_client = new Google_Client();
						$google_client->setDeveloperKey($config['youtube_api_key']);

						$youtube_api = new PhpmelodyYouTube($google_client);

						$query_params['search_orderby'] = ($query_params['search_orderby'] == 'published') ? 'date' : $query_params['search_orderby'];
						$query_params['per_page'] = $query_params['results'];

						switch ($sub['sub_type'])
						{
							default:
							case 'search':

								if ($query_params['action'] == 'search-popular' || $query_params['keyword'] == 'popular') // @since v2.3.1
								{
									$api_data = $youtube_api->pm_most_popular($query_params);
								}
								else
								{
									$query_params['search_time'] = 'this_week';
									$query_params['per_page'] = 0;
									$api_data = $youtube_api->pm_search($query_params['keyword'], $query_params, true);
								}

							break;
							
							// do the same for all @since v2.3.1
							case 'user':
							case 'user-favorites':
							case 'user-playlist':
								
								$args = array('pm-user-type' => ($query_params['data_source'] == 'youtube') ? 'user' : 'channel');
								$playlists = $youtube_api->pm_user_playlists($query_params['username'], $args);
								$api_data = $youtube_api->pm_playlist($youtube_api->pm_uploads_playlist_id, array('per_page' => 50), false);

							break;
						}
				

						
						if ($api_data['error']['message'] != '')
						{
							$ajax_msg = $api_data['error']['message'];
							exit(json_encode(array(	'success' => false, 
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' =>  pm_alert_error($ajax_msg),
													'sub_id' => $sub_id)));
						}

						$total_search_results = 0;
						$data = json_decode($data, true);
				
						if ($sub['sub_type'] == 'search')
						{
							$total_search_results = $api_data['meta']['total_results'];
						}
						else //if ($sub['sub_type'] == 'user' || $sub['sub_type'] == 'user-favorites' || $sub['sub_type'] == 'user-playlist')
						{
							$last_7_days = $time_now - (86400 * 7);

							if (count($api_data['results']) > 0)
							foreach ($api_data['results'] as $k => $item)
							{
								if ($item['publish_date_timestamp'] >= $last_7_days)
								{
									$total_search_results++;
								}
							}
						}
						
					break;
					
					case 'dailymotion':
					
							$query_params['search_time'] = ($query_params['search_time'] != 'this_week') ? 'this_week' : '';
							
							include(ABSPATH . _ADMIN_FOLDER .'/src/dailymotion-sdk/autoload.php');
							$dailymotion_api = new PhpmelodyDailymotion();
							
							try {
								$args = array('page' => 1,
											  'per_page' => (int) $query_params['results']
											);
								$args = array_merge($args, $query_params);
								
								switch ($sub['sub_type'])
								{
									default:
									case 'search':
										
										$api_data = $dailymotion_api->pm_search($query_params['keyword'], $args);
										
									break;
									case 'user':
									
										$api_data = $dailymotion_api->pm_user_videos($query_params['username'], $args);
										
									break;
									
									case 'user-favorites':
										
										$api_data = $dailymotion_api->pm_user_favorites($query_params['username'], $args);
										
									break;
									
									case 'user-playlist':
										
										$api_data = $dailymotion_api->pm_playlist($query_params['playlistid'], $args);
										
									break;
								}
								
								$total_search_results = (int) $api_data['meta']['total_results'];

							} catch(DailymotionApiException $e) {
								
								if ($dailymotion_api->error)
								{
									$api_data['error']['message'] = '<strong>Dailymotion API error '. $dailymotion_api->error->code . ':</strong> '. $dailymotion_api->error->message;
								}
								else
								{
									$api_data['error']['message'] = '<strong>Dailymotion API error:</strong> '. $e->__toString();
								}
								
								exit(json_encode(array(	'success' => false, 
														'alert_type' => 'error',
														'msg' => $api_data['error']['message'], 
														'html' => pm_alert_error($api_data['error']['message']),
														'sub_id' => $sub_id)));
								
							}
								
					break;
					
					case 'vimeo':
						
						/*
						 * Note: Vimeo API doesn't support time-based search for user's videos.
						 */
							
						$query_params['search_time'] = ($query_params['search_time'] != 'this_week') ? 'this_week' : '';
						
						include(ABSPATH . _ADMIN_FOLDER .'/src/vimeo-sdk/autoload.php');
						
						$vimeo_api = new PhpmelodyVimeo(null, null, $config['vimeo_api_token']);

						$args = array('page' => 1,
									  'per_page' => (int) $query_params['results']
									);
						$args = array_merge($args, $query_params);
						
						switch ($sub['sub_type'])
						{
							default:
							case 'search':
								
								$api_data = $vimeo_api->pm_search($query_params['keyword'], $args);
								
							break;
							case 'user':

								$api_data = $vimeo_api->pm_user_videos($query_params['username'], $args);
								
							break;
							
							case 'user-favorites':
								
								$api_data = $vimeo_api->pm_user_favorites($query_params['username'], $args);
								
							break;
							
							case 'user-playlist':

								$api_data = $vimeo_api->pm_playlist($query_params['playlistid'], $args);
								
							break;
						}
						
						if (array_key_exists('error', $api_data))
						{
							exit(json_encode(array(	'success' => false, 
													'alert_type' => 'error',
													'msg' => $api_data['error']['message'], 
													'html' => pm_alert_error($api_data['error']['message']),
													'sub_id' => $sub_id)));
						}
						
						$total_search_results = (int) $api_data['meta']['total_results'];
						
					break;
				} // end switch ($sub['data_source'])
				
				// cache results
				$sql = 'UPDATE pm_import_subscriptions 
						SET last_query_time = '. $time_now .', 
							last_query_results = '. $total_search_results .' 
						WHERE sub_id = '. $sub_id;
				@mysql_query($sql); 

				$ajax_msg = ($total_search_results == 0) ? 'None' : number_format($total_search_results);
				exit(json_encode(array(	'success' => true, 
										'alert_type' => '',
										'msg' => $ajax_msg,
										'html' => $ajax_msg,
										'sub_id' => $sub_id)));
			break;
		}
	break;
	
	case 'import':
		
		if ( ! is_admin() && ! ( is_moderator() && mod_can('manage_videos')) && ! doing_cron())
		{
			$ajax_msg = 'Sorry, you do not have access to this area.';
			exit(json_encode(array(	'success' => false,
									'alert_type' => 'error',
									'msg' => $ajax_msg,
									'html' => pm_alert_error($ajax_msg, null, true)
								   )));
		}
		
		@set_time_limit(200);
				
		$sources = a_fetch_video_sources();
		$data_source = 'youtube';
		
		if (in_array($_POST['data_source'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo', 'csv')))
		{
			setcookie('aa_import_from', $_POST['data_source'], time()+(COOKIE_TIME * 100), COOKIE_PATH);
		}
		
		if (in_array($_COOKIE['aa_import_from'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo', 'csv')))
		{
			$data_source = $_COOKIE['aa_import_from'];
		}
		
		if ($_GET['data_source'] != '' || $_POST['data_source'] != '')
		{
			$data_source = ($_GET['data_source'] != '') ? $_GET['data_source'] : $_POST['data_source'];
			$data_source = ($data_source == 'youtube-channel') ? 'youtube' : $data_source;
		}
		
		$sub_id = (int) $_POST['sub_id'];

		switch ($action)
		{
			case 'csv-get-videos':
			case 'search':
				
				if (empty($_POST['keyword']) && $action != 'csv-get-videos')
				{
					$ajax_msg = 'Please enter your keywords first.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg, null, true)
										  )));
				}
				
				$import_page = (int) $_POST['page'];

				if(empty($import_page))
					$import_page = 1;
				
				$autodata = 0;
				$autofilling = 0;
				$overwrite_category = array();
				
				if ( ! empty($_POST['keyword']))
				{
					$v				= trim($_POST['keyword']);
					$import_results	= ((int) $_POST['results'] > 0) ? (int) $_POST['results'] : 20;
					
					if($_POST['autofilling'] == '1') 
					{
						$autofill = $_POST['keyword'];
						$autofilling = 1;
					}
					if($_POST['autodata'] == '1')
					{
						$autodata = 1;
					}
					if (is_array($_POST['use_this_category']))
					{
						$overwrite_category = $_POST['use_this_category'];
					}
					if ($_POST['oc'] == 1 || $_POST['utc'] != '')
					{
						$overwrite_category = (array) explode(',', $_POST['utc']);	//	utc = use_this_cateogory
					}
				}
				elseif($_GET['keyword'] != '')
				{
					$v				= urldecode($_GET['keyword']);
					
					if($_GET['results'] != '')
					{
						$import_results	= (int) $_GET['results'];
					}
					else
					{
						$import_results = 20;
					}
					
					if($_GET['autofilling'] == 1)
					{
						$autofill = urldecode($_GET['keyword']);
						$autofilling = 1;
					}
					if($_GET['autodata'] == 1)
					{
						$autodata = 1;
					}
					if($_GET['oc'] == 1)	//	oc = overwrite_category
					{
						$overwrite_category = (array) explode(',', $_GET['utc']);	//	utc = use_this_cateogory
					}
				}
				
				$search_in_category = ($_GET['search_category'] != '') ? trim($_GET['search_category']) : $_POST['search_category'];
				$search_orderby = ($_GET['search_orderby'] != '') ? $_GET['search_orderby'] : $_POST['search_orderby'];
				$search_duration = ($_GET['search_duration'] != '') ? $_GET['search_duration'] : $_POST['search_duration'];
				$search_language = ($_GET['search_language'] != '') ? $_GET['search_language'] : $_POST['search_language'];
				$search_time = ($_GET['search_time'] != '') ? $_GET['search_time'] : $_POST['search_time'];
				$search_license = ($_GET['search_license'] != '') ? $_GET['search_license'] : $_POST['search_license'];
				$search_hd = ($_GET['search_hd'] == 'true' || $_POST['search_hd'] == 'true') ? true : false;
				$search_3d = ($_GET['search_3d'] == 'true' || $_POST['search_3d'] == 'true') ? true : false;
				$search_region = ($_GET['search_region'] != '') ? $_GET['search_region'] : $_POST['search_region'];
				
			
				$start_from = ($data_source == 'youtube') ? 0 : ($import_page * $import_results) - $import_results + 1;
				
				$search_term = str_replace("- ", " ", $v);
				
				$api_data = array();
				switch ($data_source)
				{
					case 'youtube':
			
						if ( ! empty($config['youtube_api_key']))
						{
							include(ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/autoload.php');
			
							$google_client = new Google_Client();
							$google_client->setDeveloperKey($config['youtube_api_key']);
			
							$youtube_api = new PhpmelodyYouTube($google_client);
							
							$args = array('per_page' => (int) $import_results);
							$args = array_merge($args, $_POST, $_GET);
							
							if ($search_term == 'popular')
							{
								$api_data = $youtube_api->pm_most_popular($args);
							}
							else
							{
								$api_data = $youtube_api->pm_search($search_term, $args);
							}
						}
						else
						{
							$api_data = array('error' => array('message' =>
								'To access the Youtube API an <em>API Key</em> is required. <br />
								For step-by-step instructions on how to create your API key, please <strong><a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank">watch this video</a></strong>.'));
						}
			
					break; 
					
					case 'dailymotion':
						
						include(ABSPATH . _ADMIN_FOLDER .'/src/dailymotion-sdk/autoload.php');
					
						$dailymotion_api = new PhpmelodyDailymotion();
			
						try {
							$args = array('page' => $import_page,
										  'per_page' => (int) $import_results,
										);
							$args = array_merge($args, $_POST, $_GET);
							
							$api_data = $dailymotion_api->pm_search($search_term, $args);
			  
						} catch(DailymotionApiException $e) {
			
							if ($dailymotion_api->error)
							{
								$api_data['error']['message'] = '<strong>Dailymotion API error '. $dailymotion_api->error->code . ':</strong> '. $dailymotion_api->error->message;
							}
							else
							{
								$api_data['error']['message'] = '<strong>Dailymotion API error:</strong> '. $e->__toString();
							}
						}
			
					break;
					
					case 'vimeo':
			
						if ( ! empty($config['vimeo_api_token']))
						{
							include(ABSPATH . _ADMIN_FOLDER .'/src/vimeo-sdk/autoload.php');
							
							$vimeo_api = new PhpmelodyVimeo(null, null, $config['vimeo_api_token']);
							
							$args = array('page' => $import_page,
										  'per_page' => (int) $import_results,
										);
							$args = array_merge($args, $_POST, $_GET);
							
							$api_data = $vimeo_api->pm_search($search_term, $args);
			
						}
						else
						{
							$api_data = array('error' => array('message' =>
								'Vimeo API requires a <em>Access Token</em> to retrieve data. This is how you can get an API key:
								<br /><br />
								<ol>
									<li><a href="https://developer.vimeo.com/apps" target="_blank" title="Vimeo Developer API">Create</a> your Vimeo developer account to generate your token.</li>
									<li>Enter the generated token in the <a href="settings.php?highlight=vimeo_api_token&view=video">Settings</a> page.</li>
								</ol>'));
						}
						
					break;
					
					case 'csv':
						
						$file_id = (int) $_POST['file_id'];
						$autodata = 1;
						$autofilling = 1;
						$overwrite_category = array();
						$import_results	= ((int) $_POST['results'] > 0) ? (int) $_POST['results'] : 50;
						
						if (is_array($_POST['use_this_category']))
						{
							$overwrite_category = $_POST['use_this_category'];
						}
						
						if ($_POST['oc'] == 1 || $_POST['utc'] != '')
						{
							$overwrite_category = (array) explode(',', $_POST['utc']);	//	utc = use_this_cateogory
						}
				
						if ( ! $file_id)
						{
							$ajax_msg = 'Missing file ID';
							exit(json_encode(array( 'success' => false,
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' => pm_alert_error($ajax_msg, false, true)
												  )));
						}
						
						$sql = "SELECT * 
								FROM pm_import_csv_files 
								WHERE file_id = $file_id"; 
						
						if ( ! $result = mysql_query($sql))
						{
							$ajax_msg = 'An error occurred while retrieving file data. <br /><strong>MySQL Error</strong>: '. mysql_error() .'<br />'.$sql;
							
							exit(json_encode(array( 'success' => false,
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' => pm_alert_error($ajax_msg, false, true)
												  )));
						}
						
						$csv_file = mysql_fetch_assoc($result);
						mysql_free_result($result);
						
						$start_from = ($import_page * $import_results) - $import_results;
						
						$api_data = array('meta' => array('total_results' => (int) $csv_file['items_processed'],
														  'page' => $import_page,
														  'prev_page' => $import_page - 1,
														  'next_page' => ($start_from + $import_results >= $csv_file['items_processed']) ? null : $import_page + 1,
														  'start' => $start_from,
														  'per_page' => $import_results
														),
										  'results' => array()
										  );
						
						$sql = "SELECT * 
								FROM pm_import_csv_items
								WHERE file_id = $file_id
								  AND processed = '1' 
								ORDER BY item_id ASC 
								LIMIT $start_from, $import_results";
								
						if ( ! $result = mysql_query($sql))
						{
							$ajax_msg = 'An error occurred while retrieving file data. <br /><strong>MySQL Error</strong>: '. mysql_error() .'<br />'.$sql; 
							
							exit(json_encode(array( 'success' => false,
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' => pm_alert_error($ajax_msg, false, true)
												  )));
						}
						
						while ($row = mysql_fetch_assoc($result))
						{
							// format array for display
							$row['yt_id'] = ($row['yt_id'] == '') ? generate_activation_key(9) : $row['yt_id']; 
							$row['id'] = $row['yt_id'];
							$row['title'] = $row['video_title'];
							$row['total_thumbs'] = 1;
							$row['thumbs'][0] = array('original' => $row['yt_thumb'],
													  'small' => $row['yt_thumb'],
													  'medium' => $row['yt_thumb'],
													  'large' => $row['yt_thumb'],
													  'extra-large' => $row['yt_thumb']
													);
							$row['keywords'] = $row['tags'];
							
							$geo_restriction = unserialize($row['geo-restriction']);
							if (count($geo_restriction) == 0 || ! is_array($geo_restriction)) // allowed everywhere
							{
								$row['geo-restriction'] = null; 
							}
							else if ($geo_restriction[0] == 'deny') // denied to
							{
								unset($geo_restriction[0]);
								$row['geo-restriction'] = array('type' => 'deny', 'list' => implode(',', $geo_restriction));
							}
							else // allowed only
							{
								if ($geo_restriction[0] == 'allow')
								{
									unset($geo_restriction[0]);
								}
								
								if (count($geo_restriction) > 0)
								{
									$row['geo-restriction'] = array('type' => 'allow', 'list' => implode(',', $geo_restriction));
								}
								else
								{
									$row['geo-restriction'] = null; 
								}
							}
							
							// embed_url
							switch ($row['source_id'])
							{
								default:
									
									$row['embed_url'] = '#';
								
								break;
								
								case $sources['youtube']['source_id']:
								
									$row['embed_url'] = '//www.youtube.com/v/'. $row['yt_id'] .'?&autoplay=1&v='. $row['yt_id'] .'&version=3';
								
								break;
								
								case $sources['vimeo']['source_id']:
								
									$row['embed_url'] = '//player.vimeo.com/video/'. $row['yt_id'];
								
								break;
								
								case $sources['dailymotion']['source_id']:
									
									$row['embed_url'] = '//www.dailymotion.com/embed/video/'. $row['yt_id'];
									
								break;
							}
							
							$row['embeddable'] = (int) $row['embeddable'];
							$row['private'] = (int) $row['private'];
							$row['duration'] = (int) $row['yt_length'];
							$row['has_errors'] = (int) $row['has_errors'];
							$row['errors'] = ($row['errors'] != '') ? unserialize($row['errors']) : '';
							$row['url'] = $row['direct'];
							
							$api_data['results'][] = $row;
						}
						mysql_free_result($result);
						
						if ($csv_file['items_detected'] == $csv_file['items_imported'])
						{
							$total_search_results = 0;
							$api_data['meta']['total_results'] = 0;
						}
						
					break; 
				}
				
				if (array_key_exists('error', $api_data))
				{
					$ajax_msg = '<strong>Unable to retrieve requested data.</strong>
								 <br />
								 <br />';
					$ajax_msg .= $api_data['error']['message'];
					if ( ! function_exists('curl_init') && ! ini_get('allow_url_fopen'))
					{
						$ajax_msg .= '<br />Your system doesn\'t support remote connections.
									  <br /> 
									  Ask your hosting provider to enable either <strong>allow_url_fopen</strong> or the <strong>cURL extension</strong>.';
					}
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg, null, true)
										  )));
				}
				
				// begin formatting
				$alt = 0;
				$total_results = count($api_data['results']);
				$counter = 0;
				$duplicates = 0;
				$total_search_results = $api_data['meta']['total_results'];
			
				if ($total_results > 0)
				{
					$import_results_html = '';
					$import_results_array = array();
					
					ob_start();
					
					foreach ($api_data['results'] as $i => $item)
					{
						// Check if we already have this video
						$count_vids = (int) count_entries('pm_videos', 'yt_id', $item['id'] ."' AND source_id = '". $sources[$data_source]['source_id']);
						$count_vids += (int) count_entries('pm_videos_trash', 'yt_id', $item['id'] ."' AND source_id = '". $sources[$data_source]['source_id']);
						
						if ($count_vids == 0)
						{
							$col = ($alt % 2) ? 'table_row1' : 'table_row2';
							$alt++;	
							
							
							$col_unembed = '';
							
							if ( ! $item['embeddable'] || $item['private'])
							{
								$col_unembed = 'table_row_unembed';
							}
						
							if (is_array($item['geo-restriction']))
							{
								$col_unembed = 'table_row_unembed';
								$georestriction = 'This video is ';
								$georestriction .=  ($item['geo-restriction']['type'] == 'deny') ? 'geo-restricted' : 'available only'; 
								$georestriction .= ' in the following countries: '. $item['geo-restriction']['list'];
							}
							
							$counter = $item['id'];
							
							if ( ! doing_cron())
							{
								include(ABSPATH . _ADMIN_FOLDER .'/import-item-template.php');
							}
							else
							{
								if ($item['embeddable'] && ! $item['private'])
								{
									$import_results_array[] = $item;
								}
							}
						}
						else
						{
							$duplicates++;
						}
					}	//	end for()
					
					$import_results_html .= ob_get_clean();
				}	//	end if()
				else
				{
					if ($data_source == 'csv')
					{
						$ajax_msg = 'Nothing else to import. Everything was imported into your database.';
					}
					else
					{
						$ajax_msg = 'Your search did not return any results. Try using different keywords or options.';
					}
					exit(json_encode(array(	'success' => true,
											'alert_type' => 'info',
											'total_results' => $total_results,
											'total_search_results' => $total_search_results, 
											'duplicates' => $duplicates,
											'msg' => $ajax_msg,
											'html' => pm_alert_info($ajax_msg, null, true),
											'sub_id' => $sub_id,
											'items' => (doing_cron()) ? $import_results_array : null
										   )));
				}
				
				$sub_name = '';
				$sub_params = array();
				
				if ( ! $sub_id && ! doing_cron())
				{
					$query_params = array(	'action' => 'search',
											'keyword' => $search_term,
											'results' => $import_results,
											'page' => $import_page,
											'autofilling' => $autofilling,
											'autodata' => $autodata,
											);
				
					if (count($overwrite_category) > 0)
					{
						$query_params['oc'] = 1;
						$query_params['utc'] = implode(',', $overwrite_category);
					}
					else
					{
						$query_params['oc'] = 0;
						$query_params['utc'] = '';
					}
					
					if ($search_in_category != '' && $search_in_category != 'all')
					{
						$query_params['search_category'] = $search_in_category;
					}
					
					if (in_array($search_orderby, array('relevance', 'date', 'published', 'viewCount', 'rating')))
					{
						$query_params['search_orderby'] = $search_orderby;
					}
					
					if (in_array($search_duration, array('short', 'medium', 'long')))
					{
						$query_params['search_duration'] = $search_duration;
					}
					
					if ($search_language != '' && $search_language != 'all')
					{
						$query_params['search_language'] = $search_language;
					}
					
					if (in_array($search_time, array('today', 'this_week', 'this_month'/*, 'all_time'*/)))
					{
						$query_params['search_time'] = $search_time;
					}
					
					if ($search_license != '' && $search_license != 'all')
					{
						$query_params['search_license'] = $search_license;
					}
					
					if ($search_hd)
					{
						$query_params['search_hd'] = 'true';
					}
					
					if ($search_3d)
					{
						$query_params['search_3d'] = 'true';
					}
					
					if ($_GET['sub_id'] != '')
					{
						$query_params['sub_id'] = (int) $_GET['sub_id'];
					}
					
					$query_params['data_source'] = $data_source;
					
//					$sub_params['data_source'] = $data_source;
					$sub_name = $query_params['keyword'];
					$sub_name .= ($search_in_category != '' && $search_in_category != 'all') ? ', '. $search_in_category : '';
					$sub_name .= (in_array($search_time, array('today', 'this_week', 'this_month'/*, 'all_time'*/))) ? ', '. str_replace('_', ' ', ucfirst($search_time)) : '';
					$sub_name .= (in_array($search_duration, array('short', 'medium', 'long'))) ? ', '. $search_duration : '';
					$sub_name .= ($search_hd) ? ', HD' : '';
					$sub_name .= ($search_3d) ? ', 3D' : '';
					
					$sub_params = serialize($query_params);
					
					// if this is the first search, try to find an existing subscription
//					if (($import_page == 1 || empty($import_page)) && empty($_POST['page']))
//					{
						$sql = "SELECT sub_id 
								FROM pm_import_subscriptions 
								WHERE sub_type = 'search'
								  AND user_id = ". $userdata['id'] ."
								  AND data LIKE '%". secure_sql($search_term) ."%". $data_source ."%'";
						if ($result = mysql_query($sql))
						{
							if (mysql_num_rows($result) == 1)
							{
								$row = mysql_fetch_assoc($result);
								$sub_id = (int) $row['sub_id'];
							}
							mysql_free_result($result);
						}
//					}
				}
				else
				{
					$sql = "SELECT user_id, sub_name, sub_type, data  
							FROM pm_import_subscriptions 
							WHERE sub_id = $sub_id";
					if ($result = mysql_query($sql))
					{
						$sub = mysql_fetch_assoc($result);
						mysql_free_result($result);
						
						$sub_name = $sub['sub_name'];
						$sub_params = $sub['data'];
					}
				}
				
				$sub_nonce = csrfguard_raw('_admin_import_subscriptions');
				
				// all videos found
				if ($duplicates == $total_results && $duplicates > 0)
				{
					if ($duplicates == $total_search_results) 
					{
						$ajax_msg = "You imported all the videos based on this particular search. Try searching for different terms.";
					}
					else 
					{
						$ajax_msg = "Looks like you already imported most of the videos based on this particular search. \n Press 'Load more' to list more videos.";
					}
					
					exit(json_encode(array(	'success' => true,
											'alert_type' => 'info',
											'next_page' => ($data_source == 'youtube' || $data_source == 'youtube-channel' || $data_source == 'csv') ? $api_data['meta']['next_page'] : ++$import_page,
											'total_results' => $total_results,
											'total_search_results' => $total_search_results,
											'duplicates' => $duplicates,
											'sub' => array( 'name' => $sub_name,
														'params' => $sub_params,
														'type' => 'search',
														'_pmnonce' => $sub_nonce['_pmnonce'],
														'_pmnonce_t' => $sub_nonce['_pmnonce_t']
													  ),
											'msg' => $ajax_msg,
											'html' => pm_alert_info($ajax_msg, null, true),
											'sub_id' => $sub_id, 
											'items' => (doing_cron()) ? $import_results_array : null
										   )));
				}
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => '',
										'next_page' => ($data_source == 'youtube' || $data_source == 'youtube-channel' || $data_source == 'csv') ? $api_data['meta']['next_page'] : ++$import_page,
										'total_results' => $total_results,
										'total_search_results' => $total_search_results,
										'duplicates' => $duplicates,
										'sub' => array( 'name' => $sub_name,
														'params' => $sub_params,
														'type' => 'search',
														'_pmnonce' => $sub_nonce['_pmnonce'],
														'_pmnonce_t' => $sub_nonce['_pmnonce_t']
													  ),
										'msg' => '',
										'html' => $import_results_html,
										'sub_id' => $sub_id,
										'items' => (doing_cron()) ? $import_results_array : null 
										)));

			break; // import -> search
			
			case 'search-user':
	
				$username = trim($_POST['username']);
				$import_page = (empty($_POST['page'])) ? 1 : (int) $_POST['page'];
				$import_action = $_POST['action'];
				$import_results = $_POST['results'];
				$autofilling = ($_POST['autofilling'] == '1') ? 1 : 0;
				$autodata = ($_POST['autodata'] == '1') ? 1 : 0;
				$overwrite_category = array();
				
				if (is_array($_POST['use_this_category']))
				{
					$overwrite_category = $_POST['use_this_category'];
				}
				
				if ($_POST['oc'] == 1)	//	oc = overwrite_category
				{
					$overwrite_category = (array) explode(',', $_POST['utc']);	//	utc = use_this_cateogory
				}
				
				$data_source = 'youtube';
				
				if (in_array($_COOKIE['aa_import_from'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo')))
				{
					$data_source = $_COOKIE['aa_import_from'];
				}
				
				if ($_GET['data_source'] != '' || $_POST['data_source'] != '')
				{
					$data_source = ($_GET['data_source'] != '') ? $_GET['data_source'] : $_POST['data_source'];
				}
				
				if ($username == '' || stripos($username, 'enter username') !== false)
				{
					$ajax_msg = 'Please enter a valid username or channel ID first.';
					exit(json_encode(array(	'success' => false,
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg, null, true)
										  )));
				}
				
				if (detect_russian($username)) 
				{
					$ajax_msg = 'Unfortunately the Youtube Search API does not support usernames containing cyrillic characters. To import videos from this user, follow these simple steps: <a href="http://help.phpmelody.com/how-to-import-from-youtube-com-users-with-russian-characters/" target="_blank">http://help.phpmelody.com/how-to-import-from-youtube-com-users-with-russian-characters/</a>';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg, null, true)
										  )));
				}
				
				$username_display = $username;
				
				switch ($import_action)
				{
					default:
					case 'search':
					case 'playlists':
					case 'favorites':
						
						// Get user videos
						switch ($data_source)
						{
							case 'youtube':
							case 'youtube-channel':
								
								include(ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/autoload.php');
								
								$google_client = new Google_Client();
								$google_client->setDeveloperKey($config['youtube_api_key']);
								
								$youtube_api = new PhpmelodyYouTube($google_client);
								
								$args = array('pm-user-type' => ($data_source == 'youtube') ? 'user' : 'channel');
								$playlists = $youtube_api->pm_user_playlists($username, $args);
								
								$username_display = ($data_source == 'youtube') ? $username_display : $youtube_api->pm_channel_title;
								
								if (array_key_exists('error', $playlists))
								{
									$ajax_msg = '<strong>Unable to retrieve requested data.</strong>
												 <br />
												 <br />';
									$ajax_msg .= $playlists['error']['message'];
									if ( ! function_exists('curl_init') && ! ini_get('allow_url_fopen'))
									{
										$ajax_msg .= '<br />Your system doesn\'t support remote connections.
													  <br /> 
													  Ask your hosting provider to enable either <strong>allow_url_fopen</strong> or the <strong>cURL extension</strong>.';
									}
									exit(json_encode(array(	'success' => false, 
															'alert_type' => 'error',
															'msg' => $ajax_msg,
															'html' => pm_alert_error($ajax_msg, null, true),
														  )));
								}
								
								$args = array('page' => (isset($_POST['page'])) ? $_POST['page'] : null,
											  'per_page' => $import_results
											);
								
								switch($import_action)
								{
									case 'search':
									
										$api_data = $youtube_api->pm_user_videos($username, $args);
										
									break;
									
									case 'favorites':
										
										$api_data = $youtube_api->pm_user_favorites($username, $args);
									
									break;
									
									case 'playlists':
									
										$api_data = $youtube_api->pm_playlist($_POST['playlistid'], $args);
									
									break;
								}
					
							break;
							
							case 'dailymotion':
								
								include(ABSPATH . _ADMIN_FOLDER .'/src/dailymotion-sdk/autoload.php');
								
								$dailymotion_api = new PhpmelodyDailymotion();
								
								try {
									$args = array('page' => $import_page,
												  'per_page' => (int) $import_results,
												);
								
									switch($import_action)
									{
										case 'search':
											
											$api_data = $dailymotion_api->pm_user_videos($username, $args);
											
										break;
										
										case 'favorites':
											
											$api_data = $dailymotion_api->pm_user_favorites($username, $args);
											
										break; 
										
										case 'playlists':
											
											$api_data = $dailymotion_api->pm_playlist($_POST['playlistid'], $args);
											
										break;
									}
									
								} catch(DailymotionApiException $e) {
					
									if ($dailymotion_api->error)
									{
										$api_data['error']['message'] = '<strong>Dailymotion API error '. $dailymotion_api->error->code . ':</strong> '. $dailymotion_api->error->message;
									}
									else
									{
										$api_data['error']['message'] = '<strong>Dailymotion API error:</strong> '. $e->__toString();
									}
								}
								
								
							break;
							
							case 'vimeo':
								
								include(ABSPATH . _ADMIN_FOLDER .'/src/vimeo-sdk/autoload.php');
								
								$vimeo_api = new PhpmelodyVimeo(null, null, $config['vimeo_api_token']);
								
								$args = array('page' => $import_page,
											  'per_page' => $import_results,
											);
											
								switch($import_action)
								{
									case 'search':
										
										$api_data = $vimeo_api->pm_user_videos($username, $args);
										
									break;
									
									case 'favorites':
										
										$api_data = $vimeo_api->pm_user_favorites($username, $args);
					
									break; 
									
									case 'playlists':
										
										$api_data = $vimeo_api->pm_playlist($_POST['playlistid'], $args);
					
									break;
								}
								
							break;
						}
						
						if (array_key_exists('error', $api_data))
						{
							$ajax_msg = '<strong>Unable to retrieve requested data.</strong>
											 <br />
											 <br />';
							$ajax_msg .= $api_data['error']['message'];
							if ( ! function_exists('curl_init') && ! ini_get('allow_url_fopen'))
							{
								$ajax_msg .= '<br />Your system doesn\'t support remote connections.
											  <br /> 
											  Ask your hosting provider to enable either <strong>allow_url_fopen</strong> or the <strong>cURL extension</strong>.';
							}
							exit(json_encode(array(	'success' => false, 
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' => pm_alert_error($ajax_msg, null, true),
												  )));
						}
						
						// begin formatting
						$total_results = count($api_data['results']);
						$alt 	 	= 0;
						$counter 	= 1;
						$duplicates = 0;
						$total_search_results = $api_data['meta']['total_results'];
						
						if ($total_results > 0)
						{
							$import_results_html = '';
							$import_results_array = array();
							
							ob_start();
							
							foreach ($api_data['results'] as $i => $item)
							{
								$tmp_src_name = ($data_source == 'youtube' || $data_source == 'youtube-channel') ? 'youtube' : $data_source;
								
								$count_vids = (int) count_entries('pm_videos', 'yt_id', $item['id'] ."' AND source_id = '". $sources[$tmp_src_name]['source_id']);
								$count_vids += (int) count_entries('pm_videos_trash', 'yt_id', $item['id'] ."' AND source_id = '". $sources[$tmp_src_name]['source_id']);
								
								if ($count_vids == 0)
								{
									$col = ($alt % 2) ? 'table_row1' : 'table_row2';
									$alt++;		
					
									$col_unembed = '';
									
									if ( ! $item['embeddable'] || $item['private'])
									{
										$col_unembed = 'table_row_unembed';
									}
					
									if (is_array($item['geo-restriction']))
									{
										$col_unembed = 'table_row_unembed';
										$georestriction = 'This video is ';
										$georestriction .=  ($item['geo-restriction']['type'] == 'deny') ? 'geo-restricted' : 'available only'; 
										$georestriction .= ' in the following countries: '. $item['geo-restriction']['list'];
									}
									
									$counter = $item['id'];
									if ( ! doing_cron())
									{
										include(ABSPATH . _ADMIN_FOLDER .'/import-item-template.php');
									}
									else
									{
										if ($item['embeddable'] && ! $item['private'])
										{
											$import_results_array[] = $item;
										}
									}
								}
								else
								{
									$duplicates++;
								}	
							}	//	end for()
							
							$exec_end = get_micro_time();
							$import_results_html .= ob_get_clean();
						}
						else
						{
							$ajax_msg = "Sorry, nothing found. \n Private videos will not appear in these results.";
							
							// Channels that are actually #hash-tags will have zero videos uploaded but one or more playlists
							// so we need to suggest checking them out. 
							if ($playlists['meta']['total_results'] > 0 && ! (($duplicates == $total_results && $duplicates > 0)))
							{
								$ajax_msg .= '<br />There may be videos in the <strong>Playlists</strong> tab. See the <strong>Playlists</strong> link on the top right area of this page.';
							}
							
							exit(json_encode(array(	'success' => true,
													'alert_type' => 'info',
													'total_results' => $total_results,
													'total_search_results' => $total_search_results, 
													'duplicates' => $duplicates,
													'msg' => $ajax_msg,
													'html' => pm_alert_info($ajax_msg, null, true),
													'sub_id' => $sub_id,
													'items' => (doing_cron()) ? $import_results_array : null
												   )));
						}
						
						if ( ! $sub_id && ! doing_cron())
						{
							switch ($import_action)
							{
								default:
								case 'search':
								case 'favorites': // @since v2.5
								case 'playlists': // @since v2.5
									
									$sub_type = 'user';
									$sub_name = $username_display;
									$sub_params = array(
														'action' => $import_action,
														'username' => $username,
														'results' => $import_results,
														'autofilling' => $autofilling,
														'autodata' => $autodata,
														'oc' => (count($overwrite_category)) ? 1 : 0,
														'utc' => (count($overwrite_category)) ? implode(',', $overwrite_category) : ''
													);
								break;
								// @since v2.5
								/*
								case 'favorites':
									
									$sub_type = 'user-favorites';
									$sub_name = $username_display ."'s favorites";
									$sub_params = array(
														'action' => $import_action,
														'username' => $username,
														'results' => $import_results,
														'autofilling' => $autofilling,
														'autodata' => $autodata,
														'oc' => (count($overwrite_category)) ? 1 : 0,
														'utc' => (count($overwrite_category)) ? implode(',', $overwrite_category) : ''
													);
								
								break;
								
								case 'playlists':
									
									$playlist_title = urldecode($_POST['title']);
									
									$sub_type = 'user-playlist';
									$sub_name = $username_display .'/'. $playlist_title;
									$sub_params = array(
														'action' => $import_action,
														'username' => $username,
														'results' => $import_results,
														'autofilling' => $autofilling,
														'autodata' => $autodata,
														'oc' => (count($overwrite_category)) ? 1 : 0,
														'utc' => (count($overwrite_category)) ? implode(',', $overwrite_category) : '',
														'playlistid' => trim($_POST['playlistid']),
														'title' => $playlist_title
													);
								
								break;
								*/
							}
							$sub_params['data_source'] = $data_source;
							$sub_params = serialize($sub_params);
							
							
							$sql = "SELECT sub_id 
									FROM pm_import_subscriptions 
									WHERE sub_type = '". $sub_type. "'
									  AND user_id = ". $userdata['id'] ."
									  AND data LIKE '%". secure_sql($username) ."%". $data_source ."%'";
							if ($result = mysql_query($sql))
							{
								if (mysql_num_rows($result) == 1)
								{
									$row = mysql_fetch_assoc($result);
									$sub_id = (int) $row['sub_id'];
								}
								mysql_free_result($result);
							}
						}
						else
						{
							$sql = "SELECT user_id, sub_name, sub_type, data
									FROM pm_import_subscriptions 
									WHERE sub_id = $sub_id";
							
							if ($result = mysql_query($sql))
							{
								$sub = mysql_fetch_assoc($result);
								mysql_free_result($result);
								
								$sub_name = $sub['sub_name'];
								$sub_params = $sub['data'];
								$sub_type = $sub['sub_type'];
							}
						}
						
						$sub_nonce = csrfguard_raw('_admin_import_subscriptions');
						
						// all videos found
						if ($duplicates == $total_results && $duplicates > 0)
						{
							if ($duplicates == $total_search_results) 
							{
								$ajax_msg = "You imported all the videos based on this particular search. Try searching for different terms.";
							}
							else 
							{
								$ajax_msg = "You may have imported most of the videos based on this particular search. \n Press 'Load more' to list more videos.";
							}
							
							exit(json_encode(array(	'success' => true,
													'alert_type' => 'info',
													'next_page' => ($data_source == 'youtube' || $data_source == 'youtube-channel') ? $api_data['meta']['next_page'] : ++$import_page,
													'total_results' => $total_results,
													'total_search_results' => $total_search_results, 
													'duplicates' => $duplicates,
													'sub' => array( 'name' => $sub_name,
																'params' => $sub_params,
																'type' => $sub_type,
																'_pmnonce' => $sub_nonce['_pmnonce'],
																'_pmnonce_t' => $sub_nonce['_pmnonce_t']
															  ),
													'msg' => $ajax_msg,
													'html' => pm_alert_info($ajax_msg, null, true),
													'sub_id' => $sub_id,
													'items' => (doing_cron()) ? $import_results_array : null
												   )));
						}
						
						exit(json_encode(array(	'success' => true, 
												'alert_type' => '',
												'next_page' => ($data_source == 'youtube' || $data_source == 'youtube-channel') ? $api_data['meta']['next_page'] : ++$import_page,
												'total_results' => $total_results,
												'total_search_results' => $total_search_results,
												'duplicates' => $duplicates,
												'sub' => array( 'name' => $sub_name,
																'params' => $sub_params,
																'type' => $sub_type,
																'_pmnonce' => $sub_nonce['_pmnonce'],
																'_pmnonce_t' => $sub_nonce['_pmnonce_t']
															  ),
												'msg' => '',
												'html' => $import_results_html,
												'sub_id' => $sub_id,
												'items' => (doing_cron()) ? $import_results_array : null
												)));
						
					break; // import -> search-user -> default/search/playlists/favorites
					
					case 'list-playlists': // show user playlists
						
						//	don't allow any white spaces in the username;
						$username = str_replace(' ', '', $username);
						$api_data = array();
						
						switch ($data_source)
						{
							case 'youtube':
							case 'youtube-channel':
								
								include(ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/autoload.php');
								
								$google_client = new Google_Client();
								$google_client->setDeveloperKey($config['youtube_api_key']);
					
								$youtube_api = new PhpmelodyYouTube($google_client);
								
								$args = array('page' => $_POST['page'], 
											  'pm-user-type' => ($data_source == 'youtube') ? 'user' : 'channel');
								$playlists = $youtube_api->pm_user_playlists($username, $args);
								
								$username_display = ($data_source == 'youtube') ? $username_display : $youtube_api->pm_channel_title;
								
							break;
							
							case 'dailymotion':
								
								include(ABSPATH . _ADMIN_FOLDER .'/src/dailymotion-sdk/autoload.php');
							
								$dailymotion_api = new PhpmelodyDailymotion();
								
								try {
									$args = array('page' => $import_page,
												  'per_page' => $import_results,
												);
									
									$playlists = $dailymotion_api->pm_user_playlists($username, $args);
									
								} catch(DailymotionApiException $e) {
					
									if ($dailymotion_api->error)
									{
										$api_data['error']['message'] = '<strong>Dailymotion API error '. $dailymotion_api->error->code . ':</strong> '. $dailymotion_api->error->message;
									}
									else
									{
										$playlists['error']['message'] = '<strong>Dailymotion API error:</strong> '. $e->__toString(); 
									}
								}
					
							break;
							
							case 'vimeo':
								
								if ( ! empty($config['vimeo_api_token']))
								{
									include(ABSPATH . _ADMIN_FOLDER .'/src/vimeo-sdk/autoload.php');
									
									$vimeo_api = new PhpmelodyVimeo(null, null, $config['vimeo_api_token']);
									
									$args = array('page' => $import_page,
												  'per_page' => $import_results,
												);
												
									$playlists = $vimeo_api->pm_user_playlists($username, $args);
								}
								else
								{
									$playlists = array('error' => array('message' =>
										'Vimeo API requires a <em>Access Token</em> to retrieve data. This is how you can get an API key:
										<br /><br />
										<ol>
											<li><a href="https://developer.vimeo.com/apps" target="_blank" title="Vimeo Developer API">Create</a> your Vimeo developer account to generate your token.</li>
											<li>Enter the generated token in the <a href="settings.php?highlight=vimeo_api_token&view=video">Settings</a> page.</li>
										</ol>'));
								}
						
							break;
						}
						
						if (array_key_exists('error', $playlists))
						{
							$ajax_msg = '<strong>Unable to retrieve requested data.</strong>
										 <br />
										 <br />';
							$ajax_msg .= $playlists['error']['message'];
							if ( ! function_exists('curl_init') && ! ini_get('allow_url_fopen'))
							{
								$ajax_msg .= '<br />Your system doesn\'t support remote connections.
											  <br /> 
											  Ask your hosting provider to enable either <strong>allow_url_fopen</strong> or the <strong>cURL extension</strong>.';
							}
							exit(json_encode(array(	'success' => false, 
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' => pm_alert_error($ajax_msg, null, true),
												  )));
						}
						
						if ($playlists['meta']['total_results'] > 0)
						{
							ob_start();
							
							?>
							<ul class="import-playlists" id="playlists">
							<?php foreach ($playlists['results'] as $i => $item) : 
								if ($_POST['playlistid'] == $item['id']) : ?>
								<li class="playlist-selected">
								<?php else : ?>
								<li class="border-radius3">
								<?php endif; ?>
									<a href="import-user.php?action=playlists&username=<?php echo $username; ?>&results=<?php echo $import_results; ?>&playlistid=<?php echo $item['id']; ?>&title=<?php echo urlencode($item['title']); ?>&autofilling=<?php echo $autofilling; ?>&autodata=<?php echo $autodata; ?>&oc=1&utc=<?php echo implode(',', $overwrite_category) .'&data_source='. $data_source .'&sub_id='. $subscription_id; ?>" class="import-user-playlist-item" data-playlist-id="<?php echo $item['id']; ?>" data-playlist-title="<?php echo htmlspecialchars($item['title']); ?>">
										<img src="img/playlist-overlay.png" class="playlist-overlay">
										<img src="<?php echo $item['playlist_thumb_url']; ?>" class="playlist-thumb" />
										<h4 class="alpha60"><?php echo $item['title']; ?></h4>
									</a>
								</li>
							<?php endforeach; ?>
							</ul>
							<?php
							
							if ( ! $sub_id)
							{
								$sub_type = 'user';
								$sub_name = $username_display;
								$sub_params = array(
													'action' => 'search',
													'username' => $username,
													'results' => $import_results,
													'autofilling' => $autofilling,
													'autodata' => $autodata,
													'oc' => (count($overwrite_category)) ? 1 : 0,
													'utc' => (count($overwrite_category)) ? implode(',', $overwrite_category) : ''
												);
								$sub_params['data_source'] = $data_source;
								$sub_params = serialize($sub_params);	
							}
							else
							{
								$sql = "SELECT user_id, sub_name, sub_type, data
										FROM pm_import_subscriptions 
										WHERE sub_id = $sub_id";
								
								if ($result = mysql_query($sql))
								{
									$sub = mysql_fetch_assoc($result);
									mysql_free_result($result);
									
									$sub_name = $sub['sub_name'];
									$sub_params = $sub['data'];
									$sub_type = $sub['sub_type'];
								}
							}
							
							$sub_nonce = csrfguard_raw('_admin_import_subscriptions');
							
							exit(json_encode(array(	'success' => true,
													'alert_type' => '',
													'next_page' => ($data_source == 'youtube' || $data_source == 'youtube-channel') ? $playlists['meta']['next_page'] : ++$import_page,
													'total_results' => count($playlists['results']),
													'total_search_results' => $playlists['meta']['total_results'],
													'duplicates' => 0,
													'sub' => array( 'name' => $sub_name,
																	'params' => $sub_params, 
																	'type' => $sub_type,
																	'_pmnonce' => $sub_nonce['_pmnonce'],
																	'_pmnonce_t' => $sub_nonce['_pmnonce_t']
																  ),
													'msg' => '',
													'html' => ob_get_clean(),
													'sub_id' => $sub_id
												  )));
						}
						else
						{
							$ajax_msg = $username . ' doesn\'t have any playlists.';
							exit(json_encode(array(	'success' => true,
													'alert_type' => 'info',
													'total_results' => 0,
													'total_search_results' => 0,
													'duplicates' => 0,
													'sub' => null,
													'msg' => $ajax_msg,
													'html' => pm_alert_info($ajax_msg, array('id' => 'playlists'), true),
												  )));
						}
						
					break; // import -> search-user -> playlists
					
					
				}

			break; // import -> search-user
			
			case 'import':
				
				$exec_start = get_micro_time();
				
				$data_source = ($data_source == 'youtube-channel') ? 'youtube' : $data_source;
				$source_id = $sources[ $data_source ]['source_id'];

				$total_videos = count($_POST['video_ids']);
				$imported_total = 0;
				$import_total_errors = 0;
				
				if ($total_videos == 0)
				{
					$ajax_msg = 'You need to select something first. No videos were selected for import.';
					exit(json_encode(array(	'success' => false,
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg, null, true),
											'imported_total' => 0,
											'total_videos' => 0,
											'import_total_errors' => 0,
											'item_status' => null
											)));
				}
				
				define('PHPMELODY', true);
				switch ($data_source)
				{
					case 'youtube':
					case 'youtube-channel':
						$download_thumb = $sources['youtube']['php_namespace'] .'\download_thumb';
						require_once(ABSPATH . _ADMIN_FOLDER .'/src/youtube.php');
					break;
					
					case 'dailymotion':
						$download_thumb = $sources['dailymotion']['php_namespace'] .'\download_thumb';
						require_once(ABSPATH . _ADMIN_FOLDER .'/src/dailymotion.php');
					break;
					
					case 'vimeo':
						$download_thumb = $sources['vimeo']['php_namespace'] .'\download_thumb';
						require_once(ABSPATH . _ADMIN_FOLDER .'/src/vimeo.php');
					break;
					case 'csv':
						$download_thumb = $sources['localhost']['php_namespace'] .'\download_thumb';
						require_once(ABSPATH . _ADMIN_FOLDER .'/src/localhost.php');
					break;
				}
				
				if (doing_cron() && $_POST['userdata']['user_id'] != '')
				{
					$userdata = get_user_data($_POST['userdata']['user_id']);
				}
				
				$item_status = array(); // keeps a status (success/fail) and other info about each imported item to be sent back to the UI
				
				// reverse order of submitted videos to import videos in the same order as they show up on the results page
				if ($total_videos > 0)
				{
					$_POST['video_ids'] = array_reverse($_POST['video_ids'], true);
				}
				
				if ($total_videos > 0)
				foreach($_POST['video_ids'] as $id => $v)
				{
					$item_stack_id = $_POST['stack_id'][$id]; // identifier for UI item
					
					$tmp_item_status = array('stack_id' => '#'. $item_stack_id,
											 'yt_id' => $id
											);
					
					$video_details = array( 'uniq_id' => '',
											'video_title' => '',	
											'description' => '',
											'yt_id' => '',
											'yt_length' => '',
											'category' => '',
											'submitted_user_id' => 0,
											'submitted' => '',
											'source_id' => '',
											'language' => '',
											'age_verification' => '',
											'url_flv' => '',
											'yt_thumb' => '',
											'mp4' => '',
											'direct' => '',
											'tags' => '',
											'featured' => 0,
											'restricted' => 0,
											'allow_comments' => 1
										  );
					
					$video_details['video_title'] = trim( str_replace('&quot;', '"', $_POST['video_title'][$id]) );
					$video_details['description'] = trim( $_POST['description'][$id] );
					$video_details['tags'] 		  = trim( $_POST['tags'][$id] );
					$video_details['category'] 	  = is_array($_POST['category'][$id]) ? implode(',', $_POST['category'][$id]) : '';
					$video_details['direct'] 	  = trim( $_POST['video_ids'][$id] );
					$video_details['source_id']	  = ($data_source == 'csv') ? $_POST['source_id'][$id] : $source_id;
					$video_details['language']	  = 1;
					$video_details['submitted_user_id'] = (int) $userdata['id'];
					$video_details['submitted']	  = $userdata['username'];
					$video_details['yt_id']		  = $_POST['video_ids'][$id];
					$video_details['yt_length']	  = $_POST['duration'][$id];
					$video_details['tags']		  = $_POST['tags'][$id];
					$video_details['yt_thumb']	  = $_POST['thumb_url'][$id];
					$video_details['url_flv']	  = $_POST['direct'][$id];
					$video_details['direct']	  = $_POST['direct'][$id];
					$video_details['mp4']		  = '';
					$video_details['description'] = nl2br($video_details['description']);
					
					$uniq_id = generate_video_uniq_id();
			
					$video_details['uniq_id'] = $uniq_id;
					
					$tmp_item_status['uniq_id'] = $uniq_id;
			
					//	download thumbnail
					if ($video_details['yt_thumb'] != '')
					{
						$img = (_THUMB_FROM == 2) ? $download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id) : true;
					}
					
					if ($_POST['featured'][$id] == "1")
					{
						$video_details['featured'] = 1;
					}
					
					$modframework->trigger_hook('admin_import_insertvideo_pre');
					$new_video = insert_new_video($video_details, $new_video_id);
					
					if ($new_video !== true)
					{
						$error_msg = 'An error occurred while inserting this video in your database.<br /><strong>MySQL reported:</strong> '. $new_video[0];
						$tmp_item_status['video_id'] = null;
						$tmp_item_status['success'] = false;
						$tmp_item_status['msg'] = $error_msg;
						$tmp_item_status['html'] = pm_alert_error($error_msg, null, true);
						$import_total_errors++;
					}
					else
					{
						$tmp_item_status['video_id'] = $new_video_id;
						$tmp_item_status['success'] = true;
						
						$modframework->trigger_hook('admin_import_insertvideo_post');
						//	tags?
						if($video_details['tags'] != '')
						{
							$tags = explode(",", $video_details['tags']);
							foreach($tags as $k => $tag)
							{
								$tags[$k] = stripslashes(trim($tag));
							}
							//	remove duplicates and 'empty' tags
							$temp = array();
							for($i = 0; $i < count($tags); $i++)
							{
								if($tags[$i] != '')
									if($i <= (count($tags)-1))
									{
										$found = 0;
										for($j = $i + 1; $j < count($tags); $j++)
										{
											if(strcmp($tags[$i], $tags[$j]) == 0)
												$found++;
										}
										if($found == 0)
											$temp[] = $tags[$i];
									}
							}
							$tags = $temp;
							//	insert tags
							if(count($tags) > 0)
								insert_tags($video_details['uniq_id'], $tags);
						}
						$imported_total++;
						
						if ($data_source == 'csv')
						{
							$csv_item_id = (int) $_POST['csv_item_id'][$id];
							$sql = "DELETE FROM pm_import_csv_items 
									WHERE item_id = ". $csv_item_id;
							@mysql_query($sql); 
						}
					}
					
					$item_status[] = $tmp_item_status;
				} // end for()
				
				$exec_end = get_micro_time();
				
				if ($data_source == 'csv' && $imported_total > 0)
				{
					$file_id = (int) $_POST['file_id'];
					$sql = "UPDATE pm_import_csv_files
							SET items_imported = items_imported + $imported_total 
							WHERE file_id = $file_id";
					@mysql_query($sql);
				}
				
				if ($imported_total == $total_videos)
				{
					$ajax_msg = 'The selected videos were successfully imported.';
				}
				else
				{
					$ajax_msg = 'Imported <strong>'.$imported_total.'</strong> out of <strong>'.$total_videos.'</strong> selected videos.';
				}
				
				if ($imported_total < $total_videos && $import_total_errors == 0)
				{
					$ajax_msg .= '<br />Duplicated videos and videos without a title were not imported.';
				}
				
				//$ajax_msg .= '<br />Import took <strong>' . get_exec_time($exec_end, $exec_start) . '</strong> seconds.';
				
				exit(json_encode(array(	'success' => true,
										'alert_type' => ($import_total_errors > 0) ? 'warning' : 'success',
										'msg' => $ajax_msg,
										'html' => ($import_total_errors > 0) ? pm_alert_warning($ajax_msg, null, true) : pm_alert_success($ajax_msg, null, true),
										'imported_total' => $imported_total,
										'total_videos' => $total_videos,
										'import_total_errors' => $import_total_errors,
										'item_status' => $item_status
										)));
				
				
			break;
		}
		
	break; // case 'import'
	
	case 'import-csv':
		
		@set_time_limit(200);
		
		$file_id = (int) $_POST['file_id'];
				
		if ( ! $file_id)
		{
			$ajax_msg = 'Missing file ID';
			exit(json_encode(array( 'success' => false,
									'alert_type' => 'error', 
									'state' => 'error',
									'msg' => $ajax_msg,
									'html' => pm_alert_error($ajax_msg, false, true),
									'message' => pm_alert_error($ajax_msg, false, true)
								  )));
		}
		
		switch ($action)
		{
			case 'process-queue':
				
				$ajax_state = 'init';
				$exec_start = get_micro_time();
				
				$sql = "SELECT * 
						FROM pm_import_csv_files 
						WHERE file_id = $file_id"; 
				
				if ( ! $result = mysql_query($sql))
				{
					exit(json_encode(array('state' => 'error',
										   'alert_type' => 'error',
										   'message' => pm_alert_error('An error occurred while retrieving file data. <br /><strong>MySQL Error</strong>: '. mysql_error(), false, true)
										  )));
				}
				
				$csv_file = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$sources = a_fetch_video_sources();
				$allowed_ext = array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', '.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');
				
				$sql_limit = 20;
				$sql_start = (int) $_POST['start'];
				
				if ($sql_start < 0)
					$sql_start = 0;
				
				$items_processed = (int) $csv_file['items_processed'];
				//$items_skipped = (int) $csv_file['items_skipped'];
				$items_skipped = 0; // reset every time
				$items_with_error = (int) $csv_file['items_with_error'];
				
				$ajax_state = 'processing';

				if ($items_processed >= $csv_file['items_detected'])
				{
					$ajax_state = 'finished';
				}
				
				if ($items_processed < $csv_file['items_detected'])
				{
					$items = array();
					/*
					$sql = "SELECT * 
							FROM pm_import_csv_items
							WHERE processed = '0'
							ORDER BY item_id ASC
							LIMIT $sql_start, $sql_limit";
					*/
					$sql = "SELECT * 
							FROM pm_import_csv_items
							WHERE file_id = $file_id 
							ORDER BY item_id ASC
							LIMIT $sql_start, $sql_limit";
								
					if ( ! $result = mysql_query($sql))
					{
						$ajax_state = 'error';
						$ajax_msg = 'An error occurred while updating your database.<br /><strong>MySQL Error</strong>: '. mysql_error();
						
						$ajax_response = array('state' => $ajax_state,
											   'file_id' => $csv_file['file_id'],
											   'start' => $sql_start,
											   'limit' => $sql_limit,
											   'progress' => round(($items_processed * 100) / $csv_file['items_detected'], 2),
											   'items_processed' => $items_processed,
											   'total_items' => $csv_file['items_detected'],
											   'eta' => 0,
											   'eta_formatted' => 'n/a',
											   'message' => pm_alert_error($ajax_msg, false, true)
											  );
						exit(json_encode($ajax_response));
					}
					
					while ($row = mysql_fetch_assoc($result))
					{
						$items[] = $row;
					}
					mysql_free_result($result);
					
					if (count($items) > 0)
					{
						foreach ($items as $k => $item)
						{
							$errors = array();
							
							if ($item['processed'] == '1')
							{
								continue;
							}
							
							$items_processed++;
							
							$video_details = array(	'uniq_id' => '',
													'video_title' => '',
													'description' => '',
													'yt_id' => '',
													'yt_length' => '',
													'category' => '',
													'submitted' => '',
													'source_id' => '',
													'language' => '',
													'age_verification' => '',
													'url_flv' => '',
													'yt_thumb' => '',
													'yt_thumb_local' => '',
													'mp4' => '',
													'direct' => '',
													'tags' => '', 
													'featured' => 0,
													'added' => '',
													'restricted' => 0, 
													'allow_comments' => 1,
													'allow_embedding' => 1
													);
							$mode = 0;
							$temp = '';
							
							$item['direct'] = expand_common_short_urls(trim($item['direct']));
							
							//	Is this a direct link to a video file?
							if (strpos($item['direct'], '?') !== false)
							{
								$temp = explode('?', $item['direct']);
								$item['direct'] = $temp[0];
							}
							
							$ext = pm_get_file_extension($item['direct'], true);
							
							if (is_array($temp) && count($temp) > 0)
							{
								$item['direct'] = '';
								$temp[0] = rtrim($temp[0], '?');
								$temp[0] = $temp[0] .'?';
								foreach ($temp as $k => $v)
								{
									$item['direct'] .= $v;
								}
							}
							
							if (in_array($ext, $allowed_ext) && (preg_match('/photobucket\.com/', $item['direct']) == 0))
							{
								if ( ! is_url($item['direct']))
								{
									// maybe it's an IP address
									if (is_ip_url($item['direct']))
									{
										$mode = 2;
									}
									else
									{
										$mode = 3;
									}
								}
								else if (strpos($item['direct'], _URL) !== false)
								{
									$mode = 3;
								}
								else
								{
									// filenames that look like domains pass as URLs (e.g. some-file.info.mp4) 
									// so we need to check them again for http(s), "//" and www
									if ( ! preg_match('%^((http(s?)\://)|(//)|(www\.))%', $item['direct']))
									{
										$mode = 3;
									}
									else 
									{
										$mode = 2;
									}
								}
							}
							else if (is_url($item['direct']))
							{
								$mode = 1;
							}
							else	//	default;
							{
								$mode = 2;
							}
							
							$get_info_failed = false;
							//	Build the $video_details array;
							switch($mode)
							{
								case 1: 	//	 Outsource (e.g. youtube); 
									
									$use_this_src = -1;
									
									foreach($sources as $src_id => $source)
									{
										if($use_this_src > -1)
										{
											break;
										}
										else
										{
											if(@preg_match($source['source_rule'], $item['direct']))
											{
												$use_this_src = $source['source_id'];
											}
										}
									}
				
									if($use_this_src > -1)
									{
										if(!file_exists( ABSPATH . _ADMIN_FOLDER .'/src/' . $sources[ $use_this_src ]['source_name'] . '.php'))
										{
											$errors[] = "File '". ABSPATH . _ADMIN_FOLDER ."/src/" . $sources[ $use_this_src ]['source_name'] . ".php' not found.";
											break;
										}
										
										$temp = array();
										$do_main = $sources[ $use_this_src ]['php_namespace'] .'\do_main';
										
										if ( ! function_exists($do_main))
										{
											require_once( ABSPATH . _ADMIN_FOLDER .'/src/' . $sources[ $use_this_src ]['source_name'] . '.php');
										}
										
										// hide possible outputs from source file; catch everything and keep going
										try {
											
											ob_start(); 
											@$do_main($temp, $item['direct']);
											ob_end_clean();
										} catch (Exception $e) {
											$errors[] = $e->getMessage();
										}
										
										$video_details = array_merge($video_details, $temp);
										
										unset($temp);
										
										$video_details['source_id'] = $use_this_src;
									}
									else
									{
										$errors[] = "<strong>This video site is not supported</strong>. For a full list of supported video sites, open and read the 'Help' section (Top Right of this page).";
									}
								break;
								
								case 2:		//	2 = direct link to .flv/.mp4 (outsource)
								
									$video_details['source_id'] = $sources['other']['source_id'];
									$video_details['url_flv'] = $item['direct'];
									$video_details['direct'] = $item['direct'];
									
								break;
								
								case 3:		//	flv hosted locally or just uploaded
								
									$video_details['url_flv'] = $item['direct'];
									$video_details['direct'] = $item['direct'];
										
									$video_details['source_id'] = $sources['localhost']['source_id'];
									
								break;
							}
							
							if ($get_info_failed)
							{
								$items_skipped++;
								continue;
							}
							
							//	Prevent adding the same video twice
							if ($video_details['direct'] != '')
							{
								$sql = "SELECT * FROM pm_videos_urls WHERE direct = '". $video_details['direct'] ."'";
								
								$result = mysql_query($sql);
								if (mysql_num_rows($result) > 0)
								{
									$row = mysql_fetch_assoc($result);
									mysql_free_result($result);
									
									//$errors[] = 'This video is already in your database.';
									$items_skipped++;
									continue;
								}
								unset($row, $sql, $result);
							}
							
							$has_errors = 0;
							if (count($errors) > 0)
							{
								$has_errors = 1;
								$items_with_error++;
							}
							
							if ($video_details['video_title'] == '' && $item['video_title'] == '' && ($mode == 2 || $mode == 3))
							{
								// extract filename
								$pathinfo = pathinfo($item['direct']);
								$filename =  basename($item['direct'], '.'. $pathinfo['extension']);
								$unwanted_chars = array("-", "_", ",","'",".","(",")","[","]","*","{","}","  ","   ");
								$video_details['video_title'] = ucwords(str_replace($unwanted_chars, " ", $filename));
							}
							
							// don't overwrite the CSV values with API data
							$video_details['video_title'] = ($item['video_title'] != '') ? $item['video_title'] : $video_details['video_title'];
							$video_details['description'] = ($item['description'] != '') ? $item['description'] : $video_details['description'];
							$video_details['tags'] = ($item['tags'] != '') ? $item['tags'] : $video_details['tags'];
							$video_details['yt_thumb'] = ($item['yt_thumb'] != '') ? $item['yt_thumb'] : $video_details['yt_thumb'];
							$video_details['yt_length'] = ($item['yt_length'] != '') ? $item['yt_length'] : $video_details['yt_length'];
							
							// trim whitespaces added sometimes with copy pasting
							$video_details['video_title'] = trim($video_details['video_title']);
							$video_details['description'] = trim($video_details['description']);
							$video_details['yt_thumb'] = trim($video_details['yt_thumb']);
							$video_details['yt_length'] = (int) $video_details['yt_length'];
							
							// UPDATE pm_import_csv_items
							$sql = "UPDATE pm_import_csv_items 
									   SET  uniq_id = '". secure_sql($video_details['uniq_id']) ."',
											video_title = '". secure_sql($video_details['video_title']) ."',
											description = '". secure_sql($video_details['description']) ."',
											yt_id = '". secure_sql($video_details['yt_id']) ."',
											yt_length = '". secure_sql($video_details['yt_length']) ."',
											source_id = '". secure_sql($video_details['source_id']) ."',
											url_flv = '". secure_sql($video_details['url_flv']) ."',
											yt_thumb = '". secure_sql($video_details['yt_thumb']) ."',
											mp4 = '". secure_sql($video_details['mp4']) ."',
											direct = '". secure_sql($video_details['direct']) ."',
											tags = '". secure_sql($video_details['tags']) ."',
											embeddable = '". (($video_details['embeddable'] === false) ? 0 : 1) ."',
											private = '". (( ! $video_details['private']) ? 0 : 1) ."',
											`geo-restriction` = '". secure_sql( serialize($video_details['geo-restriction'])) ."',
											has_errors = '$has_errors', 
											errors = '". (($has_errors) ? secure_sql( serialize($errors) ) : '' ) ."',
											processed = '1'
									WHERE item_id = ". $item['item_id'];
							
							if ( ! mysql_query($sql))
							{
								$ajax_state = 'error';
								$ajax_msg = 'An error occurred while updating your database.<br /><strong>MySQL Error</strong>: '. mysql_error();
							}
						} // end foreach ()
						
						$items_detected = $csv_file['items_detected'] - $items_skipped;
						$items_processed -= $items_skipped;
						
						// UPDATE pm_import_csv_files
						$sql = "UPDATE pm_import_csv_files 
								   SET  items_detected = $items_detected,
										items_processed = $items_processed,
										items_skipped = items_skipped + $items_skipped,
										items_with_error = $items_with_error
								WHERE file_id = ". $csv_file['file_id'];
						if ( ! mysql_query($sql))
						{
							$ajax_state = 'error';
							$ajax_msg = 'An error occurred while updating your database.<br /><strong>MySQL Error</strong>: '. mysql_error();
						}
					} // end if (count..)
					else
					{
						$ajax_state = 'finished';
					}
				}
				
				switch ($ajax_state)
				{
					default:
					case 'init':
					case 'processing':
						
						$exec_end = get_micro_time();
						$progress = round(($items_processed * 100) / $csv_file['items_detected'], 2);
						$previous_eta = (int) $_POST['eta'];
						$current_exec_time = get_exec_time($exec_end, $exec_start);
						$eta = ($csv_file['items_detected'] / $sql_limit) * ($current_exec_time + 1.5); // + assumed transport and server response times
						$eta = ($previous_eta > 0) ? (($previous_eta + $eta) / 2) : $eta;						
						
						$ajax_response = array('state' => $ajax_state,
											   'file_id' => $csv_file['file_id'],
											   'start' => $sql_start + $sql_limit,
											   'progress' => $progress,
											   'items_processed' => $items_processed,
											   'total_items' => $csv_file['items_detected'],
											   'eta' => $eta,
											   'eta_formatted' => sec2min($eta),
											   'message' => ''
											  );
			
					break;
					
					case 'finished':

						$ajax_msg = '';

						if ( $items_processed == 0) 
						{
							$ajax_msg .= '<div class="alert alert-info">';
							$ajax_msg .= 'Sorry, looks like there is nothing we can import from this CSV.';

						} 
						else 
						{
							$ajax_msg .= '<div class="alert alert-success">';
							$ajax_msg .= '<strong>Looks great!</strong> '. pm_number_format($items_processed) . ' items can be imported now. ';
						}

						if ( $items_with_error > 0 ) 
						{
							$ajax_msg .= ' Unfortunately, there were a few errors ('.pm_number_format($items_with_error).').';
						}

						if ( (pm_number_format($csv_file['items_skipped'] + $items_skipped)) > 0 ) 
						{
							$ajax_msg .= ' A total of '. pm_number_format($csv_file['items_skipped'] + $items_skipped) .' videos were skipped (possible duplicates).';
						}

						$ajax_msg .= '</div>';

						if ( $items_processed > 0)
						{ 
							$ajax_msg .= '<div class="pm-file-action-next-step">';
							$ajax_msg .= '<a href="'. _URL .'/'. _ADMIN_FOLDER .'/import-csv.php?step=3&file-id='. $csv_file['file_id'] .'" class="btn btn-success">Continue with Import</a>';
							$ajax_msg .= '</div>';
						}
						else 
						{
							$ajax_msg .= '<div class="pm-file-action-next-step">';
							$ajax_msg .= '<a href="'. _URL .'/'. _ADMIN_FOLDER .'/import-csv.php" class="btn btn-default">Upload another CSV</a>';
							$ajax_msg .= '</div>';
						}
						
						$ajax_response = array('state' => $ajax_state,
											   'file_id' => $csv_file['file_id'],
											   'start' => $csv_file['items_detected'],
											   'limit' => $sql_limit,
											   'progress' => 100,
											   'items_processed' => $items_processed,
											   'total_items' => $csv_file['items_detected'],
											   'eta' => 0,
											   'eta_formatted' => 'n/a',
											   'message' => $ajax_msg
											  );
					break;
					
					case 'error':
						
						$ajax_response = array('state' => $ajax_state,
											   'file_id' => $csv_file['file_id'],
											   'start' => $sql_start,
											   'limit' => $sql_limit,
											   'progress' => round(($items_processed * 100) / $csv_file['items_detected'], 2),
											   'items_processed' => $items_processed,
											   'total_items' => $csv_file['items_detected'],
											   'eta' => 0,
											   'eta_formatted' => 'n/a',
											   'message' => pm_alert_error($ajax_msg, false, true)
											  );
					break;
				}
				
				exit(json_encode($ajax_response));
				
			break; // case 'process-queue'
			
			case 'delete-file':
				
				$sql = "DELETE FROM pm_import_csv_files 
						WHERE file_id = $file_id";
				@mysql_query($sql);
				
				$sql = "DELETE FROM pm_import_csv_items 
						WHERE file_id = $file_id";
				@mysql_query($sql);
				
				$ajax_msg = 'The file has been removed.';
				exit(json_encode(array(	'success' => true,
										'alert_type' => 'success',
										'msg' => $ajax_msg,
										'html' => pm_alert_success($ajax_msg, null, true),
										'affected_rows' => mysql_affected_rows()
										)));
				
				
			break; // case 'delete-file'
		}
		
	break; // case 'import-csv'
	
	case 'manage-comments':
		
		switch ($action)
		{
			case 'edit-comment':
				
				$response = array('success' => false, 'msg' => '', 'html' => '');
	
				if ( ! $logged_in )
				{
					$response['msg'] = 'Please log in first.';
					echo json_encode($response);
					exit();
				}
				if (is_admin() || (is_moderator() && mod_can('manage_comments')))
				{
					$comment_id = (int) $_POST['comment_id'];
					if ($comment_id)
					{
						$comment = trim($_POST['comment_txt']);
						$comment = nl2br($comment);
						
						if ($config['allow_emojis'])
						{
							include(ABSPATH .'include/emoji/autoload.php');
							
							$emoji_client = new Emojione\Client(new Emojione\Ruleset());
							$emoji_client->ascii = true;
							$emoji_client->unicodeAlt = false;
							
							// convert unicode to shortname for storage
							$comment = $emoji_client->toShort($comment);
						}
											
						$sql = "UPDATE pm_comments 
								SET comment = '". secure_sql($comment) ."' 
								WHERE id = '". $comment_id ."'";
						if ( ! $result = mysql_query($sql))
						{
							$response['msg'] = 'Comment editing failed. MySQL Error: '. mysql_error();
							$response['alert_type'] = 'error';
						}
						else
						{
							$response['success'] = true;
							$response['alert_type'] = 'success';
							$response['html'] = ($config['allow_emojis']) ? $emoji_client->shortnameToImage($comment) : $comment;
						}
					}
					
					exit(json_encode($response));	
				}
				else
				{
					$response['msg'] = 'Sorry, you do not have access to this area.';
					echo json_encode($response);
					exit();
				}
				
			break;
		}
	break; // case 'manage-comments'
	
	
	case 'cron':
				
		include(ABSPATH .'include/cron_functions.php');
		
		if ( ! is_admin())
		{
			$ajax_msg = 'Sorry, you do not have access to this area.';
			exit(json_encode(array(	'success' => false, 
									'alert_type' => 'error',
									'msg' => $ajax_msg, 
									'html' => pm_alert_error($ajax_msg))));	
		}
		
		if ($action != 'add-job' && $action != 'edit-form')
		{
			if ( ! csrfguard_check_referer($_POST['_pmnonce']))
			{
				$ajax_msg = 'Invalid token or session expired. Please refresh this page and try again.';
				exit(json_encode(array(	'success' => false, 
										'alert_type' => 'error',
										'msg' => $ajax_msg,
										'html' => pm_alert_error($ajax_msg))));
			}
			
			$nonce = array();
			if (in_array($_POST['_pmnonce'], array('_admin_cron_jobs_form_automated-jobs', '_admin_cron_jobs_form_import-user', '_admin_cron_jobs_form_import')))
			{
				$nonce = csrfguard_raw($_POST['_pmnonce']);
			}
		}
		
		if ($action != 'add-job')
		{
			$job_id = ($_GET['job-id'] != '') ? (int) $_GET['job-id'] : (int) $_POST['job-id'];
		
			if ( ! $job_id && $action != 'edit-form')
			{
				$ajax_msg = 'Invalid job ID provided.';
				exit(json_encode(array(	'success' => false, 
										'alert_type' => 'error',
										'msg' => $ajax_msg,
										'html' => pm_alert_error($ajax_msg))));
			}
			else if ($job_id)
			{
				$job = get_cron_job($job_id);
			}
		}		
		
		if ($job === false)
		{
			$ajax_msg = 'The requested job was not found.';
			exit(json_encode(array(	'success' => false, 
									'alert_type' => 'error',
									'_pmnonce' => $nonce['_pmnonce'],
									'_pmnonce_t' => $nonce['_pmnonce_t'],
									'msg' => $ajax_msg,
									'html' => pm_alert_error($ajax_msg))));
		}
		
		switch ($action)
		{
			case 'stop-job':
			case 'start-job':
				
				$job['status'] = ($job['status'] == CRON_STATUS_STOPPED) ? CRON_STATUS_LIVE : CRON_STATUS_STOPPED;
				$job['state'] = CRON_STATE_READY;
				
				switch ($job['type'])
				{
					case 'import':
						
						$job['last_exec_time'] = 0;
						
					break;
					
					case 'vscheck':
						
						$job['data']['sql_start'] =
						$job['data']['time_started'] =
						$job['data']['videos_processed'] =
						$job['data']['time_last_run'] =
						$job['data']['total_videos'] = 
						$job['data']['progress'] = 0;
						$job['last_exec_time'] = 0;
						
					break;
					
					case 'video-sitemap':
					case 'sitemap':
						
						$job['data']['time_started'] =
						$job['data']['time_last_run'] =
						$job['data']['sql_added_time_limit'] =
						$job['data']['progress'] = 0;
						$job['last_exec_time'] = 0;
						
					break;
				}
				
				if ( ! $result = update_cron_job($job))
				{
					$ajax_msg = 'An error occurred while updating your database.<br /><strong>MySQL Error</strong>: '. mysql_error();
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				cron_log('Job '. (($job['status'] == CRON_STATUS_LIVE) ? 'activated.' : 'deactivated.'));
				
				$btn_html = $state_html = '';
				
				ob_start();
				show_play_stop_button_html($job);
				$btn_html = ob_get_clean();
				
				ob_start();
				show_cron_job_state_html($job);
				$state_html = ob_get_clean();
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'_pmnonce' => $nonce['_pmnonce'],
										'_pmnonce_t' => $nonce['_pmnonce_t'],
										'msg' => '',
										'html' => $btn_html,
										'state_html' => $state_html)));
			break;
			
			case 'add-job':
				
				if ( ! csrfguard_check_referer('_admin_edit_cron_job'))
				{
					$ajax_msg = 'Invalid token or session expired. Please refresh this page and try again.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				$nonce = csrfguard_raw('_admin_edit_cron_job');
				
				$job = $_POST;
				$job['status'] = CRON_STATUS_LIVE;
				
				if ($job['type'] == 'import')
				{
					$exclude_keywords = explode(',', $_POST['exclude_keywords']);
					if (count($exclude_keywords) > 0)
					{
						foreach ($exclude_keywords as $k => $kw)
						{
							$kw = trim($kw);
							$kw = str_replace('-', '', $kw);
							
							$exclude_keywords[$k] = $kw;
						}
						$job['data']['exclude_keywords'] = $exclude_keywords;
					}
					
					
					$job['data']['userdata']['username'] = trim($_POST['username']);
					if ($_POST['username'] != $userdata['username'])
					{
						$job['data']['userdata']['user_id'] = username_to_id($job['data']['username']);
					}
					else
					{
						$job['data']['userdata']['user_id'] = $userdata['id'];
					}
					
					if (($uploaded_after = validate_item_date($_POST)) !== false)
					{
						$job['data']['uploaded_after'] = pm_mktime($uploaded_after);
					}
				}
				
				if ($job['type'] == 'vscheck')
				{
					$job['data']['video_sorting'] = $_POST['video_sorting'];
					$job['data']['video_limit'] = $_POST['video_limit'];
				}
				
				if ( ! $job_id = add_cron_job($job))
				{
					$ajax_msg = 'An error occurred while creating this job.<br /><strong>MySQL Error</strong>: '. mysql_error();
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg)))); 
				}
				
				$job = array('job_id' => $job_id);
				cron_log('Job created.');
				
				$ajax_msg = 'New automated job created successfully. <br />Visit "Automated Jobs" to manage it.';
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'msg' => $ajax_msg,
										'html' => pm_alert_success($ajax_msg),
										'job_id' => $job_id,
										'sub_id' => $job['rel_object_id']
										)));
				
			break;
			
//			case 'edit-job':
//			break;
			
			case 'edit-form': 
				
				if ( ! $job_id)
				{
					$job = array('rel_object_id' => (int) $_GET['rel-object-id'],
								 'type' => $_GET['job-type']
							);
				}
				
				ob_start();
				
				show_edit_cron_job_form($job);
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'msg' => '',
										'html' => ob_get_clean()
										)));
			break;
			
			case 'delete-job':
				
				// prevent deleting system jobs
				if (in_array($job['type'], array('vscheck', 'sitemap', 'video-sitemap')))
				{
					$ajax_msg = 'Default jobs cannot be deleted. <br /> Please deactivate any unnecessary jobs.';
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				if ( ! delete_cron_job($job_id))
				{
					$ajax_msg = 'An error occurred while updating your database.<br /><strong>MySQL Error</strong>: '. mysql_error();
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				$ajax_msg = 'Job removed successfully.';
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'_pmnonce' => $nonce['_pmnonce'],
										'_pmnonce_t' => $nonce['_pmnonce_t'],
										'msg' => $ajax_msg,
										'html' => $schedule_btn_html)));
				
			break; 
			
			case 'view-log':
				
				$log = get_cron_log($job_id);
				$total_entries = count_entries('pm_cron_log', 'job_id', $job_id);
				
				if ($log === false)
				{
					$ajax_msg = 'An error occurred while retrieving your data.<br /><strong>MySQL Error</strong>: '. mysql_error();
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'total_entries' => $total_entries,
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				ob_start();
				
				?>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped tablesorter">
					<thead>
						<tr>
							<th width="160">Log Date</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
					<?php if (in_array($job['type'], array('vscheck', 'sitemap', 'video-sitemap')) && $job['data']['time_started'] > 0): ?>
					<tr class="alert alert-warning">
						<td><?php echo date('M d, Y h:i:s A', $job['data']['time_last_run']); ?></td>
						<td>
							Job in progress: <?php echo pm_number_format($job['data']['videos_processed']);?>
							<?php if ($job['data']['progress'] > 0):
								echo '('. round($job['data']['progress'], ($job['data']['progress'] >= 1) ? 0 : 2) .'%)'; 
							endif; ?>
							videos processed so far.
						</td>
					</tr>
					<?php endif; ?>
					<?php if (is_array($log) && count($log) > 0) :
						foreach ($log as $k => $log_data) : ?>
						<tr>	
							<td><?php echo date('M d, Y h:i:s A', $log_data['time']); ?></td>
							<td><?php echo stripslashes($log_data['notes']); ?></td>
						</tr>
						<?php endforeach;
						else : ?>
						<?php if ( ! (in_array($job['type'], array('vscheck', 'sitemap', 'video-sitemap')) && $job['data']['time_started'] > 0)): ?>
							<tr>
								<td colspan="2" align="center" style="text-align: center;">Nothing logged yet.</td>
							</tr>
						<?php endif; ?>
					<?php endif; ?>
					</tbody>
				</table>
				<?php 
				
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'total_entries' => $total_entries,
										'_pmnonce' => $nonce['_pmnonce'],
										'_pmnonce_t' => $nonce['_pmnonce_t'],
										'msg' => '',
										'html' => ob_get_clean())));
			break;
			
			case 'clear-log':
				
				if ( ! $result = clear_cron_log($job_id))
				{
					$ajax_msg = 'An error occurred while updating your database.<br /><strong>MySQL Error</strong>: '. mysql_error();
					exit(json_encode(array(	'success' => false, 
											'alert_type' => 'error',
											'total_entries' => $total_entries,
											'_pmnonce' => $nonce['_pmnonce'],
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => pm_alert_error($ajax_msg))));
				}
				
				$ajax_msg = 'The history log was cleared successfully.'; 
				exit(json_encode(array(	'success' => true, 
										'alert_type' => 'success',
										'_pmnonce' => $nonce['_pmnonce'],
										'_pmnonce_t' => $nonce['_pmnonce_t'],
										'msg' => $ajax_msg,
										'html' => pm_alert_success($ajax_msg))));
			break;	
		}
		
	break; // case 'cron'
	
	case 'upload':
		
		switch ($action)
		{
			case 'upload-image': // moved from upload_image.php @since v2.7
				
				header('Content-Type: text/plain; charset=utf-8');
				
				$whitelist	  = array('.jpg', '.gif', '.png', '.jpeg');
				$allowed_type = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg');
				$upload_errors = array(
						1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
						2 => "The uploaded file exceeds the MAX_FILE_SIZE directive.",
						3 => "The uploaded file was only partially uploaded.",
						4 => "No file was uploaded.",
						6 => "Missing a temporary folder."
					);
				
				if ($logged_in && (is_admin() || is_moderator() || is_editor()))
				{
					if (is_array($_FILES['file']))
					{
						require_once('img.resize.php');
				
						$file = $_FILES['file'];
						$ext = pm_get_file_extension($file['name'], true);
						
						if (in_array($ext, $whitelist))
						{
							if ($file['error'] == 0)
							{
								if ($file['size'] > 0)
								{
									// test mime type @since 2.7.1
									// @todo - maybe let unfiltered for is_admin() accounts? 
									$filetype = pm_check_filetype_and_ext($file['tmp_name'], $file['name'], $mimes = false);
									
									if (empty($filetype['ext']) || empty($filetype['type'])) 
									{
										$error = 'This file type is not permitted for security reasons.';
										exit(json_encode(array( 'success' => false,
																'alert_type' => 'error',
																'msg' => $error,
																'html' => pm_alert_error($error, array('id' => '_error_')))));
										
									}
									
									if ($_POST['upload-type'] != 'logo' && $_POST['upload-type'] != 'video-thumb' && $_POST['upload-type'] != 'category-image')
									{
										// WYSIWYG editor 
										$new_name = substr(md5($file['name'] . time()), 1, 8) . $ext;
										$uploadFile = _ARTICLE_ATTACH_DIR_PATH . $new_name;
										
										$move = @move_uploaded_file($file['tmp_name'], $uploadFile);
										if ($move !== false)
										{
											// generate thumbnail
											$thumb_name = str_replace($ext, '_th'.$ext, $uploadFile);
											$resize = resize_then_crop($uploadFile, $thumb_name, THUMB_W_ARTICLE, THUMB_H_ARTICLE, "255", "255", "255");
											
											$img = getimagesize($uploadFile); // 0 = width, 1 = height, 2 = type, 3 = attr
											
											$width = $img[0];
											$height = $img[1];
											//$use_lightbox = false;
											$use_lightbox = true;
											$html = '';
											
											if ($img[0] > 500)
											{
												$width = 500;
												$ratio = (500 * 100) / $img[0];
												$height = round(($img[1] * $ratio) / 100);
												
												$use_lightbox = true;
											}
											
											$file_url = make_url_relative(_ARTICLE_ATTACH_DIR . $new_name);
											$html = '<img src="'. $file_url .'" width="'. $width .'" height="'. $height .'"';
											$html .= ' vspace="" hspace="" border="0" alt="" />';
											
											if ($use_lightbox)
											{
												$html = '<a href="'. $file_url .'" rel="prettyPhoto[phpmelody]">'. $html .'</a>';
											}
											
											// @since 2.7.1 attempt to set file permissions 
											$stat = @stat(dirname($uploadFile));
											$perms = $stat['mode'] & 0000666;
											@chmod($uploadFile, $perms);
											@chmod($thumb_name, $perms);
											
											exit(json_encode(array( 'success' => true,
																	'alert_type' => 'success',
																	'msg' => '',
																	'html' => $html)));
										}
										else
										{
											$error = 'The uploaded file could not be moved.';
										}
									}
									
									if ($_POST['upload-type'] == 'logo')
									{
										$new_name = 'custom-logo' . $ext;
										
										if (is_writeable( ABSPATH . _UPFOLDER ))
										{
											$uploadFile = ABSPATH . _UPFOLDER .'/'. $new_name;
											$file_url = _URL .'/'. _UPFOLDER .'/'. $new_name;
										}
										else
										{
											$uploadFile = _THUMBS_DIR_PATH . $new_name;
											$file_url = _THUMBS_DIR . $new_name;
										}
										
										$move = @move_uploaded_file($file['tmp_name'], $uploadFile);
										if ($move !== false)
										{
											$img_metadata = getimagesize($uploadFile); // 0 = width, 1 = height, 2 = tyoe, 3 = attr
											
											$width = $img_metadata[0];
											$height = $img_metadata[1];
											$html = '';
											
											if ($img_metadata[0] > 500)
											{
												$width = 500;
												$ratio = (500 * 100) / $img_metadata[0];
												$height = round(($img_metadata[1] * $ratio) / 100);
											}
											
											$file_url = make_url_relative($file_url);
											$html = '<img src="'. $file_url .'?cachebuster='. time() .'" width="'. $width .'" height="'. $height .'"';
											$html .= ' vspace="" hspace="" border="0" alt="" />';
											$html .= '<input type="hidden" name="custom_logo_url" value="'. $file_url .'" />';
											
											// @since 2.7.1 attempt to set file permissions 
											$stat = @stat(dirname($uploadFile));
											$perms = $stat['mode'] & 0000666;
											@chmod($uploadFile, $perms);
											
											exit(json_encode(array( 'success' => true,
																	'alert_type' => 'success',
																	'msg' => '',
																	'html' => $html)));
										}
										else
										{
											$error = 'The uploaded file could not be moved.';
										}
									}
				
									if ($_POST['upload-type'] == 'video-thumb')
									{
										$img = new resize_img();
										$img->sizelimit_x = THUMB_W_VIDEO;
										$img->sizelimit_y = THUMB_H_VIDEO;
										$img->keep_proportions = true;
										$img->output = 'JPG';
										
										$uniq_id = $_POST['uniq_id'];
										
										if(empty($uniq_id)) 
										{
											$uniq_id = substr(md5($_POST['uniq_id'] . time()), 1, 8);
										}
										//$new_name = substr(md5($_POST['uniq_id'] . time()), 1, 8)."-1";
										$new_name = $uniq_id . '-1';
										$file_url = _THUMBS_DIR . $new_name . '.jpg';
										
										//	resize image and save it
										if ($img->resize_image($file['tmp_name']) === false)
										{
											exit(json_encode(array( 'success' => false,
																	'alert_type' => 'error',
																	'msg' => $img->error,
																	'html' => pm_alert_error($img->error, array('id' => '_error_')))));
										}
										else
										{
											$img->save_resizedimage(_THUMBS_DIR_PATH, $new_name);
											$html = '<img id="show-thumb" class="show-thumb-temp" src="'. $file_url .'?cachebuster='. time() .'" width="" height=""';
											$html .= ' vspace="" hspace="" border="0" alt="" />';
											$html .= '<input type="hidden" name="yt_thumb_local" value="'. $file_url .'" />';
											
											// @since 2.7.1 attempt to set file permissions 
											$stat = @stat(dirname(_THUMBS_DIR_PATH . $new_name .'.jpg'));
											$perms = $stat['mode'] & 0000666;
											@chmod(_THUMBS_DIR_PATH . $new_name .'.jpg', $perms);
											
											generate_social_thumb(_THUMBS_DIR_PATH . $new_name .'.jpg');
										}
										
										exit(json_encode(array( 'success' => true,
																'alert_type' => 'success',
																'msg' => '',
																'html' => $html)));
									}
									
									if ($_POST['upload-type'] == 'category-image')
									{
										$img = new resize_img();
										$img->sizelimit_x = 320; 
										$img->sizelimit_y = 180;
										$img->keep_proportions = true;
										$img->output = 'JPG';
										
										$category_id = (int) $_POST['cat_id'];
										
										$random_str = substr(md5($category_id . time()), 0, 11);
										$file_url = _THUMBS_DIR . $random_str . '.jpg';
										
										//	resize image and save it
										if ($img->resize_image($file['tmp_name']) === false)
										{
											exit(json_encode(array( 'success' => false,
																	'alert_type' => 'error',
																	'msg' => $img->error,
																	'html' => pm_alert_error($img->error, array('id' => '_error_')))));
										}
										else
										{
											$img->save_resizedimage(_THUMBS_DIR_PATH, $random_str);
											$html = '<img id="show-cat-cover" class="show-thumb-temp" src="'. $file_url .'?cachebuster='. time() .'" width="" height=""';
											$html .= ' vspace="" hspace="" border="0" alt="" />';
											$html .= '<input type="hidden" name="image" value="'. $random_str .'.jpg" />';
										}
										
										// @since 2.7.1 attempt to set file permissions 
										$stat = @stat(dirname(_THUMBS_DIR_PATH . $random_str .'.jpg'));
										$perms = $stat['mode'] & 0000666;
										@chmod(_THUMBS_DIR_PATH . $random_str .'.jpg', $perms);
										
										exit(json_encode(array( 'success' => true,
																'alert_type' => 'success',
																'msg' => '',
																'html' => $html)));
									}
								}
								else
								{
									$error = 'File is empty. This error could also be caused by uploads being disabled in your php.ini.';
								}
							}
							else
							{
								$error = $upload_errors[$file['error']];
							}
						}
						else
						{
							$error = 'Bad file type. Supported file types: <code>'. implode(', ', $whitelist) .'</code>';
						}
					}
					else
					{
						$error = 'Select an image first.';
					}
				}
				else if ( ! $logged_in)
				{
					$error = 'You need to log in first.';
				}
				else
				{
					$error = 'Access denied';
				}
				
				if (strlen($error) > 0)
				{
					exit(json_encode(array( 'success' => false,
											'alert_type' => 'error',
											'msg' => $error,
											'html' => pm_alert_error($error, array('id' => '_error_')))));
				}
				
			break; // case 'upload-image'
			
			case 'upload-file':  // moved from upload_file.php @since v2.7
				
				$error_msg = '';
				$allow = 1;
				$ext = pm_get_file_extension($_FILES['file']['name'], true);
				
				if ( ! $conn_id)
				{
					if ( ! ($conn_id = db_connect()))
					{
						$allow = 0;
					}
				}
				
				if ($_POST['upload-type'] == 'subtitle')
				{
					$allowed_ext 	= array('.vtt', '.srt');
					$uploadDir 	= _SUBTITLES_DIR_PATH;
				}
				else if ($_POST['upload-type'] == 'video')
				{
					$allowed_ext 	= array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', 
											'.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');
					$uploadDir 	= _VIDEOS_DIR_PATH;
				}
				else if ($_POST['upload-type'] == 'csv')
				{
					$allowed_ext 	= array('.csv', '.txt');
					$uploadDir 	= _VIDEOS_DIR_PATH;
				}
				else // Uploading media form 'ADD VIDEO' modal
				{
					$allowed_ext 	= array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', 
											'.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');
					$uploadDir 	= _VIDEOS_DIR_PATH;
				}
				
				// deprecated:
				$allowed_type = array(  // video, audio 
										'video/x-flv', 	'video/quicktime', 'video/x-msvideo', 
										'video/x-divx', 'video/mp4', 'video/x-ms-wmv', 
										'application/octet-stream',  'video/avi', 'video/x-matroska',
										'video/x-ms-asf', 'audio/x-ms-wma',	'audio/mp4', 'video/3gpp', 
										'video/3gpp2', 'audio/mpeg', 'video/mpeg', 'application/force-download', 
										'audio/mp3', 'audio/mpeg3', 'video/x-m4v', 'audio/x-m4a',
										// subtitles
										'text/vtt','text/srt',
										// csv, txt
										'text/csv', 'text/csv-schema', 'text/plain', 'text/comma-separated-values', 'application/vnd.ms-excel');
				
				$uploadFile = $uploadDir . basename($_FILES['file']['name']);
				
				if ( ! in_array($ext, $allowed_ext))
				{
					$uploadFile = str_replace($ext, '.flv', $uploadFile);
					$allow = 0;
					
					$error_msg = 'Bad file type. Supported file types: <code>'. implode(', ', $allowed_ext) .'</code>';
				}
				
				if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
				{
					$allow = 0;
					$error_msg = 'You do not have permission to upload videos.';
				}
				
				if (is_moderator() && mod_cannot('manage_videos'))
				{
					$allow = 0;
					$error_msg = 'You do not have permission to manage and upload videos.';
				}
				
				if ( ! is_array($_FILES['file']) || $_FILES['file']['size'] == 0)
				{
					$allow = 0;
					$error_msg = 'File is empty. This error could also be caused by uploads being disabled in your php.ini.';
				}
				
				if( $_POST['upload-type'] == 'subtitle' && empty($_POST['language']) )
				{
					$allow = 0;
					$error_msg = 'Please select the LANGUAGE before uploading your subtitle file.';
				}

				if ($allow == 1 && $_FILES['file']['error'] != 0)
				{
					switch($_FILES['file']['error'])
					{
						case UPLOAD_ERR_INI_SIZE:
							$error_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini which is currently set at <strong>'. ini_get('upload_max_filesize') .'</strong>';
							break;
				
						case UPLOAD_ERR_FORM_SIZE:
							$error_msg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML/Flash upload form.';
							break;
				
						case UPLOAD_ERR_PARTIAL:
							$error_msg = 'The uploaded file was only partially uploaded. Possible cause: user cancelled the upload.';
							break;
				
						case UPLOAD_ERR_NO_FILE:
							$error_msg = 'No file was uploaded. Please select a file first.';
							break;
				
						case UPLOAD_ERR_NO_TMP_DIR:
							$error_msg = 'Missing a temporary folder. Please contact your hosting provider for this issue.';
							break;
				
						case UPLOAD_ERR_CANT_WRITE:
							$error_msg = 'Failed to write file to disk. Please contact your hosting provider for this issue.';
							break;
				
						case UPLOAD_ERR_EXTENSION:
							$error_msg = 'File upload stopped by extension. A PHP extension stopped the file upload. Can\'t tell which extension caused the file upload to stop.';
							break;
				
						default:
							$error_msg = 'Unknown upload error.';
							break;
					}
				
					$allow = 0;
				}

				$new_name  = md5($_FILES['file']['name'].time());
				$new_name  = substr($new_name, 0, 8);
				$new_name .= $ext;
				$uploadFile = $uploadDir . $new_name;
				
				if ($allow == 1)
				{
					if ($_POST['upload-type'] == 'csv')
					{
						if ( ! ini_get('auto_detect_line_endings')) 
						{
							ini_set('auto_detect_line_endings', '1');
						}
				
						include( ABSPATH . _ADMIN_FOLDER .'/class.csvimporter.php');
						
						$sql = "INSERT INTO pm_import_csv_files 
										(filename, upload_date, items_detected, items_processed, items_skipped, items_with_error, items_imported)
								VALUES  ('". secure_sql($_FILES['file']['name']) ."', ". time() .", 0, 0, 0, 0, 0)";
						
						if ( ! $result = mysql_query($sql))
						{
							$ajax_msg = 'There was an error while updating your database.<br />MySQL returned: '. mysql_error();
							exit(json_encode(array( 'success' => false,
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' => '')));
						}
						
						$file_id = (int) mysql_insert_id();		
						
						$total_items_detected = 0;
						$csv_headers = array('url', 'title', 'description', 'tags', 'thumb_url', 'duration');
						
						$importer = new CsvImporter($_FILES['file']['tmp_name'], $csv_headers);
						
						while ($data = $importer->get(100))
						{
							foreach ($data as $k => $item)
							{
								// discard blank lines	
								$all_empty = true;
								
								// ignore blank lines
								foreach ($item as $kk => $vv)
								{
									if (strlen($vv) > 0)
									{
										$all_empty = false; 
										break;
									}
								}
								
								if ($all_empty)
								{
									continue;
								}
								
								$sql = "INSERT INTO pm_import_csv_items 
												(file_id, video_title, description, yt_length, yt_thumb, direct, tags) 
										VALUES (". $file_id .", 
												'". secure_sql($item['title']) ."',
												'". secure_sql($item['description']) ."',
												'". secure_sql($item['duration']) ."',
												'". secure_sql($item['thumb_url']) ."',
												'". secure_sql($item['url']) ."',
												'". secure_sql($item['tags']) ."'
												)";
								if ( ! $result = mysql_query($sql))
								{
									$ajax_msg = 'There was an error while updating your database.<br />MySQL returned: '. mysql_error();
									exit(json_encode(array( 'success' => false,
															'alert_type' => 'error',
															'msg' => $ajax_msg,
															'html' => '')));
								}
								
								$total_items_detected++;
							}
						}
						$importer->close_source_file();
						
						$sql = "UPDATE pm_import_csv_files 
								   SET items_detected = $total_items_detected 
								WHERE file_id = ". $file_id;
						
						if ( ! $result = mysql_query($sql))
						{
							$ajax_msg = 'There was an error while updating your database.<br />MySQL returned: '. mysql_error();
							exit(json_encode(array( 'success' => false,
													'alert_type' => 'error',
													'msg' => $ajax_msg,
													'html' => '')));
						}
						
						@unlink($_FILES['file']['tmp_name']);
						
						$html = pm_alert_success( secure_sql($_FILES['file']['name']) .' was successfully uploaded. This CSV appears to have '. pm_number_format($total_items_detected) .' entries.');
						$html .= '<div class="pm-file-action"><a href="'. _URL .'/'. _ADMIN_FOLDER .'/import-csv.php?step=2&file-id='. $file_id .'" class="btn">Continue</a></div>';
						
						exit(json_encode(array( 'success' => true,
												'alert_type' => 'success',
												'msg' => '',
												'html' => $html)));
					}
					
					$move = @move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile);
					
					if ($move !== false)
					{
						if ($_POST['upload-type'] == 'subtitle')
						{
							if ($_POST['uniq_id'] != '')
							{
								$languages = a_get_languages();
				
								$sql = "SELECT * FROM pm_video_subtitles
										WHERE language_tag = '". secure_sql($_POST['language']) ."'
										  AND uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
								$result = mysql_query($sql);
								$row = mysql_fetch_assoc($result);
								mysql_free_result($result);
				
								if (is_array($row))
								{
									$sql = "UPDATE pm_video_subtitles
											SET filename = '". $new_name ."'
											WHERE id = '". $row['id'] ."'";
									if ( ! mysql_query($sql))
									{
										$ajax_msg = 'There was an error while updating your database.<br />MySQL returned: '. mysql_error();
										exit(json_encode(array( 'success' => false,
																'alert_type' => 'error',
																'msg' => $ajax_msg,
																'html' => '')));
									}
				
									$removed = true;
									if ($row['filename'] != '' && file_exists(_SUBTITLES_DIR_PATH . $row['filename']))
									{
										$removed = unlink(_SUBTITLES_DIR_PATH . $row['filename']);
									}
				
									// reload subtitles list
									$html = '';
									$subtitles = a_get_video_subtitles($_POST['uniq_id']);
									foreach ($subtitles as $k => $sub)
									{
										$html .= '<li id="subtitle-'. $sub['id'] .'"><span class="pull-right">';
										$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _SUBTITLES_DIR . $sub['filename'] .'" title="Download file" target="_blank">Download</a></strong>';
										$html .= '<i class="icon-trash opac7"></i> <strong><a href="" title="Delete subtitle" data-sub-id="'. $sub['id'] .'" onclick="return delete_subtitle('. $sub['id'] .')">Delete</a></strong>';
										$html .= '</span>';
										$html .= '<strong>'. ucfirst($sub['language']) .'</strong>';
										if ($sub['filename'] == $new_name)
										{
											$html .= ' <span class="label label-info">updated</span>';
										}
										$html .= '</li>';
										$html .= '<input type="hidden" name="subtitle_id[]" value="'. $sub['id'] .'" />';
									}
				
									if ( ! $removed)
									{
										$html .= '<hr />';
										$html .= pm_alert_error('Could not remove <code>'. _SUBTITLES_DIR_PATH . $row['filename'] .'</code> from your server.', null, true);
									}
									
									exit(json_encode(array( 'success' => true,
															'alert_type' => 'success',
															'msg' => '',
															'html' => $html)));
								}
								else
								{
									$sql = "INSERT INTO pm_video_subtitles (uniq_id, language, language_tag, filename)
											VALUES ('". secure_sql($_POST['uniq_id']) ."', '". secure_sql($languages[$_POST['language']]) ."', '". secure_sql($_POST['language']) ."', '". $new_name ."')";
									if ( ! mysql_query($sql))
									{
										$ajax_msg = 'There was an error while updating your database.<br />MySQL returned: '. mysql_error();
										exit(json_encode(array( 'success' => false,
																'alert_type' => 'error',
																'msg' => $ajax_msg,
																'html' => '')));
									}
				
									// reload subtitles list
									$html = '';
									$subtitles = a_get_video_subtitles($_POST['uniq_id']);
									foreach ($subtitles as $k => $sub)
									{
										$html .= '<li id="subtitle-'. $sub['id'] .'"><span class="pull-right">';
										$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _SUBTITLES_DIR . $sub['filename'] .'" title="Download file" target="_blank">Download</a></strong>';
										$html .= '<i class="icon-trash opac7"></i> <strong><a href="" title="Delete subtitle" data-sub-id="'. $sub['id'] .'" onclick="return delete_subtitle('. $sub['id'] .')">Delete</a></strong>';
										$html .= '</span>';
										$html .= '<strong>'. ucfirst($sub['language']) .'</strong>';
										if ($sub['filename'] == $new_name)
										{
											$html .= ' <span class="label label-success">uploaded</span>';
										}
										$html .= '</li>';
									}
									
									exit(json_encode(array( 'success' => true,
															'alert_type' => 'success',
															'msg' => '',
															'html' => $html)));
								}
							}
							else
							{
								$error_msg = 'Missing video ID';
							}
						}
				
						if ($_POST['upload-type'] == 'video')
						{
							if ($_POST['uniq_id'] != '')
							{
								$sql = "SELECT url_flv 
										FROM pm_videos 
										WHERE uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
								if ($result = mysql_query($sql))
								{
									$row = mysql_fetch_assoc($result);
									mysql_free_result($result);
									
									$sql = "UPDATE pm_videos 
											SET url_flv = '". $new_name ."' 
											WHERE uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
											
									if ( ! mysql_query($sql))
									{
										$ajax_msg = 'There was an error while updating your database.<br />MySQL returned: '. mysql_error();
										exit(json_encode(array( 'success' => false,
																'alert_type' => 'error',
																'msg' => $ajax_msg,
																'html' => '')));
									}
									
									$removed = true;
									if ($row['url_flv'] != '' && file_exists(_VIDEOS_DIR_PATH . $row['url_flv']))
									{
										$removed = unlink(_VIDEOS_DIR_PATH . $row['url_flv']);
									}
				
									$html = '<span class="pull-right">';
									$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _VIDEOS_DIR . $new_name .'" title="Download file">Download</a></strong>';
									$html .= '</span>';
									$html .= '<strong>'. $new_name .'</strong> <span class="label label-success">updated</span>';
									
									if ( ! $removed)
									{
										$html .= '<hr />';
										$html .= pm_alert_error('Could not remove <code>'. _VIDEOS_DIR_PATH . $row['url_flv'] .'</code> from your server.');
									}
				
									exit(json_encode(array( 'success' => true,
															'alert_type' => 'success',
															'msg' => '',
															'html' => $html)));
								}
								else
								{
									$error_msg = 'Could not retrieve video data.';
								}
							}
							else
							{
								$error_msg = 'Missing video ID';
							}
						}
						else 
						{
							$uploadFile = str_replace("\\", "\\\\", $uploadFile);	// IIS path fix
				
							$result = update_config('last_video', $uploadFile);
							if (is_array($result))
							{
								$fp = @fopen('tmp.pm', "a");
								@fwrite($fp, $uploadFile);
								@fclose($fp);
							}
							
							exit(json_encode(array( 'success' => true,
													'alert_type' => 'success',
													'filename' => urlencode($_FILES['file']['name']),
													'msg' => '',
													'html' => $html)));
						}
					}
					else if ($move === FALSE)
					{
						$error_msg = 'Could not move uploaded file to <strong>'. $uploadDir .'</strong>.';
					}	
				}
				
				
				if ($error_msg != '')
				{
					if ($_POST['upload-type'] == 'video')
					{
						$log_msg = 'Failed to upload file <code>' . $_FILES['file']['name'] . '</code>. <br />Error issued:<br /> ';
				
						$log_msg .= '<i>' . $error_msg . '</i>';
				
						if (strpos($error_msg, "0 bytes") !== false)
						{
							$log_msg .= '<br />To upload files larger than <strong>' . readable_filesize(get_true_max_filesize()) . '</strong>,
											you need to increase your server\'s <strong>upload_max_filesize</strong> and <strong>upload_max_filesize</strong> limits.';
							$log_msg .= '<br />You can do it yourself by reading <a href="http://help.phpmelody.com/how-to-fix-the-video-uploading-process/" target="_blank">this how-to</a>, or by contacting your hosting provider.';
							$log_msg .= '<br />Meanwhile you can upload the video(s) with an FTP client into the <strong>/uploads/videos/</strong> folder and add them to your site using the "<a href="addvideo.php">Add Video from URL</a>" page.';
						}
				
						//log_error($log_msg, ($_POST['upload-type'] == 'video') ? 'Upload video' : 'Upload subtitle', 1);
					}
					else 
					{
						$log_msg = $error_msg;
					}
				
					if (file_exists($_FILES['file']['tmp_name']))
					{
						@unlink($_FILES['file']['tmp_name']);
					}
				
					//echo pm_alert_error($log_msg, array(), true);
					exit(json_encode(array( 'success' => false,
											'alert_type' => 'error',
											'msg' => $log_msg,
											'html' => '')));
				}
				
			break;
		}
		
	break; // case 'upload'
	default: 
		exit();
	break;
} // end switch ($page)

// always exit ajax requests
exit();