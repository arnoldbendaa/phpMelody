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
require_once('include/article_functions.php');
require_once('include/rating_functions.php');


if ( ! _MOD_ARTICLE)
{
	header("Location: ". _URL ."/index.". _FEXT);
	exit();
}

$page 	 = (int) $_GET['page'];
$cat_tag = trim($_GET['c']);		//	category tag, not ID
$tag 	 = urldecode($_GET['tag']);	//	safe tag
$keyword = urldecode($_GET['keywords']);

$cid	 	= 0;
$articles 	= array();
$sticky  	= false;
$categories = array();
$total_items = 0;
$show_popular = ($_GET['show'] == 'popular') ? 1 : 0;

if ($page == 0)
{
	$page = 1;
}

$limit = $config['browse_articles'];
$from = $page * $limit - ($limit);
$resync = 0;

//	get categories
$categories = art_get_categories();

//	get articles
if($cat_tag != '')
{
	$cid = 0;
	if (is_array($categories))
	foreach ($categories as $id => $cat_arr)
	{
		if ($cat_arr['tag'] == $cat_tag)
		{
			$cid = $cat_arr['id'];
			break;
		}
	}
	
	if ($cid > 0)
	{
		$articles = art_load_articles($from, $limit, $cid);
		$total_items = $categories[$cid]['published_articles'];
	}
	else
	{
		$articles = art_load_articles($from, $limit);
		$total_items = $config['published_articles'];
	}
}
else if ($tag != '' || $keyword != '')
{
	$ids = array();
	
	if ($keyword != '')
	{
		$keyword = str_replace( array("%", ">", "<"), '', $keyword);
		$keyword = trim($keyword);
		$keyword = secure_sql($keyword);
		
		$where = '';
		$and = '';
		$terms = explode(' ', $keyword);
		$limit_terms = 10; // limit query terms
		$searched_terms = 0;
		
		foreach ($terms as $k => $term)
		{
			$term = trim($term, "\"'\n\r.,-_()[]{} ");
			
			if (strlen($term) >= 2)
			{
				$where .= "{$and} ((title LIKE '%".$term."%') OR (content LIKE '%".$term."%')) ";
				$and = ' AND ';
				$searched_terms++;
			}
			
			if ($searched_terms >= $limit_terms)
			{
				break;
			}
		}
		
		if (count($terms) > 1)
		{
			$where .= " OR ((title LIKE '%".$keyword."%') OR (content LIKE '%".$keyword."%'))";
		}
		
		$sql = "SELECT id, title, content, category, status, date, author, allow_comments, comment_count, views 
				FROM art_articles 
				WHERE date <= '". time() ."' AND (". $where .")";
	}
	else
	{
		$tag = safe_tag($tag);
		$tag = secure_sql($tag);
	
		$sql = "SELECT id, article_id, tag, safe_tag 
				FROM art_tags 
				WHERE safe_tag LIKE '". $tag ."'";	
	}
	
	$result = @mysql_query($sql);
	
	if ( ! $result)
	{
		$articles = array('type' => 'error', 'msg' => $lang['search_results_msg1']);
	}
	else
	{
		$total_results = @mysql_num_rows($result);
		if ($total_results)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$ids[] = ($tag != '') ? $row['article_id'] : $row['id'];
			}

			mysql_free_result($result);
		
			$articles =  art_load_articles($from, $limit, '', 0, $ids);
			
			$ids = implode(', ', $ids);
			
			//	Count total items;
			$sql = "SELECT COUNT(*) as total 
							FROM art_articles 
							WHERE status = '1' 
							  AND id IN (". $ids .")";
			$result = @mysql_query($sql);
			$row = @mysql_fetch_assoc($result);
			@mysql_free_result($result);
			
			$total_items = ($row['total']) ? $row['total'] : 0;
		}
		else
		{	
			$articles = array('type' => 'error', 'msg' => $lang['search_results_msg1']);
		}
	}
}
else
{
	if ($show_popular)
	{
		$ids = array();
		$sql = "SELECT id FROM art_articles 
				WHERE date <= '". time() ."' 
				ORDER BY views DESC 
				LIMIT 0, 30";
		$result = @mysql_query($sql);
	
		if ( ! $result)
		{
			$articles = array('type' => 'error', 'msg' => $lang['search_results_msg1']);
		}
		else
		{
			$total_items = mysql_num_rows($result);
			
			while ($row = mysql_fetch_assoc($result))
			{
				$ids[] = $row['id'];
			}

			mysql_free_result($result);
		
			$articles =  art_load_articles($from, $limit, '', 0, $ids, 'views');
		}
	}
	else
	{
		// default
		$articles = art_load_articles($from, $limit);
		$total_items = count_entries('art_articles', 'status', '1\' AND date <= \''. time());
		
		if ($page == 1)
		{
			$sticky = get_sticky_articles();

			if ((is_array($sticky) && count($sticky) == 0)  || empty($sticky))
			{
				$sticky = false;
			}
			else
			{
				// array_merge overwrites keys
				$temp = array();
				foreach ($sticky as $a_id => $a_data)
				{
					$temp[$a_id] = $a_data;					
				}
				foreach ($articles as $a_id => $a_data)
				{
					$temp[$a_id] = $a_data;
				}
				$articles = $temp;
				unset($temp, $sticky);
			}
		}
	}
}

