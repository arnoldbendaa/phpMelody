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
$modframework->trigger_hook('category_top');
$provider_name = secure_sql($_GET['provider']);
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
if ( ! empty ($provider_name))
{
    $providers = $provider_id = get_providersid($provider_name); // $cats is from before refactoring; may be used in plugins

    if ( empty ($provider_id))
    {
        header("Location: index."._FEXT);
        exit;
    }

    $limit = _BROWSER_PAGE;
    $provider_name = get_providersname($provider_name);
    $list_subproviders = list_subproviders($provider_id, '');
}
else
{
    $categories_data = load_categories(array('db_table' => 'pm_categories', 'with_image' => true));
    $casinos_data = load_casinos(array('db_table' => 'pm_casinos', 'with_image' => true));
    $providers_data = load_providers(array('db_table' => 'pm_providers', 'with_image' => true));

    $smarty->assign('list_providers', list_providers());

    $smarty->assign('categories_data', $categories_data);
    $smarty->assign('casinos_data', $casinos_data);
    $smarty->assign('providers_data', $providers_data);
    $smarty->assign('meta_title', htmlspecialchars(_SITENAME .' - '. $lang['_categories']));
    $smarty->assign('meta_keywords', '');
    $smarty->assign('meta_description', '');
    $smarty->assign('template_dir', $template_f);
    $smarty->assign('order', $sortby);
    $smarty->display('video-providers-page.tpl');
    exit();
}
$modframework->trigger_hook('category_mid_1');
// Default Page
if (empty($page) || !is_numeric($page) || $page < 0 ) { $page = 1 ; }
if(is_numeric($provider_id)){

    $resync = 0;
    $provider = array();
    $total_items = 0;

    $providers = load_providers(array('db_table' => 'pm_providers'));

    foreach ($providers as $c_id => $c)
    {
        if ($c_id == $provider_id)
        {
            $provider = $c;
        }
    }

    $total_items = $provider['published_videos'];

    if ($total_items < 10)
    {
        $resync = 1;
    }

    $total_pages = ceil($total_items / $limit);
    $set_limit = $page * $limit - ($limit);

    if ($total_items == 0)
    {
        $problem = $lang['browse_msg2'];
    }
    else
    {
        if (isset($sortby) && isset($order))
        {
            $sql_sortby	= ($sql_sortby == '') ? "added" : $sql_sortby;
            $order		= ($order == '') ? "DESC" : $order;
        }

        if ($sortby == 'rating')
        {
            $sql = "SELECT pm_videos.uniq_id, COALESCE(pm_bin_rating_meta.score, 0) as score 
					FROM pm_videos 
					LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
					WHERE added <= '". $time_now_minute ."' 
					AND (provider LIKE '%,$provider_id,%' 
						 OR provider like '%,$provider_id' 
						 OR provider like '$provider_id,%' 
						 OR provider='$provider_id') 
					ORDER BY score DESC
					LIMIT $set_limit, $limit" ;

            $modframework->trigger_hook('category_mid_2');
            $result = mysql_query($sql);
            $uniq_ids = array();
            while ($row = mysql_fetch_array($result))
            {
                $uniq_ids[] = $row['uniq_id'];
            }
            mysql_free_result($result);
            $modframework->trigger_hook('category_mid_3');
            $videos = array();
            $videos = get_video_list_by_provider('', '', 0, $limit, 0, array(), $uniq_ids);
        }
        else
        {
            $videos = get_video_list_by_provider($sql_sortby, $order, $set_limit, $limit, $provider_id);
        }

        if ($page == $total_pages && $total_items > 0 && $row_count == 0)
        {
            $resync = 1;
        }
        else if ($row_count == 0 && $page > 1 && $total_items > 0)
        {
            $resync = 1;
        }
        else if (($row_count + $set_limit > $total_items) && $total_items > 0)
        {
            $resync = 1;
        }
        else if (($row_count + $set_limit >= $total_items) && ($provider['total_videos'] > $total_items))
        {
            $resync = 1;
        }
        else if (($row_count + $set_limit >= $provider['published_videos'])  && $page == $total_pages)
        {
            $resync = 1;
        }
    }

    if ($resync)
    {
        $sql_t = "SELECT COUNT(*) as total FROM pm_videos WHERE (provider LIKE '%,$provider_id,%' OR provider LIKE '%,$provider_id' OR provider LIKE '$provider_id,%' OR provider='$provider_id')";
        $result_t = @mysql_query($sql_t);
        $row = @mysql_fetch_assoc($result_t);
        $total_items = $row['total'];
        @mysql_free_result($result_t);
        @mysql_query("UPDATE pm_providers SET total_videos = '". $total_items ."' WHERE id = '". $provider_id ."'");

        $sql_t = "SELECT COUNT(*) as total FROM pm_videos WHERE added <= '". time() ."' AND (provider LIKE '%,$provider_id,%' OR provider LIKE '%,$provider_id' OR provider LIKE '$provider_id,%' OR provider='$provider_id')";
        $result_t = @mysql_query($sql_t);
        $row = @mysql_fetch_assoc($result_t);
        $total_items = $row['total'];
        @mysql_free_result($result_t);
        @mysql_query("UPDATE pm_providers SET published_videos = '". $total_items ."' WHERE id = '". $provider_id ."'");
    }
    $modframework->trigger_hook('category_mid_4');
}
else {
    header("Location: index."._FEXT);
    exit();
}

