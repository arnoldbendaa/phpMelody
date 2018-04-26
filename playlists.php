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
// | Copyright: (c) 2004-2014 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

session_start();
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

$playlist_uniq_id = rtrim($_GET['playlist'], '/');

if (empty($playlist_uniq_id) && ! is_user_logged_in())
{
	header('Location: '. _URL .'/index.'. _FEXT);
	exit();
}

if ($playlist_uniq_id != '')
{
	$playlist = get_playlist($playlist_uniq_id);
	
	if (is_array($playlist) && count($playlist) > 0)
	{
		$playlist_items = playlist_get_items($playlist['list_id'], 0, $playlist['items_count'], $playlist['sorting']);
	}
	else
	{
		$playlist = false;
	}

	if ($playlist['user_id'] == $userdata['id'])
	{
		$smarty->assign('my_playlist', true);
	}
	else
	{
		$smarty->assign('my_playlist', false);
	}
	
	$meta_title = ucfirst($playlist['user_name']) .' - '. $lang['my_playlists'];
	$tpl_file = 'profile-playlist-view.tpl';

	$smarty->assign('playlist', $playlist);
	$smarty->assign('playlist_items', $playlist_items);
	$smarty->assign('share_link', $playlist['playlist_href']);
	$smarty->assign('share_link_urlencoded', urlencode($playlist['playlist_href']));
	$smarty->assign('share_title_urlencoded', urlencode(htmlspecialchars_decode($playlist['title'])));
}
else
{
	$meta_title = ucfirst($userdata['name']) .' - '. $lang['my_playlists'];
	$tpl_file = 'profile-playlists.tpl';
	
	if (count($userdata['playlists']) == 0)
	{
		$playlists_count = (int) count_entries('pm_playlists', 'user_id', $userdata['id']);
		$playlists = get_user_playlists($userdata['id'], false, false, 0, $playlists_count);
	}
	else
	{
		$playlists = $userdata['playlists'];
	}
	
	$smarty->assign('allow_playlists', (int) $config['allow_playlists']);
	$smarty->assign('playlists', $playlists);
}

$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', '');
$smarty->assign('template_dir', $template_f);

$smarty->display($tpl_file);
