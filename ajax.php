<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | phpSugar, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: phpSugar (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2016 PhpSugar.com. All rights reserved.
// +------------------------------------------------------------------------+

define('PM_DOING_AJAX', true);

session_start();

@header('Content-Type: text/html; charset=UTF-8;');

require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

$illegal_chars = array(">", "<", "&", "'", '"', '*', '%');

$message = '';
$page	 = '';
$action  = '';

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
	exit();
}
$modframework->trigger_hook('ajax_top');
switch ($page)
{
	case 'comments':
		
		if (isset($_GET['do']))
		{
			switch ($action)
			{
				case 'show_comments':
					$modframework->trigger_hook('ajax_show_comments');
					
					$page 	 = (int) $_GET['page'];
					$uniq_id = $_GET['vid'];
					$uniq_id = str_replace($illegal_chars, "", $uniq_id);
					
					if ($page > 0 && preg_match('/([0-9a-zA-Z-]{5,20})/', $uniq_id) != 0)
					{
						$most_liked_comment  = false;
						
						$comment_list = get_comment_list($uniq_id, $page);
						$comment_count = count_entries('pm_comments', 'uniq_id', $uniq_id."' AND approved='1");
						
						if ($comment_count > 0)
						{
							$most_liked_comment = get_most_liked_comment($uniq_id);
							$most_liked_comment = (array) $most_liked_comment[0];
							
							if ($most_liked_comment['up_vote_count'] <= 2)
							{
								$most_liked_comment = false;
							}
							
							// remove duplicate
							if ($config['comment_default_sort'] == 'score' && $page == 1 && is_array($most_liked_comment))
							{
								unset($comment_list[0]);
							}
						}
						$smarty->assign('most_liked_comment', $most_liked_comment);
						
						$mod_can = mod_can();
				
						if ($userdata['power'] == U_ADMIN || ($userdata['power'] == U_MODERATOR && $mod_can['manage_comments']))
						{
							$smarty->assign('can_manage_comments', true);
						}
						else
						{
							$smarty->assign('can_manage_comments', false);
						}
						
						$smarty->assign('comment_list', $comment_list);
						$smarty->assign('comment_count', $comment_count);
						
						$comment_list_html = $smarty->fetch('comment-list.tpl');
						
						$comment_pagination_obj = '';
						if ($comment_count > $config['comments_page'])
						{
							$comment_pagination_obj = generate_comment_pagination_object($uniq_id, $page, $comment_count, $config['comments_page']);
						}
						$smarty->assign('comment_pagination_obj', $comment_pagination_obj);
						$comment_pagination_html = $smarty->fetch('comment-pagination.tpl');
						$modframework->trigger_hook('ajax_show_comments_output');
						echo $comment_list_html . "\n". $comment_pagination_html;
						exit();
					}
					
				break;
				
				case 'onpage_delete_comment':
					$modframework->trigger_hook('ajax_onpage_delete_comment');
					$uniq_id 	= $_GET['vid'];
					$comment_id	= (int) $_GET['cid'];
					$uniq_id = str_replace($illegal_chars, "", $uniq_id);
					$uniq_id = secure_sql($uniq_id);
					
					if (is_admin() || (is_moderator() && mod_can('manage_comments')) 
						&& (preg_match('/([0-9a-zA-Z-]{5,20})/', $uniq_id) != 0)  
						&& $comment_id != 0
						)
					{
						if (_MOD_SOCIAL)
						{
							$sql = "SELECT id, uniq_id, user_id 
									FROM pm_comments WHERE id = '" . $comment_id . "'";
							if ($result = mysql_query($sql))
							{
								$row = mysql_fetch_assoc($result);
								$sql = "DELETE FROM pm_activity 
										WHERE user_id = '". $row['user_id'] ."' 
										  AND activity_type = '". ACT_TYPE_COMMENT ."'
										  AND object_id = '". $row['id'] ."' 
										  AND object_type = '". ACT_OBJ_COMMENT ."'";
								@mysql_query($sql);
								mysql_free_result($result);
							}
						}
						$sql = "DELETE 
								FROM pm_comments 
								WHERE id = '". $comment_id ."' 
								  AND uniq_id = '". $uniq_id ."' 
								LIMIT 1";
						$result = mysql_query($sql);
						
						$sql = "DELETE FROM pm_comments_reported WHERE comment_id = '". $comment_id ."'";
						$result = mysql_query($sql);
						
						$sql = "DELETE FROM pm_bin_rating_votes WHERE uniq_id = 'com-". $comment_id ."'";
						$result = mysql_query($sql);
					}
					exit();
				break;
				
				case 'like':
				case 'dislike':
				case 'upvote':
				case 'downvote':
				
					if ( ! is_user_logged_in())
					{
						echo json_encode(array('success' => false,
											   'msg' => $lang['login_first']
											  ));
						exit();
					}
	
					$response 	= array('success' => false, 'msg' => '');
					$comment_id = (int) $_GET['comment_id'];
					
					if ( ! $comment_id)
					{
						echo json_encode($response);
						exit();
					}
					
					// check if comment exists
					$sql = "SELECT uniq_id, user_id, approved, up_vote_count, down_vote_count, score 
							FROM pm_comments 
							WHERE id = '". $comment_id ."'";
					$result = mysql_query($sql);
					$comment_data = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ( ! $comment_data || $comment_data['approved'] != 1 || $userdata['id'] == $comment_data['user_id'])
					{
						echo json_encode($response);
						exit();
					}
					
					require_once('include/rating_functions.php');
					
					$uniq_id = 'com-'. $comment_id;
					$vote_value = 0;
							
					switch ($action)
					{
						case 'like':
						case 'upvote':
							$vote_value = 1;
						break;
					}
					
					if ( ! bin_rating_user_can_vote())
					{
						echo json_encode($response);
						exit();
					}
					
					$current_vote_value = bin_rating_user_has_voted($uniq_id);
					if ($current_vote_value === false && $vote_value < 0)
					{
						// shouldn't come to this but if it does,
						// we won't let the user delete something he doesn't own
						$response['success'] = true; // 'true' so it won't trigger any unnecessary errors
						echo json_encode($response);
						exit(); 
					}
					
					if ($current_vote_value === false) // new vote
					{
						$vote = bin_rating_insert_vote($uniq_id, $vote_value);
						$up_vote_count = ($vote_value) ? $comment_data['up_vote_count'] + 1 : $comment_data['up_vote_count'];
						$down_vote_count = ($vote_value) ? $comment_data['down_vote_count'] : $comment_data['down_vote_count'] + 1;
					}
					else
					{
						if ($current_vote_value != $vote_value) // user wants to change vote 
						{
							if ($vote_value < 0) // delete
							{
								$vote = bin_rating_delete_vote($uniq_id);
								$up_vote_count = ($current_vote_value) ? $comment_data['up_vote_count'] - 1 : $comment_data['up_vote_count'];
								$down_vote_count = ($current_vote_value) ? $comment_data['down_vote_count'] : $comment_data['down_vote_count'] - 1;
							}
							else // update
							{
								$vote = bin_rating_update_vote_value($uniq_id, $vote_value);
								
								if ($vote_value > 0)
								{
									$up_vote_count = $comment_data['up_vote_count'] + 1;
									$down_vote_count = $comment_data['down_vote_count'] - 1;
								}
								else
								{
									$up_vote_count = $comment_data['up_vote_count'] - 1;
									$down_vote_count = $comment_data['down_vote_count'] + 1;
								}
							}
						}
						else
						{
							$vote = bin_rating_delete_vote($uniq_id);
							$up_vote_count = ($current_vote_value) ? $comment_data['up_vote_count'] - 1 : $comment_data['up_vote_count'];
							$down_vote_count = ($current_vote_value) ? $comment_data['down_vote_count'] : $comment_data['down_vote_count'] - 1;
						}
					}
					
					if ($vote)
					{
						$score = bin_rating_calc_score($up_vote_count, $down_vote_count);
						$sql = "UPDATE pm_comments 
								SET up_vote_count = '". $up_vote_count ."',
									down_vote_count = '". $down_vote_count ."',
									score = '". $score ."' 
								WHERE id = '". $comment_id ."'";
						$result = mysql_query($sql);
						
						$response['up_vote_count'] = (int) $up_vote_count;
						$response['down_vote_count'] = (int) $down_vote_count;
						$response['success'] = true;
					}
					else
					{
						// error
					}

					echo json_encode($response);
					exit();
					
				break;
				case 'flag':
					
					if ( ! is_user_logged_in())
					{
						echo json_encode(array('success' => false,
											   'msg' => $lang['login_first']
											  ));
						exit();
					}
	
					$response 	= array('success' => false, 'msg' => '');
					$comment_id = (int) $_GET['comment_id'];
					
					if ( ! $comment_id)
					{
						echo json_encode($response);
						exit();
					}
					
					// check if user has already flagged this comment
					$flagged = user_has_flagged_comment($comment_id);
					
					if ($flagged)
					{
						// remove flag
						$sql = "DELETE FROM pm_comments_reported  
								WHERE user_id = '". $userdata['id'] ."' 
							  	  AND comment_id = '". $comment_id ."'";
						mysql_query($sql);
						
						// report_count--
						$sql = "UPDATE pm_comments SET report_count = report_count - 1
								WHERE id = '". $comment_id ."'";
						mysql_query($sql);
						
						$response['success'] = true; 
					}
					else
					{
						// raise flag
						$sql = "INSERT INTO pm_comments_reported
										(user_id, comment_id)  
								VALUES ('". $userdata['id'] ."', '". $comment_id ."')";
						mysql_query($sql);
						
						// report_count++
						$sql = "UPDATE pm_comments SET report_count = report_count + 1
								WHERE id = '". $comment_id ."'";
						mysql_query($sql);
						$response['success'] = true;
					}
					
					echo json_encode($response);
					exit();
				break;
				
				default:
					exit();
				break;
			}
		}
	break;
	
	case 'video':
		
		switch ($action)
		{
			case 'request':
				$modframework->trigger_hook('ajax_request_video');
				$video_id 	= $_GET['vid'];
				$video_id 	= str_replace($illegal_chars, "", $video_id);
				$video 		= request_video($video_id, 'detail', true);
				
				if ( ! $video && (is_admin() || (is_moderator() && mod_can('manage_videos'))))
				{
					// check the Trash
					$video = request_video($video_id, 'detail', true, true);
				}
				
				if ( ! is_user_logged_in() && $video['restricted'] == '1')
				{
					$smarty->assign('lang', $lang);
					$smarty->display('restricted_video.tpl');
					exit();
				}

				if ( ! is_array($video))
				{
					exit();
				}
				
				echo $video['embed_code'];
			break;
			
			case 'report':
				$modframework->trigger_hook('ajax_report_video');
				
				$video_id 	= $_GET['vid'];
				$video_id 	= str_replace($illegal_chars, "", $video_id);
				
				if (preg_match('/([0-9a-zA-Z-]{5,20})/', $video_id) != 0)
				{
					switch (strtolower($_GET['error-message']))
					{
						// videoJS
						case 'playback disabled by owner':
						case 'flash: srcnotfound':
						//case 'the video could not be loaded, either because the server or network failed or because the format is not supported':
						
						// JW Player file related
						case 'error loading media: file not found':
						case 'error loading media: file could not be played':
						
						// JW Player Youtube related 
						case 'error loading youtube: video removed or private':
						case 'error loading youtube: video could not be played': // JW Player 7 default error message
						//case 'error loading youtube: embedding not allowed': // might report false positives for geo restricted videos
						
							$video = request_video($video_id, 'detail', true);
							
							if (is_array($video))
							{
								$message = ($_GET['error-message'] == 'FLASH: srcnotfound') ? 'Error loading media: File not found' : $_GET['error-message']; 
								
								report_video($video['uniq_id'], '1', secure_sql($message), 'PM Bot');
							}
							
						break;
					}
				}

			break;
			
			case 'like':
			case 'upvote':
			case 'dislike':
			case 'downvote':
				
				$allow_anon = (int) get_config('bin_rating_allow_anon_voting');
					
				if ( ! $allow_anon && ! is_user_logged_in())
				{
					echo json_encode(array('success' => false,
										   'msg' => $lang['login_first']
										  ));
					exit();
				}

				require_once('include/rating_functions.php');
				
				$response 	= array('success' => false, 'msg' => '');
				$video_id 	= trim($_GET['vid']);
				
				if (preg_match('/([0-9a-zA-Z-]{5,20})/', $video_id) != 0)
				{
					$video = request_video($video_id, 'detail', true);
					if (is_array($video))
					{
						$vote_value = 0;
						
						switch ($action)
						{
							case 'like':
							case 'upvote':
								$vote_value = 1;
							break;
						}
						
						$voted = bin_rating_vote($video['uniq_id'], $vote_value);
						
						$response['success'] = true;
						$item_meta = bin_rating_get_item_meta($video['uniq_id']);
						$balance = bin_rating_calc_balance($item_meta['up_vote_count'], $item_meta['down_vote_count']);
						$response = array_merge($response, $balance, $item_meta);
					}
					else
					{
						$response['success'] = false;
						$response['msg'] = $lang['video_not_found'];
					}
				}
				else
				{
					$response['success'] = false;
					$response['msg'] = $lang['video_not_found'];
				}
				
				echo json_encode($response);
				exit();
			break;
			
			case 'getplayer': // called after a Pre-roll ad has finished running
			
				$uniq_id = trim($_GET['vid']);
				$ad_id = (int) $_GET['aid'];
				$player_page = trim($_GET['player']);
				$playlist_uniq_id = $_GET['playlist'];
				
				if ($player_page == '' || ! in_array($player_page, array('index', 'detail', 'favorites', 'embed')))
				{
					$player_page = 'detail';
				}
	
				if(strlen($uniq_id) < 10 && strlen($uniq_id) > 5)
				{
					if(!ctype_alnum($uniq_id))
						$uniq_id = '';
					else
						$uniq_id = secure_sql($uniq_id);
				}
				else
				{
					$uniq_id = '';
				}
				
				if ($uniq_id == '')
				{
					exit('Invalid video ID');
				}
				
				//	set ad delay cookie ?
				if (empty($_COOKIE[COOKIE_PREROLLAD]))
				{
					if ($config['total_preroll_ads'] > 0)
					{
						if ($config['preroll_ads_delay'] != 0)
						{
							setcookie(COOKIE_PREROLLAD, PREROLL_AD_HASH, time() + $config['preroll_ads_delay'], COOKIE_PATH);
						}
					}
				}
				
				if ($player_page == 'detail' && $playlist_uniq_id != '')
				{
					$playlist = get_playlist($playlist_uniq_id);
	
					if (($playlist['visibility'] == PLAYLIST_PRIVATE && $playlist['user_id'] != $userdata['id']) || $playlist['items_count'] == 0)
					{
						$playlist = false;
					}
					else
					{
						$playlist_items = playlist_get_items($playlist['list_id'], 0, $playlist['items_count'], $playlist['sorting']);
						
						// prev/next links
						foreach ($playlist_items as $k => $item)
						{
							if ($item['uniq_id'] == $uniq_id)
							{
								$total_items = count($playlist_items);
								$pos = ($k == 0) ? $total_items - 1 : $k - 1;
								$smarty->assign('playlist_prev_url', $playlist_items[$pos]['playlist_video_href']);
								
								$pos = ($k == ($total_items - 1)) ?  0 : $k + 1;
								$smarty->assign('playlist_next_url', $playlist_items[$pos]['playlist_video_href']);
								
								unset($pos, $total_items);
								
								break;
							}
						}
					}
					
					$smarty->assign('playlist', $playlist);
					$smarty->assign('playlist_items', $playlist_items);
				}
				
				$video = request_video($uniq_id, $player_page);
				
				if ( ! $video && (is_admin() || (is_moderator() && mod_can('manage_videos'))))
				{
					// check the Trash
					$video = request_video($uniq_id, $player_page, false, true);
				}
				
				$modframework->trigger_hook('ajax_request_player');
				if ($video['allow_embedding'] == 1)
				{
					$smarty->assign('embedcode', generate_embed_code($video['uniq_id'], $video, false, 'iframe'));
				}
				$smarty->assign('video_subtitles', (array) get_video_subtitles($video['uniq_id']));
				$smarty->assign('page', $player_page);
				$smarty->assign('video_data', $video);
				$smarty->assign('jwplayerkey', $config['jwplayerkey']);
				$smarty->assign('jwplayer7key', $config['jwplayer7key']);
				$html = $smarty->fetch('player.tpl');
				echo $html;
				
				exit();
			break;
			
			case 'edit-video': // moved here @since v2.7
			
				$max_filesize_bytes = $config['allow_user_uploadvideo_bytes'];
				$whitelist_img	  = array('jpg', 'gif', 'png', 'jpeg');
				$allowed_types_img = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg');
				$sources = fetch_video_sources();
				$errors = array();
				$nonce_name = substr(md5('_edit_video_form'.$_POST['form_id'].$userdata['id'].pm_get_ip()), 3, 8);
				$video_status = $_POST['video_status'];
				
				if ($video_status == 'pending')
				{
					$video = false;
					$video_id = (int) $_POST['vid'];
					
					if ($video_id)
					{
						$sql = "SELECT * FROM pm_temp 
								WHERE id = ". secure_sql($video_id);
						if ($result = mysql_query($sql))
						{
							$video = mysql_fetch_assoc($result);
							
							// normalize
							$video['submitted'] = $video['username'];
							$video['direct'] = $video['url'];
							$video['url_flv'] = $video['url'];
							$video['url_flv_raw'] = $video['url'];
							$video['image_url'] = ($video['thumbnail'] != '' && strpos($video['thumbnail'], 'http') !== 0 && strpos($video['thumbnail'], '//') !== 0) ? _THUMBS_DIR . $video['thumbnail'] : make_url_https($video['thumbnail']);
							$video['duration'] = sec2hms($video['yt_length']);
							$video['description'] = str_replace(array("<br>", "<br />"), "\n", $video['description']);
							
							mysql_free_result($result);
						}
					}
				}
				else
				{
					if ($video = request_video(trim($_POST['vid']))) // vid = 'uniq_id' in this case
					{
						$tags_arr = get_video_tags($video['uniq_id'], 1);
						 (count($tags_arr) > 0) ? implode(',', $tags_arr) : '';
						
						if(count($tags_arr) > 0)
						{
							foreach($tags_arr as $id => $v)
							{
								$video['tags'][] = $v['tag']; 
							}
							$video['tags'] = implode(',', $video['tags']);
						}
						
						$video['image_url'] = $video['preview_image'];
						$video['description'] = str_replace(array("<br>", "<br />"), "\n", $video['description']);
					}
				}
				
				if (( ! $video) || ($video['submitted'] != $userdata['username']) || $config['allow_user_edit_video'] != 1)
				{
					exit(json_encode(array( 'success' => false,
											'alert_type' => 'danger',
											'_pmnonce_t' => null,
											'msg' => $lang['access_denied'],
											'html' => '')));
				}
				
				$video_type = (in_array($sources[$video['source_id']]['source_name'], array('localhost', 'windows media player', 'quicktime', 'divx', 'mp3'))) ? 'uploaded' : 'suggested';
				
				if ($_POST['btn-pressed'] == 'delete')
				{
					if ($config['allow_user_delete_video'] != 1)
					{
						$errors[] = $lang['access_denied'];
					}
					else if ( ! csrfguard_validate_token(substr(md5('_edit_video_form'.$_POST['form_id'].$userdata['id'].pm_get_ip()), 3, 8), $_POST['_pmnonce_t_edit_video_form']))
					{
						$errors[] = $lang['upload_errmsg_badtoken'];
					}
					else 
					{
						$nonce = csrfguard_raw($nonce_name);
						
						if ($video_status == 'approved')
						{
							$sql = "INSERT INTO pm_videos_trash (id, uniq_id, video_title, description, yt_id, yt_length, yt_thumb, category, submitted_user_id, submitted, added, url_flv, source_id, language, age_verification, yt_views, site_views, featured, restricted, allow_comments, allow_embedding, video_slug, mp4, direct)
									VALUES ('". $video['id'] ."',
											'". $video['uniq_id'] ."', 
											'". secure_sql($video['video_title']) ."', 
											'". secure_sql($video['description']) ."', 
											'". $video['yt_id'] ."', 
											'". $video['yt_length'] ."', 
											'". $video['yt_thumb'] ."', 
											'". $video['category'] ."', 
											'". $video['submitted_user_id'] ."',
											'". $video['submitted'] ."', 
											'". $video['added_timestamp'] ."', 
											'". $video['url_flv_raw'] ."', 
											'". $video['source_id'] ."', 
											'". $video['language'] ."', 
											'". $video['age_verification'] ."', 
											'". $video['yt_views'] ."', 
											'". $video['site_views'] ."', 
											'". $video['featured'] ."', 
											'". $video['restricted'] ."', 
											'". $video['allow_comments'] ."',
											'". $video['allow_embedding'] ."',
											'". secure_sql($video['video_slug']) ."',
											'". secure_sql($video['mp4']) ."',
											'". secure_sql($video['direct']) ."')";
							
							if ($result = @mysql_query($sql))
							{
								$sql = "DELETE FROM pm_videos 
										WHERE id = ". $video['id'];
								$result = @mysql_query($sql);
								
								if ($result)
								{
									$sql = "DELETE FROM pm_videos_urls 
											WHERE uniq_id = '". $video['uniq_id'] ."'";
									$result = @mysql_query($sql);
								
									$video['category'] = trim($video['category'], ',');
									$sql = "UPDATE pm_categories SET total_videos = total_videos - 1 ";
									if ($video['added_timestamp'] <= time())
									{
										$sql .= ", published_videos = published_videos - 1 ";
										update_config('published_videos', $config['published_videos'] - 1);
									}
									$sql .= " WHERE id IN (". $video['category'] .")";
									
									$result = @mysql_query($sql);
									
									update_config('total_videos', $config['total_videos'] - 1);
									update_config('trashed_videos', $config['trashed_videos'] + 1);
									
									exit(json_encode(array( 'success' => true,
															'alert_type' => 'success',
															'_pmnonce_t' => null,
															'msg' => sprintf($lang['video_deleted'], $userdata['profile_videos_url']),
															'html' => '')));
								}
							}
						}
						else // pending
						{
							if ($video['source_id'] == 1)
							{
								if (file_exists(_VIDEOS_DIR_PATH . $video['url']) && strlen($video['url']) > 0)
								{
									@unlink(_VIDEOS_DIR_PATH . $video['url']);
								}
								if (file_exists(_THUMBS_DIR_PATH . $video['thumbnail']) && strlen($video['thumbnail']) > 0)
								{
									@unlink(_THUMBS_DIR_PATH . $video['thumbnail']);
								}
							}
							
							$sql = "DELETE FROM pm_temp 
									WHERE id = ". $video['id'];
							$result = @mysql_query($sql);
							
							exit(json_encode(array( 'success' => true,
													'alert_type' => 'success',
													'_pmnonce_t' => null,
													'msg' => sprintf($lang['video_deleted'], $userdata['profile_videos_url']),
													'html' => '')));
						}
					}
				}
				
				if ($_POST['btn-pressed'] == 'submit')
				{
					load_categories();
					
					if ( ! csrfguard_validate_token($nonce_name, $_POST['_pmnonce_t_edit_video_form']))
					{
						$errors[] = $lang['upload_errmsg_badtoken'];
					}
					else
					{
						$nonce = csrfguard_raw($nonce_name);
						
						$video_title = secure_sql($_POST['video_title']);
						$video_title = stripslashes($video_title);
						$video_title = str_replace( array("<", ">"), '', $video_title);
						$video_title = (empty($video_title)) ? $video['video_title'] : $video_title;
						
						$category_id = (int) $_POST['category'];
						if ($category_id <= 0 || ! array_key_exists($category_id, $_video_categories))
						{
							$category_id = $video['category'];
						}
					
						$description = trim($_POST['description']);
						$description = stripslashes($description);
						$description = nl2br($description);
						$description = str_replace(array("\r", "\n"), '', $description);
						$description = removeEvilTags($description);
						
						if(_STOPBADCOMMENTS == '1')
						{
							$description = search_bad_words($description);
						}
						$description = word_wrap_pass($description);
						
						$tags = removeEvilTags($_POST['tags']);
						
						$duration = $video['duration'];
						if ($_POST['duration'] != '')
						{
							$pieces = explode(':', $_POST['duration']);
							$pieces[0] = (int) $pieces[0];
							$pieces[1] = (int) $pieces[1];
					
							$duration = (int) ($pieces[0] * 60) + $pieces[1];
						}
						
						$source_id = $video['source_id'];
						$uploaded_thumb_filename = false;
						$uploaded_video_filename = false;
						
						if ($video_type == 'uploaded')
						{
							$img = $_FILES['capture'];
							
							// upload image and overwrite existing thumb
							$ext = pm_get_file_extension($img['name']);
					
							if (($img['size'] > 0 && $img['size'] <= $max_filesize_bytes) && strlen($img['name']) > 0 && $img['error'] == 0)
							{
								if (in_array($ext, $whitelist_img))
								{
									$ext = 'jpg'; // save as JPG
									if ($video_status == 'approved')
									{
										// overwrite current thumb
										$new_name = $video['uniq_id']. '-1.'. $ext;
									}
									else
									{
										do
										{
											$new_name  = md5($img['name'] . time());
											$new_name  = substr($new_name, 2, 10);
											$new_name .= '.'.$ext;
										} while (file_exists(_THUMBS_DIR_PATH . $new_name));
									}
									
									// test mime type @since 2.7.1
									$filetype = pm_check_filetype_and_ext($img['tmp_name'], $img['name'], $mimes = false);
									if (empty($filetype['ext']) || empty($filetype['type'])) 
									{
										// $error = 'This file type is not permitted for security reasons.';
									}
									else
									{
										$copy = copy($img['tmp_name'], _THUMBS_DIR_PATH . $new_name);
										if ($copy === TRUE)
										{
											$resize = resize_then_crop(_THUMBS_DIR_PATH . $new_name, _THUMBS_DIR_PATH . $new_name, THUMB_W_VIDEO, THUMB_H_VIDEO, "255", "255", "255", $allowed_types_img);
											
											if($resize != false)
											{
												$uploaded_thumb_filename = $new_name;
												
												if (strlen($img['tmp_name']) > 0)
												{
													@unlink($img['tmp_name']);
												}
												
												if ($video_status == 'pending' && $video['thumbnail'] != '' && file_exists(_THUMBS_DIR_PATH . $video['thumbnail']))
												{
													@unlink(_THUMBS_DIR_PATH . $video['thumbnail']);
												}
												
												$social_thumb_filename = str_replace('-1.', '-social.', $uploaded_thumb_filename);
												if ($video_status == 'approved' && $social_thumb_filename != '' && file_exists(_THUMBS_DIR_PATH . $social_thumb_filename))
												{
													@unlink(_THUMBS_DIR_PATH . $social_thumb_filename);
												}
												
												@chmod(_THUMBS_DIR_PATH . $new_name, 0644); // @since v2.7
											} 
										}
									}
								}
							}
							
							// new video file uploaded?
							$mysql_insert_id = (int) $_POST['temp_id'];
							if ($mysql_insert_id)
							{
								$sql = "SELECT *
										FROM pm_temp
										WHERE id = ". secure_sql($mysql_insert_id);
								if ($result = mysql_query($sql))
								{
									$row = mysql_fetch_assoc($result);
									mysql_free_result($result);
								}
								
								if (is_array($row) && count($row) > 0)
								{
									if ($row['user_id'] == $userdata['id'])
									{
										$uploaded_video_filename = $row['url'];
										$source_id = 1;
										
										// delete row from pm_temp
										$sql = "DELETE FROM pm_temp
												WHERE id = ". $row['id'];
										$result = @mysql_query($sql);
									}
								}
							}
						}
						else
						{
							$direct = $video['direct'];
							
							if ($_POST['direct'] != $video['direct'] && trim($_POST['direct']) != '')
							{
								include_once(ABSPATH . _ADMIN_FOLDER . '/functions.php');
								
								$url = expand_common_short_urls(trim($_POST['direct']));
								
								if (is_url($url) || is_ip_url($url))
								{
									$use_this_src = -1;
									
									foreach($sources as $src_id => $source)
									{
										if($source['source_name'] != 'localhost' && $source['source_name'] != 'other')
										{
											if(@preg_match($source['source_rule'], $url))
											{
												$use_this_src = $source['source_id'];
												break;
											}
										}
									}
									
									if ($use_this_src > -1)
									{
										if ( ! file_exists( ABSPATH . _ADMIN_FOLDER ."/src/" . $sources[ $use_this_src ]['source_name'] . ".php"))
										{
											$errors[] = $lang['suggest_msg5'];
										}
										else
										{
											require_once( ABSPATH . _ADMIN_FOLDER ."/src/" . $sources[ $use_this_src ]['source_name'] . ".php");
											
											$do_main = $sources[ $use_this_src ]['php_namespace'] .'\do_main';
											$do_main($fetched_video_data, $url);
											
											$source_id = $use_this_src;
											$url_flv = $fetched_video_data['url_flv'];
											$direct = $fetched_video_data['direct'];
											$mp4 = $fetched_video_data['mp4'];
											$yt_id = $fetched_video_data['yt_id'];
											
											unset($fetched_video_data);
										}
									}
									else
									{
										$errors[] = $lang['suggest_msg5'];
									}
								} 
								else 
								{
									$errors[] = $lang['suggest_msg3'];  
								}
							}
							
							$yt_thumb = trim($_POST['yt_thumb']);
							$ext = pm_get_file_extension($yt_thumb);
							if ($ext == 'webp')
							{
								// replace .webp and _webp from URLs for youtube image URLs shown in Chrome
								$yt_thumb = str_replace(array('_webp', '.webp'), array('', '.jpg'), $yt_thumb);
								$ext = 'jpg';
							}
							
							if ($yt_thumb != $video['yt_thumb'] && (strpos($yt_thumb, 'http') === 0 || strpos($yt_thumb, '//') === 0) && in_array($ext, $whitelist_img))
							{
								// overwrite current thumb
								if ($video_status == 'approved')
								{
									$download_thumb = $sources['localhost']['php_namespace'] .'\download_thumb';
									if ( ! function_exists($download_thumb))
									{
										require_once( ABSPATH . _ADMIN_FOLDER .'/src/localhost.php');
									}
									$img = $download_thumb($yt_thumb, _THUMBS_DIR_PATH, $video['uniq_id'], true);
									@generate_social_thumb($img);
								}
							}
							else
							{
								$yt_thumb = $video['yt_thumb'];
							}
						}
					}
					
					if (count($errors) == 0)
					{
						if ($video_status == 'approved')
						{
							$sql = "UPDATE pm_videos 
									   SET video_title = '". secure_sql($video_title) ."', 
									   	   description = '". secure_sql($description) ."',
										   yt_length = '". secure_sql($duration) ."', 
										   category = '". secure_sql($category_id) ."' ";
							
							if ($source_id)
							{
								$sql .= ", source_id = '". secure_sql($source_id) ."' ";
								$sql .= ", status = '0' ";
							}
							
							if ($url_flv)
							{
								$sql .= ", url_flv = '". secure_sql($url_flv) ."' ";
							}
							
							if ($uploaded_video_filename)
							{
								$sql .= ", url_flv = '". secure_sql($uploaded_video_filename) ."' ";
								$direct = $uploaded_video_filename;
							}
							
							if ($yt_thumb)
							{
								$sql .= ", yt_thumb = '". secure_sql($yt_thumb) ."' ";
							}
							
							if ($uploaded_thumb_filename)
							{
								$sql .= ", yt_thumb = '". secure_sql($uploaded_thumb_filename) ."' ";
							}
							
							$sql .= ($yt_id) ? ", yt_id = '". secure_sql($yt_id) ."' " : '';
							
							if ($direct)
							{
								$sql_urls = "UPDATE pm_videos_urls 
											    SET direct = '". secure_sql($direct) ."' 
											  WHERE uniq_id = '". secure_sql($video['uniq_id']) ."'";
								$result = @mysql_query($sql_urls);
							}
							
							// update category count
							if ($category_id != $video['category'])
							{
								$sql_category = "UPDATE pm_categories 
												    SET total_videos = total_videos + 1,
													    published_videos = published_videos + 1 
												  WHERE id = ". secure_sql($category_id);
								$result = @mysql_query($sql_category);
								
								$sql_category = "UPDATE pm_categories 
												    SET total_videos = total_videos - 1,
													    published_videos = published_videos - 1 
												  WHERE id = ". secure_sql($video['category']);
								$result = @mysql_query($sql_category);
							}
							
							$tags = explode(",", $tags);
					
							//	remove duplicate tags and 'empty' tags
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
					
							$tags_insert = array();
							foreach($tags as $k => $tag)
							{
								//	handle mistakes
								$tag = stripslashes(trim($tag));
								$tags[$k] = $tag;
								if($tag != '' && (strlen($tag) > 0))
								{
									//	new tags vs old tags
									$found = 0;
									$safe_tag = safe_tag($tag);
									
									foreach($tags_arr as $key => $arr)
									{
										if(in_array($safe_tag, $arr))
											$found++;
									}
									if($found == 0)
										$tags_insert[] = $tag;
								}
							}
							
							//	were there any tags changed or removed?
							$remove_tags = array();
							foreach($tags_arr as $tag_id => $tag)
							{
								if(in_array($tag['tag'], $tags) === false)
								{
									$remove_tags[] = $tag['tag_id'];
								} 
							}
							
							if(count($tags_insert) > 0)
							{
								insert_tags($video['uniq_id'], $tags_insert);
							}
							
							if(count($remove_tags) > 0)
							{
								$sql_tags = "DELETE FROM pm_tags WHERE tag_id IN(". implode(",", $remove_tags) .")";
								$result = @mysql_query($sql_tags);
							}
						}
						else
						{
							$sql = "UPDATE pm_temp 
									   SET video_title = '". secure_sql($video_title) ."', 
									   	   description = '". secure_sql($description) ."',
										   yt_length = '". secure_sql($duration) ."', 
										   category = '". secure_sql($category_id) ."' ";
							
							$sql .= ($tags != $video['tags']) ? ", tags = '". secure_sql($tags) ."' " : '';
							$sql .= ($source_id) ? ", source_id = '". secure_sql($source_id) ."' " : '';
							
							if ($direct)
							{
								$sql .= ", url = '". secure_sql($direct) ."' ";
							}
							
							if ($uploaded_video_filename)
							{
								$sql .= ", url = '". secure_sql($uploaded_video_filename) ."' ";
							}
							
							if ($yt_thumb)
							{
								$sql .= ", thumbnail = '". secure_sql($yt_thumb) ."' ";
							}
							
							if ($uploaded_thumb_filename)
							{
								$sql .= ", thumbnail = '". secure_sql($uploaded_thumb_filename) ."' ";
							}
							
							if ($url_flv)
							{
								$sql .= ", url_flv = '". secure_sql($url_flv) ."' ";
							}
							
							$sql .= ($yt_id) ? ", yt_id = '". secure_sql($yt_id) ."' " : '';
							$sql .= ($mp4) ? ", mp4 = '". secure_sql($mp4) ."' " : '';
							
							$sql .= ($source_id) ? ", source_id = '". secure_sql($source_id) ."' " : '';
						}
						
						$sql .= " WHERE id = ". $video['id'];
						if ( ! $result = mysql_query($sql))
						{
							$errors[] = $lang['suggest_msg6'];
						}
						else
						{
							if ($uploaded_video_filename)
							{
								$previous_filename = ($video_status == 'approved') ? $video['url_flv_raw'] : $video['url'];
								
								if (strlen($previous_filename) > 0 && file_exists(_VIDEOS_DIR_PATH . $previous_filename))
								{
									@unlink(_VIDEOS_DIR_PATH . $previous_filename);
								}
							}
							
							exit(json_encode(array( 'success' => true,
													'alert_type' => 'success',
													'_pmnonce_t' => $nonce['_pmnonce_t'],
													'msg' => sprintf($lang['video_updated'], $userdata['profile_videos_url']),
													'html' => '')));
						}
					}
					
					if (count($errors) > 0)
					{
						$ajax_msg = '<ul class="subtle-list">';
						foreach ($errors as $k => $error_msg)
						{
							$ajax_msg .= '<li>'. $error_msg .'</li>';
						}
						$ajax_msg .= '</ul>';
						
						exit(json_encode(array( 'success' => false,
												'alert_type' => 'danger',
												'_pmnonce_t' => $nonce['_pmnonce_t'],
												'msg' => $ajax_msg,
												'html' => $ajax_msg)));
					}
				}
				
			break;
            case 'getRelated':
                $category = $_GET['category'];
                $videoId= $_GET['videoId'];
                $title= $_GET['title'];
                $tmp_parts = explode(',', $category);
                $related_video_list = get_related_video_list($tmp_parts, $title, $config['watch_related_limit'], $videoId);
                foreach ($related_video_list as $k => $vid)
                {
                    if ($vid['id'] == $videoId)
                    {
                        unset($related_video_list[$k]);
                    }
                }
                echo json_encode($related_video_list);
                exit();
                break;
            case 'getUserVideo':
                $user_id = $_GET['userId'];
                $videoId = $_GET['videoId'];

                $lists = smarty_get_user_videos($user_id);
                $result = [];
                foreach ($lists as $k => $vid)
                {
                    if ($vid['id'] != $videoId)
                    {
                        array_push($result,$lists[$k]);
                    }
                }

                echo json_encode($result);
                exit(); break;
			default:
				exit();
			break;
		}
		
		
	break;
	
	case 'favorites': // @deprecated since v2.2
	case 'playlists':
		
		if ($action != 'get-playlist-items' && $action != 'play-video' && $action != 'request')
		{
			if ( ! is_user_logged_in())
			{
				exit(json_encode(array( 'success' => false,
										'msg' => $lang['registration_req'],
										'html' => pm_alert_danger($lang['registration_req'], false, true))));
			}
		}
		
		switch ($action)
		{
			case 'video-watch-load-my-playlists':
 
				$video_id = $_GET['video-id'];
				
				$my_playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
				$my_playlists = get_user_playlists($userdata['id'], false, false, 0, $my_playlists_count);

				$playlist_ids = array();
				
				foreach ($my_playlists as $k => $playlist_data)
				{
					// remove 'History' and 'Liked' from this list
					if ($playlist_data['type'] == PLAYLIST_TYPE_HISTORY || $playlist_data['type'] == PLAYLIST_TYPE_LIKED)
					{
						unset($my_playlists[$k]);
						continue;
					}
					
					$playlist_ids[] = $playlist_data['list_id'];
				}
		
				$playlist_ids = playlist_has_video($playlist_ids, $video_id);
		
				if ($playlist_ids)
				{
					foreach ($my_playlists as $k => $playlist_data)
					{
						if (in_array($playlist_data['list_id'], $playlist_ids))
						{
							$my_playlists[$k]['has_current_video'] = true;
						}
					}
					unset($playlist_ids);
				}
				$smarty->assign('template_dir',$template_f);
				$smarty->assign('video_data', array('id' => $video_id));
				$smarty->assign('my_playlists', $my_playlists);
				$html = $smarty->fetch('video-watch-playlists.tpl');
				
				exit(json_encode(array( 'success' => true,
										'msg' => '',
										'html' => $html)));
			break;
			
			case 'update-playlist':

				$playlist = get_playlist_by_id((int) $_POST['playlist-id']);
				
				if ( ! $playlist)
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_not_found'],
											'html' => pm_alert_danger($lang['playlist_not_found'], false, true))));
				}
				
				if ($playlist['user_id'] != $userdata['id'])
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_msg_update_own'],
											'html' => pm_alert_danger($lang['playlist_msg_update_own'], false, true))));
				}
		
				$updated_playlist = $playlist;
				
				if (in_array($_POST['sorting'], array('default', 'popular', 'date-added-desc', 'date-added-asc', 'date-published-desc', 'date-published-asc')))
				{
					$updated_playlist['sorting'] = $_POST['sorting'];
				}

				if (in_array($_POST['visibility'], array(PLAYLIST_PUBLIC, PLAYLIST_PRIVATE)))
				{
					$updated_playlist['visibility'] = $_POST['visibility'];
				}
				
				if ($playlist['type'] != PLAYLIST_TYPE_WATCH_LATER && $playlist['type'] != PLAYLIST_TYPE_FAVORITES && $playlist['type'] != PLAYLIST_TYPE_LIKED && $playlist['type'] != PLAYLIST_TYPE_HISTORY)
				{
					$title = trim($_POST['title']);
					$title = htmlspecialchars_decode($title);
					
					if ($title != '')
					{
						$updated_playlist['title'] = $title;
					}
				}
				
				$updated = update_playlist($playlist['list_id'], $updated_playlist);

				exit(json_encode(array( 'success' => true,
										'msg' => $lang['playlist_msg_updated'],
										'html' => pm_alert_success('<i class="icon-ok icon-white"></i> '. $lang['playlist_msg_updated'], false, true))));
				
			break;
			
			case 'create-playlist':
				
				$user_playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
				$user_playlists_count = $user_playlists_count - 3; // default playlists are not taken into account
				
				if ($config['allow_playlists'] == 0)
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['disabled_feature'],
											'html' => pm_alert_danger($lang['disabled_feature'], false, true))));
				}
				
				if ($user_playlists_count >= $config['playlists_limit'])
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_msg_max_limit'], 
											'html' => pm_alert_danger($lang['playlist_msg_max_limit'], false, true))));
				}
				
				$playlist_data = array();
				
				$playlist_data['title'] = trim($_POST['title']);
				if ($playlist_data['title'] == '')
				{
					$playlist_data['title'] = date('F j, Y g:i A');
				}
				
				if (in_array($_POST['sorting'], array('default', 'popular', 'date-added-desc', 'date-added-asc', 'date-published-desc', 'date-published-asc')))
				{
					$playlist_data['sorting'] = $_POST['sorting'];
				}

				if (in_array($_POST['visibility'], array(PLAYLIST_PUBLIC, PLAYLIST_PRIVATE)))
				{
					$playlist_data['visibility'] = $_POST['visibility'];
				}
				
				$playlist_uniq_id = insert_playlist($userdata['id'], PLAYLIST_TYPE_CUSTOM, $playlist_data);
				
				if ($_POST['ui'] == 'video-watch')
				{
					if ( ! $playlist_uniq_id)
					{
						exit(json_encode(array( 'success' => false,
												'msg' => $lang['playlist_msg_create_error'], 
												'html' => pm_alert_danger($lang['playlist_msg_create_error'], false, true)))); 
					}
					
					$video_id = (int) $_POST['video-id'];
					$list_id = mysql_insert_id();
					
					playlist_add_item($list_id, $video_id);
					
					$my_playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
					$my_playlists = get_user_playlists($userdata['id'], false, false, 0, $my_playlists_count);
	
					$playlist_ids = array();
					
					foreach ($my_playlists as $k => $playlist_data)
					{
						// remove 'History' and 'Liked' from this list
						if ($playlist_data['type'] == PLAYLIST_TYPE_HISTORY || $playlist_data['type'] == PLAYLIST_TYPE_LIKED)
						{
							unset($my_playlists[$k]);
							continue;
						}
						
						$playlist_ids[] = $playlist_data['list_id'];
					}
			
					$playlist_ids = playlist_has_video($playlist_ids, $video_id);
			
					if ($playlist_ids)
					{
						foreach ($my_playlists as $k => $playlist_data)
						{
							if (in_array($playlist_data['list_id'], $playlist_ids))
							{
								$my_playlists[$k]['has_current_video'] = true;
							}
						}
						unset($playlist_ids);
					}

					$smarty->assign('video_data', array('id' => $video_id));
					$smarty->assign('my_playlists', $my_playlists);
					$html = $smarty->fetch('video-watch-playlists.tpl');
					
					exit(json_encode(array( 'success' => true,
											'playlist_uniq_id' => $playlist_uniq_id,
											'msg' => $lang['playlist_msg_created'], 
											'html' => pm_alert_success('<i class="icon-ok icon-white"></i> '. $lang['playlist_msg_created'], false, true),
											'html_content' => $html)));
				}
							
				exit(json_encode(array( 'success' => true,
										'playlist_uniq_id' => $playlist_uniq_id,
										'msg' => $lang['playlist_msg_created'],
										'html' => pm_alert_success('<i class="icon-ok icon-white"></i> '.$lang['playlist_msg_created'], false, true))));
			break;
			
			case 'delete-playlist':

				$playlist = get_playlist_by_id((int) $_POST['playlist-id']);
				
				if ( ! $playlist)
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_not_found'], 
											'html' => pm_alert_danger($lang['playlist_not_found'], false, true)))); 
				}
				
				if ($playlist['user_id'] != $userdata['id'])
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_msg_delete_own'], 
											'html' => pm_alert_danger($lang['playlist_msg_delete_own'], false, true)))); 
				}
				
				if ($playlist['type'] == PLAYLIST_TYPE_WATCH_LATER || $playlist['type'] == PLAYLIST_TYPE_FAVORITES || $playlist['type'] == PLAYLIST_TYPE_LIKED || $playlist['type'] == PLAYLIST_TYPE_HISTORY)
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_msg_cannot_delete'], 
											'html' => pm_alert_danger($lang['playlist_msg_cannot_delete'], false, true)))); 
				}
				
				delete_playlist($playlist['list_id']);
				
				exit(json_encode(array('success' => true, 
									   'msg' => '',
									   'html' => '')));

			break;

			case 'add-to-playlist': // refers to the playlist on video-watch.tpl 

				$playlist_id = (int) $_POST['playlist-id'];
				$video_id = (int) $_POST['video-id'];
				
				$playlist = get_playlist_by_id($playlist_id);
				
				if ($playlist['items_count'] >= $config['playlists_items_limit'])
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_msg_full'], 
											'html' => pm_alert_danger($lang['playlist_msg_full'], false, true)))); 
				}
				
				if ( ! $added = playlist_add_item($playlist_id, $video_id))
				{
					exit(json_encode(array( 'success' => false,
												'msg' => $lang['playlist_msg_update_error'], 
												'html' => pm_alert_danger($lang['playlist_msg_update_error'], false, true)))); 
				
				}
				
				if ($playlist['type'] == PLAYLIST_TYPE_FAVORITES && _MOD_SOCIAL)
				{
					$video = request_video(video_id_to_uniq_id($video_id));

					log_activity(array(
								'user_id' => $userdata['id'],
								'activity_type' => ACT_TYPE_FAVORITE,
								'object_id' => $video['id'],
								'object_type' => ACT_OBJ_VIDEO,
								'object_data' => $video
								)
							);
							
					notify_user(username_to_id($video['submitted']), 
								$userdata['id'],
								ACT_TYPE_FAVORITE, 
								array( 'from_userdata' => $userdata,
								 		'object_type'=> ACT_OBJ_VIDEO,
										'object' => $video
									  )
								);
				}
				
				// update playlists pane
				$my_playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
				$my_playlists = get_user_playlists($userdata['id'], false, false, 0, $my_playlists_count);
	
				$playlist_ids = array();
				
				foreach ($my_playlists as $k => $playlist_data)
				{
					// remove 'History' and 'Liked' from this list
					if ($playlist_data['type'] == PLAYLIST_TYPE_HISTORY || $playlist_data['type'] == PLAYLIST_TYPE_LIKED)
					{
						unset($my_playlists[$k]);
						continue;
					}
					
					$playlist_ids[] = $playlist_data['list_id'];
				}
		
				$playlist_ids = playlist_has_video($playlist_ids, $video_id);
		
				if ($playlist_ids)
				{
					foreach ($my_playlists as $k => $playlist_data)
					{
						if (in_array($playlist_data['list_id'], $playlist_ids))
						{
							$my_playlists[$k]['has_current_video'] = true;
						}
					}
					unset($playlist_ids);
				}

				$smarty->assign('video_data', array('id' => $video_id));
				$smarty->assign('my_playlists', $my_playlists);
				$html = $smarty->fetch('video-watch-playlists.tpl');
				
				exit(json_encode(array( 'success' => true,
										'msg' => $lang['playlist_msg_video_added'], 
										'html' => $html)));
				
				//<i class="icon-ok icon-white"></i> The video was added to your playlist
				
			break;
			
			case 'remove-from-playlist': // refers to the playlist on video-watch.tpl 
 			
				$playlist_id = (int) $_POST['playlist-id'];
				$video_id = (int) $_POST['video-id'];
				
				$playlist = get_playlist_by_id($playlist_id);
				
				if ( ! $deleted = playlist_delete_item($playlist_id, $video_id))
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_msg_update_error'], 
											'html' => pm_alert_danger($lang['playlist_msg_update_error'], false, true)))); 
				
				}
				
				if ($playlist['type'] == PLAYLIST_TYPE_FAVORITES && _MOD_SOCIAL)
				{
					
					$activity_id = get_activity_id(array('user_id' => $userdata['id'], 
														 'activity_type' => ACT_TYPE_FAVORITE, 
														 'object_id' => $video_id,
														 'object_type' => ACT_OBJ_VIDEO
														)
												  );
			
					if ($activity_id)
					{
						$activity_data = get_activity_data($activity_id);
						$video = request_video(video_id_to_uniq_id($video_id));
						
						cancel_notification(username_to_id($video['submitted']), 
											$userdata['id'],
											ACT_TYPE_FAVORITE, 
											$activity_data['time']);
						
						delete_activity($activity_id);
					}
				}
				
				// update playlists pane
				$my_playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
				$my_playlists = get_user_playlists($userdata['id'], false, false, 0, $my_playlists_count);
	
				$playlist_ids = array();
					
				foreach ($my_playlists as $k => $playlist_data)
				{
					// remove 'History' and 'Liked' from this list
					if ($playlist_data['type'] == PLAYLIST_TYPE_HISTORY || $playlist_data['type'] == PLAYLIST_TYPE_LIKED)
					{
						unset($my_playlists[$k]);
						continue;
					}
					
					$playlist_ids[] = $playlist_data['list_id'];
				}
		
				$playlist_ids = playlist_has_video($playlist_ids, $video_id);
		
				if ($playlist_ids)
				{
					foreach ($my_playlists as $k => $playlist_data)
					{
						if (in_array($playlist_data['list_id'], $playlist_ids))
						{
							$my_playlists[$k]['has_current_video'] = true;
						}
					}
					unset($playlist_ids);
				}
				
				$smarty->assign('video_data', array('id' => $video_id));
				$smarty->assign('my_playlists', $my_playlists);
				$html = $smarty->fetch('video-watch-playlists.tpl');
				
				exit(json_encode(array( 'success' => true,
										'msg' => $lang['playlist_msg_video_added'], 
										'html' => $html)));
				
			break;
			
			case 'remove-item': // refers to the playlist management page - playlist.php  
				
				$playlist_id = (int) $_POST['playlist-id'];
				$video_id = (int) $_POST['video-id'];
				
				if ( ! $playlist_id && ! $video_id)
				{
					exit();
				}
				
				$playlist = get_playlist_by_id($playlist_id);

				if ($playlist['user_id'] != $userdata['id'])
				{
					exit();
				}
				
				$deleted = playlist_delete_item($playlist_id, $video_id);
				
				exit();
				
			break;
			
			case 'request': // deprecated
			case 'play-video': // deprecated
				
				$modframework->trigger_hook('ajax_favorites_request');
				
				$video_id = 1;
				$uniq_id 	= $_GET['vid'];
				$uniq_id 	= str_replace($illegal_chars, '', $uniq_id);
				$video 		= request_video($uniq_id, 'playlist', true);
				$embed_code	= '';

				if ($video['video_player'] == 'jwplayer' || $video['video_player'] == 'jwplayer6' || $video['video_player'] == 'videojs')
				{
					$video_subtitles = array();
					$video_subtitles = get_video_subtitles($video['uniq_id']);
				}

				if ( ! is_array($video))
				{
					exit();
				}
				
				if ( ! is_user_logged_in() && $video['restricted'] == '1')
				{
					$smarty->assign('lang', $lang);
					$smarty->display('restricted_video.tpl');
					exit();
				}
				
				if ($video['video_player'] == 'flvplayer')
				{
					$embed_code  = '<embed src="'. _URL .'/players/flowplayer2/flowplayer.swf?config='; // @since v2.2
					$embed_code .= '{';
					$embed_code .= "embedded: true,
									showOnLoadBegin: true, 
									useHwScaling: false, 
									showStopButton: true, 
									menuItems: [false, false, true, true, true, false, false], 
									timeDisplayFontColor: '0x". _TIMECOLOR ."', 
									controlBarBackgroundColor: '0x". _BGCOLOR ."', 
									progressBarColor2: '0x000000', 
									progressBarColor1: '0xFFFFFF', 
									watermarkLinkUrl: '". _WATERMARKURL ."', 
									showWatermark: '". _WATERMARKSHOW ."', 
									watermarkUrl: '". _WATERMARKURL  ."', 
									controlsOverVideo: 'locked', 
									controlBarGloss: 'high', 
									useNativeFullScreen: true, 
									showPlayListButtons: false, 
									initialScale: 'fit', 
									hideControls: false, 
									loop: false, 
									bufferLength: 5, 
									startingBufferLength: 2, 
									autoBuffering: ". _AUTOBUFF .", 
									autoPlay: true, 
									baseURL: '', 
									useSmoothing: true,";
									
					$embed_code .= "playList: [ { overlayId: 'play', 
												  name: 'ClickToPlay'
												 }, 
												 {  linkWindow: '_blank', 
												 	linkUrl: '". _URL ."/watch.php?vid=". $video['uniq_id'] ."', 
													url: '". _URL ."/videos.php?vid=". $video['uniq_id'] ."', 
													name: '". rawurlencode($video['video_title']) ."',";
					if ( $video['source_id'] == 57 )
					{
					$embed_code .=					"type: 'mp3'";
					}
					$embed_code .=			"}]";
					$embed_code .= '}");';
					
					$embed_code .= '" width="'. $config['player_w_favs'] .'" height="'. $config['player_h_favs'] . '"';
					$embed_code .= ' scale="noscale" bgcolor="'. _BGCOLOR .'"';
					$embed_code .= ' type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" ';
					$embed_code .= ' allowNetworking="all" pluginspage="//www.macromedia.com/go/getflashplayer">';
					$embed_code .= '</embed>';
					$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
				}
				else if ($video['video_player'] == 'jwplayer')
				{
					//$embed_code  = '<embed src="'. _URL .'/jwplayer.swf" ';
					$embed_code  = '<embed src="'. _URL .'/players/jwplayer5/jwplayer.swf" '; // @since v2.2
					$embed_code .= ' width="'. $config['player_w_favs'] .'" height="'. $config['player_h_favs'] . '"';
					$embed_code .= ' scale="noscale" bgcolor="'. _BGCOLOR .'"';
					$embed_code .= ' type="application/x-shockwave-flash" allowFullScreen="true" ';
					$embed_code .= ' allowScriptAccess="always" wmode="transparent" ';
					$embed_code .= ' flashvars="';
					
					if ($video['source_id'] == 3)
					{
						$embed_code .= '&file='. urlencode($video['direct']);
						$embed_code .= '&type=youtube';
					}
					else if ($video['source_id'] == 0)
					{
						$embed_code .= '&file='. urlencode($video['jw_flashvars']['file']);
						$embed_code .= '&streamer='. urlencode($video['jw_flashvars']['streamer']);
						$embed_code .= ($video['jw_flashvars']['provider'] != '') ? '&provider='. $video['jw_flashvars']['provider'] : '';
						$embed_code .= ($video['jw_flashvars']['startparam'] != '') ? '&http.startparam='. $video['jw_flashvars']['startparam'] : '';
						$embed_code .= ($video['jw_flashvars']['loadbalance'] != '') ? '&rtmp.loadbalance='. $video['jw_flashvars']['loadbalance'] : '';
						$embed_code .= ($video['jw_flashvars']['subscribe'] != '') ? '&rtmp.subscribe='. $video['jw_flashvars']['subscribe'] : '';
					}
					else
					{
						$embed_code .= '&file='. urlencode(_URL ."/videos.php?vid=". $video['uniq_id']);
						$embed_code .= '&type=video';
					}
					$embed_code .= '&backcolor='. _BGCOLOR;
					$embed_code .= '&frontcolor='. _TIMECOLOR;
					$embed_code .= '&screencolor=000000';
					$embed_code .= '&bufferlength=5';
					$embed_code .= '&controlbar=over';
					$embed_code .= '&autostart=true';
					$embed_code .= '&logo='. urlencode(_WATERMARKURL);
					$embed_code .= '&link='. urlencode(_WATERMARKLINK);
					//$embed_code .= '&skin='. urlencode(_URL).'/skins/'._JWSKIN;
					$embed_code .= '&skin='. urlencode(_URL) .'/players/jwplayer5/skins/'. _JWSKIN; // @since v2.2
					$embed_code .= '&plugins=timeslidertooltipplugin-2';
					$embed_code .= '">';
					$embed_code .= '</embed>';
					$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
				}
				else if ($video['video_player'] == 'jwplayer6')
				{
					$jw_file = $video['url_flv'];
					
					if ($video['source_id'] == 3)
					{
						$jw_file = $video['direct'];
					}
					else if ($video['source_id'] == 0)
					{
						$jw_file = $video['jw_flashvars']['file'];
					}
					else
					{
						if (_SEOMOD)
						{
							$file_ext = pm_get_file_extension($video['url_flv_raw'], false);
							$jw_file = ($file_ext == 'flv') ?  _URL .'/videos.flv?vid='. $video['uniq_id'] : _URL .'/videos.mp4?vid='. $video['uniq_id'];
						}
					}
					
					$rtmp = '';
					$rtmp .= ($video['jw_flashvars']['provider'] != '') ? " provider: '". $video['jw_flashvars']['provider'] ."', " : '';
					$rtmp .= ($video['jw_flashvars']['startparam'] != '') ? " startparam: '". $video['jw_flashvars']['startparam'] ."', " : '';
					$rtmp .= ($video['jw_flashvars']['loadbalance'] != '') ? " loadbalance: ". $video['jw_flashvars']['loadbalance'] .", " : '';
					$rtmp .= ($video['jw_flashvars']['subscribe'] != '') ? " subscribe: ". $video['jw_flashvars']['subscribe'] .", " : '';
					$rtmp .= ($video['jw_flashvars']['securetoken'] != '') ? " securetoken: '". $video['jw_flashvars']['securetoken'] ."', " : '';
					$rtmp = rtrim($rtmp, ',');
					$rtmp = ($rtmp != '') ? 'rtmp: { '. $rtmp .'}, ' : '';
					
					$embed_code .= '<script type="text/javascript" src="'. _URL .'/players/jwplayer6/jwplayer.js"></script>';
					$embed_code .= '<script type="text/javascript">jwplayer.key="'.$config["jwplayerkey"].'";</script>';
					$embed_code .= '<script type="text/javascript">';
					$embed_code .= "
							var flashvars = {
								flashplayer : '". _URL ."/players/jwplayer6/jwplayer.flash.swf',
								file : '". $jw_file ."',
								$rtmp
								primary: 'flash',
								width: '". $config['player_w_favs'] ."',
								height: '". $config['player_h_favs'] ."',
								image: '". make_url_https($video['yt_thumb']) ."',
								logo: {file: '". _WATERMARKURL ."',link: '". _WATERMARKLINK ."'},
								autostart: 'true',
								tracks: [";

					if (count($video_subtitles) > 0)
					{
						foreach ($video_subtitles as $subtitle)
						{
							$embed_code .= 	'{ file: "'. $subtitle['filename'] .'", label: "'. $subtitle['language'] .'", kind: "subtitles" },';
						}
					}

					$embed_code .= "]

							};
							jwplayer('Playerholder').setup(flashvars);
						</script>";
					$embed_code .= '<div id="Playerholder"></div>';
					
					$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
				}
				else if ($video['video_player'] == 'videojs')
				{
					$embed_code = '';

					$jw_file = $video['url_flv'];
					
					if ($video['source_id'] == 3)
					{
						$jw_file = $video['direct'];
					}
					else if ($video['source_id'] == 0)
					{
						$jw_file = $video['jw_flashvars']['file'];
					}
					else
					{
						if (_SEOMOD)
						{
							$jw_file = _URL ."/videos.mp4?vid=". $video['uniq_id'];
						}
					}

					$embed_code .= '<div id="Playerholder">';
					$embed_code .= '<link href="'. _URL .'/players/video-js/video-js.min.css" rel="stylesheet">';
					$embed_code .= '<script type="text/javascript" src="'. _URL .'/players/video-js/video.js"></script>';
					$embed_code .= '<script type="text/javascript" src="'. _URL .'/players/video-js/plugins/youtube.js"></script>';

					if(_WATERMARKURL != '')
					{
						$embed_code .= '<script type="text/javascript" src="'. _URL .'/players/video-js/plugins/videojs.logobrand.js"></script>';
					}

					$embed_code .= '<video src="" id="video-js" class="video-js vjs-default-skin" poster="'. make_url_https($video['preview_image']) .'" preload="" data-setup=\'{ "techOrder": [';

					if ($video['source_id'] == 3)
					{
						$embed_code .= '"youtube",';
					}
					$embed_code .= '"html5","flash"], "controls": true }\' width="'. _PLAYER_W_EMBED .'" height="'. _PLAYER_H_EMBED .'">';


					if (count($video_subtitles > 0))
					{
						foreach ($video_subtitles as $subtitle)
						{
							$embed_code .= 	'<track kind="captions" src="'. $subtitle['filename'] .'" srclang="'. $subtitle['language_tag'] .'" label="'. $subtitle['language'] .'">';
						}
					}

					$embed_code .= '<script type="text/javascript">';
					$embed_code .= "var video = videojs('video-js').ready(function(){
									var player = this;";

					if (_WATERMARKURL != '')
					{
						$embed_code .= "player.logobrand({
											image: '". _WATERMARKURL ."',
											destination: '". _WATERMARKLINK ."'
										});";
					}

					if ($video['source_id'] == 0) // RTMP
					{
						$embed_code .= "player.src([
											{
												src: \"". $jw_file ."\", 
												type: \"rtmp/mp4\"
											}
										]);";
					}

					if($video['source_id'] == 1 || $video['source_id'] == 2)  // Remote 
					{
						$embed_code .= "player.src([
											{
												src: \"". _URL2 ."/videos.php?vid=". $video['uniq_id'] ."\", ";

							if($video['file_type'] != '') 
							{
								$embed_code .= 'type:' . $video['file_type'];
							} else {
								$embed_code .= 'type: "video/flv"';
							} 

						$embed_code .= "	}
										]);";
					}

					if ($video['source_id'] == 3) // Youtube
					{
						$embed_code .= "player.src(\"". $jw_file ."\");";
					}

					if($video['source_id'] == 16) // Vimeo
					{
						$embed_code .= "player.src([
											{
												src: \"". _URL2 ."/videos.php?vid=". $video['uniq_id'] ."\", 
												type: \"video/mp4\"
											}
										]);";
					}		

					if($video['source_id'] == 57) // Mp3
					{
						$embed_code .= "player.src([
											{
												src: \"". _URL2 ."/videos.php?vid=". $video['uniq_id'] ."\", 
												type: \"audio/mp3\"
											}
										]);";
					}					
					$embed_code .= "});</script></div>";
					$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
				}
				else
				{
					$embed_code = $video['embed_code'];	
				}
				$modframework->trigger_hook('ajax_favorites_request_output');
				update_view_count($video['id'], $video['site_views']);
				
				echo $embed_code;
				
			break;
			
			case 'watch-later-add':
				
				$video_id = (int) $_POST['video-id'];
				
				if ( ! $video_id)
				{
					exit(json_encode(array( 'success' => false,
											'msg' => 'Invalid video id', 
											'html' => '')));
				}
				
				$sql = "SELECT COUNT(*) as total_found 
						FROM pm_videos 
						WHERE id = ". $video_id;
				$result = @mysql_query($sql);
				$row = @mysql_fetch_assoc($result);
				
				if ((int) $row['total_found'] > 0)
				{
					$playlist = get_user_playlist_watch_later($userdata['id']);
				
					if ($playlist['items_count'] >= $config['playlists_items_limit'])
					{
						exit(json_encode(array( 'success' => false,
												'msg' => $lang['playlist_msg_full'], 
												'html' => pm_alert_danger($lang['playlist_msg_full'], false, true)))); 
					}
					
					if ( ! $added = playlist_add_item($playlist['list_id'], $video_id))
					{
						exit(json_encode(array( 'success' => false,
												'msg' => $lang['playlist_msg_update_error'], 
												'html' => pm_alert_danger($lang['playlist_msg_update_error'], false, true)))); 
					}
				}
				
				exit(json_encode(array( 'success' => true,
										'msg' => '',
										'html' => '')));
				
			break;
			
			case 'watch-later-remove':
				
				$video_id = (int) $_POST['video-id'];
				
				if ( ! $video_id)
				{
					exit(json_encode(array( 'success' => false,
											'msg' => 'Invalid video id', 
											'html' => '')));
				}
				
				$sql = "SELECT list_id 
						FROM pm_playlists 
						WHERE user_id = ". $userdata['id'];
				$result = @mysql_query($sql);
				$playlist = @mysql_fetch_assoc($result);
				@mysql_free_result($result);
				
				if ( ! $deleted = playlist_delete_item($playlist['list_id'], $video_id))
				{
					exit(json_encode(array( 'success' => false,
											'msg' => $lang['playlist_msg_update_error'], 
											'html' => pm_alert_danger($lang['playlist_msg_update_error'], false, true)))); 
				
				}
				
				exit(json_encode(array( 'success' => true,
										'msg' => '',
										'html' => '')));
				
			break;
			
			default:
				exit();
			break;
		}
		
	break;
	
	case 'users':
	
		if (is_admin() || (is_moderator() && mod_can('manage_users')))
		{
			$user_id = (int) $_GET['uid'];
			$user_id = abs($user_id);
			
			if ($user_id == 0)
			{
				if ($config['guests_can_comment'] == 1)
				{
					exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Visitors cannot be banned. You can disable visitor commenting from your Admin Area.')));
				}
				
				exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'No user ID provided.')));
			}
			
			if ($user_id == $userdata['id'])
			{
				exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'You?')));
			}
	
			$banned = array();
					
			$sql = "SELECT * 
					FROM pm_users 
					WHERE id = '". $user_id ."'";
			$result = @mysql_query($sql);
			if ( !  $result)
			{
				log_error('MySQL Error: '. mysql_error() . '<br>File: '. __FILE__ .' on line '. __LINE__, 'User Management', '1');
				exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Could not ban account. Check the System Log for more details.')));
			}
			
			$user = mysql_fetch_assoc($result);
			mysql_free_result($result);
	
			$banned = banlist($user['id']);
			$span_id = (int) $_GET['spanid'];
			$html = '';
			
			if ($action == 'allow' && ! $banned)
			{
				$action = 'ban';
			}
			else if ($action == 'ban' && $banned['user_id'] == $user['id'])
			{
				$action = 'allow';
			}
			
			switch ($action)
			{
				case 'allow':
	
					if($banned['user_id'] == $user['id'])
					{
						$sql = "DELETE 
								FROM pm_banlist 
								WHERE user_id ='". $user['id'] ."'";
	
						$result = @mysql_query($sql);
						if ( ! $result)
						{
							log_error('MySQL Error: '. mysql_error() . '<br>File: '. __FILE__ .' on line '. __LINE__, 'User Management', '1');
							exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Could not ban account. Check the System Log for more details.')));
						}
						else
						{
							$response = array('success' => true, 
											  'msg' => 'Done',
											  'hide_label' => true
											  );
							exit(json_encode($response));
						}
					}
	
				break;
				
				case 'ban':
				
					if ($user['power'] == U_ADMIN)
					{
						exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Administrator accounts cannot be banned.')));
					}
					
					if ($banned['user_id'] == $user['id'])
					{
						exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'This account is already banned.'))); 
					}
					
				
					$sql = "INSERT INTO pm_banlist 
							SET user_id = '". $user['id'] ."', 
								reason = ''";
					$result = @mysql_query($sql);
					if ( ! $result)
					{
						log_error('MySQL Error: '. mysql_error() . '<br>File: '. __FILE__ .' on line '. __LINE__, 'User Management', '1');
						exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Could not ban account. Check the System Log for more details.'))); 
					}
					$response = array('success' => true, 
									  'msg' => $lang['user_account_banned'],
									  'show_label' => true
									  );
					exit(json_encode($response));
					
				break;
			
				default:
					exit();
				break;
			}
		}
		
		exit(json_encode(array('success' => false, 'msg' => 'Not allowed')));
		
	break;
	
	case 'detail': 
		
		switch ($action)
		{
			
			case 'share':
			case 'report':
				
				$uniq_id = trim($_POST['vid']);
				if(strlen($uniq_id) < 10 && strlen($uniq_id) > 5)
				{
					if(!ctype_alnum($uniq_id))
						$uniq_id = '';
					else
						$uniq_id = secure_sql($uniq_id);
				}
				else
				{
					$uniq_id = '';
				}
				
				if ($uniq_id == '')
				{
					echo json_encode(array('success' => false,
										    'msg' => 'Invalid video ID'
										  ));
					exit();
				}
				
				$video = request_video($uniq_id);
				
				if ( ! is_user_logged_in() && $video['restricted'] == '1')
				{
					echo json_encode(array('success' => false,
										    'msg' => $lang['registration_req']
										  ));
					exit ();
				}
				
				foreach ($_POST as $k => $v)
				{
					$v = str_ireplace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($v)); // @since v2.3
					$_POST[$k] = htmlspecialchars($v);
				}
				
				if ( ! is_user_logged_in())
				{
					// check captcha code
					include (ABSPATH ."include/securimage/securimage.php");
					$img = new Securimage();
					$valid = $img->check($_POST['imagetext']);
					if ( ! $valid)
					{
						echo json_encode(array('success' => false,
									'msg' => $lang['register_err_msg1']
								 ));
						exit ();
					}
				}
			
				$post_email = trim($_POST['email']);
				$post_name = secure_sql(trim($_POST['name']));
				$post_reason = secure_sql(trim($_POST['reason']));
				
				if ($action == 'share')
				{
					if ( ! is_real_email_address($post_email)) 
					{
						echo json_encode(array('success' => false,
										       'msg' => $lang['register_err_msg2']
										  	  ));
						exit();
					}
					
					// ** SENDING EMAIL ** //
					require_once("include/class.phpmailer.php");
					$mailsubject = sprintf($lang['mailer_subj5'], $post_name);
					$array_content[]=array("mail_from", $post_name);  
					$array_content[]=array("video_id", $video['uniq_id']);
					$array_content[]=array("video_name", $video['video_title']);
					$array_content[]=array("site_url", _URL);
					
					if(file_exists('./email_template/'.$_language_email_dir.'/email_send_to_friend.txt'))
					{
						$mail = send_a_mail($array_content, $post_email, $mailsubject, 'email_template/'.$_language_email_dir.'/email_send_to_friend.txt');
					}
					elseif(file_exists('./email_template/english/email_send_to_friend.txt'))
					{
						$mail = send_a_mail($array_content, $post_email, $mailsubject, 'email_template/english/email_send_to_friend.txt');
					}
					elseif(file_exists('./email_template/email_send_to_friend.txt'))
					{
						$mail = send_a_mail($array_content, $post_email, $mailsubject, 'email_template/email_send_to_friend.txt');
					}
					else
					{
						@log_error('Email template "email_send_to_friend.txt" not found!', 'Share Video', 1);
						$mail = TRUE;
					}
					if($mail !== TRUE)
					{
						@log_error($mail, 'Share Video', 1);
					}
					
					if (_MOD_SOCIAL && is_user_logged_in())
					{
						log_activity(array(
							'user_id' => $userdata['id'],
							'activity_type' => ACT_TYPE_SEND_VIDEO,
							'object_id' => $video['id'],
							'object_type' => ACT_OBJ_VIDEO,
							'object_data' => $video
							)
						);
					}
					
					echo json_encode(array('success' => true,
										   'msg' => $lang['share_msg1']
										   ));
				}
				else
				{
					report_video($uniq_id, 1, $post_reason, $post_name);
					
					echo json_encode(array('success' => true,
										   'msg' => $lang['report_msg2']
										   ));
				}

			break;
		}
		
	break;
	case 'suggest':
		
		$response = array('failed' => true);
			
		require_once(_ADMIN_FOLDER . '/functions.php');
		
		switch ($action)
		{
			case 'getdata':
				
				$url = expand_common_short_urls(trim($_POST['url']));

				if ($url == '')
				{
					// empty URL
					$response = array('failed' => true,
									  'message' => 'Video URL '. $lang['register_err_msg8']
									 );
				}
				else
				{
					if ( ! is_url($url) && ! is_ip_url($url))
					{
						// invalid URL given
						$response = array('failed' => true,
									 	  'message' => $lang['suggest_msg3']
									 );
					}
					else
					{
						$sources = a_fetch_video_sources();
						$use_this_src = -1;
						foreach($sources as $src_id => $source)
						{
							if($use_this_src > -1)
							{
								break;
							}
							else
							{
								if(@preg_match($source['source_rule'], $url))
								{
									$use_this_src = $source['source_id'];
								}
							}
						}
						
						if ($use_this_src > -1)
						{
							if ( ! file_exists( "./". _ADMIN_FOLDER ."/src/" . $sources[ $use_this_src ]['source_name'] . ".php"))
							{
								// reply as 'not a supported video source'
								$response = array('failed' => true, 
										 	  	  'message' => $lang['suggest_msg5']
										 	);
							}
							else
							{
								require_once( "./". _ADMIN_FOLDER ."/src/" . $sources[ $use_this_src ]['source_name'] . ".php");
								
								$do_main = $sources[ $use_this_src ]['php_namespace'] .'\do_main';
								$do_main($video_details, $url);
								
								//	Lookup this URL in the database, check for existence to avoid duplication.
								$sql = "SELECT COUNT(*) as total_results 
										  FROM pm_videos_urls 
										 WHERE direct = '". secure_sql($url) ."'"; 

								$result = mysql_query($sql);
								$row = mysql_fetch_assoc($result);
								mysql_free_result($result);
								
								if ($row['total_results'] > 0)
								{
									$response = array('failed' => true, 
										 	  		  'message' => $lang['suggest_msg1']
										 		);
									break;
								}
								unset($sql, $result, $row);
								
								$sql = "SELECT COUNT(*) as total_results 
										  FROM pm_temp 
										 WHERE url = '". secure_sql($url) ."'";
								$result = mysql_query($sql);
								$row = mysql_fetch_assoc($result);
								mysql_free_result($result);
								
								if ($row['total_results'] > 0)
								{
									$response = array('failed' => true, 
										 	  		  'message' => $lang['suggest_msg2']
										 		);
									break;
								}
								
								$video_details['source_id'] = $use_this_src;
								$video_details['yt_thumb'] = make_url_https($video_details['yt_thumb']);
								
								$response = array('success' => true,
												  'videodata' => $video_details
											);
							}
						}
						else
						{
							// not a supported video source
							$response = array('failed' => true, 
									 	  	  'message' => $lang['suggest_msg5']
									 	);
						}
					}
				}
					
			break;
			
			case 'submitvideo':
				
				$required_fields = array('yt_id' => 'URL',
										 'category' => $lang['category'],
										 'video_title' => $lang['video'], 
								   );
				foreach( $_POST as $key => $value) 
				{
					$value = trim($value);
					if (array_key_exists(strtolower($key), $required_fields) && empty($value))
						$errors[$key] = $required_fields[$key] .' '. $lang['register_err_msg8'];
				}
				
				if ($_POST['category'] == '-1') 
				{
					$errors['category'] = $lang['choose_category'];
				}
				
				$url = expand_common_short_urls(trim($_POST['yt_id']));
				
				$sources = a_fetch_video_sources();
				$use_this_src = $source_id = (int) $_POST['source_id'];
				$modframework->trigger_hook('suggest_validate');
				if ( ! $source_id || ! array_key_exists($source_id, $sources))
				{
					$use_this_src = -1;

					foreach($sources as $src_id => $source)
					{
						if($source['source_name'] != 'localhost' && $source['source_name'] != 'other')
						{
							if(@preg_match($source['source_rule'], $url))
							{
								$use_this_src = $source['source_id'];
								break;
							}
						}
					}
					
					if ($url != '' && $use_this_src == -1)
					{
						$errors['yt_id'] = $lang['suggest_msg5'];
					}
				}
				
				if ($use_this_src > -1)
				{
					if ( ! file_exists('./'. _ADMIN_FOLDER .'/src/' . $sources[ $use_this_src ]['source_name'] . '.php'))
					{
						$response = array('failed' => true, 
							 	  	  	  'message' => $lang['suggest_msg5']
							 			 );
						exit(json_encode($response));
					}
					else
					{
						require_once('./'. _ADMIN_FOLDER .'/src/' . $sources[ $use_this_src ]['source_name'] . '.php');
						
						$do_main = $sources[ $use_this_src ]['php_namespace'] .'\do_main';
						$download_thumb = $sources[ $use_this_src ]['php_namespace'] .'\download_thumb';
						@$do_main($video_details, $url);
						
						$video_details['source_id'] = $use_this_src;
					}
				}
				
				if (count($errors) == 0)
				{
					$url = secure_sql($url);
					//	Lookup this URL in the database, check for existence to avoid duplication.
					$sql = "SELECT COUNT(*) as total_results 
							  FROM pm_videos_urls 
							 WHERE direct = '". $url ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					$modframework->trigger_hook('suggest_check');
					if ($row['total_results'] > 0)
					{
						$response = array('failed' => true, 
							 	  		  'message' => $lang['suggest_msg1']
							 		);
						break;
					}
					unset($sql, $result, $row);
					
					$sql = "SELECT COUNT(*) as total_results 
							  FROM pm_temp 
							 WHERE url = '". secure_sql($url) ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ($row['total_results'] > 0)
					{
						$response = array('failed' => true, 
							 	  		  'message' => $lang['suggest_msg2']
							 		);
						break;
					}
					
					$description = trim($_POST['description']);
					$description = nl2br($description);
					$description = stripslashes($description);
					$description = str_replace(array("\r", "\n"), '', $description);
					$description = removeEvilTags($description);
					$description = secure_sql($description);
					
					if(_STOPBADCOMMENTS == '1') 
					{
						$description = search_bad_words($description);
					}
					$description = word_wrap_pass($description);
					
					$video_title = 		secure_sql($_POST['video_title']);
					$video_title = 		str_replace( array("<", ">"), '', $video_title);
					$submitted = secure_sql($userdata['username']);
					$category = secure_sql($_POST['category']);
					
					$yt_id = specialchars($yt_id, 0);
					
					$user_id = $userdata['id'];
					$tags = removeEvilTags($_POST['tags']);
					$tags = secure_sql($tags);
					
					$duration = 0;
					if ( ! empty($video_details['yt_length']))
					{
						$duration = (int) $video_details['yt_length'];
					}
					$modframework->trigger_hook('suggest_ajax_insert_before');
					if ($config['auto_approve_suggested_videos'] == 1 || 
					   ($config['auto_approve_suggested_videos_verified'] == 1 && $userdata['channel_verified'] == 1)) 
					{
						// insert new video procedure

						// overwrite some data with user input
						$video_details['video_title'] = $video_title;
						$video_details['description'] = $description;
						$video_details['category'] = $category;
						$video_details['yt_length'] = $duration;
						$video_details['tags'] = $tags;
						$video_details['language'] = 1;
						$video_details['age_verification'] = 0;
						$video_details['submitted_user_id'] = (int) $userdata['id'];
						$video_details['submitted'] = $submitted;
						$video_details['added'] = time();
						$video_details['source_id'] = $use_this_src;
						$video_details['featured'] = 0;
						$video_details['restricted'] = 0;
						$video_details['allow_comments'] = 1;
						$video_details['direct'] = (empty($video_details['direct'])) ? $url : $video_details['direct'];
						$video_details['url_flv'] = ($video_details['url_flv'] == '') ? $url : $video_details['url_flv']; 
						
						$uniq_id = generate_video_uniq_id();
						$video_details['uniq_id'] = $uniq_id;
						
						$modframework->trigger_hook('suggest_ajax_autoapprove_insert_before');
						
						// insert to database
						$new_video = insert_new_video($video_details, $new_video_id);
						
						if ($new_video !== true)
						{
							$response = array('failed' => true, 
							 	  	  		  'message' => $lang['suggest_msg6']
							 				 );
							exit(json_encode($response));
						}
						else
						{
							$modframework->trigger_hook('suggest_ajax_autoapprove_insert_after');
							// download thumbnail
							if ('' != $video_details['yt_thumb'])
							{
								$img = $download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
							}
							else
							{ 
								$img = true;
							}
							
							// do tags
							if ($video_details['tags'] != '')
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
									insert_tags($uniq_id, $tags);
							}
						}
						$response = array('success' => true, 
								 	  	  'message' => $lang['suggest_msg7']  
								 	);
					}
					else
					{
						$sql = "INSERT INTO pm_temp (url, video_title, description, yt_length, tags, category, username, user_id, added, source_id, language, thumbnail, yt_id, url_flv, mp4)  
								 VALUES ('". $url ."', 
								 		 '". $video_title ."', 
										 '". $description ."',
										 '". $duration ."', 
										 '". $tags ."', 
										 '". $category ."', 
										 '". $submitted ."', 
										 '". $user_id ."', 
										 '". time() ."', 
										 '". $use_this_src ."', 
										 '1', 
										 '". $video_details['yt_thumb'] ."',
										 '". $video_details['yt_id'] ."', 
										 '". $video_details['url_flv'] ."', 
										 '". $video_details['mp4'] ."')";
						$modframework->trigger_hook('suggest_ajax_inserttemp_before');
						$query = @mysql_query($sql);
						
						if ( ! $query)
						{
							$response = array('failed' => true, 
								  	  		  'message' => $lang['suggest_msg6']
							 				  );
						}
						else
						{
							$modframework->trigger_hook('suggest_ajax_inserttemp_after');
							$response = array('success' => true, 
									 	  	  'message' => $lang['suggest_msg4']
									 	);
						}
					}
					break;
				}
				else
				{
					$error_msg = '<ul>';
					foreach ($errors as $k => $msg)
					{
						$error_msg .= '<li>'. $msg .'</li>';
					}
					$error_msg .= '</ul>';
					// not a supported video source
					$response = array('failed' => true, 
							 	  	  'message' => $error_msg
							 	);
				}

			break;
		}
		
		echo json_encode($response);
		exit();
	break;
	
	case 'profile':

		switch ($action)
		{
			case 'follow':
			case 'unfollow':
			case 'getfollowers':
			case 'getfollowing':
			case 'activity-stream':
			case 'update-status':
			case 'load-notifications':
				
				if ( ! _MOD_SOCIAL)
				{
					exit('Activate social module first.');
				}
				
			break;
		}
		$profile_user_id = ($_GET['uid'] != '')  ? (int) $_GET['uid'] : (int) $_POST['uid'];
		if ( ! $profile_user_id && $action != 'load-notifications')
		{
			exit('Invalid user ID provided.');
		}
		
		switch ($action)
		{
			case 'load-notifications':

				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$from = $page * NOTIFICATIONS_PER_PAGE - (NOTIFICATIONS_PER_PAGE); 
				
				$notification_list = get_latest_notifications($from, NOTIFICATIONS_PER_PAGE);
				
				// mark as read
				mark_notification_read(NOTIFICATIONS_PER_PAGE);
				if ($notification_list != false)
				{
					$smarty->assign('total_notifications', count($notification_list));
					$smarty->assign('notification_list', $notification_list);
				}
				else
				{
					$smarty->assign('total_notifications', 0);
					$smarty->assign('notification_list', array());
				}
				$html = $smarty->fetch('notification-list.tpl');
				
				exit($html);
				
			break;
			
			case 'follow': // return JSON
				
				$response = array('success' => false,
								  'msg' => '', 
								  'html' => '');
				
				if ( ! is_user_logged_in() || $profile_user_id == 0)
				{
					$response['msg'] = $lang['registration_req'];
					exit(json_encode($response));
				}
				
				// check if this user has reached the user_following_limit
				if ($userdata['following_count'] >= $config['user_following_limit'])
				{
					$response['msg'] = $lang['follow_error_max_limit'];
					exit(json_encode($response));
				}
				
				// check if user-to-follow exists
				$sql = "SELECT COUNT(*) as total 
						FROM pm_users 
						WHERE id = '". $profile_user_id ."'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				if ($row['total'] == 0)
				{
					$response['msg'] = $lang['login_msg12'];
					exit(json_encode($response));
				}
				
				$follow = follow($profile_user_id);

				$response['success'] = true;
				
				$smarty->assign('profile_user_id', $profile_user_id);
				$smarty->assign('profile_data', array('am_following' => true));
				$response['html'] = $smarty->fetch('user-subscribe-button.tpl');

				exit(json_encode($response));
			break;
			
			case 'unfollow': // return JSON
			
				$response = array('success' => false,
								  'msg' => '', 
								  'html' => '');
				
				if ( ! is_user_logged_in() || $profile_user_id == 0)
				{
					$response['msg'] = $lang['registration_req'];
					exit(json_encode($response));
				}
				
				// check if relationship exists
				if (is_follow_relationship($profile_user_id, $userdata['id']))
				{
					unfollow($profile_user_id);
				}
				$response['success'] = true;

				$smarty->assign('profile_user_id', $profile_user_id);
				$smarty->assign('profile_data', array('am_following' => false));
				$response['html'] = $smarty->fetch('user-subscribe-button.tpl');
				
				exit(json_encode($response));
			break;
			
			
			case 'getfollowers': // return HTML
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$profiles_per_page = FOLLOW_PROFILES_PER_PAGE;
				$from = $page * $profiles_per_page - ($profiles_per_page); 
				
				$followers_count = 0;
				if ($profile_user_id == $userdata['id'])
				{
					$followers_count = $userdata['followers_count'];
				}
				else
				{
					$sql = "SELECT followers_count 
							FROM pm_users 
							WHERE id = '". $profile_user_id ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					$followers_count = (int) $row['followers_count'];
				}

				if ($followers_count)
				{
					load_countries_list();
					
					$total_pages = ceil($followers_count / $profiles_per_page);
					
					// get list
					$list = get_followers_list($profile_user_id, $from, $profiles_per_page);
					
					if (count($list) > 0)
					{
						$my_following_list = $my_followers_list = array();
						$user_ids = array();
						
						foreach ($list as $uid => $u)
						{
							$user_ids[] = $uid;
						}
						
						check_multiple_relationships($user_ids, $my_followers_list, $my_following_list);

						foreach ($list as $user_id => $u)
						{
							if ($user_id != $userdata['id'])
							{
								$list[$user_id]['is_following_me'] = (in_array($user_id, $my_followers_list)) ? true : false;
								$list[$user_id]['am_following'] = (in_array($user_id, $my_following_list)) ? true : false;
							}
						}
						
						$smarty->assign('follow_count', $followers_count);
						$smarty->assign('profile_list', $list);
						$smarty->assign('total_profiles', count($list));
						$html = $smarty->fetch('user-follow-list.tpl');
						exit($html);
					}
				}
				
				$smarty->assign('follow_count', 0);
				$smarty->assign('profile_list', array());
				$smarty->assign('total_profiles', 0);
				$html = $smarty->fetch('user-follow-list.tpl');
				exit($html);
				
			break;
			
			case 'getfollowing': // return HTML
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$profiles_per_page = FOLLOW_PROFILES_PER_PAGE;
				$from = $page * $profiles_per_page - ($profiles_per_page); 
				
				$following_count = 0;
				if ($profile_user_id == $userdata['id'])
				{
					$following_count = $userdata['following_count'];
				}
				else
				{
					$sql = "SELECT following_count 
							FROM pm_users 
							WHERE id = '". $profile_user_id ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					$following_count = (int) $row['following_count'];
				}
				
				if ($following_count > 0)
				{
					load_countries_list();
					
					$total_pages = ceil($following_count / $profiles_per_page);
					
					// get list
					$list = get_following_list($profile_user_id, $from, $profiles_per_page);
					
					if (count($list) > 0)
					{
						$my_following_list = $my_followers_list = array();
						$user_ids = array();
						
						foreach ($list as $uid => $u)
						{
							$user_ids[] = $uid;
						}
						
						check_multiple_relationships($user_ids, $my_followers_list, $my_following_list);
						
						foreach ($list as $user_id => $u)
						{
							if ($user_id != $userdata['id'])
							{
								$list[$user_id]['is_following_me'] = (in_array($user_id, $my_followers_list)) ? true : false;
								$list[$user_id]['am_following'] = (in_array($user_id, $my_following_list)) ? true : false;
							}
						}
						
						$smarty->assign('follow_count', $following_count);
						$smarty->assign('profile_list', $list);
						$smarty->assign('total_profiles', count($list));
						$html = $smarty->fetch('user-follow-list.tpl');
						exit($html);
					}
				}
				
				$smarty->assign('follow_count', 0);
				$smarty->assign('profile_list', array());
				$smarty->assign('total_profiles', 0);
				$html = $smarty->fetch('user-follow-list.tpl');
				exit($html);
				
			break;
			
			case 'activity-stream':
				
				if ( ! is_user_logged_in())
				{
					exit($lang['registration_req']);
				}
				if ($userdata['id'] != $profile_user_id)
				{
					exit();
				}
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$from = $page * ACTIVITIES_PER_PAGE - (ACTIVITIES_PER_PAGE); 
				
				$actor_bucket = array();
				$object_bucket = array();
				$target_bucket = array();
				$activity_meta_bucket = array();
				$activity_stream = get_following_activity_stream($from, ACTIVITIES_PER_PAGE);
				
				activity_stream_rollup($activity_stream, $actor_bucket, $object_bucket, $target_bucket, $activity_meta_bucket);
				
				$smarty->assign('total_activities', count($activity_stream));
				unset($activity_stream);
			
				$smarty->assign('actor_bucket', $actor_bucket);
				$smarty->assign('object_bucket', $object_bucket);
				$smarty->assign('target_bucket', $target_bucket);
				$smarty->assign('activity_meta_bucket', $activity_meta_bucket);
				
				$activity_stream_html = $smarty->fetch('activity-stream.tpl');
				exit($activity_stream_html);
				
			break;
			
			case 'user-activity':
				
				if ( ! is_user_logged_in())
				{
					exit($lang['registration_req']);
				}

				if ($profile_user_id != $userdata['id'] && ! is_follow_relationship($profile_user_id, $userdata['id']))
				{
					exit();
				}
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$from = $page * ACTIVITIES_PER_PAGE - (ACTIVITIES_PER_PAGE);
				
				$actor_bucket = array();
				$object_bucket = array();
				$target_bucket = array();
				$activity_meta_bucket = array();

				$activity_stream = get_user_activity($profile_user_id, $from, ACTIVITIES_PER_PAGE);
				activity_stream_rollup($activity_stream, $actor_bucket, $object_bucket, $target_bucket, $activity_meta_bucket);
				
				$smarty->assign('total_activities', count($activity_stream));
				unset($activity_stream);
				
				$smarty->assign('actor_bucket', $actor_bucket);
				$smarty->assign('object_bucket', $object_bucket);
				$smarty->assign('target_bucket', $target_bucket);
				$smarty->assign('activity_meta_bucket', $activity_meta_bucket);
 
				$user_activity_html = $smarty->fetch('user-activity.tpl');
				
				exit($user_activity_html);
				
			break;
			
			case 'user-activity-hide':
				
				$activity_id = (int) $_GET['activity_id'];

				if ( ! is_user_logged_in() || ! $activity_id)
				{
					exit($lang['registration_req']);
				}
				
				$activity_data = get_activity_data($activity_id);
				
				if ( ! $activity_data || $activity_data['user_id'] != $userdata['id'])
				{
					exit();
				}
				
				hide_activity($activity_id);
				exit();
				
			break;
			
			case 'update-status':

				if ( ! is_user_logged_in() || $profile_user_id == 0)
				{
					exit(json_encode(array('success' => false, 'msg' => $lang['registration_req'], 'html' => '')));
				}
				
				$status = trim($_POST['txt']);
				$status = stripslashes($status);
				$status = str_replace("\n", '<br />', $status);//nl2br($status);
				$status = removeEvilTags($status);
				//$status = secure_sql($status);
				
				if(_STOPBADCOMMENTS == '1') 
				{
					$status = search_bad_words($status);
				}
				
				if ($config['allow_emojis'])
				{
					include(ABSPATH .'include/emoji/autoload.php');
					
					$emoji_client = new Emojione\Client(new Emojione\Ruleset());
					$emoji_client->ascii = true;
					$emoji_client->unicodeAlt = false;
					
					// convert unicode to shortname for storage
					$status = $emoji_client->toShort($status);
				}
				
				$status = word_wrap_pass($status);
				
				if ( ! strlen($status))
				{
					exit(json_encode(array('success' => false, 'msg' => $lang['user_status_error_empty'], 'html' => '')));
				}
				
				

				$activity_id = log_activity(array(
												'user_id' => $userdata['id'],
												'activity_type' => ACT_TYPE_STATUS,
												'metadata' => array('statustext' => $status)
												)
											);
				if ( ! $activity_id)
				{
					exit(json_encode(array('success' => false, 'msg' => $lang['comment_msg4'], 'html' => ''))); 
				}
				
				$actor_bucket = array();
				$object_bucket = array();
				$target_bucket = array();
				$activity_meta_bucket = array();

				activity_stream_rollup(get_user_activity($userdata['id'], 0, 1), $actor_bucket, $object_bucket, $target_bucket, $activity_meta_bucket);
				$smarty->assign('total_activities', 1);
				
				$smarty->assign('actor_bucket', $actor_bucket);
				$smarty->assign('object_bucket', $object_bucket);
				$smarty->assign('target_bucket', $target_bucket);
				$smarty->assign('activity_meta_bucket', $activity_meta_bucket);
 
				$user_activity_html = $smarty->fetch('user-activity.tpl');
				
				exit(json_encode(array('success' => true, 'msg' => '', 'html' => $user_activity_html)));

			break;
			
			case 'profile-load-playlists':
				
				//$profile_user_id = (int) $_GET['profile-id'];

				if ( ! $profile_user_id)
				{
					exit(json_encode(array( 'success' => false,
								'msg' => $lang['playlist_msg_no_playlists'], 
								'html' => pm_alert_danger($lang['playlist_msg_no_playlists'])))); 
				}
				
				if ($profile_user_id == $userdata['id'])
				{
					$playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
					$playlists = get_user_playlists($userdata['id'], false, false, 0, $playlists_count);
				}
				else
				{
					//$playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
					$sql = "SELECT COUNT(*) as total 
							FROM pm_playlists 
							WHERE user_id = ". secure_sql($profile_user_id) ." 
							  AND visibility = ". PLAYLIST_PUBLIC;
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$playlists_count = (int) $row['total'];
					$playlists = get_user_playlists($profile_user_id, false, PLAYLIST_PUBLIC, 0, $playlists_count);
				}
				
				$smarty->assign('playlists', $playlists);
				$html = $smarty->fetch('profile-playlists-ul.tpl');
				
				exit(json_encode(array( 'success' => true,
							'msg' => '',
							'html' => $html)));
				
			break;
		}
	break;
	
	case 'upload':
		
		switch($action) 
		{
			case 'user-avatar': // ajax upload
			case 'channel-cover':
				
				if ( ! is_user_logged_in())
				{
					exit(json_encode(array( 'success' => false, 
											'alert_type' => 'danger',
											'file_url' => null,
											'msg' => $lang['registration_req'], 
											'html' => '')));
				}

				$image_data = $_POST['image-data']; // expecting JPEG
				$image_data_parts = explode(',', $image_data);
				$image_data = array_pop($image_data_parts);
				$image_data = base64_decode($image_data);
				//preg_match('/data:(.*);/', $image_data_parts[0], $matches);
				//$image_type = $matches[1];
				
				$file_ext = 'jpg';
				
				if ($action == 'user-avatar')
				{
					$upload_dir = _AVATARS_DIR_PATH;
					$filename = 'avatar'. rand(0, 1000) .'-'. $userdata['id'] .'.'. $file_ext;
				}
				else
				{
					$upload_dir = _COVERS_DIR_PATH;
					$filename  = md5(time() . rand(0, 1000));
					$filename  = substr($filename, 0, 14) .'-max.'. $file_ext;
				}
								
				try {
					$image = imagecreatefromstring($image_data);
					unset($image_data);
										
					if( function_exists('imageantialias'))
					{
						 imageantialias($image, true); 
					}
					
					$image_saved = imagejpeg($image, $upload_dir . $filename);
					
					if ($action == 'channel-cover' && $image_saved)
					{
						$sizes = array( '450' => str_replace('-max.', '-450.', $filename),
										'225' => str_replace('-max.', '-225.', $filename)
									);
						list($width_full, $height_full) = @getimagesize($upload_dir . $filename);
						
						resize_then_crop($upload_dir . $filename, $upload_dir . $sizes['450'], ($width_full / 2), ($height_full / 2), 255, 255, 255);
						resize_then_crop($upload_dir . $filename, $upload_dir . $sizes['225'], ($width_full / 4), ($height_full / 4), 255, 255, 255);
						@chmod($upload_dir . $sizes['225'], 0644); // @since v2.7
						@chmod($upload_dir . $sizes['450'], 0644);
					}
					
					@chmod($upload_dir . $filename, 0644);
					
				} catch (Exception $e) {
					exit(json_encode(array( 'success' => false, 
											'alert_type' => 'danger',
											'file_url' => null,
											'msg' => $lang['upload_errmsg1'],  
											'html' => '')));
				}
				
				$sql = "UPDATE pm_users 
						   SET ". (($action == 'user-avatar') ? ' avatar ' : ' channel_cover ') ." = '". $filename ."'
						WHERE id = ". $userdata['id']; 
				
				if ( ! $result = mysql_query($sql))
				{
					exit(json_encode(array( 'success' => false, 
											'alert_type' => 'danger',
											'file_url' => null,
											'msg' => $lang['upload_errmsg1'],
											'html' => '')));
				}
				
				if ($action == 'user-avatar')
				{
					if ($userdata['avatar'] != '' && $userdata['avatar'] != 'default.gif')
					{
						@unlink($upload_dir . $userdata['avatar']);
					}
				}
				else
				{
					if ($userdata['channel_cover']['filename'] != '')
					{
						delete_channel_cover_files($userdata['channel_cover']['filename']);
					}
				}
				
				if (_MOD_SOCIAL)
				{
					if ($action == 'user-avatar')
					{
						log_activity(array(
								'user_id' => $userdata['id'],
								'activity_type' => ACT_TYPE_UPDATE_AVATAR,
								'object_id' => $userdata['id'],
								'object_type' => ACT_OBJ_PROFILE
								)
							);
					}
					else
					{
						log_activity(array(
								'user_id' => $userdata['id'],
								'activity_type' => ACT_TYPE_UPDATE_COVER,
								'object_id' => $userdata['id'],
								'object_type' => ACT_OBJ_PROFILE
								)
							);
					}
				}
				
				exit(json_encode(array( 'success' => true, 
										'alert_type' => 'success',
										'file_url' => ($action == 'user-avatar') ? _AVATARS_DIR . $filename : _COVERS_DIR . $filename,
										'msg' => ($action == 'user-avatar') ? $lang['ua_msg3'] : $lang['channel_cover_save_success'],
										'html' => '')));
				
			break;
			
			case 'upload-media-file': // file sent by flash/jquery uploader on 'Upload video' page
			case 'useruploadvideo': // backwards compat 
				$error_msg = '';
				$max_filesize_bytes = return_bytes($config['allow_user_uploadvideo_bytes']);
				
				// message for Admin/Content Manager, in case the Upload Process stops mid-point
				$tmp_description = 'You are seeing this because the user has not finished the upload process by submitting the Upload Form.';
				$tmp_description .= '<br />';
				$tmp_description .= 'Now, you can either Edit & Approve this item or Delete it.';
				
				$whitelist	   = array('flv', 'mov', 'avi', 'divx', 'mp4', 'wmv', 'mkv',
									   'asf', 'wma', 'mp3', 'm4v', 'm4a', '3gp', '3g2');

				$allowed_types = array( 'video/x-flv', 	'video/quicktime', 'video/x-msvideo', 
										'video/x-divx', 'video/mp4', 'video/x-ms-wmv', 
										'application/octet-stream',  'video/avi', 'video/x-matroska',
										'video/x-ms-asf', 'audio/x-ms-wma',	'audio/mp4', 'video/3gpp', 
										'video/3gpp2', 'audio/mpeg', 'video/mpeg', 'application/force-download', 
										'audio/mp3', 'audio/mpeg3', 'video/x-m4v', 'audio/x-m4a');
				
				$file = $_FILES['video'];
				
				$nonce_name = substr(md5('_uploadform'.$_POST['form_id'].$userdata['id'].pm_get_ip()), 3, 8);
				$nonce = array();
				
				if ($config['allow_user_uploadvideo'] == '0')
				{
					$error_msg = $lang['disabled_feature'];
				}
				
				if ( ! csrfguard_validate_token($nonce_name, $_POST['_pmnonce_t']))
				{
					$error_msg = $lang['upload_errmsg_badtoken'];
				}
				
				if ( ! is_user_logged_in()) 
				{
					$error_msg = $lang['upload_login_first'];
				}
				
				// check if the user has reached the daily uploads limit limit
				if ( ! is_admin() && ! is_moderator())
				{
					$sql = "SELECT COUNT(*) as total
							FROM pm_temp
							WHERE user_id = '". $userdata['id'] ."'
							  AND source_id = '1'
							  AND added >= '". mktime(0, 0, 0) ."'
							  AND added <= '". mktime(23, 59, 59) ."'";
					$result = @mysql_query($sql);
					$row = @mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$uploaded_today = $row['total'];
					
					if ($uploaded_today > $config['user_upload_daily_limit'])
					{
						$error_msg = $lang['upload_errmsg11'];
					}
				}
				
				if ($error_msg == '')
				{
					// regenerate form nonce
					$nonce = csrfguard_raw($nonce_name);

					$tmp_parts = explode('.', $file['name']);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);
					if (($file['size'] > 0 && $file['size'] <= $max_filesize_bytes) && strlen($file['name']) > 0 && $file['error'] == 0)
					{
						if (in_array($ext, $whitelist))
						{
							do
							{
								$new_name  = md5($file['name'].rand(1,888));
								$new_name  = substr($new_name, 2, 10);
								$new_name .= '.'.$ext;
							}
							while (file_exists(_VIDEOS_DIR_PATH . $new_name));
							
							$modframework->trigger_hook('upload_moveupload');

							if ($move = @move_uploaded_file($file['tmp_name'], _VIDEOS_DIR_PATH . $new_name))
							{
								@chmod(_VIDEOS_DIR_PATH . $new_name, 0644); // @since v2.7
								
								// INSERT INTO pm_temp, disregarding the current configuration
								// with special title. 
								$sql = "INSERT INTO pm_temp
												(url, video_title, description, yt_length, tags, category,casino,provider, username, user_id, 
												 added, source_id, language, thumbnail, yt_id, url_flv, mp4)
										VALUES ('". $new_name ."', 'n/a', '". secure_sql($tmp_description) ."', 0, '', 0, 0,0,'". $userdata['username'] ."', 
												'". $userdata['id'] ."', '". time() ."', 1, 1, '', '', '', '')";
//								echo $sql;
								$result = @mysql_query($sql);
								
								if ( ! $result)
								{
									$error_msg = $lang['upload_errmsg1'];
								}
								else
								{
									$temp_video_id = mysql_insert_id();
								}

								$modframework->trigger_hook('upload_insertvideo_after');
							}
							else
							{

								$error_msg = $lang['upload_errmsg1'];
							}
						}
						else
						{
							$error_msg = $lang['upload_errmsg2'];
						}
					}
					else
					{
						switch ($file['error'])
						{	
		
							case UPLOAD_ERR_INI_SIZE:
								$error_msg = $lang['upload_errmsg3'];
							break;
							
							case UPLOAD_ERR_FORM_SIZE:
								$error_msg = $lang['upload_errmsg4'];
							break;
							
							case UPLOAD_ERR_PARTIAL:
								$error_msg = $lang['upload_errmsg5'];
							break;
							
							case  UPLOAD_ERR_NO_FILE:
								$error_msg = $lang['upload_errmsg6'];
							break;
							
							case UPLOAD_ERR_NO_TMP_DIR:
								$error_msg = $lang['upload_errmsg7'];
							break;
							
							case 7: //UPLOAD_ERR_CANT_WRITE:
								$error_msg = $lang['upload_errmsg8'];
							break;
							
							case 8: //UPLOAD_ERR_EXTENSION:
								$error_msg = $lang['upload_errmsg9'];
							break;
							
							default:
							case UPLOAD_ERR_OK:
							break;
						}
					}
				}
				
				if ($temp_video_id) 
				{
					exit(json_encode(array( 'success' => true,
											'alert_type' => 'success',
											'video_id' => $temp_video_id,
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'file' => (is_admin()) ? $_FILES : null, // debugging
											'msg' => '',
											'html' => '')));
				}
				
				if ($error_msg != '')
				{
					if ($file['tmp_name'] != '' && file_exists($file['tmp_name']))
					{
						@unlink($file['tmp_name']);
					}
					
					exit(json_encode(array( 'success' => false, 
											'alert_type' => 'danger',
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $error_msg,
											'file' => (is_admin()) ? $_FILES : null, // debugging
											'html' => '')));
				}
				
			break; // upload-media-file
			
			case 'submit-upload-video-form':
				
				$nonce_name = substr(md5('_uploadform'.$_POST['form_id'].$userdata['id'].pm_get_ip()), 3, 8);
				$nonce = array();
				$error_msg = '';
				$max_filesize_bytes = return_bytes($config['allow_user_uploadvideo_bytes']);
				$whitelist_img	  = array('jpg', 'gif', 'png', 'jpeg');
				$allowed_types_img = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg');
				
				if ($config['allow_user_uploadvideo'] == '0')
				{
					$error_msg = $lang['disabled_feature'];
				}
				
				if ( ! csrfguard_validate_token($nonce_name, $_POST['_pmnonce_t']))
				{
					$error_msg = $lang['upload_errmsg_badtoken'];
				}
				
				if ( ! is_user_logged_in()) 
				{
					$error_msg = $lang['upload_login_first'];
				}
				
				if ($error_msg != '')
				{
					exit(json_encode(array( 'success' => false, 
											'alert_type' => 'danger',
											'msg' => $error_msg,
											'html' => '')));
				}
				
				// regenerate form nonce
				$nonce = csrfguard_raw($nonce_name);
				$del_tmp_file = false;
				$category_id = (int) $_POST['category'];
				$casino_id = (int) $_POST['casino'];
				$provider_id = (int) $_POST['provider'];
				$img = $_FILES['capture'];
				$thumbnail = '';
				$modframework->trigger_hook('upload_start');
				$error_msg = '';
				
				$required_fields = array('video_title' => $lang['video']);
				
				foreach ($_POST as $key => $value)
				{
					//$value = unspecialchars(trim($value), 1);
					$_POST[$key] = trim($value);
		
					if (array_key_exists(strtolower($key), $required_fields) && empty($value))
						$errors[$key] = $required_fields[$key]." ".$lang['register_err_msg8'];
				}
		
				if ($category_id <= 0)
				{
					$errors['category'] = $lang['choose_category'];
				}
				if ($casino_id <= 0)
				{
					$errors['casino'] = "Please select a casino";
				}
				if ($provider_id <= 0)
				{
					$errors['provider'] = "Please select a provider";
				}
				$modframework->trigger_hook('upload_thumb_before');
		
				// upload image
				$thumbnail = '';
				$tmp_parts = explode('.', $img['name']);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);
		
				if (($img['size'] > 0 && $img['size'] <= $max_filesize_bytes) && strlen($img['name']) > 0 && $img['error'] == 0)
				{
					if (in_array($ext, $whitelist_img))
					{
						$ext = 'jpg'; // save as JPG
						do
						{
							$new_name  = md5($img['name'].rand(1,888));
							$new_name  = substr($new_name, 2, 10);
							$new_name .= '.'.$ext;
						} while (file_exists(_THUMBS_DIR_PATH . $new_name));
						
						// test mime type @since 2.7.1
						$filetype = pm_check_filetype_and_ext($img['tmp_name'], $img['name'], $mimes = false);
						if (empty($filetype['ext']) || empty($filetype['type'])) 
						{
							// $error = 'This file type is not permitted for security reasons.';
						}
						else
						{
							$copy = @copy($img['tmp_name'], _THUMBS_DIR_PATH . $new_name);
							if ($copy === TRUE)
							{
								$resize = resize_then_crop(_THUMBS_DIR_PATH . $new_name, _THUMBS_DIR_PATH . $new_name, THUMB_W_VIDEO, THUMB_H_VIDEO, "255", "255", "255", $allowed_types_img);
								
								if($resize != false)
								{
									$thumbnail = $new_name;
								}
								
								@chmod(_THUMBS_DIR_PATH . $new_name, 0644); // @since v2.7
							}
						}
					}
				}
				
				// end upload image
				$modframework->trigger_hook('upload_thumb_after');
		
				if (count($errors) > 0)
				{
					$del_tmp_file = true;
				}
				else
				{
					$mysql_insert_id = (int) $_POST['temp_id'];
					if ($mysql_insert_id)
					{
						$sql = "SELECT *
								FROM pm_temp
								WHERE id = ". secure_sql($mysql_insert_id);
						$row = false;
						if ($result = mysql_query($sql))
						{
							$row = mysql_fetch_assoc($result);
							mysql_free_result($result);
						}
		
						if (is_array($row) && count($row) > 0)
						{
							if ($row['user_id'] == $userdata['id'])
							{
								if ($_POST['duration'] != '')
								{
									$pieces = explode(':', $_POST['duration']);
									$pieces[0] = (int) $pieces[0];
									$pieces[1] = (int) $pieces[1];
		
									$duration = (int) ($pieces[0] * 60) + $pieces[1];
								}
		
								$description = trim($_POST['description']);
								$description = stripslashes($description);
								$description = nl2br($description);
								$description = str_replace(array("\r", "\n"), '', $description);
								$description = removeEvilTags($description);
								$description = secure_sql($description);
		
								if(_STOPBADCOMMENTS == '1')
								{
									$description = search_bad_words($description);
								}
								$description = word_wrap_pass($description);
		
								$video_title = 	secure_sql($_POST['video_title']);
								$video_title = 	str_replace( array("<", ">"), '', $video_title);
		
								$tags = removeEvilTags($_POST['tags']);
								$tags = secure_sql($tags);
								$modframework->trigger_hook('upload_insertvideo_before');
		
								if ($config['auto_approve_suggested_videos'] == 1 ||
								   ($config['auto_approve_suggested_videos_verified'] == 1 && $userdata['channel_verified'] == 1))
								{
									$video_details = array();
		
									$video_details['video_title'] = $video_title;
									$video_details['description'] = $description;
									$video_details['category'] = $category_id;
									$video_details['casino'] = $casino_id;
									$video_details['provider'] = $provider_id;
									$video_details['yt_length'] = $duration;
									$video_details['tags'] = $tags;
									$video_details['language'] = 1;
									$video_details['age_verification'] = 0;
									$video_details['submitted_user_id'] = (int) $userdata['id'];
									$video_details['submitted'] = $userdata['username'];
									$video_details['added'] = time();
									$video_details['source_id'] = 1;
									$video_details['featured'] = 0;
									$video_details['restricted'] = 0;
									$video_details['yt_thumb'] = $thumbnail;
									$video_details['direct'] = $row['url'];
									$video_details['url_flv'] = $row['url'];
									$video_details['allow_comments'] = 1;
									
									$uniq_id = generate_video_uniq_id();
									$video_details['uniq_id'] = $uniq_id;
		
									// try to rename uploaded thumb
									if ($thumbnail != '')
									{
										$ext = 'jpg';
										if (rename(_THUMBS_DIR_PATH . $thumbnail, _THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext))
										{
											$video_details['yt_thumb'] = $uniq_id . '-1.'. $ext;
										}
									}



									// insert to database
									$new_video = insert_new_video($video_details, $new_video_id);
									if ($new_video !== true)
									{
										$errors['mediafile'] = $lang['upload_errmsg1'];
									}
									else
									{
										$modframework->trigger_hook('upload_insertvideo_autoapprove_after');
										// do tags
										if ($video_details['tags'] != '')
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
												insert_tags($uniq_id, $tags);
										}
		
										$sql = "DELETE FROM pm_temp
												WHERE id = ". $row['id'];
										@mysql_query($sql);
									}
									
									exit(json_encode(array( 'success' => true, 
															'alert_type' => 'success',
															'_pmnonce_t' => $nonce['_pmnonce_t'],
															'msg' => $lang['suggest_msg7'],
															'html' => '')));
									
									//header("Location: ". _URL .'/upload.'. _FEXT .'?s=approved');
									//exit();
								}
								else
								{
									$sql = "UPDATE pm_temp
											   SET video_title = '". $video_title ."',
											   	   description = '". $description ."',
												   yt_length = '". $duration ."',
												   tags = '". $tags ."',
												   category = '". $category_id ."',
												   added = '". time() ."',
												   thumbnail = '". $thumbnail ."'
											WHERE id = ". secure_sql($mysql_insert_id);
		
									$result = @mysql_query($sql);
		
									$modframework->trigger_hook('upload_insertvideo_updatetempdata');
		
									if ( ! $result)
									{
										$errors['mediafile'] = $lang['upload_errmsg1'];
									}
									else
									{
										// success
										//header("Location: ". _URL .'/upload.'. _FEXT .'?s=uploaded');
										//exit();
										exit(json_encode(array( 'success' => true, 
																'alert_type' => 'success',
																'_pmnonce_t' => $nonce['_pmnonce_t'],
																'msg' => $lang['suggest_msg4'],
																'html' => '')));
									}
								}
							}
							else
							{
								// Current User ID different from Uploader's User ID
								$errors['mediafile'] = $lang['upload_errmsg1'];
							}
						}
						else
						{
							// pm_temp row not found or mysql error
							$errors['mediafile'] = $lang['upload_errmsg6'];
						}
					}
					else
					{
						// $mysql_insert_id = 0
						$errors['mediafile'] = $lang['upload_errmsg1'];
					}
				}
				
				if ($del_tmp_file && strlen($img['tmp_name']) > 0)
				{
					@unlink($img['tmp_name']);
				}
				
				if (count($errors))  
				{
					$ajax_msg = '<ul class="subtle-list">';
					foreach ($errors as $k => $error_msg)
					{
						$ajax_msg .= '<li>'. $error_msg .'</li>';
					}
					$ajax_msg .= '</ul>';
					
					
					exit(json_encode(array( 'success' => false, 
											'alert_type' => 'danger',
											'_pmnonce_t' => $nonce['_pmnonce_t'],
											'msg' => $ajax_msg,
											'html' => $ajax_msg)));
				}
				
			break; // submit-uploaded-video
		}
		
	break;
	
	case 'stats': // advertisments
	
		$ad_id = (int) $_GET['aid'];
		$ad_type = (int) $_GET['at'];
		
		switch ($action) 
		{
			case 'show':
				
				if ( ! pm_detect_crawler())
				{
					if ($ad_id && in_array($ad_type, array(_AD_TYPE_CLASSIC, _AD_TYPE_VIDEO, _AD_TYPE_PREROLL)))
					{
						$sql_table = '';
						switch ($ad_type)
						{
							case _AD_TYPE_CLASSIC:
								$sql_table = 'pm_ads';
							break;
							
							case _AD_TYPE_VIDEO:
								$sql_table = 'pm_videoads';
							break;
							
							case _AD_TYPE_PREROLL:
								$sql_table = 'pm_preroll_ads';
							break;
						}
						
						$sql = "SELECT COUNT(*) as total_found FROM $sql_table WHERE id = $ad_id";
						if ($result = @mysql_query($sql))
						{
							$row = mysql_fetch_assoc($result);
							mysql_free_result($result);
							
							if ($row['total_found'] > 0)
							{
								$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, impressions)
										VALUES (CURDATE(), $ad_id, $ad_type, 1) 
										ON DUPLICATE KEY 
											UPDATE impressions = impressions + 1";
								@mysql_query($sql);
				
							}
						}
					}
				}

				header("Content-type: image/gif"); 
				header("Expires: Wed, 5 Feb 1986 06:06:06 GMT"); 
				header("Cache-Control: no-cache"); 
				header("Cache-Control: must-revalidate"); 
				printf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%', 71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59);
				
				exit();
			
			break;
			
			case 'skip':
				
				if ($ad_id && in_array($ad_type, array(_AD_TYPE_CLASSIC, _AD_TYPE_VIDEO, _AD_TYPE_PREROLL)))
				{
					$sql_table = '';
					switch ($ad_type)
					{
						case _AD_TYPE_CLASSIC:
							$sql_table = 'pm_ads';
						break;
						
						case _AD_TYPE_VIDEO:
							$sql_table = 'pm_videoads';
						break;
						
						case _AD_TYPE_PREROLL:
							$sql_table = 'pm_preroll_ads';
						break;	
					}
					
					$sql = "SELECT COUNT(*) as total_found FROM $sql_table WHERE id = $ad_id";
					if ($result = @mysql_query($sql))
					{
						$row = mysql_fetch_assoc($result);
						mysql_free_result($result);
						
						if ($row['total_found'] > 0)
						{
							$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, skips)
									VALUES (CURDATE(), $ad_id, $ad_type, 1) 
									ON DUPLICATE KEY 
										UPDATE skips = skips + 1";
							@mysql_query($sql); 
						}
					}
				}

			break;
			
			case 'click':
			break;
		}

	break;
    case 'search':
        switch ($action)
        {
            case 'getVideoHtml':
                $video_uniq_id = $_GET['videoId'];
                $video = request_video($video_uniq_id);
                $videoComment = get_comment_count($video_uniq_id);
                $video['comment_count'] = $videoComment;
                $smarty->assign('video_data', $video);
                $html = $smarty->fetch('my-player.tpl');
                echo $html;
                exit();
                break;
        }	//	end switch ($page)
    break;
    case 'index':
        switch ($action){
            case 'loadmore':
                $index = $_GET['index'];
                $orderBy = $_GET['orderBy'];
                $jsonIds = $_GET['ids'];
                $ids = json_decode($jsonIds);
                if(empty($orderBy))
                    $orderBy = 'added';
                $list = get_video_list($orderBy, 'DESC', 30*$index, 30, 0, $ids);
                $smarty->assign("results",$list);
                $html = $smarty->fetch('indexVideoList.tpl');
                echo $html;
                exit();
                break;
//                echo $index;

        }
}	//	end switch ($page)

exit();
