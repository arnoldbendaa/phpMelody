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
// | Copyright: (c) 2004-2015 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+


if ( ! defined('VS_UNCHECKED'))		define('VS_UNCHECKED', 0);
if ( ! defined('VS_OK'))			define('VS_OK', 1);
if ( ! defined('VS_BROKEN'))		define('VS_BROKEN', 2);
if ( ! defined('VS_RESTRICTED'))	define('VS_RESTRICTED', 3);

/**
 * Runs the update query on `pm_videos` table
 * 
 * @param int $video_id (pm_videos.id)
 * @param int $status VS_OK, VS_BROKEN, etc. 
 * @return bool false on mysql error, true on success
 */
function vscheck_update_video_status($video_id, $status)
{
	if (count_entries('pm_videos_trash', 'id', $video_id) > 0)
	{
		$sql_table = 'pm_videos_trash';
	}
	else
	{
		$sql_table = 'pm_videos';
	}
	
	$sql = "UPDATE $sql_table 
			SET status = '". $status ."', 
			    last_check = '". time() ."' 
			WHERE id = '". $video_id ."'";
				
	return ( ! mysql_query($sql)) ? false : true;
}


/**
 * Get HTTP headers via get_headers(), cURL or fopen()
 * 
 * @param string $url
 * @return array indexed array with the headers data; contains 'error' key on failure
 */
function vscheck_fetch_headers($url)
{
	$headers = array();
	$url = trim($url);
	
	$error = 0;
	if(function_exists('get_headers'))
	{
		$url = str_replace(' ', '%20', $url);
		if (strpos($url, 'http') !== 0 && strpos($url, '//') !== 0)
		{
			$url = 'http://' . $url;
		}
		$headers = get_headers($url, 0);
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
				return array('error' => $errormsg);
			}				
			$headers = explode("\n", $data);
		}
		else if(ini_get('allow_url_fopen') == 1)
		{
			$fp = @fopen($url, "r");
			if ( ! $fp)
			{
				$error = 1;
			}
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
	if ($error)
	{
		return array('error' => 'Failed to open stream.');
	}
	return $headers;
}


/**
 * Get raw video data from `pm_videos` and `pm_videos_urls` without using the heavier request_video()
 * 
 * @param int $video_id the `pm_videos`.`id`
 * @return array containing video data
 */
function vscheck_get_video_details($video_id)
{
	$sql = "SELECT * 
			FROM pm_videos 
			LEFT JOIN pm_videos_urls 
			  ON (pm_videos.uniq_id = pm_videos_urls.uniq_id) 
			WHERE pm_videos.id = '". $video_id ."' 
			LIMIT 1";
		
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	
	if (mysql_num_rows($result) == 0)
	{
		// look in trash
		$sql = "SELECT * 
				FROM pm_videos_trash
				WHERE id = '". $video_id ."' 
				LIMIT 1";
		$result = @mysql_query($sql);
		if ( ! $result)
		{
			return false;
		}
	}
	
	$video = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	return $video;
}


/**
 * Check the video's status.  
 * 
 * This function is used by both the automated background process and manual checks (via admin UI).
 * Checks the API where available because this way is more reliable.
 * The video status will not be updated here.
 * 
 * @todo TBD: For other sources, we could do a simple vscheck_fetch_headers() based on the original url and look for a 404 response. 
 * 		Pros: extends functionality to all sources
 * 		Cons: not as reliable as probably expected by the customer
 * 
 * @param array $args must contain one of: video_id, uniq_id and/or a video data array as returned by request_video() (key = video-data) 
 * 
 * @return array containing the determined video status, a display message to be used in manual checks and a "api message" to be used in 'PM Bot' reports
 */
