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
@header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 300));
@header("Cache-Control: no-cache");
@header("Pragma: no-cache");

error_reporting(0);

define('IGNORE_MOBILE',true);

require('config.php');
require_once('include/functions.php');
require_once('include/article_functions.php');



$rssVersion = '2.0';
$category 	= (int) $_GET['c'];
$category 	= abs($category);
$items 		= array();
$rss_for	= $_GET['feed'];
$mime_types = array('flv' => 'video/x-flv',
						'mp4' => 'video/mp4',
						'mov' => 'video/quicktime',
						'wmv' => 'video/x-ms-wmv',
						'divx' => 'video/divx',
						'avi' => 'video/divx',
						'mkv' => 'video/divx',
						'asf' => 'video/x-ms-asf', 
						'wma' => 'audio/x-ms-wma', 
						'mp3' => 'audio/mpeg', 
						'm4v' => 'video/mp4', 
						'm4a' => 'audio/mp4', 
						'3gp' => 'video/3gpp', 
						'3g2' => 'video/3gpp2'
						);
				
if ($rss_for == 'articles' && _MOD_ARTICLE != 1)
{
	$rss_for = '';
}

function clean_feed($input) 
{
	$original = array("<", ">", "&", '"', "'", "<br/>", "<br>");
	$replaced = array("&lt;", "&gt;", "&amp;", "&quot;","&apos;", "", "");
	$newinput = str_replace($original, $replaced, $input);
	
	return $newinput;
}

function rss_show_thumb($uniq_id, $source_id, $yt_thumb, $t_id = 1)
{
	if(_THUMB_FROM == 1) 	//	Outsource
	{	
		$thumb_not_url = ($yt_thumb != '' && strpos($yt_thumb, 'http') !== 0 && strpos($yt_thumb, '//') !== 0) ? true : false;
		if(($source_id == 1) || $thumb_not_url)	//	thumbnail is hosted locally
		{
			if($source_id == 1 && $yt_thumb == '')
			{
				//	default thumbnail
				$thumb_url = _NOTHUMB;
			}
			elseif($yt_thumb != '' && $thumb_not_url)
			{
				if(!file_exists(_THUMBS_DIR_PATH . $yt_thumb))
				{
					$thumb_url = _NOTHUMB;
				}
				else
				{
					$thumb_url = _THUMBS_DIR . $yt_thumb;
				}
			}
			else
			{
				$thumb_url = $yt_thumb;
			}
		}
		else
		{
			if($yt_thumb == '')
			{
				$thumb_url = _NOTHUMB;
			}
			else
			{
				$thumb_url = $yt_thumb;
			}
		}
	}
	else 	//	Localhost
	{
		if(!file_exists(_THUMBS_DIR_PATH . $uniq_id . "-" . $t_id . ".jpg"))
		{
			$thumb_url = _NOTHUMB;
		}
		else
		{
			$thumb_url = _THUMBS_DIR . $uniq_id . "-" . $t_id . ".jpg";
		}
	}
	return make_url_https($thumb_url);
}

