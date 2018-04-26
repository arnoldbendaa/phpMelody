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

namespace phpmelody\sources\src_vk;

function get_info($url)
{
	$video_data = array();
	
	$target_url = $url;
	
	$error = 0;
	
	$headers = fetch_headers($target_url);
	
	if (function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$video_data = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if ($errormsg != '')
		{
			echo '<div class="alert alert-danger">'. $errormsg .'</div>';
			return false;
		}
		$video_data = explode("\n", $video_data);
		
		if (strpos($video_data[0], '404') !== false)
		{
			echo '<div class="alert alert-danger"><strong>404</strong> page <code>'. $target_url .'</code> not found.</div>';
			return false;
		}
		
		if (strpos($video_data[0], '403') !== false)
		{
			echo '<div class="alert alert-danger">Access to <code>'. $target_url .'</code> has been denied. Authentication may be required.</div>';
			return false;
		}
	}
	else if (ini_get('allow_url_fopen') == 1)
	{	
		$headers = fetch_headers($target_url);
		
		if (strpos($headers[0], '404') !== false)
		{
			echo '<div class="alert alert-danger"><strong>404</strong> page <code>'. $target_url .'</code> not found.</div>';
			return false;
		}
		
		if (strpos($headers[0], '403') !== false)
		{
			echo '<div class="alert alert-danger">Access to <code>'. $target_url .'</code> has been denied. Authentication may be required.</div>';
			return false; 
		}

		$video_data = file($target_url);
		
		if ($video_data === false)
		{
			echo '<div class="alert alert-danger">An error occurred while retrieving the requested page.</div>';
			return false;
		}
		if ( ! is_array($video_data) )
		{
			$video_data = explode("\n", $video_data);
		}
	}

	if ( ! function_exists('iconv'))
	{
		echo '<div class="alert alert-danger"><strong>Warning</strong>: function iconv() is required to decode <strong>windows-1251</strong> characters to <strong>utf-8</strong>.<br />Ask your hosting provider to install or enable the <a href="http://www.php.net/manual/en/book.iconv.php" target="_blank" title="PHP Manual - Iconv"><strong>iconv</strong></a> extension on your account.</div>';
	}
	
	$key_found = false; 
	
	$buff = array();
	foreach ($video_data as $k => $line)
	{
		if (strpos($line, '<meta') !== false || strpos($line, 'ajax.preload(') !== false)
		{
			if (function_exists('iconv'))
			{
				$line = iconv('windows-1251', 'utf-8', $line);
			}

			$line = trim($line);
			$line = stripslashes($line);
			$buff[] = $line;
			
			if (strpos($line, '"vtag"'))
			{
				$key_found = true;
			}
		}
	}
	
	if ( ! $key_found)
	{
		echo '<div class="alert alert-danger">Cannot embed videos that aren\'t hosted by the provider.</div>';
	}
	
	return $buff;
}

function fetch_headers($url)
{
	$headers = array();
	$url = trim($url);
	
	$error = 0;
	if(function_exists('get_headers'))
	{
		$url = str_replace(' ', '%20', $url);
		if( ! strstr($url, "http://"))
		{
			$url = "http://" . $url;
		}
		$headers = @get_headers($url, 0);
		if(!$headers)
		{
			$error = 1;
		}
	}
	
	if($error == 1 || function_exists('get_headers') === FALSE)
	{
		$error = 0;

		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_NOBODY ,1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
			$data = curl_exec($ch);
			$errormsg = curl_error($ch);
			curl_close($ch);
			
			if($errormsg != '')
			{
				echo '<div class="alert alert-error">'.$errormsg.'</div>';
				return false;
			}				
			$headers = explode("\n", $data);
		}
		else if(ini_get('allow_url_fopen') == 1)
		{
			$fp = @fopen($url, "r");
			if(!$fp)
				$error = 1;
			else
			{
				if(function_exists('stream_get_meta_data'))
				{
					$data = @stream_get_meta_data($fp);
					$headers = $data['wrapper_data'];
				}
				else
				{
					$headers = $http_response_header;
				}
			}
			@fclose($fp);
		}
	}
	return $headers;
}

