<?php
/**
 *   Copyright 2014 Vimeo
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

spl_autoload_register(function ($class) {
	// Make sure that the class being loaded is in the vimeo namespace
    if (substr(strtolower($class), 0, 6) !== 'vimeo\\') {
        return;
    }

    // Locate and load the file that contains the class
    $path = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
    	require($path);
    }
});


/**
 * PHP Melody classes, functions, etc extending Vimeo's SDK
 */
class PhpmelodyVimeo extends \Vimeo\Vimeo
{
	public function __construct($client_id, $client_secret, $access_token = null)
	{
		@chmod(dirname(__FILE__). '/certificates', 0755); // @since v2.7
		
		parent::__construct($client_id, $client_secret, $access_token);
		$this->CURL_DEFAULTS[CURLOPT_TIMEOUT] = 60;
	}
	
	/**
	 * Search videos and format data
	 * 
	 * @param string $keywords Search keywords
	 * @param array $args [optional] search filters, options, etc.
	 * @return array of formatted data ready to display on results page
	 */
	public function pm_search($keywords, $args = array())
	{
		if ( ! array_key_exists('page', $args) || empty($args['page']))
		{
			$args['page'] = 1;
		}
		if ( ! array_key_exists('per_page', $args) || empty($args['per_page']))
		{
			$args['per_page'] = 20;
		}
		
//		$args['query'] = urlencode($keywords);
		$args['query'] = urldecode($keywords);
		
		switch ($args['search_orderby'])
		{
			default:
			case 'relevance':
				$args['sort'] = 'relevant';
				$args['direction'] = 'desc';
			break;

			case 'date':
			case 'published':
				$args['sort'] = 'date';
				$args['direction'] = 'desc';
			break;
			
			case 'viewCount':
				$args['sort'] = 'plays';
				$args['direction'] = 'desc';
			break;
			
			case 'rating':
				$args['sort'] = 'likes';
				$args['direction'] = 'desc'; 
			break;
		}
		
		$result = $this->request('/videos', $args, 'GET');
	
		$api_data = array();

		if ($result['status'] != 200)
		{
			return $this->pm_error($result);
		}
		
		return $this->pm_format_video_data($result);
	}
	
	/**
	 * Get data for user's uploaded videos, favorites and a certain playlist and format it to our needs
	 * 
	 * @param string $feed_type [optional]
	 * @param string $id username or playlist id
	 * @param array $args [optional]
	 * @return array formatted data
	 */
	public function pm_user_feed($feed_type = 'videos', $id, $args = array())
	{
		if ( ! array_key_exists('page', $args))
		{
			$args['page'] = 1;
		}
		if ( ! array_key_exists('per_page', $args))
		{
			$args['per_page'] = 20;
		}
		
		
		switch ($feed_type)
		{
			default:
			case 'videos':
				$api_resource = '/users/'. $id .'/videos';
			break;
			
			case 'favorites':
				$api_resource = '/users/'. $id .'/likes';
			break;
			
			case 'playlist':
				// $id should be "{username or id}/albums/{album-id}"
				$api_resource = '/users/'. $id .'/videos';

			break;
		}
	
		$result = $this->request($api_resource, $args, 'GET');
		
		if ($result['status'] != 200)
		{
			return $this->pm_error($result);
		}
		
		return $this->pm_format_video_data($result);
	}
	
