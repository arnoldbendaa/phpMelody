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


function get_article($id)
{
	global $categories, $config;
	
	$int_values = array('id', 'status', 'author', 'allow_comments', 'comment_count', 'date', 'views');
		
	$article = array();

	$id = (int) $id;
	if ($id == 0)
	{
		return array();
	}

	$sql = "SELECT * 
			FROM art_articles 
			WHERE id = '". $id ."'";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array();
	}
	
	$article = mysql_fetch_assoc($result);
	mysql_free_result($result);
	if (is_array($article))
	foreach ($article as $k => $v)
	{
		if (in_array($k, $int_values))
		{
			$article[$k] = (int) $v;
		}
	}
	
	$article['html5_datetime'] = date('Y-m-d\TH:i:sO', $article['date']); // ISO 8601
	$article['full_datetime'] = date('l, F j, Y g:i A', $article['date']); 
	
	//	handle categories
	if ( ! is_array($categories))
	{
		$categories = art_get_categories();
	}

	$pieces = explode(',', $article['category']);
	$str	= '';
	$cat_id_arr = array();

	foreach ($pieces as $k => $cat_id)
	{
		$cat_id = trim($cat_id);
		$cat_id_arr[$cat_id] = $categories[$cat_id]['name'];
		$str .= $categories[$cat_id]['name'] .', ';
	} 
	$article['category_as_arr'] = $cat_id_arr;
	$article['category_as_str'] = substr($str, 0, -2);

	$tags = get_article_tags($id);
	if (count($tags) > 0)
	{
		foreach($tags as $k => $tag_arr)
		{
			$article['tags'] .= $tag_arr['tag'] .', ';
		}
		$article['tags'] = substr($article['tags'], 0, -2);
	}
	
	$article['meta'] = get_meta($article['id'], IS_ARTICLE);

	$article['content'] = filter_text_https_friendly($article['content']);
	$article['excerpt'] = generate_excerpt($article['content'], 255);
	
	if ($config['comment_system'] == 'off')
	{
		$article['allow_comments'] = 0;
	}
	
	return $article;
}
	
function list_articles($search_term, $search_type = 'title', $from = 0, $to = 20, $filter = '', $filter_value = '') 
{
	global $config;
	
	$int_values = array('id', 'status', 'author', 'allow_comments', 'comment_count', 'date', 'views');
	$articles	= array();
	
	if ( ! empty($search_term)) 
	{
		switch ($search_type)
		{
			default:
			case 'title': 
				
				$sql = "SELECT * 
						FROM art_articles 
						WHERE title LIKE '%". secure_sql($search_term) ."%' 
						ORDER BY id DESC";
			break;
			
			case 'content':

				$sql = "SELECT * 
						FROM art_articles  
						WHERE content LIKE '%". secure_sql($search_term) ."%'
						ORDER BY id DESC";
			break;
		}
	}	//	end if ! empty(search_term)
	else 
	{
		$sql = "SELECT * 
				FROM art_articles ";
				
		if ($filter != '')
		{
			switch ($filter)
			{
				case 'category':
					
					if ($filter_value == 0)
					{
						$sql .= " WHERE category LIKE '0' ORDER BY id DESC ";
					}
					else
					{
						$sql .= " WHERE (category LIKE '". $filter_value ."' 
								     OR category LIKE '". $filter_value .",%' 
								     OR category LIKE '%,". $filter_value ."' 
								     OR category LIKE '%,". $filter_value .",%')
								  ORDER BY id DESC ";
					}

				break;
				
				case 'mostviewed':
				
					$sql .= " ORDER BY views DESC ";
					
				break;
				
				case 'private':
					
					$sql .= " WHERE status = '0' 
							  ORDER BY id DESC ";
					
				break;
				
				case 'sticky':
				case 'featured':
					
					$sql .= " WHERE featured = '1' 
							  ORDER BY id DESC ";
					
				break;
				
				case 'restricted':
					
					$sql .= " WHERE restricted = '1' 
							  ORDER BY id DESC ";
					
				break;
				
				case 'public':
				
					$sql .= " WHERE status = '1' 
							  ORDER BY id DESC ";
						  
				break;
			}
		}
		else
		{
			$sql .= " ORDER BY id DESC ";
		}
		
		$sql .= " LIMIT ". $from .", ". $to;
	}

	$result = mysql_query($sql);
	
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => 'MySQL Error: '. mysql_error());
	}
	
	if (mysql_num_rows($result) > 0)
	{
		$i = 0;
		$categories = art_get_categories();
		
		while ($row = mysql_fetch_assoc($result))
		{
			foreach ($row as $k => $v)
			{
				if (in_array($k, $int_values))
				{
					$row[$k] = (int) $v;
				}
			}
			
			$pieces = array();
			$str	= '';
			$cat_id_arr = array();
			
			$id = $row['id'];
			$articles[$i] = $row;
			$articles[$i]['html5_datetime'] = date('Y-m-d\TH:i:sO', $row['date']); // ISO 8601
			$articles[$i]['full_datetime'] = date('l, F j, Y g:i A', $row['date']); 
			
			$tags = get_article_tags($id);
			if (count($tags) > 0)
			{
				foreach($tags as $k => $tag_arr)
				{
					$articles[$i]['tags'] .= $tag_arr['tag'] .', ';
				}
				$articles[$i]['tags'] = substr($articles[$i]['tags'], 0, -2);
			}
			
			$articles[$i]['meta'] = get_meta($id, IS_ARTICLE);
			
			$pieces = explode(',', $row['category']);
			foreach ($pieces as $k => $cat_id)
			{
				$cat_id = trim($cat_id);
				$cat_id_arr[$cat_id] = $categories[$cat_id]['name'];
				$str .= $categories[$cat_id]['name'] .', ';
			} 
			$articles[$i]['category_as_arr'] = $cat_id_arr;
			$articles[$i]['category_as_str'] = substr($str, 0, -2);
			
			$articles[$i]['content'] = filter_text_https_friendly($articles[$i]['content']);
			$articles[$i]['excerpt'] = generate_excerpt($articles[$i]['content'], 255); 
			
			if ($config['comment_system'] == 'off')
			{
				$articles[$i]['allow_comments'] = 0;
			}
			
			$i++;
		}
		
		mysql_free_result($result);
	}

	return $articles;
}

