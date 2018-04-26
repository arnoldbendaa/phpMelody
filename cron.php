<?php

define('IGNORE_MAINTENANCE_MODE', true);

include('config.php');
include(ABSPATH .'include/cron_functions.php');
include(ABSPATH . _ADMIN_FOLDER .'/functions.php');
include(ABSPATH . 'include/httpful/bootstrap.php');

if ( ! doing_cron())
{
	cron_debug('Access denied! "cron-key" parameter is required.');
	cron_end();
}

error_reporting(((cron_debug()) ? E_ALL & ~E_NOTICE : 0));
ini_set('log_errors', ((cron_debug()) ? 1 : 0));
set_time_limit(60 * 15);

define('DOING_CRON', true);
$admin_ajax_url = _URL . '/'. _ADMIN_FOLDER .'/admin-ajax.php';

if ($config['maintenance_mode'] == 1)
{
	cron_debug('Maintenance Mode is ON.');
	cron_end();
}

$job = get_first_job_available();

cron_debug('$job:', $job);

switch ($job['type'])
{
	case 'import':
		
		// Prevent job from executing multiple times
		update_cron_job_state($job, CRON_STATE_BUSY);
		
		// Get subscription details
		if ( ! $subscription = get_import_subscription($job['rel_object_id']))
		{
			cron_debug(sprintf('MySQL Error: %s <br />Line: %d', mysql_error(), __LINE__));
			cron_end();
		}
		
		cron_debug('$subscription', $subscription);
		
		// Prepare search request params
 		if ($subscription['sub_type'] == 'search')
		{
			$post_params = $subscription['data'];
			$post_params['p'] = 'import';
			$post_params['do'] = 'search';
			
			if (count($job['data']['exclude_keywords']) > 0)
			{
				foreach ($job['data']['exclude_keywords'] as $k => $kw)
				{
					$post_params['keyword'] .= ' -'. $kw;
				}
			}
			$post_params['keyword'] = rtrim($post_params['keyword'], '-');
			
			if ( ! $post_params['search_time'])
			{
				$post_params['search_time'] = 'this_month';
			}
		}
		else if ($subscription['sub_type'] == 'user')
		{
			$post_params = $subscription['data'];
			$post_params['p'] = 'import';
			$post_params['do'] = 'search-user';
			$post_params['action'] = 'search';
		}
		
		$post_params['cron-key'] = $config['cron_secret_key'];
		
		try {
			$response = \Httpful\Request::post($admin_ajax_url, http_build_query($post_params))->withoutStrictSSL()->mime(\Httpful\Mime::FORM)->send();
		} catch (Exception $e) {
			cron_debug('$admin_ajax_url:', $admin_ajax_url, 'http_build_query($post_params):', http_build_query($post_params), '$post_params:', $post_params, 'cron.php line: '. __LINE__);
			cron_exit_on_error('Error: '. $e->getMessage() ."\n<br />File: ". $e->getFile() ."\n<br />Line: ". $e->getLine());
		}
		
		if ($response->code != 200)
		{
			if ($job['data']['search_request_attempts'] <= 10)
			{
				$job['data']['search_request_attempts']++;
				cron_end(false);
			}
			else
			{
				cron_exit_on_error('Failed to retrieve search data. Your server responded with a <code>'. $response->code .'</code> multiple times. <br />Try again in a few minutes.');
			}
		}
		else
		{
			unset($job['data']['search_request_attempts']);
		}
		
		cron_debug('Search Request Response:', $response);
		
		$search_data = json_decode($response->raw_body, true);
		
		unset($response, $post_params);
		
		if ( ! $search_data['success'] && $search_data['alert_type'] == 'error')
		{
			cron_exit_on_error($search_data['msg']);
		}
		
		if ($search_data['total_search_results'] == 0)
		{
			cron_end();
		}
		
		if ($search_data['duplicates'] == $search_data['total_results'] && $search_data['total_results'] > 0)
		{
			cron_end();
		}
		
		// Filter items out
		foreach ($search_data['items'] as $k => $item)
		{
			// @todo geo-location restriction filtering
			
			// filter out videos that have been added before this job's creation date
			if ($item['publish_date_timestamp'] < $job['data']['uploaded_after'] && $job['data']['uploaded_after'] > 0)
			{
				unset($search_data['items'][$k]);
			}
		}
		
		$total_items_imported = 0;
		if (count($search_data['items']) > 0)
		{
			$post_params = $subscription['data'];
			$post_params['p'] = 'import';
			$post_params['do'] = 'import';
			$post_params['cron-key'] = $config['cron_secret_key'];
			
			$post_params['userdata'] = $job['data']['userdata'];
			
			// build the POST data array in reverse chronological order
			$search_data['items'] = array_reverse($search_data['items'], true);
			$category = explode(',', $subscription['data']['utc']);
			
			foreach ($search_data['items'] as $k => $item)
			{
				$id = $item['id'];
			
				$post_params['video_ids'][ $id ] = $id;
				$post_params['video_title'][ $id ] = $item['title'];
				$post_params['category'][ $id ] = $category;
				$post_params['description'][ $id ] = $item['description'];
				$post_params['tags'][ $id ] = $item['keywords'];
				//$post_params['thumb_url'][ $id ] = ($subscription['data']['data_source'] == 'youtube' || $subscription['data']['data_source'] == 'youtube-channel') ? $item['thumbs'][0]['large'] : $item['thumbs'][0]['medium'];
				$post_params['thumb_url'][ $id ] = $item['thumbs'][0][$config['download_thumb_res']];
				$post_params['duration'][ $id ] = $item['duration'];
				$post_params['direct'][ $id ] = $item['url'];
				$post_params['url_flv'][ $id ] = '';
			}

 			try {
				$response = \Httpful\Request::post($admin_ajax_url, http_build_query($post_params))->withoutStrictSSL()->mime(\Httpful\Mime::FORM)->send();
			} catch (Exception $e) {
				cron_debug('$admin_ajax_url:', $admin_ajax_url, 'http_build_query($post_params):', http_build_query($post_params), '$post_params:', $post_params, 'cron.php line: '. __LINE__);
				cron_exit_on_error('Error: '. $e->getMessage() ."\n<br />File: ". $e->getFile() ."\n<br />Line: ". $e->getLine());
			}
			
			if ($response->code != 200)
			{
				cron_log('Failed to send the import request. Your server responded with a <code>'. $response->code .'</code> multiple times. <br />Try again in a few minutes.');
				cron_end();
			}
			
			cron_debug('Import Request Response:', $response);
			
			$imported = json_decode($response->raw_body, true);
			
			unset($response);
			
			if ( ! $imported['success'] && $imported['alert_type'] == 'error')
			{
				cron_exit_on_error($imported['msg']);
			}
			
			if ($imported['success'])
			{
				$msg = 'Imported '. pm_number_format($imported['imported_total']) .' videos out of '. pm_number_format($imported['total_videos'])  .' available.';
				
				if ($imported['import_total_errors'] > 0)
				{
					foreach ($imported['item_status'] as $k => $arr)
					{
						if ( ! $arr['success'])
						{
							$msg .= "\n<br />uniq_id[". $arr['uniq_id'] ."]: ". $arr['msg'];
						}
					}
				}
				
				cron_log($msg);
			}
		}
		
		cron_end();
	
	break; // case 'import'
	
	case 'vscheck':
		
		if ($config['published_videos'] == 0)
		{
			cron_debug('No published videos were found.');
			cron_end();
		}
		
		update_cron_job_state($job, CRON_STATE_BUSY);
		
		include_once(ABSPATH . _ADMIN_FOLDER .'/functions-vscheck.php');
			
		$sources = a_fetch_video_sources();
		
		$sql_start = ($job['data']['sql_start']) ? $job['data']['sql_start'] : 0;
		$sql_limit = ( ! empty($job['data']['video_limit'])) ? (int) $job['data']['video_limit'] : 20;
		
		$yt_ids_arr = array();
		$while_loops = 0;
		$videos = array();
		$total_videos = 0;
		
		// we've exceeded total number of published videos
		// or the previous job is starting to take too long to finish
		// => job finished. 
		if ($sql_start >= $config['published_videos'] 
//				|| ($job['data']['time_started'] > 0 && ($time_now - $job['data']['time_started']) > ($job['exec_frequency'] * 1.5) )
			)
		{
			if ($job['data']['videos_processed'] > 0)
			{
				cron_log('Checked '. pm_number_format($job['data']['videos_processed']) .' videos in '. time_since($job['data']['time_started'], true));
			}
			
			// reset
			$job['data']['sql_start'] =
			$job['data']['time_started'] =
			$job['data']['videos_processed'] =
			$job['data']['time_last_run'] =
			$job['data']['total_videos'] =
			$job['data']['progress'] = 0;
			$job['state'] = CRON_STATE_READY;
			update_cron_job($job);

			cron_end(); // record last_exec_time.
		}
		
		// record time started this multi-part job
		if ($sql_start == 0)
		{
			$job['data']['time_started'] = $time_now;
			update_cron_job($job);
		}
		
		// get video data batch
		do
		{
			$while_loops++;
			
			if ($sql_start >= $config['published_videos'])
			{
				continue;
			}
			
			switch ($job['data']['video_sorting'])
			{
				default:
				case 'most-viewed':
					$sql_order_by = ' ORDER BY site_views DESC ';
				break;
				
				case 'latest':
					$sql_order_by = ' ORDER BY id DESC ';
				break;
				
				case 'oldest':
					$sql_order_by = ' ORDER BY id ASC ';
				break;
			}
			
			$sql = "SELECT id, uniq_id, yt_id, url_flv, source_id, last_check, status 
					FROM pm_videos 
					WHERE added <= $time_now_minute
					  AND source_id IN (". $sources['other']['source_id'] .", 
					  					". $sources['youtube']['source_id'] .",
										". $sources['dailymotion']['source_id'] .",
										". $sources['vimeo']['source_id'] .")
					  AND status != ". VS_BROKEN ."
					$sql_order_by
					LIMIT $sql_start, $sql_limit";
		
			cron_debug($sql);
			
			if ( ! $result = mysql_query($sql))
			{
				cron_exit_on_error(sprintf('MySQL Error: %s <br />Line: %d', mysql_error(), __LINE__)); 
			}
									
			while ($row = mysql_fetch_assoc($result))
			{
				// filter out videos that have been checked in the last exec_frequency time frame
				if (($time_now - $row['last_check']) < $job['exec_frequency'] || $row['status'] == VS_BROKEN)
				{
					continue;
				}
				
				// group youtube videos
				if ($row['source_id'] == $sources['youtube']['source_id'])
				{
					$yt_ids_arr[] = $row['yt_id'];
				}
				
				$videos[ $row['id'] ] = $row;
			}
			mysql_free_result($result);
			
			$total_videos = count($videos);
			
			if ($total_videos < $sql_limit)
			{
				$sql_start += $sql_limit;
			}

		} while ($while_loops < 5 && $total_videos < $sql_limit); 
		
		$total_videos = count($videos);
		
		if ($total_videos == 0)
		{
			// move to the next 'page' 
			$job['data']['sql_start'] = $sql_start + $sql_limit;
			$job['data']['time_last_run'] = time();
			update_cron_job($job);
			
			cron_debug('No videos to process.');
			cron_end(false);
		}
		cron_debug('Total videos found: '. $total_videos);
		
		// start with YT, one api request for all
		if (count($yt_ids_arr) > 0)
		{
			if ($config['youtube_api_key'] != '')
			{
				define('PHPMELODY', true);
				include(ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/autoload.php');
				
				$google_client = new Google_Client();
				$google_client->setDeveloperKey($config['youtube_api_key']);
				$youtube_api = new PhpmelodyYouTube($google_client);
				
				$response = $youtube_api->pm_get_video_status( implode(',', $yt_ids_arr));
				
				if (is_array($response) && array_key_exists('error', $response))
				{
					cron_log('Youtube API error: '. $response['error']['message']);
				}
				else
				{
					foreach ($videos as $video_id => $video_data)
					{
						if ($video_data['source_id'] == $sources['youtube']['source_id'])
						{
							$videos[$video_id]['status'] = $response[ $video_data['yt_id'] ];
						}
					}
				}
				
				cron_debug('YT API response: ', $response);
			}
			else
			{
				cron_log('Missing Youtube API Key. You need to <a href="'. _URL .'/'. _ADMIN_FOLDER .'/settings.php?view=video">set an API key</a> to enable importing. <br />Current database value: <code>'. $config['youtube_api_key'] .'</code>.');
			}
		}
		
		// continue with the other sources
		$vscheck_calls = 0;
		foreach ($videos as $video_id => $video_data)
		{
			if ($video_data['source_id'] != $sources['youtube']['source_id'])
			{
				$vscheck = vscheck_get_video_status(array('video-data' => $video_data));
				$videos[$video_id]['status'] = $vscheck['status'];
				
				if ($vscheck['display_message'] != '' || $vscheck['api_message'] != '')
				{
					cron_debug($vscheck);
				}
				
				// avoid any 'per second' API rate limiters
				if ($vscheck_calls % 5 == 0)
				{
					sleep(1);
				}
				
				$vscheck_calls++;
			}
		}
		
		// update status
		foreach ($videos as $video_id => $video_data)
		{
			if ( ! vscheck_update_video_status($video_id, $video_data['status']))
			{
				cron_log(sprintf('MySQL Error: %s <br />Line: %d', mysql_error(), __LINE__));
			}
			
			if ($video_data['status'] == VS_BROKEN)
			{
				report_video($video_data['uniq_id'], '1', 'Video removed or made private.', 'PM Bot');
			}
		}
		
		cron_debug('$videos:', $videos);
		
		// move to the next 'page' 
		$job['data']['sql_start'] = $sql_start + $sql_limit;
		$job['data']['videos_processed'] += $total_videos;
		$job['data']['time_last_run'] = time();
		
		// calculate progress
		if ( ! $job['data']['total_videos'] || ($config['published_videos'] < $job['data']['total_videos']))
		{
			$sql = "SELECT COUNT(*) as total_videos 
					FROM pm_videos 
					WHERE added <= $time_now_minute
					  AND source_id IN (". $sources['other']['source_id'] .", 
					  					". $sources['youtube']['source_id'] .",
										". $sources['dailymotion']['source_id'] .",
										". $sources['vimeo']['source_id'] .")
					  AND status != ". VS_BROKEN;
			
			if ($result = @mysql_query($sql))
			{
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$job['data']['total_videos'] = $row['total_videos'];
			}
		}
		
		if ($job['data']['total_videos'] > 0)
		{
			$job['data']['progress'] = round(($job['data']['videos_processed'] * 100) / $job['data']['total_videos'], 2);
		}
		
		update_cron_job($job);

		cron_debug('Script execution time: <strong>' . get_exec_time(get_micro_time(), $exec_start) . '</strong> seconds.'); 
		
		cron_end(false); // don't update last_exec_time yet.
		
	break; // case 'vscheck'
	
	case 'sitemap':
	case 'video-sitemap':
		
		if ($config['published_videos'] == 0)
		{
			cron_debug('No published videos were found.');
			cron_end();
		}
		
		update_cron_job_state($job, CRON_STATE_BUSY);
		
		$last_options_used = sitemap_load_options();
		cron_debug('$last_options_used: ', $last_options_used);

		$job_defaults = array(
			'do' => 'map',
			'totalitems' => '',
			'start' => 0,
			'limit' => ($last_options_used['limit'] > 0) ? $last_options_used['limit'] : 50000,
			// video-sitemap:
			'tags' => ($last_options_used['media_keywords']) ? $last_options_used['media_keywords'] : '',
			'cats' => ($last_options_used['media_category']) ? $last_options_used['media_category'] : '',
			'pub' => ($last_options_used['item_pubDate']) ? $last_options_used['item_pubDate'] : '',
			'progress' => 0,
			'c' => 0,
			// sitemap: 
			'ping_google' => (isset($last_options_used['ping_google'])) ? $last_options_used['ping_google'] : 'no',
			'ping_bing' => (isset($last_options_used['ping_bing'])) ? $last_options_used['ping_bing'] : 'no',
		);

		if ( ! $job['data']['time_started'])
		{
			$job['data'] = $job_defaults;
			$job['data']['time_started'] = $time_now;
			$job['data']['time_last_run'] = $time_now;
			$job['data']['sql_added_time_limit'] = $time_now;
		}
		
		$url_params = $job['data'];
		$url_params['type'] = $job['type']; 
		$url_params['cron-key'] = $config['cron_secret_key'];
		
		$url = _URL .'/'. _ADMIN_FOLDER .'/sitemap.php';
		
		if ($job['data']['c'] <= $job['data']['totalitems'])
		{
			try {
				$response = \Httpful\Request::get($url .'?'. http_build_query($url_params))->withoutStrictSSL()->send();
			} catch (Exception $e) {
				cron_debug('$url:', $url .'?'. http_build_query($url_params), '$url_params:', $url_params, 'cron.php line: '. __LINE__);
				cron_exit_on_error('Error: '. $e->getMessage() ."\n<br />File: ". $e->getFile() ."\n<br />Line: ". $e->getLine());
			}
			
			cron_debug('Response:', $response);
			
			if ($response->code != 200)
			{
				if ($job['data']['get_request_attempts'] <= 10)
				{
					$job['data']['get_request_attempts']++;
					cron_end(false);
				}
				else
				{ 
					cron_exit_on_error('Your server responded with a <code>'. $response->code .'</code> multiple times. <br />Try again in a few minutes.');
				}
			}
			else
			{
				unset($job['data']['get_request_attempts']);
			}
			
			$body = json_decode($response->raw_body, true);
		}
		else
		{
			// avoid infinite loops
			$body['state'] = 'finished';
		}
		cron_debug('$body: ', $body);
		
		$job['data']['state'] = $body['state'];
		
		switch ($body['state'])
		{
			case 'processing':
			case 'error':
				
				$job['data']['start'] = $body['start'];
				$job['data']['limit'] = $body['limit'];
				$job['data']['progress'] = round($body['progress'], 2);
				$job['data']['c'] = $body['c'];
				$job['data']['totalitems'] = $body['totalitems'];
				$job['data']['time_last_run'] = time();
				$job['data']['videos_processed'] = $body['start']; // to display in 'View Log'
				
				if ($body['state'] == 'processing')
				{
					update_cron_job($job);
					cron_end(false); // don't update last_exec_time yet.
				}
				else
				{
					cron_exit_on_error($body['msg']);
				}
				
			break;
			
			case 'finished':
				
				// log how much it took to complete
				cron_log('Sitemap updated successfully!<br />The process took '. time_since($job['data']['time_started'], true) .' to complete.');
				
				// reset job
				$job['data']['time_started'] =
				$job['data']['time_last_run'] =
				$job['data']['sql_added_time_limit'] =
				$job['data']['progress'] = 0;
				
				update_cron_job($job);
				
				// rename XML files from .xml.tmp to .xml
				$file_ext = '.xml.tmp';
				$files = array();
				$sitemap_basepath = ABSPATH . _UPFOLDER .'/'; // with trailing slash
				
				// sitemap index files
				if ($job['type'] == 'video-sitemap')
				{
					$files[] = ABSPATH . 'video-sitemap-index'. $file_ext;
				}
				else
				{
					$files[] = ABSPATH . 'sitemap-index'. $file_ext;
					$files[] = $sitemap_basepath . 'sitemap-base'. $file_ext;
				}
				
				// sitemap files
				if ($body['total_files_created'] > 0)
				{
					for ($i = 1; $i <= $body['total_files_created']; $i++)
					{
						if ($job['type'] == 'video-sitemap')
						{
							$files[] = $sitemap_basepath . 'video-sitemap-'. $i . $file_ext;
						}
						else
						{
							$files[] = $sitemap_basepath . 'sitemap-'. $i . $file_ext;
						}
					}
					
					// in case 'finished' case was forced some .tmp.xml files won't make it in $files[] so we need to scan the directory
					if ($dir = opendir($sitemap_basepath))
					{
						while (false !== ($f = readdir($dir)))
						{
							if (strpos($f, $file_ext) !== false && ! in_array($sitemap_basepath . $f, $files))
							{
								$files[] = $sitemap_basepath . $f;
							}
						}
					}
				}
				
				cron_debug('$files: ', $files);
				
				foreach ($files as $k => $file_path)
				{
					if (file_exists($file_path))
					{
						rename($file_path, str_replace($file_ext, '.xml', $file_path));
					}
				}
				
			break;
		}
		
		cron_debug('Script execution time: <strong>' . get_exec_time(get_micro_time(), $exec_start) . '</strong> seconds.');
		cron_end();
		
	break; // case 'sitemap' and 'video-sitemap'
}

exit();