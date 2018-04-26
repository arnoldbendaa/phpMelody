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

if ( ! defined('ABSPATH'))
{
	exit();
}


/********* Functions for User Follow System *********/

/**
 * Function for creating a Follow relationship between two users and nothing more.
 * 
 * Function doesn't check if $follower_id is authenticated. Check for auth before calling this function.
 * Function doesn't validate user_ids nor does it check if either one of them is banned.
 * Function doesn't check if follower_id has reached the user_following_limit. 
 * 
 * @param int $leader_id
 * @param int $follower_id [optional] if none provided will use $userdata 
 * @return bool 
 */
function follow($leader_id, $follower_id = -1)
{
	global $userdata;
	
	if ($follower_id == -1)
	{
		$follower_id = $userdata['id'];
	}
	
	if ($leader_id == $follower_id)
	{
		return false;
	}
	
	// check if relationship has already been created 
	if (is_follow_relationship($leader_id, $follower_id))
	{
		return false;
	}
	
	$sql = "INSERT INTO pm_users_follow 
					(user_id, follower_id, date) 
			VALUES ('". $leader_id ."', '". $follower_id ."', '". time() ."')";
	if ( ! mysql_query($sql))
	{
		return false;
	}
	
	// update respective counts for leader and follower 
	$sql = "UPDATE pm_users 
			SET following_count = following_count + 1 
			WHERE id = '". $follower_id ."'";
	mysql_query($sql);
	
	$sql = "UPDATE pm_users 
			SET followers_count = followers_count + 1 
			WHERE id = '". $leader_id ."'";
	mysql_query($sql);
	
	$activity_id = get_activity_id(array('user_id' => $follower_id, 
										 'activity_type' => ACT_TYPE_FOLLOW, 
										 'object_id' => $leader_id, 
										 'object_type' => ACT_OBJ_USER
										)
								  );
	
	
	if ($activity_id)
	{
		delete_activity($activity_id);
	}
	
	// notify leader one time only
	if ( ! $activity_id)
	{
		follow_notify_user($leader_id, $follower_id);
	}
	
	// log activity
	log_activity(array(
					'user_id' => $follower_id,
					'activity_type' => ACT_TYPE_FOLLOW,
					'object_id' => $leader_id,
					'object_type' => ACT_OBJ_USER,
					'object_data' => fetch_user_advanced($leader_id)
					)
				);
				
	
	return true;
}

/**
 * Function for destroing a Follow relationship between two users
 * 
 * Function doesn't check if $follower_id is authenticated. Check for auth before calling this function.
 * Function doesn't validate user_ids nor does it check if either one of them is banned.
 * 
 * @param int $leader_id
 * @param int $follower_id [optional] if none provided will use $userdata 
 * @return bool
 */
function unfollow($leader_id, $follower_id = -1)
{
	global $userdata;
	
	if ($follower_id == -1)
	{
		$follower_id = $userdata['id'];
	}
	
	if ($leader_id == $follower_id)
	{
		return false;
	}
	
	// check if relationship exists 
	if ( ! is_follow_relationship($leader_id, $follower_id))
	{
		return false;
	}
	
	$sql = "DELETE FROM pm_users_follow 
			WHERE user_id = '". $leader_id ."'
			  AND follower_id = '". $follower_id ."'";
	if ( ! mysql_query($sql))
	{
		return false;
	}
	
	// update respective counts for leader and follower 
	$sql = "UPDATE pm_users 
			SET following_count = following_count - 1 
			WHERE id = '". $follower_id ."'";
	mysql_query($sql);
	
	$sql = "UPDATE pm_users 
			SET followers_count = followers_count - 1 
			WHERE id = '". $leader_id ."'";
	mysql_query($sql);
	
	$activity_id = get_activity_id(array('user_id' => $follower_id, 
										 'activity_type' => ACT_TYPE_UNFOLLOW, 
										 'object_id' => $leader_id, 
										 'object_type' => ACT_OBJ_USER
										)
								  );
	if ($activity_id)
	{
		delete_activity($activity_id);
	}
	
	log_activity(array(
					'user_id' => $follower_id,
					'activity_type' => ACT_TYPE_UNFOLLOW,
					'object_id' => $leader_id,
					'object_type' => ACT_OBJ_USER,
					'object_data' => fetch_user_advanced($leader_id)
					)
				);

	$activity_id = get_activity_id(array('user_id' => $follower_id, 
										 'activity_type' => ACT_TYPE_FOLLOW, 
										 'object_id' => $leader_id, 
										 'object_type' => ACT_OBJ_USER
										)
								  );
	if ($activity_id)
	{
		$activity_data = get_activity_data($activity_id);
		if ( ! is_loggable_activity(ACT_TYPE_UNFOLLOW))
		{
			delete_activity($activity_id);
		}
		cancel_notification($leader_id, 
							$follower_id,
							ACT_TYPE_FOLLOW, 
							$activity_data['time']);
	}
	
	return true;
}

function follow_notify_user($leader_id, $follower_id = -1)
{
	global $userdata;
	
	if ($follower_id == -1)
	{
		$follower_id = $userdata['id'];
	}

	notify_user($leader_id, $follower_id, ACT_TYPE_FOLLOW, array());
	
	return true;
} 

/**
 * Function retrieves a list of followers for a given leader.  
 * 
 * @param int $leader_id
 * @param int $start [optional]
 * @param int $limit [optional]
 * @return array of users (followers) on succes; empty array on mysql error
 */
function get_followers_list($leader_id, $start = 0, $limit = 30)
{
	global $lang;
	
	if ( ! $leader_id)
	{
		return array();
	}
	
	$sql = "SELECT f.follower_id, u.username, u.name, u.gender, u.country, u.reg_date, u.last_signin, u.power, u.about, u.avatar, u.followers_count, u.following_count, u.channel_slug, u.channel_verified, u.channel_cover 
			FROM pm_users_follow f 
			JOIN pm_users u ON (f.follower_id = u.id)
			WHERE user_id = '". $leader_id ."' 
			LIMIT $start, $limit";
	
	if ( ! $result = mysql_query($sql))
	{
		return array();
	}
	
	$followers = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$followers[$row['follower_id']] = $row;
		$followers[$row['follower_id']]['avatar_url'] = get_avatar_url($row['avatar'], $row['follower_id']);
		$followers[$row['follower_id']]['profile_url'] = get_profile_url($row);
		$followers[$row['follower_id']]['followers_compact'] = pm_compact_number_format($row['followers_count']);
		$followers[$row['follower_id']]['following_compact'] = pm_compact_number_format($row['following_count']);
		$followers[$row['follower_id']]['last_seen'] = ucwords(time_since($row['last_signin'], true)).' '.$lang['ago'];
		$followers[$row['follower_id']]['is_online'] = islive($row['last_signin']);
		$followers[$row['follower_id']]['country_label'] = countryid2name($row['country']);
		
		if ($row['channel_cover'] != '')
		{
			$followers[$row['follower_id']]['channel_cover'] = array('filename' => $row['channel_cover'],
													 'max' => _COVERS_DIR . $row['channel_cover'],
													 '450' => _COVERS_DIR . str_replace('-max.', '-450.', $row['channel_cover']),
													 '225' => _COVERS_DIR . str_replace('-max.', '-225.', $row['channel_cover']) 
													);
		}
		else
		{
			$followers[$row['follower_id']]['channel_cover'] = array('filename' => $row['channel_cover'],
													 'max' => null,
													 '450' => null,
													 '225' => null 
													);
		}


	}
	mysql_free_result($result);
	
	return $followers;
}

