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
if($_POST['select_language'] == 1 || (strcmp($_POST['select_language'],"1") == 0))
{
	require_once('include/settings.php');
	
	$l_id = (int) $_POST['lang_id'];
	if( ! array_key_exists($l_id, $langs) )
	{
		$l_id = 1;
	}
	
	setcookie(COOKIE_LANG, $l_id, time()+COOKIE_TIME, COOKIE_PATH);
	exit();
}

//require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');
require_once('include/rating_functions.php');
$modframework->trigger_hook('index_top');

// define meta tags & common variables
if ('' != $config['homepage_title'])
{
	$meta_title = $config['homepage_title'];
}
else
{
	$meta_title = sprintf($lang['homepage_title'], _SITENAME);	
}
$meta_keywords = $config['homepage_keywords'];
$meta_description = $config['homepage_description'];
// end

$top_videos = top_videos($config['top_videos_sort'], _TOPVIDS);
$new_videos = get_video_list('added', 'desc', 0, _NEWVIDS);
$featured_videos = get_featured_video_list((int) $config['homepage_featured_limit']);
$total_featured_videos = count($featured_videos);

// pull out featured categories data
$featured_categories_data = array();
$featured_categories = ($config['homepage_featured_categories'] != '') ? unserialize($config['homepage_featured_categories']) : array();
if (count($featured_categories) > 0)
{
	load_categories();
	foreach ($_video_categories as $cid => $category_data)
	{
		$_video_categories[$cid]['url'] = make_link('category', array('tag' => $category_data['tag']));
	}
	
	foreach ($featured_categories as $k => $cid)
	{
		$featured_categories_data[$cid] = get_video_list('added', 'desc', 0, 10, $cid);
	}
}

if($config['show_tags'] == 1)
{
	$tag_cloud = tag_cloud(0, $config['tag_cloud_limit'], $config['shuffle_tags']);
	$smarty->assign('tags', $tag_cloud);
	$smarty->assign('show_tags', 1);
}

if($config['show_stats'] == 1)
{
	$stats = stats();
	$smarty->assign('stats', $stats);
	$smarty->assign('show_stats', 1);
}
//	Get latest articles
if (_MOD_ARTICLE)
{
	$articles = art_load_articles(0, $config['article_widget_limit']);

	if ( ! array_key_exists('type', $articles))
	{
		foreach ($articles as $id => $article)
		{
			$articles[$id]['title'] = fewchars($article['title'], 55);
		}
		$smarty->assign('articles', $articles);
	}
}

$playingnow = videosplaying($config['playingnow_limit']);
$total_playingnow = (is_array($playingnow)) ? count($playingnow) : 0;

if ($config['player_autoplay'] == '0' && $video['video_player'] != 'embed' && $video['source_id'] != 3) 
{
	// don't update site_views for this video. It will be updated when the user hits the play button.
}
else
{
	// in all other cases, update site_views on page load.
	if ($total_featured_videos == 1)
	{
		update_view_count($featured_videos[0]['id'], $featured_videos[0]['site_views'], false);
	}
}
// pre-roll [static] ads & subtitles
if ($total_featured_videos == 1)
{
	serve_preroll_ad('index', $featured_videos[0]);
	$smarty->assign('video_subtitles', (array) get_video_subtitles($featured_videos[0]['uniq_id']));
}

$smarty->assign('total_playingnow', $total_playingnow);
$smarty->assign('playingnow', $playingnow);

$smarty->assign('featured_videos', $featured_videos);
$smarty->assign('featured_videos_total', $total_featured_videos);
$smarty->assign('featured_channels', get_featured_channels());
$smarty->assign('new_videos', $new_videos);
$smarty->assign('top_videos', $top_videos);
$smarty->assign('categories', $_video_categories);
$smarty->assign('featured_categories_data', $featured_categories_data);
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_keywords', htmlspecialchars($meta_keywords));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
//new page
//inserted by arnold
$page = (int) $_GET['page'];
if ( ! $page)
{
    $page = 1;
}
$limit	= $config['new_page_limit'];
$from 	= $page * $limit - ($limit);
$order 	= $_GET['order'];
$total_videos = 0;

if ( ! in_array($order, array('top_today', 'top_lastWeek', 'top_lastMonth', 'top_allTime', 'popular_today', 'popular_lastWeek', 'popular_lastMonth', 'popular_allTime', 'longest')))
{
    $order = '';
}

