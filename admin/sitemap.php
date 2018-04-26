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
// | Copyright: (c) 2004-2016 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+


/*
 * START local functions
 */
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

function xmlnl()
{
	global $xml_output;
	$xml_output .= "\r\n";
}

function create_sitemap_index($args = array()) 
{
	global $xml_output;

	$defaults = array(	'xml_version' => '1.0',
						'encoding' => 'UTF-8', 
						'rss_version' => '2.0',
						'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
						'link_url' => _URL,
						'total_files' => 1,
						'sitemap_type' => 'sitemap'
					);
					
	$options = array_merge($defaults, $args);
	extract($options);
	
	$xml_output .= '<?xml version="'. $xml_version .'" encoding="'. $encoding .'"?>';
	xmlnl();
	$xml_output .= '<sitemapindex ';
	
	if (is_array($xmlns))
	{
		foreach ($xmlns as $type => $url)
		{
			$xml_output .= ' xmlns:'. $type .'="'. $url .'" ';
		}
	}
	else
	{
		$xml_output .= ' xmlns="'. $xmlns .'" ';
	}
	$xml_output .= '>';
	xmlnl();
	
	if ($sitemap_type == 'sitemap')
	{
		$xml_output .= '<sitemap>';
		xmlnl();
		$xml_output .= '  <loc>'. _URL .'/'. _UPFOLDER .'/sitemap-base.xml</loc>';
		xmlnl();
		$xml_output .= '  <lastmod>'. date('c') .'</lastmod>';
		xmlnl();
		$xml_output .= '</sitemap>';
		xmlnl();
	}
	
	for ($i = 1; $i <= $total_files; $i++)
	{
		$xml_output .= '<sitemap>';
		xmlnl();
		
		if ($sitemap_type == 'video-sitemap')
		{
			$filename = 'video-sitemap-'. $i .'.xml';
		}
		else
		{
			$filename = 'sitemap-'. $i .'.xml';
		}
		
		$xml_output .= '  <loc>'. _URL .'/'. _UPFOLDER .'/'. $filename .'</loc>';
		xmlnl();
		$xml_output .= '  <lastmod>'. date('c') .'</lastmod>';
		xmlnl();
		$xml_output .= '</sitemap>';
		xmlnl();
	}
	$xml_output .= '</sitemapindex>';
	
	return;
}

function sitemap_header($args = array())
{
	global $xml_output, $lang, $config;
	
	$defaults = array(	'xml_version' => '1.0',
						'encoding' => 'UTF-8', 
						'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
						'link_url' => _URL,
					);
	
	$options = array_merge($defaults, $args);
	extract($options); 
	
	$xml_output .= '<?xml version="'. $xml_version .'" encoding="'. $encoding .'"?>';
	xmlnl();
	$xml_output .= '<urlset ';
	
	if (is_array($xmlns))
	{
		foreach ($xmlns as $type => $url)
		{
			$xml_output .= ' xmlns:'. $type .'="'. $url .'" ';
		}
	}
	else
	{
		$xml_output .= ' xmlns="'. $xmlns .'" ';
	}
	
	$xml_output .= '>';
	xmlnl();
	
	return;	
}

function sitemap_footer()
{
	global $xml_output;
	
	xmlnl();
	$xml_output .= '</urlset>';
	
	return;
}

function sitemap_item($url = '', $args = array()) 
{
	global $xml_output, $config;
	
	if (strlen($url) == 0)
		return;
	
	$defaults = array('changefreq' => false,
					  'lastmod' => false,
					  'lastmod_format' => 'c'
				);
	$options = array_merge($defaults, $args);
	extract($options); 
	
	$xml_output .= '<url>';
	xmlnl();

	$xml_output .= '<loc>'. clean_feed(make_url_https($url)) .'</loc>';
	xmlnl();
	
	if ($lastmod)
	{
		$xml_output .= '<lastmod>'. date($lastmod_format, $lastmod) .'</lastmod>';
		xmlnl();
	}
	
	if ($changefreq)
	{
		$xml_output .= '<changefreq>'. $changefreq .'</changefreq>';
		xmlnl();
	}
	
	$xml_output .= '</url>';
	xmlnl();

	return;
}

function video_sitemap_header($args = array()) 
{
	global $xml_output, $lang, $config, $file_ext;
	
	$defaults = array(	'xml_version' => '1.0',
						'encoding' => 'UTF-8', 
						'rss_version' => '2.0',
						'xmlns' => array('media' => 'http://search.yahoo.com/mrss/',
										 'dcterms' => 'http://purl.org/dc/terms/'
									),
						'link_url' => _URL,
					);
	
	$options = array_merge($defaults, $args);
	extract($options); 
	
	$xml_output .= '<?xml version="'. $xml_version .'" encoding="'. $encoding .'"?>';
	xmlnl();
	$xml_output .= '<rss version="'. $rss_version .'" ';
	
	if (is_array($xmlns))
	{
		foreach ($xmlns as $type => $url)
		{
			$xml_output .= ' xmlns:'. $type .'="'. $url .'" ';
		}
	}
	else
	{
		$xml_output .= ' xmlns="'. $xmlns .'" ';
	}
	
	$xml_output .= '>';
	xmlnl();
	$xml_output .= '<channel>';
	xmlnl();
	$xml_output .= '<link>'. str_replace($file_ext, '.xml', $link_url) .'</link>';
	xmlnl();
	
	// <title>
	if ($config['homepage_title'] != '')
	{
		$channel_title .= clean_feed($config['homepage_title']);
	}
	else
	{
		$channel_title .= clean_feed(sprintf($lang['homepage_title'], _SITENAME));
	}
	$xml_output .= '<title>'. $channel_title .'</title>';
	xmlnl();
	
	// <description>
	$xml_output .= '<description>';
	if ($config['homepage_description'] != '')
	{
		$xml_output .= clean_feed($config['homepage_description']);
	}
	else 
	{
		$xml_output .= $channel_title;
	}
	$xml_output .= '</description>';
	xmlnl();
	
	return;
}