/**
 * Function retrieves a list of user_ids that a give user_id is following. 
 * 
 * @param int $follower_id [optional] if none provided will use $userdata
 * @param int $start
 * @param int $limit
 * @return array of users (leaders) on succes; empty array on mysql error
 */
function get_following_list($follower_id = -1, $start = 0, $limit = 30)
{
	global $userdata, $lang;
	
	if ($follower_id == -1)
	{
		$follower_id = $userdata['id'];
	}
	
	$sql = "SELECT f.user_id, u.username, u.name, u.gender, u.country, u.reg_date, u.last_signin, u.power, u.about, u.avatar, u.followers_count, u.following_count, u.channel_slug, u.channel_verified 
			FROM pm_users_follow f 
			JOIN pm_users u ON (f.user_id = u.id)
			WHERE follower_id = '". $follower_id ."' 
			LIMIT $start, $limit";
	
	if ( ! $result = mysql_query($sql))
	{
		return array();
	}
	
	$leaders = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$row['user_id'] = (int) $row['user_id'];
		$leaders[$row['user_id']] = $row;
		$leaders[$row['user_id']]['avatar_url'] = get_avatar_url($row['avatar'], $row['user_id']);
		$leaders[$row['user_id']]['profile_url'] = get_profile_url($row);
		$leaders[$row['user_id']]['followers_compact'] = pm_compact_number_format($row['followers_count']);
		$leaders[$row['user_id']]['following_compact'] = pm_compact_number_format($row['following_count']);
		$leaders[$row['user_id']]['last_seen'] = time_since($row['last_signin'], true).' '.$lang['ago'];
		$leaders[$row['user_id']]['is_online'] = islive($row['last_signin']);
		$leaders[$row['user_id']]['country_label'] = countryid2name($row['country']);
	}
	mysql_free_result($result);
	
	return $leaders;
}

/**
 * Retrieve array of all user IDs current user (or given user) is following.
 *  
 * @return array 
 */
function get_following_ids($follower_id = -1)
{
	global $userdata;
	
	if ($follower_id == -1)
	{
		$follower_id = $userdata['id'];
	}
	
	$sql = "SELECT user_id 
			FROM pm_users_follow  
			WHERE follower_id = '". $follower_id ."'";
	if ( ! $result = mysql_query($sql))
	{
		return array();
	}
	
	$user_id_list = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$user_id_list[] = (int) $row['user_id'];
	}
	mysql_free_result($result);
	
	return $user_id_list;
}

/**
 * Retrieve array of all user IDs current user (or given user) is leading.
 */
function get_followers_ids($leader_id = -1)
{
	global $userdata;
	
	if ($leader_id == -1)
	{
		$leader_id = $userdata['id'];
	}
	
	$sql = "SELECT follower_id  
			FROM pm_users_follow  
			WHERE user_id = '". $leader_id ."'";
	if ( ! $result = mysql_query($sql))
	{
		return array();
	}
	
	$user_id_list = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$user_id_list[] = (int) $row['follower_id'];
	}
	mysql_free_result($result);
	
	return $user_id_list;
}

/**
 * Check if a relationship between two users exists.
 * 
 * @param int $leader_id
 * @param int $follower_id
 * @return bool true if it exists, false if not or on mysql_error
 */
function is_follow_relationship($leader_id, $follower_id)
{
	if ($leader_id == $follower_id)
	{
		return false;
	}
	
	$sql = "SELECT COUNT(*) as total 
			FROM pm_users_follow 
			WHERE user_id = '". $leader_id ."' 
			  AND follower_id = '". $follower_id ."'";
	$result = mysql_query($sql);
	
	if ( ! $result)
	{
		return false;
	}
	
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);

	return ($row['total'] > 0) ? true : false;
}

function check_multiple_relationships($user_ids = array(), &$followers, &$following)
{
	global $userdata;
		
	if(($key = array_search($userdata['id'], $user_ids)) !== false) 
	{
		unset($user_ids[$key]);
	}

	if (count($user_ids) == 0 || empty($userdata['id']))
	{
		return false;
	}

	$sql = "SELECT user_id, follower_id 
			FROM pm_users_follow 
			WHERE (		user_id = '". $userdata['id'] ."' 
					AND follower_id IN (". implode(',', $user_ids) .")
				  )
			   OR (		follower_id = '". $userdata['id'] ."'
			   		AND user_id IN (". implode(',', $user_ids) .")
			   	  )";
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		if ($row['user_id'] == $userdata['id'])
		{
			$followers[] = $row['follower_id'];
		}
		else
		{
			$following[] = $row['user_id'];
		}
		
	}
	mysql_free_result($result);

	return true;
}

/**
 * Retrieves the total number of followers a given user has  
 * 
 * @param int $leader_id [optional] if none provided will use $userdata
 * @return 
 */
function count_followers($leader_id = -1)
{
	global $userdata;
	
	if ($leader_id == -1)
	{
		$leader_id = $userdata['id'];
	}
	
	$sql = "SELECT COUNT(*) as total 
			FROM pm_users_follow 
			WHERE user_id = '". $leader_id ."'";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	return (int) $row['total'];
	
}

/**
 * Retrieves the total number of users a given user is following 
 * 
 * @param int $follower_id [optional] if none provided will use $userdata
 * @return bool|int false on mysql error, total on success 
 */
function count_following($follower_id = -1)
{
	global $userdata;
	
	if ($follower_id == -1)
	{
		$follower_id = $userdata['id'];
	}
	
	$sql = "SELECT COUNT(*) as total 
			FROM pm_users_follow 
			WHERE follower_id = '". $follower_id ."'";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	return (int) $row['total'];
}

/**
 * Use this function when deleting a user. It takes care of the follow & following counts
 * of each following/follower related to current user.
 * 
 * @param int|array $user_id - can pass a single user ID or array of user IDs
 * @return bool 
 */
function follow_delete_user($user_id)
{
	$following = $followers = array();
	
	if ( ! $user_id)
	{
		return false;
	}

	$sql_where_part = (is_array($user_id)) ? ' IN ('. implode(',', $user_id) .') ' : " = '$user_id'";
	
	$sql = "SELECT follower_id 
			FROM pm_users_follow
			WHERE user_id ". $sql_where_part;
	if ($result = mysql_query($sql))
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$followers[$row['follower_id']]++;
		}
		mysql_free_result($result);
	}
	
	$sql = "SELECT user_id 
			FROM pm_users_follow
			WHERE follower_id ". $sql_where_part;
	if ($result = mysql_query($sql))
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$following[$row['user_id']]++;
		}
		mysql_free_result($result);
	}
	
	if (count($followers))
	{
		foreach ($followers as $uid => $count)
		{
			$count_2 = 0;
			$unset = false;
			
			$sql = "UPDATE pm_users  
					SET following_count = IF(following_count - '$count' < 0, '0', following_count - '$count') ";
			
			if ($following[$uid] > 0)
			{
				$count_2 = $following[$uid];
				
				$sql .= ", followers_count = IF(followers_count - '$count_2' < 0, '0', followers_count - '$count_2') ";
				$unset = true;
			}
			
			$sql .= " WHERE id = '$uid' ";
			
			if ($r = mysql_query($sql) && $unset)
			{
				unset($following[$uid]);
			}
		}
	}

	if (count($following))
	{
		foreach ($following as $uid => $count)
		{
			$sql = "UPDATE pm_users  
					   SET followers_count = IF(followers_count - '$count' < 0, '0', followers_count - '$count') 
					 WHERE id = '$uid' ";
			
			mysql_query($sql);
		}
	}
	
	$sql = "DELETE FROM pm_users_follow 
			WHERE user_id $sql_where_part
			   OR follower_id $sql_where_part ";
	return mysql_query($sql);
}