//	count total videos
switch ($order)
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

    case 'top_today':
    case 'top_lastWeek':
    case 'top_lastMonth':
    case 'top_allTime':
    case 'popular_today':
    case 'popular_lastWeek':
    case 'popular_lastMonth':
    case 'popular_allTime':
    case 'longest':

        switch ($order)
        {
            case 'top_today':
            case 'popular_today':
                $time = getdate();
                $time_start = mktime(0, 0, 0, $time['mon'], $time['mday'], $time['year']);
                $time_end	= mktime(23, 59, 59, $time['mon'], $time['mday'], $time['year']);
                break;

            case 'top_lastWeek':
            case 'popular_lastWeek':
                $time = getdate();
                $time_start = mktime(0,0,0,date("m"),date("d")-date("w")-7);
                $time_end	= mktime(0,0,0,date("m"),date("d")-date("w"));
                break;
            case 'top_lastMonth':
            case 'popular_lastMonth':
                $time = getdate();
                $days_this_month = (int) date('t', mktime(0,0,0, $time['mon'], 1, $time['year']));

                $time_start = mktime(0, 0, 0, $time['mon'], 1, $time['year']);
                $time_end	= mktime(23, 59, 59, $time['mon'], $days_this_month, $time['year']);
                break;
            case 'top_allTime':
            case 'popular_allTime':
            case 'longest':
                $time = getdate();
                $time_start = mktime(0, 0, 0, 1, 1, $time['year']-30);
                $time_end	= mktime(0, 0, 0, 1, 1, $time['year']+10);
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
    switch ($order)
    {
        default:

            $sql = "SELECT id  
					FROM pm_videos 
					WHERE added <= '". $time_now_minute ."' 
					ORDER BY added DESC 
					LIMIT ". $from .",". $limit;
            break;

        case 'top_today':
        case 'top_lastWeek':
        case 'top_lastMonth':
        case 'top_allTime':
        case 'popular_today':
        case 'popular_lastWeek':
        case 'popular_lastMonth':
        case 'popular_allTime':
        case 'longest':
        switch ($order) {
            case 'top_today':
            case 'top_lastWeek':
            case 'top_lastMonth':
            case 'top_allTime':
                $sql = "SELECT id, COALESCE(pm_bin_rating_meta.score, 0) as score 
                            FROM pm_videos 
                            LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
                            WHERE added >= '". $time_start ."'
                            AND added <= '". $time_end ."' 
                            ORDER BY score DESC 
                            LIMIT $from, $limit" ;
                break;
            case 'popular_today':
            case 'popular_lastWeek':
            case 'popular_lastMonth':
            case 'popular_allTime':
                $sql = "SELECT id, COALESCE(pm_bin_rating_meta.score, 0) as score 
                                FROM pm_videos 
                                LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
                                WHERE added >= '". $time_start ."'
                                AND added <= '". $time_end ."' 
                                ORDER BY site_views DESC 
                                LIMIT $from, $limit" ;
                break;
            case 'longest':
                $sql = "SELECT id, COALESCE(pm_bin_rating_meta.score, 0) as score 
                            FROM pm_videos 
                            LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
                            WHERE added >= '". $time_start ."'
                            AND added <= '". $time_end ."' 
                            ORDER BY yt_length DESC 
                            LIMIT $from, $limit" ;
                break;
        }
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
// define meta tags & common variables
$meta_title = $lang['nv_page_title'];
if(!empty($order)) {
    $meta_title .= ' - '.$lang["added"].' '.$order;
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
$smarty->assign('order',$order);







$cat_name = secure_sql($_GET['cat']);
$page = $_GET['page'];
$sortby = secure_sql($_GET['sortby']);
$order = secure_sql($_GET['order']);

if ( ! in_array($sortby, array('views', 'date', 'rating', 'title')))
{
    $sortby = '';
}

if ( ! in_array($order, array('desc', 'asc', 'DESC', 'ASC')))
{
    $order = '';
}

if($sortby == 'views') {
    $sql_sortby = 'site_views';
    $order = 'DESC';
}
elseif($sortby == 'date') {
    $sql_sortby = 'added';
    $order = 'DESC';
}
elseif($sortby == 'rating') {
    $sql_sortby = 'total_value';
    $order = 'DESC';
}
elseif($sortby == 'title') {
    $sql_sortby = 'video_title';
    $order = 'ASC';
}

if ( ! empty ($cat_name))
{
    $cats = $cat_id = get_catid($cat_name); // $cats is from before refactoring; may be used in plugins

    if ( empty ($cat_id))
    {
        header("Location: index."._FEXT);
        exit;
    }

    $limit = _BROWSER_PAGE;
    $category_name = get_catname($cat_name);
    $list_subcats = list_subcategories($cat_id, '');
}
else
{
    $categories_data = load_categories(array('db_table' => 'pm_categories', 'with_image' => true));

    $smarty->assign('list_categories', list_categories());

    $smarty->assign('categories_data', $categories_data);

    $smarty->assign('meta_title', htmlspecialchars(_SITENAME .' - '. $lang['_categories']));
    $smarty->assign('meta_keywords', '');
    $smarty->assign('meta_description', '');
    $smarty->assign('template_dir', $template_f);

//    $smarty->display('video-categories-page.tpl');
//    exit();
}


$smarty->assign('catResult', $videos);
$smarty->display('index.tpl');