function video_sitemap_item($item = array(), $args = array()) 
{
	global $xml_output, $config, $lang, $mime_types, $video_sources, $file_ext;
	
	$no_thumb = ABSPATH . '/templates/'. _TPLFOLDER .'/images/no-thumbnail.jpg';
	if (count($item) == 0)
		return;
	
	$defaults = array('media_keywords' => false,
					  'media_category' => false,
					  'item_pubDate' => false
				);
	$options = array_merge($defaults, $args);
	extract($options); 
	
	
	$item['source_id'] = (int) $item['source_id'];
	$item['restricted'] = (int) $item['restricted'];
	
	$date 	= date('Y-m-d', $item['added']);
	$pubDate= date('r', $item['added']);
	$title	= clean_feed($item['video_title']);
	$desc 	= generate_excerpt($item['description'], 255);
	$link = makevideolink($item['uniq_id'], $item['video_title'], $item['video_slug']);
	
	// description
	if (strlen($desc) == 0)
	{
		$desc = clean_feed($item['video_title']);
	}
	//$desc = htmlentities($desc, ENT_QUOTES); // does not validate
	$desc	= '<![CDATA['. $desc .']]>';

	// media:content type
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
			$finfo 		= @finfo_open(FILEINFO_MIME);
			$mime_type 	= @finfo_file($finfo, _VIDEOS_DIR_PATH . $item['url_flv']);
			finfo_close($finfo);
		}
	}
	else
	{
		$mime_type = $mime_types['flv'];
	}
	
	// fileSize
	$fileSize = 0;
	if ($item['source_id'] == 1) // localhost
	{
		if (@file_exists(_VIDEOS_DIR_PATH . $item['url_flv']))
		{
			$fileSize = (int) @filesize(_VIDEOS_DIR_PATH . $item['url_flv']);
		}
	}
	
	$thumb_url = rss_show_thumb($item['uniq_id'], $item['source_id'], $item['yt_thumb']);
	if (strpos($thumb_url, '?'))
	{
		$pieces = explode('?', $thumb_url);
		$thumb_url = $pieces[0] .'?'. clean_feed($pieces[1]);
	}
	
	$thumb_w = 0;
	$thumb_h = 0;
	
	if ($item['yt_thumb'] != '')
	{
		if (strpos($item['yt_thumb'], 'http') !== 0)
		{
			if (@file_exists(_THUMBS_DIR_PATH . $item['yt_thumb']))
			{
				list($thumb_w, $thumb_h) = getimagesize(_THUMBS_DIR_PATH . $item['yt_thumb']);
			}
		}
		else if (_THUMB_FROM == 2)
		{
			if (@file_exists(_THUMBS_DIR_PATH . $item['uniq_id'] . '-1.jpg'))
			{
				list($thumb_w, $thumb_h) = getimagesize(_THUMBS_DIR_PATH . $item['uniq_id'] . '-1.jpg');
			}
		}
	}

	// media:player START
	//$player_url = _URL .'/player.swf';
	$player_url = '';
	$flashvars = '';
	$swf_player_type = '';
	
	if ($item['source_id'] == $video_sources['youtube']['source_id'] && $item['direct'] == '')
	{
		$item['direct'] = make_url_https('http://www.youtube.com/watch?v='. $item['yt_id']);
	}
	
	$swf_player_type = $config['video_player'];
	
	switch ($config['video_player'])
	{
		case 'jwplayer':
		case 'flvplayer':
			
			if ($video_sources[ $item['source_id'] ]['flv_player_support'] == 0 || 
				$video_sources[ $item['source_id'] ]['user_choice'] == 'embed')
			{
				$swf_player_type	= 'embed';
			}
			
		break;
	
		case 'embed':
			
			if ($video_sources[ $item['source_id'] ]['embed_player_support'] == 0)
			{
				$swf_player_type	= 'flvplayer';
			}
			
		break;
	}
			
	if ($item['source_id'] == 0)
	{
		$sql = "SELECT * 
				FROM pm_embed_code 
				WHERE uniq_id = '". $item['uniq_id'] ."'";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if (is_serialized($row['embed_code']))
		{
			$swf_player_type = 'jwplayer';
			
			$item_flashvars = unserialize($row['embed_code']);
			$pieces = explode(';', $item['url_flv'], 2);
			
			$player_url = _URL .'/players/jwplayer5/jwplayer.swf'; // @since v2.2
			$flashvars .= 'file='. str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), make_url_https($pieces[0]));
			$flashvars .= '&streamer='. str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), make_url_https($pieces[1]));
			$flashvars .= ($item_flashvars['provider'] != '') ? '&provider='. make_url_https($item_flashvars['provider']) : '';
			$flashvars .= ($item_flashvars['startparam'] != '') ? '&http.startparam='. $item_flashvars['startparam'] : '';
			$flashvars .= ($item_flashvars['loadbalance'] != '') ? '&rtmp.loadbalance='. $item_flashvars['loadbalance'] : '';
			$flashvars .= ($item_flashvars['subscribe'] != '') ? '&rtmp.subscribe='. $item_flashvars['subscribe'] : '';
			//$flashvars .= '&config='. urlencode(_URL ."/jwembed.xml");
			$flashvars .= '&config='. urlencode(_URL ."/players/jwplayer5/jwembed.xml");
			unset($item_flashvars, $pieces);
		}
		else
		{
			if (preg_match('/src="(.*?)"/i', $row['embed_code'], $matches) != 0)
			{
				$player_url = $matches[1];
			}
			else if (preg_match('/name="movie" value="(.*?)"/i', $row['embed_code'], $matches) != 0)
			{
				$player_url = $matches[1];
			}
			
			if (strpos($player_url, '//') === 0)
			{
				$player_url = (strpos($player_url, 'http') !== 0) ? 'http:'. $player_url : ltrim($player_url, '/');
			}
			
			if (preg_match('/flashvars="(.*?)"/i', $row['embed_code'], $matches) != 0)
			{
				$flashvars = $matches[1];
			}
			
			$swf_player_type = 'embed';
		}
	}
	
	if ($item['source_id'] == 1 || $item['source_id'] == 2)
	{
		// PLAYER TYPE + File TYPE => 
		$tmp_parts = explode('.', $item['url_flv']);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);
		switch ($ext)
		{
			case 'mov': case '3gp': case '3g2':	case 'm4a': case 'wmv': case 'asf': case 'wma': case 'mkv': case 'divx': case 'avi':
			$player_url = '';
			break;

			case 'mp3':
				
				$item['url_flv'] = _URL .'/videos.php?vid='. $item['uniq_id'];
				
				$player_url = _URL .'/players/jwplayer5/jwplayer.swf'; // @since v2.2	
				$flashvars .= 'file='. str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), $item['url_flv']);
				$flashvars .= '&type=sound';
				//$flashvars .= '&config='. urlencode(_URL ."/jwembed.xml");
				$flashvars .= '&config='. urlencode(_URL ."/players/jwplayer5/jwembed.xml");

			break;
		}
	}

	if ($item['source_id'] > 2)
	{
		switch ($swf_player_type)
		{
			case 'jwplayer7':
			case 'jwplayer6': // jw6 doesn't support passing config data via GET, so fallback to jw5 and maintain indexable URLs
			case 'jwplayer':
			case 'videojs':
				
				$player_url = _URL .'/players/jwplayer5/jwplayer.swf';
				
				if ($item['source_id'] == 3)
				{
					$flashvars .= 'file='. urlencode(make_url_https($item['direct']));
					$flashvars .= '&type=youtube';
				}
				else
				{
					$flashvars .= 'file='. urlencode(_URL ."/videos.php?vid=". $item['uniq_id']);
					$flashvars .= '&type=video';
				}
				//$flashvars .= '&config='. urlencode(_URL ."/jwembed.xml");
				$flashvars .= '&config='. urlencode(_URL ."/players/jwplayer5/jwembed.xml");
				
			break;
			
			case 'flvplayer':
				
				//$player_url = _URL .'/fpembed-'. $item['uniq_id'] .'.swf'; @deprecated since v2.3 because of possible mod_rewrite issues
				$player_url = _URL .'/fpembed.php?vid='. $item['uniq_id']; 
				$flashvars = '';

			break;
			
			case 'embed':
				
				$embed_code = $video_sources[ $item['source_id'] ]['embed_code'];
				$embed_code = str_replace("%%yt_id%%", $item['yt_id'], $embed_code);
				$temp_url_flv = make_url_https(str_replace("&", "&amp;", $item['url_flv']));
				$embed_code = str_replace("%%url_flv%%", $temp_url_flv, $embed_code);
				$embed_code = str_replace("%%direct%%", $item['direct'], $embed_code);
				$embed_code = str_replace("%%player_w%%", _PLAYER_W_EMBED, $embed_code);
				$embed_code = str_replace("%%player_h%%", _PLAYER_H_EMBED, $embed_code);
				$embed_code = str_replace("%%player_autoplay%%", '0', $embed_code);
			
				if ($item['source_id'] == $video_sources['trilulilu']['source_id'] && $item['direct'] != '')
				{
					$temp = '';
					$temp = rtrim($item['direct'], "/");
					$temp = str_replace(array('http://', 'https://', 'www.'), "", $temp);
					
					@preg_match('/^trilulilu\.ro\/(.*?)\/([a-zA-Z0-9]+)$/i', $temp, $matches);
					$embed_code = str_replace("%%username%%", $matches[1], $embed_code);
				}
				
				if (preg_match('/src="(.*?)"/i', $embed_code, $matches) != 0)
				{
					$player_url = $matches[1];
				}
				else if (preg_match('/name="movie" value="(.*?)"/i', $embed_code, $matches) != 0)
				{
					$player_url = $matches[1];
				}
				
				if (preg_match('/flashvars="(.*?)"/i', $embed_code, $matches) != 0)
				{
					$flashvars = $matches[1];
				}

			break;
		}
	}
	$player_url = make_url_https($player_url);
	// media:player END

	// media:content url
	$media_content_url = '';
	if ($player_url == '')
	{
		if ((strpos($item['url_flv'], 'http') !== false) || ($item['source_id'] == 1))
		{
			$tmp_parts = explode('.', $item['url_flv']);
			$ext = array_pop($tmp_parts);
			$ext = strtolower($ext);
	
			if (array_key_exists($ext, $mime_types))
			{
				$media_content_url = _URL .'/videos.php?vid='. $item['uniq_id'];
			}
		}
	}
	
	$flashvars = str_replace('?', '', $flashvars);
	$flashvars = ($flashvars != '') ? '?'.$flashvars : $flashvars;
	
	// START output 
	$xml_output = '<item>';
	xmlnl();
	
	$xml_output .= '<link>'. str_replace($file_ext, '.xml', $link) .'</link>';
	xmlnl();
	
	$xml_output .= '<media:content medium="video"';
	$xml_output .= ($media_content_url != '') ? ' url="'. $media_content_url .'" ' : '';
	$xml_output .= ($item['yt_length'] > 0) ? ' duration="'. $item['yt_length'] .'" ' : '';
	$xml_output .= ($mime_type != '' ) ? ' type="'. $mime_type .'" ' : '';
	$xml_output .= ($fileSize > 0) ? ' fileSize="'. $fileSize .'" ' : '';
	$xml_output .= '>';
	xmlnl();
	
	if ($player_url != '')
	{
		$xml_output .= '<media:player url="'. str_replace('&', '&amp;', $player_url . $flashvars) .'" height="'. _PLAYER_H_EMBED .'" width="'. _PLAYER_W_EMBED .'" />';
		xmlnl();
	}
	
	$xml_output .= '<media:title>'. $title .'</media:title>';
	xmlnl();
	
	$xml_output .= '<media:description type="html">'. $desc .'</media:description>';
	xmlnl();
	
	$xml_output .= '<media:thumbnail url="'. $thumb_url .'" ';
	$xml_output .= ($thumb_w > 0) ? ' width="'. $thumb_w .'" ' : '';
	$xml_output .= ($thumb_h > 0) ? ' height="'. $thumb_h .'" ' : '';
	$xml_output .= '/>';
	xmlnl();
	
	if ($media_keywords)
	{
		$tags_str = '';
		$tags = (array) get_video_tags($item['uniq_id'], 0);
		
		$count = 0;
		foreach ($tags as $t)
		{
			$tags_str .= clean_feed($t['tag']).',';
			$count++;
			if ($count == 10)
				break;
		}
		$tags_str = substr($tags_str, 0, -1);
		
		if ($tags_str != '')
		{
			$xml_output .= '<media:keywords>'. clean_feed($tags_str) .'</media:keywords>';
			xmlnl();
		}
	}
	
	
	if ($media_category)
	{
		$categories = load_categories();
	
		$long_cat = '';
		$parent =  0;
		$c = explode(',', $item['category']);
		
		if (count($c) > 0)
		{
			foreach ($c as $k => $c_id)
			{
				$long_cat = $categories[$c_id]['name'];
				$parent =  $categories[$c_id]['parent_id'];
				while ($parent != 0)
				{
					if ($long_cat == '')
					{
						$long_cat = $categories[$parent]['name'];
					}
					else
					{
						$long_cat = $categories[$parent]['name'] .'/'. $long_cat;
					}
					
					$parent = $categories[$parent]['parent_id'];
				}
				
				$xml_output .= '<media:category label="'. clean_feed($categories[$c_id]['name'])  .'">'. clean_feed(strtolower($long_cat)) .'</media:category>';
				xmlnl();
			}
		}
	}
	
	if ($item['restricted'] == 1)
	{
		$xml_output .= '<media:restriction type="sharing" relationship="deny" />';
		xmlnl();
	}
	
	$xml_output .= '</media:content>';
	xmlnl();
	
	if ($item_pubDate)
	{
		$xml_output .= '<pubDate>'. $pubDate .'</pubDate>';
		xmlnl();
	}
	
	$xml_output .= '</item>';
	xmlnl();
	
	return;
}

