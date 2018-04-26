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

function top_videos_list_categories_display_item($item, &$all_children, $level = 0, $options)
{
	$li_class = $caturl = $output = $li_item = '';

	if ( ! $item)
		return;
	
	$padding = str_repeat($options['spacer'], $level);
		
	// href
	$caturl = _URL .'/topvideos.'. _FEXT .'?c='. $item['tag'];
	
	$sub_cats = '';

	if (isset($all_children[$item['id']]) && ($level < $options['max_levels'] || $options['max_levels'] == 0))
	{
		$sub_cats .= "\n";
		
		foreach ($all_children[$item['id']] as $k => $child)
		{
			if ( ! isset($newlevel))
			{
				$newlevel = true;
				$subcats_ul_class = ($child['id'] == $options['selected'] || $options['expand_items'] == true) ? 'ul-sub-menu' : 'ul-sub-menu';
				$sub_cats .= $padding."<ul class='".$subcats_ul_class."'>\n";
			}
			$sub_cats .= top_videos_list_categories_display_item($child, $all_children, $level+1, $options);
		}
		unset($all_children[$item['id']]);
	}
	
	// li class
	if ($item['id'] == $options['selected'])
	{
		$li_class .= ' selected';
	}
	
	if ($options['selected_grandfather'] > 0)
	{
		if ($item['id'] == $options['selected_grandfather'])
		{
			$li_class .= ' selected';
		}
	}
		
	// li
	$output .= $padding .'<li class="'. $li_class .'"><a href="'. $caturl .'">'. htmlentities($item['name'],ENT_COMPAT,'UTF-8') .'</a>';
	$output .= $sub_cats;
	
	if (isset($newlevel) && $newlevel)
	{
		$output .= $padding."</ul>\n";
	}
		
	$output .= $padding."</li>\n";
	
	return $output;
}

function top_videos_list_categories($selected = 0, $args = array()) 
{
	$output = '';
	
	$defaults = array(
		'db_table' => 'pm_categories',
		'selected' => 0, 
		'order_by' => 'position',
		'sort' => 'ASC',
		'selected_grandfather' => 0, 
		'spacer' => "\t",
		'max_levels' => 1,
		'ul_wrapper' => true
	);
	
	$options = array_merge($defaults, $args);
	$options['selected'] = ( ! is_object($selected)) ? $selected : 0;
	extract($options);
	
	$parents = $parent_ids = $children = array();
	$categories = load_categories($options);
	
	foreach ($categories as $c_id => $c)
	{
		if ($c['parent_id'] == 0)
		{
			$parents[] = $c;
			$parent_ids[] = $c['id'];
		}
		else
		{
			$children[$c['parent_id']][] = $c;
		}
	}

	// find "grandfather" of selected child category
	if (count($parent_ids) > 0 && $selected > 0 && ( ! in_array($selected, $parent_ids)))
	{
		$options['selected_grandfather'] = $selected;

		$counter = 0;
		$exit_limit = count($parent_ids) * 3;
		while (( ! in_array($options['selected_grandfather'], $parent_ids)) && $counter < $exit_limit)
		{
			$find = $options['selected_grandfather'];
			foreach ($children as $pid => $children_arr)
			{
				$found = false;
			
				if (count($children_arr) > 0)
				{
					foreach ($children_arr as $k => $child)
					{
						if ($child['id'] == $find)
						{
							$found = true;
							$options['selected_grandfather'] = $child['parent_id'];
							break;
						}
					}
					if ($found)
					{
						break;
					}
				}
			}
			
			$counter++;
		}
	}
	
	foreach ($parents as $k => $p)
	{
		$options['expand_items'] = ($options['selected_grandfather'] == $p['id']) ? true : false;
		$output .= top_videos_list_categories_display_item($p, $children, 0, $options);
	}

	if (count($children) > 0 && $options['max_levels'] == 0)
	{
		foreach ($children as $parent_id => $orphans)
		{
			foreach ($orphans as $k => $orphan)
			{
				$orphan['parent_id'] = 0;
				$output .= top_videos_list_categories_display_item($orphan, $empty, 0, $options);
			}
		}
	}
	
	//	wrapper
	if ($ul_wrapper)
	{
		// return "<ul id='ul_categories'>\n".$output."\n</ul>"; // @deprecated since v2.3
	}
	
	return $output;
}


session_start();
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');
require_once('include/rating_functions.php');

$page = (int) $_GET['page'];
if ( ! $page)
{
	$page = 1;
}
$limit	= $config['top_page_limit'];
$from 	= $page * $limit - ($limit);
$total_videos = (int) $config['published_videos'];
$total_pages = ceil($total_videos / $limit);

//	Reset chart?
$span = ($config['chart_days'] * (3600 * 24))/4;
if($span > 0)
{
	if($config['chart_last_reset'] < ($time_now - $span))
	{
		reset_chart();
	}
}
$modframework->trigger_hook('topvideos_top');

$cats = trim($_GET['c']);
if ( ! preg_match('/(^[a-zA-Z0-9_-]+)$/i', $cats))
{
	$cats = '';
}
$action = trim($_GET['do']);

if ( ! in_array($action, array('recent', 'rating')))
{
	$action = '';
}

