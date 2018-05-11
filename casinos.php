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
$cas_name = secure_sql($_GET['cas']);
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

if ( ! empty ($cas_name))
{
    $casinos = $cas_id = get_casinosid($cas_name); // $cats is from before refactoring; may be used in plugins

    if ( empty ($cas_id))
    {
        header("Location: index."._FEXT);
        exit;
    }

    $limit = _BROWSER_PAGE;
    $casino_name = get_casinosname($cas_name);
    $list_subcasinos = list_subcasinos($cas_id, '');
}
else
{
    $casinos_data = load_casinos(array('db_table' => 'pm_casinos', 'with_image' => true));

    $smarty->assign('list_casinos', list_casinos());

    $smarty->assign('casinos_data', $casinos_data);

    $smarty->assign('meta_title', htmlspecialchars(_SITENAME .' - '. $lang['_categories']));
    $smarty->assign('meta_keywords', '');
    $smarty->assign('meta_description', '');
    $smarty->assign('template_dir', $template_f);
    $smarty->assign('order', $sortby);
    $smarty->display('video-casinos-page.tpl');
    exit();
}
$modframework->trigger_hook('category_mid_1');
// Default Page
if (empty($page) || !is_numeric($page) || $page < 0 ) { $page = 1 ; }
if(is_numeric($cas_id)){

    $resync = 0;
    $casino = array();
    $total_items = 0;

    $casinos = load_casinos(array('db_table' => 'pm_casinos'));

    foreach ($casinos as $c_id => $c)
    {
        if ($c_id == $cas_id)
        {
            $casino = $c;
        }
    }

    $total_items = $casino['published_videos'];

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
					AND (casino LIKE '%,$cas_id,%' 
						 OR casino like '%,$cas_id' 
						 OR casino like '$cas_id,%' 
						 OR casino='$cas_id') 
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
            $videos = get_video_list_by_casino('', '', 0, $limit, 0, array(), $uniq_ids);
        }
        else
        {
            $videos = get_video_list_by_casino($sql_sortby, $order, $set_limit, $limit, $cas_id);
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
        else if (($row_count + $set_limit >= $total_items) && ($casino['total_videos'] > $total_items))
        {
            $resync = 1;
        }
        else if (($row_count + $set_limit >= $casino['published_videos'])  && $page == $total_pages)
        {
            $resync = 1;
        }
    }

    if ($resync)
    {
        $sql_t = "SELECT COUNT(*) as total FROM pm_videos WHERE (casino LIKE '%,$cas_id,%' OR casino LIKE '%,$cas_id' OR casino LIKE '$cas_id,%' OR casino='$cas_id')";
        $result_t = @mysql_query($sql_t);
        $row = @mysql_fetch_assoc($result_t);
        $total_items = $row['total'];
        @mysql_free_result($result_t);
        @mysql_query("UPDATE pm_casinos SET total_videos = '". $total_items ."' WHERE id = '". $cas_id ."'");

        $sql_t = "SELECT COUNT(*) as total FROM pm_videos WHERE added <= '". time() ."' AND (casino LIKE '%,$cas_id,%' OR casino LIKE '%,$cas_id' OR casino LIKE '$cas_id,%' OR casino='$cas_id')";
        $result_t = @mysql_query($sql_t);
        $row = @mysql_fetch_assoc($result_t);
        $total_items = $row['total'];
        @mysql_free_result($result_t);
        @mysql_query("UPDATE pm_casinos SET published_videos = '". $total_items ."' WHERE id = '". $cas_id ."'");
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
        $filename = 'casinos.php';
        if($cas_name != '')	$append_url .= 'cas='		. $cas_name;
        if($page != '')		$append_url .= '&page='	. $page;
        if($sortby != '')	$append_url .= '&sortby='	. $sortby;
        if($order != '')	$append_url .= '&order='	. $order;
    }
    else
    {
        $filename = "browse-" . $cas_name . "-videos-" . $page . "-" . $sortby . ".html";
    }

    $pagination = generate_smart_pagination($page, $total_items, $limit, 1, $filename, $append_url, _SEOMOD);
}


$meta_title = $casino['meta_title'];
if ($casino['meta_title'] == '')
{
    if($page != 1 && is_numeric($page))
    {
        $meta_title = $casino_name." ".$lang['videos']." - ".sprintf($lang['page_number'], $page);
    }
    else
    {
        $meta_title = $casino_name." ".$lang['videos'];
    }
}
$meta_description = ($casino['meta_description'] != '') ? $casino['meta_description'] : $casino_name." ".$lang['videos']." - "._SITENAME." - ". $page;
$meta_keywords = $casino['meta_keywords'];

// MAKE SORT BY STICK
if(!empty($cas_id)) {
    $list_casinos = list_casinos(0, $cas_id);
    $smarty->assign('list_casinos', $list_casinos);
} else {
    $list_casinos = list_casinos(0, '');
    $smarty->assign('list_casinos', $list_casinos);
}
if($config['show_tags'] == 1)
{
    $tag_cloud = tag_cloud(0, $config['tag_cloud_limit'], $config['shuffle_tags']);
    $smarty->assign('tags', $tag_cloud);
    $smarty->assign('show_tags', 1);
}
$smarty->assign('cas_id', $cas_id);
$smarty->assign('problem', $problem);
$smarty->assign('gv_casino_name', $casino_name);
$smarty->assign('gv_cas', $cas_name);
$smarty->assign('gv_pagenumber', $page);
$smarty->assign('gv_sortby', $sortby);
$smarty->assign('gv_casino_description', $casino['description']);

$smarty->assign('list_subcasinos', $list_subcasinos);
$smarty->assign('pagination', $pagination);

$smarty->assign('page_count_info', $page_count_info);
$smarty->assign('pag_left', $pag_left);
$smarty->assign('pag_right', $pag_right);

$smarty->assign('results', $videos);
$smarty->assign('casinos_data', load_casinos()); // @since v2.7 -- use smarty var {$_video_categories} instead
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_keywords', $meta_keywords);
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->display('video-casino.tpl');