/********* END Functions for User Follow System  *********/


/********* Functions for User Activity System *********/


function activity_load_options()
{
	global $config;
	return unserialize(stripslashes($config['activity_options']));
}

function activity_save_options($args = array())
{
	global $config;
	
	$current = activity_load_options();
	$o = array_merge($current, $args);

	return update_config('activity_options', serialize($o), true);
}

function is_loggable_activity($activity_type) 
{
	global $config;

	$o = activity_load_options();
	
	if ($o[$activity_type] == 0)
	{
		return false;
	}

	return true;
}

/**
 * Log activity 
 * 
 * @param object $args [optional]
 * @return int - activity ID
 */
function log_activity($args = array())
{
	global $config, $userdata;
	
	$defaults = array(
		'user_id' => false,
		'activity_type' => false,
		'time' => time(),
		'object_id' => 0,
		'object_type' => '',
		'object_data' => array(),
		'target_id' => 0,
		'target_type' => '',
		'target_data' => array(),
		'hide' => 0,
		'metadata' => array()
	);
	
	$options = array_merge($defaults, $args);
	extract($options);
	
	if ( ! $user_id)
	{
		$user_id = $userdata['id'];
	}
	
	if ( ! $activity_type || ! is_loggable_activity($activity_type))
	{
		return false;
	}

	if ($object_id != 0 && count($object_data) > 0)
	{
		switch ($object_type)
		{
			case ACT_OBJ_USER:
				
				$metadata['object'] = array('id' => $object_data['id'],
											'username' => $object_data['username'],
											'name' => $object_data['name'],
											'avatar' => $object_data['avatar'],
											'channel_slug' => $object_data['channel_slug']
											);
			break;
			
			case ACT_OBJ_VIDEO:
				
				$metadata['object'] = array('uniq_id' => $object_data['uniq_id'],
											'video_title' => $object_data['video_title'],
											'video_slug' => $object_data['video_slug'],
											'duration' => sec2hms($object_data['yt_length']),
											'yt_thumb' => $object_data['yt_thumb'],
											'submitted' => $object_data['submitted']
											);
			break;
			
			case ACT_OBJ_COMMENT:
				
				if ($user_id == 0)
				{
					$metadata['object'] = array('guestname' => $object_data['guestname']);
				}
					
			break;
			
			case ACT_OBJ_ARTICLE:
				
				$metadata['object'] = array('id' => $object_data['id'], 
											'title' => $object_data['title'],
											'article_slug' => $object_data['article_slug']
											);
				
			break;
			
			case ACT_OBJ_PROFILE:
				
				// mostly for avatar updates
				$metadata['object'] = array('avatar' => $object_data['avatar']);
				
			break;
			
			case ACT_OBJ_PLAYLIST:
			break;
			
			
			case ACT_OBJ_ACTIVITY:
			break;
			
			
			case ACT_OBJ_STATUS: 
			break;
		}
	}
	
	if ($target_id != 0 && count($target_data) > 0)
	{
		switch ($target_type)
		{
			case ACT_OBJ_USER:
				
				$metadata['target'] = array('id' => $target_data['id'],
											'username' => $target_data['username'],
											'name' => $target_data['name'],
											'avatar' => $target_data['avatar'],
											'channel_slug' => $target_data['channel_slug']
											);
			break;
			
			case ACT_OBJ_VIDEO:
				
				$metadata['target'] = array('uniq_id' => $target_data['uniq_id'],
											'video_title' => $target_data['video_title'],
											'video_slug' => $target_data['video_slug'],
											'duration' => sec2hms($target_data['yt_length']),
											'yt_thumb' => $target_data['yt_thumb'],
											'submitted' => $target_data['submitted']
											);
			break;
			
			case ACT_OBJ_COMMENT:
				
			break;
			
			case ACT_OBJ_ARTICLE:

				$metadata['target'] = array('id' => $target_data['id'], 
											'title' => $target_data['title'],
											'article_slug' => $target_data['article_slug']
											);
				
			break;
			
			case ACT_OBJ_PROFILE:
				
				// mostly for avatar updates
				$metadata['target'] = array('statustext' => $target_data['statustext'],
											'avatar' => $target_data['avatar']);
				
			break;
			
			case ACT_OBJ_PLAYLIST:
			break;
			
			
			case ACT_OBJ_ACTIVITY:
			break;
			
			
			case ACT_OBJ_STATUS: 
			break;
		}
	}
	
	if (count($args['metadata']) > 0)
	{
		foreach ($args['metadata'] as $k => $v)
		{
			$metadata[$k] = $v;
		}
	}
	
	// insert
	$sql = "INSERT INTO pm_activity 
					(user_id, activity_type, time, object_id, object_type, target_id, target_type, hide, metadata)
			VALUES ('". $user_id ."',
					'". $activity_type ."',
					'". $time ."',
					'". $object_id ."',
					'". $object_type ."',
					'". $target_id ."',
					'". $target_type ."',
					'0',
					'". secure_sql(serialize($metadata)) ."'
					)";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	$activity_id = mysql_insert_id();
	
	return $activity_id;
}

/**
 * Simple function for updating a row in the pm_activity table. 
 * Expecting serialized data for "metadata" column!
 * 
 * @param int $activity_id
 * @param array $args (column_name, new value) pair.
 * @return bool 
 */
function update_activity($activity_id, $args)
{
	if ( ! $activity_id || empty($args))
	{
		return false;
	}
	
	$sql = "UPDATE pm_activity 
			SET ";

	foreach ($args as $col => $new_val)
	{
		$sql .= $col ." = '". secure_sql($new_val) ."', ";
	}
	$sql = substr($sql, 0, -2);
	$sql .= " WHERE activity_id = '". $activity_id ."'";
	
	if ( ! mysql_query($sql))
	{
		return false;
	}
	
	return true;
}

/**
 * Performs the hide and unhide (make public) actions for a particular activity.
 * 
 * @param int $activity_id
 * @return bool 
 */
function hide_activity($activity_id) 
{
	global $userdata;
	
	if ( ! $activity_id || ! is_user_logged_in())
	{
		return false;
	}
	
	$activity_data = get_activity_data($activity_id);
	
	if ($activity_data['hide'] == 0)
	{
		$activity_data['hide'] = 1;
	}
	else
	{
		$activity_data['hide'] = 0;
	}
	
	return update_activity($activity_id, array('hide' => $activity_data['hide']));
}

/**
 * Retrieves raw data from pm_activity for a given single activity_id or an array of activity_ids.
 * 
 * @param int|array $activity_id
 * @return bool|array 
 */
function get_activity_data($activity_id)
{
	if (empty($activity_id))
	{
		return false;
	}
	
	$single = false;
	if ( ! is_array($activity_id))
	{
		$single = true;
	}

	if (is_array($activity_id) && count($activity_id) == 1)
	{
		$single = true;
		$activity_id = (int) $activity_id[0];
	}

	
	$sql = "SELECT * FROM 
			pm_activity ";
	
	if ( ! $single)
	{
		$sql .= " WHERE activity_id IN (". implode(',', $activity_id) .")";
	} 
	else
	{
		$sql .= " WHERE activity_id = '". $activity_id ."'";
	}

	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	$activity_data = array();
	
	if ( ! $single)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$activity_data[$row['activity_id']] = $row;
		}
	}
	else
	{
		$row = mysql_fetch_assoc($result);
		$activity_data = $row;
	}
	
	mysql_free_result($result);
	
	return $activity_data;
}

