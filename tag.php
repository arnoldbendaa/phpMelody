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

$page = $_GET['page'];

if(empty($page) || !is_numeric($page) || $page == '')
	$page = 1;
	
$limit = _BROWSER_PAGE;
$from = $page * $limit - ($limit);
$total_results = 0;
$err = '';

$tag = urldecode(trim($_GET['t']));
$tag = str_replace( array("%", "<", ">", '"', "'", '&'), '', $tag);

if($tag != '' && strlen($tag) > 1)
{
	$tag = safe_tag($tag);
	$tag = secure_sql($tag);
	
	$sql = "SELECT SQL_CALC_FOUND_ROWS pm_tags.uniq_id 
				FROM pm_tags 
				JOIN pm_videos ON (pm_tags.uniq_id = pm_videos.uniq_id)
				WHERE pm_tags.safe_tag LIKE '$tag' 
				  AND pm_videos.added <= $time_now_minute 
				LIMIT $from, $limit"; 
	
	if ( ! $result = mysql_query($sql))
	{
		$err = $lang['search_results_msg1'];
	}
	else
	{
		$v_ids = array();
		$videos = '';
		
		$result_calc = mysql_query('SELECT FOUND_ROWS()');
		$row = mysql_fetch_array($result_calc);
		$total_results = (int) $row[0];
		
		$row = array();
		while($row = mysql_fetch_assoc($result))
		{	
			$v_ids[] = $row['uniq_id'];
		}
		mysql_free_result($result);
		
		$list = array();
		if ($total_results > 0)
		{
			$list = get_video_list('added', 'desc', 0, 0, 0, false, $v_ids);
		}
		else
		{
			$err = $lang['search_results_msg1'];
		}
	}
}
else
{	//	the tag is either too short, or no tag was given
	$err = $lang['search_results_msg1'];
}

// generate pagination
$pagination = '';
if ($total_results > $limit)
{
	if (_SEOMOD)
	{
		$filename = 'tags/'. $tag .'/page-'. $page .'/';
	}
	else
	{
		$filename = 'tag.php';
		$append_url = 't='. $tag;
	}
	
	$pagination = '';
	$pagination = generate_smart_pagination($page, $total_results, $limit, 1, $filename, $append_url, _SEOMOD);
}

//	Get tag's real name;
$real_tag = '';
$real_tag = safe2tag($tag);
$real_tag = ($real_tag === false) ? '' : $real_tag;

if($config['show_tags'] == 1)
{
	$tag_cloud = tag_cloud(0, $config['tag_cloud_limit'], $config['shuffle_tags']);
	$smarty->assign('tags', $tag_cloud);
	$smarty->assign('show_tags', 1);
}
// define meta tags & common variables
$meta_title = $lang['search_results'].': &quot;'.htmlspecialchars($real_tag).'&quot;';
if(!empty($page) && $page > 1) {
	$meta_title .= ' - '.sprintf($lang['page_number'], $page);
}
$meta_title = sprintf($meta_title, _SITENAME);
$meta_description = $meta_title;
// end

$smarty->assign('tags', $tag_cloud);
$smarty->assign('error_msg', $err);
$smarty->assign('searchstring', htmlspecialchars($real_tag));
$smarty->assign('results', $list);
$smarty->assign('pagination', $pagination);
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->display('video-tag.tpl');
?>