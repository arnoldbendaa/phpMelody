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

session_start();
define('IGNORE_MOBILE', true);
require_once('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php'); 

if (empty($_GET['vid'])) 
{
	exit();
} 
else 
{
	$video_sources = fetch_video_sources();
	$video_src_name = '';
	
	$temp		= array();
	$video		= array();
	$mime_type	= 'video/x-flv';
	
	$video_id 	= secure_sql($_GET['vid']);
		
	$sql = "SELECT pm_videos.*, pm_videos_urls.mp4, pm_videos_urls.direct 
			FROM pm_videos 
			LEFT JOIN pm_videos_urls 
				   ON (pm_videos.uniq_id = pm_videos_urls.uniq_id) 
			WHERE pm_videos.uniq_id = '". $video_id ."'";

	$result =  @mysql_query($sql);
	$video = @mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	if ( ! $video && (is_admin() || (is_moderator() && mod_can('manage_videos'))))
	{
		// check the Trash
		$sql = "SELECT * 
				FROM pm_videos_trash 
				WHERE uniq_id = '". $video_id ."'";
		
		$result =  @mysql_query($sql);
		$video = @mysql_fetch_assoc($result);
		mysql_free_result($result);
	}
	
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
						
	define('PHPMELODY', true);
	
	$divx_player = false;
	if ($video['source_id'] == $video_sources['localhost']['source_id'] || $video['source_id'] == $video_sources['other']['source_id'])
	{
		$tmp_parts = explode('.', $video['url_flv']);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);

		if ($ext == 'mkv' || $ext == 'avi' || $ext == 'mkv')
		{
			$divx_player = true;
		}
	}
	
	if ( ! $divx_player)
	{
		if ( ! is_user_logged_in() && $video['restricted'] == '1')
		{
			echo $lang['registration_req'];
			exit();
		}	
	}
	
	$video_src_name = strtolower($video_sources[$video['source_id']]['source_name']);

	switch ($video_src_name)
	{
		case 'veoh':	// VEOH

			@include(ABSPATH . _ADMIN_FOLDER ."/src/veoh.php");
			
			$do_main = $video_sources['veoh']['php_namespace'] .'\do_main';
			$do_main($temp, $video['direct']);
			$flv_url = $temp['url_flv'];
			unset($temp);
		break;
		
		case 'metacafe': // Metacafe
		
			@include(ABSPATH . _ADMIN_FOLDER ."/src/metacafe.php");
			
			$get_flv = $video_sources['metacafe']['php_namespace'] .'\get_flv';
			$flv_url = $get_flv($video['direct']);
			unset($temp);
		break;
		
		case 'funnyordie': // FunnyOrDie
		
			$parts = explode("/", $video['direct']);
			$vid_id = $parts[ count($parts)-2 ];
			$flv_url = 'http://videos0.ordienetworks.com/videos/'.$vid_id.'/sd.flv';
		break;
		
		case 'vimeo': // Vimeo
		
			@include(ABSPATH . _ADMIN_FOLDER ."/src/vimeo.php");
			
			$do_main = $video_sources['vimeo']['php_namespace'] .'\do_main';
			$do_main($temp, $video['direct']);
			$flv_url = $temp['url_flv'];
			unset($temp);
		break;
		
		case 'myspace': // Myspace

			@include(ABSPATH . _ADMIN_FOLDER ."/src/myspace.php");
			
			$do_main = $video_sources['myspace']['php_namespace'] .'\do_main';
			$do_main($temp, $video['direct']);
				
			$flv_url = $temp['url_flv'];
			unset($temp);
			
			if ($flv_url == '')
			{
				report_video($video['uniq_id'], '1', 'The *.FLV URL was not found', 'PM Bot');
			}
			
		break;
		
		case 'break': //	break.com
			
			if (strpos($video['url_flv'], 'media1.break'))
			{
				$video['url_flv'] = str_replace('media1.', 'video1.', $video['url_flv']);
				
				$sql = "UPDATE pm_videos SET url_flv = '". secure_sql($video['url_flv']) ."' 
							WHERE id = '". $video['id'] ."'";
				@mysql_query($sql);
			}
			
			$flv_url = $video['url_flv'];
			
		break;
		
		case 'sevenload': // sevenload
			
			@include(ABSPATH . _ADMIN_FOLDER ."/src/sevenload.php");
			
			$do_main = $video_sources['sevenload']['php_namespace'] .'\do_main';
			$fetch_headers = $video_sources['sevenload']['php_namespace'] .'\fetch_headers';
			
			if ($video['direct'] == '')
			{
				$video['direct'] = 'http://en.sevenload.com/videos/'. $video['yt_id'] .'-';

				$headers = $fetch_headers($video['direct']);
				$arr_length = count($headers);
	
				for($i = 0; $i < $arr_length; $i++)
				{
					if(strpos($headers[$i], "ocation:") !== false)
					{
						$str1 = explode("ocation:", $headers[$i]);
						$video['direct'] = trim($str1[1]);
						break;
					}
				}
	
				@mysql_query("UPDATE pm_videos_urls SET direct='". $video['direct'] ."' WHERE uniq_id = '". $video['uniq_id'] ."'");
			}

			$do_main($temp, $video['direct']);
			
			$flv_url = str_replace('&amp;', '&', $temp['url_flv']);
			unset($temp);

		break;
		
		case 'trilulilu': // trilulilu.ro
		
			if (strlen($video['direct']) == 0)
			{
				if (strlen($video['url_flv']) > 0)
				{
					$flv_url = $video['url_flv'];
				}
				else
				{
					report_video($video['uniq_id'], '1', 'The *.FLV URL was not found', 'PM Bot');
				}
			}
			else
			{
				@include(ABSPATH . _ADMIN_FOLDER ."/src/trilulilu.php");
				
				$do_main = $video_sources['trilulilu']['php_namespace'] .'\do_main';
				$do_main($temp, $video['direct']);
				$flv_url = $temp['url_flv'];
				unset($temp);	
			}			
		break;
		
		case 'vbox7':
			
			@include(ABSPATH . _ADMIN_FOLDER ."/src/vbox7.php");
			
			$do_main = $video_sources['vbox7']['php_namespace'] .'\do_main';
			$do_main($temp, $video['direct']);
			$flv_url = $temp['url_flv'];
			unset($temp);	

		break;
		
		case 'mynet':
			
			@include(ABSPATH . _ADMIN_FOLDER ."/src/mynet.php");
			
			$do_main = $video_sources['mynet']['php_namespace'] .'\do_main';
			$do_main($temp, $video['direct']);
			
			$flv_url = $temp['url_flv'];
			$flv_url = str_replace('&amp;', '&', $flv_url);
			unset($temp);
			
		break;
		
		default:
		
			if ($video['source_id'] == $video_sources['localhost']['source_id'] || $video['source_id'] == $video_sources['other']['source_id'])
			{
				if(strpos($video['url_flv'], 'http') === 0 || strpos($video['url_flv'], '//') === 0)
				{
					$flv_url = $video['url_flv'];
				}
				else
				{
					$flv_url = _VIDEOS_DIR . $video['url_flv'];
				}
				
				$tmp_parts = explode('.', $video['url_flv']);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);

				if (array_key_exists($ext, $mime_types))
				{
					$mime_type = $mime_types[$ext];
				}
				else if (function_exists('finfo_open')) 
				{
					$finfo 		= finfo_open(FILEINFO_MIME);
					$mime_type 	= finfo_file($finfo, _VIDEOS_DIR_PATH . $video['url_flv']);
					finfo_close($finfo);
				}
			}
			else
			{
				$flv_url = $video['url_flv'];
			}
			
		break;
	}

	@update_view_count($video['id'], $video['site_views']);


	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Content-Type: ". $mime_type);
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header('Location: '. make_url_https($flv_url));
}
exit();
