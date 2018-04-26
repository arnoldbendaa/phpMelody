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

function get_page_by_name($page_name)
{
	$page_name = str_replace(' ', '', $page_name);
	
	if ('' == $page_name)
	{
		return array();
	}
	
	$sql = "SELECT id 
			FROM pm_pages 
			WHERE page_name = '". secure_sql($page_name) ."'";
	$result = mysql_query($sql);
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		return get_page($row['id']);
	}
	return array();
}

function get_page($page_id = 0)
{
	$page = array();
	$int_values = array('id', 'status', 'showinmenu', 'author', 'date', 'views');
	$page_id = (int) $page_id;
	
	if (!$page_id) 
	{
		return array();
	}
	
	$sql = "SELECT * 
			FROM pm_pages
			WHERE id = '". $page_id ."'";
		
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array();
	}
	
	$page = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	if (!$page)
	{
		return array();
	}
	
	foreach ($page as $k => $v)
	{
		if (in_array($k, $int_values))
		{
			$page[$k] = (int) $v;
		}
	}
	
	$meta_data = get_meta($page_id, IS_PAGE);
	
	$page['content'] = filter_text_https_friendly($page['content']);

	$page['meta_keywords'] = $meta_data['_meta_keywords'];
	$page['meta_description'] = $meta_data['_meta_description'];
	
	$page['title'] = htmlspecialchars($page['title']);

	return $page;
}

function insert_new_page($postarr)
{
	global $config;
	
	$title	 = trim($postarr['title']);
	$content = $postarr['content'];
	$status	 = (int) $postarr['status'];
	$showinmenu	 = (int) $postarr['showinmenu'];
	$author	 = (int) $postarr['author'];
	$page_name = trim($postarr['page_name']);
	
	if (strlen($title) == 0)
	{
		return array('type' => 'error', 'msg' => 'Please provide a title first.');
	}
	
	if ($author == 0)
	{
		$author = 1;	//	default user
	}
	$title 	 = prep_title($title);
	$content = prep_content($content);

	if ('' == $page_name)
		$page_name = filter_page_name($title);
	
	$page_name = filter_page_name($page_name);
	
	$sql = "INSERT INTO pm_pages 
					(title, content, author, date, status, showinmenu, page_name, views) 
			VALUES  ('". $title ."', 
					 '". $content ."', 
					 '". $author ."', 
					 '". time() ."', 
					 '". $status."',
					 '". $showinmenu."', 
					 '". $page_name ."', 
					 '0')";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => 'MySQL Error: '. mysql_error());
	}
	$last_id = mysql_insert_id();
	
	update_config('total_pages', $config['total_pages']+1, true);
	
	$postarr['meta_keywords'] = htmlspecialchars($postarr['meta_keywords'],  ENT_QUOTES);
	$postarr['meta_description'] = htmlspecialchars($postarr['meta_description'],  ENT_QUOTES);
	$postarr['meta_description'] = str_replace(array("\n", "\r"), '', $postarr['meta_description']);
	
	update_meta($last_id, IS_PAGE, '_meta_keywords', $postarr['meta_keywords']);
	update_meta($last_id, IS_PAGE, '_meta_description', $postarr['meta_description']);
	
	return array('type' => 'ok', 'msg' => 'Page published.', 'page_id' => $last_id);
}

function update_page($id, $postarr)
{
	$id		 = (int) $id;
	$title	 = trim($postarr['title']);
	$content = $postarr['content'];
	$status	 = (int) $postarr['status'];
	$showinmenu	 = (int) $postarr['showinmenu'];
	$author	 = (int) $postarr['author'];
	$page_name = trim($postarr['page_name']);

	if ($id == 0)
	{
		return array('type' => 'error', 'msg' => 'Invalid page ID');
	}
	
	if (strlen($title) == 0)
	{
		return array('type' => 'error', 'msg' => 'What is the title?');
	}
	
	$title 	 = prep_title($title);
	$content = prep_content($content);
	
	if ('' == $page_name)
		$page_name = filter_page_name($title);
	
	$page_name = filter_page_name($page_name);
	
	
	$sql = "UPDATE pm_pages  
			SET title = '". $title ."', 
				content = '". $content ."', 
				author = '". $author ."', 
				status = '". $status ."', 
				showinmenu = '". $showinmenu ."',
				page_name = '". $page_name ."' 
			WHERE id = '". $id ."'";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => 'MySQL Error: '. mysql_error());
	}
	
	$postarr['meta_keywords'] = html_entity_decode($postarr['meta_keywords'], ENT_QUOTES);
	$postarr['meta_keywords'] = htmlspecialchars($postarr['meta_keywords'], ENT_QUOTES);
	$postarr['meta_description'] = html_entity_decode($postarr['meta_description'], ENT_QUOTES);
	$postarr['meta_description'] = htmlspecialchars($postarr['meta_description'], ENT_QUOTES);
	$postarr['meta_description'] = str_replace(array("\n", "\r"), '', $postarr['meta_description']);
	
	update_meta($id, IS_PAGE, '_meta_keywords', $postarr['meta_keywords']);
	update_meta($id, IS_PAGE, '_meta_description', $postarr['meta_description']);

	return array('type' => 'ok', 'msg' => 'Page updated.');
}

