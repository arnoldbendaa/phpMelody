<?php
require_once ABSPATH . _ADMIN_FOLDER .'/src/dailymotion-sdk/Dailymotion.php';

@chmod(dirname(__FILE__), 0755); // @since v2.7 to enable cURL to update the certificate when needed

/**
 * PHP Melody classes, functions, etc extending Dailymotion's SDK
 */
class PhpmelodyDailymotion extends Dailymotion
{
	
	public $video_object_fields = array('id', 'title', 'description', 'duration', 
										'thumbnail_url', 
										//'thumbnail_60_url', // 80x60
										//'thumbnail_120_url',  // 160x120
										'thumbnail_180_url',  // 240x180
										'thumbnail_240_url',  // 320x240
										//'thumbnail_360_url',  // 480x360
										'thumbnail_480_url', // 640x480
										'thumbnail_720_url', // 960x720
										'url', 'embed_url', 'tags', 'explicit', 'access_error', 'allow_embed', 'geoblocking', 'mediablocking', 
										'paywall', 'private', 'svod', 'tvod', 'created_time' //'channel'
										);
	/**
	 * Search videos and format data
	 * 
	 * @param string $keywords Search keywords
	 * @param array $args [optional] search filters, options, etc.
	 * @return array of formatted data ready to display on results page
	 */
	public function pm_search($keywords, $args = array())
	{
		if ( ! array_key_exists('page', $args) || ! $args['page'])
		{
			$args['page'] = 1;
		}
		if ( ! array_key_exists('per_page', $args) || ! $args['per_page'])
		{
			$args['per_page'] = 20;
		}


		$filters = '&flags=no_live,no_premium';
		$filters .= ($args['search_hd']) ? ',hd' : '';
		$filters .= ($args['search_3d']) ? ',3d' : '';
		
		$filters .= ($args['search_category'] != '' && $args['search_category'] != 'all') ? '&channel='. $args['search_category'] : '';
		$filters .= ( ! empty($args['search_language']) && $args['search_language'] != 'all') ? '&languages='. $args['search_language'] : '';
		
		switch ($args['search_duration'])
		{
			case 'short': // ~4 minutes
				$filters .= '&shorter_than=4';
			break;
			
			case 'medium': // 4-20 minutes
				$filters .= '&shorter_than=20&longer_than=4';
			break; 
			
			case 'long': // 20+ minutes
				$filters .= '&longer_than=20';
			break;
		}
		
		if ($args['search_region'] != '')
		{
			$filters .= '&country='. $args['search_region'];
		}
		
		switch ($args['search_time'])
		{
			case 'today':
				$date = getdate();
				$filters .= '&created_after='. mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
			break;
			
			case 'this_week':
				$date = getdate();
				$filters .= '&created_after='. mktime(0, 0, 0, $date['mon'], $date['mday'] - $date['wday'], $date['year']);
			break;
			
			case 'this_month': 
				$date = getdate();
				$filters .= '&created_after='. mktime(0, 0, 0, $date['mon'], 1, $date['year']);
			break;
		}
		
		switch ($args['search_orderby'])
		{
			default:
			case 'relevance':
				$filters .= '&sort=relevance';
			break;

			case 'date':
			case 'published':
				$filters .= '&sort=recent';
			break;
			
			case 'viewCount': // visited
				$filters .= '&sort=visited';
			break;
			
			case 'rating':
				$filters .= '&sort=rated'; 
			break;
		}

		$result = $this->get('/videos?search='. urlencode($keywords) .'&page='. $args['page'] .'&limit='. $args['per_page'] . $filters,
							 array('fields' => $this->video_object_fields)
						);
		
		if ( ! empty($result['error']))
		{
			return $result;
		}
		
		return $this->pm_format_video_data($result);
	}
	
	
	/**
	 * Get data for user's uploaded videos, favorites and a certain playlist and format it to our needs
	 * 
	 * @param string $feed_type [optional]
	 * @param string $id username or playlist id
	 * @param array $args [optional]
	 * @return array of formatted data
	 */
	public function pm_user_feed($feed_type = 'videos', $id, $args = array())
	{
		if ( ! array_key_exists('page', $args) || ! $args['page'])
		{
			$args['page'] = 1;
		}
		if ( ! array_key_exists('per_page', $args) || ! $args['per_page'])
		{
			$args['per_page'] = 20;
		}
		
		
		$filters = '';
		
		switch ($args['search_orderby'])
		{
			default:
			case 'published': // recent
				$filters .= '&sort=recent';
			break;
		}
		
		switch ($args['search_time'])
		{
			case 'today':
				$date = getdate();
				$filters .= '&created_after='. mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
			break;
			
			case 'this_week':
				$date = getdate();
				$filters .= '&created_after='. mktime(0, 0, 0, $date['mon'], $date['mday'] - $date['wday'], $date['year']);
			break;
			
			case 'this_month': 
				$date = getdate();
				$filters .= '&created_after='. mktime(0, 0, 0, $date['mon'], 1, $date['year']);
			break;
		}
		
		switch ($feed_type)
		{
			default:
			case 'videos':
				$api_resource = '/user/'. urlencode($id) .'/videos';
			break;
			
			case 'favorites':
				$api_resource = '/user/'. urlencode($id) .'/favorites';
			break;
			
			case 'playlist':
				 $api_resource = '/playlist/'. urlencode($id) .'/videos';
			break;
		}
		
		$result = $this->get($api_resource .'?page='. $args['page'] .'&limit='. $args['per_page'] . $filters,
							array('fields' => $this->video_object_fields)
					);

		if ( ! empty($result['error']))
		{
			return $result;
		}		

		return $this->pm_format_video_data($result);
	}