function get_activity_id($args)
{
	$defaults = array(
		'user_id' => false,
		'activity_type' => false,
		'object_id' => 0,
		'object_type' => '',
		'target_id' => 0,
		'target_type' => '',
		'hide' => -1
	);
	
	$options = array_merge($defaults, $args);
	extract($options);
	
	if ( ! $user_id && ! $activity_type)
	{
		return false;
	}
	
	$sql_where = " user_id = '". $user_id ."' AND activity_type = '". $activity_type ."' ";
	
	if ($object_id && $object_type)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " object_id = '". $object_id ."' AND object_type = '". $object_type ."' ";
	}
	
	if ($target_id && $target_type)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " target_id = '". $target_id ."' AND target_type = '". $target_type ."' ";
	}
	
	if ($hide >= 0)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " hide = '". $hide ."' ";
	}
	
	$sql = "SELECT activity_id 
			FROM pm_activity 
			WHERE $sql_where 
			LIMIT 1";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);

	return (int) $row['activity_id'];
}

/**
 * Retrieve activity feed (like facebook's "News Feed") for the current user:
 * - first, get a list of user IDs current user is following
 * - based on these user IDs, get activity for each one of them
 * 
 * @param int $start [optional]
 * @param int $limit [optional]
 * @return array 
 */
function get_following_activity_stream($start = 0, $limit = 30)
{
	global $userdata;

	if ( ! $userdata['id'])
	{
		return array();
	}
	
	if ($userdata['following_count'] == 0)
	{
		return array();
	}
	
	$following_ids = get_following_ids($userdata['id']);
	
	$sql = "SELECT pm_activity.*, pm_users.username, pm_users.name, pm_users.avatar, pm_users.power, pm_users.channel_slug, pm_users.channel_verified  
			FROM pm_activity 
			JOIN pm_users ON (pm_activity.user_id = pm_users.id)  
			WHERE (
					user_id IN (". implode(',', $following_ids) .")  
			   		OR (user_id = '". $userdata['id'] ."' AND activity_type LIKE '". ACT_TYPE_STATUS ."')
				  )   
			  AND hide = '0' 
			ORDER BY time DESC 
			LIMIT $start, $limit";
 
	$result = mysql_query($sql);
	
	$activity_list = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$activity_list[$row['activity_id']] = $row;
	}
	mysql_free_result($result);
	
	parse_activity_list($activity_list);
	
	return $activity_list;
}



function delete_activity($activity_id) 
{
	if ( ! $activity_id)
	{
		return false;
	}
	
	$sql = "DELETE FROM pm_activity 
			WHERE activity_id = '". $activity_id ."'";
	  
	return mysql_query($sql);
}


/**
 * Remove(delete) ALL activities a given Entity ($id, $object_type) is related to, including 
 * Entity as Object, Entity as Target and Entity as Actor. 
 * 
 * Use when deleting user/video/comment/article/etc.
 * Usage: remove_all_related_activity(5, ACT_OBJ_VIDEO);
 * 
 * @param int $id
 * @param string $object_type
 * @return bool
 */
function remove_all_related_activity($id, $object_type)
{
	
	if ( ! $id && ! $object_type)
	{
		return false;
	}
	
	$sql_where = '';
	if ($object_type == ACT_OBJ_USER)
	{
		$sql_where .= " user_id = '". $id ."' ";
	}
	
	$sql_where .= ($sql_where != '') ? ' OR ' : '';
	$sql_where .= " (object_id = '". $id ."' AND object_type = '". $object_type ."') ";
	$sql_where .= ($sql_where != '') ? ' OR ' : '';
	$sql_where .= " (target_id = '". $id ."' AND target_type = '". $object_type ."') ";
	
	$sql = "DELETE FROM pm_activity
			WHERE ". $sql_where;
	
	return mysql_query($sql);
}


/**
 * Shorthand for MySQL DELETE query. Uses AND operator.
 *
 * @param int $user_id [optional]
 * @param string $activity_type [optional]
 * @param int $object_id [optional]
 * @param string $object_type [optional]
 * @param int $target_id [optional]
 * @param string $target_type [optional]
 * @return bool
 */
function custom_remove_activity($user_id = 0, $activity_type = '', $object_id = 0, $object_type = '', $target_id = 0, $target_type = '')
{
	if (empty($user_id) && empty($activity_type) && empty($object_id) && empty($object_type) && empty($target_id) && empty($target_type))
	{
		return false;
	}
	
	$sql_where = '';
				
	if ($user_id)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " user_id = '". $user_id ."' ";
	}
	
	if ($activity_type != '')
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " activity_type LIKE '". $activity_type ."' ";
	}
	
	if ($object_id)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " object_id = '". $object_id ."' ";
	}
	
	if ($object_type != '')
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " object_type = '". $object_type ."' ";
	}
	
	if ($target_id)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " target_id = '". $target_id ."' ";
	}
	
	if ($target_type != '')
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " target_type = '". $target_type ."' ";
	}
	
	
	$sql = "DELETE FROM pm_activity 
			WHERE ". $sql_where;
	
	return mysql_query($sql);
}

/**
 * Parse and group activities so they look nice and intuitive. Basically, we want to create 
 * human-friendly sentences. 
 * Each sentence is has made up of 4 parts: actor(s), a verb, object(s) and a target - target is rarely used.
 * Grouping is based on common verbs guided by a few simple 1-many or many-1 rules. 
 * 
 * Function will fill up the each "bucket" accordingly.
 * 
 * @param array $activity_data - array of data returned by get_following_activity_stream()
 * @param array $actor_bucket
 * @param array $object_bucket
 * @param array $target_bucket
 * @param array $activity_meta_bucket - stores verb, actors_count, objects_count, time, html5_datetime, full_datetime, time_since, etc.
 * @return bool
 */