function insert_new_article($postarr)
{
	global $config;
	
	foreach ($postarr as $k => $v)
	{
		if (is_string($v))
		{
			$postarr[$k] = stripslashes($v);
		}
	}
	
	$title	 = trim($postarr['title']);
	$content = (ini_get('magic_quotes_gpc')) ? stripslashes($postarr['content']) : $postarr['content'];
	$status	 = (int) $postarr['status'];
	$featured	 = (int) $postarr['featured'];
	$restricted	 = (int) $postarr['restricted'];
	$author	 = (int) $postarr['author'];
	$tags 	 = trim($postarr['tags']);
	$categories = $postarr['categories'];	//	array();
	$allow_comments = (int) $postarr['allow_comments'];
	$pic_ext = array('jpg', 'jpeg', 'gif', 'png');
	$article_slug = $postarr['article_slug'];
	
	if (strlen($title) == 0)
	{
		return array('type' => 'error', 'msg' => 'What is the title?');
	}
	if (count($categories) == 0)
	{
		return array('type' => 'error', 'msg' => 'Please select a category first');
	}
	
	if ($allow_comments != 0 && $allow_comments != 1)
	{
		$allow_comments = 1;
	}
	
	if ($author == 0)
	{
		$author = 1;	//	default user
	}
	
	if ($article_slug == '')
	{
		$article_slug = $title;
	}
	$article_slug = sanitize_title($article_slug);
	
	$category = '';
	$category = implode(",", $categories);
	
	$title 	 = prep_title($title);
	$content = prep_content($content);
	
	$date = validate_item_date($postarr);
	if ($date === false)
	{
		return array('type' => 'error', 'msg' => 'Invalid date');
	}
	$date = pm_mktime($date);
	
	$sql = "INSERT INTO art_articles 
					(title, content, category, status, date, author, allow_comments, comment_count, views, featured, restricted, article_slug) 
			VALUES  ('". $title ."', 
					 '". $content ."', 
					 '". $category ."', 
					 '". $status ."', 
					 '". $date ."', 
					 '". $author ."', 
					 '". $allow_comments ."', 
					 '0',
					 '0',  
					 '". $featured ."', 
					 '". $restricted ."',
					 '". secure_sql($article_slug) ."'
					 )";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => 'MySQL Error: '. mysql_error());
	}
	$last_id = mysql_insert_id();
	
	$postarr['content'] = stripslashes($postarr['content']);
	$postarr['content'] = str_replace('&quot;', '"', $postarr['content']);
		
	@preg_match_all('/src="(.*?)"/i', $postarr['content'], $matches);
	
	if (count($matches[1]) > 0)
	{	
		$thumb_counter = 0;
		
		foreach ($matches[1] as $k => $url)
		{
			if (strpos($url, _URL) === false && ! is_url($url))
			{
				$url = _URL . $url;
			}
			
			if (strpos($url, _URL) !== false)
			{
				$tmp_parts = explode('.', $url);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);
				
				if (in_array($ext, $pic_ext))
				{
					$buff = explode('/', $url);
					$filename = $buff[ count($buff) - 1];
					$thumbname = str_replace('.', '_th.', $buff[ count($buff) - 1 ]);
					
					if (file_exists(_ARTICLE_ATTACH_DIR_PATH . $thumbname))
					{
						if ($thumb_counter == 0)
						{
							add_meta($last_id, IS_ARTICLE, '_post_thumb_show', $thumbname);
						}
						
						add_meta($last_id, IS_ARTICLE, '_post_thumb', $thumbname);	
						$thumb_counter++;
					}
					
					if (file_exists(_ARTICLE_ATTACH_DIR_PATH . $filename))
					{
						add_meta($last_id, IS_ARTICLE, '_post_image', $filename);
					}
				}
			}
		}
	}
	
	$sql = "UPDATE art_categories SET total_articles=total_articles+1 "; 
	
	if ($date <= time())
	{
		$sql .= ", published_articles=published_articles+1 ";
		
		update_config('published_articles', $config['published_articles']+1, true);
	}
	$sql .= " WHERE id IN (". $category .")";
	@mysql_query($sql);
	
	//	insert tags
	$tags = explode(',', $tags);
	if (count($tags) > 0)
	{
		art_insert_tags($last_id, $tags);
	}
	
	update_config('total_articles', $config['total_articles']+1, true);
	
	if (is_array($postarr['meta']))
	{
		$meta_ids = array();
		foreach ($postarr['meta'] as $meta_id => $arr)
		{
			$meta_ids[] = $meta_id;
		}
		
		if (count($meta_ids) > 0)
		{
			$sql = "UPDATE pm_meta 
					SET item_id = $last_id 
					WHERE id IN (". implode(',', $meta_ids) .")";
			mysql_query($sql);
		}
	}
	
	return array('type' => 'ok', 'msg' => 'This article has been posted', 'article_id' => $last_id);	
}