	/**
	 * Just a wrapper for pm_user_feed() 
	 */
	public function pm_user_videos($username, $args = array())
	{
		return $this->pm_user_feed('videos', $username, $args);
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
		
		
		if ( ! array_key_exists('page', $args))
		{
			$args['page'] = 1;
		}
		if ( ! array_key_exists('per_page', $args))
		{
			$args['per_page'] = 20;
		}
		
		$result = $this->request('/users/'. $username .'/albums', $args, 'GET');
		
		if ($result['status'] != 200)
		{
			return $this->pm_error($result);
		}
				
		$normalized = array('meta' => array('total_results' => (int) $result['body']['total'],
											'page' => $result['body']['page'],
											'start' => (($result['body']['page'] * $result['body']['per_page']) - 1),
											'per_page' => $result['body']['per_page']
										),
							'results' => array()
						);
		
		$i = 0;
		foreach ($result['body']['data'] as $k => $item)
		{
			if ($item['privacy']['view'] != 'anybody')
			{
				continue;
			}
			
			$tmp = array();
			
			$tmp['id'] = str_replace('/users/', '', $item['uri']);
			$tmp['title'] = $item['name'];
			$tmp['description'] = $item['description'];
			$tmp['url'] = $item['link'];
			$tmp['total_thumbs'] = 1;
			$tmp['thumbs'] = array(0 => array('original' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][ count($item['pictures']['sizes']) - 1 ]['link']),
												'small' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][1]['link']),
												'medium' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][2]['link']),
												'large' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][3]['link']),
												'extra-large' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][4]['link']),
										)
								);
			
			$tmp['playlist_thumb_url'] = $tmp['thumbs'][0]['medium'];
			
			$tmp['total_videos'] = (int) $item['metadata']['connections']['videos']['total'];
			
			$user_id = explode('/', $item['user']['uri']);
			$user_id = array_pop($user_id);
			$tmp['user_id'] = $user_id;
			$tmp['user_avatar_url'] = null;
			
			$normalized['results'][$i] = $tmp;
	
			$i++;
		}

		return $normalized;
	}
	
	/**
	 * 
	 * @param object $result
	 * @return 
	 */
	public function pm_format_video_data($result)
	{
		$normalized = array('meta' => array('total_results' => (int) $result['body']['total'],
											'page' => $result['body']['page'],
											'start' => null,
											'per_page' => $result['body']['per_page']
											),
							'results' => array()
					);
				
		$i = 0;
		foreach ($result['body']['data'] as $k => $item)
		{
			if ($item['privacy']['view'] != 'anybody')
			{
				continue;
			}
			
			$tmp = array();
			
			$id = explode('/', $item['uri']);
			$id = array_pop($id);
		
			$tmp['id'] = $id;
			$tmp['title'] = $item['name'];
			$tmp['description'] = $item['description'];
			$tmp['duration'] = $item['duration'];
			$tmp['private'] = null;
			$tmp['url'] = 'https://vimeo.com/'. $id;
			
			$tmp['publish_date'] = $item['created_time'];
			$tmp['publish_date_timestamp'] = strtotime($item['created_time']);
			
			$tmp['private'] = null;
			$tmp['embeddable'] = ($item['privacy']['embed'] == 'public') ? true : false;
		
		
			$tmp['total_thumbs'] = 1;
			
			$tmp['thumbs'] = array(0 => array('original' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][ count($item['pictures']['sizes']) - 1 ]['link']),
												'small' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][1]['link']),
												'medium' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][2]['link']),
												'large' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][3]['link']),
												'extra-large' => str_replace(array('http://', 'https://'), '//', $item['pictures']['sizes'][4]['link']),
										)
								);
			
			$tmp['keywords'] = '';
			if (is_array($item['tags']) && count($item['tags']) > 0)
			{
				$tmp['keywords'] = array();
				foreach ($item['tags'] as $kk => $tag)
				{
					$tmp['keywords'][] = $tag['tag'];
				}
				
				$tmp['keywords'] = implode(',', $tmp['keywords']);
			}
			
			$tmp['geo-restriction'] = null; 
			$tmp['embed_url'] = 'https://player.vimeo.com/video/'. $id;
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
		$result = $this->request('/users/'. $username .'/pictures', array(), 'GET');
		
		if ($result['status'] == 200)
		{
			foreach ($result['body']['data'][0]['sizes'] as $k => $img)
			{
				if ($img['width'] >= 80)
				{
					return $img['link'];
				}
			}
		}
		
		return '//i.vimeocdn.com/portrait/default_75x75.jpg';
	}

	public function pm_error($result)
	{
		$api_data['error'] = array('type' => null,
								   'code' => ($result['status'] != '') ? $result['status'] : null,
								   'message' => '<strong>Vimeo API error</strong>: ' . (($result['body']['error'] != '') ? $result['body']['error'] : $result['body']['error_description'])
							);
		return $api_data;
	}

}