function get_flv($video_data)
{
	global $config;
	
	$url = $hd_url = '';
	
	$arr_length = count($video_data);
	for ($i = 0; $i < $arr_length; $i++)
	{
		if (strpos($video_data[$i], 'ajax.preload') !== false)
		{
			//if (preg_match('/"vtag":"(.*?)"/', $video_data[$i], $matches))
			if (preg_match('/"url720":"(.*?)"/', $video_data[$i], $matches))
			{
				$hd_url = stripslashes($matches[1]);
			}
			
			if (preg_match('/"url480":"(.*?)"/', $video_data[$i], $matches))
			{
				$url = stripslashes($matches[1]);
				break;
			}
			
			if (preg_match('/"url360":"(.*?)"/', $video_data[$i], $matches))
			{
				$url = stripslashes($matches[1]);
				break;
			}
			
			if (preg_match('/"url240":"(.*?)"/', $video_data[$i], $matches))
			{
				$url = stripslashes($matches[1]);
				break;
			}
		}
	}
	
	if ($config['use_hq_vids'] == '1' && $hd_url != '')
	{
		return $hd_url;
	}
	
	return ($url == '') ? false : $url;
}

function get_thumb_link($video_data) 
{
	$url = '';
	
	$arr_length = count($video_data);
	for ($i = 0; $i < $arr_length; $i++)
	{
		if (strpos($video_data[$i], 'ajax.preload') !== false)
		{
			if (preg_match('/"vtag":"(.*?)"/', $video_data[$i], $matches))
			{
				
			}
		}
	}
	
	return false;
}


