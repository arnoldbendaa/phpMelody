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

function bin_rating_insert_vote($uniq_id, $vote_value)
{
	global $userdata;
	
	$user_id = (is_array($userdata) && $userdata['id'] != '') ? $userdata['id'] : 0;
	$ip = bin_rating_user_get_ip();
	
	$sql = "INSERT INTO pm_bin_rating_votes
						(uniq_id, vote_value, vote_ip, user_id, date)
				VALUES ('". secure_sql($uniq_id) ."',
						'". $vote_value ."',
						'". secure_sql($ip) ."',
						'". secure_sql($user_id) ."',
						'". time() ."'
						)";
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	if ($vote_value == 1 && $user_id != 0 && strpos($uniq_id, 'com-') === false)
	{
		$playlist = get_user_playlist_liked($userdata['id']);
		$video_id = uniq_id_to_video_id($uniq_id);
		playlist_add_item($playlist['list_id'], $video_id);
	}
	
	if (_MOD_SOCIAL && $user_id != 0 && strpos($uniq_id, 'com-') === false)
	{
		$act_type = ($vote_value == 1) ? ACT_TYPE_LIKE : ACT_TYPE_DISLIKE; 
		
		$video = request_video($uniq_id);
		log_activity(array(
			'user_id' => $user_id,
			'activity_type' => $act_type,
			'object_id' => $video['id'],
			'object_type' => ACT_OBJ_VIDEO,
			'object_data' => $video
			)
		);
		
		if ($vote_value == 1)
		{
			notify_user(username_to_id($video['submitted']), 
						$userdata['id'],
						ACT_TYPE_LIKE, 
						array( 'from_userdata' => $userdata,
						 		'object_type'=> ACT_OBJ_VIDEO,
								'object' => $video
							  )
						);
		}
	}
	
	return true;
}

function bin_rating_update_vote_value($uniq_id, $vote_value)
{
	global $userdata, $config;

	$user_id = (is_array($userdata) && $userdata['id'] != '') ? $userdata['id'] : 0;
	$allow_anon = (int) $config['bin_rating_allow_anon_voting'];
	 
	$ip = bin_rating_user_get_ip();
	
	$sql = "UPDATE pm_bin_rating_votes 
			   SET vote_value = '". $vote_value ."'
			 WHERE uniq_id = '". secure_sql($uniq_id) ."'
			   AND ";

	if ($user_id)
	{
		$sql .= " user_id = '". $user_id ."'";
	}
	else if ( ! $user_id && $allow_anon)
	{
		$sql .= " vote_ip = '". secure_sql($ip) ."' AND user_id = '0'";
	}
	else
	{
		return false;
	}

	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	if ($vote_value == 0 && $user_id != 0 && strpos($uniq_id, 'com-') === false)
	{
		$playlist = get_user_playlist_liked($userdata['id']);
		$video_id = uniq_id_to_video_id($uniq_id);
		playlist_delete_item($playlist['list_id'], $video_id);
	}
	
	if ($vote_value == 1 && $user_id != 0 && strpos($uniq_id, 'com-') === false)
	{
		$playlist = get_user_playlist_liked($userdata['id']);
		$video_id = uniq_id_to_video_id($uniq_id);
		playlist_add_item($playlist['list_id'], $video_id);
	}
	
	if (_MOD_SOCIAL && $user_id != 0 && strpos($uniq_id, 'com-') === false)
	{
		$act_type = ($vote_value == 1) ? ACT_TYPE_DISLIKE : ACT_TYPE_LIKE; // search the opposite vote value!
		
		$video = request_video($uniq_id);
		
		$activity_id = get_activity_id(array('user_id' => $user_id, 
											 'activity_type' => $act_type, 
											 'object_id' => $video['id'],
											 'object_type' => ACT_OBJ_VIDEO
											)
									  );
		
		if ($activity_id)
		{
			$new_act_type = ($vote_value == 1) ? ACT_TYPE_LIKE : ACT_TYPE_DISLIKE;
			update_activity($activity_id, array('activity_type' => $new_act_type));
		}
	}
	return true;
}