function activity_stream_rollup($activity_data, &$actor_bucket, &$object_bucket, &$target_bucket, &$activity_meta_bucket) 
{
	global $config, $lang;
	
	$actors = array();
	$verb = '';
	$objects = array();
	$target = array();
	
	$temp = $activity_data;
	unset($activity_data);
	foreach ($temp as $activity_id => $data)
	{
		$activity_data[] = $data;
	}
	unset($temp);
	
	$activity_count = count($activity_data);
	
	for($i = 0; $i < $activity_count; $i++)
	{
		$a = $activity_data[$i];
		
		// rule 1 (1-many): Actor, follows, group leaders
		if ($a['activity_type'] == ACT_TYPE_FOLLOW)
		{
			$activity_meta_bucket[$i] = array('lang' => $lang['activity_'. $a['activity_type']],
											  'activity_type' => $a['activity_type'],
											  'time' => $a['time'],
											  'html5_datetime' => $a['html5_datetime'],
											  'full_datetime' => $a['full_datetime'],
											  'time_since' => $a['time_since'],
											  'actors_count' => 1,
											  'objects_count' => 1,
											  'targets_count' => 0,
											  'object_type' => $a['object_type'],
											  'target_type' => $a['target_type']
											 );
			$actor_bucket[$i][] = array('user_id' => $a['user_id'],
									    'username' => $a['username'],
									    'name' => $a['name'],
									    'power' => $a['power'],
									    'avatar' => $a['avatar'],
									    'avatar_url' => make_url_https($a['avatar_url']),
									    'profile_url' => make_url_https($a['profile_url']),
										'channel_verified' => $a['channel_verified']
									   );
			$object_bucket[$i][] = $a['metadata']['object'];
			
			$temp_count = count($activity_data);
			
			// look for objects by same actor
			for ($j = $i + 1; $j < $activity_count ; $j++)
			{
				$b = $activity_data[$j];

				if ($b['activity_type'] == $a['activity_type'] && $b['user_id'] == $a['user_id'])
				{
					$object_bucket[$i][] = $b['metadata']['object'];
					$activity_meta_bucket[$i]['objects_count']++;
					unset($activity_data[$j]);
				}
			}
		}
		// rule 2 (many-1): group actors, type, common OBJECT
		else if (in_array($a['activity_type'], array(ACT_TYPE_LIKE, ACT_TYPE_WATCH, ACT_TYPE_DISLIKE, ACT_TYPE_FAVORITE, ACT_TYPE_COMMENT, ACT_TYPE_UNFOLLOW)))
		{
			$activity_meta_bucket[$i] = array('lang' => $lang['activity_'. $a['activity_type']],
											  'activity_type' => $a['activity_type'],
											  'time' => $a['time'],
											  'html5_datetime' => $a['html5_datetime'],
											  'full_datetime' => $a['full_datetime'],
											  'time_since' => $a['time_since'],
											  'actors_count' => 1,
											  'objects_count' => 0,
											  'targets_count' => 0,
											  'object_type' => $a['object_type'],
											  'target_type' => $a['target_type']
											 );
			$actor_bucket[$i][] = array('user_id' => $a['user_id'],
									    'username' => $a['username'],
									    'name' => $a['name'],
									    'power' => $a['power'],
									    'avatar' => $a['avatar'],
									    'avatar_url' => make_url_https($a['avatar_url']),
									    'profile_url' => make_url_https($a['profile_url']),
										'channel_verified' => $a['channel_verified']
									   );
			$object_bucket[$i][] = $a['metadata']['object'];
			$target_bucket[$i][] = $a['metadata']['target'];
			
			$activity_meta_bucket[$i]['objects_count'] = count($object_bucket[$i]);
			$activity_meta_bucket[$i]['targets_count'] = count($target_bucket[$i]);
			
			// look for actors with same object
			for ($j = 0; $j < $activity_count ; $j++)
			{
				if (is_array($activity_data[$j]))
				{
					$b = $activity_data[$j];
					
					$what = 'object';
					
					if ($a['activity_type'] == ACT_TYPE_COMMENT) // group by target
					{
						$what = 'target';
					}
					else // group by object
					{
						$what = 'object';
					}
	
					if ($a['activity_id'] != $b['activity_id']) // skip self
					{
						if ($b['activity_type'] == $a['activity_type'] 
							&& $b[$what .'_id'] == $a[$what .'_id'] 
							&& $b[$what .'_type'] == $a[$what .'_type']
							&& $b['user_id'] != $a['user_id'])
						{
							// unique actors only
							$found_actor = false;
							if (is_array($actor_bucket[$i]))
							{
								$kkkk = 1;
								foreach ($actor_bucket[$i] as $key => $u)
								{
									if ($u['user_id'] == $b['user_id'])
									{
										$found_actor = true;
										break;
									}
								}
							}
	
							if ( ! $found_actor)
							{
								$actor_bucket[$i][] = array('user_id' => $b['user_id'],
														    'username' => $b['username'],
														    'name' => $b['name'],
														    'power' => $b['power'],
														    'avatar' => $b['avatar'],
														    'avatar_url' => make_url_https($b['avatar_url']),
														    'profile_url' => make_url_https($b['profile_url']),
															'channel_verified' => $b['channel_verified']
														   );
								$activity_meta_bucket[$i]['actors_count']++;
							}
							unset($activity_data[$j]);
						}
						else if ($b['activity_type'] == $a['activity_type'] 
								&& $b[$what .'_id'] == $a[$what .'_id'] 
								&& $b[$what .'_type'] == $a[$what .'_type']
								&& $b['user_id'] == $a['user_id']) // same user id here
						{
							unset($activity_data[$j]);
						}
					}
				}
			} // end for($j);

			if ($activity_meta_bucket[$i]['actors_count'] > 1 && array_key_exists('activity_'. $a['activity_type'] .'_plural', $lang))
			{
				$activity_meta_bucket[$i]['lang'] = $lang['activity_'. $a['activity_type'] .'_plural'];
			}
		}
		else // everything else, treat as single
		{
			if (is_array($a))
			{
				$activity_meta_bucket[$i] = array('lang' => $lang['activity_'. $a['activity_type']],
												  'activity_type' => $a['activity_type'],
												  'time' => $a['time'],
												  'html5_datetime' => $a['html5_datetime'],
												  'full_datetime' => $a['full_datetime'],
												  'time_since' => $a['time_since'],
												  'actors_count' => 1,
												  'objects_count' => 0,
												  'targets_count' => 0,
												  'object_type' => $a['object_type'],
												  'target_type' => $a['target_type']
												 );
				$actor_bucket[$i][] = array('user_id' => $a['user_id'],
										    'username' => $a['username'],
										    'name' => $a['name'],
										    'power' => $a['power'],
										    'avatar' => $a['avatar'],
										    'avatar_url' => make_url_https($a['avatar_url']),
										    'profile_url' => make_url_https($a['profile_url']),
											'channel_verified' => $a['channel_verified']
										   );
				$object_bucket[$i][] = (array) $a['metadata']['object'];
				$target_bucket[$i][] = (array) $a['metadata']['target'];
				$activity_meta_bucket[$i]['objects_count'] = count($object_bucket[$i]);
				$activity_meta_bucket[$i]['targets_count'] = count($target_bucket[$i]);
			}
		}
		
		if (count($a['metadata']) > 0)
		foreach ($a['metadata'] as $k => $v)
		{
			if ($k != 'object' && $k != 'target')
			{
				if ($k == 'statustext' && $config['allow_emojis'] == 1)
				{
					if ( ! class_exists('Emojione\\Client'))
					{
						include(ABSPATH .'include/emoji/autoload.php');
					} 
					$emoji_client = new Emojione\Client(new Emojione\Ruleset());
					$emoji_client->ascii = true;
					$emoji_client->unicodeAlt = false;
					
					$v = $emoji_client->shortnameToImage($v);
				}
				
				$activity_meta_bucket[$i]['metadata'][$k] = $v;				
			}
		}
		
		if (in_array($a['activity_type'], array(ACT_TYPE_LIKE, ACT_TYPE_DISLIKE)))
		{
			$activity_meta_bucket[$i]['lang'] .= ' '. $lang['activity_obj_'.$a['object_type']];
		}
		else if (in_array($a['activity_type'], array(ACT_TYPE_COMMENT)))
		{
			$activity_meta_bucket[$i]['lang'] .= ' '. $lang['activity_obj_'.$a['target_type']];
		}
		
		if (is_array($a)) // this is just for 'My activity' page
		{
			$activity_meta_bucket[$i]['activity_id'] = $a['activity_id'];
		}
		
	} // end main for()
}


/**
 * Retrieve a given user's activity
 * 
 * @param int $user_id
 * @param int $start [optional]
 * @param int $limit [optional]
 * @return array raw activity data, pass this to activity_stream_rollup() 
 */
function get_user_activity($user_id, $start = 0, $limit = 30)
{
	global $userdata;

	$u = array(); // will hold the actor's data.  
	
	if ($userdata['id'] != $user_id)
	{
		$sql = "SELECT id, username, name, avatar, power, channel_slug  
				FROM pm_users 
				WHERE id = '". $user_id ."'";
		$result = mysql_query($sql);
		$u = mysql_fetch_assoc($result);
		mysql_free_result($result);
	}
	else
	{
		$u = $userdata;
	}
	
	$sql = "SELECT * 
			FROM pm_activity 
			WHERE user_id = '". $user_id ."'
			  AND hide = '0' 
			ORDER BY time DESC 
			LIMIT $start, $limit";	
	
	$result = mysql_query($sql);
	
	$activity_list = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$activity_list[$row['activity_id']] = array_merge($row, $u);
	}
	mysql_free_result($result);
	
	parse_activity_list($activity_list);
	
	return $activity_list;
}

