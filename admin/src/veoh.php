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

namespace phpmelody\sources\src_veoh;

function get_info($url)
{
	$video_data = array();
	
	$url = urldecode($url);
	
	preg_match("/watch\/([^(\&|$)]*)/i", $url, $matches);
	if(strlen($matches[1]) > 0)
	{
		$video_id = $matches[1];
	}
	else
	{	
		preg_match('/watch=([^(\&|$)]*)/', $url, $matches);
		$video_id = $matches[1];
	}
	if($video_id == '')
	{
		echo 'Sorry, I don\'t recognize this URL';
		return false;
	}

	$target_url = "http://www.veoh.com/rest/video/".$video_id."/details";

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
		$video_data = @file($target_url);
		if($video_data === false)
			$error = 1;
	}
	if(!is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	//	cleanup
	$buff_arr = array();
	$i = 0;
	$allow_embedding = 1;
	foreach($video_data as $k => $v)
	{
		$v = trim($v, " \t\r\n");
		if($v != '')
		{
			$buff_arr[$i++] = $v;
		}
		if($allow_embedding == 1)
		{
			if(preg_match('/allowEmbedding="([a-zA-Z]*)"/', $v, $matches) != 0)
			{
				$allow_embedding = (strtolower($matches[1]) == 'false') ? 0 : 1;
			}
		}
	}
	$video_data = $buff_arr;

	if(!$allow_embedding)
	{
		echo 'Embedding disabled for this video.';
		return false;
	}
	
	return $video_data;
}

function get_flv($video_data)
{
	//	extract flv link
	$arr_length = count($video_data);

	for($i = 0; $i < $arr_length; $i++)
	{	
		if(preg_match('/fullPreviewHashPath=\"(.*?)\"/', $video_data[$i], $matches))
		{	
			$flv_link = $matches[1];
			break;
		}
	}
	return $flv_link;
}

function get_thumb_link($video_data) 
{
	$thumb_link = '';
	$arr_length = count($video_data);
	for($i = 0; $i < $arr_length; $i++)
	{
		if(preg_match('/fullMedResImagePath=\"(.*?)\"/', $video_data[$i], $matches))
		{	
			$link = $matches[1];
			break;
		}
	}
	$thumb_link = $link;
	return $thumb_link;
}

function video_details($video_data, $url, &$info) 
{
	$arr_length = count($video_data);
	for($i = 0; $i < $arr_length; $i++)
	{
		$video_data[$i] = str_replace( array("\n", "\t", "\r"), '', $video_data[$i]);

		//	video id
		if(preg_match("/permalinkId=\"(.*?)\"/", $video_data[$i], $matches) != 0)
		{			
			$info['yt_id'] = $matches[1];
		}
		//	duration
		elseif(preg_match("/length=\"(.*?)\"/i", $video_data[$i], $matches) != 0)
		{
			$time = $matches[1];
			$total = 0;
			if(preg_match("/([0-9]+) hr ([0-9]+) min/", $time, $buff))
			{
				$total = 3600 * $buff[1];	//	hours
				$total += 60 * $buff[2];	//	minutes
			}
			elseif(preg_match("/([0-9]+) min ([0-9]+) sec/", $time, $buff))
			{
				$total = 60 * $buff[1];	//	minutes
				$total += $buff[2];	//	seconds
			}
			elseif(preg_match("/([0-9]+) sec/", $time, $buff))
			{
				$total = $buff[1];	//	seconds
			}
			$info['yt_length'] = $total;
		}
		//	video title
		elseif(preg_match("/title=\"(.*?)\"/", $video_data[$i], $matches) != 0)
		{
			$info['video_title'] = $matches[1];
		}	
		//	tags
		elseif(preg_match("/tagsCommaSeparated=\"(.*?)\"/", $video_data[$i], $matches) != 0)
		{
			$info['tags'] = $matches[1];
		}
		//	description
		elseif(preg_match("/description=\"(.*?)\"/", $video_data[$i], $matches) != 0)
		{
			$info['description'] = $matches[1];
		}
		//	mp4 link
		elseif(preg_match("/ipodLink=\"(.*?)\"/", $video_data[$i], $matches) != 0)
		{
			$info['mp4'] = $matches[1];
		}
	}
	$info['url_flv'] = get_flv($video_data);
	$info['yt_thumb'] = get_thumb_link($video_data);
	//$info['direct'] = $url;
	$direct = urldecode($url);
	if(strstr($direct, "http://") === FALSE)
		$direct = "http://" . $direct;
	if(strstr($direct, "?"))
	{
		$str1 = explode("?", $direct);
		$direct = $str1[0];
	}
	$info['direct'] = $direct;
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