function update_article($id, $postarr)
{
	global $config;
	
	$id		 = (int) $id;
	$title	 = trim($postarr['title']);
	$content = (ini_get('magic_quotes_gpc')) ? stripslashes($postarr['content']) : $postarr['content'];
	$status	 = (int) $postarr['status'];
	$featured	 = (int) $postarr['featured'];
	$restricted	 = (int) $postarr['restricted'];
	$author	 = (int) $postarr['author'];
	$tags 	 = trim($postarr['tags']);
	$categories = $postarr['categories'];	//	array();
	$allow_comments = (int) $postarr['allow_comments'];
	$post_thumb_show = $postarr['post_thumb_show'];
	$pic_ext = array('jpg', 'jpeg', 'gif', 'png');
	$article_slug = $postarr['article_slug'];
	
	if ($id == 0)
	{
		return array('type' => 'error', 'msg' => 'Invalid article ID');
	}
	
	if (strlen($title) == 0)
	{
		return array('type' => 'error', 'msg' => 'What is the title?');
	}
	
	if (count($categories) == 0)
	{
		return array('type' => 'error', 'msg' => 'Please select a category first');	
	}
	
	$date = validate_item_date($postarr);
	$date_old = (int) $postarr['date_old'];
	if ($date === false)
	{
		return array('type' => 'error', 'msg' => 'Invalid date');
	}
	else
	{
		$date = pm_mktime($date);
		$time_now = time();
		
		if ($date != $date_old)
		{
			if ($date_old <= $time_now && $date > $time_now)
			{
				update_config('published_articles', $config['published_articles'] - 1);
				
				$sql_tmp = "UPDATE art_categories 
							SET published_articles = published_articles - 1
							WHERE id IN (". $postarr['categories_old'] .")";
				@mysql_query($sql_tmp);
			}
			
			if ($date_old > $time_now && $date <= $time_now)
			{
				//was future, not it's published
				update_config('published_articles', $config['published_articles'] + 1);
				
				$sql_tmp = "UPDATE art_categories 
							SET published_articles = published_articles + 1
							WHERE id IN (". $postarr['categories_old'] .")";
				@mysql_query($sql_tmp);
			}
		}
		
	}
	
	
	if ($article_slug == '')
	{
		$article_slug = $title;
	}
	$article_slug = sanitize_title($article_slug);
	
	$category = '';
	$category = implode(",", $categories);
	
	$title 	 = prep_title($title);
	$content = prep_content($content);
	
	$sql = "UPDATE art_articles 
			SET title = '". $title ."', 
				content = '". $content ."', 
				category = '". $category ."', 
				status = '". $status ."', 
				date = '". $date ."',
				author = '". $author ."', 
				allow_comments = '". $allow_comments ."', 
				featured = '". $featured ."', 
				restricted = '". $restricted ."',
				article_slug = '". secure_sql($article_slug) ."' 
			WHERE id = '". $id ."'";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => 'MySQL Error: '. mysql_error());
	}
	
	$article_meta = get_meta($id, IS_ARTICLE);
	$post_thumb_arr = (is_array($article_meta['*']['_post_thumb'])) ? $article_meta['*']['_post_thumb'] : array();
	$post_image_arr = (is_array($article_meta['*']['_post_image'])) ? $article_meta['*']['_post_image'] : array();
	
	if (strpos($post_thumb_show, '/'))
	{
		$buff = explode('/', $post_thumb_show);
		$post_thumb_show = $buff[ count($buff) - 1];
	}
	
	if ($post_thumb_show != '' && $post_thumb_show != $article_meta['_post_thumb_show'])
	{
		update_meta($id, IS_ARTICLE, '_post_thumb_show', $post_thumb_show);
	}
	
	$postarr['content'] = stripslashes($postarr['content']);
	$postarr['content'] = str_replace('&quot;', '"', $postarr['content']);
	
	@preg_match_all('/src="(.*?)"/i', $postarr['content'], $matches);

	if (count($matches[1]) > 0)
	{
		foreach ($matches[1] as $k => $url)
		{
			if (strpos($url, _URL) === false && ! is_url($url))
			{
				$url = _URL . $url;
			}
			
			if (strpos($url, _URL) !== false)
			{
				$tmp_parts = explode('.', $url);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);
				
				if (in_array($ext, $pic_ext))
				{
					$buff = explode('/', $url);
					$filename = $buff[ count($buff) - 1];
					$thumbname = str_replace('.', '_th.', $buff[ count($buff) - 1 ]);
					
					if ( ! in_array($thumbname, $post_thumb_arr))
					{
						if (file_exists(_ARTICLE_ATTACH_DIR_PATH . $thumbname))
						{
							add_meta($id, IS_ARTICLE, '_post_thumb', $thumbname);
						}
					}
					
					if ( ! in_array($filename, $post_image_arr))
					{
						if (file_exists(_ARTICLE_ATTACH_DIR_PATH . $filename))
						{
							add_meta($id, IS_ARTICLE, '_post_image', $filename);
						}
					}
				}
			}
		}
	}
	
	//	insert tags
	$tags = explode(',', $tags);
	if (count($tags) > 0)
	{
		art_update_tags($id, $tags);
	}
	
	if (strcmp($category, $_POST['categories_old']) != 0)
	{
		$c_inc = $c_dec = array();
		$buffer_new = $buffer_old = array();
		$buffer_new = explode(',', $category);
		$buffer_old = explode(',', $_POST['categories_old']);
		
		foreach ($buffer_new as $k => $cid)
		{
			if ( ! in_array($cid, $buffer_old))
			{
				$c_inc[] = $cid;
			}
		}
		foreach ($buffer_old as $k => $cid)
		{
			if ( ! in_array($cid, $buffer_new))
			{
				$c_dec[] = $cid;
			}
		}
		
		if (count($c_inc) > 0)
		{
			$str = implode(',', $c_inc);
			$sql = "UPDATE art_categories SET total_articles=total_articles+1 ";
			$sql .= ($date <= time()) ? ", published_articles=published_articles+1 " : '';
			$sql .= " WHERE id IN (". $str .")";
			mysql_query($sql);
			unset($str);
		}
		if (count($c_dec) > 0)
		{
			$str = implode(',', $c_dec);
			$sql = "UPDATE art_categories SET total_articles=total_articles-1 ";
			$sql .= ($date <= time()) ? ", published_articles=published_articles-1" : '';
			$sql .= " WHERE id IN (". $str .")";
			mysql_query($sql);
			unset($str);
		}
	}
	
	return array('type' => 'ok', 'msg' => 'Article updated.');
}

function delete_article($id)
{
	global $config;
	
	$id = (int) $id;
	$category = '';

	if ($id == 0)
	{
		return array('type' => 'error', 'msg' => 'Sorry, this article does NOT exist. Invalid article ID'); 
	}
		
	$sql = "SELECT date, category FROM art_articles 
			WHERE id = '". $id ."'";
	$result = mysql_query($sql);
	$article_data = mysql_fetch_assoc($result);
	
	$category = $article_data['category'];
	
	mysql_free_result($result);
	unset($result, $sql);
	
	//	Delete article
	$sql = "DELETE FROM art_articles 
			WHERE id = '". $id ."'";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => 'Could not delete this article. A MySQL error occurred: '. mysql_error());
	}
	
	delete_meta($id, IS_ARTICLE);
	
	update_config('total_articles', $config['total_articles']-1, true);
	
	//	Update article count
	$sql = "UPDATE art_categories 
			SET total_articles=total_articles-1 ";
	if ($article_data['date'] <= time())
	{
		$sql .= ", published_articles=published_articles-1 ";
		update_config('published_articles', $config['published_articles']-1, true);
	}
	$sql .= " WHERE id IN (". $category .")";
	mysql_query($sql);
	
	//	Delete article comments
	$sql = "DELETE FROM pm_comments 
			WHERE uniq_id = 'article-". $id ."'";
	mysql_query($sql);
	
	//	Delete article tags
	$sql = "DELETE FROM art_tags 
			WHERE article_id = '". $id ."'";
	mysql_query($sql);
	
	return array('type' => 'ok', 'msg' => 'The article was deleted');
}

function mass_delete_articles($ids)
{
	global $config;
	
	$temp = array();
	foreach ($ids as $k => $id)
	{
		$id = (int) $id;
		if ($id > 0)
		{
			$temp[] = $id;
		}
	}
	$ids = $temp;

	if (count($ids) > 0)
	{
		foreach ($ids as $k => $id)
		{
			delete_meta($id, IS_ARTICLE);
		}
		
		$ids_str = implode(', ', $ids);
		$in_arr = '';	//	for non 'art_' tables
		foreach ($ids as $k => $id)
		{
			$in_arr .= "'article-". $id."', ";
		}
		$in_arr = substr($in_arr, 0, -2);

		$article_list_data = array();

		$sql = "SELECT id, date, category FROM art_articles 
				WHERE id IN (" . $ids_str . ")";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_assoc($result))
		{
			$article_list_data[$row['id']] = $row;
		}
		mysql_free_result($result);

		//	Delete articles
		$sql = "DELETE FROM art_articles 
				WHERE id IN (" . $ids_str . ")";
		$result = mysql_query($sql);

		if ( ! $result)
		{
			return array('type' => 'error', 'msg' => 'Could not delete these articles. A MySQL error occurred: '. mysql_error());
		}

		//	Update article count for each category
		$total_published_ids = 0;
		$article_count = array();
		$article_published_count = array();
		$time_now = time();
		
		foreach ($article_list_data as $art_id => $data)
		{
			$buffer = explode(',', $data['category']);
			foreach ($buffer as $k => $category_id)
			{
				$article_count[$category_id]++;
				if ($data['date'] <= $time_now)
				{
					$article_published_count[$category_id]++;
				}
			}
			
			if ($data['date'] <= $time_now)
			{
				$total_published_ids++;
			}
		}
		
		if (count($article_count) > 0)
		foreach ($article_count as $cid => $count)
		{
			if ('' != $cid)
			{

				$sql = "UPDATE art_categories SET total_articles=total_articles-". $count;
				if ($article_published_count[$cid] > 0)
				{
					$sql .= ", published_articles=published_articles-". $article_published_count[$cid];
				}
				$sql .= " WHERE id = '". $cid ."'";
				mysql_query($sql);
			}
		}

		update_config('total_articles', count_entries('art_articles', '', ''), true);
		
		if ($total_published_ids)
		{
			update_config('published_articles', $config['published_articles'] - $total_published_ids);
		}

		//	Delete comments
		$sql = "DELETE FROM pm_comments 
				WHERE uniq_id IN (" . $in_arr . ")";
		mysql_query($sql);

		
		//	Delete article tags
		$sql = "DELETE FROM art_tags 
				WHERE article_id IN (" . $ids_str . ")";
		mysql_query($sql);
		
		return array('type' => 'ok', 'msg' => 'The selected articles have been deleted');
	}
	return false;
}