function vscheck_get_video_status($args)
{
	global $config, $video_sources;

	$video = null;
	$status = 0;
	$display_message = '';	// human readable message to be displayed on a html page
	$api_message = '';		// human readable message to be used by PM BOT when triggering a report
	
	if (is_array($args['video-data']))
	{
		$video = $args['video-data'];
	}
	else
	{
		if ($args['uniq_id'] != '')
		{
			$video_id = uniq_id_to_video_id($args['uniq_id']);
		}
		else
		{
			$video_id = $args['video_id'];
		}
		
		$video = vscheck_get_video_details($video_id);
	}
	
	switch ( (int) $video['source_id'])
	{
		default:

			$display_message = "Sorry, Video ID '". $video['uniq_id'] ."' cannot be verified."; 
			
		break;
		
		case 1: // localhost
		
			if ($video['url_flv'] != '')
			{
				if (file_exists(_VIDEOS_DIR_PATH . $video['url_flv']))
				{
					if (($size = filesize(_VIDEOS_DIR_PATH . $video['url_flv'])) === 0)
					{
						$status = VS_BROKEN;
						$api_message = $display_message = 'File <code>'. $video['url_flv'] .'</code> is <strong>'. $size .' bytes</strong> in size.';
					}
					else
					{
						$status = VS_OK;
					}
				}
				else
				{
					$status = VS_BROKEN;
					$api_message = $display_message = 'File <code>'. $video['url_flv'] .'</code> not found in <code>'. _VIDEOS_DIR_PATH .'</code> directory.';
				}
				
				
			}
		
		break;
		
		case 2: // remote file location
		
			$status = $video['status'];
			
			$headers = (array) vscheck_fetch_headers($video['url_flv']);
			
			if (array_key_exists('error', $headers))
			{
				$display_message = 'Could not fetch requested information. <br />Error <code>'. $headers['error'] .'</code>';
			}
			else
			{
				preg_match('/[0-9]{3}/', $headers[0], $matches);
				$code = (int) $matches[0];
				
				unset($matches);
				
				switch ($code)
				{
					case 200:
					case 304: 
					
						$status = VS_OK;
					
					break;
					
					case 301:
					case 302:
					
						// get new location
						foreach ($headers as $k => $v)
						{
							if(strpos($v, "ocation:") !== false)
							{
								$str1 = explode("ocation:", $v);
								$link = trim($str1[1]);
								break;
							}
						}
						
						$status = VS_BROKEN;
						
						$api_message = $display_message = 'File moved '. (($code == 301) ? 'permanently' : 'temporarily') .' to this location: <code>'. $link .'</code>';
					
					break;
					
					case 400:
					case 401:
					case 403:
					case 404:
					case 501:
					case 502:
					
						$status = VS_BROKEN;
						$display_message = 'The remote server responded with a <code>'. $headers[0] .'</code> to your request.';
						$api_message = 'The remote server responded with a "'. $headers[0] .'" when tried a status check.';
					
					break;
					
					case 500:
					case 503:
					
						$display_message = 'The remote server is temporarily unavailable. Try again in a few minutes.';
					
					break;
				}
			}
			
		break;
		
		case 3:	//	Youtube
		
			if ($config['youtube_api_key'] == '')
			{
				$display_message = "You need a Youtube API Key to check videos. For <strong><a href='http://help.phpmelody.com/how-to-create-a-youtube-api-key/' target='_blank'>these step-by-step video instructions</a></strong> on how to create your API key.";
				break;
			}
			
			if ($video['yt_id'] == '')
			{
				$display_message = "Video '".$video['uniq_id']."' is missing the original video id.";
				break;
			}
			
			if ( ! class_exists('PhpmelodyYouTube'))
			{
				define('PHPMELODY', true);
				include(ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/autoload.php');
			}
			
			$google_client = new Google_Client();
			$google_client->setDeveloperKey($config['youtube_api_key']);
			$youtube_api = new PhpmelodyYouTube($google_client);
			
			// cheaper way first
			$response = $youtube_api->pm_get_video_status($video['yt_id']);
			
			if (is_array($response) && array_key_exists('error', $response))
			{
				$display_message = $response['error']['message']; // api error
			}
			else
			{
				$status = $response[$video['yt_id']];
			}
			
		break;
		
		
		case 5: // dailymotion
			
			if ($video['yt_id'] == '')
			{
				$display_message = "Video '".$video['uniq_id']."' is missing the original video id.";
				break;
			}
						
			if ( ! class_exists('PhpmelodyDailymotion'))
			{
				include(ABSPATH . _ADMIN_FOLDER .'/src/dailymotion-sdk/autoload.php');
			}
			
			$dailymotion_api = new PhpmelodyDailymotion();
			
			$status = VS_OK;
			
			try {

				$result = $dailymotion_api->get('/video/'. $video['yt_id'],
												array('fields' => array('access_error', 'allow_embed', 'geoblocking', 'paywall', 'private', 'svod', 'tvod')) 
											);
				
				if ($result['paywall'] || $result['private'] || $result['svod'] || $result['tvod'] || ! empty($result['access_error']))
				{
					$status = VS_BROKEN;
					
					if ($result['access_error'])
					{
						$api_message = $display_message = $result['access_error']['raw_message'];
					}
					else
					{
						$api_message = $display_message = 'This video is either private or behind a paywall.';
					}
					
					break;
				}
				
				if (count($result['geoblocking']) == 0 || ! is_array($result['geoblocking'])) // allowed everywhere
				{
					 $status = VS_OK;
				}
				else if ($result['geoblocking'][0] == 'deny') // denied to
				{
					$status = VS_RESTRICTED;
				}
				else // allowed only
				{
					if ($result['geoblocking'][0] == 'allow')
					{
						unset($result['geoblocking'][0]);
					}
					
					if (count($result['geoblocking']) > 0)
					{
						$status = VS_RESTRICTED; 
					}
				}
  
			} catch(DailymotionApiException $e) {
				
				if ($dailymotion_api->error)
				{
					$display_message = '<strong>Dailymotion API error '. $dailymotion_api->error->code . ':</strong> '. $dailymotion_api->error->message;
				}
				else
				{
					$display_message = '<strong>Dailymotion API error:</strong> '. $e->__toString();
				}
				
				if (strpos($display_message, 'deleted') || strpos($display_message, 'not exist') || strpos($display_message, 'removed'))
				{
					$status = VS_BROKEN;
				}
			}
			
		break;

		case 16: // vimeo
		
			if ($video['yt_id'] == '')
			{
				$display_message = "Video '".$video['uniq_id']."' is missing the original video id.";
				break;
			}
			
			$status = VS_OK;
			
			if ( ! empty($config['vimeo_api_token']))
			{
				if ( ! class_exists('PhpmelodyVimeo'))
				{
					include(ABSPATH . _ADMIN_FOLDER .'/src/vimeo-sdk/autoload.php');
				}
				
				$vimeo_api = new PhpmelodyVimeo(null, null, $config['vimeo_api_token']);
				
				$result = $vimeo_api->request('/videos/'. $video['yt_id'], array(), 'GET');

				if ($result['status'] == 200)
				{
					$privacy = $result['body']['privacy'];
				}
				else if ($result['status'] == 404)
				{
					$status = VS_BROKEN;
					 
					$api_message = $display_message = 'Vimeo API responded with a "404 Not Found".<br />Error <code>'. $result['body']['error'] .'</code>';
					
					break;
				}
			}
			else
			{
				if ( ! function_exists('phpmelody\sources\src_vimeo\get_info'))
				{
					include_once(ABSPATH . _ADMIN_FOLDER .'/src/vimeo.php');
				}
				
				$get_info = $video_sources['vimeo']['php_namespace'] .'\get_info';
				$result = $get_info('https://vimeo.com/'. $video['yt_id']);

				if ($result === null)
				{
					$headers = vscheck_fetch_headers('https://vimeo.com/'. $video['yt_id']);
					
					preg_match('/[0-9]{3}/', $headers[0], $matches);
					$code = (int) $matches[0];
					
					unset($matches);
				
					switch ($code)
					{
						case 400:
						case 401:
						case 403:
						case 404:
						case 501:
						case 502:
						
							$status = VS_BROKEN;
							$display_message = 'The Vimeo server responded with a <code>'. $headers[0] .'</code> to your request.';
							$api_message = 'The Vimeo server responded with a "'. $headers[0] .'".';
						
						break;
						
						case 500:
						case 503:
						
							$display_message = 'The Vimeo server is temporarily unavailable. Try again in a few minutes.';
						
						break;
					}
					
					break;
				}
				
				$privacy = array('embed' => $result['embed_privacy']);
			}
			
			if ($privacy['view'] == 'nobody')
			{
				$status = VS_BROKEN;
				$api_message = $display_message = 'This video is not available for public viewing.';
				
				break;
			}
			
			if (in_array($privacy['embed'], array('private', 'nowhere')))
			{
				$status = VS_BROKEN;
				$api_message = $display_message = 'This video is not embeddable.';

				break;
			}
			
		break;
	}
	
	$display_message = ($display_message != '' && strpos($display_message, $video['uniq_id']) === false) ? $video['uniq_id'] .': '. $display_message : $display_message;
	
	return array('status' => $status,
				 'display_message' => $display_message,
				 'api_message' => strip_tags( str_replace(array('<br />', '<br>'), "\n", $api_message))
			); 
}