function video_sitemap_footer() 
{
	global $xml_output;
	
	xmlnl();
	$xml_output .= '</channel>';
	xmlnl();
	$xml_output .= '</rss>';
	
	return;
}

function sitemap_ping_service($sitemap_url, $service = 'google')
{
	global $config;
	
	if (empty($sitemap_url) || $config['disable_indexing'] == 1)
		return;
	
	switch ($service)
	{
		case 'google':
			
			$service_url = 'http://www.google.com/webmasters/tools/ping?sitemap='. urlencode($sitemap_url);
			
		break;
		
		case 'bing':
			
			$service_url = 'http://www.bing.com/ping?sitemap='. urlencode($sitemap_url);
			 
		break;
		
		default:
			return false;
		break;
	}
	
	$headers = false;

	if (function_exists('curl_init')) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $service_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		
		$data = curl_exec($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if ($curl_error != '')
		{
			$error = true;
		}
		else
		{
			$headers = explode("\n", $data);
		}
	}
	else if (ini_get('allow_url_fopen') == 1) 
	{
		$fp = @fopen($service_url, 'r');
		if ( ! $fp)
		{
			$error = true;
		}
		else
		{
			$data = @stream_get_meta_data($fp);
			$headers = $data['wrapper_data'];
		}
		@fclose($fp);
	}
	
	if (is_array($headers) && strpos($headers[0], '200') !== false)
	{
		return true;
	}
	
	return false;
}

