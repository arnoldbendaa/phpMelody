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

namespace phpmelody\sources\src_vimeo;

function get_info($url)
{
	$video_data = array();

	preg_match("/vimeo.com\/([^(\&|$)]*)/", $url, $matches);
	$vid_id = $matches[1];
	if(strstr($vid_id, "/") !== false)
	{
		$tmp = explode("/", $vid_id);
		$vid_id = $tmp[0];
		unset($tmp);
	}
	elseif(strstr($vid_id, "?") !== false)
	{
		$tmp = explode("?", $vid_id);
		$vid_id = $tmp[0];
		unset($tmp);
	}
	unset($matches);

	$target_url = "https://vimeo.com/api/v2/video/" . $vid_id .'.php';
	
	$error = 0;
	if(function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
		
		if ( ! extension_loaded("curl"))
		{
    		echo '<div class="alert alert-danger"><strong>Vimeo</strong> requires cURL which appears to be missing. Ask your hosting provider to enable/install <strong>cURL</strong>.</div>';
    	}    
		
		$video_data = @file_get_contents($target_url);
		if($video_data === false)
			$error = 1;
	}
	if (is_array($video_data))
	{
		$video_data = implode('', $video_data);
	}
	$video_data = unserialize($video_data);
	$video_data = $video_data[0];

	return $video_data;
}

function get_flv($video_data, $mobile_url = false)
{
	$target_url = 'https://player.vimeo.com/video/'. $video_data['id'] .'/config';

	if(function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$data = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if($errormsg != '')
		{
			return '';
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$data = @file_get_contents($target_url);
		if($data === false)
			return '';
	}

	if ( ! function_exists('json_decode'))
	{
		preg_match('/"sd":{(.*?),"url":"(.*?)",/', $data, $matches);
		
		return $matches[2];
	}
	
	$data = json_decode($data, true);

	if (_USE_HQ_VIDS && ! $mobile_url && $data['video']['allow_hd'] == 1 && strlen($data['request']['files']['progressive'][2]['url']) > 0)
	{
		//return $data['request']['files']['h264']['hd']['url'];
		return $data['request']['files']['progressive'][2]['url'];
	}

	//return ($mobile_url) ? $data['request']['files']['h264']['mobile']['url'] : $data['request']['files']['h264']['sd']['url'];
	return ($mobile_url) ? $data['request']['files']['progressive'][0]['url'] : $data['request']['files']['progressive'][1]['url'];
}

function get_thumb_link($video_data) 
{
	global $config;
	
	if ($config['download_thumb_res'] == 'small')
	{
		return $video_data['thumbnail_small'];
	}
	
	if ($config['download_thumb_res'] == 'medium')
	{
		return $video_data['thumbnail_medium'];
	}
	
	return (isset($video_data['thumbnail_large'])) ? $video_data['thumbnail_large'] : $video_data['thumbnail_medium'];
}


function video_details($video_data, $url, &$info) 
{
	$info = array(  'video_title'	=> $video_data['title'],
					'description'	=> $video_data['description'],
					'yt_id'			=> $video_data['id'],
					'url_flv' 		=> get_flv($video_data),
					'yt_thumb' 		=> get_thumb_link($video_data),
					'direct' 		=> $video_data['url'],
					'tags' 			=> $video_data['tags'],
					'yt_length' 	=> $video_data['duration'],
					
			);
	
	return true;
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
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:16.0) Gecko/20100101 Firefox/16.0');
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