function get_article_tags($id)
{
	$tags = array();
	
	$sql = "SELECT *
			FROM art_tags 
			WHERE article_id = '". $id ."' 
			ORDER BY id ASC";
			
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array();
	}
	
	$i = 0;
	while ($row = mysql_fetch_assoc($result))
	{
		$tags[$i] = $row;
		$tags[$i]['link'] = art_make_link('browse-tag', array('tag' => $row['safe_tag'], 'page' => 1));
		$i++;
	}
	
	return $tags;
}

function art_insert_tags($id, $arr_tags)
{
	$id = (int) $id;
	
	//	remove duplicate and empty tags
	$temp = array();
	for($i = 0; $total = count($arr_tags), $i < $total; $i++)
	{
		if($arr_tags[$i] != '')
			if($i <= ($total-1))
			{
				$found = 0;
				for($j = $i + 1; $j < $total; $j++)
				{
					if(strcmp($arr_tags[$i], $arr_tags[$j]) == 0)
						$found++;
				}
				if($found == 0)
					$temp[] = $arr_tags[$i];
			}
	}
	$arr_tags = $temp;
	

	$count = count($arr_tags);
	for ($i = 0; $i < $count; $i++)
	{
		$tag = stripslashes(trim($arr_tags[$i]));
		if (strlen($tag) > 0)
		{
			$safe_tag = safe_tag($tag);
			$tag = str_replace('"', '&quot;', $tag);
			
			$sql = "INSERT INTO art_tags 
							(article_id, tag, safe_tag) 
					VALUES 	('". $id ."', '". secure_sql($tag) ."', '". $safe_tag ."')";

			$result = mysql_query($sql);
		}
	}
	
	return true;
}

function art_update_tags($id, $arr_tags)
{
	$id = (int) $id;
	
	if ($id == 0)
	{
		return false;
	}
	
	$current_tags = get_article_tags($id);
	$tags = $arr_tags;

	//	remove duplicate tags and 'empty' tags
	$temp = array();
	for($i = 0; $i < count($tags); $i++)
	{
		if($tags[$i] != '')
			if($i <= (count($tags)-1))
			{
				$found = 0;
				for($j = $i + 1; $j < count($tags); $j++)
				{
					if(strcmp($tags[$i], $tags[$j]) == 0)
						$found++;
				}
				if($found == 0)
					$temp[] = $tags[$i];
			}
	}
	$tags = $temp;
	
	$tags_insert = array();
	foreach($tags as $k => $tag)
	{
		//	handle mistakes
		$tag = stripslashes(trim($tag));
		$tags[$k] = $tag;
		if($tag != '' && (strlen($tag) > 0))
		{
			//	new tags vs old tags
			$found = 0;
			$safe_tag = safe_tag($tag);
			
			foreach($current_tags as $key => $arr)
			{
				if(in_array($safe_tag, $arr))
					$found++;
			}
			if($found == 0)
				$tags_insert[] = $tag;
		}
	}
	
	//	were there any tags changed or removed?
	$remove_tags = array();
	foreach($current_tags as $k => $v)
	{
		if(in_array($v['tag'], $tags) === false)
		{
			$remove_tags[] = $v['id'];
		} 
	}
	//	insert new tags in database
	if(count($tags_insert) > 0)
	{
		art_insert_tags($id, $tags_insert);
	}

	//	remove left-out tags
	if(count($remove_tags) > 0)
	{
		$this_arr = '';
		$this_arr = implode(',', $remove_tags);
		
		$sql = "DELETE FROM art_tags 
				WHERE article_id = '". $id ."' 
				  AND id IN(".$this_arr.")";

		$result = @mysql_query($sql);
	}
	
	return true;
}

function art_get_categories($args = array())
{
	$args['db_table'] = 'art_categories';
	$categories = load_categories($args); // @since 2.2
	if (count($categories) > 0)
	{
		foreach ($categories as $id => $category)
		{
			$categories[$id]['link'] = art_make_link('category', array('tag' => $category['tag'], 'page' => '1'));
		}
	}

	return $categories;
}

function art_insert_category($postarr)
{
	return insert_category($postarr, 'article'); // @since 2.2
}

function art_update_category($id, $postarr)
{
	return update_category($id, $postarr, 'article'); // @since 2.2
}

function art_delete_category($id)
{
	return delete_category($id, 'article'); // @since 2.2
}

function pre_post_filter($value)
{
	$find	 = array("<", ">", '"');
	$replace = array("&lt;", "&gt;", "&quot;");
	return str_replace($find, $replace, $value);
}

function after_post_filter($value)
{
	$find	 = array("&lt;", "&gt;", "&quot;");
	$replace = array("<", ">", '"');
	$value	 = stripslashes($value);
	return str_replace($find, $replace, $value);
}

