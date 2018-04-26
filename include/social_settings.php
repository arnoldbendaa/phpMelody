<?php

if ( ! defined('ABSPATH'))
{
	exit();
}

define('ACTIVITIES_PER_PAGE', 20);
define('NOTIFICATIONS_PER_PAGE', 7);
define('FOLLOW_PROFILES_PER_PAGE', 10);

/* Note for plugin/template developers: 
 * 1. Define your plugin's verbs and object/targe types with a short and unique prefix
 * (ex. if plugin's name = "Facebook Login", prefix would look like "fbl_" and activity might be "fbl_loggedin").
 * Non-prefix verbs are reserved for PHP Melody's core. 
 * 2. Define custom activities in a different file
 * 3. use activity_load_options() and activity_save_options() to add custom activities to pm_config.
 */
// reserved Activity Types (verbs)
define('ACT_TYPE_FOLLOW', 'follow');
define('ACT_TYPE_UNFOLLOW', 'unfollow');
define('ACT_TYPE_WATCH', 'watch');
define('ACT_TYPE_READ', 'read');
define('ACT_TYPE_COMMENT', 'comment');
define('ACT_TYPE_LIKE', 'like');
define('ACT_TYPE_DISLIKE', 'dislike');
define('ACT_TYPE_FAVORITE', 'favorite');
define('ACT_TYPE_JOIN', 'join'); // a.k.a. register
define('ACT_TYPE_UPLOAD_VIDEO', 'upload-video');
define('ACT_TYPE_SUGGEST_VIDEO', 'suggest-video');
define('ACT_TYPE_UPDATE_AVATAR', 'update-avatar');
define('ACT_TYPE_UPDATE_COVER', 'update-cover');
define('ACT_TYPE_STATUS', 'status');
define('ACT_TYPE_SEND_VIDEO', 'send-video'); // sharing via email
define('ACT_TYPE_CREATE_PLAYLIST', 'create-playlist');
define('ACT_TYPE_UPDATE_PLAYLIST', 'update-playlist');

// reserved activity Object/Target types
define('ACT_OBJ_USER', 		'user');
define('ACT_OBJ_VIDEO', 	'video');
define('ACT_OBJ_COMMENT', 	'comment');
define('ACT_OBJ_ARTICLE',	'article');
define('ACT_OBJ_PROFILE', 	'profile');
define('ACT_OBJ_ACTIVITY', 	'activity');
define('ACT_OBJ_STATUS', 	'status');
define('ACT_OBJ_PLAYLIST', 	'playlist');

$default_activity_options = array(ACT_TYPE_FOLLOW => 1,
								  ACT_TYPE_UNFOLLOW => 0,
								  ACT_TYPE_WATCH => 0, 
								  ACT_TYPE_LIKE => 1,
								  ACT_TYPE_DISLIKE => 1,
								  ACT_TYPE_FAVORITE => 1,
								  ACT_TYPE_SEND_VIDEO => 0,
								  ACT_TYPE_UPLOAD_VIDEO => 1,
								  ACT_TYPE_SUGGEST_VIDEO => 1,
								  ACT_TYPE_READ => 0,
								  ACT_TYPE_COMMENT => 1,
								  ACT_TYPE_JOIN => 1,
								  ACT_TYPE_UPDATE_AVATAR => 0,
								  ACT_TYPE_UPDATE_COVER => 0,
								  ACT_TYPE_STATUS => 1,
								  ACT_TYPE_CREATE_PLAYLIST => 1,
								  ACT_TYPE_UPDATE_PLAYLIST => 1
								);

$activity_labels = array(ACT_TYPE_FOLLOW 		=> 'Follow a user',
						  ACT_TYPE_UNFOLLOW 	=> 'Unfollow a user',
						  ACT_TYPE_WATCH 		=> 'Watch a video',
						  ACT_TYPE_LIKE 		=> 'Like a video',
						  ACT_TYPE_DISLIKE	 	=> 'Dislike a video',
						  ACT_TYPE_FAVORITE 	=> 'Add video to favorites',
						  ACT_TYPE_SEND_VIDEO 	=> 'Send a video to a friend',
						  ACT_TYPE_UPLOAD_VIDEO => 'Upload a video',
						  ACT_TYPE_SUGGEST_VIDEO => 'Suggest a video',
						  ACT_TYPE_READ 		=> 'Read an article',
						  ACT_TYPE_COMMENT 		=> 'Post a comment',
						  ACT_TYPE_JOIN 		=> 'Register account',
						  ACT_TYPE_UPDATE_AVATAR => 'Change avatar',
						  ACT_TYPE_UPDATE_COVER => 'Change cover',
						  ACT_TYPE_STATUS 		=> 'Post status',
						  ACT_TYPE_CREATE_PLAYLIST => 'Create a playlist',
						  ACT_TYPE_UPDATE_PLAYLIST => 'Add video to playlist'
						);


$notify_loggable_activity_types = array(ACT_TYPE_FOLLOW,
										ACT_TYPE_LIKE,
										ACT_TYPE_FAVORITE,
										ACT_TYPE_COMMENT
										);
