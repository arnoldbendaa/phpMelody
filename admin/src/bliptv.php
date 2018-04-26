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


namespace phpmelody\sources\src_bliptv;

function get_info($url)
{
	$video_data = array();
	
	if (strpos($url, 'blip.tv/file/') !== false)
	{
		//preg_match("/blip.tv\/file\/([^(\&|$)]*)/", $url, $matches);
		$headers = fetch_headers($url);
		$arr_length = count($headers);
		$link = $url2;
		for($i = 0; $i < $arr_length; $i++)
		{
			if(strstr($headers[$i], "ocation:"))
			{
				$str1 = explode("ocation:", $headers[$i]);
				$url = trim($str1[1]);
				break;
			}
		}
	}
	
	preg_match('/blip\.tv\/(.*?)\/([^(\&|$)]*)/', $url, $matches);
	
	$tmp_parts = explode('-', $matches[2]);
	$vid_id = array_pop($tmp_parts);
	
	if(strpos($vid_id, "/") !== false)
	{
		$tmp = explode("/", $vid_id);
		$vid_id = $tmp[0];
		unset($tmp);
	}
	unset($matches);
	
	$target_url = 'http://blip.tv/rss/minimal/'. $vid_id;
	
	$error = 0;
	
	if(function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$video_data = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if($errormsg != '')
		{
			echo $errormsg;
			return false;
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$video_data = @file_get_contents($target_url);
		if($video_data === false)
			$error = 1;
	}
	
	if ( ! is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	$total = count($video_data);
	for($i = 0; $i < $total; $i++)
	{
		$video_data[$i] = trim($video_data[$i]);
	}
	
	return $video_data;
}

function get_flv($video_data, $url)
{
	$arr_length = count($video_data);
	$allowed_ext = array('flv', 'mp4', 'mkv', 'm4v', '3gp', '3g2');
	
	for($i = 0; $i < $arr_length; $i++)
	{
		$ext = '';
		if (strpos($video_data[$i], '<media:content') !== false)
		{
			preg_match('/<media:content(.*)url="(.*?)"/', $video_data[$i], $matches);
			
			$tmp_parts = explode('.', $matches[2]);
			$ext = array_pop($tmp_parts);
			$ext = strtolower($ext);
			
			if (in_array($ext, $allowed_ext))
			{
				return $matches[2];
			}
		}
	}

	return '';
}

function get_thumb_link($video_data) 
{
	$arr_length = count($video_data);
	for($i = 0; $i < $arr_length; $i++)
	{
		if(preg_match('/<blip:smallThumbnail>(.*?)<\/blip/', $video_data[$i], $matches) != 0)
		{	
			return $matches[1];
		}
		
		if(preg_match('/<media:thumbnail url="(.*?)"/', $video_data[$i], $matches) != 0)
		{
			return $matches[1];
		}
	}
	return '';
}



function video_details($video_data, $url, &$info) 
{
	$arr_length = count($video_data);
	
	for($i = 0; $i < $arr_length; $i++)
	{
		//	description
		if ($info['description'] == '')
		{
			if(preg_match('/<blip:puredescription>(.*?)<\/blip:puredescription>/', $video_data[$i], $matches) != 0)
			{
				$info['description'] = $matches[1];
			}
		}
		
		//	video id
		if ($info['yt_id'] == '')
		{
			//if(preg_match('/<blip:item_id>(.*?)<\/blip:item_id>/', $video_data[$i], $matches) != 0)
			if(preg_match('/<blip:embedLookup>(.*?)</', $video_data[$i], $matches) != 0)
			{
				$info['yt_id'] = $matches[1];
			}
		}
		//	tags
		if ($info['tags'] == '')
		{
			if(preg_match('/<media:keywords>(.*?)<\/media:keywords>/', $video_data[$i], $matches) != 0)
			{
				$info['tags'] = $matches[1];
			}
		}
		
		//	mp4
		if ($info['mp4'] == '')
		{
			if(preg_match('/<media:content (.*?) type=\"video\/mp4\" url=\"(.*?)\"(.*?)>/', $video_data[$i], $matches) != 0)
			{
				$info['mp4'] = $matches[2];
			}
		}
		
		if ($info['video_title'] == '')
		{
			//	title
			if(preg_match('/<media:title>(.*?)<\/media:title>/', $video_data[$i], $matches) != 0)
			{
				$info['video_title'] = str_replace('"', '', $matches[1]);
			}
		}
		
		if ($info['direct'] == '')
		{
			if (preg_match('/<link>http:\/\/blip.tv\/(.*?)<\//', $video_data[$i], $matches) != 0)
			{
				$info['direct'] = 'http://blip.tv/'.$matches[1];
			}
		}
	}
	$info['url_flv'] = get_flv($video_data, $url);
	$info['yt_thumb'] = get_thumb_link($video_data);
	
	if ($info['direct'] == '')
	{
		$info['direct'] = 'http://blip.tv/file/'.$info['yt_id'];
	}	
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

?>