/**
 * Function created for recycling code. 
 * Feed it raw data from pm_activity and it will parse and unserialize. 
 * 
 * @param array $the_list - list of activities
 * @param object $the_list - list of activities 
 */
function parse_activity_list(&$the_list)
{
	
	if (count($the_list) > 0)
	foreach ($the_list as $k => $row)
	{
		$the_list[$k]['html5_datetime'] = date('Y-m-d\TH:i:sO', $row['time']); // ISO 8601
		$the_list[$k]['full_datetime'] = date('l, F j, Y g:i A', $row['time']); 
		$the_list[$k]['time_since'] = time_since($row['time']);
		$the_list[$k]['avatar_url'] = get_avatar_url($row['avatar'], $row['user_id']);
		$the_list[$k]['profile_url'] = get_profile_url($row);
		
		$meta = unserialize($row['metadata']);
	  
		if ($row['object_id'] != 0)
		{
	
			switch ($row['object_type'])
			{
				case ACT_OBJ_USER:
	
					$meta['object']['avatar_url'] = get_avatar_url($meta['object']['avatar'], $meta['object']['id']);
					$meta['object']['profile_url'] = get_profile_url($meta['object']);
	
				break;
				
				case ACT_OBJ_VIDEO:
	
					$meta['object']['video_href'] = makevideolink($meta['object']['uniq_id'], $meta['object']['video_title'], $meta['object']['video_slug']);
					$meta['object']['thumb_img_url'] = show_thumb($meta['object']['uniq_id']);
					$meta['object']['views_compact'] = pm_compact_number_format($meta['object']['site_views']);
	
				break;
				
				case ACT_OBJ_COMMENT:
					 
					if ($row['user_id'] == 0)
					{
						$the_list[$k]['username'] = $meta['object']['guestname'];
					}
					
				break;
				
				case ACT_OBJ_ARTICLE:
					
					$meta['object']['link'] = art_make_link('article', $meta['object']);
					
				break;
				
				case ACT_OBJ_PROFILE:
					
					// mostly for avatar updates
					$meta['object']['avatar_url'] = get_avatar_url($meta['object']['avatar'], $meta['object']['id']);
					
				break;
				
				case ACT_OBJ_PLAYLIST:
				break;
				
				
				case ACT_OBJ_ACTIVITY:
				break;
				
				
				case ACT_OBJ_STATUS: 
				break;
			}
		}
		
		if ($row['target_id'] != 0)
		{
			switch ($row['target_type'])
			{
				case ACT_OBJ_USER:
				
					$meta['target']['avatar_url'] = get_avatar_url($meta['target']['avatar'], $meta['target']['id']);
					$meta['target']['profile_url'] = get_profile_url($meta['target']);
	
				break;
				
				case ACT_OBJ_VIDEO:
					
					$meta['target']['video_href'] = makevideolink($meta['target']['uniq_id'], $meta['target']['video_title'], $meta['object']['video_slug']);
					$meta['target']['thumb_img_url'] = show_thumb($meta['target']['uniq_id']);
					$meta['target']['views_compact'] = pm_compact_number_format($meta['target']['site_views']);
	
				break;
				
				case ACT_OBJ_COMMENT:
					
				break;
				
				case ACT_OBJ_ARTICLE:
					
					$meta['target']['link'] = art_make_link('article', $meta['target']);
					
				break;
				
				case ACT_OBJ_PROFILE:
					
					// mostly for avatar updates
					$meta['target']['avatar_url'] = get_avatar_url($meta['target']['avatar'], $meta['target']['id']);
					
				break;
				
				case ACT_OBJ_PLAYLIST:
				break;
				
				
				case ACT_OBJ_ACTIVITY:
				break;
				
				
				case ACT_OBJ_STATUS: 
				break;
			}
		}
	
		$the_list[$k]['metadata'] = $meta;
	}
}

function admin_get_activities($start = 0, $limit = 20, $page = 1, $filter = '', $filter_value = '')
{
	$sql_where = '';
	
	$row_count = 0;
	
	if ($filter != '')
	{
		$sql_where = ' WHERE ';
		switch ($filter)
		{
			case 'user_id': 
				$sql_where .= " user_id = '". $filter_value ."' ";
			break;
			
			case 'type':
				$sql_where .= " activity_type LIKE '". $filter_value ."' ";
			break;
		}
	}
	
	$sql = "SELECT pm_activity.*, pm_users.username, pm_users.name, pm_users.avatar, pm_users.power, pm_users.channel_slug, pm_users.channel_verified 
			FROM pm_activity 
			LEFT JOIN pm_users ON (pm_activity.user_id = pm_users.id)
			$sql_where 
			ORDER BY time DESC 
			LIMIT $start, $limit";

	$result = mysql_query($sql);
	
	$activity_list = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$activity_list[$row['activity_id']] = $row;
	}
	mysql_free_result($result);
	
	parse_activity_list($activity_list);
	
	return $activity_list;
}

/********* END Functions for User Activity System *********/

/********* Functions for User Notifications System *********/

/**
 * Use this to send notifications to a user 
 * 
 * @param int $to_user_id - notification receiver
 * @param int $from_user_id - notification creator
 * @param string $activity_type   
 * @param array $metadata - containing data about $from_user_id and data about the object in cause (object_type[must!], thumb, id, etc.)
 * @return 
 */
function notify_user($to_user_id, $from_user_id, $activity_type, $metadata)
{
	global $notify_loggable_activity_types;
	
	if ( ! in_array($activity_type, $notify_loggable_activity_types))
	{
		return false;
	}
	
	if ($to_user_id == $from_user_id) // avoid sending notification to self
	{
		return true;
	}
	
	$time_now = time();
	
	if ( ! array_key_exists('from_userdata', $metadata))
	{
		$from_userdata = fetch_user_advanced($from_user_id);
		$metadata['from_userdata'] = array( 'id' => $from_userdata['id'],
											'username' => $from_userdata['username'],
											'name' => $from_userdata['name'],
											'avatar_url' => $from_userdata['avatar_url'],
											'channel_slug' => $from_userdata['channel_slug'],
											'channel_verified' => $from_userdata['channel_verified']
										  );
	}
	else
	{
		$from_userdata = $metadata['from_userdata'];
		$metadata['from_userdata'] = array( 'id' => $from_userdata['id'],
											'username' => $from_userdata['username'],
											'name' => $from_userdata['name'],
											'avatar_url' => $from_userdata['avatar_url'], 
											'channel_slug' => $from_userdata['channel_slug'],
											'channel_verified' => $from_userdata['channel_verified']
										  );
	}
	
	switch ($metadata['object_type'])
	{
		case ACT_OBJ_VIDEO:
			$object_data = $metadata['object'];
			$metadata['object'] = array('uniq_id' => $object_data['uniq_id'],
										'video_title' => $object_data['video_title'],
										'video_slug' => $object_data['video_slug'],
										'yt_thumb' => $object_data['yt_thumb'],
										'submitted' => $object_data['submitted']
										);
		break;
		case ACT_OBJ_ARTICLE:
			$object_data = $metadata['object'];
			$metadata['object'] = array('id' => $object_data['id'], 
										'title' => $object_data['title'],
										'article_slug'=> $object_data['article_slug'],
										'views' => $object_data['views']
										);
		break;

		case ACT_OBJ_PROFILE:				
		break;
		
		case ACT_OBJ_COMMENT:
		break;
		
		case ACT_OBJ_PLAYLIST:
		break;
		
		case ACT_OBJ_ACTIVITY:
		break;
		
		case ACT_OBJ_STATUS: 
		break;
	}
	
	$sql = "INSERT INTO pm_notifications 
				(to_user_id, from_user_id, activity_type, time, seen, metadata)
			VALUES ('". $to_user_id ."',
					'". $from_user_id ."',
					'". $activity_type ."', 
					'". $time_now ."', 
					'0', 
					'". secure_sql(serialize($metadata)) ."') ";
	if ( ! mysql_query($sql))
	{
		return false;
	}
	
	$sql = "UPDATE pm_users 
			SET unread_notifications_count = unread_notifications_count + 1 
			WHERE id = '$to_user_id'";
	if ( ! mysql_query($sql))
	{
		return false;
	}
	
	// table cleanup and optimization
	prune_notifications_table();
	
	return true;
} 