function art_load_articles($from = 0, $limit = 6, $category_id = '', $user_id = 0, $array_of_ids = '', $order_by = 'date', $preview = false)
{	
	global $lang, $categories, $userdata, $config;
	
	$articles = array();
	
	$from  = (int) $from;
	$limit = (int) $limit;
	$user_id = (int) $user_id;
	$category_id = (int) $category_id;
	
	if ($limit == 0) 
		$limit = 6; //	default
	
	$status = 1;
	
	$sql = 'SELECT * 
			FROM art_articles ';

	if ($preview && (is_admin() || is_editor() || (is_moderator() && mod_can('manage_articles'))))
	{
		$sql .= ' WHERE status IN (0, 1) ';
	}
	else 
	{
		$sql .= " WHERE status = 1 AND date <= '". time() ."' ";
	}
	
	if (is_array($array_of_ids) && count($array_of_ids) > 0)
	{
		$ids_str = implode(', ', $array_of_ids);
		$sql .= " AND id IN (". $ids_str .") ";
	}
	else if ($category_id > 0)
	{
		$sql .= " AND (category LIKE '". $category_id ."' 
					 OR category LIKE '". $category_id .",%' 
					 OR category LIKE '%,". $category_id ."' 
					 OR category LIKE '%,". $category_id .",%') ";
	}
	else if ($user_id > 0)
	{
		$sql .= " AND author = '". $user_id ."' ";
	}
	
	$sql .= ($order_by != '') ? " ORDER BY ". $order_by ." DESC " : '';
	$sql .= " LIMIT ". $from .", ". $limit;

	$result = @mysql_query($sql);
	
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => $lang['browse_msg1'] );
	}
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$articles[$row['id']] = $row;
		}
	
		mysql_free_result($result);
		
		foreach ($articles as $id => $article)
		{
			$articles[$id]['rating'] = 0;
			$articles[$id]['tags'] = '';
			$articles[$id]['content'] = filter_text_https_friendly($articles[$id]['content']);
			
			$unique_id = 'article-'. $id;
			
			// get likes and dislikes
			if (function_exists('bin_rating_get_item_meta'))
			{
				$rating_meta = bin_rating_get_item_meta($unique_id);
				$balance = bin_rating_calc_balance($rating_meta['up_vote_count'], $rating_meta['down_vote_count']);
				
				$articles[$id]['up_vote_count'] = $rating_meta['up_vote_count'];
				$articles[$id]['down_vote_count'] = $rating_meta['down_vote_count'];
				$articles[$id]['up_vote_count_formatted'] = pm_number_format($rating_meta['up_vote_count']);
				$articles[$id]['down_vote_count_formatted'] = pm_number_format($rating_meta['down_vote_count']);
				$articles[$id] = array_merge($articles[$id], $balance);
			}
			
			//	number of comments
			$articles[$id]['comment_count'] = (int) count_entries('pm_comments', 'uniq_id', $unique_id);
			
			//	get the author's username
			$author = fetch_user_advanced($article['author']);
			$articles[$id]['author_data'] = $author;// @since v2.6
			$articles[$id]['username'] = $author['username'];
			$articles[$id]['name'] = $author['name'];
			$articles[$id]['avatar_url'] = $author['avatar_url'];
			$articles[$id]['author_power'] = $author['power'];
			$articles[$id]['author_name'] = $author['name'];
			$articles[$id]['author_username'] = $author['username'];
			$articles[$id]['author_profile_href'] = ($author['username'] != 'bot') ? $author['profile_url'] : '#';
			$articles[$id]['author_about'] = $author['about'];
						
			//	get tags
			$articles[$id]['tags'] = get_article_tags($id);
			
			//	generate link
			$articles[$id]['link'] = art_make_link('article', $article);
			$articles[$id]['meta'] = get_meta($id, IS_ARTICLE);
			
			// separate prettyPhoto galleries
			$articles[$id]['content'] = str_replace('[phpmelody]', '['. $id .']', $articles[$id]['content']);
			
			// excerpt
			$articles[$id]['excerpt'] = generate_excerpt($articles[$id]['content'], 255);
			
			$articles[$id]['content'] = lwmarkup_parse($articles[$id]['content']);
			
			$articles[$id]['views_formatted'] = (function_exists('pm_number_format')) ? pm_number_format($articles[$id]['views']) : number_format($articles[$id]['views'], 0, '.', ',');
			
			$articles[$id]['html5_datetime'] = date('Y-m-d\TH:i:sO', $article['date']); // ISO 8601
			
			$articles[$id]['full_datetime'] = date('l, F j, Y g:i A', $article['date']); 
			
			if ($config['comment_system'] == 'off')
			{
				$articles[$id]['allow_comments'] = 0;
			}
			
			unset($author, $unique_id, $sql, $result);
		}
	}
	
	if (count($articles) == 0)
	{
		return array('type' => 'error', 'msg' => $lang['article_no_articles']);
	}
	
	return $articles;
	
}

function art_make_link($type = 'article', $args = array())
{
	$url = '';
	
	$url = _URL .'/';
	switch ($type)
	{
		case 'article':
			
			if (_SEOMOD)
			{
				$url_part = '';
				
				if ($args['article_slug'] != '')
				{
					$url_part = $args['article_slug'];
				}
				else
				{
					$url_part = sanitize_title($args['title']);
					if (strlen($url_part) == 0)
					{
						$url_part = 'read';
					}
					$url_part = 'read-'. $url_part;
					$url_part = preg_replace('/-video$/', '_video', $url_part); // pre-v2.1
				}
				$url .= 'articles/'. $url_part .'_'. $args['id'] .'.'. _FEXT;
			}
			else
			{
				$url .= 'article_read.php?a='. $args['id'];
			}
		break;
		
		case 'category':
		case 'browse-articles':
		
			if (_SEOMOD)
			{
				$url .= 'articles/';
				
				if ($args['tag'] != '')
				{
					$url .= 'browse-'. $args['tag'];
				}
				else
				{
					$url .= 'index';
				}
				
				$url .= ($args['page'] != '') ? '-'. $args['page'] : '-1';
				$url .= '.'. _FEXT;
			}
			else
			{
				$url .= 'article.php?';
				
				if ($args['tag'] != '')
				{
					$url .= 'c='. $args['tag'];
				}
				
				$url .= '&page='. $args['page'];
			}
		
		break;
		
		case 'browse-tag':
		
			if (_SEOMOD)
			{
				$url .= 'articles/tag/';
				$url .= $args['tag'];
				$url .= '/';
				$url .= ($args['page'] != '') ? 'page-'. $args['page'] : 'page-1';
				$url .= '/';
			}
			else
			{
				$url .= 'article.php?';
				$url .= '&tag='. $args['tag'];
				$url .= '&page='. $args['page'];
			}
			
		break;
		
		case 'popular':
						
			if (_SEOMOD)
			{
				$url .= 'articles/popular';

				$url .= ($args['page'] != '') ? '-'. $args['page'] : '-1';
				$url .= '.'. _FEXT;
			}
			else
			{
				$url = 'article.php?';
				$url = 'show=popular';
				$url = '&page='. $args['page'];
			}
		break;
	}
	return $url;
}


function art_html_list_categories_display_item($item, &$all_children, $level = 0, $options)
{
	$li_class = $caturl = $output = $li_item = '';

	if ( ! $item)
		return;
	
	$padding = str_repeat("\t", $level);
	
	// href
	$caturl = $item['link'];
	
	$sub_cats = '';
	if (isset($all_children[$item['id']]) && ($level < $options['max_levels'] || $options['max_levels'] == 0))
	{
		$sub_cats .= "\n";
		
		foreach ($all_children[$item['id']] as $k => $child)
		{
			if ( ! isset($newlevel))
			{
				$newlevel = true;
//				$li_class .= 'topcat'; // @deprecated since v2.3
				$li_class .= 'dropdown-submenu';
//				$subcats_ul_class = ($child['id'] == $options['selected'] || $options['expand_items'] == true) ? 'visible_li' : 'hidden_li'; // @deprecated since v2.3
				$subcats_ul_class = ($child['id'] == $options['selected'] || $options['expand_items'] == true) ? 'dropdown-menu' : 'dropdown-menu';
				$sub_cats .= $padding."<ul class='".$subcats_ul_class."'>\n";
			}
			$sub_cats .= art_html_list_categories_display_item($child, $all_children, $level+1, $options);
		}
		unset($all_children[$item['id']]);
	}

	// li class
	if ($item['id'] == $options['selected'])
	{
		if ($item['parent_id'] == 0)
		{
			$li_class .= ' selectedcat';
		}
		else 
		{
			$li_class .= ' selectedsubcat';
		}
	}
	else 
	{
		$li_class .= '';
	}
	
	if ($options['selected_grandfather'] > 0)
	{
		if ($item['id'] == $options['selected_grandfather'])
		{
			if ($item['parent_id'] == 0)
			{
				$li_class .= ' selectedcat';
			}
			else 
			{
				$li_class .= ' selectedsubcat';
			}
		}
	}
	
	// li
	$output .= $padding .'<li class="'. $li_class .'"><a href="'. $caturl .'" class="'.$li_class.'">'. htmlentities($item['name'],ENT_COMPAT,'UTF-8') .'</a>';
	$output .= $sub_cats;

	if (isset($newlevel) && $newlevel)
	{
		$output .= $padding."</ul>\n";
	}
	
	$output .= $padding."</li>\n";

	return $output;
}