function bin_rating_delete_vote($uniq_id)
{
	global $userdata, $config;
	
	$user_id = (is_array($userdata) && $userdata['id'] != '') ? $userdata['id'] : 0;
	$ip = bin_rating_user_get_ip();
	$vote_data = bin_rating_get_vote_data($uniq_id);
	$allow_anon = (int) $config['bin_rating_allow_anon_voting'];
	
	if ($vote_data === false)
	{
		return false;
	}
	
	$sql = "DELETE FROM pm_bin_rating_votes 
				  WHERE uniq_id = '". secure_sql($uniq_id) ."'
				    AND ";
	if ($user_id)
	{
		$sql .= " user_id = '". secure_sql($user_id) ."'";
	}
	else if ($allow_anon)
	{
		$sql .= " vote_ip = '". secure_sql($ip) ."' AND user_id = '0'";
	}
	else
	{
		return false;
	}

	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	$sql = "UPDATE pm_bin_rating_meta 
			   SET ";
	$sql .= ($vote_data['vote_value']) ? ' up_vote_count = up_vote_count - 1 ' : ' down_vote_count = down_vote_count - 1 ';
	$sql .= ", score = (up_vote_count - down_vote_count) ";
	$sql .= " WHERE uniq_id = '". secure_sql($uniq_id) ."'";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	if ($vote_data['vote_value'] == 1 && $user_id != 0 && strpos($uniq_id, 'com-') === false)
	{
		$playlist = get_user_playlist_liked($userdata['id']);
		$video_id = uniq_id_to_video_id($uniq_id);
		playlist_delete_item($playlist['list_id'], $video_id);
	}
	
	if (_MOD_SOCIAL && $user_id != 0 && strpos($uniq_id, 'com-') === false)
	{
		$act_type = ($vote_data['vote_value'] == 1) ? ACT_TYPE_LIKE : ACT_TYPE_DISLIKE;
		
		$video = request_video($uniq_id);
		
		$activity_id = get_activity_id(array('user_id' => $user_id, 
											 'activity_type' => $act_type, 
											 'object_id' => $video['id'],
											 'object_type' => ACT_OBJ_VIDEO
											)
									  );
		
		if ($activity_id)
		{
			if ($vote_data['vote_value'] == 1)
			{
				$activity_data = get_activity_data($activity_id);
	
				cancel_notification(username_to_id($video['submitted']), 
									$userdata['id'],
									ACT_TYPE_LIKE, 
									$activity_data['time']);
			}

			delete_activity($activity_id);
		}
	}
	
	return true;
}


function bin_rating_update_vote_count($uniq_id, $vote_value, $just_changing_vote = false)
{
	// this function handles only UPDATEs, no DELETEs
	
	$sql = "UPDATE pm_bin_rating_meta ";
	
	if ($just_changing_vote)
	{
		// update both likes & dislikes
		if ($vote_value) // upvote
		{
			$sql .= " SET up_vote_count = up_vote_count+1,
					      down_vote_count = down_vote_count-1 ";
		}
		else  // downvote
		{
			$sql .= " SET up_vote_count = up_vote_count-1,
					      down_vote_count = down_vote_count+1 ";
		}
	}
	else
	{
		// update just one (like or dislike)
		$sql .= ($vote_value) ? " SET up_vote_count = up_vote_count+1 " : " SET down_vote_count = down_vote_count+1 ";
	}
	
	// recalculate score
	$sql .= ", score = (up_vote_count - down_vote_count) ";
	$sql .= " WHERE uniq_id = '". secure_sql($uniq_id) ."'";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	return true;
}

function bin_rating_vote($uniq_id, $vote_value)
{
	if ( ! bin_rating_user_can_vote())
	{
		return false;
	}
	$current_vote_value = bin_rating_user_has_voted($uniq_id);

	if ($current_vote_value === false && $vote_value < 0)
	{
		// shouldn't come to this but if it does,
		// we won't let the user delete something he doesn't own
		return true; // 'true' so it won't trigger any unnecessary errors 
	}

	if ($current_vote_value === false) // new vote
	{
		$item_meta = bin_rating_get_item_meta($uniq_id);
		
		if ($item_meta === false) // probably the first time this item has received a vote
		{
			$up_vote_count = ($vote_value) ? 1 : 0;
			$down_vote_count = ($vote_value) ? 0 : 1;
			$score = bin_rating_calc_score($up_vote_count, $down_vote_count);
			$sql = "INSERT INTO pm_bin_rating_meta 
						(uniq_id, up_vote_count, down_vote_count, score)
					 	VALUES (
						'". secure_sql($uniq_id) ."',
						'". $up_vote_count ."',
						'". $down_vote_count ."',
						'". $score ."')";

			if ( ! $result = mysql_query($sql))
			{
				return false;
			}
		}
		else
		{
			// update vote counters and score
			bin_rating_update_vote_count($uniq_id, $vote_value);
		}
		
		// insert new vote
		return bin_rating_insert_vote($uniq_id, $vote_value);
	}
	else
	{
		if ($current_vote_value != $vote_value) // user wants to change vote 
		{
			if ($vote_value < 0) // delete
			{
				return bin_rating_delete_vote($uniq_id);
			}
			else // update
			{
				bin_rating_update_vote_value($uniq_id, $vote_value);
				return bin_rating_update_vote_count($uniq_id, $vote_value, $just_changing_vote = true);
			}
		}
		else // user wants to cancel the vote
		{
			return bin_rating_delete_vote($uniq_id);
		}
	}
	
	return false;
}