function mark_notification_read($limit = 0)
{
	global $userdata;
	
	$sql = "UPDATE pm_notifications 
			SET seen = '1' 
			WHERE to_user_id = '". $userdata['id'] ."'
			  AND seen = '0'";
	$sql .= ($limit > 0) ? ' ORDER BY time DESC LIMIT '. $limit : '';

	if ( ! mysql_query($sql))
	{
		return false;
	}
	
	
	$sql = "UPDATE pm_users 
			SET unread_notifications_count = ";
	if ($limit > 0)
	{
		if ($userdata['unread_notifications_count'] - $limit < 0)
		{
			$sql .= "'0'";
		}
		else
		{
			$sql .= ' unread_notifications_count - '. $limit;
		}
	}
	$sql .= ' WHERE id = '. $userdata['id'];
	
	return mysql_query($sql);
}

/**
 * Get unread notifications for a particular user. 
 * 
 * @param object $user_id
 * @return 
 */
function get_latest_notifications($start = 0, $limit = 7) 
{
	global $userdata, $lang;
	
	$sql = "SELECT * 
			FROM pm_notifications 
			WHERE to_user_id = '". $userdata['id'] ."'
			ORDER BY time DESC 
			LIMIT $start, $limit";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	$list = array();
	
	while ($row = mysql_fetch_assoc($result))
	{
		$id = $row['notification_id'];
		$meta = unserialize($row['metadata']);
		
		if ($meta['from_userdata']['id'] != 0)
		{
			$meta['from_userdata']['profile_url'] = get_profile_url($meta['from_userdata']);
		}
		else
		{
			$meta['from_userdata']['profile_url'] = '#';
		}
		
		switch ($meta['object_type'])
		{
			case ACT_OBJ_USER:
			break;
			
			case ACT_OBJ_VIDEO:

				$meta['object']['video_href'] = makevideolink($meta['object']['uniq_id'], $meta['object']['video_title'], $meta['object']['video_slug']);
				$meta['object']['thumb_img_url'] = show_thumb($meta['object']['uniq_id']);
				$meta['object']['views_compact'] = pm_compact_number_format($meta['object']['site_views']);

			break;
			
			case ACT_OBJ_COMMENT:
			break;
			
			case ACT_OBJ_ARTICLE:
				
				$meta['object']['link'] = art_make_link('article', $meta['object']);
				
			break;
			
			case ACT_OBJ_PROFILE:
			break;
			
			case ACT_OBJ_PLAYLIST:
			break;
			
			
			case ACT_OBJ_ACTIVITY:
			break;
			
			
			case ACT_OBJ_STATUS: 
			break;
		}
		$sentence = '';
		
		switch ($row['activity_type'])
		{
			case ACT_TYPE_FOLLOW:
			case ACT_TYPE_FAVORITE:	
				$sentence = $lang['notification_'. $row['activity_type']];
			break;
			
			case ACT_TYPE_LIKE:
			case ACT_TYPE_COMMENT:
				
				$sentence = $lang['notification_'. $row['activity_type'] .'_'. $meta['object_type']];

			break;
		}
		
		$list[$id] = $row;
		$list[$id]['metadata'] = $meta;
		$list[$id]['lang'] = $sentence;
		$list[$id]['html5_datetime'] = date('Y-m-d\TH:i:sO', $row['time']);
		$list[$id]['full_datetime'] = date('l, F j, Y g:i A', $row['time']);
		$list[$id]['time_since'] = time_since($row['time']);

	}
	mysql_free_result($result);

	if (count($list) == 0)
	{
		return false;
	}
	
	return $list;
}

/**
 * Use this function for "reverting" actions. For example, when a user clicks the LIKE button, it triggers a notification. 
 * But when the user clicks the same button again, it cancels the "Like" so it should also "cancel" the notification.
 * 
 * @param object $to_user_id
 * @param object $from_user_id
 * @param object $activity_type
 * @param object $time
 * @return 
 */
function cancel_notification($to_user_id, $from_user_id, $activity_type, $time)
{
	if (empty($to_user_id) || empty($from_user_id) || empty($activity_type) || empty($time))
	{
		return false;
	}
	
	$sql = "DELETE FROM pm_notifications 
			WHERE to_user_id = '$to_user_id' 
			  AND from_user_id = '$from_user_id'
			  AND activity_type LIKE '$activity_type'
			  AND time = '$time'";
	$result = mysql_query($sql);
	if ( $result && mysql_affected_rows() > 0)
	{
		$sql = "UPDATE pm_users 
				SET unread_notifications_count = IF(unread_notifications_count - '1' < 0, '0',  unread_notifications_count - '1') 
				WHERE id = '$to_user_id'";
		if ( ! mysql_query($sql))
		{
			return false;
		}
	}
	return false;
}

/**
 * Delete all notifications associated to user_id.  
 * This includes both as a creator and as a receiver. 
 * Use this function when deleting a user completely. 
 * 
 * @param object $user_id [optional]
 * @return 
 */
function notifications_delete_user($user_id = 0)
{
	if ( ! $user_id)
	{
		return false;
	}

	$sql = "DELETE FROM pm_notifications 
			WHERE to_user_id = '$user_id' 
			   OR from_user_id = '$user_id'";
	
	return mysql_query($sql);
}

/**
 * Delete old and read notifications regularly, to keep the table clean and light. 
 * Performs DELETE and OPTIMIZE TABLE queries based on time of last cleanup stored in pm_config.
 * 
 * @return 
 */
function prune_notifications_table()
{
	global $config;
	
	$time_now = time();
	$last_prune = (int) $config['pm_notifications_last_prune'];
	
	if ($time_now - (86400 * 30) > $last_prune) // 1 month
	{
		$sql = "DELETE FROM pm_notifications 
				WHERE time <= '". ($time_now - (86400 * 30)) ."' 
				  AND seen = '1'";
		if ( ! $result = mysql_query($sql))
		{
			return false;
		}
		if (mysql_affected_rows() > 0)
		{
			$sql = "OPTIMIZE TABLE pm_notifications";
			if ( ! $result = mysql_query($sql))
			{
				return false;
			}
		}
				
		update_config('pm_notifications_last_prune', $time_now);
	}
	
	return true;
}

/********* END Functions for User Notifications System *********/