// generate smart pagination
$pagination = '';
if ($total_items > $limit)
{
    $append_url = '';

    if ( ! _SEOMOD)
    {
        $append_url = '';
        $filename = 'providers.php';
        if($provider_name != '')	$append_url .= 'provider='		. $provider_name;
        if($page != '')		$append_url .= '&page='	. $page;
        if($sortby != '')	$append_url .= '&sortby='	. $sortby;
        if($order != '')	$append_url .= '&order='	. $order;
    }
    else
    {
        $filename = "browse-" . $provider_name . "-videos-" . $page . "-" . $sortby . ".html";
    }

    $pagination = generate_smart_pagination($page, $total_items, $limit, 1, $filename, $append_url, _SEOMOD);
}


$meta_title = $provider['meta_title'];
if ($provider['meta_title'] == '')
{
    if($page != 1 && is_numeric($page))
    {
        $meta_title = $provider_name." ".$lang['videos']." - ".sprintf($lang['page_number'], $page);
    }
    else
    {
        $meta_title = $provider_name." ".$lang['videos'];
    }
}
$meta_description = ($provider['meta_description'] != '') ? $provider['meta_description'] : $provider_name." ".$lang['videos']." - "._SITENAME." - ". $page;
$meta_keywords = $provider['meta_keywords'];

// MAKE SORT BY STICK
if(!empty($provider_id)) {
    $list_providers = list_providers(0, $provider_id);
    $smarty->assign('list_providers', $list_providers);
} else {
    $list_providers = list_providers(0, '');
    $smarty->assign('list_providers', $list_providers);
}
if($config['show_tags'] == 1)
{
    $tag_cloud = tag_cloud(0, $config['tag_cloud_limit'], $config['shuffle_tags']);
    $smarty->assign('tags', $tag_cloud);
    $smarty->assign('show_tags', 1);
}
$smarty->assign('provider_id', $provider_id);
$smarty->assign('problem', $problem);
$smarty->assign('gv_provider_name', $provider_name);
$smarty->assign('gv_provider', $provider_name);
$smarty->assign('gv_pagenumber', $page);
$smarty->assign('gv_sortby', $sortby);
$smarty->assign('gv_provider_description', $provider['description']);

$smarty->assign('list_subproviders', $list_subproviders);
$smarty->assign('pagination', $pagination);

$smarty->assign('page_count_info', $page_count_info);
$smarty->assign('pag_left', $pag_left);
$smarty->assign('pag_right', $pag_right);

$smarty->assign('results', $videos);
$smarty->assign('categories_data', load_categories()); // @since v2.7 -- use smarty var {$_video_categories} instead
$smarty->assign('casinos_data', load_casinos()); // @since v2.7 -- use smarty var {$_video_categories} instead
$smarty->assign('providers_data', load_providers()); // @since v2.7 -- use smarty var {$_video_categories} instead
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_keywords', $meta_keywords);
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->display('video-provider.tpl');
