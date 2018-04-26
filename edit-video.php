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
// | Copyright: (c) 2004-2015 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

session_start();
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

$max_filesize_bytes = $config['allow_user_uploadvideo_bytes'];
$whitelist_img	  = array('jpg', 'gif', 'png', 'jpeg');
$allowed_types_img = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg');
$sources = fetch_video_sources();
$errors = array();
$show_form = true;
$form_id = md5(time().pm_get_ip());

if ($_GET['type'] == 'pending')
{
	$video = false;
	$video_status = 'pending';
	$video_id = (int) $_GET['vid'];
	
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
	$video_status = 'approved';
	
	if ($video = request_video(trim($_GET['vid'])))
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
	$smarty->assign('errors', array($lang['access_denied']));
	$smarty->assign('show_form', false);
	
	// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
	$smarty->assign('meta_title', htmlspecialchars($lang['edit_video'])); 
	$smarty->assign('meta_description', htmlspecialchars($lang['edit_video'] .' "'. $video['video_title'] .'"')); 
	$smarty->assign('template_dir', $template_f);
	$smarty->display('video-edit.tpl');
	exit();
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
					
					$smarty->assign('success', 'deleted');
					$show_form = false;
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
			
			$smarty->assign('success', 'deleted');
			$show_form = false;
		}
	}
}

if ($_POST['btn-pressed'] == 'submit')
{
	load_categories();
	
	if ( ! csrfguard_validate_token(substr(md5('_edit_video_form'.$_POST['form_id'].$userdata['id'].pm_get_ip()), 3, 8), $_POST['_pmnonce_t_edit_video_form']))
	{
		$errors[] = $lang['upload_errmsg_badtoken'];
	}
	else
	{
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
				if (in_array($img['type'], $allowed_types_img) && in_array($ext, $whitelist_img))
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
					unlink(_VIDEOS_DIR_PATH . $previous_filename);
				}
			}
			
			$smarty->assign('success', 'updated');
			$video = array_merge($video, $_POST);
			$show_form = false;
		}
	}
	
	if (count($errors) > 0)
	{
		$show_form = true;
		$video = array_merge($video, $_POST);
	}
}

$smarty->assign('video_data', $video);
$smarty->assign('video_id', ($video_id) ? $video_id : $video['uniq_id']);
$smarty->assign('video_type', $video_type);
$smarty->assign('video_status', $video_status);

$smarty->assign('form_action', ($video_status == 'approved') ? 'edit-video.php?vid='. $video['uniq_id'] : 'edit-video.php?vid='. $video['id'] .'&type=pending');
$smarty->assign('max_file_size', $max_filesize_bytes);
$smarty->assign('upload_limit', readable_filesize($max_filesize_bytes));
$smarty->assign('categories_dropdown', categories_dropdown(array('selected' => $video['category'], 'attr_class' => 'span5 form-control')));

$nonce = csrfguard_raw( substr(md5('_uploadform'.$form_id.$userdata['id'].pm_get_ip()), 3, 8) );
$smarty->assign('form_id', $form_id);
$smarty->assign('upload_csrf_token', $nonce['_pmnonce_t']); 
$smarty->assign('form_csrf', csrfguard_raw( substr(md5('_edit_video_form'.$form_id.$userdata['id'].pm_get_ip()), 3, 8) ));
$smarty->assign('show_form', $show_form);
$smarty->assign('errors', $errors);
$smarty->assign('allow_user_delete_video', (int) $config['allow_user_delete_video']);

$smarty->assign('meta_title', htmlspecialchars($lang['edit_video']));
$smarty->assign('meta_description', htmlspecialchars($lang['edit_video'] .' "'. $video['video_title'] .'"'));
$smarty->assign('template_dir', $template_f);
$smarty->display('video-edit.tpl');