/*
 * END local functions
 */

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
$sitemap_type = (strtolower($_GET['type']) == 'video-sitemap') ? 'video-sitemap' : 'sitemap';

// Handle AJAX requests - START 
if ($_GET['do'] == 'map')
{
	session_start();
	require_once('../config.php');
	include_once(ABSPATH .'include/functions.php');
	include_once(ABSPATH .'include/user_functions.php');
	include_once(ABSPATH .'include/islogged.php');
	include_once(ABSPATH . _ADMIN_FOLDER .'/functions.php');
	
	//$time_now = time();
	$time_now = (doing_cron() && (int) $_GET['sql_added_time_limit'] > 0) ? $_GET['sql_added_time_limit'] : time();
	
	$ajax_state = '';
	$default_options = array('limit' => 50000,
							 'media_keywords' => false,
							 'media_category' => false,
							 'item_pubDate' => false,
							);
	if ( ! doing_cron())
	{
		if ( ! $logged_in || ! is_admin())
		{
			$ajax_state = 'error';
			$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.';
			
			$ajax_response = array('state' => $ajax_state,
								   'msg' => $ajax_msg,
								   'html' => pm_alert_error($ajax_msg, false, true)
								  );
			exit(json_encode($ajax_response));
		}
	}
	
	$ajax_state = 'init';
	$ping_google = (strtolower($_GET['ping_google']) == 'yes' || strtolower($_POST['ping_google']) == 'yes') ? true : false;
	$ping_bing = (strtolower($_GET['ping_bing']) == 'yes' || strtolower($_POST['ping_bing']) == 'yes') ? true : false;
	$sitemap_basepath = ABSPATH . _UPFOLDER .'/'; // with trailing slash
	$file_res = false;
	$sql_limit = 500;	//	sql limit per iteration 
	$items_per_file = (int) $_GET['limit']; // max. items to process per file 
	$sql_start = (int) $_GET['start'];
	//$sql_start--;
	$items_processed = (int) $_GET['c']; 
	
	// assess total number of items we will have;
	if ((int) $_GET['totalitems'] == 0 || doing_cron())
	{
		if ( ! doing_cron())
		{
			// save form options
			$options = array(
				'ping_google' => ($ping_google) ? 'yes' : 'no',
				'ping_bing' => ($ping_bing) ? 'yes' : 'no',
				'limit' => (int) $_GET['limit']
			);
			
			sitemap_save_options($options);
		}
		
		// To avoid infinite loops, the published_videos (and published_articles) need to be as fresh as possible.
		$sql = "SELECT COUNT(*) as total 
				FROM pm_videos
				WHERE added <= $time_now";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ((int) $row['total'] != (int) $config['published_videos'] && ! doing_cron())
		{
			update_config('published_videos', $row['total'], true);
		}
		
		if (doing_cron())
		{
			// overwrite $config value but don't update_config()
			$config['published_videos'] = $row['total'];
		}
		
		if (_MOD_ARTICLES)
		{
			$sql = "SELECT COUNT(*) as total 
					FROM art_articles
					WHERE date <= $time_now
					  AND status = '1'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			if ((int) $row['total'] != (int) $config['published_articles'] && ! doing_cron())
			{
				update_config('published_articles', $row['total'], true);
			}
			
			if (doing_cron())
			{
				// overwrite $config value but don't update_config()
				$config['published_articles'] = $row['total'];
			}
		}
		
		if ($sitemap_type == 'video-sitemap')
		{
			$total_items = (int) $config['published_videos'];
		}
		else
		{
			$total_items = (int) $config['published_videos'];
			
			if (_MOD_ARTICLE)
			{
				$total_items += (int) $config['published_articles'];
			}
		}
	}
	else
	{
		$total_items = (int) $_GET['totalitems'];
	}
	
	if ($items_per_file > $total_items)
		$items_per_file = $total_items;

	if ($sql_start < 0)
		$sql_start = 0;

	$total_files = ($total_items > 0) ? ceil($total_items / $items_per_file) : 1;
	//$file_index = ceil(($sql_start + $sql_limit) / $items_per_file);
	$file_index = ceil(($items_processed + $sql_limit) / $items_per_file);
	
	if ($file_index <= 0)
		$file_index = 1;

	if ($file_index > $total_files)
		$file_index = $total_files;

	$bof = false; // beginning of file
	if ($items_processed == 0 || (($file_index * $items_per_file) - $sql_start) == $items_per_file)
		$bof = true;
  
	$file_ext = (doing_cron()) ? '.xml.tmp' : '.xml';
	
	if ($items_processed == 0)
	{
		$files = array();
		if ($sitemap_type == 'video-sitemap')
		{
			$files[] = ABSPATH . 'video-sitemap-index'. $file_ext;
		}
		else
		{
			$files[] = ABSPATH . 'sitemap-index'. $file_ext;
			$files[] = $sitemap_basepath . 'sitemap-base'. $file_ext;
		}
		
		for ($i = 1; $i <= $total_files; $i++)
		{
			if ($sitemap_type == 'video-sitemap')
			{
				$files[] = $sitemap_basepath . 'video-sitemap-'. $i . $file_ext;
			}
			else
			{
				$files[] = $sitemap_basepath . 'sitemap-'. $i . $file_ext;
			}
		}
		
		foreach ($files as $k => $file_path)
		{
			if ( ! file_exists($file_path))
			{
				// try creating it
				$file_res = @fopen($file_path, 'w');
				
				if ( ! $file_res)
				{
					$ajax_state = 'error';
					$ajax_msg = 'Error: Could not create file <code>'. $file_path .'</code>. Please create and upload it manually to the <code>uploads</code> directory and set writing permissions (CHMOD 0777).';
					
					exit(json_encode(array('state' => $ajax_state,
										   'msg' => $ajax_msg,
										   'html' => pm_alert_error($ajax_msg, false, true)
										  )));
				}
				else
				{
					fclose($file_res);
				}
			}
			else if ( ! is_writable($file_path))
			{
				$ajax_state = 'error';
				$ajax_msg = 'Error: file <code>'. $file_path .'</code> is not writable. Please set writing permissions (CHMOD 0777) to this file and try again.';
				
				exit(json_encode(array('state' => $ajax_state,
									   'msg' => $ajax_msg,
									   'html' => pm_alert_error($ajax_msg, false, true)
									  )));
			}
		}
	}

	switch ($sitemap_type)
	{
		case 'video-sitemap':
		
			$options = array('media_keywords' => (isset($_GET['tags']) && $_GET['tags'] == '1') ? true : false,
							 'media_category' => (isset($_GET['cats']) && $_GET['cats'] == '1') ? true : false,
							 'item_pubDate' => (isset($_GET['pub']) && $_GET['pub'] == '1') ? true : false
							);
			
			$video_sources = fetch_video_sources();
			$xml_output = '';

			$filename = 'video-sitemap-'. $file_index . $file_ext;
			$prev_filename = 'video-sitemap-'. ($file_index - 1) . $file_ext;
			
			if ($bof)
			{
				$file_res = fopen($sitemap_basepath . $filename, 'w');
				video_sitemap_header(array('link_url' => _URL .'/'. _UPFOLDER .'/'. $filename));
				fwrite($file_res, $xml_output);
					
				if ($file_index > 1)
				{
					$prev_file_res = fopen($sitemap_basepath . $prev_filename, 'a');
					$xml_output = '';
					video_sitemap_footer();
					fwrite($prev_file_res, $xml_output);
					fclose($prev_file_res);
					
					unset($xml_output, $prev_file_res, $prev_filename);
				}
			}
			else
			{
				$file_res = fopen($sitemap_basepath .  $filename, 'a');
			}
			
			$ajax_state = 'processing';
			
			//if ($items_processed >= $items_per_file)
			if ($items_processed >= $total_items)
			{
				$xml_output = '';
				video_sitemap_footer();
				fwrite($file_res, $xml_output);
		
				$ajax_state = 'finished';
			}
			
			//if ($items_processed < $items_per_file)
			if ($items_processed < $total_items)
			{
				$sql = "SELECT pm_videos.*, pm_videos_urls.mp4, pm_videos_urls.direct 
						FROM pm_videos 
						LEFT JOIN pm_videos_urls 
							 ON (pm_videos.uniq_id = pm_videos_urls.uniq_id)
						WHERE added <= $time_now  
						ORDER BY added DESC 
						LIMIT $sql_start, $sql_limit";
		
				$result = mysql_query($sql);

				if ( ! $result)
				{
					$ajax_state = 'error';
					$ajax_msg = 'There was an error while generating sitemap. <br /><strong>MySQL returned:</strong> '. mysql_error();
					$ajax_msg .= '<br />In file: <strong>'. __FILE__ .'</strong> line <strong>'. __LINE__ .'</strong>';
				}
				else
				{
					while ($row = mysql_fetch_assoc($result))
					{
						$xml_output = '';
					
						video_sitemap_item($row, $options);
						fwrite($file_res, $xml_output);
						
						$items_processed++;
					}
				}
			}

			switch ($ajax_state)
			{
				default:
				case 'init':
				case 'processing':
					
					$ajax_response = array('state' => $ajax_state,
										   'start' => $sql_start + $sql_limit,
										   'limit' => $items_per_file,
										   'progress' => round(($items_processed * 100) / $total_items, 2),
										   'c' => $items_processed,
										   'totalitems' => $total_items,
										   'msg' => '',
										   'html' => ''
										  );
	
				break;
				
				case 'finished':
				
					$options[$sitemap_type .'_last_build'] = time();
					$options['total_videos'] = (int) $config['published_videos'];
					
					sitemap_save_options($options);
					
					// create sitemap index file.
					$xml_output = '';
					$index_filename = 'video-sitemap-index'. $file_ext;
					$index_file_res = @fopen(ABSPATH . $index_filename, 'w');

					create_sitemap_index(array('total_files' => $total_files, 'sitemap_type' => $sitemap_type));
					
					@fwrite($index_file_res, $xml_output);
					@fclose($index_file_res);
					
					// delete older, extra files (above the current $total_files index).
					$i = $total_files + 1;
					while (file_exists( $sitemap_basepath . 'video-sitemap-'. $i .'.xml'))
					{
						@unlink($sitemap_basepath . 'video-sitemap-'. $i .'.xml');
						$i++;
						
						if ( $i > $total_files + 20)
						{
							break;
						}
					}
					
					$ajax_msg = pm_alert_success('Sitemap successfully created. Sitemap URL: <code>'. _URL .'/'. $index_filename .'</code>.', false, true);
					
					if ($ping_google)
					{
						$ping_google = sitemap_ping_service(_URL .'/'. $index_filename, 'google');
						
						if ($ping_google)
						{
							$ajax_msg .= pm_alert_success('Google.com successfully pinged.', false, true);
						}
						else
						{
							$ajax_msg .= pm_alert_error('An error occurred while pinging Google.com. Please submit the sitemap URL manually from your Google Webmasters Tools dashboard.', false, true);
						}
					}
					
					if ($ping_bing)
					{
						$ping_bing = sitemap_ping_service(_URL .'/'. $index_filename, 'bing');
						
						if ($ping_bing)
						{
							$ajax_msg .= pm_alert_success('Bing.com successfully pinged.', false, true);
						}
						else
						{
							$ajax_msg .= pm_alert_error('An error occurred while pinging Bing.com. Please submit the sitemap URL manually from your Bing Webmasters Tools dashboard.', false, true);
						}
					}
					
					$ajax_response = array('state' => $ajax_state,
										   'start' => $total_items,
										   'limit' => $items_per_file,
										   'progress' => 100,
										   'c' => $items_processed,
										   'totalitems' => $total_items,
										   'total_files_created' => $total_files,
										   'msg' => strip_tags($ajax_msg),
										   'html' => $ajax_msg
										  );	
				break;
				
				case 'error':

					$ajax_response = array('state' => $ajax_state,
										   'start' => $sql_start,
										   'limit' => $items_per_file,
										   'progress' => round(($items_processed * 100) / $total_items, 2),
										   'c' => $items_processed,
										   'totalitems' => $total_items,
										   'total_files_created' => $total_files,
										   'msg' => $ajax_msg,
										   'html' => pm_alert_error($ajax_msg, false, true)
										  );
				break;
			}
			
			if ($file_res)
			{
				fclose($file_res);
			}
			
			echo json_encode($ajax_response);
			
		break;
		
		case 'sitemap':
			
			$item_options = array('lastmod' => $time_now,
								  'changefreq' => 'weekly'
								 );
			
			// create the base sitemap first (base URLs, categories, pages): sitemap-base.xml
			if ($items_processed == 0)
			{
				$file_res = @fopen($sitemap_basepath . 'sitemap-base'. $file_ext, 'w');
				
				$xml_output = '';
				
				sitemap_header();
				//fwrite($file_res, $xml_output);
				
				//$xml_output = '';
				
				$base_urls = array( _URL .'/index.'. _FEXT,
									_URL .'/newvideos.'. _FEXT,
									_URL .'/topvideos.'. _FEXT,
									_URL .'/register.'. _FEXT,
								  ); 
				foreach ($base_urls as $k => $url)
				{
					//$xml_output = '';
					sitemap_item($url, array('lastmod' => $time_now));
					//fwrite($file_res, $xml_output);
				}
				
				$categories = load_categories();
				if (is_array($categories) && count($categories) > 0)
				{
					foreach ($categories as $c_id => $category)
					{
						if (_SEOMOD)
						{
							$url = _URL .'/browse-'. $category['tag'] .'-videos-1-date.html';
						}
						else
						{
							$url = _URL .'/category.php?cat='. $category['tag'];
						}
						
						//$xml_output = '';
						sitemap_item($url, $item_options);
						//fwrite($file_res, $xml_output);
					}
				}
				
				if (_MOD_ARTICLE)
				{
					$categories = load_categories(array('db_table' => 'art_categories'));
					if (is_array($categories) && count($categories) > 0)
					{
						foreach ($categories as $c_id => $category)
						{
							$url = art_make_link('category', array('id' => $category['id'], 'tag' => $category['tag']));

							sitemap_item($url, $item_options);
						}
					}
				}
				
				unset($categories);
				
				fwrite($file_res, $xml_output);
				
				$xml_output = '';
				
				// Pages
				if ( (int) $config['total_pages'] > 0)
				{
					if ( ! function_exists('make_page_link'))
					{
						include_once(ABSPATH .'include/page_functions.php');
					}
					
					$sql = "SELECT id, page_name 
							FROM pm_pages 
							WHERE status = '1' 
							  AND page_name != '404'
							ORDER BY id ASC";
					if ($result = mysql_query($sql))
					{
						while ($row = mysql_fetch_assoc($result))
						{
							$url = make_page_link($row);
							
							sitemap_item($url);
						}
						mysql_free_result($result);
					}
				}

				sitemap_footer();
				fwrite($file_res, $xml_output);
				fclose($file_res);
			}
			
			$xml_output = '';
			$file_res = false;
			
			$filename = 'sitemap-'. $file_index . $file_ext;
			$prev_filename = 'sitemap-'. ($file_index - 1) . $file_ext;
			
			if ($bof)
			{
				$file_res = fopen($sitemap_basepath . $filename, 'w');
				sitemap_header(array('link_url' => _URL .'/'. _UPFOLDER .'/'. $filename));
				fwrite($file_res, $xml_output);

				if ($file_index > 1)
				{
					$prev_file_res = fopen($sitemap_basepath . $prev_filename, 'a');
					$xml_output = '';
					sitemap_footer();
					fwrite($prev_file_res, $xml_output);
					fclose($prev_file_res);
					
					unset($xml_output, $prev_file_res, $prev_filename);
				}
			}
			else
			{
				$file_res = fopen($sitemap_basepath .  $filename, 'a');
			}
			
			$ajax_state = 'processing';
			
			if ($items_processed >= $total_items)
			{
				$xml_output = '';
				sitemap_footer();
				fwrite($file_res, $xml_output); 
		
				$ajax_state = 'finished';
			}
			
			if ($items_processed < $total_items)
			{
				if (_MOD_ARTICLES && ($items_processed >= $config['published_videos']))
				{
					if ( ! function_exists('art_make_link'))
					{
						include(ABSPATH .'include/article_functions.php');
					}
					
					if ($items_processed == $config['published_videos'])
					{
						$sql_start = 0;
					}
					
					$sql = "SELECT id, title, date, article_slug 
							FROM art_articles
							WHERE date <= $time_now
							  AND status = '1'
							ORDER BY date DESC 
							LIMIT $sql_start, $sql_limit";
					
					$result = mysql_query($sql);
					
					if ( ! $result)
					{
						$ajax_state = 'error';
						$ajax_msg = 'There was an error while generating sitemap. <br /><strong>MySQL returned:</strong> '. mysql_error();
						$ajax_msg .= '<br />In file: <strong>'. __FILE__ .'</strong> line <strong>'. __LINE__ .'</strong>';
					}
					else
					{
						while ($row = mysql_fetch_assoc($result))
						{
							$xml_output = $url = '';
						
							$url = art_make_link('article', $row);
							
							sitemap_item($url, $item_options);
							fwrite($file_res, $xml_output);
							
							$items_processed++;
						}
					}
				}
				else
				{
					$sql = "SELECT uniq_id, video_title, video_slug  
							FROM pm_videos 
							WHERE added <= $time_now  
							ORDER BY added DESC 
							LIMIT $sql_start, $sql_limit";
		
					$result = mysql_query($sql);
					if ( ! $result)
					{
						$ajax_state = 'error';
						$ajax_msg = 'There was an error while generating sitemap. <br /><strong>MySQL returned:</strong> '. mysql_error();
						$ajax_msg .= '<br />In file: <strong>'. __FILE__ .'</strong> line <strong>'. __LINE__ .'</strong>';
					}
					else
					{
						while ($row = mysql_fetch_assoc($result))
						{
							$xml_output = $url = '';
							
							$url = makevideolink($row['uniq_id'], $row['video_title'], $row['video_slug']);
								
							sitemap_item($url, $item_options);
							fwrite($file_res, $xml_output);
							$items_processed++;
						}
					}
				}
			}
			
			switch ($ajax_state)
			{
				default:
				case 'init':
				case 'processing':
					
					$ajax_response = array('state' => $ajax_state,
										   'start' => $sql_start + $sql_limit,
										   'limit' => $items_per_file,
										   'progress' => round(($items_processed * 100) / $total_items, 2),
										   'c' => $items_processed,
										   'totalitems' => $total_items,
										   'msg' => '',
										   'html' => ''
										  );
	
				break;
				
				case 'finished':
				
					$options[$sitemap_type .'_last_build'] = time();
					$options['total_videos'] = (int) $config['published_videos'];
					
					sitemap_save_options($options); 
					
					// create sitemap index file.
					$xml_output = '';
					$index_filename = 'sitemap-index'. $file_ext;
					$index_file_res = @fopen(ABSPATH . $index_filename, 'w');

					create_sitemap_index(array('total_files' => $total_files, 'sitemap_type' => $sitemap_type));
					
					@fwrite($index_file_res, $xml_output);
					@fclose($index_file_res);
					
					// delete older, extra files (above the current $total_files index).
					$i = $total_files + 1;
					while (file_exists( $sitemap_basepath . 'sitemap-'. $i .'.xml'))
					{
						@unlink($sitemap_basepath . 'sitemap-'. $i .'.xml');
						$i++;
						
						if ( $i > $total_files + 20)
						{
							break;
						}
					}
					
					$ajax_msg = pm_alert_success('The sitemap index file is now available at <code>'. _URL .'/'. $index_filename .'</code>.', false, true);
					
					if ($ping_google)
					{
						$ping_google = sitemap_ping_service(_URL .'/'. $index_filename, 'google');
						
						if ($ping_google)
						{
							$ajax_msg .= pm_alert_success('Google.com successfully pinged.', false, true);
						}
						else
						{
							$ajax_msg .= pm_alert_error('An error occurred while pinging Google.com. Please submit the sitemap URL manually from your Google Webmasters Tools dashboard.', false, true);
						}
					}
					
					if ($ping_bing)
					{
						$ping_bing = sitemap_ping_service(_URL .'/'. $index_filename, 'bing');
						
						if ($ping_bing)
						{
							$ajax_msg .= pm_alert_success('Bing.com successfully pinged.', false, true);
						}
						else
						{
							$ajax_msg .= pm_alert_error('An error occurred while pinging Bing.com. Please submit the sitemap URL manually from your Bing Webmasters Tools dashboard.', false, true);
						}
					}
					
					$ajax_response = array('state' => $ajax_state,
										   'start' => $total_items,
										   'limit' => $items_per_file,
										   'progress' => 100,
										   'c' => $items_processed,
										   'totalitems' => $total_items,
										   'total_files_created' => $total_files,
										   'msg' => strip_tags($ajax_msg),
										   'html' => $ajax_msg
										  );	
				break;
				
				case 'error':

					$ajax_response = array('state' => $ajax_state,
										   'start' => $sql_start,
										   'limit' => $items_per_file,
										   'progress' => round(($items_processed * 100) / $total_items, 2),
										   'c' => $items_processed,
										   'totalitems' => $total_items,
										   'total_files_created' => $total_files,
										   'msg' => $ajax_msg,
										   'html' => pm_alert_error($ajax_msg, false, true)
										  );
				break;
			}
			
			if ($file_res)
			{
				fclose($file_res);
			}
			
			echo json_encode($ajax_response);
			
		break;
	}
	
	
	exit();
}
// Handle AJAX requests - END 