function video_details($video_data, $url, &$info) 
{
	$title = $thumb = $direct = $description = $swf_url = $duration = false;
	
	$arr_length = count($video_data);
	
	for ($i = 0; $i < $arr_length; $i++)
	{
		if ( ! $title)
		{
			if (strpos($video_data[$i], '"og:title"'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['video_title'] = $matches[1];
					$title = true;
				}
			}
		}
		if ( ! $thumb)
		{
			if (strpos($video_data[$i], '"og:image"'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['yt_thumb'] = $matches[1];
					$thumb = true;
				}
			}
			if (strpos($video_data[$i], '"videothumbnail"') || strpos($video_data[$i], '"image_src"'))
			{
				if (preg_match('/href="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['yt_thumb'] = $matches[1];
					$thumb = true;
				}
			}
		}
		if ( ! $direct)
		{
			if (strpos($video_data[$i], '"og:url"'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['direct'] = $matches[1];
					$direct = true;
				}
			}
		}
		if ( ! $description)
		{
			if (strpos($video_data[$i], '"og:description"'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['description'] = $matches[1];
					$description = true;
				}
			}
		}
		if ( ! $tags)
		{
			if (strpos($video_data[$i], 'name="keywords"'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['tags'] = $matches[1];
					$tags = true;
				}
			}
		}
		if ( ! $swf_url)
		{
			//http://vk.com/video?act=get_swf&oid=28908630&vid=165233143&embed_hash=8b4ddfd72ca8f5f3
			if (strpos($video_data[$i], '"og:video"'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$parts = parse_url($matches[1]);

					$info['yt_id'] = str_replace('act=get_swf&', '', $parts['query']);
					$info['yt_id'] = str_replace('embed_hash=', 'hash=', $info['yt_id']);
					$info['yt_id'] = str_replace('vid=', 'id=', $info['yt_id']);
					$swf_url = true;
					unset($parts);
				}
			}
		}
		if ( ! $duration)
		{
			if (strpos($video_data[$i], '"og:video:duration"'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['yt_length'] = (int) $matches[1];
					$duration = true;
				}
			}
		}
		
		if ($title && $thumb && $tags && $direct && $description && $swf_url && $duration)
		{
			break;
		}
	}

	$info['url_flv'] = get_flv($video_data);
	
	if ($info['direct'] != '')
	{
		$info['direct'] = $url;
	}
	
	return;
}

function download_thumb($thumbnail_link, $upload_path, $video_uniq_id, $overwrite_existing_file = false) {
	
	if (strpos($thumbnail_link, '//') === 0)
	{
		$thumbnail_link = 'http:'. $thumbnail_link;
	}
	
	if (strpos($thumbnail_link, 'http') !== 0)
	{
		$thumbnail_link = 'http://'. $thumbnail_link;
	}
	
	$last_ch = substr($upload_path, strlen($upload_path)-1, strlen($upload_path));
	if($last_ch != "/")
		$upload_path .= "/"; 

	$ext = ".jpg";
	
	$thumb_name = $video_uniq_id . "-1" . $ext;
	
	if(is_file( $upload_path . $thumb_name ) && ! $overwrite_existing_file) {
		return FALSE;
	}
	
	$error = 0;

	if ( function_exists('curl_init') ) 
	{

		$ch = curl_init();
		$timeout = 0;
		curl_setopt ($ch, CURLOPT_URL, $thumbnail_link);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		
		// Getting binary data
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$image = curl_exec($ch);
		curl_close($ch);
		
		//	create & save image;
		$img_res = @imagecreatefromstring($image);
		if($img_res === false)
			return FALSE;
		
		$img_width = imagesx($img_res);
		$img_height = imagesy($img_res);
		
		$resource = @imagecreatetruecolor($img_width, $img_height);
		
		if( function_exists('imageantialias'))
		{
			@imageantialias($resource, true); 
		}
		
		@imagecopyresampled($resource, $img_res, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height);
		@imagedestroy($img_res);
	
		switch($ext)
		{
			case ".gif":
				//GIF
				@imagegif($resource, $upload_path . $thumb_name);
			break;
			case ".jpg":
				//JPG
				@imagejpeg($resource, $upload_path . $thumb_name);
			break;  
			case ".png":
				//PNG
				@imagepng($resource, $upload_path . $thumb_name);
			break;
		}
	}
	else if( ini_get('allow_url_fopen') == 1 )
	{
		// try copying it... if it fails, go to backup method.
		if(!copy($thumbnail_link, $upload_path . $thumb_name ))
		{
			//	create a new image
			list($img_width, $img_height, $img_type, $img_attr) = @getimagesize($thumbnail_link);

			$image = '';

			switch($img_type)
			{
				case 1:
					//GIF
					$image = imagecreatefromgif($thumbnail_link);
					$ext = ".gif";
				break;
				case 2:
					//JPG
					$image = imagecreatefromjpeg($thumbnail_link);
					$ext = ".jpg";
				break;  
				case 3:
					//PNG
					$image = imagecreatefrompng($thumbnail_link);
					$ext = ".png";
				break;
			}
			
			$resource = @imagecreatetruecolor($img_width, $img_height);
			if( function_exists('imageantialias'))
			{
				@imageantialias($resource, true); 
			}
			
			@imagecopyresampled($resource, $image, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height);
			@imagedestroy($image);
		}
		
		$thumb_name = $video_uniq_id . "-1" . $ext;
		
		$img_type = 2;
		switch($img_type)
		{
			default:
			case 1:
				//GIF
				@imagegif($resource, $upload_path . $thumb_name);
			break;
			case 2:
				//JPG
				@imagejpeg($resource, $upload_path . $thumb_name);
			break;  
			case 3:
				//PNG
				@imagepng($resource, $upload_path . $thumb_name);
			break;
		}
		
		if($resource === '')
			$error = 1;
	} 

	return $upload_path . $thumb_name;
}
function do_main(&$video_details, $url)
{
	$video_data = get_info($url);
	if($video_data != false)
	{
		video_details($video_data, $url, $video_details);
	}
	else
	{
		$video_details = array();
	}
}