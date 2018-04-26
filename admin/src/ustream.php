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

namespace phpmelody\sources\src_ustream;

function get_info($url)
{
	$video_data = array();
	
	$pieces = explode('/', $url);
	
	$target_url = 'http://www.ustream.tv/';
	if ($pieces[3] == 'channel' || $pieces[3] == 'recorded')
	{
		if (strpos($pieces[4], '#'))
		{
			$buff = explode('#', $pieces[4]);
			$pieces[4] = $buff[0];
		}
		$target_url .= $pieces[3] .'/'. $pieces[4];
	}
	else 
	{
		if (strpos($pieces[3], '#'))
		{
			$buff = explode('#', $pieces[3]);
			$pieces[3] = $buff[0];
		}
		$target_url .= $pieces[3];
	}
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
	
	if (strlen($video_data) == 0)
	{
		$headers = fetch_headers($target_url);
		
		$arr_length = count($headers);
		for($i = 0; $i < $arr_length; $i++)
		{
			if(strstr($headers[$i], "ocation:"))
			{
				$buff = explode("ocation:", $headers[$i]);
				$loc = trim($buff[1]);
				break;
			}
		}
				
		$new_url = 'http://www.ustream.tv'. $loc;
		
		return get_info($new_url);
	}
	
	if(!is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	return $video_data;
}

function get_flv($video_data, $url)
{
	return;
}

function get_thumb_link($video_data, $url) 
{
	return;
}


function video_details($video_data, $url, &$info) 
{
	$title = $thumb = $direct = $swf = $yt_id = false;

	$arr_length = count($video_data);
	
	for ($i = 0; $i < $arr_length; $i++)
	{
		if ( ! $title)
		{
			if (strpos($video_data[$i], 'og:title'))
			{
				if (preg_match('/content="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$info['video_title'] = trim($matches[1]);
					$title = true;
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
		}
		
		if ( ! $yt_id)
		{
			if (strpos($video_data[$i], 'name="flashvars"'))
			{
				if (preg_match('/value="(.*?)"/', $video_data[$i], $matches) != 0)
				{
					$pieces = explode('&', $matches[1]);
					$info['yt_id'] = $pieces[0];
					$yt_id = true;
				}
			}
		}
		
		if (($title && $thumb && $direct && $yt_id) !== false)
		{
			break;
		}
	}
	
	if ($info['direct'] == '')
	{
		$pieces = explode('/', $url);
		
		$info['direct'] = 'http://www.ustream.tv/';
		
		if ($pieces[3] == 'channel' || $pieces[3] == 'recorded')
		{
			if (strpos($pieces[4], '#'))
			{
				$buff = explode('#', $pieces[4]);
				$pieces[4] = $buff[0];
			}
			$info['direct'] .= $pieces[3] .'/'. $pieces[4];
		}
		else 
		{
			if (strpos($pieces[3], '#'))
			{
				$buff = explode('#', $pieces[3]);
				$pieces[3] = $buff[0];
			}
			$info['direct'] .= $pieces[3];
		}
		
		if ($pieces[3] != 'recorded')
		{
			$arr_length = count($video_data);
		
			for($i = 0; $i < $arr_length; $i++)
			{
				if (strlen($info['yt_id']) == 0)
				{
					$buff = strtolower($video_data[$i]);
					if (strpos($buff, 'ustream.vars.flashvars='))
					{
						preg_match('/ustream.vars.channelid="(.*?)";/', $buff, $matches);
						$info['yt_id'] = $matches[1]; 
					}
				}
				
				if (strlen($info['video_title']) == 0)
				{
					$buff = strtolower($video_data[$i]);
					if (strpos($buff, 'ustream.vars.channeltitle'))
					{
						preg_match('/ustream.vars.channeltitle="(.*?)";/', $buff, $matches);
						$info['video_title'] = $matches[1];
					}	
				}
				
				if (strlen($info['video_title']) > 0 && strlen($info['yt_id']) > 0)
				{
					break;
				}
			}	
			
			$info['yt_id'] = 'cid='. $info['yt_id'];
		}
		else
		{
			$info['yt_id'] = 'vid='. $pieces[4];
		}
	}

	$info['url_flv'] = 'http://static-cdn1.ustream.tv/swf/live/viewer3:68.swf?vrsl=c:334&ulbr=100&'. $info['yt_id'] .'&sv=6';
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