if ('' != $articles['type'])
{
	$articles = $articles['msg'];
}
else
{
	foreach ($articles as $id => $article)
	{
		//	Handle excerpt
		$pieces = array();
		$pieces = explode('<hr />', $article['content']);
		$pieces[0] = rtrim($pieces[0], "\n\r\t ");

		if (strtolower(substr($pieces[0], strlen($pieces[0]) - 5)) == '<div>')
		{
			$pieces[0] = substr($pieces[0], 0, -5);
		}
		
		$articles[$id]['content'] = $pieces[0];
		
		$articles[$id]['date'] = date('M j, Y', $article['date']);
		
		unset($pieces);

		//	Handle article categories - make links
		$pieces = explode(',', $article['category']);
		foreach ($pieces as $k => $cat_id)
		{
			$name = $categories[$cat_id]['name'];
			
			$articles[$id]['pretty_cats'][$name] = $categories[$cat_id]['link'];
		}
	}
}

// generate smart pagination
$uri = '';
if ( ! strpos($_SERVER['REQUEST_URI'], '?'))
{
	$uri = ('' != $_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : 'article.php';
	$uri .= '?';
	
	if($cat_tag != '')	$uri .= '&c='	 . $cat_tag;
	if($page)			$uri .= '&page=' . $page;
	if($tag != '')		$uri .= '&tag='	 . $tag;
}
else
{
	$uri = $_SERVER['REQUEST_URI'];
}

$uri = explode('?', $uri); 
$filename = ('' != $_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : 'article.php';

if(!is_numeric($_GET['page']))
{
	$uri[1] = str_replace("page=".$_GET['page'], "page=1", $uri[1]);
}

if (_SEOMOD && $keyword == '')
{
	$uri[1] = '';
	$filename = $_SERVER['REQUEST_URI'];

	if($filename == '')
	{
		$filename = 'index-'. $page .'.'. _FEXT;
	}
	else
	{
		if(strpos($filename, "/", 0) !== FALSE)
		{
			$temp = explode("/", $filename);
			$filename = $temp[count($temp)-1];
		}
		
		if ($filename == '' || strpos($_SERVER['REQUEST_URI'], '/tag/') !== false)
		{
			$filename = $_SERVER['REQUEST_URI'];
		}
	}
}

//	Some HTML 
$total_pages = ceil($total_items / $limit);
$article_categories = art_html_list_categories($cid, $categories, array('max_levels' => 1));

if($config['show_tags'] == 1)
{
	$tag_cloud = art_tag_cloud(0, $config['tag_cloud_limit'], $config['shuffle_tags']);
	$smarty->assign('tags', $tag_cloud);
	$smarty->assign('show_tags', 1);
}

if ($cid > 0)
{
	$row_count = count($articles);
	
	// resync table counts?
	if ($page == $total_pages && $total_items > 0 && $row_count == 0)
	{
		$resync = 1;
	}
	else if ($row_count == 0 && $page > 1 && $total_items > 0)
	{
		$resync = 1;
	}
	else if (($row_count + $from > $total_items) && $total_items > 0)
	{
		$resync = 1;
	}
	else if (($row_count + $from >= $total_items) && ($categories[$cid]['total_articles'] > $total_items))
	{
		$resync = 1;
	}
	else if (($row_count + $from < $categories[$cid]['published_articles']) && $page == $total_pages)
	{
		$resync = 1;
	}
}
else if ($keyword == '' && $tag == '')
{
	if ($page == $total_pages || $total_pages == 0)
	{
		$resync = 1;
	}
}

if ($resync)
{
	if ($cid > 0)
	{
		$sql = "SELECT COUNT(*) as total 
				FROM art_articles 
				WHERE category LIKE '". $cid ."' 
				   OR category LIKE '". $cid .",%' 
				   OR category LIKE '%,". $cid ."' 
				   OR category LIKE '%,". $cid .",%'";
	 
		$result = @mysql_query($sql);
		$row = @mysql_fetch_assoc($result);
		$total_items = $row['total'];
		@mysql_free_result($result);
		@mysql_query("UPDATE art_categories SET total_articles = '". $total_items ."' WHERE id = '". $cid ."'");
		
		$sql = "SELECT COUNT(*) as total 
				FROM art_articles 
				WHERE date <= '". time() ."'
				  AND status = 1   
				  AND (category LIKE '". $cid ."' 
				   OR category LIKE '". $cid .",%' 
				   OR category LIKE '%,". $cid ."' 
				   OR category LIKE '%,". $cid .",%')"; 
		
		$result = @mysql_query($sql);
		$row = @mysql_fetch_assoc($result);
		$total_items = $row['total'];
		@mysql_free_result($result);
		@mysql_query("UPDATE art_categories SET published_articles = '". $total_items ."' WHERE id = '". $cid ."'");
	}
	
	// recount published videos count
	$count = count_entries('art_articles', 'status',  '1\' AND date <= \''. time());
	if ($config['published_articles'] != $count)
	{
		$total_items = $count;
		update_config('published_articles', $count);
	}
	
	$count = count_entries('art_articles', 'status',  '1');
	if ($config['total_articles'] != $count)
	{
		update_config('total_articles', $count);
	}
}

if ($keyword == '')
{
	$pagination = art_pagination($page, $total_items, $limit, 1, $filename, $uri[1], _SEOMOD);
}
else	//	ignore SEOMOD when searching
{
	$pagination = art_pagination($page, $total_items, $limit, 1, $filename, $uri[1], 0);
}


//	meta title
$meta_title = $meta_keywords = $meta_description = '';
if ($cid > 0)
{
	if ($categories[$cid]['meta_title'] != '')
	{
		$meta_title = $categories[$cid]['meta_title'];
	}
	else 
	{
		$meta_title .= $categories[$cid]['name'];
	}
	if(!empty($page) && $page > 1) {
		$meta_title .= ' - '.sprintf($lang['page_number'], $page);
	}
	$meta_title .= ' - '._SITENAME;
	$article_h2 = $categories[$cid]['name'];
	
	$meta_keywords = $categories[$cid]['meta_keywords'];
	$meta_description = strip_tags($categories[$cid]['meta_description']);
	if(!empty($page) && $page > 1) {
		$meta_description .= ' - '.sprintf($lang['page_number'], $page);
	}
}
else if ($tag != '' || $keyword != '')
{
	$meta_title = _SITENAME;
	$meta_title .= ' - ';
	$meta_title .= ($tag != '') ? urldecode($_GET['tag']) : $lang['search_results'];

	if(!empty($page) && $page > 1) {
		$meta_title .= ' - '.sprintf($lang['page_number'], $page);
	}
	$meta_title .= ' - '._SITENAME;
	
	$article_h2 = ($tag != '') ? $lang['search_results'] . ': ' . urldecode($_GET['tag']) : $lang['search_results'];
	$article_h2 = specialchars($article_h2, 1); 
}
else
{
	$meta_title .= _SITENAME;
	if ($show_popular)
	{
		$meta_title = $article_h2 = $lang['articles_mostread'].' '.$lang['articles'];
		if(!empty($page) && $page > 1) {
			$meta_title .= ' - '.sprintf($lang['page_number'], $page);
		}
		$meta_title .= ' - '._SITENAME;
	}
	else
	{
		$meta_title = $article_h2 = $lang['articles_latest'];
		if(!empty($page) && $page > 1) {
			$meta_title .= ' - '.sprintf($lang['page_number'], $page);
		}
		$meta_title .= ' - '._SITENAME;
	}
	$meta_description = $meta_title;
}


//	browsing style
if ($cid > 0)
{
	$browsing_style = 'category';
} 
else if ($tag != '' || $keyword != '')
{
	$browsing_style = 'search';
}
else 
{
	$browsing_style = 'default';
}

//	assign values to template
$smarty->assign('browsing_style', $browsing_style); 
$smarty->assign('total_results', $total_results); 
$smarty->assign('articles', $articles);
$smarty->assign('categories', $categories);
$smarty->assign('article_categories', $article_categories); 
$smarty->assign('pagination', $pagination);
$smarty->assign('article_h2', $article_h2);

$smarty->assign('cat_id', $cid); 
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_keywords', $meta_keywords);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $config['template_f']); 
$smarty->display('article-category.tpl');
?>