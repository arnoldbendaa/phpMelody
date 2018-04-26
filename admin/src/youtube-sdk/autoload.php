<?php
require_once ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/src/Google/autoload.php';
require_once ABSPATH . _ADMIN_FOLDER .'/src/youtube-sdk/src/Google/Service/YouTube.php';

@chmod(dirname(__FILE__) .'/src/Google/IO', 0755); // @since v2.7 to enable cURL to update the certificate when needed

/**
 * PHP Melody classes, functions, etc extending Youtube's SDK
 */
class PhpmelodyYoutube extends Google_Service_YouTube
{
	// user/channel related data
	public $pm_channel_id;
	public $pm_channel_title;
	public $pm_uploads_playlist_id;
	public $pm_favorites_playlist_id;
	public $pm_likes_playlist_id;
	public $pm_watch_later_playlist_id;
	public $pm_watch_history_playlist_id;

	/**
	 * Search videos and format data
	 *
	 * @param string $keywords Search keywords
	 * @param array $args [optional] search filters, options, etc.
	 * @return array of formatted data ready to display on results page
	 */
	public function pm_search($keywords, $args = array(), $meta_only = false)
	{
		$google_client = $this->getClient();
		$google_client->setClassConfig('Google_Cache_File', array('directory' => ABSPATH . _ADMIN_FOLDER .'/'. BKUP_DIR));

		// split longer search requests in multiple queries
		$max_results = 0;
		$search_multipart = false;
		$search_parts = -1;

		if ($args['per_page'] <= 50)
		{
			$max_results = $args['per_page'];
		}
		else
		{
			$search_multipart = true;
			$search_parts = floor($args['per_page'] / 50);
			$max_results = 50;
		}

		$args['search_orderby'] = ($args['search_orderby'] == 'published') ? 'date' : $args['search_orderby'];

		$search_params = array(
			'q' 				=> $keywords,
			'type' 				=> 'video',
			'videoEmbeddable'	=> 'true',
			'maxResults' 		=> $max_results,
			'pageToken' 		=> ($args['page'] != '' && ! is_int($args['page']) && ! ctype_digit($args['page'])) ? $args['page'] : null,
			'videoCategoryId' 	=> ($args['search_category'] != '' && $args['search_category'] != 'all') ? $this->pm_category_v2_to_v3($args['search_category']) : null,
			'order' 			=> (in_array($args['search_orderby'], array('relevance', 'date', 'viewCount', 'rating'))) ? $args['search_orderby'] : null,
			'videoDuration' 	=> (in_array($args['search_duration'], array('short', 'medium', 'long'))) ? $args['search_duration'] : null,
			'videoLicense' 		=> ($args['search_license'] != '' && $args['search_license'] != 'all') ? str_replace('cc', 'creativeCommon', $args['search_license']) : null,
			'videoDefinition' 	=> ($args['search_hd'] == 'true') ? 'high' : null,
			'videoDimension'	=> ($args['search_3d'] == 'true') ? '3d' : null,
			'regionCode'		=> ($args['search_region'] != '' && $args['search_region'] != 'all') ? $args['search_region'] : null,
			'relevanceLanguage'	=> ($args['search_language'] != '' && $args['search_language'] != 'all') ? $args['search_language'] : null
		);

		if (in_array($args['search_time'], array('today', 'this_week', 'this_month'/*, 'all_time'*/)))
		{
			$date = getdate();
			switch ($args['search_time'])//($args['search_time'])
			{
				case 'today':
					$search_params['publishedAfter'] = date("Y-m-d\TH:i:sP", mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']));
					break;

				case 'this_week':
					$search_params['publishedAfter'] = date("Y-m-d\TH:i:sP", mktime(0, 0, 0, $date['mon'], $date['mday'] - $date['wday'], $date['year']));
					break;

				case 'this_month':
					$search_params['publishedAfter'] = date("Y-m-d\TH:i:sP", mktime(0, 0, 0, $date['mon'], 1, $date['year']));
					break;
			}
		}

		$calls = 0;
		$api_data = array();

		// get video ids first
		do {

			$video_ids = array();

			try {
				$result =  $this->search->listSearch('id', $search_params);

				if ($calls == 0)
				{
					$api_data = array(  'meta' => array('total_results' => (int) $result['pageInfo']['totalResults'],
														'page' => null,
														'prev_page' => $result['prevPageToken'],
														'next_page' => $result['nextPageToken'],
														'start' => null,
														'per_page' => (int) $result['pageInfo']['resultsPerPage']
													),
										'results' => array()
									);
					if ($meta_only) 
					{
						return $api_data;
					}
				}
				else
				{
					$api_data['meta']['next_page'] = $result['nextPageToken'];
				}

				if ($result['nextPageToken'] == null || $result['pageInfo']['totalResults'] <= 50)
				{
					$search_parts = -1;
				}
				else
				{
					$search_params['pageToken'] = $result['nextPageToken'];
				}

				foreach ($result['items'] as $item)
				{
					$video_ids[] = $item['id']['videoId'];
				}

			} catch (Google_ServiceException $e) { // youtube service error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			} catch (Google_Exception $e) { // google client error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			}

			unset($result);
			if (count($video_ids) == 0)
			{
				return $api_data;
			}

			// get details for all videos
			try {

				$result = $this->videos->listVideos('id,snippet,contentDetails,status', array('id' => implode(',', $video_ids)));
				$api_data['results'] = array_merge($api_data['results'], $this->pm_format_video_data($result));

			} catch (Google_ServiceException $e) { // youtube service error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			} catch (Google_Exception $e) { // google client error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			}

			$search_parts--;
			$calls++;

		} while ($search_parts > 0);

		return $api_data;
	}

	/**
	 * Get most popular videos in a category (optional) and/or region (optional)
	 *
	 * @param array $args [optional]
	 * @return array of formatted data
	 */
	public function pm_most_popular($args = array())
	{
		$google_client = $this->getClient();
		$google_client->setClassConfig('Google_Cache_File', array('directory' => ABSPATH . _ADMIN_FOLDER .'/'. BKUP_DIR));

		$max_results = 0;
		$search_multipart = false;
		$search_parts = -1;

		if ($args['per_page'] <= 50)
		{
			$max_results = $args['per_page'];
		}
		else
		{
			$search_multipart = true;
			$search_parts = floor($args['per_page'] / 50);
			$max_results = 50;
		}

		$search_params = array(
			'chart' 			=> 'mostPopular',
			'maxResults' 		=> $max_results,
			'pageToken' 		=> ($args['page'] != '' && ! is_int($args['page']) && ! ctype_digit($args['page'])) ? $args['page'] : null,
			'videoCategoryId' 	=> ($args['search_category'] != '' && $args['search_category'] != 'all') ? $this->pm_category_v2_to_v3($args['search_category']) : null,
			'regionCode'		=> ($args['search_region'] != '' && $args['search_region'] != 'all') ? $args['search_region'] : null,
		);

		$calls = 0;
		$api_data = array();

		do {
			try {

				$result = $this->videos->listVideos('id,snippet,contentDetails,status', $search_params);
				
				if ($calls == 0)
				{
					$api_data = array(  'meta' => array('total_results' => (int) $result['pageInfo']['totalResults'],
														'page' => null,
														'prev_page' => $result['prevPageToken'],
														'next_page' => $result['nextPageToken'],
														'start' => null,
														'per_page' => (int) $result['pageInfo']['resultsPerPage']
													),
										'results' => array()
								);
				}
				else
				{
					$api_data['meta']['next_page'] = $result['nextPageToken'];
				}

				if ($result['nextPageToken'] == null || $result['pageInfo']['totalResults'] <= 50)
				{
					$search_parts = -1;
				}
				else
				{
					$search_params['pageToken'] = $result['nextPageToken'];
				}

				$api_data['results'] = array_merge($api_data['results'], $this->pm_format_video_data($result));

			} catch (Google_ServiceException $e) { // youtube service error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			} catch (Google_Exception $e) { // google client error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			}

			$search_parts--;
			$calls++;

		} while ($search_parts > 0);

		return $api_data;
	}

	/**
	 * Get on or more video's details 
	 * 
	 * @param string $yt_id the youtube video id or multiple comma-separated youtube video ids
	 * @return array of formatted data
	 */
	public function pm_get_video($yt_id = '')
	{
		$google_client = $this->getClient();
		$google_client->setClassConfig('Google_Cache_File', array('directory' => ABSPATH . _ADMIN_FOLDER .'/'. BKUP_DIR));

		try {
			$result = $this->videos->listVideos('id,snippet,contentDetails,status', array('id' => $yt_id));

			if ($result['pageInfo']['totalResults'] == 0
				|| $result['items'][0]['status']['uploadStatus'] == 'deleted')
			{
				return array('error' => array('message' => 'Video not found', 'code' => 404));
			}
			
			if ($result['items'][0]['status']['uploadStatus'] == 'rejected')
			{
				return array('error' => array('message' => 'This video was rejected for violating the terms of use.', 'code' => 404));
			}

			$video_data = $this->pm_format_video_data($result);

			return $video_data;

		} catch (Google_ServiceException $e) { // youtube service error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		} catch (Google_Exception $e) { // google client error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		}

		return array();
	}

	/**
	 * Get/check video(s) status. 
	 * 
	 * @param string $yt_id the youtube video id(s), comma separated
	 * @return array as (yt_id => VS_* constant)
	 */
	public function pm_get_video_status($yt_id = '')
	{
		$google_client = $this->getClient();
		$google_client->setClassConfig('Google_Cache_File', array('directory' => ABSPATH . _ADMIN_FOLDER .'/'. BKUP_DIR));
		
		$yt_id_arr = explode(',', $yt_id);
		$yt_id_count = count($yt_id_arr);
		
		$status = array();
		
		try {
			$result = $this->videos->listVideos('status,contentDetails', array('id' => $yt_id));
			
			$total_results = $result['pageInfo']['totalResults'];
			
			for ($i = 0; $i < $total_results; $i++)
			{
				if ($result['items'][$i]['status']['uploadStatus'] == 'deleted'
					|| $result['items'][$i]['status']['uploadStatus'] == 'rejected'
					|| $result['items'][$i]['status']['embeddable'] == false 
					|| $result['items'][$i]['status']['privacyStatus'] == 'private')
				{
					$status[ $result['items'][$i]['id'] ] = VS_BROKEN;
				}
				else if (is_object($result['items'][$i]['contentDetails']['regionRestriction']))
				{
					$status[ $result['items'][$i]['id'] ] = VS_RESTRICTED;
				}
				else
				{
					$status[ $result['items'][$i]['id'] ] = VS_OK;
				}
			}
			
			if ($total_results < $yt_id_count)
			{
				for ($i = 0; $i < $yt_id_count; $i++)
				{
					if ( ! array_key_exists($yt_id_arr[$i], $status))
					{
						$status[ $yt_id_arr[$i] ] = VS_BROKEN;
					}
				}
			}
			
			return $status;
			
		} catch (Google_ServiceException $e) { // youtube service error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		} catch (Google_Exception $e) { // google client error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		}

		return false;
	}
	

	/**
	 * USE pm_user_playlists() and/or pm_playlist()
	 *
	 * @param string $feed_type [optional]
	 * @param string $id username or playlist id
	 * @param array $args [optional]
	 * @return array of formatted data
	 */
	public function pm_user_feed($feed_type = 'videos', $id, $args = array())
	{
		return;
	}

	/**
	 *  Just a wrapper for pm_playlist()
	 */
	public function pm_user_videos($username, $args = array())
	{
		return $this->pm_playlist($this->pm_uploads_playlist_id, $args);
	}

	/**
	 * Just a wrapper for pm_playlist()
	 */
	public function pm_user_favorites($username, $args = array())
	{
		if ($this->pm_favorites_playlist_id != null)
		{
			return $this->pm_playlist($this->pm_favorites_playlist_id, $args);
		}

		$api_data = array('meta' => array('total_results' => 0),
						  'results' => array()
					);

		return $api_data;
	}

	/**
	 * Retrieve videos from a playlist
	 *
	 * @param $playlist_id ID of the playlist
	 * @param array $args
	 * @return array $api_data
	 */
	public function pm_playlist($playlist_id, $args = array(), $detailed_video_data = true)
	{
		$google_client = $this->getClient();
		$google_client->setClassConfig('Google_Cache_File', array('directory' => ABSPATH . _ADMIN_FOLDER .'/'. BKUP_DIR));

		try {

			$api_params = array(
				'playlistId' => $playlist_id,
				'maxResults' => (int) $args['per_page'],
				'pageToken' => ($args['page'] != '' && ! is_int($args['page']) && ! ctype_digit($args['page'])) ? $args['page'] : null,
			);

			// get video ids first
			$result = $this->playlistItems->listPlaylistItems('id,contentDetails', $api_params);

			$video_ids = array();

			$api_data = array(  'meta' => array('total_results' => (int) $result['pageInfo']['totalResults'],
												'page' => null,
												'prev_page' => $result['prevPageToken'],
												'next_page' => $result['nextPageToken'],
												'start' => null,
												'per_page' => (int) $result['pageInfo']['resultsPerPage']
											),
								'results' => array()
						);


			foreach ($result['items'] as $item)
			{
				$video_ids[] = $item['contentDetails']['videoId'];
			}

			// get video details
			try {

				$part_param = ($detailed_video_data) ? 'id,snippet,contentDetails,status' : 'id,snippet';
				$result = $this->videos->listVideos($part_param, array('id' => implode(',', $video_ids)));

				$api_data['results'] = $this->pm_format_video_data($result, $detailed_video_data);

			} catch (Google_ServiceException $e) { // youtube service error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			} catch (Google_Exception $e) { // google client error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			}

		} catch (Google_ServiceException $e) { // youtube service error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		} catch (Google_Exception $e) { // google client error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		}

		return $api_data;
	}

	/**
	 * Get user playlists and format data
	 *
	 * @param string $username
	 * @param array $args [optional]
	 * @return array of formatted data
	 */
	public function pm_user_playlists($username, $args = array())
	{
		$google_client = $this->getClient();
		$google_client->setClassConfig('Google_Cache_File', array('directory' => ABSPATH . _ADMIN_FOLDER .'/'. BKUP_DIR));

		$api_data = array();

		// get channel id
		if ( ! $this->pm_channel_id)
		{
			try {
				
				if ($args['pm-user-type'] == 'channel')
				{
					$result = $this->channels->listChannels('id,contentDetails,brandingSettings', array('id' => $username));
				}
				else
				{
					$result = $this->channels->listChannels('id,contentDetails', array('forUsername' => $username));
				}

				if ($result['pageInfo']['totalResults'] == 0)
				{
					$error_message = ($args['pm-user-type'] == 'channel') ? 'Channel not found' : 'User not found';
					return array('error' => array('message' => '<strong>'. $error_message .'</strong>'));
				}
					
				// save channel id it for later use
				$this->pm_channel_id = $result['items'][0]['id'];
				$this->pm_channel_title = $result['items'][0]['brandingSettings']['channel']['title'];
				$this->pm_uploads_playlist_id = $result['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
				$this->pm_favorites_playlist_id = $result['items'][0]['contentDetails']['relatedPlaylists']['favorites'];
				$this->pm_likes_playlist_id = $result['items'][0]['contentDetails']['relatedPlaylists']['likes'];
				$this->pm_watch_history_playlist_id = $result['items'][0]['contentDetails']['relatedPlaylists']['watchHistory'];
				$this->pm_watch_later_playlist_id = $result['items'][0]['contentDetails']['relatedPlaylists']['watchLater'];

			} catch (Google_ServiceException $e) { // youtube service error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			} catch (Google_Exception $e) { // google client error
				return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
			}
		}

		// get all other playlists
		try {
			$search_params = array(	'channelId' => $this->pm_channel_id, 
									'pageToken' => ($args['page'] != '' && ! is_int($args['page']) && ! ctype_digit($args['page'])) ? $args['page'] : null,
									'maxResults' => 50
								);
			
			$result = $this->playlists->listPlaylists('id,snippet', $search_params);

			$api_data = array('meta' => array(  'total_results' => (int) $result['pageInfo']['totalResults'],
												'prev_page' => $result['prevPageToken'],
												'next_page' => $result['nextPageToken'],
												'per_page' => (int) $result['pageInfo']['resultsPerPage']
											),
							  'results' => array()
						);
			$i = 0;

			// push 'Liked videos' playlist before others
			// if ($this->pm_likes_playlist_id != null)
			// {
			// 	$likes_playlist = array(
			// 		'id' => $this->pm_likes_playlist_id,
			// 		'title' => 'Liked videos',
			// 		'description' => '',
			// 		'total_thumbs' => 0,
			// 		'playlist_thumb_url' => '',
			// 		'total_videos' => null,
			// 		'user_avatar_url' => null
			// 	);
			// 	$api_data['meta']['total_results']++;
			// 	$api_data['results'][$i] = $likes_playlist;
			// 	$i++;
			// }

			foreach ($result['items'] as $item)
			{
				$tmp = array();

				$tmp['id'] = str_replace('/users/', '', $item['id']);
				$tmp['title'] = $item['snippet']['title'];
				$tmp['description'] = $item['snippet']['description'];

				$tmp['total_thumbs'] = 1;
				$tmp['thumbs'] = array(0 => array('original' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['maxres']['url']),
													'small' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['default']['url']),
													'medium' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['medium']['url']),
													'large' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['high']['url']),
												)
								);
				
				$tmp['playlist_thumb_url'] = $tmp['thumbs'][0]['medium'];
				$tmp['total_videos'] = null;
				$tmp['user_avatar_url'] = null;

				$api_data['results'][$i] = $tmp;

				$i++;
			}

		} catch (Google_ServiceException $e) { // youtube service error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> ' . $e->getMessage()));
		} catch (Google_Exception $e) { // google client error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> ' . $e->getMessage()));
		}

		return $api_data;
	}

	/**
	 * Format Youtube videos.listVideos type of response; excluding the 'meta' part of our $api_data
	 * 
	 * @param array $result is the API's videos.listVideos response, including "items".
	 * @param bool $detailed_video_data true to get all details or false to get just the most important
	 *
	 * @return array the $api_data['results'] part
	 */
	public function pm_format_video_data($result, $detailed_video_data = true)
	{

		$data = array();

		foreach ($result['items'] as $item)
		{
			$tmp = array();

			$id = $item['id'];

			$tmp['id'] = $id;
			$tmp['title'] = $item['snippet']['title'];
			$tmp['description'] = $item['snippet']['description'];

			$tmp['total_thumbs'] = 3; 
		
			$tmp['thumbs'] = array( 0 => array('original' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['maxres']['url']),
												'small' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['default']['url']),
												'medium' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['medium']['url']),
												'large' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['high']['url']),
												'extra-large' => str_replace(array('http://', 'https://'), '//', $item['snippet']['thumbnails']['standard']['url']),
											),
									1 => array('original' => '//img.youtube.com/vi/'. $id .'/2.jpg',
												'small' => '//img.youtube.com/vi/'. $id .'/2.jpg',
												'medium' => '//img.youtube.com/vi/'. $id .'/2.jpg',
												'large' => '//img.youtube.com/vi/'. $id .'/2.jpg',
												'extra-large' => '//img.youtube.com/vi/'. $id .'/2.jpg',
												
										),
									2 => array('original' => '//img.youtube.com/vi/'. $id .'/3.jpg',
												'small' => '//img.youtube.com/vi/'. $id .'/3.jpg',
												'medium' => '//img.youtube.com/vi/'. $id .'/3.jpg',
												'large' => '//img.youtube.com/vi/'. $id .'/3.jpg',
												'extra-large' => '//img.youtube.com/vi/'. $id .'/3.jpg',
										),
			);
			// 'maxres' and 'standard' are not always present
			$tmp['thumbs'][0]['original'] = ($tmp['thumbs'][0]['original'] == '') ? $tmp['thumbs'][0]['large'] : $tmp['thumbs'][0]['original'];
			$tmp['thumbs'][0]['extra-large'] = ($tmp['thumbs'][0]['extra-large'] == '') ? $tmp['thumbs'][0]['large'] : $tmp['thumbs'][0]['extra-large'];
			
			$tmp['publish_date'] = $item['snippet']['publishedAt'];
			$tmp['publish_date_timestamp'] = strtotime($item['snippet']['publishedAt']);
			$tmp['keywords'] = '';
			$tmp['url'] = 'https://www.youtube.com/watch?v='. $id;
			$tmp['embed_url'] = 'https://www.youtube.com/v/'. $id .'?&autoplay=1&v='. $id .'&version=3';
			$tmp['url_flv'] = '';
			$tmp['mp4'] = '';

			if ($detailed_video_data)
			{
				$duration = 0;
				if ($item['contentDetails']['duration'])
				{
					if (class_exists('DateInterval'))
					{
						$duration = new DateInterval($item['contentDetails']['duration']);
						$duration = $duration->s + ($duration->i * 60) + ($duration->h * 3600);
					}
					else
					{
						$duration = $this->get_duration_seconds($item['contentDetails']['duration']);
					}
				}

				$tmp['duration'] = (int) $duration;
				$tmp['embeddable'] = $item['status']['embeddable'];
				$tmp['geo-restriction'] = null;

				if (is_object($item['contentDetails']['regionRestriction']))
				{
					$regionRestriction = $item['contentDetails']['regionRestriction'];
					if ($regionRestriction['blocked'] != null) // denied to
					{
						$tmp['geo-restriction'] = array('type' => 'deny', 'list' => (is_array($regionRestriction['blocked'])) ? implode(', ', $regionRestriction['blocked']) : $regionRestriction['blocked']);
					}
					else // allowed only
					{
						$tmp['geo-restriction'] = array('type' => 'allow', 'list' => (is_array($regionRestriction['allowed'])) ? implode(', ', $regionRestriction['allowed']) : $regionRestriction['allowed']);
					}
					unset($regionRestriction);
				}


				$tmp['private'] = ($item['status']['privacyStatus'] == 'private') ? true : null;
				$tmp['upload_status'] = $item['status']['uploadStatus'];
			}

			$data[] = $tmp;
		}

		return $data;
	}

	/**
	 * Get user avatar url
	 *
	 * @param string $username
	 * @return string url
	 */
	public function pm_get_user_avatar_url($username, $args = array())
	{
		$google_client = $this->getClient();
		$google_client->setClassConfig('Google_Cache_File', array('directory' => ABSPATH . _ADMIN_FOLDER .'/'. BKUP_DIR));

		try {
			
			if ($args['pm-user-type'] == 'channel')
			{
				$result = $this->channels->listChannels('id,snippet', array('id' => $username));
			}
			else
			{
				$result = $this->channels->listChannels('id,snippet', array('forUsername' => $username));
			}
			
			if ($result['pageInfo']['totalResults'] == 0)
			{
				$error_message = ($args['pm-user-type'] == 'channel') ? 'Channel not found' : 'User not found';
				return array('error' => array('message' => '<strong>'. $error_message .'</strong>'));
			}

			// save channel id it for later use
			$this->pm_channel_id = $result['items'][0]['id'];

			return $result['items'][0]['snippet']['thumbnails']['default']['url'];

		} catch (Google_ServiceException $e) { // youtube service error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		} catch (Google_Exception $e) { // google client error
			return array('error' => array('message' => '<strong>Youtube API error:</strong> '. $e->getMessage()));
		}

		return '';
	}

	/**
	 * Converts API v2 category id to API v3 equivalent or closest
	 *
	 * @param string $category_id old or new category id/key
	 * @return string v3 category id
	 */
	public function pm_category_v2_to_v3($category_id = '')
	{
		$category_id = strtolower($category_id);

		$category_map = array( // v2 id => v3 id
							'autos' => '2',
							'comedy' => '23',
							'education' => '27',
							'entertainment' => '24',
							'film' => '1',
							'games' => '20',
							'howto' => '26',
							'music' => '10',
							'news' => '25',
							'nonprofit' => '29',
							'people' => '22',
							'animals' => '15',
							'tech' => '28',
							'sports' => '17',
							'travel' => '19',
						);

		if (array_key_exists($category_id, $category_map))
		{
			return $category_map[$category_id];
		}

		return $category_id;
	}


	/**
	 * Parse ISO 8601 duration
	 *
	 * @param string $iso_duration ISO 8601 formatted duration (e.g. PT15M51S = 15 minutes and 51 seconds)
	 * @param bool $allow_negative
	 * @return bool|array duration as array (year, month, day, hour, minute, second)
	 */
	public function parse_duration($iso_duration, $allow_negative = true)
	{
		// Parse duration parts
		$matches = array();
		preg_match('/^(-|)?P([0-9]+Y|)?([0-9]+M|)?([0-9]+D|)?T?([0-9]+H|)?([0-9]+M|)?([0-9]+S|)?$/', $iso_duration, $matches);

		if ( ! empty($matches))
		{
			// Strip all but digits and -
			foreach($matches as &$match){
				$match = preg_replace('/((?!([0-9]|-)).)*/', '', $match);
			}
			// Fetch min/plus symbol
			$result['symbol'] = ($matches[1] == '-') ? $matches[1] : '+'; // May be needed for actions outside this function.
			// Fetch duration parts
			$m = ($allow_negative) ? $matches[1] : '';
			$result['year']   = intval($m.$matches[2]);
			$result['month']  = intval($m.$matches[3]);
			$result['day']    = intval($m.$matches[4]);
			$result['hour']   = intval($m.$matches[5]);
			$result['minute'] = intval($m.$matches[6]);
			$result['second'] = intval($m.$matches[7]);
			return $result;
		}

		return false;
	}

	/**
	 * Parse ISO 8601 duration to seconds
	 *
	 * @param string $iso_duration ISO 8601 formatted duration (e.g. PT15M51S = 15 minutes and 51 seconds)
	 * @return bool|int number of seconds, false on failure
	 */
	public function get_duration_seconds($iso_duration)
	{
		// Get duration parts
		$duration = $this->parse_duration($iso_duration, false);
		if ($duration)
		{
			extract($duration);
			$dparam  = $symbol; // plus/min symbol
			$dparam .= (!empty($year)) ? $year . 'Year' : '';
			$dparam .= (!empty($month)) ? $month . 'Month' : '';
			$dparam .= (!empty($day)) ? $day . 'Day' : '';
			$dparam .= (!empty($hour)) ? $hour . 'Hour' : '';
			$dparam .= (!empty($minute)) ? $minute . 'Minute' : '';
			$dparam .= (!empty($second)) ? $second . 'Second' : '';
			$date = '19700101UTC';
			return strtotime($date.$dparam) - strtotime($date);
		}

		return false;
	}
}
