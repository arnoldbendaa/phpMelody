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
require ('config.php');
require_once ('include/functions.php');
require_once ('include/user_functions.php');
require_once ('include/islogged.php');

$page = $_GET['page'];
$sortby = secure_sql($_GET['sortby']);
$order = secure_sql($_GET['order']);
$startwith = trim($_GET['letter']);
$act = $_GET['do'];

if (!in_array($sortby, array('name', 'country', 'lastseen')))
{
	$sortby = '';
}

if (!in_array($order, array('desc', 'asc', 'DESC', 'ASC')))
{
	$order = '';
}

if ($sortby == 'name')
{
	$sql_sortby = 'username';
	$order = 'ASC';
}
elseif ($sortby == 'country')
{
	$sql_sortby = 'country';
	$order = 'ASC';
}
elseif ($sortby == 'lastseen')
{
	$sql_sortby = 'last_signin';
	$order = 'DESC';
}

$limit = _BROWSER_PAGE;
// define meta tags & common variables
$meta_title = $lang['members'];
if (! empty ($page) && $page > 1)
{
	$meta_title .= ' - '.sprintf($lang['page_number'], $page);
}
$meta_title .= ' - '._SITENAME;
$meta_title = sprintf($meta_title, _SITENAME);
$meta_description = $meta_title;

// end

if ($startwith != '')
{
	$is_letter = 1;
	if ($startwith != 'other')
		$startwith = substr($startwith, 0, 1);
	if (!ctype_alnum($startwith))
		$is_letter = 0;
}
else
{
	$is_letter = 0;
}

if ( empty ($page) || !is_numeric($page) || $page < 0)
{
	$page = 1;
}

$x = 2;
$sql = "SELECT COUNT(*) as total_users FROM pm_users WHERE power IN ('".U_ACTIVE."', '".U_ADMIN."', '".U_MODERATOR."', '".U_EDITOR."')";
if ($is_letter == 1 && $startwith != 'other')
{
	$sql .= " AND username LIKE '".$startwith."%'";
}
elseif ($is_letter == 1 && $startwith == 'other')
{
	for ($i = 97; $i < 123; $i++)
	{
		$sql .= " AND username NOT LIKE '".chr($i)."%'";
	}
}
//	Show online users only - MOD
if (strtolower($act) == 'online')
{
	$sql .= " AND last_signin > '". (time() - 300)."'";
}
$result = @mysql_query($sql);
$row = @mysql_fetch_assoc($result);
$total_items = $row['total_users'];
//$total_items = @mysql_num_rows($result) ;
$total_pages = ceil($total_items / $limit);
$set_limit = $page * $limit - ($limit);
mysql_free_result($result);

load_countries_list();

$user_list = array();

if ($total_items == 0 && strtolower($act) == 'online')
{
	$problem = $lang['memberlist_msg1'];
}
elseif ($total_items == 0)
{
	$problem = $lang['memberlist_msg2'];
}
else
{
	if (isset ($sortby) && isset ($order))
	{
		( empty ($sql_sortby)) ? $sql_sortby = "id" : "";
		( empty ($order)) ? $order = "DESC" : "";
	}
	$sql .= " ORDER BY ".$sql_sortby." $order LIMIT $set_limit, $limit";
	$sql = str_replace('COUNT(*) as total_users', 'id', $sql);
	$result = mysql_query($sql);
	
	$i = 0;
	$is_user_logged_in = is_user_logged_in();
	while ($row = mysql_fetch_assoc($result))
	{
		$is_online = islive($row['last_signin']);
		$banned = banlist($row['id']);
		
		$user_list[$row['id']] = $row;
		$user_list[$row['id']] = get_user_data($row['id']);
	}
	mysql_free_result($result);
	
	if (_MOD_SOCIAL && is_user_logged_in())
	{
		$my_following_list = $my_followers_list = array();
		$user_ids = array();
		
		foreach ($user_list as $uid=>$u)
		{
			$user_ids[] = $uid;
		}
		
		check_multiple_relationships($user_ids, $my_followers_list, $my_following_list);
		
		foreach ($user_list as $i=>$u)
		{
			if ($userdata['id'] != $i)
			{
				$user_list[$i]['is_following_me'] = (in_array($i, $my_followers_list)) ? true : false;
				$user_list[$i]['am_following'] = (in_array($i, $my_following_list)) ? true : false;
			}
		}
	}
	
}

$pagination = '';
if ($total_items > $limit)
{
	$filename = 'memberlist.'._FEXT;
	
	$extra = '';
	
	if ($is_letter)
	{
		$extra = 'letter='.$startwith;
	}
	elseif (strtolower($act) == 'online')
	{
		$extra = 'do='.$act;
	}
	else
	{
		$extra = 'sortby='.$sortby;
	}
	
	$pagination = generate_smart_pagination($page, $total_items, $limit, 1, $filename, $extra);
}

$list_cats = list_categories(0, '');
$smarty->assign('list_categories', $list_cats);
$smarty->assign('problem', $problem);
$smarty->assign('gv_pagenumber', $page);
$smarty->assign('gv_sortby', $sortby);
$smarty->assign('pagination', $pagination);
$smarty->assign('page_count_info', $page_count_info);
$smarty->assign('pag_left', $pag_left);
$smarty->assign('pag_right', $pag_right);

$smarty->assign('user_list', $user_list);
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->display('memberlist.tpl');