function generateFeed($homeURL, $title, $version = '2.0', $items = array(), $category_data = '', $feed = '') 
{
	global $lang, $config, $mime_types;

	$output  = '<?xml version="1.0" encoding="UTF-8"?>';
	$output .= "\r\n";
	$output .= '<rss version="'. $version .'"';
	if ($feed == 'articles')
	{
		$output .= '>';
	}
	else
	{
		$output .= ' xmlns:media="http://search.yahoo.com/mrss/" xmlns:dcterms="http://purl.org/dc/terms/">';		
		
	}
	$output .= "\r\n";
	$output .= ' <channel>';
	$output .= "\r\n";
 
	if (is_array($category_data) && count($category_data) > 0)
	{
		$rss_title = $category_data['name'];
		
		if ($feed == 'articles')
		{
			$caturl = art_make_link('category', array('tag' => $category_data['tag'], 'page' => '1'));;		
		}
		else
		{
			// link
			if(_SEOMOD == 1) 
			{
				$caturl = _URL."/browse-". $category_data['tag'] ."-videos-1-date.html";
			}
			else
			{
				$caturl = _URL."/category.php?cat=". $category_data['tag'];
			}
		}
		
		$output .= '  <title>'. clean_feed($rss_title) .' - RSS Feed</title>';
		$output .= "\r\n";
		$output .= '  <link>'. clean_feed($caturl) .'</link>';
	}
	else
	{
		$rss_title = '';
		if ('' != $config['homepage_title'])
		{
			$rss_title = $config['homepage_title'];
		}
		else if (isset($lang['homepage_title']))
		{
			$rss_title = sprintf($lang['homepage_title'], _SITENAME);
		}
		else
		{
			$rss_title = ucwords($title);
		}	
		
		$output .= '  <title>'. clean_feed($rss_title) .' - RSS Feed</title>';
		$output .= "\r\n";
		$output .= '  <link>'. clean_feed(_URL) .'</link>';
	}
	$output .= "\r\n";
	
	if ('' != $config['homepage_description'])
	{
		$output .= '  <description>'. clean_feed($config['homepage_description']) .'</description>';
	}
	else
	{
		$output .= '  <description>'. clean_feed($rss_title) .'</description>';
	}
	
	$output .= "\r\n";

	if (count($items) > 0) 
	{
		foreach ($items as $k => $item)
		{
			if ($feed == 'articles')
			{
				$item['content'] = filter_text_https_friendly($item['content']);
				$date 	= date('Y-m-d', $item['date']);
				$pubDate= date('r', $item['date']);
				$title = clean_feed($item['title']);
				$pieces = array();
				$pieces = explode('<hr>', $item['content']);
				$desc = $pieces[0];
				$link = art_make_link('article', $item);
			}
			else
			{
				$item['source_id'] = (int) $item['source_id'];
				$desc_thumb = '';
				$date 	= date('Y-m-d', $item['added']);
				$pubDate= date('r', $item['added']);
				$title	= clean_feed($item['video_title']);			
				$desc 	= filter_text_https_friendly($item['description']);
				
				if (strlen($desc) == 0)
				{
					if (is_array($lang))
					{
						$desc = clean_feed($item['video_title']);
					}
					else
					{
						$desc = clean_feed($item['video_title']) .' added on '. $date;
					}
				}
				$link = makevideolink($item['uniq_id'], $item['video_title'], $item['video_slug']);
				
				//$player_url = _URL .'/player.swf';
				$player_url = _URL .'/players/flowplayer2/flowplayer.swf'; // @since v2.2
				
				$mime_type = '';
				if ($item['source_id'] == 1 || $item['source_id'] == 2)
				{
					$tmp_parts = explode('.', $item['url_flv']);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);
	
					if (array_key_exists($ext, $mime_types))
					{
						$mime_type = $mime_types[$ext];
					}
					else if (function_exists('finfo_open')) 
					{
						$finfo 		= finfo_open(FILEINFO_MIME);
						$mime_type 	= finfo_file($finfo, _VIDEOS_DIR_PATH . $item['url_flv']);
						finfo_close($finfo);
					}
				}
				else
				{
					$mime_type = $mime_types['flv'];
				}
				
				$fileSize = 0;
				if ($item['source_id'] == 1) // localhost
				{
					if (@file_exists(_VIDEOS_DIR_PATH . $item['url_flv']))
					{
						$fileSize = (int) @filesize(_VIDEOS_DIR_PATH . $item['url_flv']);
					}
				}
				
				$media_content_url = '';
				if (((strpos($item['url_flv'], 'http') === 0 || strpos($item['url_flv'], '//') === 0) && $item['source_id'] != 0) || $item['source_id'] == 1)
				{
					$tmp_parts = explode('.', $item['url_flv']);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);
	
					if (array_key_exists($ext, $mime_types))
					{
						$media_content_url = _URL .'/videos.php?vid='. $item['uniq_id'];
					}
				}
				
				$thumb_w = 0;
				$thumb_h = 0;
				if ($item['yt_thumb'] != '')
				{
					if (strpos($item['yt_thumb'], 'http') !== 0 && strpos($item['yt_thumb'], '//') !== 0)
					{
						if (@file_exists(_THUMBS_DIR_PATH . $item['yt_thumb']))
						{
							list($thumb_w, $thumb_h) = getimagesize(_THUMBS_DIR_PATH . $item['yt_thumb']);
						}
						else if (_THUMB_FROM == 2)
						{
							if (@file_exists(_THUMBS_DIR_PATH . $item['uniq_id'] . '-1.jpg'))
							{
								list($thumb_w, $thumb_h) = getimagesize(_THUMBS_DIR_PATH . $item['uniq_id'] . '-1.jpg');
							}
						}
					}
				}
				$thumb_url = rss_show_thumb($item['uniq_id'], $item['source_id'], $item['yt_thumb']);
				$desc_thumb = '<p><img src="'.$thumb_url.'" ';
				$desc_thumb .= ($thumb_w > 0) ? ' width="'. $thumb_w .'" ' : '';
				$desc_thumb .= ($thumb_h > 0) ? ' height="'. $thumb_h .'" ' : '';
				$desc_thumb .= ' /></p>';
			}
			$desc	= '<![CDATA['. $desc_thumb . $desc .']]>';
			
			$output .= ($feed == 'articles') ? '  <item>' : '  <item xmlns:media="http://search.yahoo.com/mrss/" xmlns:dcterms="http://purl.org/dc/terms/">';
			$output .= "\r\n";
			
			$output .= '   <title>'. $title .'</title>';
			$output .= "\r\n";
			$output .= '   <link>'. clean_feed($link) .'</link>';
			$output .= "\r\n";
			$output .= '   <description>'. str_replace("<img src=\"/uploads/articles/", "<img src=\""._URL."/uploads/articles/", $desc) .'</description>';
			$output .= "\r\n";
			$output .= '   <pubDate>'. $pubDate .'</pubDate>';
			$output .= "\r\n";
			
			if ($feed != 'articles')
			{
				$output .= '   <media:content medium="video"';
				$output .= ($media_content_url != '') ? ' url="'. make_url_https($media_content_url) .'" ' : '';
				$output .= ($item['yt_length'] > 0) ? ' duration="'. $item['yt_length'] .'" ' : '';
				$output .= ($mime_type != '' ) ? ' type="'. $mime_type .'" ' : '';
				$output .= ($fileSize > 0) ? ' fileSize="'. $fileSize .'" ' : '';
				$output .= ' height="'. _PLAYER_H .'" width="'. _PLAYER_W .'" ';
				$output .= '>';
				$output .= "\r\n";
				$output .= '   <media:player url="'. $player_url .'" />';
				$output .= "\r\n";
				$output .= '   <media:title>'. clean_feed($title) .'</media:title>';
				$output .= "\r\n";
				$output .= '   <media:description>'. clean_feed($desc) .'</media:description>';
				$output .= "\r\n";
				$output .= '   <media:thumbnail url="'. clean_feed($thumb_url) .'" ';
				$output .= ($thumb_w > 0) ? ' width="'. $thumb_w .'" ' : '';
				$output .= ($thumb_h > 0) ? ' height="'. $thumb_h .'" ' : '';
				$output .= '/>';
				$output .= "\r\n";
				$output .= '   </media:content>';
				$output .= "\r\n";
			}
			$output .= '   <guid>'. $link .'</guid>';
			$output .= "\r\n";
			$output .= '  </item>';
			$output .= "\r\n";
		}
	}
	
	$output .= ' </channel>';
	$output .= "\r\n";
	$output .= '</rss>';
	
	return $output;
}

