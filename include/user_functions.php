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
/*
	this functions checks if there are any existing cookies set at last login 
	returns 0 if no cookies were found and 1 otherwise
*/
function lookup_cookies(){
	
		if( empty($_COOKIE[COOKIE_NAME]) || empty($_COOKIE[COOKIE_KEY]) )
			return 0;
	return 1;
}

/*
	this functions checks if there are any existing session variables set at last login
	returns 0 if no sessions were found and 1 otherwise
*/
function lookup_sessions(){

		if( empty($_SESSION[COOKIE_NAME]) || empty($_SESSION[COOKIE_KEY]))
			return 0;
	return 1;
}

/*
	this function checks if the current user is logged in.
*/
function is_user_logged_in(){

	if( lookup_cookies() ){
		
		if( !check_user_login_info($_COOKIE[COOKIE_NAME], $_COOKIE[COOKIE_KEY]) )
		{	
			logout();
			return 0;
		}
		else {
			if( !lookup_sessions()){

				$_SESSION[COOKIE_NAME] = $_COOKIE[COOKIE_NAME];
				$_SESSION[COOKIE_KEY] = $_COOKIE[COOKIE_KEY];
			}
			elseif( strcmp($_COOKIE[COOKIE_NAME], $_SESSION[COOKIE_NAME])  || strcmp($_COOKIE[COOKIE_KEY], $_SESSION[COOKIE_KEY]) ) 
				return 0;
		  return 1;
		}
	}		
	if( lookup_sessions() ){
		if( !check_user_login_info($_SESSION[COOKIE_NAME], $_SESSION[COOKIE_KEY]) )
			return 0;
	return 1;
	}
return 0;
}

/*
	this function verifies if the email and double-hashed password(key) exist in DB and match
*/
function check_user_login_info($username, $user_key){

	global $conn_id;
	
	if( strlen($user_key) != 32 )
		return 0; 
	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	$username = str_replace(" ", "", $username);
	$username = stripslashes($username);
	
	$sql = "SELECT username, password FROM pm_users WHERE username= '". secure_sql($username) ."'";
	$result = @mysql_query($sql);
	if( !$result ) 
		return 0;
	$rows = @mysql_num_rows($result) ;
	if( $rows == 0 )
		return 0;
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	
	// check if passwords match
	if( strcmp($user_key, md5($row['password'])) )
		return 0;
return 1;
}

/*
	this function verifies if the email and single-hashed password exist in DB and match; similar to check_user_login_info();
*/
function confirm_login( $username, $pass, $hashed = false ){

	global $conn_id, $config;

	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	$sql = "SELECT id, username, password, power FROM pm_users WHERE username= '". secure_sql($username) ."'";
	$result = @mysql_query($sql);
	if( !$result ) 
		return 0;
	$rows = @mysql_num_rows($result) ;
	if( $rows == 0 )
		return 0;
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	$password = ($hashed) ? $pass : md5($pass);	
	
	// check if passwords match
	if( strcmp($password, $row['password']) ) 
 		return 0;
	
return 1;
}

function is_user_account_active($user_id, $username)
{
	if($user_id != '')
	{
		$sql = "SELECT power FROM pm_users WHERE id = '". secure_sql($user_id) ."'";
	}
	elseif($username != '')
	{
		$sql = "SELECT power FROM pm_users WHERE username = '".secure_sql($username)."'";
	}
	$result = @mysql_query($sql);
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	
	if( $row['power'] == U_INACTIVE )
		return 0;
	return 1;
}

/**
 * Set the $_SESSION and $_COOKIE
 * 
 * @param string $username
 * @param string $pass clear-text or hashed password
 * @param bool $remember [optional] if true, sets cookies to remember the user on next visit 
 * @param bool $hashed [optional] true tells us that $pass is hashed, false tells us $pass is in clear-text
 * @param bool $skip_confirm_login [optional] if true, we skip the confirm_login() pass; handy for social oauth logins 
 * @return 
 */