$showm = '8';
$load_scrolltofixed = 1;
$load_jquery_ui = 1;
$_page_title = ($sitemap_type == 'video-sitemap') ? 'Create video sitemap' : 'Create sitemap';
include('header.php');

?>
<script type="text/javascript">
	
	function build_map(start, limit, params, html_output_sel)
	{
		$( "#progressbar" ).show();
		
		$.ajax({
			url: 'sitemap.php',
			data: 'type=' + $('input[name="sitemap_type"]').val() +
				  '&do=map' + 
				  '&start='+ start +
				  '&limit='+ limit +
				  '&tags='+ params.tags +
				  '&cats='+ params.cats +
				  '&pub='+ params.pub +
				  '&progress=' + params.progress +
				  '&c=' + params.c +
				  '&totalitems='+ params.totalitems +
				  '&ping_google='+ $('input:radio[name="ping_google"]:checked').val() +
				  '&ping_bing='+ $('input:radio[name="ping_bing"]:checked').val(),
			success: function(data){
						
						$('.bar').css({'width': data['progress'] + "%"});
						$('.bar').html(data['progress'] + "%");
												
						switch (data['state'])
						{
							case 'processing':
								$( "#progressbar" ).show();
								//$( "#progressbar" ).progressbar({value: data['progress'] }).append(data['progress']);
								params.progress = data['progress'];
								params.c = data['c'];
								params.totalitems = data['totalitems'];

								build_map(data['start'], data['limit'], params, html_output_sel);
								
							break;
							
							case 'finished':
								$('#build-map-button').attr('disabled', false);
							case 'error':
								if (data['state'] == 'finished') {
									$( "#progressbar" ).hide();
									$('#ajax-response').html(data['html']);
									$('#more_v_details').hide();
								} else {
									//$( "#progressbar" ).progressbar({value: data['progress'] });
									$('#ajax-response').html(data['html']);
								}
								$('#build-map-button').attr('disabled', false);
							break;
						}
					},
			dataType: 'json'
		});
	}

	$(document).ready(function(){
		$('#build-map-button').click(function(){
			
			var start, limit;
			var tags, cats, pub, file, fileindex;
			var params = new Array();
			
			tags = ($("input[name='media_keywords']").attr('checked')) ? '1' : '0';
			cats = ($("input[name='media_category']").attr('checked')) ? '1' : '0';
			pub = ($("input[name='item_pubDate']").attr('checked')) ? '1' : '0';
			file = $("select[name='file']").val();
			
			limit = parseInt($("input[name='limit']").val());

			params['tags'] = tags;
			params['cats'] = cats;
			params['pub'] = pub;
			params['progress'] = 0;
			params['c'] = 0;
			params['totalitems'] = '';
				
			$(this).attr('disabled', true);
			build_map(0, limit, params, '#ajax-response');
			
			return false;
		});
	});