function art_html_list_categories($selected = 0, $categories = '', $args = array()) 
{
	global $lang, $show_popular;
	$output = '';
	$defaults = array(
		'selected' => 0, 
		'order_by' => 'position',
		'sort' => 'ASC',
		'selected_grandfather' => 0,
		'max_levels' => 0, 
		'ul_wrapper' => true
	);
	
	$options = array_merge($defaults, $args);
	$options['selected'] = $selected;
	extract($options);
	
	if ( ! is_array($categories))
	{
		$categories = art_get_categories($args);
	}
	
	$parents = $parent_ids = $children = array();
	
	foreach ($categories as $id => $cat_arr)
	{
		if ($cat_arr['parent_id'] == 0)
		{
			$parents[] = $cat_arr;
			$parent_ids[] = $cat_arr['id'];
		}
		else
		{
			$children[$cat_arr['parent_id']][] = $cat_arr;
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
		$output .= art_html_list_categories_display_item($p, $children, 0, $options);
	}
	
	if (count($children) > 0 && $options['max_levels'] == 0)
	{
		foreach ($children as $parent_id => $orphans)
		{
			foreach ($orphans as $k => $orphan)
			{
				$orphan['parent_id'] = 0;
				$output .= art_html_list_categories_display_item($orphan, $empty, 0, $options);
			}
		}
	}
	
	// Popular articles link
	$popular_li_class = '';
	if ($show_popular)
	{
		$popular_li_class = ' class="selectedcat" ';
	}
	
	if (_SEOMOD)
	{
		$output = '<li '. $popular_li_class .'><a href="'. _URL .'/articles/popular-1.'. _FEXT .'">'.$lang['articles_mostread'].'</a></li>'. $output;
	}
	else
	{
		$output = '<li '. $popular_li_class .'><a href="'. _URL .'/article.php?show=popular">'.$lang['articles_mostread'].'</a></li>'. $output;
	}

	//	wrapper
	$output = '<li><a href="'. _URL .'/article.'. _FEXT .'">'.$lang['articles_latest'].'</a></li>'. $output;
	
	//	wrapper
	if ($ul_wrapper)
	{
		// $output = "<ul id='ul_categories'>\n$output\n</ul>"; // @deprecated since v2.3
	}	
	return $output;
}

function art_pagination($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "&page=", $seomod = 0)
{
	global $lang;
	
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 15;
	if(!$page) $page = 1;
	if(!$targetpage) $targetpage = "/";
	
	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($totalitems / $limit);
	$lpm1 = $lastpage - 1;
	$seo_url_regex = '/(index|browse|popular|tag\/)(.*?)([^(\.\/\?|\&|$)]*)/';
	
	$type = 'browse-articles';	//	default value

	if($seomod == 1)
	{	
		@preg_match($seo_url_regex, $targetpage, $matches);

		if ($matches[1] == 'browse')
		{
			$type = 'category';
			$tag = rtrim($matches[3], '0123456789');	//	remove page number
			$tag = trim($tag , '-');
		}
		else if ($matches[1] == 'index')
		{
			$type = 'browse-articles';
		}
		else if ($matches[1] == 'tag/')
		{
			$type = 'browse-tag';
			$tag = trim($matches[3], '-/_');
		}
		else if ($matches[1] == 'popular')
		{
			$type = 'popular';
		}

		$pagestring1	= art_make_link($type, array('page' => '1', 'tag' => $tag));
		$pagestring2	= art_make_link($type, array('page' => '2', 'tag' => $tag));
		$pagestringlpm1	= art_make_link($type, array('page' => $lpm1, 'tag' => $tag));
		$pagestringlast	= art_make_link($type, array('page' => $lastpage, 'tag' => $tag));
	}
	else
	{
		if(strpos($pagestring, 'page=', 0) === FALSE)
			$pagestring .= "&page=";
		
		$pagestring1 	= preg_replace('/page=([0-9]*)/', 'page=1', $pagestring);
		$pagestring2 	= preg_replace('/page=([0-9]*)/', 'page=2', $pagestring);
		$pagestringlpm1 = preg_replace('/page=([0-9]*)/', 'page='.$lpm1, $pagestring);
		$pagestringlast = preg_replace('/page=([0-9]*)/', 'page='.$lastpage, $pagestring);
	}
	
	$obj = array();
	
	if($lastpage > 1)
	{	
		//previous button
		if ($page > 1)
		{
			if($seomod == 1)
			{
				$url_query = art_make_link($type, array('page' => $prev, 'tag' => $tag));
				$obj[] = array('li' => array('class' => ''),
						     'a' =>  array('href' => $url_query
									   ),
						     'text' => '&laquo;'
					  );
			}
			else
			{
				$url_query = preg_replace('/page=([0-9]*)/', 'page='.$prev, $pagestring);
				$obj[] = array('li' => array('class' => ''),
						     'a' =>  array('href' => $targetpage .'?'. $url_query
									   ),
						     'text' => '&laquo;'
					  );
			}
		}
		else
		{
			$obj[] = array('li' => array('class' => 'disabled'),
						 'a' =>  array('href' => '#',
							 		   'onclick' => 'return false;'
								),
						 'text' => '&laquo;'
					  );
		}
				
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				{
					$obj[] = array('li' => array('class' => 'active'),
								 'a' =>  array('href' => '#',
									 		   'onclick' => 'return false;'
										),
								 'text' => $counter
							  );
				}
				else
				{
					if($seomod == 1)
					{
						$url_query = art_make_link($type, array('page' => $counter, 'tag' => $tag));
						$obj[] = array('li' => array('class' => ''),
									 'a' =>  array('href' => $url_query
											),
									 'text' => $counter
								  );
					}
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						$obj[] = array('li' => array('class' => ''),
									 'a' =>  array('href' => $targetpage .'?'. $url_query
											),
									 'text' => $counter
								  );
					}
				}					
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 2 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
					{
						$obj[] = array('li' => array('class' => 'active'),
									 'a' =>  array('href' => '#',
									 			   'onclick' => 'return false;'
											),
									 'text' => $counter
								  );
					}
					else
					{
						if($seomod == 1)
						{
							$url_query = art_make_link($type, array('page' => $counter, 'tag' => $tag));
							$obj[] = array('li' => array('class' => ''),
										 'a' =>  array('href' => $url_query
												),
										 'text' => $counter
									  );
						}
						else
						{
							$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
							$obj[] = array('li' => array('class' => ''),
										 'a' =>  array('href' => $targetpage .'?'. $url_query
												),
										 'text' => $counter
									  );
						}
					}				
				}
				$obj[] = array('li' => array('class' => 'disabled'),
							 'a' =>  array('href' => '#',
							 			   'onclick' => 'return false;'
									),
							 'text' => '...'
						  );
				if($seomod == 1)
				{					
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestringlpm1
										),
								 'text' => $lpm1
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestringlast
										),
								 'text' => $lastpage
							  );
					
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestringlpm1
										),
								 'text' => $lpm1
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestringlast
										),
								 'text' => $lastpage
							  );
				}
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				if($seomod == 1)
				{					
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestring1
										),
								 'text' => '1'
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestring2
										),
								 'text' => '2'
							  );	
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestring1
										),
								 'text' => '1'
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestring2
										),
								 'text' => '2'
							  );	
				}				
				$obj[] = array('li' => array('class' => 'disabled'),
							 'a' =>  array('href' => '#',
							 			   'onclick' => 'return false;'
									),
							 'text' => '...'
						  );
							  
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
					{
						$obj[] = array('li' => array('class' => 'active'),
									 'a' =>  array('href' => '#',
										 		   'onclick' => 'return false;'
											),
									 'text' => $counter
								  );
						
					}
					else
					{
						if($seomod == 1)
						{
							$url_query = art_make_link($type, array('page' => $counter, 'tag' => $tag));
							$obj[] = array('li' => array('class' => ''),
									 'a' =>  array('href' => $url_query
											),
									 'text' => $counter
								  );
						}
						else
						{
							$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
							$obj[] = array('li' => array('class' => ''),
									 'a' =>  array('href' => $targetpage .'?'. $url_query
											),
									 'text' => $counter
								  );
						}						
					}
				}

				$obj[] = array('li' => array('class' => 'disabled'),
							 'a' =>  array('href' => '#',
							 			   'onclick' => 'return false;'
									),
							 'text' => '...'
						  );
				if($seomod == 1)
				{
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestringlpm1
										),
								 'text' => $lpm1
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestringlast
										),
								 'text' => $lastpage
							  );
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestringlpm1
										),
								 'text' => $lpm1
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestringlast
										),
								 'text' => $lastpage
							  );	
				}					
			}
			//close to end; only hide early pages
			else
			{
				if($seomod == 1)
				{
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestring1
										),
								 'text' => '1'
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $pagestring2
										),
								 'text' => '2'
							  );	
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestring1
										),
								 'text' => '1'
							  );
					$obj[] = array('li' => array('class' => ''),
								 'a' =>  array('href' => $targetpage .'?'. $pagestring2
										),
								 'text' => '2'
							  );	
				}				
				$obj[] = array('li' => array('class' => 'disabled'),
							 'a' =>  array('href' => '#',
							 			   'onclick' => 'return false;'
									),
							 'text' => '...'
						  );
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
					{
						$obj[] = array('li' => array('class' => 'active'),
									 'a' =>  array('href' => '#',
										 		   'onclick' => 'return false;'
											),
									 'text' => $counter
								  );
					}
					else
					{
						if($seomod == 1)
						{
							$url_query = art_make_link($type, array('page' => $counter, 'tag' => $tag));
							$obj[] = array('li' => array('class' => ''),
										 'a' =>  array('href' => $url_query
												),
										 'text' => $counter
									  );
						}
						else
						{
							$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
							$obj[] = array('li' => array('class' => ''),
									 'a' =>  array('href' => $targetpage .'?'. $url_query
											),
									 'text' => $counter
								  );
						}
					}
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
		{
			if($seomod == 1)
			{
				$url_query = art_make_link($type, array('page' => $next, 'tag' => $tag));
				$obj[] = array('li' => array('class' => ''),
							 'a' =>  array('href' => $url_query
									),
							 'text' => '&raquo;'
						  );
			}
			else
			{
				$url_query = preg_replace('/page=([0-9]*)/', 'page='.$next, $pagestring);
				$obj[] = array('li' => array('class' => ''),
							 'a' =>  array('href' => $targetpage .'?'. $url_query
									),
							 'text' => '&raquo;'
						  );
			}				
		}
		else
		{
			$obj[] = array('li' => array('class' => 'disabled'),
						 'a' =>  array('href' => '#',
						 			   'onclick' => 'return false;'
								),
						 'text' => '&raquo;'
					  );
		}
	}

	return $obj;
}


