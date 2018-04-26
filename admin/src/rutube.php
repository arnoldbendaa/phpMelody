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

namespace phpmelody\sources\src_rutube;

function get_info($url)
{
	$video_data = array();
	
	preg_match('/rutube\.ru\/video\/(.*)([^(\/|$)]*)/', $url, $matches);
	
	$id = trim($matches[1], " /");

	$target_url = 'http://rutube.ru/api/video/'. $id .'/?format=json';

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
		
		if ($errormsg != '')
		{
			echo '<div class="alert alert-danger">Error: '. $errormsg .'</div>';
			return false;
		}
	}
	else if (ini_get('allow_url_fopen') == 1)
	{
		$video_data = file($target_url);
		
		if ($video_data === false)
		{
			echo '<div class="alert alert-danger">An error occurred while retrieving the requested page.</div>';
			return false;
		}
		
		if (is_array($video_data))
		{
			$video_data = $video_data[0];
		}
	}
	
	if (function_exists('iconv'))
	{
		$video_data = iconv('windows-1251', 'utf-8', $video_data);
	}
	else
	{
		echo '<div class="alert alert-danger"><strong>Warning</strong>: function iconv() is required to decode <strong>windows-1251</strong> characters to <strong>utf-8</strong>.<br />Ask your hosting provider to install or enable the <a href="http://www.php.net/manual/en/book.iconv.php" target="_blank" title="PHP Manual - Iconv"><strong>iconv</strong></a> extension on your account.</div>';
	}

	if (function_exists('json_decode'))
	{
		$json = json_decode($video_data, true);
		
		if ($json !== null)
		{
			return $json;
		}
	}
	
	$video_data = str_replace(array("\n", "\r", "\t"), '', $video_data);

	return $video_data;
}

function get_flv($video_data)
{
	return;
}

function get_thumb_link($video_data) 
{
	if (is_array($video_data))
	{
		return $video_data['thumbnail_url'] .'?size=m';
	}
	else
	{
		if (preg_match('/"thumbnail_url": "(.*?)"/', $video_data, $matches))
		{
			return $matches[1] .'?size=m';
		}
	}

	return '';
}

function video_details($video_data, $url, &$info) 
{
	
	if (is_array($video_data))
	{
		$info['video_title'] 	= $video_data['title'];
		$info['description']	= $video_data['description'];
		$info['direct'] 		= $video_data['video_url'];
		$info['url_flv']		= $video_data['video_url'];
		$info['yt_id'] 			= $video_data['track_id']; 
		$info['yt_length']		= $video_data['duration'];
	}
	else
	{
		if (preg_match('/"title": "(.*?)"/', $video_data, $matches))
		{
			$info['video_title'] = $matches[1];
		}
		
		if (preg_match('/"description": "(.*?)"/', $video_data, $matches))
		{
			$info['description'] = $matches[1];
		}
		
		if (preg_match('/"track_id": ([0-9]+)(}|,|$)?/', $video_data, $matches))
		{
			$info['yt_id'] = $matches[1];
		}
		
		if (preg_match('/"duration": (.*?)(}|,|$)?"/', $video_data, $matches))
		{
			$info['yt_length'] = (int) $matches[1];
		}
		
		if (preg_match('/"video_url": "(.*?)"/', $video_data, $matches))
		{
			$info['direct'] = $matches[1];
			$info['url_flv'] = $matches[1];
		}
	}

	$info['yt_thumb'] = get_thumb_link($video_data);
	
	if ($info['direct'] == '')
	{
		$info['direct'] = $info['url_flv'] = $url;
	}
	

	
	return true;
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


?>