</script>
<div id="adminPrimary">

	<div class="row-fluid" id="help-assist">
		<div class="span12">
		<div class="tabbable tabs-left">
		  <ul class="nav nav-tabs">
			<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
			<li><a href="#help-resources" data-toggle="tab">Resources</a></li>
		  </ul>
		  <div class="tab-content">
			<div class="tab-pane fade in active" id="help-overview">
			<p><strong>PHP Melody</strong> can create two types for sitemaps for your website: a <strong>regular sitemap</strong> and a <strong>video sitemap</strong>.</p>
			<p>The regular sitemaps will include all of your website's URLs: videos, categories, articles, pages, etc.</p>
			<p>The video sitemaps help search engines crawl and index only your videos. Once indexed, your videos will include in the search engine results a video thumbnail. Displaying an thumbnail has been shown to increase the Click-Through-Rate and should help your site get more traffic.</p>
			<p>Google allows a maximum of 50,000 entries per sitemap file. As as result we've tweaked PHP Melody to work by creating an index sitemap. This way, even if your site has more than 50,000 entries you'll only have to submit and work with only one sitemap file.</p>
			</div>

			<div class="tab-pane fade in" id="help-resources">
			<p><strong>Submit your sitemap</strong> to <a href="https://www.google.com/webmasters/tools/home?hl=en" target="_blank">Google Webmaster Tools</a>.</p>
			<p><strong>Submit your sitemap</strong> to <a href="http://www.bing.com/toolbox/webmaster" target="_blank">Bing Webmaster Tools</a>.</p>
			<p>Google Video Sitemap guidelines: <a href="http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472" target="_blank">http://support.google.com/webmasters/bin/answer.py?hl=en&amp;answer=80472</a></p>
			<p>Bing Video Sitemap guidelines: <a href="http://www.bing.com/webmaster/help/how-to-submit-sitemaps-82a15bd4" target="_blank">http://www.bing.com/webmaster/help/how-to-submit-sitemaps-82a15bd4</a></p>
			</div>
		  </div>
		</div> <!-- /tabbable -->
		</div><!-- .span12 -->
	</div><!-- /help-assist -->
	<div class="content">
	<a href="#" id="show-help-assist">Help</a>