function art_tag_cloud($randomize = 0, $limit = 15, $shuffle = 1)
{	
	$max_size = 20;
	$min_size = 11;
	
	$sql = "SELECT *, COUNT(*) as numvids FROM art_tags GROUP BY tag";
	if($randomize == 0)
		$sql .= " ORDER BY numvids DESC";
	else
		$sql .= " ORDER BY tag_id DESC";
	if($limit != 0)
		$sql .= " LIMIT ".$limit;

	$result = mysql_query($sql);
	$tags = array();
	if($result)
	{
		$max = 0;
		$min = 10000;
		while($row = mysql_fetch_assoc($result))
		{
			if($row['numvids'] > $max)
			{
				$max = $row['numvids'];
			}
			if($row['numvids'] < $min)
			{
				$min = $row['numvids'];
			}
			//$tags[ $row['tag_id'] ] = $row;
			$tags[] = $row;
		}
		
		$spread = $max - $min;
		if($spread == 0)
			$spread = 1;
		$step = ($max_size - $min_size) / ($spread);
		
		foreach($tags as $tag_id => $tag)
		{
			$size = round($min_size + (($tag['numvids'] - $min) * $step));
			$href = art_make_link('browse-tag', array('tag' => $tag['safe_tag'], 'page' => '1'));
			
			$tags[ $tag_id ]['href'] = '<a href="'.$href.'" class="tag_cloud_link" style="font-size:'.$size.'px;">'.$tag['tag'].'</a> ';				
		}
	}
	if($shuffle == 1)
	{
		shuffle($tags);
	}
	return $tags;
}

function prep_title($title)
{
	return secure_sql($title);
}

function prep_content($content)
{
	if (strpos($content, 'src="..') !== false)
	{
		$content = str_ireplace('src="..', 'src="'. _URL, $content);
	}
	$content = str_replace('<HR>', '<hr>', $content);
	
	// remove "empty" prettyPhoto links
	$content = preg_replace('|<a href="[^"\r\n]*" rel="prettyPhoto\[phpmelody\]"><br></a>|', '', $content);
	
	$content = secure_sql($content);
	return $content;
}

function art_update_view_count($article_id)
{
	$updated = false;
	$read = array();
	
	if (pm_detect_crawler())
	{
		return false;
	}

	if ( ! array_key_exists('read', $_SESSION))
	{
		$_SESSION['read'] = '';
	}
	
	$read = (array) unserialize($_SESSION['read']);

	if ( ! in_array($article_id, $read))
	{
		$sql = "UPDATE art_articles 
				SET views = views+1   
				WHERE id = '". $article_id ."'";
		$result = @mysql_query($sql);
		$read[] = $article_id;
		if (count($read) > 25)
			$read = array(); // reset
		$_SESSION['read'] = serialize($read);
		$updated = true;
	}
	
	return $updated;
}


