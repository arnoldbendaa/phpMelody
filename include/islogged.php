<?php
$smarty->register_function('list_categories', 'list_categories');

$logged_in	= is_user_logged_in();
$userdata	= array();
$user_adv	= array();

if ($logged_in) 
{
	$userdata = fetch_user_info($_SESSION[COOKIE_NAME]);
	
	//	check banlist
	$ban = banlist($userdata['id']);
	
	if($ban['id'] == '')
	{
		$smarty->assign('logged_in', 1);
		$smarty->assign('s_user_id', $userdata['id']);
		$smarty->assign('s_username', $userdata['username']);
		$smarty->assign('s_name', $userdata['name']);
		$smarty->assign('s_country', $userdata['country']);
		$smarty->assign('s_email', $userdata['email']);
		$smarty->assign('s_avatar', $userdata['avatar']);
		$smarty->assign('s_power', $userdata['power']);
		$smarty->assign('s_avatar_url', $userdata['avatar_url']);
		
		$userdata['playlists'] = get_user_playlists($userdata['id'], false, false, 0, (int) count_entries('pm_playlists', 'user_id', $userdata['id']));
		$smarty->assign('s_user_playlists', $userdata['playlists']);
		
		$smarty->assign('current_user_data', $userdata); // @since v2.6
		
		if (is_admin()) 
		{
			$smarty->assign('is_admin', 'yes');
		}
		
		if (is_moderator())
		{
			$smarty->assign('is_moderator', 'yes');
		}
		
		if (is_editor())
		{
			$smarty->assign('is_editor', 'yes');
		}
		
		if (_MOD_SOCIAL && $userdata['unread_notifications_count'] > 0)
		{
			$smarty->assign('notification_count', $userdata['unread_notifications_count']);
		}
		else
		{
			//$smarty->assign('notification_list', array());
			$smarty->assign('notification_count', 0);
		}
		
		if ($userdata['last_signin'] < ($time_now - 300))
		{
			$sql = "UPDATE pm_users 
					SET last_signin  = '". $time_now ."', 
						last_signin_ip = '". addslashes(pm_get_ip()) ."' 
					WHERE id = '". $userdata['id'] ."'";
			@mysql_query($sql);
		}
		$modframework->trigger_hook('islogged_islogged');
	}
	else
	{
		logout();
		$logged_in = 0;
	}
}
$smarty->assign('logged_in', $logged_in);

$modframework->trigger_hook('islogged_bottom');
