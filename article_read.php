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

$id = (int) $_GET['a'];
$article = array();

if ($id > 0)
{
	$preview = ($_GET['mode'] == 'preview') ? true : false;
	$uniq_id = 'article-';
	$result = array();
	$result = art_load_articles(0, 1, '', 0, array($id), '', $preview);
	$article = (array) $result[$id];
	$modframework->trigger_hook('article_read_top');
	if ( ! array_key_exists('type', $result))
	{
		//	meta tags
		//$meta_title = htmlentities($article['title'], ENT_COMPAT, 'UTF-8');
		$meta_title = $article['title'];
		$meta_title .= ' - '. _SITENAME;
		
		$meta_description = generate_excerpt(str_replace('"', '&quot;', $article['excerpt']), 70) .'...';
		
		$meta_keywords = '';
		foreach ($article['tags'] as $k => $tag_arr)
		{	
			$meta_keywords .= $tag_arr['tag'] . ', ';
		}
		$meta_keywords = substr($meta_keywords, 0, -2);
		
		//	get categories
		$categories = art_get_categories();

		$uniq_id .= $article['id'];
		//	remove delimiter
		$article['content'] = @preg_replace('/<hr>/', '', $article['content'], 1);
		
		//	build direct link to article
		$article['link'] = art_make_link('article', $article);
		
		//	convert timestamp to date
		$article['date'] = date('M j, Y', $article['date']);
		
		//	handle article categories - make links
		$pieces = explode(',', $article['category']);
		foreach ($pieces as $k => $cat_id)
		{
			$name = $categories[$cat_id]['name'];
			
			$article['pretty_cats'][$name] = $categories[$cat_id]['link'];
		} 
		
		$most_liked_comment = false;
		//	handle comments
		if ($article['allow_comments'] == 1)
		{
			$comment_list = get_comment_list($uniq_id, 1);
			$comment_count = count_entries('pm_comments', 'uniq_id', $uniq_id);
			$mod_can = mod_can();
	
			if ($userdata['power'] == U_ADMIN || ($userdata['power'] == U_MODERATOR && $mod_can['manage_comments']))
			{
				$smarty->assign('can_manage_comments', true);
			}
			else
			{
				$smarty->assign('can_manage_comments', false);
			}
			
			$comment_pagination_obj = '';
			if ($comment_count > $config['comments_page'])
			{
				$comment_pagination_obj = generate_comment_pagination_object($uniq_id, 1, $comment_count, $config['comments_page']);
			}
			if ($comment_count > 0)
			{
				$most_liked_comment = get_most_liked_comment($uniq_id);
				$most_liked_comment = (array) $most_liked_comment[0];
				
				if ($most_liked_comment['up_vote_count'] <= 2)
				{
					$most_liked_comment = false;
				}
				
				// remove duplicate
				if ($config['comment_default_sort'] == 'score' && is_array($most_liked_comment))
				{
					unset($comment_list[0]);
				}
			}
		}
		else
		{
			$comment_list = array();
			$comment_count = 0;
			$smarty->assign('can_manage_comments', false);
		}
		
		art_update_view_count($article['id']);
		
		if (_MOD_SOCIAL && is_user_logged_in())
		{
			$article['am_following'] = is_follow_relationship($article['author'], $userdata['id']);
			
			// avoid duplicates
			$activity_id = get_activity_id(array('user_id' => $userdata['id'],
										 		 'activity_type' => ACT_TYPE_READ, 
										 		 'object_id' => $article['id'],
										 		 'object_type' => ACT_OBJ_ARTICLE
												)
										  );
			if ( ! $activity_id)
			{
				log_activity(array( 'user_id' => $userdata['id'],
									'activity_type' => ACT_TYPE_READ,
									'object_id' => $article['id'],
									'object_type' => ACT_OBJ_ARTICLE,
									'object_data' => $article
									));
			}
		}
		$tmp_parts = explode(',', $article['category']);
		$tmp_cat_id = array_pop($tmp_parts);
		$related_articles = get_related_article_list($tmp_cat_id, $article['title'], 4);
		
		if ($related_articles['type'] != '')
		{
			$related_articles = $related_articles['msg'];
		}
		else
		{
			foreach ($related_articles as $k => $arr)
			{
				if ($arr['id'] == $article['id'])
				{
					unset($related_articles[$k]);
				}
			}
		}
	}
	else
	{
		redirect_404();
	}
}
else 
{
	header("Location: ". _URL ."/article.php");
	exit();
}


if(isset($_COOKIE[COOKIE_AUTHOR]) && $_COOKIE[COOKIE_AUTHOR] != '')
	$smarty->assign('guestname', str_replace( array('"', '>', '<'), "", $_COOKIE[COOKIE_AUTHOR]) );
else
	$smarty->assign('guestname', '');

$must_sign_in = sprintf($lang['must_sign_in'], _URL."/login."._FEXT, _URL."/register."._FEXT);

$twitter_status  = '';
$twitter_status  = $article['title']; 
$twitter_status .= ' '. $article['link'];
$twitter_status  = urlencode($twitter_status);

//	Some HTML 
$article_categories = art_html_list_categories($article['category'], $categories, array('max_levels' => 1));
if($config['show_tags'] == 1)
{
	$tag_cloud = art_tag_cloud(0, $config['tag_cloud_limit'], $config['shuffle_tags']);
	$smarty->assign('tags', $tag_cloud);
	$smarty->assign('show_tags', 1);
}

// Facebook image src
if (is_array($article) && $article['meta']['_post_thumb_show'] != '')
{
	$facebook_image_src =  _ARTICLE_ATTACH_DIR . $article['meta']['_post_thumb_show'];
	$smarty->assign('facebook_image_src', $facebook_image_src);
}

$smarty->assign('uniq_id', $uniq_id); 
$smarty->assign('article', $article);
$smarty->assign('related_articles', $related_articles); 
$smarty->assign('comment_list', $comment_list);
$smarty->assign('most_liked_comment', $most_liked_comment);
$smarty->assign('comment_count', $comment_count);
$smarty->assign('comment_pagination_obj', $comment_pagination_obj);
$smarty->assign('show_addthis_widget', $config['show_addthis_widget']);

$smarty->assign('must_sign_in', $must_sign_in);
$smarty->assign('categories', $categories);	 
$smarty->assign('article_categories', $article_categories); 

$smarty->assign('guests_can_comment', $config['guests_can_comment']);
$smarty->assign('user_id', $userdata['id']);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_keywords', $meta_keywords);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $template_f);
$modframework->trigger_hook('article_read_bottom');
$smarty->display('article-read.tpl');
?>