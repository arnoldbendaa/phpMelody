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

$_GET['view'] = trim($_GET['view'], '/ ');
$username = urldecode($_GET['u']);

$sql = "SELECT id  
		FROM pm_users 
		WHERE username = '". secure_sql($username) ."' 
		   OR channel_slug = '". secure_sql(sanitize_title($username)) ."'";

if ($result = mysql_query($sql))
{
	$rows = mysql_num_rows($result);
	$user_id = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$profile_data = get_user_data($user_id['id']);
}
 
if ($rows == 0) 
{
	redirect_404();
}

// total videos submitted
$query_total = @mysql_query("SELECT COUNT(*) as total FROM pm_videos WHERE submitted = '". secure_sql($profile_data['username']) ."' AND added <= '". time() ."'");
$total_submissions = @mysql_fetch_assoc($query_total);
$total_submissions = (int) $total_submissions['total'];

@mysql_free_result($query_total);

// videos suggested by this user
$submitted_video_list = array();

$sql = "SELECT uniq_id 
		FROM pm_videos 
		WHERE submitted = '". secure_sql($profile_data['username']) ."' 
		  AND added <= '". time() ."' 
		ORDER BY id DESC
		LIMIT 60";
$result = @mysql_query($sql);

if ($result)
{
	$uniq_ids = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$uniq_ids[] = $row['uniq_id'];
	}	
	mysql_free_result($result);
	
	$submitted_video_list = get_video_list('', '', 0, 0, 0, array(), $uniq_ids); 
}

if ($userdata['id'] == $profile_data['id'])
{
	$pending_video_list = array(); 
	
	$sql = "SELECT id, video_title, yt_length, added, thumbnail 
			FROM pm_temp 
			WHERE user_id = ". $userdata['id'] ." 
			ORDER BY added DESC  
			LIMIT 0, 8";
	
	$result = @mysql_query($sql);
	if ($result)
	{
		$i = 0;
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$pending_video_thumb = (empty($row['thumbnail'])) ? _NOTHUMB : $row['thumbnail'];
				if (strpos($pending_video_thumb, 'http') !== 0 && strpos($pending_video_thumb, '//') !== 0)
				{
					$pending_video_thumb = _THUMBS_DIR . $pending_video_thumb;
				}
				
				$pending_video_list[$i] = array('id' => (int) $row['id'],
												'uniq_id' => (int) $row['id'],
												'video_title' => htmlentities($row['video_title']),
												'video_href' => '#',
												'thumb_img_url' => make_url_https($pending_video_thumb),
												'author_data' => $userdata,
												'author_username' => $userdata['username'],
												'author_profile_href' => get_profile_url($userdata),
												'added_timestamp' => (int) $row['added'],
												'html5_datetime' => date('Y-m-d\TH:i:sO', (int) $row['added']), // ISO 8601,
												'full_datetime' => date('l, F j, Y g:i A', (int) $row['added']),
												'time_since_added' => time_since((int) $row['added']),
												'yt_length' => (int) $row['yt_length'],
												'duration' => sec2hms( (int) $row['yt_length']),
												'iso8601_duration' => iso8601_duration((int) $row['yt_length']), // ISO 8601
												'views_compact' => 0,
												'views' => 0,
												'likes_compact' => 0,
												'pending_approval' => true,
												);
				
				$i++;
			}
			mysql_free_result($result);
		}
		if (count($pending_video_list) > 0)
		{
			$submitted_video_list = array_merge($pending_video_list, $submitted_video_list);
		}
	}
}

$status = (islive($profile_data['last_signin'])) ? $lang['memberlist_on'] : $lang['memberlist_off'];

if (_MOD_SOCIAL && $userdata['id'] != $profile_data['id'])
{
	$profile_data['is_following_me'] = is_follow_relationship($userdata['id'], $profile_data['id']);
	$profile_data['am_following'] = is_follow_relationship($profile_data['id'], $userdata['id']);
}

if (_MOD_SOCIAL && $userdata['id'] == $profile_data['id'])
{
	$from = 0; 
	
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
	
	if (empty($_COOKIE['suggest_profiles']) || $_COOKIE['suggest_profiles'] != 'no')
	{
		$who_to_follow = suggest_who_to_follow(0, 3);
		$smarty->assign('who_to_follow_list', $who_to_follow);
	}
}

$banned = banlist($profile_data['id']);

if ($userdata['id'] == $profile_data['id'])
{
	$total_playlists = count($userdata['playlists']);
}
else
{
	$total_playlists = count_entries('pm_playlists', 'user_id', $profile_data['id'] ."' AND visibility = '1");
}

// define meta tags & common variables
$meta_title = sprintf($lang['profile_title'], $profile_data['name'], $profile_data['username'], _SITENAME);
$meta_description = sprintf($lang['profile_description'], $profile_data['name'], $profile_data['username']).' | '._SITENAME.'. '.fewchars(strip_tags($profile_data['about']), 40);
// end

$smarty->assign('profile_data', $profile_data); // @since v2.6
$smarty->assign('full_name', $profile_data['name']);
$smarty->assign('username', $profile_data['username']);
$smarty->assign('gender', ucwords($lang[ $profile_data['gender'] ]));
// Removed from TPL since v2.6
$smarty->assign('country', countryid2name($profile_data['country']));
$smarty->assign('reg_date', time_since($profile_data['reg_date']).' '.$lang['ago']);
$smarty->assign('last_seen', time_since($profile_data['last_signin']).' '.$lang['ago']);
$smarty->assign('status', $status);
// End Removed from TPL since v2.6
$smarty->assign('about', $profile_data['about']);
$smarty->assign('avatar', $profile_data['avatar_url']);
$smarty->assign('share_link', $share_link);
$smarty->assign('user_is_banned', ($banned['user_id'] == $profile_data['id']) ? true : false);
$smarty->assign('social_website', $profile_data['social_links']['website']);
$smarty->assign('social_facebook', $profile_data['social_links']['facebook']);
$smarty->assign('social_twitter', $profile_data['social_links']['twitter']);
$smarty->assign('power', $profile_data['power']);
$smarty->assign('submitted_video_list', $submitted_video_list);
$smarty->assign('total_submissions', $total_submissions);
$smarty->assign('total_playlists', $total_playlists);
$smarty->assign('allow_user_edit_video', (int) $config['allow_user_edit_video']);
$smarty->assign('allow_user_delete_video', (int) $config['allow_user_delete_video']);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$modframework->trigger_hook('user_profile_display');
$smarty->display('channel.tpl');