function log_user_in($username, $pass, $remember = true, $hashed = false, $skip_confirm_login = false)
{
	global $conn_id;

	if (empty($conn_id)) 
	{
		$conn_id = db_connect();
	}

	if ( ! $skip_confirm_login)
	{
		if ( ! confirm_login($username, $pass, $hashed))
			return 0;
	}
	
	$key = ($hashed) ? md5($pass) : md5(md5($pass));
	
	session_regenerate_id(true);
	$_SESSION[COOKIE_NAME] = $username;
	$_SESSION[COOKIE_KEY] = $key;	
	
	if ($remember)
	{
		if (COOKIE_HTTPONLY)
		{
			setcookie(COOKIE_NAME, $username, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
			setcookie(COOKIE_KEY, $key, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
		}
		else
		{
			setcookie(COOKIE_NAME, $username, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
			setcookie(COOKIE_KEY, $key, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
		}
	}

	return 1;
}

/*
	this function logs the current user out
*/
function logout(){

	if (COOKIE_HTTPONLY)
	{
		setcookie(COOKIE_NAME, ' ', time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
		setcookie(COOKIE_KEY, ' ',  time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
	}
	else
	{
		setcookie(COOKIE_NAME, ' ', time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
		setcookie(COOKIE_KEY, ' ',  time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
	}
	
	$keep['previous_page'] = $_SESSION['previous_page'];

	$_SESSION = array();
	@session_destroy();
	
	foreach ($keep as $k => $v)
	{
		$_SESSION[$k] = $v;
	}
	
	return 1;
}


function get_last_referer() 
{
	$page = ($_SESSION['previous_page'] != '') ? $_SESSION['previous_page'] : 'index.'. _FEXT;
	// do some cleanup
	$page = str_replace(_URL, '', $page);
	$page = preg_replace('|https?://[^/]+|i', '', $page);
	
	return '/'. $page;
	
	// backup
	/*if ( ! empty($_SERVER['HTTP_REFERER']))
	{		
		$referer = strip_tags($_SERVER['HTTP_REFERER']);
		$referer = str_replace( array("<",">", "'", '"'), "", $referer);
		$referer = str_replace(_URL, '', $referer);		
		$referer = preg_replace('|https?://[^/]+|i', '', $referer );
		
		return $referer;
	}*/
}
/*
	this function checks if the current user is logged in.
*/
function reset_password($email_address = ''){
	global $conn_id;
	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	if(empty($email_address) || $email_address == '')
		return 0;
	$new_pass = generate_unique_id();
	$activation_key = generate_activation_key();
	
	$new_md5 = md5($new_pass);
	$sql = "UPDATE pm_users SET new_password = '". $new_md5 ."', activation_key = '". secure_sql($activation_key) ."' WHERE email= '". secure_sql($email_address) ."'";
	$result = @mysql_query($sql, $conn_id);
	if( !$result ) 
		return 0;
		
	return array("pass" => $new_pass, "key" => $activation_key);
}

function fetch_user_info($username)
{
	// @since v2.6
	return get_user_data(false, $username);
}

function fetch_user_advanced($id) 
{
	// @since v2.6
	return get_user_data($id);
}

/**
 * Get complete user data by user ID or username
 * 
 * @todo get user by email
 * 
 * @since 2.6
 * @param int $user_id [optional]
 * @param string $username [optional]
 * @return bool|array false when not found or on failure, array on success
 */
function get_user_data($user_id = false, $username = false)
{
	global $_pm_cache, $lang, $time_now_minute; 
	
	if ( ! $username && ! $user_id)
	{
		return false;
	}
	
	$cache_key = ($user_id) ? __FUNCTION__ . $user_id : __FUNCTION__ . $username;
	
	if (($user_data = $_pm_cache->get($cache_key)) !== false)
	{
		return $user_data;
	}
	
	$sql = "SELECT * 
			FROM pm_users 
			WHERE ";
	$sql .= ($username) ? " username = '". secure_sql($username) ."' " : " id = ". ((int) $user_id);
	
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	
	if (mysql_num_rows($result) == 0)
	{
		return false;
	}
	
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	foreach ($row as $k => $v)
	{
		$user_data[$k] = stripslashes($v);
	}
	$is_online = islive($user_data['last_signin']);
	$banned = banlist($user_data['id']);
	
	$user_data['id'] = (int) $user_data['id'];
	$user_data['user_id'] = $user_data['id']; // duplicate entry so I don't have to worry about it when switching between projects :)
	$user_data['profile_url'] = get_profile_url($user_data);
	$user_data['profile_videos_url'] = get_profile_url($user_data, array('view' => 'videos'));
	$user_data['profile_playlists_url'] = get_profile_url($user_data, array('view' => 'playlists'));
	
	$user_data['country'] = (int) $user_data['country'];
	$user_data['country_label'] = countryid2name($user_data['country']);
	$user_data['reg_date'] = (int) $user_data['reg_date'];
	$user_data['last_signin'] = (int) $user_data['last_signin'];
	$user_data['last_seen'] = time_since($user_data['last_signin'], true).' '.$lang['ago'];
	$user_data['is_online'] = $is_online;
	$user_data['status_label'] = ($is_online) ? $lang['memberlist_on'] : $lang['memberlist_off'];
	$user_data['user_is_banned'] = ($banned['user_id'] == $user_data['id']) ? true : false;
	
	$user_data['followers_count'] = (int) $user_data['followers_count'];
	$user_data['following_count'] = (int) $user_data['following_count'];
	$user_data['followers_count_formatted'] = pm_number_format($user_data['followers_count']);
	$user_data['following_count_formatted'] = pm_number_format($user_data['following_count']);
	$user_data['unread_notifications_count'] = (int) $user_data['unread_notifications_count'];
	$user_data['unread_notifications_count_formatted'] = pm_number_format($user_data['unread_notifications_count']);
	$user_data['unread_notifications_compact'] = pm_compact_number_format($user_data['unread_notifications_count']);
	
	$user_data['avatar_url'] = get_avatar_url($user_data['avatar'], $user_data['id']);
	$user_data['followers_compact'] = pm_compact_number_format($user_data['followers_count']);
	$user_data['following_compact'] = pm_compact_number_format($user_data['following_count']);
	$user_data['statuses_compact'] = pm_compact_number_format($user_data['statuses_count']);
	
	if ($user_data['channel_cover'] != '')
	{
		$user_data['channel_cover'] = array('filename' => $user_data['channel_cover'],
											'max' => _COVERS_DIR . $user_data['channel_cover'],
											'450' => _COVERS_DIR . str_replace('-max.', '-450.', $user_data['channel_cover']),
											'225' => _COVERS_DIR . str_replace('-max.', '-225.', $user_data['channel_cover']) 
											);
	}
	else
	{
		$user_data['channel_cover'] = array('filename' => $user_data['channel_cover'],
											'max' => null,
											'450' => null,
											'225' => null 
											);
	}
	
	$user_data['social_links'] = (empty($user_data['social_links'])) ? array() : unserialize($user_data['social_links']);
	
	// backwards compatibility @since v2.6
	if (array_key_exists('website', $user_data))
	{
		$user_data['social_links']['website'] = ($user_data['website'] != '' && $user_data['social_links']['website'] == '') ? $user_data['website'] : $user_data['social_links']['website'];
		$user_data['social_links']['facebook'] = ($user_data['facebook'] != '' && $user_data['social_links']['facebook'] == '') ? $user_data['facebook'] : $user_data['social_links']['facebook'];
		$user_data['social_links']['twitter'] = ($user_data['twitter'] != '' && $user_data['social_links']['twitter'] == '') ? $user_data['twitter'] : $user_data['social_links']['twitter'];
	}
	
	foreach ($user_data['social_links'] as $social_network => $url)
	{
		if ('' != $url && strpos($url, 'http') !== 0)
		{
			$user_data['social_links'][$social_network] = 'https://'. $url;
		}
	}
	
	$user_data['videos_count'] = count_entries('pm_videos', 'submitted', secure_sql($user_data['username'])  ."' AND added <= '". $time_now_minute ); // @todo replace `submitted` with `submitted_user_id`
	$user_data['videos_count_formatted'] = pm_number_format($user_data['videos_count']);
	$user_data['videos_count_compact'] = pm_compact_number_format($user_data['videos_count']);
	
	$_pm_cache->add($cache_key, $user_data);
	
	
	return $user_data;
}

/**
 *  Get user data by user ID or username; just the DB row - no URLs, no counting, no formatting. 
 * 
 * @since 2.7
 * 
 * @todo get user by email
 * 
 * @param int $user_id [optional]
 * @param string $username [optional]
 * @return bool|array false when not found or on failure, array on success
 */
function get_basic_user_data($user_id = false, $username = false)
{
	global $_pm_cache; 
	
	if ( ! $username && ! $user_id)
	{
		return false;
	}
	
	$cache_key = ($user_id) ? __FUNCTION__ . $user_id : __FUNCTION__ . $username;
	
	if (($user_data = $_pm_cache->get($cache_key)) !== false)
	{
		return $user_data;
	}
	
	$sql = "SELECT * 
			FROM pm_users 
			WHERE ";
	$sql .= ($username) ? " username = '". secure_sql($username) ."' " : " id = ". ((int) $user_id);
	
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	
	if (mysql_num_rows($result) == 0)
	{
		return false;
	}
	
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	foreach ($row as $k => $v)
	{
		$user_data[$k] = stripslashes($v);
	}
	
	$user_data['id'] = (int) $user_data['id'];
	$user_data['user_id'] = $user_data['id']; // duplicate entry so I don't have to worry about it when switching between projects :)
	
	$_pm_cache->add($cache_key, $user_data);
	
	return $user_data;
}

function generate_unique_id(){
	return substr(md5(uniqid(time(), true)), 0, 7);
}

function username_to_id($username)
{
	if(!$username) return false;
	$username = stripslashes($username);
	$sql = "SELECT id FROM pm_users where username LIKE '". secure_sql($username) ."'";
	$result = mysql_query($sql);
	if(!$result)
		return 0;
	$total = mysql_num_rows($result);
	if($total > 0)
	{
		$r = mysql_fetch_assoc($result);
		mysql_free_result($result);
		return $r['id'];
	}
	return 0;
}

function banlist($user_id)
{
	global $_pm_cache;
	
	if ( ! $user_id)
	{
		return array();
	} 
	
	$cache_key = __FUNCTION__ . $user_id;
	
	if (($ban = $_pm_cache->get($cache_key)) !== false)
	{
		return $ban;
	}
	
	$sql = "SELECT * FROM pm_banlist 
			WHERE user_id = ". secure_sql($user_id);
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return array();
	}
	
	$ban = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$_pm_cache->add($cache_key, $ban);
	
	return $ban;
}

function mod_can($action = '')
{
	global $config;

	$user_can = $temp = $buff = array();
	
	$temp = explode(';', $config['moderator_can']);
	
	foreach ($temp as $key => $value)
	{
		$buff = explode(':', $value);
		
		$user_can[$buff[0]] = (int) $buff[1]; 
	}
	
	return ('' != $action) ? $user_can[$action] : $user_can;
}

function mod_cannot($action)
{
	$mod_can = mod_can($action);
	return ($mod_can) ? false : true;
}

function is_moderator()
{
	global $userdata;
	return ($userdata['power'] == U_MODERATOR) ? true : false;
}

function is_editor()
{
	global $userdata;
	return ($userdata['power'] == U_EDITOR) ? true : false;
}

function is_admin()
{
	global $userdata;
	return ($userdata['power'] == U_ADMIN) ? true : false;
}

function is_regular_user()
{
	global $userdata;
	if (is_array($userdata) && is_user_logged_in())
	{
		return ($userdata['power'] == U_ACTIVE) ? true : false;
	}
	
	return false;
}

function is_registered_user()
{
	if (is_regular_user() || is_editor() || is_moderator() || is_admin())
	{
		return true;
	}
	return false;
}

function get_avatar_url($avatar = '', $user_id = 0)
{
	if ($avatar != '')
	{
		return _AVATARS_DIR . $avatar;
	}
	
	return _AVATARS_DIR .'default.gif';
}

/**
 * Builds the URL for a user's profile/channel. 
 * The customizable 'channel_slug' attribute gets priority
 * 
 * @param object $userdata
 * @param object $args [optional]
 * @return 
 */
function get_profile_url($userdata, $args = array())
{
	$url = _URL;
	
	// give priority to channel_slug 
	if (_SEOMOD)
	{
		$url .= '/user/'. (($userdata['channel_slug'] != '') ? $userdata['channel_slug'] : $userdata['username']) .'/';
		$url .= ($args['view'] != '') ? $args['view'] .'/' : '';
	}
	else
	{
		$url .= '/user.php?u='. (($userdata['channel_slug'] != '') ? $userdata['channel_slug'] : $userdata['username']);
		$url .= ($args['view'] != '') ? '&view='. $args['view'] : '';
	}
	
	return $url; 
}

function delete_channel_cover_files($filename)
{
	if ( ! file_exists(_COVERS_DIR_PATH . $filename) && $filename != '')
	{
		return false;
	}
	
	$sizes = array( 'max' => $filename,
					'450' => str_replace('-max.', '-450.', $filename),
					'225' => str_replace('-max.', '-225.', $filename)
			);
	
	foreach ($sizes as $width => $fn)
	{
		@unlink(_COVERS_DIR_PATH . $fn);
	}
	
	return true;
}

function get_featured_channels($limit = 5, $sort_by = 'RAND()', $order = '')
{
	global $time_now;
	
	$time_now = ( ! $time_now) ? time() : $time_now;
	
	$sql = "SELECT id 
			FROM pm_users 
			WHERE channel_featured = '1'
			ORDER BY $sort_by $order 
			LIMIT 0, $limit";
	
	$channels = array();
	if ($result = @mysql_query($sql))
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$channels[$row['id']] = get_user_data($row['id']);
		}
		mysql_free_result($result);
	}
	
	return $channels;
}