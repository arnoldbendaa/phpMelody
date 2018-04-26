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

namespace phpmelody\sources\src_mailru;

function get_info($url)
{
	$curl_data = array();
	
	$target_url = $url;
	
	$error = 0;
	
	if (function_exists('curl_init'))
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$curl_data = curl_exec($ch);
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
		$curl_data = file($target_url);

		if ($curl_data === false)
		{
			echo '<div class="alert alert-danger">An error occurred while retrieving the requested page.</div>';
			return false;
		}
	}
	
	if (function_exists('iconv'))
	{
		$curl_data = iconv('windows-1251', 'utf-8', $curl_data);
	}
	else
	{
		echo '<div class="alert alert-danger"><strong>Warning</strong>: function iconv() is required to decode <strong>windows-1251</strong> characters to <strong>utf-8</strong>.<br />Ask your hosting provider to install or enable the <a href="http://www.php.net/manual/en/book.iconv.php" target="_blank" title="PHP Manual - Iconv"><strong>iconv</strong></a> extension on your account.</div>';
	}
	

	if ( ! is_array($curl_data))
	{
		$curl_data = explode("\n", $curl_data);
	}
	
	return $curl_data;
}

function get_flv($url, &$video_data)
{
	global $config;
	
	$pieces = explode('/', $url);
	$pieces_count = count($pieces);
	$pieces_count--;
	
	$pieces[$pieces_count] = str_replace('html', 'json', $pieces[$pieces_count]);
	
	$target_url = 'http://api.video.mail.ru/videos/'. $pieces[$pieces_count - 3] .'/'. $pieces[$pieces_count - 2] .'/'. $pieces[$pieces_count - 1] .'/'. $pieces[$pieces_count];

	$error = 0;
	
	if(function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$curl_data = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if($errormsg != '')
		{
			echo '<div class="alert alert-danger">Error: '. $errormsg .'</div>';
			return false;
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$curl_data = file($target_url);
		if($curl_data === false)
			$error = 1;
	}
	
	if (function_exists('json_decode'))
	{
		$json = json_decode($curl_data, true);
		
		if ($json !== null)
		{
			if ($json['error'] != '')
			{
				echo '<div class="alert alert-danger">Error: '. htmlentities($json['error']) .'</div>';
				return false;
			}
			
			$video_data['url_flv'] = ($config['use_hq_vids'] == '1' && $json['videos']['hd'] != '') ? $json['videos']['hd'] : $json['videos']['sd'];
			$video_data['yt_length'] = (int) $json['duration'];
			
			return true;
		}
	}

	$curl_data = str_replace(array("\n","\t","\r"), '', $curl_data);
	
	$url_flv = '';
	$duration = 0;
	
	if ($config['use_hq_vids'] == '1')
	{
		preg_match('/"hd":"(.*?)"/', $curl_data, $matches);
		$url_flv = $matches[1];
	}
	
	// fallback, not all items are in HD
	if ($url_flv == '')
	{
		preg_match('/"sd":"(.*?)"/', $curl_data, $matches);
		$url_flv = $matches[1];
	}
	
	if (preg_match('/"duration":([0-9]+),/', $curl_data, $matches))
	{
		$duration = (int) $matches[1];
	}
	
	$video_data['url_flv'] = (strlen($url_flv) > 0) ? $url_flv : false;
	$video_data['yt_length'] = $duration;
	 
	return true;
}

function get_thumb_link($video_data) 
{
	return;
}

function video_details($video_data, $url, &$info) 
{
	$title = $thumb = $direct = $description = $tags = false;
	
	$arr_length = count($video_data);
	
	for ($i = 0; $i < $arr_length; $i++)
	{
		if (strlen($video_data[$i]) < 10)
		{
			continue;
		}
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
			if (strpos($video_data[$i], '"image_src"'))
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
		
		if (($title && $thumb && $tags && $direct && $description) || strpos($video_data[$i], '<body') !== false)
		{
			break;
		}
	}
	
	//$info['yt_thumb'] = str_replace('/p-', '/i-', $info['yt_thumb']);
	
	get_flv($url, $info);
	
	if ($info['direct'] == '')
	{
		$info['direct'] = $url;
	}

	$pieces = explode('/video/', $url);

	$info['yt_id'] = $pieces[1];
	
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

?>