	/**
	 *  Two requests: one to get the total number of public videos uploaded by the user and the other to get the videos
	 */
	public function pm_user_videos($username, $args = array())
	{
		try {
			$user = $this->get('/user/'. urlencode($username),
				array('fields' => array('videos_total'))
			);
			$user['videos_total'] = (int) $user['videos_total'];
		} catch(DailymotionApiException $e) {}

		$items = $this->pm_user_feed('videos', $username, $args);

		if ($user['videos_total'] > 0)
		{
			$items['meta']['total_results'] = $user['videos_total'];
		}

		return $items;
	}
	
	/**
	 * Just a wrapper for pm_user_feed() 
	 */
	public function pm_user_favorites($username, $args = array()) 
	{
		return $this->pm_user_feed('favorites', $username, $args);
	}
	
	/**
	 * Just a wrapper for pm_user_feed() 
	 */
	public function pm_playlist($playlist_id, $args = array()) 
	{
		return $this->pm_user_feed('playlist', $playlist_id, $args);
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
		if ( ! array_key_exists('page', $args) || ! $args['page'])
		{
			$args['page'] = 1;
		}
		if ( ! array_key_exists('per_page', $args) || ! $args['per_page'])
		{
			$args['per_page'] = 20;
		}
		$filters = '&sort=recent';

		$result = $this->get('/user/'. urlencode($username) .'/playlists' .'?page='. $args['page'] .'&limit='. $args['per_page'] . $filters,
							array('fields' => array('id', 'name', 'description', 'owner.avatar_120_url', 'thumbnail_url', 'thumbnail_180_url', 'thumbnail_240_url', 'thumbnail_480_url', 'thumbnail_720_url', 'videos_total'))
					);
					
		if ( ! empty($result['error']))
		{
			return $result;
		}

		$normalized = array('meta' => array('total_results' => (int) $result['total'],
											'page' => $result['page'],
											'start' => (($result['page'] * $result['limit']) - 1),
											'per_page' => $result['limit']
										),
							'results' => array()
						);
		$i = 0;
		foreach ($result['list'] as $k => $item)
		{
			$tmp = array();
			$tmp = $item;
			
			$tmp['url'] = 'http://www.dailymotion.com/playlist/'. $item['id'];
			$tmp['title'] = $item['name']; 
			
			$tmp['total_thumbs'] = 1;
			$tmp['thumbs'] = array(0 => array('original' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_url']),
											  'small' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_180_url']),
											  'medium' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_240_url']),
											  'large' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_480_url']),
											  'extra-large' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_720_url']),
											)
								);
			$tmp['playlist_thumb_url'] = $item['thumbnail_240_url'];
			$tmp['total_videos'] = $item['total_videos'];
			
			$tmp['user_avatar_url'] = $item['owner.avatar_120_url'];
			$tmp['user_id'] = null;
			
			$normalized['results'][$i] = $tmp;
			$i++;
		}
		
		return $normalized;
	}
	
	
	public function pm_format_video_data($result)
	{
		// Normalize data for our form
		$normalized = array('meta' => array('total_results' => (int) $result['total'],
											'page' => $result['page'],
											'start' => (($result['page'] * $result['limit']) - 1),
											'per_page' => $result['limit']
										),
							'results' => array()
						);
		$i = 0;

		foreach ($result['list'] as $k => $item)
		{
			if (/*$item['allow_embed'] == false || */$item['paywall'] || $item['private'] || $item['svod'] || $item['tvod'] 
				|| ! empty($item['access_error']))
			{
				continue;
			}
			
			$tmp = array();
			
			$tmp = $item;
			
			$tmp['publish_date'] = date('c', $item['created_time']);
			$tmp['publish_date_timestamp'] = $item['created_time'];
			
			$tmp['embeddable'] = $item['allow_embed'];
			$tmp['total_thumbs'] = 1;
			$tmp['thumbs'] = array(0 => array('original' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_url']),
											  'small' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_180_url']),
											  'medium' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_240_url']),
											  'large' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_480_url']),
											  'extra-large' => str_replace(array('http://', 'https://'), '//',  $item['thumbnail_720_url'])
										)
								);
			$tmp['keywords'] = (is_array($item['tags'])) ? implode(',', $item['tags']) : '';
			
			if (count($item['geoblocking']) == 0 || ! is_array($item['geoblocking'])) // allowed everywhere
			{
				$tmp['geo-restriction'] = null; 
			}
			else if ($item['geoblocking'][0] == 'deny') // denied to
			{
				unset($item['geoblocking'][0]);
				$tmp['geo-restriction'] = array('type' => 'deny', 'list' => implode(',', $item['geoblocking']));
			}
			else // allowed only
			{
				if ($item['geoblocking'][0] == 'allow')
				{
					unset($item['geoblocking'][0]);
				}
				
				if (count($item['geoblocking']) > 0)
				{
					$tmp['geo-restriction'] = array('type' => 'allow', 'list' => implode(',', $item['geoblocking']));
				}
				else
				{
					$tmp['geo-restriction'] = null; 
				}
			}

			$tmp['url_flv'] = '';
			$tmp['mp4'] = '';
			
			$normalized['results'][$i] = $tmp;
			
			$i++;
		}
		
		return $normalized;
	}
	
	/**
	 * Get user avatar url for Import Subscriptions
	 * 
	 * @param string $username
	 * @return string url
	 */
	public function pm_get_user_avatar_url($username)
	{
		try {
			$result = $this->get('/user/'. urlencode($username),
								array('fields' => array('avatar_80_url'))
						);
			if ($result['avatar_80_url'] != '')
			{
				return str_replace(array('http://', 'https://'), '//',  $result['avatar_80_url']);
			}

		} catch(DailymotionApiException $e) {
			return '//s1.dmcdn.net/default.png';
		}

		return '//s1.dmcdn.net/default.png';
	}
	
	/**
	 * Get single video details
	 * 
	 * @param string $video_id the dailymotion video id
	 * @since 2.4.1
	 * @return array  
	 */
	public function pm_get_video($video_id = '')
	{
		$result = $this->get('/video/'. $video_id,
							 array('fields' => $this->video_object_fields)
					);
		if ( ! empty($result['error']))
		{
			return $result;
		}
		
		return $this->pm_format_video_data(array('list' => array(0 => $result),
												 'total' => 1,
												 'page' => 1,
												 'start' => 0,
												 'per_page' => 1
											)
				);
	}
}