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
require_once(ABSPATH .'include/functions.php');
require_once(ABSPATH .'include/user_functions.php');
require_once(ABSPATH .'include/islogged.php');
require_once(ABSPATH .'include/emoji/autoload.php');
			
$emoji_client = new Emojione\Client(new Emojione\Ruleset());
$emoji_client->ascii = true;
$emoji_client->unicodeAlt = false;

$message = '';
$parent_obj = false;
$parent_obj_type = 'video';

if ( ($logged_in == 1) || ($logged_in == 0 && $config['guests_can_comment'] == 1))
{
	if (isset ($_POST['vid']))
	{
		$vid = secure_sql($_POST['vid']);
		if (strpos($vid, 'article-') !== false)
		{
			$vid = str_replace('article-', '', $vid);
			$vid = (int) $vid;
			
			if ( ! $vid)
			{
				exit(json_encode(array( 'cond' => false, 
										'alert_type' => 'error',
										'msg' => $lang['comment_msg4'],
										'html' => pm_alert_warning($lang['comment_msg4']))));
			}
			if ( ! function_exists('get_article'))
			{
				require_once('include/article_functions.php');
			}
			
			$parent_obj = get_article($vid);
			$parent_obj_type = 'article';
			$vid = secure_sql($_POST['vid']); 
		}
		else
		{
			$parent_obj = request_video($vid);
		}
		
		if ( ! $parent_obj) // invalid ID or non-existing video/article
		{
			exit(json_encode(array( 'cond' => false, 
									'alert_type' => 'error',
									'msg' => $lang['comment_msg4'],
									'html' => pm_alert_warning($lang['comment_msg4']))));
		}
		
		if ((int) $parent_obj['allow_comments'] == 0)
		{
			exit(json_encode(array( 'cond' => false, 
									'alert_type' => 'error',
									'msg' => $lang['comments_disabled'],
									'html' => pm_alert_warning($lang['comments_disabled']))));
		}
 
		if ($logged_in == 1)
		{
			$ip = secure_sql(pm_get_ip());
			$user = $userdata['username'];
			$user_id = $userdata['id'];
		}
		else
		{
			$ip = secure_sql(pm_get_ip());
			$user = trim($_POST['username']);
			$user = $emoji_client->toShort($user); // convert unicode to shortname
			$user = strip_tags($user);
			$user = specialchars($user, 1);
			$user = secure_sql($user);
			$user_id = 0;
			
			if ((int) $parent_obj['restricted'] == 1)
			{
				$ajax_msg = sprintf($lang['must_sign_in'], _URL."/login."._FEXT, _URL."/register."._FEXT);
				exit(json_encode(array( 'cond' => false, 
										'alert_type' => 'error',
										'msg' => $ajax_msg,
										'html' => pm_alert_warning($ajax_msg))));
			}
	
			if ($user == '')
			{
				exit (json_encode(array('cond' => false,
										'alert_type' => 'error', 
										'msg' => $lang['comment_msg1'],
										'html' => pm_alert_warning($lang['comment_msg1']))));
			}
			else
			{
				$sql = "SELECT username FROM pm_users WHERE power = '".U_ADMIN."'";
				$result = mysql_query($sql);
				$username_found = 0;
				if ($result)
				{
					$row = "";
					while ($row = mysql_fetch_assoc($result))
					{
						if (strcmp(strtolower($user), strtolower($row['username'])) == 0)
						{
							$username_found = 1;
						break;
						}
					}
				}
				if ($username_found)
				{
					exit(json_encode(array( 'cond' => false,
											'alert_type' => 'error', 
											'msg' => $lang['comment_msg7'],
											'html' => pm_alert_warning($lang['comment_msg7']))));
				}
				mysql_free_result($result);
			}
		}
		
		$added = time();
		// ** PREP THE COMMENT FOR MYSQL OR REMOVE IT IF IT'S SPAM ** //
		$comment = trim($_POST['comment_txt']);
		$comment = $emoji_client->toShort($comment); // convert unicode to shortname
		$comment = nl2br($comment);
		$comment = removeEvilTags($comment);
		
		if ($comment == '')
		{
			exit(json_encode(array( 'cond' => false, 
									'alert_type' => 'error', 
									'msg' => $lang['comment_msg2'],
									'html' => pm_alert_warning($lang['comment_msg2']))));
		}
		
		if (_STOPBADCOMMENTS == '1')
		{
			$comment = search_bad_words($comment);
		}
		
		$comment = word_wrap_pass($comment);
		
		if ($logged_in == 0)
		{
			//	Check captcha code
			include (ABSPATH ."include/securimage/securimage.php");
			$img = new Securimage();
			$valid_captcha = $img->check($_POST['captcha']);
			if (!$valid_captcha && $_POST['captcha'] != '')
			{
				exit(json_encode(array( 'cond' => false, 
										'alert_type' => 'error', 
										'msg' => $lang['register_err_msg1'],
										'html' => pm_alert_warning($lang['register_err_msg1']))));
			}
			elseif ($_POST['captcha'] == '')
			{
				exit(json_encode(array( 'cond' => false, 
										'alert_type' => 'error', 
										'msg' => $lang['type_captcha'],
										'html' => pm_alert_warning($lang['type_captcha']))));
			}
		}
		
		/*if ( ! in_array($userdata['power'], array(U_ADMIN, U_MODERATOR, U_EDITOR)))
		 {
		 // check for duplicate comments/spam
		 $query = @mysql_query("SELECT id FROM pm_comments WHERE uniq_id = '".$vid."' AND user_id = '".$user_id."' AND comment LIKE '".$comment."' AND user_ip = '".$ip."'");
		 $duplicate_rows = @mysql_num_rows($query);
		 }
		 else
		 {
		 $duplicate_rows = 0;
		 }
		 
		 if ($duplicate_rows >= 1)
		 {
		 $message = json_encode(array('cond' => false, 'msg' => $lang['comment_msg3']));
		 }
		 else if ($duplicate_rows == 0 && !empty($comment))*/
		// more annoying than necessary; commented since v1.9
		
		if ($comment != '')
		{
			$sql = "INSERT INTO pm_comments SET uniq_id = '".$vid."', username = '".$user."', comment = '".secure_sql($comment)."', user_ip = '".$ip."', added = '".$added."', user_id = '".$user_id."'";
			
			if ($userdata['power'] == U_ADMIN || $userdata['power'] == U_MODERATOR)
			{
				$sql .= ", approved = '1'";
			}
			else if ($config['comm_moderation_level'] == MODERATE_ALL)
			{
				$sql .= ", approved = '0'";
			}
			else if ( ($config['comm_moderation_level'] == MODERATE_GUESTS) && ($logged_in == 0))
			{
				$sql .= ", approved = '0'";
			}
			else
			{
				//	no moderation or the user is logged in;
				$sql .= ", approved = '1'";
			}
			
			$result = @mysql_query($sql);
			$new_comment_id = mysql_insert_id();
			
			if (!$result)
			{
				$message = json_encode(array('cond' => false, 
											 'alert_type' => 'error', 
											 'msg' => $lang['comment_msg4'],
											 'html' => pm_alert_warning($lang['comment_msg4'])));
				
			}
			else
			{
				if ( ( ($config['comm_moderation_level'] == MODERATE_ALL) || ( ($config['comm_moderation_level'] == MODERATE_GUESTS) && ($logged_in == 0))) && ($userdata['power'] != U_ADMIN && $userdata['power'] != U_MODERATOR))
				{
					$message = json_encode(array('cond' => true, 
												 'alert_type' => 'success', 
												 'msg' => $lang['comment_msg5'],
												 'html' => pm_alert_success($lang['comment_msg5'])));
				}
				else
				{
					$html = '';
					$comment_data = get_comment_list($vid, 1, true, 'added');
					$smarty->assign('comment_data', $comment_data[0]);
					$html = $smarty->fetch('comment-list-li-body.tpl');
					
					$message = json_encode(array('cond' => true, 
												 'alert_type' => 'success',
												 'preview' => true, 
												 'msg' => $lang['comment_msg6'],
												 'html' => pm_alert_success($lang['comment_msg6']),
												 'preview_html' => $html));
					if (_MOD_SOCIAL)
					{
						$object_data = array();
						
						if (!$logged_in)
						{
							$userdata = array('id' => 0, 
											  'username' => $user, 
											  'name' => $user, 
											  'avatar_url' => get_avatar_url()
											 );
							$object_data = array('guestname' => $user);
						}
						
						log_activity(array( 'user_id' => $userdata['id'], 
											'activity_type' => ACT_TYPE_COMMENT, 
											'object_id' => $new_comment_id, 
											'object_type' => ACT_OBJ_COMMENT, 
											'object_data' => $object_data, 
											'target_id' =>  $parent_obj['id'], 
											'target_type' => ($parent_obj_type == 'video') ? ACT_OBJ_VIDEO : ACT_OBJ_ARTICLE, 
											'target_data' => $parent_obj
											)
									);
						
						notify_user( ($parent_obj_type == 'video') ? username_to_id($parent_obj['submitted']) : $parent_obj['author'], 
									$userdata['id'], 
									ACT_TYPE_COMMENT, 
									array(  'from_userdata' => $userdata, 
											'object_type' => ($parent_obj_type == 'video') ? ACT_OBJ_VIDEO : ACT_OBJ_ARTICLE, 
											'object' => $parent_obj
										)
								   );
					}
				}
			}
			//	set a cookie to remember this guest
			if ($logged_in != 1 && $_COOKIE[COOKIE_AUTHOR] == '' || (strcmp($_COOKIE[COOKIE_AUTHOR], specialchars($user)) != 0))
			{
				setcookie(COOKIE_AUTHOR, specialchars($user), time() + COOKIE_TIME, COOKIE_PATH);
			}
		}
	}
	else
	{
		$message = json_encode(array('cond' => false, 
									 'alert_type' => 'error',
									 'msg' => $lang['comment_msg4'],
									 'html' => pm_alert_warning($lang['comment_msg4'])));
	}
}
else
{
	exit;
}
echo $message;
exit ();
?>