$categories_list = categories_dropdown(array('options_only' => true, 'select_all_option' => false, 'value_attr_db_col' => 'tag', 'selected' => $cats));
$categories_list = preg_replace('/value="(.*?)"/', 'value="'. _URL .'/topvideos.'. _FEXT .'?c=$1"', $categories_list);
$categories_ul_list = top_videos_list_categories($selected = 0, $args = array());

if ($cats != '') 
{
	$catid = get_catid($cats);
	$cat_name = get_catname($cats);	
	
	$sql = "SELECT published_videos 
			FROM pm_categories
			WHERE id = '". $catid ."'";
	$result = mysql_query($sql); 
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$total_videos = $row['published_videos'];
	
	unset($sql, $result, $row);
	
	$sql = "SELECT id  
			 FROM pm_videos 
			 WHERE added <= '". $time_now_minute ."'
			 AND (category LIKE '$catid' 
			 	  OR category LIKE '$catid,%' 
			 	  OR category LIKE '%,$catid' 
			 	  OR category LIKE '%,$catid,%') 
			 ORDER BY site_views DESC LIMIT $from, $limit";
} 
elseif ($action == 'recent') 
{
	$videos = get_chart(50);
	$total_videos = 50;
}
elseif ($action == 'rating')
{
	$sql = "SELECT id, COALESCE(pm_bin_rating_meta.score, 0) as score 
			FROM pm_videos 
			LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
			WHERE added <= '". $time_now_minute ."'
			ORDER BY score DESC 
			LIMIT $from, $limit" ;
}
else 
{
	$sql = "SELECT id 
			FROM pm_videos 
			WHERE added <= '". $time_now_minute ."' 
			ORDER BY site_views DESC 
			LIMIT $from, $limit" ;

}

if($action == '')
{
	$ids = array();
	$result = mysql_query($sql);
	while ($row = mysql_fetch_assoc($result))
	{
		$ids[] = $row['id'];
	}
	mysql_free_result($result);
	
	$list = array();
	if (count($ids) > 0)
	{
		$list = get_video_list('site_views', 'DESC', 0, 0, 0, $ids);
	}
	
	if ($page == $total_pages || $total_pages == 0)
	{
		// recount published_videos
		$count = count_entries('pm_videos', '1',  '1\' AND added <= \''. time());
		if ($config['published_videos'] != $count)
		{
			$total_videos = $count;
			update_config('published_videos', $count);
		}
	}
	
}
elseif ($action == 'recent') 
{
	$list = array();

	if (count($videos) > 0)
	{
		$ids = array();
		foreach ($videos as $uniq_id => $v)
		{
			$ids[] = $v['id'];
		}
		
		$list = get_video_list('', '', 0, 0, 0, $ids);
	}
}
elseif ($action == 'rating')
{
	$ids = array();
	$result = mysql_query($sql);
	while ($row = mysql_fetch_assoc($result))
	{
		$ids[] = $row['id'];
	}
	mysql_free_result($result);
	
	if (count($ids) > 0)
	{
		$list = array();
		$list = get_video_list('', '', 0, 0, 0, $ids);
	}
}

$count_last_days = count_days($config['chart_last_reset'], time());

if($config['chart_days'] == 0) 
{
	if($count_last_days >= 0 && $count_last_days <= 1)
	{
		$smarty->assign('chart_days', $lang['yesterdays_top']);
	}
	else
	{
		$smarty->assign('chart_days', sprintf($lang['top_videos_last_x_days'], $count_last_days));
	}
} 
else 
{
	if(($count_last_days >= 0 && $count_last_days <= 1) && $config['chart_days'] <= 1)
	{
		$smarty->assign('chart_days', $lang['yesterdays_top']);
	}
	else
	{
		$smarty->assign('chart_days', sprintf($lang['top_videos_last_x_days'], $config['chart_days']));
	}
}

$i = 1;
foreach ($list as $k => $v)
{
	$list[$k]['position'] = $from + $i++;
}

$pagination = '';
if ($total_videos > $limit)
{
	$filename = (_SEOMOD) ? 'topvideos.html' : 'topvideos.php';
	
	$extra = '';
	if ($cats != '')
	{
		$extra = 'c='.$cats;
	}
	if ($action != '')
	{
		$extra = 'do='.$action;
	}
	
	$pagination = generate_smart_pagination($page, $total_videos, $limit, 1, $filename, $extra);
}

// define meta tags & common variables
$meta_title = $lang['top_m_videos_from'];
if(!empty($date)) {
	$meta_title .= ' - '.$lang["added"].' '.$date;
} 
if(!empty($cats)) {
	$meta_title .= ' - '.$cat_name;
}
if(!empty($page) && $page > 1) {
	$meta_title .= ' - '.sprintf($lang['page_number'], $page);
}
$meta_title = sprintf($meta_title, _SITENAME);
$meta_description = $meta_title;
// end
$smarty->assign('cat_name', $cat_name);
$smarty->assign('results', $list);
$smarty->assign('categories_list', $categories_list);
$smarty->assign('categories_ul_list', $categories_ul_list);
$smarty->assign('pagination', $pagination);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$modframework->trigger_hook('topvideos_bottom');
$smarty->display('video-top.tpl');
?>