function art_cats_checklist_display_item($item, &$all_children, $level = 0, $options)
{
	$output = '';
	
	if ( ! $item)
		return '';

	$value_attr_db_col = ($options['value_attr_db_col'] == '') ? 'id' : $options['value_attr_db_col'];
	
	$padding = str_repeat($options['spacer'], $level);
	
	$sub_cats = '';
	if (isset($all_children[$item['id']]))
	{
		$sub_cats .= "\n";
		
		foreach ($all_children[$item['id']] as $k => $child)
		{
			if ( ! isset($newlevel))
			{
				$newlevel = true;
				$sub_cats .= "$padding<ul class=''>\n";
			}
			$sub_cats .= art_cats_checklist_display_item($child, $all_children, $level+1, $options);
		}
		unset($all_children[$item['id']]);
	}
	
	$output .= "<li>\n";
	$output .= ($level > 0) ? '<img src="images/ico_subcat2.gif" />' : '';
	$output .= "<label>\n";
	$output .= "". $padding;
	$output .= '<input type="checkbox" name="'. $options['attr_name'] .'" value="'. $item[$value_attr_db_col] .'" ';
	
	
	if ( ! is_array($options['selected']))
	{
		$output .= ($options['selected'] == $item[$value_attr_db_col]) ? 'checked="checked"' : '';
	}
	else
	{
		// multiple selected items for DDCL
		if (in_array($item[$value_attr_db_col], $options['selected']))
		{
			$output .= 'checked="checked"';
		}
	}
	$output .= ($options['attr_id'] != '') ? ' id="'. $options['attr_id'] .'" ' : '';
	$output .= ' /> '. htmlentities($item['name'],ENT_COMPAT,'UTF-8');
	$output .= "</label>\n";
	$output .= $sub_cats;
	if (isset($newlevel) && $newlevel)
	{
		$output .= "$padding</ul>\n";
	}
	
	$output .= '</li>';
	
	return $output;
}

function art_cats_checklist($categories = false, $args = array())
{
	$output = '';
	$empty = array();
	$defaults = array(
		'attr_id' => '',
		'attr_name' => 'categories[]',
		'value_attr_db_col' => 'id',
		'checked' => array(),
		'parents_only' => false,
		'spacer' => '',
		'no_items_text' => '',
		'selected' => '',
		'ul_wrapper' => true
	);
	
	$options = array_merge($defaults, $args);
	
	extract($options);
	
	if ( ! is_array($categories))
	{
		$categories = art_get_categories($args);
	}
	
	$parents = $parent_ids = $children = array();
	
	if (count($categories) == 0)
	{
		return $no_items_text;
	}
	
	foreach ($categories as $id => $cat_arr)
	{
		if ($cat_arr['parent_id'] == 0)
		{
			$parents[] = $cat_arr;
			$parent_ids[] = $cat_arr['id'];
		}
		else
		{
			$children[$cat_arr['parent_id']][] = $cat_arr;
		}
	}
	
	foreach ($parents as $k => $p)
	{
		if ($parents_only == true)
		{
			$output .= art_cats_checklist_display_item($p, $empty, 0, $options);
		}
		else
		{
			$output .= art_cats_checklist_display_item($p, $children, 0, $options);
		}
		
	}
	
	if (count($children) > 0 && ( ! $parents_only))
	{
		foreach ($children as $parent_id => $orphans)
		{
			foreach ($orphans as $k => $orphan)
			{
				$output .= art_cats_checklist_display_item($orphan, $empty, 0, $options);
			}
		}
	}
	
	if ($ul_wrapper)
	{
		return "<ul id='art_category_checklist'>\n$output\n</ul>";
	}
	
	return $output;
}

if ( ! function_exists('smarty_art_html_list_categories')) // otherwise, it will trigger a "duplicate function" error in /mobile/ and other areas when MOD_ARTICLE = OFF
{
	function smarty_art_html_list_categories($params, &$smarty)
	{
		$selected = ($params['selected']) ? $params['selected'] : 0;
		unset($params['selected']);
		$categories = ($params['categories']) ? $params['categories'] : '';
		unset($params['categories']);
		
		return art_html_list_categories($selected, $categories, $params);
	}
}

function get_related_article_list($category_id = 0, $title = '', $limit = 5)
{
	global $config, $article;
	
	if ( ! $category_id && $title == '')
	{
		return art_load_articles(0, $limit);
	}
	
	$ids = array();
	
	$sql = "SELECT id
			FROM art_articles 
			WHERE  
			MATCH (title) AGAINST ('". addslashes($title) ."')
			  AND date <= '". time() ."' 
			ORDER BY id DESC 
			LIMIT 0, $limit";
	
	$result = mysql_query($sql);
	if (mysql_num_rows($result) == 0) // backup method; just serve something
	{
		$sql = "SELECT id 
				FROM art_articles 
				WHERE id != '". $article['id'] ."'
				  AND status = '1' 
				  AND date <= '". time() ."' 
				  AND (category LIKE '". $category_id ."' 
				   OR category LIKE '". $category_id .",%' 
				   OR category LIKE '%,". $category_id ."' 
				   OR category LIKE '%,". $category_id .",%')";
		$result = mysql_query($sql);
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$ids[] = $row['id'];
	}
	mysql_free_result($result);
	$total_ids = count($ids);

	// fill it to the brim
	if ($total_ids > 0 && $total_ids < $limit)
	{
		$limit_left = $limit - $total_ids;
		
		$not_in_ids = $ids;
		$not_in_ids[] = $article['id'];
		
		$sql = "SELECT id 
				FROM art_articles 
				WHERE id NOT IN (". implode(',', $not_in_ids) .")
				  AND status = '1' 
				  AND date <= '". time() ."' 
				  AND (category LIKE '". $category_id ."' 
				   OR category LIKE '". $category_id .",%' 
				   OR category LIKE '%,". $category_id ."' 
				   OR category LIKE '%,". $category_id .",%')";
		$result = mysql_query($sql);
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$ids[] = $row['id'];
			}
			mysql_free_result($result);
		}
	}
	
	$unsorted_list = art_load_articles(0, $limit, '', 0, $ids);
	
	if ($unsorted_list['type'] != '')
	{
		return $unsorted_list;
	}
	
	$list =  array();
	$i = 0; 
	foreach ($ids as $k => $id)
	{
		foreach ($unsorted_list as $kk => $article_data)
		{
			if ($article_data['id'] == $id)
			{
				$list[$i] = (array) $article_data;
				break;
			}
		}
		$i++;
	}

	return $list;
}

function get_sticky_articles()
{
 	$sql = "SELECT id 
			FROM art_articles 
			WHERE status = 1 
			  AND date <= '". time() ."'
			  AND featured = '1' 
			ORDER BY id DESC";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) > 0)
	{
		$ids = array();
		while ($row = mysql_fetch_assoc($result))
		{
			$ids[] = $row['id'];
		}
		mysql_free_result($result);

		$sticky = art_load_articles(0, count($ids), '', 0, $ids);
		
		if ($sticky['type'] != '')
		{
			return false;
		}
		
		return $sticky;
	}
	
	return false;
}