function delete_page($id)
{
	global $config;
	
	$id = (int) $id;
	
	if ($id == 0)
	{
		return array('type' => 'error', 'msg' => 'Sorry, this page does NOT exist. Invalid page ID.'); 
	}
	
	$sql = "DELETE FROM pm_pages 
			WHERE id = '". $id ."'";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return array('type' => 'error', 'msg' => 'Could not delete this page. A MySQL error occurred:'. mysql_error());
	}
	
	update_config('total_pages', $config['total_pages']-1, true);
	
	delete_meta($id, IS_PAGE);
	
	return array('type' => 'ok', 'msg' => 'Page deleted.');
}

function mass_delete_pages($ids)
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
		$ids_str = implode(', ', $ids);
		
		$sql = "DELETE FROM pm_pages 
				WHERE id IN (" . $ids_str . ")";
		$result = mysql_query($sql);
		if ( ! $result)
		{
			return array('type' => 'error', 'msg' => 'Could not delete these pages. A MySQL error occurred: '. mysql_error());
		}
		
		foreach ($ids as $k => $id)
		{
			delete_meta($id, IS_PAGE);
		}
		
		update_config('total_pages', count_entries('pm_pages', '', ''), true);

		return array('type' => 'ok', 'msg' => 'The selected pages have been deleted.');
	}
	return false;
}

function list_pages($search_term, $search_type = 'title', $from = 0, $to = 20, $filter = '', $filter_value = '') 
{
	$int_values = array('id', 'status', 'author', 'showinmenu', 'date', 'views');
	$pages	= array();
	
	if ('' != $search_term) 
	{
		switch ($search_type)
		{
			default:
			case 'title': 
				
				$sql = "SELECT * 
						FROM pm_pages  
						WHERE title LIKE '%". secure_sql($search_term) ."%' 
						ORDER BY id DESC";
			break;
			
			case 'content':

				$sql = "SELECT * 
						FROM pm_pages   
						WHERE content LIKE '%". secure_sql($search_term) ."%' 
						ORDER BY id DESC";
			break;
		}
	}
	else 
	{
		$sql = "SELECT * 
				FROM pm_pages ";
				
		if ($filter != '')
		{
			switch ($filter)
			{
				case 'mostviewed':
				
					$sql .= " ORDER BY views DESC ";
					
				break;
				
				case 'private':
					
					$sql .= " WHERE status = '0' 
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
		while ($row = mysql_fetch_assoc($result))
		{
			foreach ($row as $k => $v)
			{
				if (in_array($k, $int_values))
				{
					$row[$k] = (int) $v;
				}
			}
			$pages[$i] = $row;
			
			$meta_data = get_meta($row['id'], IS_PAGE);
			$pages[$i]['meta_keywords'] = unspecialchars($meta_data['_meta_keywords']);
			$pages[$i]['meta_description'] = unspecialchars($meta_data['_meta_description']);
			
			$i++;
		}
		
		mysql_free_result($result);
	}
	
	return $pages;
}

function make_page_link($page = array())
{
	$url = _URL .'/';
	
	if (_SEOMOD)
	{
		if ('' !=  $page['page_name'])
		{
			$url .= 'pages/'. $page['page_name'] .'.html';			
		}
		else // missing page name
		{
			$url .= 'page.php?p='. $page['id']; 
		}
	}
	else
	{
		$url .= 'page.php?p='. $page['id']; 
	}
	
	return $url;
}

function filter_page_name($string)
{
	$string = trim($string);
	$string = sanitize_title($string);
	
	return $string;
}

function generate_page_links($filter = '')
{
	global $config;
	
	$pages = array();
		
	if ($config['total_pages'] == 0)
	{
		return '';
	}
	
	$sql = "SELECT id, title, page_name 
			FROM pm_pages 
			WHERE status = '1'";

	if($filter == 'header')
	{
	$sql .= "AND showinmenu = '1' ";
	}
	
	$sql .= "AND page_name != '404' 
			  AND page_name != 'terms-toa'
			ORDER BY id ASC";
	$result = mysql_query($sql);
	
	if ( ! $result)
	{
		return '';
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$pages[] = $row;
	}
	mysql_free_result($result);
	
	$links_to_pages = array();
	foreach ($pages as $id => $page)
	{
		$links_to_pages[] = array('page_url' => make_page_link($page),
								  'title' => htmlspecialchars($page['title']),
								  'title_escaped' => htmlentities($page['title'])
								 );
	}
	
	return $links_to_pages; 
}

function page_update_view_count($page_id)
{
	$updated = false;
	$read = array();
	
	if (pm_detect_crawler())
	{
		return false;
	}
	
	if ( ! in_array('read_pages', $_SESSION))
	{
		$_SESSION['read_pages'] = '';
	}
	
	$read = (array) unserialize($_SESSION['read_pages']);

	if ( ! in_array($page_id, $read))
	{
		$sql = "UPDATE pm_pages  
				SET views = views+1   
				WHERE id = '". $page_id ."'";
		$result = @mysql_query($sql);
		$read[] = $page_id;
		$_SESSION['read_pages'] = serialize($read);
		$updated = true;
	}
	
	return $updated;
}