function bin_rating_get_vote_data($uniq_id)
{
	global $userdata, $config;
	
	$user_id = (is_array($userdata) && $userdata['id'] != '') ? $userdata['id'] : 0;
	$allow_anon = (int) $config['bin_rating_allow_anon_voting'];
	$ip = bin_rating_user_get_ip();
	
	if ($user_id)
	{
		$sql = "SELECT vote_id, uniq_id, vote_value, vote_ip, user_id, date
			  	  FROM pm_bin_rating_votes 
			 	 WHERE uniq_id = '". secure_sql($uniq_id) ."'
			   	   AND user_id = '". secure_sql($user_id) ."'";
	}
	else if ( ! $user_id && $allow_anon)
	{
		$sql = "SELECT vote_id, uniq_id, vote_value, vote_ip, user_id, date
				  FROM pm_bin_rating_votes 
				 WHERE uniq_id = '". secure_sql($uniq_id) ."'
				   AND vote_ip = '". secure_sql($ip) ."'
				   AND user_id = '0'";
	}
	else
	{
		return false;
	}
	
	$result = mysql_query($sql); 
	if ( ! $result) 
	{
		return false;
	}
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		$row['vote_value'] = (int) $row['vote_value'];
		$row['user_id'] = (int) $row['user_id'];
		$row['vote_id'] = (int) $row['vote_id'];

		return $row;
	}

	return false;
}

function bin_rating_calc_score($up_vote_count, $down_vote_count)
{
	return $up_vote_count - $down_vote_count;
}

function bin_rating_calc_balance($up_vote_count, $down_vote_count)
{
	$total_votes = $up_vote_count + $down_vote_count;
	// take care of division by 0 
	$up_pct = ($total_votes) ? round( ($up_vote_count * 100) / $total_votes, 0) : 0;
	$down_pct = ($total_votes) ? round( ($down_vote_count * 100) / $total_votes, 0) : 0;
	
	return array('up_pct' => $up_pct, 'down_pct' => $down_pct);
}

function bin_rating_user_can_vote()
{
	global $userdata, $config;
	
	$allow_anon = (int) $config['bin_rating_allow_anon_voting'];
	
	if ( ! is_array($userdata) && ! $allow_anon)
	{
		return false;
	}
	
	if ( ! is_array($userdata) && $allow_anon)
	{
		$bots_ua = array('googlebot', 'google', 'msnbot', 'ia_archiver', 'lycos', 'jeeves', 'scooter', 'fast-webcrawler', 'slurp@inktomi', 'turnitinbot', 'technorati', 'yahoo', 'findexa', 'findlinks', 'gaisbo', 'zyborg', 'surveybot', 'bloglines', 'blogsearch', 'ubsub', 'syndic8', 'userland', 'gigabot', 'become.com');
		$ua = $_SERVER['HTTP_USER_AGENT'];
		foreach ($bots_ua as $bot) 
		{ 
			if (stristr($ua, $bot) !== false) 
			{
				return false;
			} 
		}
	}
	
	return true;
}

function bin_rating_user_has_voted($uniq_id)
{
	global $userdata, $config;
	
	$user_id = (is_array($userdata) && $userdata['id'] != '') ? $userdata['id'] : 0;
	$allow_anon = (int) $config['bin_rating_allow_anon_voting'];
	$ip = bin_rating_user_get_ip();

	if ($user_id)
	{
		$sql = "SELECT vote_value 
			  	  FROM pm_bin_rating_votes 
			 	 WHERE uniq_id = '". secure_sql($uniq_id) ."'
			   	   AND user_id = '". secure_sql($user_id) ."'";
	}
	else if ( ! $user_id && $allow_anon)
	{
		$sql = "SELECT vote_value 
				  FROM pm_bin_rating_votes 
				 WHERE uniq_id = '". secure_sql($uniq_id) ."'
				   AND vote_ip = '". secure_sql($ip) ."'
				   AND user_id = '0'";
	}
	else
	{
		return false;
	}
	
	$result = mysql_query($sql); 
	if ( ! $result) 
	{
		return false;
	}
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		return $row['vote_value']; 
	}
	
	return false;
}

function bin_rating_get_item_meta($uniq_id)
{
	$sql = "SELECT vote_meta_id, uniq_id, up_vote_count, down_vote_count, score 
		 	 FROM pm_bin_rating_meta 
			WHERE uniq_id = '". secure_sql($uniq_id) ."'
			LIMIT 1";
	
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		return $row; 
	}
	
	return false;
}


function bin_rating_delete_item_meta($uniq_id)
{
	// use only when deleting an item
	$sql = "DELETE FROM pm_bin_rating_meta 
				  WHERE uniq_id = '". secure_sql($uniq_id) ."'
				  LIMIT 1";
				  
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	return true;
}

function bin_rating_user_get_ip()
{
	return pm_get_ip(); // @since v2.6.1
}