/**
 * User recommender algo:
 * - excludes self, banned users, inactive accounts and users already following.
 * - ranks each account based on:
 * 		- location (L)	= give priority to users from the same location as current user
 * 		- popularity (P)= followers/following ratio 
 * 		- lifetime (Li)	= diff between last seen and register date
 * 		- recency (R)	= when was the account last used - penalize if not recent
 * 		- power	(Pw)	= give extra points to staff members
 * 		The overall rank is a sum of all these factors: L + P + Li + R + Pw.
 * 		
 * 		Scoring table:
 * 		+1 for same location
 * 		+0.5 if lifetime above average; +0.2 if average; -0.2 if below average
 * 		+0.3 if account was recently used; +0.1 if relatively recently used; -0.2 otherwise
 * 		+1 if popularity above average; +0.25 if average and 0 if below average
 * 		+0.5 if Admin, +0.25 if mod or editor
 * 
 * 		-> adjust these numbers until you get satisfying results for you
 * 
 * @since 2.1
 * @return boolean|array - boolean false if fails, array with usual data ready for $smarty
 */
function suggest_who_to_follow($start_from = 0, $limit = 10)
{
	global $userdata, $config, $time_now, $lang;

	load_countries_list();
	
	$time_now = ( ! $time_now) ? time() : $time_now;
	
	if ( ! is_array($userdata))
	{
		return false;
	}

	// get count of total relevant accounts
	$sql = "SELECT COUNT(*) as total 
			FROM pm_users 
			WHERE power IN ('".U_ACTIVE."', '".U_ADMIN."', '".U_MODERATOR."', '".U_EDITOR."')";

	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$total_active_accounts = (int) $row['total'];
	
	// get list of accounts already following
	$following = false;
	if ($userdata['following_count'] > 0)
	{
		$following = get_following_ids();
	}
	
	$ignore_user_ids = array(0 => $userdata['id']);

	if (is_array($following))
	{
		$ignore_user_ids = array_merge($ignore_user_ids, $following);
	}
	unset($following);

	$sql_limit = ($total_active_accounts > 150) ? 150 : $total_active_accounts;
	
	// get a list of all banned accounts
	$sql = "SELECT user_id 
			FROM pm_banlist";
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}

	while ($row = mysql_fetch_assoc($result))
	{
		$id = (int) $row['user_id'];
		if ( ! in_array($id, $ignore_user_ids) && $id > 0)
		{
			$ignore_user_ids[] = $id;
		}
	}
	mysql_free_result($result);

	// clean array of potential null values that might screw the following $sql 
	foreach ($ignore_user_ids as $k => $v)
	{
		if ( ! $v)
		{
			unset($ignore_user_ids[$k]);
		}
	}

	sort($ignore_user_ids);
	
	// get relevant accounts only
	$sql = "SELECT id, username, name, gender, country, reg_date, last_signin, power, avatar, followers_count, following_count, channel_slug, channel_cover, channel_verified 
			FROM pm_users 
			WHERE power IN ('".U_ACTIVE."', '".U_ADMIN."', '".U_MODERATOR."', '".U_EDITOR."') ";
	$sql .= (count($ignore_user_ids) > 0) ? ' AND id NOT IN ('. implode(',', $ignore_user_ids) .') ' : '';
	$sql .= " ORDER BY followers_count, last_signin DESC
			LIMIT $start_from, $sql_limit";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	if (mysql_num_rows($result) == 0)
	{
		return false;
	}
	
	$user_list = array();
	$score_board = array();
	$lifespan_max = $popularity_max = 0;
	$lifespan_min = $popularity_min = 999999;
	$lifespan_avg = $popularity_avg = 0;
	
	while ($row = mysql_fetch_assoc($result))
	{
		$id = (int) $row['id'];
		
		$user_list[$id] = $row;
		$user_list[$id] = get_user_data($row['id']);
		
		$popularity = 0;
		if ($row['following_count'] > 0)
		{
			$popularity = (float) round($row['followers_count'] / $row['following_count'], 2);
		}
		else 
		{
			$popularity = (float) round($row['followers_count'] * 0.6);
		}
		
		$popularity_max = ($popularity > $popularity_max) ? $popularity : $popularity_max;
		$popularity_min = ($popularity <= $popularity_min) ? $popularity : $popularity_min;
		
		$user_list[$id]['popularity'] = $popularity;
		
		$recency = $time_now - $row['last_signin'];
		
		if ($recency <= 604800) // last 7 days
		{
			$user_list[$id]['recency'] = 0.3;
		} 
		else if ($recency <= 1296000) // between 7 and 15 days
		{
			$user_list[$id]['recency'] = 0.1;
		}
		else 
		{
			$user_list[$id]['recency'] = -0.2; // most likely abandoned, so apply a penalty
		}
		
		$lifespan = $row['last_signin'] - $row['reg_date'];
		
		$user_list[$id]['lifespan'] = $lifespan;
		
		$lifespan_max = ($lifespan > $lifespan_max) ? $lifespan : $lifespan_max;
		$lifespan_min = ($lifespan <= $lifespan_min) ? $lifespan : $lifespan_min;
	}
	mysql_free_result($result);
	
	$lifespan_avg = (float) ($lifespan_max + $lifespan_min) / 2;
	$popularity_avg = (float) ($popularity_max + $popularity_min) / 2;
	
	if (count($user_list) == 0)
	{
		return false;
	}
	
	// score each one
	foreach ($user_list as $id => $u)
	{
		$score = 0;
		$user_list[$id]['country_score'] = 0;
		if ($userdata['country'] == $u['country'])
		{
			$user_list[$id]['country_score'] = 1;
			$score += 1;
		}
		
		$score += $u['recency'];
		
		if ($u['lifespan'] < $lifespan_avg)
		{
			$user_list[$id]['lifespan_score'] = -0.5;
			$score -= 0.2; // apply penalty to fresh accounts
		}
		else if ($u['lifespan'] == $lifespan_avg)
		{
			$user_list[$id]['lifespan_score'] = 0.2;
			$score += 0.2;
		}
		else 
		{
			$user_list[$id]['lifespan_score'] = 0.5;
			$score += 0.5;
		}
		
		$user_list[$id]['popularity_score'] = 0;
		if ($u['popularity'] > $popularity_avg)
		{
			$user_list[$id]['popularity_score'] = 1;
			$score += 1;
		}
		else if ($u['popularity'] == $popularity_avg)
		{
			$user_list[$id]['popularity_score'] = 0.25;
			$score += 0.25;
		}
		
		$user_list[$id]['power_score'] = 0;
		if ($u['power'] == U_ADMIN)
		{
			$user_list[$id]['power_score'] = 0.5;
			$score += 0.5;
		}
		else if ($u['power'] == U_EDITOR || $u['power'] == U_MODERATOR)
		{
			$user_list[$id]['power_score'] = 0.25;
			$score += 0.25;
		}

		$score_board[$id] = $score;
	}
	arsort($score_board);
	
	$user_ids = array();
	$limit_suggestions = ($limit > 0) ? $limit : 10;
	$i = 1;

	$final_list = array();
	foreach ($score_board as $id => $score)
	{
		$final_list[$id] = $user_list[$id];
		$final_list[$id]['score'] = $score;

		$user_ids[] = $id;
		
		if ($i == $limit_suggestions)
		{
			break;
		}
		
		$i++;
	} 

	unset($score_board, $user_list);
	
	$my_following_list = $my_followers_list = array();
	
	check_multiple_relationships($user_ids, $my_followers_list, $my_following_list);
	
	foreach ($final_list as $id => $u)
	{
		$final_list[$id]['is_following_me'] = (in_array($id, $my_followers_list)) ? true : false;
	}
	
	return $final_list;
}