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

namespace phpmelody\sources\src_5min;

$xml = array();
$xml_index = "";
$xml_array_counter = 0;
$xml_error_msg = '';

function get_info($url)
{
	global $xml, $xml_array_counter, $xml_index, $xml_error_msg;
	
	$video_data = array();
	
	preg_match('/5min\.com\/video\/(.*?)-([0-9]+)/i', $url, $matches);
	$target_url = 'http://api.5min.com/video/'. $matches[2] .'/info.xml';
	
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
	
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, __NAMESPACE__ ."\parser_startElement", __NAMESPACE__ ."\parser_endElement");
	xml_set_character_data_handler($xml_parser, __NAMESPACE__ ."\parser_characterData");
	if(is_array($video_data))
	{
		$video_data = implode("", $video_data);
	}
	
	if( ! \xml_parse($xml_parser, $video_data, TRUE))
	{
		$xml_error_msg = sprintf("XML error: %s at line %d", 
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser));
	}
	xml_parser_free($xml_parser);
	
	unset($video_data);

	return $xml;
}

function get_flv($video_data)
{
	return $video_data[0]['MEDIA:CONTENT']['url'];
}

function get_thumb_link($video_data) 
{
	return $video_data[0]['MEDIA:THUMBNAIL']['url'];
}


function video_details($video_data, $url, &$info) 
{
	//	flv
	$info['url_flv'] = get_flv($video_data);
	
	//	thumbnail link
	$info['yt_thumb'] = get_thumb_link($video_data);
	
	//	description
	$info['description'] = str_replace('Array', '', $video_data[0]['MEDIA:DESCRIPTION']);
	
	//	title
	$info['video_title'] = str_replace('Array', '', $video_data[0]['MEDIA:TITLE']);
	
	//	tags
	$info['tags'] = $video_data[0]['MEDIA:KEYWORDS'];
	
	//	video id
	$info['yt_id'] = $video_data[0]['ID'];
	
	//	direct link
	$info['direct'] = $video_data[0]['LINK'];
		
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

function do_main(&$video_details, $url, $show_warnings = true)
{
	$video_data = get_info($url);
	if($video_data != false)
	{
		video_details($video_data, $url, $video_details);
	}
	else
	{
		if ($show_warnings && $xml_error_msg != '')
		{
			echo '<div class="alert alert-error">'. $xml_error_msg .'</div>';
		}
		$video_details = array();
	}
}

function parser_startElement($parser, $name, $attrs) 
{
	global $xml, $xml_array_counter, $xml_index;
	switch($name) 
	{
		case "ENTRY":
			$xml_index = "";
			break;
		default:
			$xml_index = $name;
			if(count($attrs) > 0)
			{
				foreach($attrs as $key => $value) 
				{
					$xml[$xml_array_counter][$xml_index][strtolower($key)] = $value;
				}
			}
			else
			{
				$xml[$xml_array_counter][$xml_index] = "";
			}
			break;
	}
}

function parser_endElement($parser, $name) 
{
	global $xml, $xml_index, $xml_array_counter;
	switch($name) 
	{
		case "ENTRY":
			$xml_array_counter++;
			break;
	}
	$xml_index = "";
}

function parser_characterData($parser, $data) 
{
	global $xml, $xml_array_counter, $xml_index;
	if ($xml_index != "") 
	{
		$xml[$xml_array_counter][$xml_index] .= trim($data);
	}
}

?>