<?php
$sitemap_basepath = ABSPATH . _UPFOLDER .'/'; 

$form_options = array(
					'limit' => 50000,
					'media_keywords' => false,
					'media_category' => false,
					'item_pubDate' => false,
					'ping_google' => 'no',
					'ping_bing' => 'no'
					);

$last_options_used = sitemap_load_options();

$form_options = array_merge($form_options, $last_options_used);

if ($sitemap_type == 'video-sitemap')
{
	$form_options['max_limit'] = $config['published_videos'];
}
else
{
	$form_options['max_limit'] = ($config['published_videos'] + $config['total_pages'] + $config['published_articles'] + 4);
}

//if ($config['published_videos'] < 50000)
//{
//	$form_options['limit'] = $form_options['max_limit'];
//}

?>

<h2><?php echo ($sitemap_type == 'video-sitemap') ? 'Create Video Sitemap' : 'Create Sitemap'; ?></h2>

<div id="ajax-response"></div>

<?php
if ($form_options[$sitemap_type .'_last_build'] > 0) 
{
	if ($sitemap_type == 'video-sitemap')
	{
		$sitemap_location = _URL .'/video-sitemap-index.xml';
	}
	else
	{
		$sitemap_location = _URL .'/sitemap-index.xml';
	}
}

if ($config['disable_indexing'] == 1)
{
	echo pm_alert_warning('The current settings are set to discourage search engines from indexing '. _URL .'. <br />To allow search engines to index your site, visit <a href="settings.php">Settings / General Settings</a>.');
}