if ( ! $category)
{
	if ($rss_for == 'articles')
	{
		$sql = "SELECT * 	
				FROM art_articles  
				WHERE status = '1' AND date <= '". time() ."' 
				ORDER BY date DESC 
				LIMIT 20";	
	}
	elseif ($rss_for == 'topvideos')
	{
		$sql = "SELECT * 	
				FROM pm_videos  
				WHERE added <= '". time() ."'
				ORDER BY site_views DESC 
				LIMIT 20";
	}

	else
	{
		$sql = "SELECT * 	
				FROM pm_videos 
				WHERE added <= '". time() ."'
				ORDER BY added DESC 
				LIMIT 20";
	}
}
else if ($category > 0) 
{ 
	if ($rss_for == 'articles')
	{
		$sql = "SELECT * 
				FROM art_articles 
				WHERE status = '1' 
				  AND (category LIKE '%,$category,%' 
				   OR category LIKE '%,$category' 
				   OR category LIKE '$category,%' 
				   OR category='$category') 
				  AND date <= '". time() ."'
				ORDER BY date DESC 
				LIMIT 20";
	}
	else
	{
		$sql = "SELECT * 
				FROM pm_videos 
				WHERE (category LIKE '%,$category,%' 
				   OR category LIKE '%,$category' 
				   OR category LIKE '$category,%' 
				   OR category='$category') 
				  AND added <= '". time() ."' 
				ORDER BY added DESC 
				LIMIT 20";
	}
}

$result = @mysql_query($sql);
if(@mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_assoc($result))
	{
		$items[] = $row;
	}
	
	mysql_free_result($result);
}
$cat_data = array();

if ($category > 0)
{
	if ($rss_for == 'articles')
	{
		$sql = "SELECT * 
				FROM art_categories 
				WHERE id='".$category."'";	
	}
	else
	{
		$sql = "SELECT * 
				FROM pm_categories 
				WHERE id='".$category."'";
	}
	
	$result = @mysql_query($sql); 
	$cat_data 	= @mysql_fetch_assoc($result);

	mysql_free_result($result);
}

@header("Content-Type: text/xml; charset=utf-8");

$url = _URL;
if ($rss_for == 'articles')
{
	$url .= (_SEOMOD) ? '/articles/' : '/article.php';
}
echo generateFeed($url , _SITENAME, $rssVersion, $items, $cat_data, $rss_for);
?>