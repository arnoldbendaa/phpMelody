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

namespace phpmelody\sources\src_trilulilu;

function get_info($url)
{
	$video_data = array();
	
	$target_url = $url;
	
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
	if(!is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	return $video_data;
}

function get_flv($video_data)
{
	return true;
}

function get_thumb_link($video_data) 
{
	$arr_length = count($video_data);
	for($i = 0; $i < $arr_length; $i++)
	{
		if (strpos($video_data[$i], 'rel="image_src"'))
		{
			preg_match('/href="(.*?)"/', $video_data[$i], $matches);
			return $matches[1];
		}
		if (strpos($video_data[$i], 'og:image'))
		{
			preg_match('/content="(.*?)"/', $video_data[$i], $matches);
			return $matches[1];
		}
	}
	return '';
}

function video_details($video_data, $url, &$info) 
{

	$arr_length = count($video_data);

	$userid = '';
	$hash = '';
	$server = '';
	$key = '';
	$key_alt = 'ministhebest'; // alternative key

	$total = 0;
	for($i = 0; $i < $arr_length; $i++)
	{
		//	video title
		if (strlen($info['video_title']) == 0)
		{
			if(preg_match('/<title>(.*?)-(.*?)-/', $video_data[$i], $matches) != 0)
			{	
				$info['video_title'] = str_replace('"', '', trim($matches[2]));
				$total++;
			}
		}
		
		if ($userid == '')
		{
			//swfobject.embedSWF("http://static.trilulilu.ro/flash/player/videoplayer2011.swf", "video_player_swf", "620", "381", "9.0.0", "http://static.trilulilu.ro/swfobject/expressInstall.swf", {"userid":"ioanaralukutz","hash":"4b96a30dc5ed44","server":"68","autoplay":"true","hasAds":"true","viewfile":1,"key":"","custom_id":"video_player_swf","exitFullScreen":"onExitFullscreen","enterFullScreen":"onEnterFullscreen","isContinuous":"false","seek":"0"}, {wmode: 'opaque', allowscriptaccess: 'always', allowFullScreen: 'true'})</script>										
			if (preg_match('/<param name="flashvars" value="(.*?)">/', $video_data[$i], $matches) != 0)
			{
				$pairs = explode('&', $matches[1]);
				
				foreach ($pairs as $k => $pair)
				{
					$key_value = explode('=', $pair);
					switch (strtolower($key_value[0]))
					{
						case 'userid': 	$userid = $key_value[1]; 	break;
						case 'hash':	$hash = $key_value[1];		break;
						case 'server':	$server = $key_value[1];	break;
						case 'key':		$key = $key_value[1];		break;
					}
				}
			}
			else if (strpos($video_data[$i], 'swfobject.embedSWF'))
			{
				//{"userid":"ioanaralukutz","hash":"4b96a30dc5ed44","server":"68","autoplay":"true","hasAds":"true","viewfile":1,"key":"","custom_id":"video_player_swf","exitFullScreen":"onExitFullscreen","enterFullScreen":"onEnterFullscreen","isContinuous":"false","seek":"0"}
				preg_match('/"userid":"(.*?)"/', $video_data[$i], $matches);
				$userid = $matches[1];
				
				preg_match('/"hash":"(.*?)"/', $video_data[$i], $matches);
				$hash = $matches[1];
				
				preg_match('/"server":"(.*?)"/', $video_data[$i], $matches);
				$server = $matches[1];
				
				preg_match('/"key":"(.*?)"/', $video_data[$i], $matches);
				$key = $matches[1];
			}
		}
		else
		{
			$total++;
		}

		if ($total == 2)
		{
			break;
		}
	}
	
	if ($key == '')
	{
		$key = $key_alt; 
	}
	
	//	video id
	$info['yt_id'] = $hash;
	
	//	flv
	$flv_url_base = 'http://fs'.$server.'.trilulilu.ro/stream.php?type=video&source=site&hash='.$hash.'&username='.$userid.'&key='.$key;
	
	$flv_url = $flv_url_base .'&format=mp4-360p&start=';
	$response = fetch_headers($flv_url);
	if ( ! strpos($response[0], '200'))
	{
		$flv_url = $flv_url_base .'&format=flv-vp6&start=';
	}	
	$info['url_flv'] = $flv_url;

	//	direct link
	$info['direct'] = $url;
	
	//	thumbnail link
	$info['yt_thumb'] = get_thumb_link($video_data);
	
	if ($info['yt_thumb'] == '')
	{
		$info['yt_thumb'] = 'http://thumbs'.$server.'.trilulilu.ro/'.$userid.'/'.$info['yt_id'].'/thumb';
	}
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