// reminder about live automated jobs 
include(ABSPATH .'/include/cron_functions.php');
$job = @get_cron_job_by_type($sitemap_type);

if ($job && $job['status'] == CRON_STATUS_LIVE)
{
	if ($job['state'] == CRON_STATE_BUSY 
		|| ($job['state'] == CRON_STATE_READY && $job['data']['time_started'] > 0)) // job in progress
	{
		// copied message from admin-ajax.php (View Log): 
		$warning_msg = 'The sitemap is currently updated by an <a href="automated-jobs.php">Automated Job</a>.<br />Progress: '. pm_number_format($job['data']['videos_processed']) .'';
		$warning_msg .= ($job['data']['progress'] > 0) ? '('. round($job['data']['progress'], ($job['data']['progress'] >= 1) ? 0 : 2) .'%)' : ''; 
		$warning_msg .= ' videos processed so far.';
		
		echo pm_alert_warning($warning_msg);
	}
	else
	{
		echo pm_alert_info('Reminder: There is an <a href="automated-jobs.php">automated job</a> scheduled to run '. cron_frequency_sec_to_lang($job['exec_frequency']) .' for this sitemap. Sitemaps will be automatically created for you.');
	}
}
?>

<div class="t1">
<form action="sitemap.php" method="post">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	<?php if ( !empty ($sitemap_location)) : ?>
		<tr class="">
			<td width="20%">Sitemap URL</td>
			<td>
				<code class="text-success"><?php echo $sitemap_location; ?></code>
				<br />
				<small>
				Most recent update: <strong><?php echo date('F j, Y g:i a', $form_options[$sitemap_type .'_last_build']); ?></strong>
				<?php if ($job && $job['status'] == CRON_STATUS_LIVE)
				{
					echo '<br />This sitemap will be updated automatically. Visit <a href="automated-jobs.php">Automated Jobs</a> for more details.';
				}
				?>
				</small>
			</td>
		</tr>
	<?php endif; ?>
	<tr class="table_row1">
		<?php if ($sitemap_type == 'video-sitemap') : ?>
		<td width="20%">Videos per sitemap</td>
		<?php else : ?>
		<td width="20%">URLs per sitemap</td>
		<?php endif; ?>
		<td>
			<input type="text" name="limit" size="9" value="<?php echo $form_options['limit']; ?>" />
			<input type="hidden" name="max-limit" value="<?php echo (int) $form_options['max_limit']; ?>" />
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit the number of videos for each sitemap.<br /><strong>Maximum value</strong>: 50,000"><i class="icon-info-sign"></i></a>
		</td>
	</tr>

	<?php if ($sitemap_type == 'video-sitemap') : ?>
	<tr class="table_row1">
		<td width="20%" valign="top">Optional data</td>
		<td>
			<label>
				<input type="checkbox" name="media_keywords" value="1" <?php echo ($form_options['media_keywords']) ? 'checked="checked"' : '';?> /> Include <code>&lt;media:keywords&gt;</code> 
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="The video's tags will be used to fill the <code>media:keywords</code> element."><i class="icon-info-sign"></i></a>
			</label>
			<br /> 
			<label>
				<input type="checkbox" name="media_category" value="1" <?php echo ($form_options['media_category']) ? 'checked="checked"' : '';?> /> Include <code>&lt;media:category&gt;</code> 
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="The video's categories will be used to fill the <code>media:category</code> element."><i class="icon-info-sign"></i></a>
			</label>
			<br />
			<label>
				<input type="checkbox" name="item_pubDate" value="1" <?php echo ($form_options['item_pubDate']) ? 'checked="checked"' : '';?> /> Include <code>&lt;pubDate&gt;</code> 
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="<code>pubDate</code> refers to the date when the video was published."><i class="icon-info-sign"></i></a>
			</label>
		</td>
	</tr>
	<?php endif; ?>
	<tr class="table_row1">
		<td width="20%">Ping Google.com</td>
		<td>
			<label>
				<input type="radio" name="ping_google" value="yes" <?php echo ($form_options['ping_google'] == 'yes') ? 'checked="checked"' : ''; ?>> Yes
			</label>
			<label>
				<input type="radio" name="ping_google" value="no" <?php echo ($form_options['ping_google'] != 'yes') ? 'checked="checked"' : ''; ?>> No
			</label>
			
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Select 'Yes' if you want to automatically resubmit it when the sitemap has finished generating (works only if you have already submitted it via Google Webmasters Tools).<br /><br /><strong>Warning:</strong> pinging will not tell you if the submitted sitemap was validated or not."><i class="icon-info-sign"></i></a>
		</td>
	</tr>
	<input type="hidden" name="sitemap_type" value="<?php echo $sitemap_type; ?>" />
	<tr class="table_row1">
		<td width="20%">Ping Bing.com</td>
		<td>
			<label>
				<input type="radio" name="ping_bing" value="yes" <?php echo ($form_options['ping_bing'] == 'yes') ? 'checked="checked"' : ''; ?>> Yes
			</label>
			<label>
				<input type="radio" name="ping_bing" value="no" <?php echo ($form_options['ping_bing'] != 'yes') ? 'checked="checked"' : ''; ?>> No
			</label>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Select 'Yes' if you want to automatically resubmit it when the sitemap has finished generating.<br /><br /><strong>Warning:</strong> pinging will not tell you if the submitted sitemap was validated or not."><i class="icon-info-sign"></i></a>
		</td>
	</tr>
</table>

<div class="clearfix"></div>
<div style="width: 100%; height: 18px;" id="progressbar"  class="progress progress-success progress-striped active hide">
	<div class="bar" style="width: 0%;"></div>
</div>
<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
	<div class="btn-group">
		<button type="submit" name="submit" data-loading-text="Generating..." class="btn btn-small btn-success btn-strong" id="build-map-button">Create <?php echo ($sitemap_type == 'video-sitemap') ? 'video sitemap' : 'sitemap'; ?></button>
	</div>
</div>
</div><!-- #list-controls -->
</form>
</div>

	</div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');