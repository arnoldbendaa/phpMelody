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
require_once('include/rating_functions.php');

$modframework->trigger_hook('newvideos_top');
$page = (int) $_GET['page'];
if ( ! $page)
{
	$page = 1;
}
$limit	= $config['new_page_limit'];
$from 	= $page * $limit - ($limit);
$date 	= $_GET['d'];
$total_videos = 0;

if ( ! in_array($date, array('today', 'yesterday', 'month')))
{
	$date = '';
}

//	count total videos
switch ($date)
{
	default: 
		
		$total_videos = $config['published_videos'];
		
		$total_pages = ceil($total_videos / $limit);
		
		if ($page == $total_pages || $total_pages == 0)
		{
			// recount published videos count
			$count = count_entries('pm_videos', '1',  '1\' AND added <= \''. time());
			if ($config['published_videos'] != $count)
			{
				$total_videos = $count;
				update_config('published_videos', $count);
			}
		}
		
	break;
	
	case 'today':
	case 'yesterday':
	case 'month':
		
		switch ($date)
		{
			case 'today':
				$time = getdate();
				$time_start = mktime(0, 0, 0, $time['mon'], $time['mday'], $time['year']);
				$time_end	= mktime(23, 59, 59, $time['mon'], $time['mday'], $time['year']);
			break;
			
			case 'yesterday':
				$time = getdate();
				$time_start = mktime(0, 0, 0, $time['mon'], $time['mday'] - 1, $time['year']);
				$time_end	= mktime(23, 59, 59, $time['mon'], $time['mday'] - 1, $time['year']);
			break;
			
			case 'month':
				$time = getdate();	
				$days_this_month = (int) date('t', mktime(0,0,0, $time['mon'], 1, $time['year']));
				
				$time_start = mktime(0, 0, 0, $time['mon'], 1, $time['year']);
				$time_end	= mktime(23, 59, 59, $time['mon'], $days_this_month, $time['year']);
			break;
		}
		
		$sql = "SELECT COUNT(*) as total_found 
				FROM pm_videos 
				WHERE added >= '". $time_start ."' 
				  AND added <= '". $time_end ."'";
		$result = @mysql_query($sql);
		
		if ( ! $result)
		{
			$total_videos = 0;
		}
		else
		{
			$row = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			$total_videos = (int) $row['total_found'];
			unset($row);
		}
		
	break;
}

if ($total_videos > 0)
{
	switch ($date)
	{
		default: 
				
			$sql = "SELECT id  
					FROM pm_videos 
					WHERE added <= '". $time_now_minute ."' 
					ORDER BY added DESC 
					LIMIT ". $from .",". $limit;
		break;
		
		case 'today':
		case 'yesterday':
		case 'month':
			
			$sql = "SELECT id  
					FROM pm_videos 
					WHERE added >= '". $time_start ."' 
				  	  AND added <= '". $time_end ."' 
					ORDER BY added DESC 
					LIMIT ". $from .",". $limit;	
		break;
	}
	
	$result = mysql_query($sql);
	$ids = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$ids[] = $row['id'];
	}
	mysql_free_result($result);

	$list = get_video_list('added', 'DESC', 0, 0, 0, $ids);
}

//	generate smart pagination
$pagination = '';

if ($total_videos > $limit)
{
	$filename = (_SEOMOD) ? 'newvideos.html' : 'newvideos.php';
	
	$extra = '';
	if ($date != '')
	{
		$extra = 'd='. $date;
	}
	
	$pagination = generate_smart_pagination($page, $total_videos, $limit, 1, $filename, $extra);
}

// define meta tags & common variables
$meta_title = $lang['nv_page_title'];
if(!empty($date)) {
	$meta_title .= ' - '.$lang["added"].' '.$date;
} 
if(!empty($page) && $page > 1) {
	$meta_title .= ' - '.sprintf($lang['page_number'], $page);
}
$meta_title = sprintf($meta_title, _SITENAME);
$meta_description = $meta_title;
// end

$smarty->assign('total_videos', $total_videos);
$smarty->assign('cat_name', $cat_name);
$smarty->assign('results', $list);
$smarty->assign('categories_list', $categories_list);
$smarty->assign('pagination', $pagination); 

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$modframework->trigger_hook('newvideos_bottom');
$smarty->display('video-new.tpl');
?>